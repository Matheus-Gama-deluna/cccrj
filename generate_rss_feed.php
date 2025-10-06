<?php
// generate_rss_feed.php

// Script para gerar feeds RSS com base no conteúdo migrado

require_once 'api/config/database.php';

try {
    echo "Gerando feeds RSS...\n\n";
    
    $pdo = connectDatabase();
    $pdo->exec("USE cccrj_db");
    
    // 1. Gerar feed RSS de notícias
    echo "1. Gerando feed RSS de notícias...\n";
    
    $rssNews = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $rssNews .= '<rss version="2.0">' . "\n";
    $rssNews .= '  <channel>' . "\n";
    $rssNews .= '    <title>CCCRJ - Notícias do Setor Cafeeiro</title>' . "\n";
    $rssNews .= '    <link>http://www.cccrj.com.br</link>' . "\n";
    $rssNews .= '    <description>Notícias e atualizações do Centro de Comércio do Café do Rio de Janeiro</description>' . "\n";
    $rssNews .= '    <language>pt-br</language>' . "\n";
    $rssNews .= '    <generator>CCCRJ RSS Generator</generator>' . "\n";
    $rssNews .= '    <lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
    
    // Adicionar notícias recentes
    $stmt = $pdo->prepare("
        SELECT c.*, 'clipping' as type 
        FROM clippings c 
        WHERE c.is_active = 1 
        ORDER BY c.date DESC, c.created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $recentNews = $stmt->fetchAll();
    
    foreach ($recentNews as $news) {
        $title = htmlspecialchars($news['title']);
        $description = htmlspecialchars(substr($news['summary'] ?? $news['content'], 0, 200) . '...');
        $link = "http://www.cccrj.com.br/clipping/{$news['id']}";
        $pubDate = date('r', strtotime($news['date'] ?? $news['created_at']));
        $category = htmlspecialchars($news['category']);
        
        $rssNews .= "    <item>\n";
        $rssNews .= "      <title>$title</title>\n";
        $rssNews .= "      <description>$description</description>\n";
        $rssNews .= "      <link>$link</link>\n";
        $rssNews .= "      <guid>$link</guid>\n";
        $rssNews .= "      <pubDate>$pubDate</pubDate>\n";
        $rssNews .= "      <category>$category</category>\n";
        $rssNews .= "    </item>\n";
    }
    
    $rssNews .= '  </channel>' . "\n";
    $rssNews .= '</rss>';
    
    $rssNewsFile = 'rss/news.xml';
    if (!is_dir(dirname($rssNewsFile))) {
        mkdir(dirname($rssNewsFile), 0755, true);
    }
    file_put_contents($rssNewsFile, $rssNews);
    
    echo "✓ Feed RSS de notícias gerado: $rssNewsFile (" . count($recentNews) . " itens)\n\n";
    
    // 2. Gerar feed RSS de publicações
    echo "2. Gerando feed RSS de publicações...\n";
    
    $rssPublications = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $rssPublications .= '<rss version="2.0">' . "\n";
    $rssPublications .= '  <channel>' . "\n";
    $rssPublications .= '    <title>CCCRJ - Publicações Técnicas</title>' . "\n";
    $rssPublications .= '    <link>http://www.cccrj.com.br</link>' . "\n";
    $rssPublications .= '    <description>Publicações técnicas do Centro de Comércio do Café do Rio de Janeiro</description>' . "\n";
    $rssPublications .= '    <language>pt-br</language>' . "\n";
    $rssPublications .= '    <generator>CCCRJ RSS Generator</generator>' . "\n";
    $rssPublications .= '    <lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
    
    // Adicionar publicações recentes
    $stmt = $pdo->prepare("
        SELECT p.*, 'publication' as type 
        FROM publications p 
        WHERE p.is_active = 1 
        ORDER BY p.date DESC, p.created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $recentPublications = $stmt->fetchAll();
    
    foreach ($recentPublications as $pub) {
        $title = htmlspecialchars($pub['title']);
        $description = htmlspecialchars($pub['description'] ?? 'Publicação técnica do CCCRJ');
        $link = "http://www.cccrj.com.br/publicacoes/{$pub['id']}";
        $pubDate = date('r', strtotime($pub['date'] ?? $pub['created_at']));
        $category = htmlspecialchars($pub['type']);
        
        $rssPublications .= "    <item>\n";
        $rssPublications .= "      <title>$title</title>\n";
        $rssPublications .= "      <description>$description</description>\n";
        $rssPublications .= "      <link>$link</link>\n";
        $rssPublications .= "      <guid>$link</guid>\n";
        $rssPublications .= "      <pubDate>$pubDate</pubDate>\n";
        $rssPublications .= "      <category>$category</category>\n";
        $rssPublications .= "    </item>\n";
    }
    
    $rssPublications .= '  </channel>' . "\n";
    $rssPublications .= '</rss>';
    
    $rssPublicationsFile = 'rss/publications.xml';
    if (!is_dir(dirname($rssPublicationsFile))) {
        mkdir(dirname($rssPublicationsFile), 0755, true);
    }
    file_put_contents($rssPublicationsFile, $rssPublications);
    
    echo "✓ Feed RSS de publicações gerado: $rssPublicationsFile (" . count($recentPublications) . " itens)\n\n";
    
    // 3. Gerar feed RSS de eventos históricos
    echo "3. Gerando feed RSS de eventos históricos...\n";
    
    $rssHistory = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $rssHistory .= '<rss version="2.0">' . "\n";
    $rssHistory .= '  <channel>' . "\n";
    $rssHistory .= '    <title>CCCRJ - História do CCCRJ</title>' . "\n";
    $rssHistory .= '    <link>http://www.cccrj.com.br</link>' . "\n";
    $rssHistory .= '    <description>História e eventos marcantes do Centro de Comércio do Café do Rio de Janeiro</description>' . "\n";
    $rssHistory .= '    <language>pt-br</language>' . "\n";
    $rssHistory .= '    <generator>CCCRJ RSS Generator</generator>' . "\n";
    $rssHistory .= '    <lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
    
    // Adicionar eventos históricos recentes
    $stmt = $pdo->prepare("
        SELECT h.*, 'historical_event' as type 
        FROM historical_events h 
        WHERE h.is_active = 1 
        ORDER BY h.date DESC, h.created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $recentHistory = $stmt->fetchAll();
    
    foreach ($recentHistory as $event) {
        $title = htmlspecialchars($event['title']);
        $description = htmlspecialchars(substr($event['description'] ?? $event['content'], 0, 200) . '...');
        $link = "http://www.cccrj.com.br/historia/{$event['id']}";
        $pubDate = date('r', strtotime($event['date'] ?? $event['created_at']));
        $category = htmlspecialchars($event['event_type']);
        
        $rssHistory .= "    <item>\n";
        $rssHistory .= "      <title>$title</title>\n";
        $rssHistory .= "      <description>$description</description>\n";
        $rssHistory .= "      <link>$link</link>\n";
        $rssHistory .= "      <guid>$link</guid>\n";
        $rssHistory .= "      <pubDate>$pubDate</pubDate>\n";
        $rssHistory .= "      <category>$category</category>\n";
        $rssHistory .= "    </item>\n";
    }
    
    $rssHistory .= '  </channel>' . "\n";
    $rssHistory .= '</rss>';
    
    $rssHistoryFile = 'rss/history.xml';
    if (!is_dir(dirname($rssHistoryFile))) {
        mkdir(dirname($rssHistoryFile), 0755, true);
    }
    file_put_contents($rssHistoryFile, $rssHistory);
    
    echo "✓ Feed RSS de eventos históricos gerado: $rssHistoryFile (" . count($recentHistory) . " itens)\n\n";
    
    // 4. Gerar feed RSS de itens do CRMC
    echo "4. Gerando feed RSS de itens do CRMC...\n";
    
    $rssCrmc = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $rssCrmc .= '<rss version="2.0">' . "\n";
    $rssCrmc .= '  <channel>' . "\n";
    $rssCrmc .= '    <title>CCCRJ - Centro de Referência e Memória do Café</title>' . "\n";
    $rssCrmc .= '    <link>http://www.cccrj.com.br</link>' . "\n";
    $rssCrmc .= '    <description>Conteúdo do Centro de Referência e Memória do Café do Rio de Janeiro</description>' . "\n";
    $rssCrmc .= '    <language>pt-br</language>' . "\n";
    $rssCrmc .= '    <generator>CCCRJ RSS Generator</generator>' . "\n";
    $rssCrmc .= '    <lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
    
    // Adicionar itens do CRMC recentes
    $stmt = $pdo->prepare("
        SELECT c.*, 'crmc_item' as type 
        FROM crmc_items c 
        WHERE c.is_active = 1 
        ORDER BY c.created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $recentCrmc = $stmt->fetchAll();
    
    foreach ($recentCrmc as $item) {
        $title = htmlspecialchars($item['title']);
        $description = htmlspecialchars(substr($item['description'] ?? $item['content'], 0, 200) . '...');
        $link = "http://www.cccrj.com.br/crmc/{$item['id']}";
        $pubDate = date('r', strtotime($item['created_at']));
        $category = htmlspecialchars($item['category']);
        
        $rssCrmc .= "    <item>\n";
        $rssCrmc .= "      <title>$title</title>\n";
        $rssCrmc .= "      <description>$description</description>\n";
        $rssCrmc .= "      <link>$link</link>\n";
        $rssCrmc .= "      <guid>$link</guid>\n";
        $rssCrmc .= "      <pubDate>$pubDate</pubDate>\n";
        $rssCrmc .= "      <category>$category</category>\n";
        $rssCrmc .= "    </item>\n";
    }
    
    $rssCrmc .= '  </channel>' . "\n";
    $rssCrmc .= '</rss>';
    
    $rssCrmcFile = 'rss/crmc.xml';
    if (!is_dir(dirname($rssCrmcFile))) {
        mkdir(dirname($rssCrmcFile), 0755, true);
    }
    file_put_contents($rssCrmcFile, $rssCrmc);
    
    echo "✓ Feed RSS de itens do CRMC gerado: $rssCrmcFile (" . count($recentCrmc) . " itens)\n\n";
    
    // 5. Criar página de índice dos feeds
    echo "5. Criando página de índice dos feeds...\n";
    
    $feedIndex = '<!DOCTYPE html>' . "\n";
    $feedIndex .= '<html lang="pt-BR">' . "\n";
    $feedIndex .= '<head>' . "\n";
    $feedIndex .= '    <meta charset="UTF-8">' . "\n";
    $feedIndex .= '    <title>Feeds RSS - CCCRJ</title>' . "\n";
    $feedIndex .= '    <style>' . "\n";
    $feedIndex .= '        body { font-family: Arial, sans-serif; margin: 40px; }' . "\n";
    $feedIndex .= '        h1 { color: #8B2635; }' . "\n";
    $feedIndex .= '        ul { list-style-type: none; padding: 0; }' . "\n";
    $feedIndex .= '        li { margin: 10px 0; padding: 10px; background: #f5f5f5; border-radius: 5px; }' . "\n";
    $feedIndex .= '        a { color: #8B2635; text-decoration: none; font-weight: bold; }' . "\n";
    $feedIndex .= '        a:hover { text-decoration: underline; }' . "\n";
    $feedIndex .= '    </style>' . "\n";
    $feedIndex .= '</head>' . "\n";
    $feedIndex .= '<body>' . "\n";
    $feedIndex .= '    <h1>Feeds RSS do CCCRJ</h1>' . "\n";
    $feedIndex .= '    <ul>' . "\n";
    $feedIndex .= '        <li><a href="rss/news.xml">Notícias do Setor Cafeeiro</a> (' . count($recentNews) . ' itens)</li>' . "\n";
    $feedIndex .= '        <li><a href="rss/publications.xml">Publicações Técnicas</a> (' . count($recentPublications) . ' itens)</li>' . "\n";
    $feedIndex .= '        <li><a href="rss/history.xml">História do CCCRJ</a> (' . count($recentHistory) . ' itens)</li>' . "\n";
    $feedIndex .= '        <li><a href="rss/crmc.xml">Centro de Referência e Memória do Café</a> (' . count($recentCrmc) . ' itens)</li>' . "\n";
    $feedIndex .= '    </ul>' . "\n";
    $feedIndex .= '    <p><small>Última atualização: ' . date('d/m/Y H:i:s') . '</small></p>' . "\n";
    $feedIndex .= '</body>' . "\n";
    $feedIndex .= '</html>';
    
    $feedIndexFile = 'rss/index.html';
    file_put_contents($feedIndexFile, $feedIndex);
    
    echo "✓ Página de índice dos feeds gerada: $feedIndexFile\n\n";
    
    echo "✅ Geração de feeds RSS concluída!\n";
    echo "\nFeeds gerados:\n";
    echo "- $rssNewsFile (" . count($recentNews) . " itens)\n";
    echo "- $rssPublicationsFile (" . count($recentPublications) . " itens)\n";
    echo "- $rssHistoryFile (" . count($recentHistory) . " itens)\n";
    echo "- $rssCrmcFile (" . count($recentCrmc) . " itens)\n";
    echo "- $feedIndexFile (índice)\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao gerar feeds RSS: " . $e->getMessage() . "\n";
}

?>