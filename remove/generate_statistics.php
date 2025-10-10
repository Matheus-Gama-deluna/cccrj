<?php
// generate_statistics.php

// Script para gerar estatísticas do conteúdo migrado

require_once 'api/config/database.php';

try {
    echo "Gerando estatísticas do conteúdo migrado...\n\n";
    
    $pdo = connectDatabase();
    $pdo->exec("USE cccrj_db");
    
    // Criar diretório de relatórios
    $reportsDir = 'reports';
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d-H-i-s');
    $reportFile = "$reportsDir/statistics-report-$timestamp.txt";
    
    $reportContent = "RELATÓRIO DE ESTATÍSTICAS DO CONTEÚDO MIGRADO\n";
    $reportContent .= "==========================================\n\n";
    $reportContent .= "Data da geração: " . date('d/m/Y H:i:s') . "\n";
    $reportContent .= "Timestamp: $timestamp\n\n";
    
    echo $reportContent;
    
    // 1. Estatísticas gerais
    echo "1. Estatísticas gerais...\n";
    $reportContent .= "1. ESTATÍSTICAS GERAIS\n";
    $reportContent .= "---------------------\n";
    
    $tables = [
        'historical_events' => 'Eventos Históricos',
        'publications' => 'Publicações',
        'archive_items' => 'Itens do Acervo',
        'clippings' => 'Clippings',
        'about_sections' => 'Seções Sobre',
        'crmc_items' => 'Itens do CRMC'
    ];
    
    $totalRecords = 0;
    foreach ($tables as $table => $label) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $stmt->fetch()['count'];
        $totalRecords += $count;
        
        $reportContent .= "$label: $count registros\n";
        echo "✓ $label: $count registros\n";
    }
    
    $reportContent .= "\nTotal de registros: $totalRecords\n\n";
    echo "\nTotal de registros: $totalRecords\n\n";
    
    // 2. Estatísticas por data
    echo "2. Estatísticas por data...\n";
    $reportContent .= "2. ESTATÍSTICAS POR DATA\n";
    $reportContent .= "-----------------------\n";
    
    foreach ($tables as $table => $label) {
        $stmt = $pdo->query("SELECT MIN(created_at) as min_date, MAX(created_at) as max_date FROM `$table`");
        $dates = $stmt->fetch();
        
        if ($dates['min_date']) {
            $reportContent .= "$label:\n";
            $reportContent .= "  - Primeiro registro: " . date('d/m/Y', strtotime($dates['min_date'])) . "\n";
            $reportContent .= "  - Último registro: " . date('d/m/Y', strtotime($dates['max_date'])) . "\n\n";
            
            echo "✓ $label:\n";
            echo "  - Primeiro registro: " . date('d/m/Y', strtotime($dates['min_date'])) . "\n";
            echo "  - Último registro: " . date('d/m/Y', strtotime($dates['max_date'])) . "\n\n";
        }
    }
    
    // 3. Estatísticas por tipo/categoria
    echo "3. Estatísticas por tipo/categoria...\n";
    $reportContent .= "3. ESTATÍSTICAS POR TIPO/CATEGORIA\n";
    $reportContent .= "----------------------------------\n";
    
    // Tipos de eventos históricos
    $stmt = $pdo->query("SELECT event_type, COUNT(*) as count FROM historical_events GROUP BY event_type ORDER BY count DESC");
    $eventTypes = $stmt->fetchAll();
    
    $reportContent .= "Tipos de Eventos Históricos:\n";
    foreach ($eventTypes as $type) {
        $reportContent .= "  - {$type['event_type']}: {$type['count']} registros\n";
    }
    $reportContent .= "\n";
    
    echo "✓ Tipos de Eventos Históricos:\n";
    foreach ($eventTypes as $type) {
        echo "  - {$type['event_type']}: {$type['count']} registros\n";
    }
    echo "\n";
    
    // Tipos de publicações
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM publications GROUP BY type ORDER BY count DESC");
    $publicationTypes = $stmt->fetchAll();
    
    $reportContent .= "Tipos de Publicações:\n";
    foreach ($publicationTypes as $type) {
        $reportContent .= "  - {$type['type']}: {$type['count']} registros\n";
    }
    $reportContent .= "\n";
    
    echo "✓ Tipos de Publicações:\n";
    foreach ($publicationTypes as $type) {
        echo "  - {$type['type']}: {$type['count']} registros\n";
    }
    echo "\n";
    
    // Categorias de clipping
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM clippings GROUP BY category ORDER BY count DESC");
    $clippingCategories = $stmt->fetchAll();
    
    $reportContent .= "Categorias de Clipping:\n";
    foreach ($clippingCategories as $category) {
        $reportContent .= "  - {$category['category']}: {$category['count']} registros\n";
    }
    $reportContent .= "\n";
    
    echo "✓ Categorias de Clipping:\n";
    foreach ($clippingCategories as $category) {
        echo "  - {$category['category']}: {$category['count']} registros\n";
    }
    echo "\n";
    
    // Categorias do CRMC
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM crmc_items GROUP BY category ORDER BY count DESC");
    $crmcCategories = $stmt->fetchAll();
    
    $reportContent .= "Categorias do CRMC:\n";
    foreach ($crmcCategories as $category) {
        $reportContent .= "  - {$category['category']}: {$category['count']} registros\n";
    }
    $reportContent .= "\n";
    
    echo "✓ Categorias do CRMC:\n";
    foreach ($crmcCategories as $category) {
        echo "  - {$category['category']}: {$category['count']} registros\n";
    }
    echo "\n";
    
    // 4. Estatísticas de conteúdo ativo/inativo
    echo "4. Estatísticas de conteúdo ativo/inativo...\n";
    $reportContent .= "4. ESTATÍSTICAS DE CONTEÚDO ATIVO/INATIVO\n";
    $reportContent .= "------------------------------------------\n";
    
    foreach ($tables as $table => $label) {
        $stmt = $pdo->query("SELECT SUM(is_active = 1) as active, SUM(is_active = 0) as inactive FROM `$table`");
        $status = $stmt->fetch();
        
        $total = $status['active'] + $status['inactive'];
        $activePercent = $total > 0 ? round(($status['active'] / $total) * 100, 2) : 0;
        $inactivePercent = $total > 0 ? round(($status['inactive'] / $total) * 100, 2) : 0;
        
        $reportContent .= "$label:\n";
        $reportContent .= "  - Ativo: {$status['active']} registros ($activePercent%)\n";
        $reportContent .= "  - Inativo: {$status['inactive']} registros ($inactivePercent%)\n\n";
        
        echo "✓ $label:\n";
        echo "  - Ativo: {$status['active']} registros ($activePercent%)\n";
        echo "  - Inativo: {$status['inactive']} registros ($inactivePercent%)\n\n";
    }
    
    // 5. Estatísticas de conteúdo por ano
    echo "5. Estatísticas de conteúdo por ano...\n";
    $reportContent .= "5. ESTATÍSTICAS DE CONTEÚDO POR ANO\n";
    $reportContent .= "-----------------------------------\n";
    
    foreach ($tables as $table => $label) {
        $stmt = $pdo->query("SELECT YEAR(created_at) as year, COUNT(*) as count FROM `$table` GROUP BY YEAR(created_at) ORDER BY year DESC");
        $yearlyStats = $stmt->fetchAll();
        
        if (count($yearlyStats) > 0) {
            $reportContent .= "$label:\n";
            foreach ($yearlyStats as $stat) {
                $reportContent .= "  - {$stat['year']}: {$stat['count']} registros\n";
            }
            $reportContent .= "\n";
            
            echo "✓ $label:\n";
            foreach ($yearlyStats as $stat) {
                echo "  - {$stat['year']}: {$stat['count']} registros\n";
            }
            echo "\n";
        }
    }
    
    // 6. Estatísticas especiais
    echo "6. Estatísticas especiais...\n";
    $reportContent .= "6. ESTATÍSTICAS ESPECIAIS\n";
    $reportContent .= "-------------------------\n";
    
    // Eventos em destaque
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE is_featured = 1");
    $featuredEvents = $stmt->fetch()['count'];
    $reportContent .= "Eventos históricos em destaque: $featuredEvents\n";
    echo "✓ Eventos históricos em destaque: $featuredEvents\n";
    
    // Publicações por tipo
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM publications GROUP BY type");
    $pubStats = $stmt->fetchAll();
    $reportContent .= "Publicações por tipo:\n";
    foreach ($pubStats as $stat) {
        $reportContent .= "  - {$stat['type']}: {$stat['count']} registros\n";
    }
    $reportContent .= "\n";
    
    echo "✓ Publicações por tipo:\n";
    foreach ($pubStats as $stat) {
        echo "  - {$stat['type']}: {$stat['count']} registros\n";
    }
    echo "\n";
    
    // Salvar relatório em arquivo
    file_put_contents($reportFile, $reportContent);
    
    echo "✅ Estatísticas geradas com sucesso!\n";
    echo "\nRelatório salvo em: $reportFile\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao gerar estatísticas: " . $e->getMessage() . "\n";
}
?>