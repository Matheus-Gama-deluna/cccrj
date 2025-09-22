<?php
// Script de teste detalhado para verificar a estrutura do site do CECAFÉ

function fetchPage($url) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]
    ]);
    
    $content = file_get_contents($url, false, $context);
    if ($content === FALSE) {
        throw new Exception("Falha ao carregar página: $url");
    }
    
    return $content;
}

function testSiteStructure() {
    echo "=== Teste Detalhado de Estrutura do Site do CECAFÉ ===\n\n";
    
    try {
        // Buscar a página principal de notícias
        $url = 'https://www.cecafe.com.br/secao/publicacoes/noticias/';
        echo "Buscando página: $url\n";
        
        $html = fetchPage($url);
        echo "Página carregada com sucesso! Tamanho: " . strlen($html) . " bytes\n\n";
        
        // Verificar se é HTML válido
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        echo "Estrutura HTML verificada com sucesso!\n\n";
        
        // Tentar identificar elementos de notícias
        $xpath = new DOMXPath($dom);
        
        // Procurar por artigos ou divs que possam conter notícias
        echo "Procurando por elementos de notícias...\n";
        
        // Testar seletores específicos
        $selectors = [
            "//article",
            "//div[contains(@class, 'post')]",
            "//div[@class='post']",
            "//div[contains(@class, 'hentry')]",
            "//div[contains(@class, 'entry')]"
        ];
        
        foreach ($selectors as $selector) {
            $elements = $xpath->query($selector);
            echo "Selector '$selector': " . $elements->length . " elementos encontrados\n";
            
            // Mostrar detalhes dos primeiros elementos
            if ($elements->length > 0) {
                echo "  Examinando os 2 primeiros elementos:\n";
                for ($i = 0; $i < min(2, $elements->length); $i++) {
                    $element = $elements->item($i);
                    $text = trim(preg_replace('/\s+/', ' ', substr($element->textContent, 0, 100)));
                    echo "    Elemento $i: '$text...'\n";
                }
            }
        }
        
        // Procurar por links de notícias
        echo "\nProcurando por links de notícias...\n";
        $linkElements = $xpath->query("//a[contains(@href, '/publicacoes/noticias/')]");
        echo "Links para notícias encontrados: " . $linkElements->length . "\n";
        
        if ($linkElements->length > 0) {
            echo "Examinando os 3 primeiros links:\n";
            for ($i = 0; $i < min(3, $linkElements->length); $i++) {
                $link = $linkElements->item($i);
                $href = $link->getAttribute('href');
                $text = trim(preg_replace('/\s+/', ' ', substr($link->textContent, 0, 50)));
                echo "  Link $i: '$text...' -> $href\n";
            }
        }
        
        // Procurar por títulos de notícias (h1, h2, h3, etc.)
        echo "\nProcurando por títulos de notícias...\n";
        $titleSelectors = [
            "//h1[contains(@class, 'title') or contains(@class, 'titulo')]",
            "//h2[contains(@class, 'title') or contains(@class, 'titulo')]",
            "//h3[contains(@class, 'title') or contains(@class, 'titulo')]",
            "//h1",
            "//h2",
            "//h3"
        ];
        
        foreach ($titleSelectors as $selector) {
            $titles = $xpath->query($selector);
            if ($titles->length > 0) {
                echo "Selector '$selector': " . $titles->length . " elementos encontrados\n";
                echo "  Primeiro título: '" . trim(preg_replace('/\s+/', ' ', substr($titles->item(0)->textContent, 0, 60))) . "...'\n";
                break;
            }
        }
        
        // Procurar por datas
        echo "\nProcurando por datas...\n";
        $dateElements = $xpath->query("//time | //*[@class='date' or @class='data'] | //*[contains(text(), '/')]");
        echo "Elementos que podem conter datas: " . $dateElements->length . "\n";
        
        // Procurar por conteúdo/resumo
        echo "\nProcurando por conteúdo/resumo...\n";
        $contentSelectors = [
            "//div[contains(@class, 'content')]",
            "//div[contains(@class, 'excerpt')]",
            "//div[contains(@class, 'resumo')]",
            "//p"
        ];
        
        foreach ($contentSelectors as $selector) {
            $contents = $xpath->query($selector);
            if ($contents->length > 0) {
                echo "Selector '$selector': " . $contents->length . " elementos encontrados\n";
                $firstContent = trim(preg_replace('/\s+/', ' ', substr($contents->item(0)->textContent, 0, 60)));
                echo "  Primeiro conteúdo: '$firstContent...'\n";
                break;
            }
        }
        
        echo "\n=== Teste Concluído ===\n";
        echo "Com base na análise, podemos verificar se a estrutura do site é compatível com o web scraper.\n";
        
    } catch (Exception $e) {
        echo "Erro ao testar o site: " . $e->getMessage() . "\n";
        return false;
    }
    
    return true;
}

// Executar o teste
testSiteStructure();
?>