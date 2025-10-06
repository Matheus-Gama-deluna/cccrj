<?php
// generate_sitemap.php

// Script para gerar um mapa do site (sitemap.xml)

require_once 'api/config/database.php';

try {
    echo "Gerando mapa do site...\n\n";
    
    $pdo = connectDatabase();
    $pdo->exec("USE cccrj_db");
    
    // Criar conteúdo do sitemap
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // 1. Adicionar páginas estáticas
    echo "1. Adicionando páginas estáticas...\n";
    
    $staticPages = [
        '' => '2023-01-01', // Home
        'index.html' => '2023-01-01',
        'sobre.html' => '2023-01-01',
        'contato.html' => '2023-01-01',
        'login.html' => '2023-01-01'
    ];
    
    foreach ($staticPages as $page => $date) {
        $url = $page ? "http://www.cccrj.com.br/$page" : "http://www.cccrj.com.br/";
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$date</lastmod>\n";
        $sitemap .= "    <changefreq>weekly</changefreq>\n";
        $sitemap .= "    <priority>1.0</priority>\n";
        $sitemap .= "  </url>\n";
        echo "✓ Página estática adicionada: $url\n";
    }
    
    echo "\n";
    
    // 2. Adicionar eventos históricos
    echo "2. Adicionando eventos históricos...\n";
    
    $stmt = $pdo->query("SELECT id, date, updated_at FROM historical_events WHERE is_active = 1 ORDER BY date DESC");
    $events = $stmt->fetchAll();
    
    foreach ($events as $event) {
        $url = "http://www.cccrj.com.br/historia/evento/{$event['id']}";
        $lastmod = date('Y-m-d', strtotime($event['updated_at']));
        $date = date('Y-m-d', strtotime($event['date']));
        
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$lastmod</lastmod>\n";
        $sitemap .= "    <changefreq>yearly</changefreq>\n";
        $sitemap .= "    <priority>0.8</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    echo "✓ " . count($events) . " eventos históricos adicionados\n\n";
    
    // 3. Adicionar publicações
    echo "3. Adicionando publicações...\n";
    
    $stmt = $pdo->query("SELECT id, date, updated_at FROM publications WHERE is_active = 1 ORDER BY date DESC");
    $publications = $stmt->fetchAll();
    
    foreach ($publications as $pub) {
        $url = "http://www.cccrj.com.br/publicacoes/{$pub['id']}";
        $lastmod = date('Y-m-d', strtotime($pub['updated_at']));
        $date = date('Y-m-d', strtotime($pub['date']));
        
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$lastmod</lastmod>\n";
        $sitemap .= "    <changefreq>monthly</changefreq>\n";
        $sitemap .= "    <priority>0.7</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    echo "✓ " . count($publications) . " publicações adicionadas\n\n";
    
    // 4. Adicionar clippings
    echo "4. Adicionando clippings...\n";
    
    $stmt = $pdo->query("SELECT id, date, updated_at FROM clippings WHERE is_active = 1 ORDER BY date DESC");
    $clippings = $stmt->fetchAll();
    
    foreach ($clippings as $clip) {
        $url = "http://www.cccrj.com.br/clipping/{$clip['id']}";
        $lastmod = date('Y-m-d', strtotime($clip['updated_at']));
        $date = date('Y-m-d', strtotime($clip['date']));
        
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$lastmod</lastmod>\n";
        $sitemap .= "    <changefreq>daily</changefreq>\n";
        $sitemap .= "    <priority>0.6</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    echo "✓ " . count($clippings) . " clippings adicionados\n\n";
    
    // 5. Adicionar itens do CRMC
    echo "5. Adicionando itens do CRMC...\n";
    
    $stmt = $pdo->query("SELECT id, updated_at FROM crmc_items WHERE is_active = 1 ORDER BY created_at DESC");
    $crmcItems = $stmt->fetchAll();
    
    foreach ($crmcItems as $item) {
        $url = "http://www.cccrj.com.br/crmc/{$item['id']}";
        $lastmod = date('Y-m-d', strtotime($item['updated_at']));
        
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$lastmod</lastmod>\n";
        $sitemap .= "    <changefreq>monthly</changefreq>\n";
        $sitemap .= "    <priority>0.5</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    echo "✓ " . count($crmcItems) . " itens do CRMC adicionados\n\n";
    
    // 6. Adicionar seções sobre
    echo "6. Adicionando seções sobre...\n";
    
    $stmt = $pdo->query("SELECT id, updated_at FROM about_sections WHERE is_active = 1 ORDER BY `order`");
    $aboutSections = $stmt->fetchAll();
    
    foreach ($aboutSections as $section) {
        $url = "http://www.cccrj.com.br/sobre/{$section['id']}";
        $lastmod = date('Y-m-d', strtotime($section['updated_at']));
        
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$lastmod</lastmod>\n";
        $sitemap .= "    <changefreq>monthly</changefreq>\n";
        $sitemap .= "    <priority>0.7</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    echo "✓ " . count($aboutSections) . " seções sobre adicionadas\n\n";
    
    // 7. Adicionar itens do acervo
    echo "7. Adicionando itens do acervo...\n";
    
    $stmt = $pdo->query("SELECT id, date, updated_at FROM archive_items WHERE is_active = 1 ORDER BY date DESC");
    $archiveItems = $stmt->fetchAll();
    
    foreach ($archiveItems as $item) {
        $url = "http://www.cccrj.com.br/acervo/{$item['id']}";
        $lastmod = date('Y-m-d', strtotime($item['updated_at']));
        $date = date('Y-m-d', strtotime($item['date']));
        
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>$url</loc>\n";
        $sitemap .= "    <lastmod>$lastmod</lastmod>\n";
        $sitemap .= "    <changefreq>yearly</changefreq>\n";
        $sitemap .= "    <priority>0.4</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    echo "✓ " . count($archiveItems) . " itens do acervo adicionados\n\n";
    
    // Fechar sitemap
    $sitemap .= '</urlset>';
    
    // Salvar sitemap
    $sitemapFile = 'sitemap.xml';
    file_put_contents($sitemapFile, $sitemap);
    
    echo "✅ Mapa do site gerado com sucesso!\n";
    echo "\nArquivo salvo: $sitemapFile\n";
    echo "Total de URLs: " . (count($staticPages) + count($events) + count($publications) + count($clippings) + count($crmcItems) + count($aboutSections) + count($archiveItems)) . "\n";
    
    // 8. Criar sitemap index (para sites grandes)
    echo "\n8. Criando sitemap index...\n";
    
    $sitemapIndex = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemapIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $sitemapIndex .= "  <sitemap>\n";
    $sitemapIndex .= "    <loc>http://www.cccrj.com.br/sitemap.xml</loc>\n";
    $sitemapIndex .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    $sitemapIndex .= "  </sitemap>\n";
    $sitemapIndex .= '</sitemapindex>';
    
    $sitemapIndexFile = 'sitemap_index.xml';
    file_put_contents($sitemapIndexFile, $sitemapIndex);
    
    echo "✓ Sitemap index criado: $sitemapIndexFile\n";
    
    echo "\n✅ Geração completa do mapa do site concluída!\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao gerar mapa do site: " . $e->getMessage() . "\n";
}

?>