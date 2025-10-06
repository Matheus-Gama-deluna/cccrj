<?php
// verify_populated_content.php

// Script para verificar se o conteúdo foi populado corretamente no banco de dados

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';
require_once 'api/models/AboutSection.php';
require_once 'api/models/CrmcItem.php';
require_once 'api/models/ArchiveItem.php';

try {
    echo "Verificando conteúdo populado no banco de dados...\n\n";
    
    $pdo = connectDatabase();
    
    // Verificar conteúdo do CCCRJ
    echo "1. Verificando conteúdo do CCCRJ...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE event_type = 'institucional'");
    $cccrjCount = $stmt->fetch()['count'];
    echo "✓ $cccrjCount eventos históricos institucionais encontrados\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM about_sections WHERE section_type = 'institucional'");
    $aboutCount = $stmt->fetch()['count'];
    echo "✓ $aboutCount seções sobre institucionais encontradas\n";
    
    echo "\n";
    
    // Verificar conteúdo do CRMC
    echo "2. Verificando conteúdo do CRMC...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM crmc_items");
    $crmcCount = $stmt->fetch()['count'];
    echo "✓ $crmcCount itens do CRMC encontrados\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE event_type = 'cultural'");
    $culturalCount = $stmt->fetch()['count'];
    echo "✓ $culturalCount eventos culturais encontrados\n";
    
    echo "\n";
    
    // Verificar revistas
    echo "3. Verificando revistas...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM publications WHERE type = 'revista'");
    $revistaCount = $stmt->fetch()['count'];
    echo "✓ $revistaCount revistas encontradas\n";
    
    echo "\n";
    
    // Verificar clipping
    echo "4. Verificando clipping...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM clippings");
    $clippingCount = $stmt->fetch()['count'];
    echo "✓ $clippingCount clippings encontrados\n";
    
    echo "\n";
    
    // Verificar conteúdo do café no Rio
    echo "5. Verificando conteúdo do café no Rio...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE event_type = 'historico'");
    $rioCount = $stmt->fetch()['count'];
    echo "✓ $rioCount eventos históricos do café no Rio encontrados\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM about_sections WHERE section_type = 'historico'");
    $rioAboutCount = $stmt->fetch()['count'];
    echo "✓ $rioAboutCount seções sobre o café no Rio encontradas\n";
    
    echo "\n";
    
    // Mostrar amostra dos dados populados
    echo "6. Amostra dos dados populados:\n\n";
    
    // Amostra de eventos históricos
    echo "Eventos Históricos:\n";
    $stmt = $pdo->query("SELECT title, date, event_type FROM historical_events ORDER BY date LIMIT 5");
    $events = $stmt->fetchAll();
    foreach ($events as $event) {
        echo "  - {$event['title']} ({$event['date']}) [{$event['event_type']}]\n";
    }
    
    echo "\n";
    
    // Amostra de publicações
    echo "Publicações:\n";
    $stmt = $pdo->query("SELECT title, date, type, number FROM publications ORDER BY date DESC LIMIT 5");
    $publications = $stmt->fetchAll();
    foreach ($publications as $pub) {
        echo "  - {$pub['title']} ({$pub['date']}) [{$pub['type']} #{$pub['number']}]\n";
    }
    
    echo "\n";
    
    // Amostra de clippings
    echo "Clippings:\n";
    $stmt = $pdo->query("SELECT title, date, category FROM clippings ORDER BY date DESC LIMIT 5");
    $clippings = $stmt->fetchAll();
    foreach ($clippings as $clip) {
        echo "  - {$clip['title']} ({$clip['date']}) [{$clip['category']}]\n";
    }
    
    echo "\n";
    
    // Verificar integridade dos dados
    echo "7. Verificando integridade dos dados...\n";
    
    // Verificar registros ativos
    $stmt = $pdo->query("SELECT SUM(is_active = 1) as active, SUM(is_active = 0) as inactive FROM historical_events");
    $status = $stmt->fetch();
    $activeEvents = $status['active'];
    $inactiveEvents = $status['inactive'];
    echo "✓ $activeEvents eventos históricos ativos, $inactiveEvents inativos\n";
    
    $stmt = $pdo->query("SELECT SUM(is_active = 1) as active, SUM(is_active = 0) as inactive FROM publications");
    $status = $stmt->fetch();
    $activePublications = $status['active'];
    $inactivePublications = $status['inactive'];
    echo "✓ $activePublications publicações ativas, $inactivePublications inativas\n";
    
    $stmt = $pdo->query("SELECT SUM(is_active = 1) as active, SUM(is_active = 0) as inactive FROM clippings");
    $status = $stmt->fetch();
    $activeClippings = $status['active'];
    $inactiveClippings = $status['inactive'];
    echo "✓ $activeClippings clippings ativos, $inactiveClippings inativos\n";
    
    echo "\n";
    
    // Verificar dados com conteúdo
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE content IS NOT NULL AND LENGTH(content) > 0");
    $eventsWithContent = $stmt->fetch()['count'];
    echo "✓ $eventsWithContent eventos históricos com conteúdo\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM publications WHERE description IS NOT NULL AND LENGTH(description) > 0");
    $pubsWithDescription = $stmt->fetch()['count'];
    echo "✓ $pubsWithDescription publicações com descrição\n";
    
    echo "\n";
    
    echo "✅ Verificação concluída!\n";
    
    // Resumo estatístico
    $totalRecords = $cccrjCount + $aboutCount + $crmcCount + $culturalCount + $revistaCount + $clippingCount + $rioCount + $rioAboutCount;
    $totalActiveRecords = $activeEvents + $activePublications + $activeClippings;
    $totalInactiveRecords = $inactiveEvents + $inactivePublications + $inactiveClippings;
    $totalRecordsWithContent = $eventsWithContent + $pubsWithDescription;
    
    echo "\nResumo estatístico:\n";
    echo "- Total de registros: $totalRecords\n";
    echo "- Registros ativos: $totalActiveRecords\n";
    echo "- Registros inativos: $totalInactiveRecords\n";
    echo "- Registros com conteúdo: $totalRecordsWithContent\n";
    echo "- Eventos históricos institucionais: $cccrjCount\n";
    echo "- Seções sobre institucionais: $aboutCount\n";
    echo "- Itens do CRMC: $crmcCount\n";
    echo "- Eventos culturais: $culturalCount\n";
    echo "- Revistas: $revistaCount\n";
    echo "- Clippings: $clippingCount\n";
    echo "- Eventos históricos do café no Rio: $rioCount\n";
    echo "- Seções sobre o café no Rio: $rioAboutCount\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante a verificação: " . $e->getMessage() . "\n";
}
?>