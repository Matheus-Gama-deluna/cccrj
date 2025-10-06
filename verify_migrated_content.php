<?php
// verify_migrated_content.php

// Script para verificar se o conteúdo do site original foi migrado corretamente

require_once 'api/config/database.php';

try {
    echo "Verificando conteúdo migrado...\n\n";
    
    $pdo = connectDatabase();
    $pdo->exec("USE cccrj_db");
    
    // 1. Verificar conteúdo do CCCRJ
    echo "1. Verificando conteúdo do CCCRJ...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE event_type = 'institucional'");
    $cccrjCount = $stmt->fetch()['count'];
    echo "✓ $cccrjCount eventos históricos institucionais encontrados\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM about_sections WHERE section_type = 'institucional'");
    $aboutCount = $stmt->fetch()['count'];
    echo "✓ $aboutCount seções sobre institucionais encontradas\n";
    
    echo "\n";
    
    // 2. Verificar conteúdo do CRMC
    echo "2. Verificando conteúdo do CRMC...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM crmc_items");
    $crmcCount = $stmt->fetch()['count'];
    echo "✓ $crmcCount itens do CRMC encontrados\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE event_type = 'cultural'");
    $culturalCount = $stmt->fetch()['count'];
    echo "✓ $culturalCount eventos culturais encontrados\n";
    
    echo "\n";
    
    // 3. Verificar revistas
    echo "3. Verificando revistas...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM publications WHERE type = 'revista'");
    $revistaCount = $stmt->fetch()['count'];
    echo "✓ $revistaCount revistas encontradas\n";
    
    echo "\n";
    
    // 4. Verificar clipping
    echo "4. Verificando clipping...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM clippings");
    $clippingCount = $stmt->fetch()['count'];
    echo "✓ $clippingCount clippings encontrados\n";
    
    echo "\n";
    
    // 5. Mostrar amostra dos dados migrados
    echo "5. Amostra dos dados migrados:\n\n";
    
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
    
    // 6. Verificar integridade dos dados
    echo "6. Verificando integridade dos dados...\n";
    
    // Verificar registros ativos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM historical_events WHERE is_active = 1");
    $activeEvents = $stmt->fetch()['count'];
    echo "✓ $activeEvents eventos históricos ativos\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM publications WHERE is_active = 1");
    $activePublications = $stmt->fetch()['count'];
    echo "✓ $activePublications publicações ativas\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM clippings WHERE is_active = 1");
    $activeClippings = $stmt->fetch()['count'];
    echo "✓ $activeClippings clippings ativos\n";
    
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
    echo "\nResumo:\n";
    echo "- Eventos históricos institucionais: $cccrjCount\n";
    echo "- Seções sobre institucionais: $aboutCount\n";
    echo "- Itens do CRMC: $crmcCount\n";
    echo "- Eventos culturais: $culturalCount\n";
    echo "- Revistas: $revistaCount\n";
    echo "- Clippings: $clippingCount\n";
    echo "- Eventos históricos ativos: $activeEvents\n";
    echo "- Publicações ativas: $activePublications\n";
    echo "- Clippings ativos: $activeClippings\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante a verificação: " . $e->getMessage() . "\n";
}
?>