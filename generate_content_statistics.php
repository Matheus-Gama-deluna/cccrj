<?php
// generate_content_statistics.php

// Script para gerar estatísticas detalhadas do conteúdo populado no banco de dados

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';
require_once 'api/models/AboutSection.php';
require_once 'api/models/CrmcItem.php';
require_once 'api/models/ArchiveItem.php';

try {
    echo "Gerando estatísticas detalhadas do conteúdo populado...\n\n";
    
    $pdo = connectDatabase();
    
    // 1. Estatísticas gerais
    echo "1. Estatísticas gerais...\n";
    
    $tables = [
        'historical_events' => 'Eventos Históricos',
        'publications' => 'Publicações',
        'archive_items' => 'Itens do Acervo',
        'clippings' => 'Clippings',
        'about_sections' => 'Seções Sobre',
        'crmc_items' => 'Itens do CRMC'
    ];
    
    $totalRecords = 0;
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $stmt->fetch()['count'];
        $totalRecords += $count;
        
        echo "✓ $description: $count registros\n";
    }
    
    echo "\nTotal de registros: $totalRecords\n\n";
    
    // 2. Estatísticas por data
    echo "2. Estatísticas por data...\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT MIN(created_at) as min_date, MAX(created_at) as max_date FROM `$table`");
        $dates = $stmt->fetch();
        
        if ($dates['min_date']) {
            echo "✓ $description:\n";
            echo "  - Primeiro registro: " . date('d/m/Y', strtotime($dates['min_date'])) . "\n";
            echo "  - Último registro: " . date('d/m/Y', strtotime($dates['max_date'])) . "\n\n";
        }
    }
    
    // 3. Estatísticas por tipo/categoria
    echo "3. Estatísticas por tipo/categoria...\n";
    
    // Tipos de eventos históricos
    $stmt = $pdo->query("SELECT event_type, COUNT(*) as count FROM historical_events GROUP BY event_type ORDER BY count DESC");
    $eventTypes = $stmt->fetchAll();
    
    echo "Tipos de Eventos Históricos:\n";
    foreach ($eventTypes as $type) {
        echo "  - {$type['event_type']}: {$type['count']} registros\n";
    }
    echo "\n";
    
    // Tipos de publicações
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM publications GROUP BY type ORDER BY count DESC");
    $publicationTypes = $stmt->fetchAll();
    
    echo "Tipos de Publicações:\n";
    foreach ($publicationTypes as $type) {
        echo "  - {$type['type']}: {$type['count']} registros\n";
    }
    echo "\n";
    
    // Categorias de clipping
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM clippings GROUP BY category ORDER BY count DESC");
    $clippingCategories = $stmt->fetchAll();
    
    echo "Categorias de Clipping:\n";
    foreach ($clippingCategories as $category) {
        echo "  - {$category['category']}: {$category['count']} registros\n";
    }
    echo "\n";
    
    // Categorias do CRMC
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM crmc_items GROUP BY category ORDER BY count DESC");
    $crmcCategories = $stmt->fetchAll();
    
    echo "Categorias do CRMC:\n";
    foreach ($crmcCategories as $category) {
        echo "  - {$category['category']}: {$category['count']} registros\n";
    }
    echo "\n";
    
    // Tipos de seções sobre
    $stmt = $pdo->query("SELECT section_type, COUNT(*) as count FROM about_sections GROUP BY section_type ORDER BY count DESC");
    $aboutSectionTypes = $stmt->fetchAll();
    
    echo "Tipos de Seções Sobre:\n";
    foreach ($aboutSectionTypes as $type) {
        echo "  - {$type['section_type']}: {$type['count']} registros\n";
    }
    echo "\n";
    
    // 4. Estatísticas de conteúdo ativo/inativo
    echo "4. Estatísticas de conteúdo ativo/inativo...\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT SUM(is_active = 1) as active, SUM(is_active = 0) as inactive FROM `$table`");
        $status = $stmt->fetch();
        
        $total = $status['active'] + $status['inactive'];
        $activePercent = $total > 0 ? round(($status['active'] / $total) * 100, 2) : 0;
        $inactivePercent = $total > 0 ? round(($status['inactive'] / $total) * 100, 2) : 0;
        
        echo "✓ $description:\n";
        echo "  - Ativo: {$status['active']} registros ($activePercent%)\n";
        echo "  - Inativo: {$status['inactive']} registros ($inactivePercent%)\n\n";
    }
    
    // 5. Estatísticas de conteúdo por ano
    echo "5. Estatísticas de conteúdo por ano...\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT YEAR(created_at) as year, COUNT(*) as count FROM `$table` GROUP BY YEAR(created_at) ORDER BY year DESC");
        $yearlyStats = $stmt->fetchAll();
        
        if (count($yearlyStats) > 0) {
            echo "$description:\n";
            foreach ($yearlyStats as $stat) {
                echo "  - {$stat['year']}: {$stat['count']} registros\n";
            }
            echo "\n";
        }
    }
    
    // 6. Estatísticas especiais
    echo "6. Estatísticas especiais...\n";
    
    // Eventos em destaque
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE is_featured = 1");
    $featuredEvents = $stmt->fetch()['count'];
    echo "✓ Eventos históricos em destaque: $featuredEvents\n";
    
    // Publicações por tipo
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM publications GROUP BY type");
    $pubStats = $stmt->fetchAll();
    echo "✓ Publicações por tipo:\n";
    foreach ($pubStats as $stat) {
        echo "  - {$stat['type']}: {$stat['count']} registros\n";
    }
    echo "\n";
    
    // 7. Gerar relatório em formato legível
    echo "7. Gerando relatório em formato legível...\n";
    
    $reportsDir = 'reports';
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d-H-i-s');
    $reportFile = "$reportsDir/content_statistics_$timestamp.txt";
    
    $reportContent = "RELATÓRIO DE ESTATÍSTICAS DO CONTEÚDO POPULADO\n";
    $reportContent .= "==========================================\n\n";
    $reportContent .= "Data da geração: " . date('d/m/Y H:i:s') . "\n";
    $reportContent .= "Timestamp: $timestamp\n\n";
    
    $reportContent .= "1. ESTATÍSTICAS GERAIS\n";
    $reportContent .= "--------------------\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $stmt->fetch()['count'];
        $reportContent .= "$description: $count registros\n";
    }
    
    $reportContent .= "\nTotal de registros: $totalRecords\n\n";
    
    $reportContent .= "2. ESTATÍSTICAS POR DATA\n";
    $reportContent .= "-----------------------\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT MIN(created_at) as min_date, MAX(created_at) as max_date FROM `$table`");
        $dates = $stmt->fetch();
        
        if ($dates['min_date']) {
            $reportContent .= "$description:\n";
            $reportContent .= "  - Primeiro registro: " . date('d/m/Y', strtotime($dates['min_date'])) . "\n";
            $reportContent .= "  - Último registro: " . date('d/m/Y', strtotime($dates['max_date'])) . "\n\n";
        }
    }
    
    $reportContent .= "3. ESTATÍSTICAS POR TIPO/CATEGORIA\n";
    $reportContent .= "----------------------------------\n";
    
    $reportContent .= "Tipos de Eventos Históricos:\n";
    foreach ($eventTypes as $type) {
        $reportContent .= "  - {$type['event_type']}: {$type['count']} registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Tipos de Publicações:\n";
    foreach ($publicationTypes as $type) {
        $reportContent .= "  - {$type['type']}: {$type['count']} registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Categorias de Clipping:\n";
    foreach ($clippingCategories as $category) {
        $reportContent .= "  - {$category['category']}: {$category['count']} registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Categorias do CRMC:\n";
    foreach ($crmcCategories as $category) {
        $reportContent .= "  - {$category['category']}: {$category['count']} registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Tipos de Seções Sobre:\n";
    foreach ($aboutSectionTypes as $type) {
        $reportContent .= "  - {$type['section_type']}: {$type['count']} registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "4. ESTATÍSTICAS DE CONTEÚDO ATIVO/INATIVO\n";
    $reportContent .= "------------------------------------------\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT SUM(is_active = 1) as active, SUM(is_active = 0) as inactive FROM `$table`");
        $status = $stmt->fetch();
        
        $total = $status['active'] + $status['inactive'];
        $activePercent = $total > 0 ? round(($status['active'] / $total) * 100, 2) : 0;
        $inactivePercent = $total > 0 ? round(($status['inactive'] / $total) * 100, 2) : 0;
        
        $reportContent .= "$description:\n";
        $reportContent .= "  - Ativo: {$status['active']} registros ($activePercent%)\n";
        $reportContent .= "  - Inativo: {$status['inactive']} registros ($inactivePercent%)\n\n";
    }
    
    $reportContent .= "5. ESTATÍSTICAS DE CONTEÚDO POR ANO\n";
    $reportContent .= "----------------------------------\n";
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT YEAR(created_at) as year, COUNT(*) as count FROM `$table` GROUP BY YEAR(created_at) ORDER BY year DESC");
        $yearlyStats = $stmt->fetchAll();
        
        if (count($yearlyStats) > 0) {
            $reportContent .= "$description:\n";
            foreach ($yearlyStats as $stat) {
                $reportContent .= "  - {$stat['year']}: {$stat['count']} registros\n";
            }
            $reportContent .= "\n";
        }
    }
    
    $reportContent .= "6. ESTATÍSTICAS ESPECIAIS\n";
    $reportContent .= "-------------------------\n";
    
    $reportContent .= "Eventos históricos em destaque: $featuredEvents\n\n";
    
    $reportContent .= "Publicações por tipo:\n";
    foreach ($pubStats as $stat) {
        $reportContent .= "  - {$stat['type']}: {$stat['count']} registros\n";
    }
    $reportContent .= "\n";
    
    file_put_contents($reportFile, $reportContent);
    
    echo "✓ Relatório salvo em: $reportFile\n\n";
    
    echo "✅ Geração de estatísticas concluída!\n";
    echo "\nRelatório gerado:\n";
    echo "- $reportFile (Texto)\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao gerar estatísticas: " . $e->getMessage() . "\n";
}
?>