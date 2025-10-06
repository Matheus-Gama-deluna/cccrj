<?php
// export_populated_content.php

// Script para exportar o conteúdo populado para backup

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';
require_once 'api/models/AboutSection.php';
require_once 'api/models/CrmcItem.php';
require_once 'api/models/ArchiveItem.php';

try {
    echo "Exportando conteúdo populado para backup...\n\n";
    
    $pdo = connectDatabase();
    
    // Criar diretório de exportação
    $exportDir = 'exports';
    if (!is_dir($exportDir)) {
        mkdir($exportDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d-H-i-s');
    $exportSubDir = "$exportDir/export-$timestamp";
    mkdir($exportSubDir, 0755, true);
    
    echo "Exportando para: $exportSubDir\n\n";
    
    // 1. Exportar eventos históricos
    echo "1. Exportando eventos históricos...\n";
    
    $stmt = $pdo->query("SELECT * FROM historical_events ORDER BY date");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $eventsFile = "$exportSubDir/historical_events.json";
    file_put_contents($eventsFile, json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Eventos históricos exportados para $eventsFile (" . count($events) . " registros)\n";
    
    echo "\n";
    
    // 2. Exportar publicações
    echo "2. Exportando publicações...\n";
    
    $stmt = $pdo->query("SELECT * FROM publications ORDER BY date DESC");
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $publicationsFile = "$exportSubDir/publications.json";
    file_put_contents($publicationsFile, json_encode($publications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Publicações exportadas para $publicationsFile (" . count($publications) . " registros)\n";
    
    echo "\n";
    
    // 3. Exportar itens do acervo
    echo "3. Exportando itens do acervo...\n";
    
    $stmt = $pdo->query("SELECT * FROM archive_items ORDER BY date DESC");
    $archiveItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $archiveFile = "$exportSubDir/archive_items.json";
    file_put_contents($archiveFile, json_encode($archiveItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Itens do acervo exportados para $archiveFile (" . count($archiveItems) . " registros)\n";
    
    echo "\n";
    
    // 4. Exportar clippings
    echo "4. Exportando clippings...\n";
    
    $stmt = $pdo->query("SELECT * FROM clippings ORDER BY date DESC");
    $clippings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $clippingsFile = "$exportSubDir/clippings.json";
    file_put_contents($clippingsFile, json_encode($clippings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Clippings exportados para $clippingsFile (" . count($clippings) . " registros)\n";
    
    echo "\n";
    
    // 5. Exportar seções sobre
    echo "5. Exportando seções sobre...\n";
    
    $stmt = $pdo->query("SELECT * FROM about_sections ORDER BY `order`");
    $aboutSections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $aboutFile = "$exportSubDir/about_sections.json";
    file_put_contents($aboutFile, json_encode($aboutSections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Seções sobre exportadas para $aboutFile (" . count($aboutSections) . " registros)\n";
    
    echo "\n";
    
    // 6. Exportar itens do CRMC
    echo "6. Exportando itens do CRMC...\n";
    
    $stmt = $pdo->query("SELECT * FROM crmc_items ORDER BY created_at DESC");
    $crmcItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $crmcFile = "$exportSubDir/crmc_items.json";
    file_put_contents($crmcFile, json_encode($crmcItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Itens do CRMC exportados para $crmcFile (" . count($crmcItems) . " registros)\n";
    
    echo "\n";
    
    // 7. Criar manifesto da exportação
    echo "7. Criando manifesto da exportação...\n";
    
    $manifest = [
        'export_timestamp' => $timestamp,
        'export_date' => date('Y-m-d H:i:s'),
        'total_records' => [
            'historical_events' => count($events),
            'publications' => count($publications),
            'archive_items' => count($archiveItems),
            'clippings' => count($clippings),
            'about_sections' => count($aboutSections),
            'crmc_items' => count($crmcItems)
        ],
        'files_exported' => [
            'historical_events.json',
            'publications.json',
            'archive_items.json',
            'clippings.json',
            'about_sections.json',
            'crmc_items.json'
        ]
    ];
    
    $manifestFile = "$exportSubDir/manifest.json";
    file_put_contents($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Manifesto da exportação criado em $manifestFile\n";
    
    echo "\n";
    
    // 8. Criar arquivo README da exportação
    echo "8. Criando README da exportação...\n";
    
    $readmeContent = "Exportação de Conteúdo do CCCRJ
==============================

Data da exportação: " . date('d/m/Y H:i:s') . "
Timestamp: $timestamp

Conteúdo exportado:
- " . count($events) . " eventos históricos
- " . count($publications) . " publicações
- " . count($archiveItems) . " itens do acervo
- " . count($clippings) . " clippings
- " . count($aboutSections) . " seções sobre
- " . count($crmcItems) . " itens do CRMC

Formato dos arquivos: JSON
Codificação: UTF-8

Os arquivos podem ser importados de volta para o sistema usando os scripts
de importação apropriados.
";
    
    $readmeFile = "$exportSubDir/README.txt";
    file_put_contents($readmeFile, $readmeContent);
    echo "✓ README da exportação criado em $readmeFile\n";
    
    echo "\n";
    
    echo "✅ Exportação concluída!\n";
    echo "\nArquivos exportados para: $exportSubDir\n";
    echo "\nConteúdo exportado:\n";
    echo "- $eventsFile (" . count($events) . " registros)\n";
    echo "- $publicationsFile (" . count($publications) . " registros)\n";
    echo "- $archiveFile (" . count($archiveItems) . " registros)\n";
    echo "- $clippingsFile (" . count($clippings) . " registros)\n";
    echo "- $aboutFile (" . count($aboutSections) . " registros)\n";
    echo "- $crmcFile (" . count($crmcItems) . " registros)\n";
    echo "- $manifestFile (manifesto da exportação)\n";
    echo "- $readmeFile (informações sobre a exportação)\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante a exportação: " . $e->getMessage() . "\n";
}
?>