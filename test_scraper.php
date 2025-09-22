<?php
// Script de teste para verificar a estrutura do site do CECAFÉ

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
    echo "=== Teste de Estrutura do Site do CECAFÉ ===\n\n";
    
    try {
        // Buscar a página principal de notícias
        $url = 'https://www.cecafe.com.br/secao/publicacoes/noticias/';
        echo "Buscando página: $url\n";
        
        $html = fetchPage($url);
        echo "Página carregada com sucesso! Tamanho: " . strlen($html) . " bytes\n\n";
        
        // Salvar o HTML para inspeção
        file_put_contents('cecafe_page.html', $html);
        echo "HTML salvo em cecafe_page.html para inspeção\n\n";
        
        // Verificar se é HTML válido
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        echo "Estrutura HTML verificada com sucesso!\n\n";
        
        // Tentar identificar elementos de notícias
        $xpath = new DOMXPath($dom);
        
        // Procurar por elementos que possam conter notícias
        $selectors = [
            "//article",
            "//div[contains(@class, 'views-row')]",
            "//div[contains(@class, 'noticia')]",
            "//div[contains(@class, 'post')]",
            "//div[contains(@class, 'node')]"
        ];
        
        echo "Tentando identificar elementos de notícias...\n";
        
        foreach ($selectors as $selector) {
            $elements = $xpath->query($selector);
            echo "Selector '$selector': " . $elements->length . " elementos encontrados\n";
        }
        
        echo "\n=== Teste Concluído ===\n";
        echo "Verifique o arquivo cecafe_page.html para analisar a estrutura do site.\n";
        
    } catch (Exception $e) {
        echo "Erro ao testar o site: " . $e->getMessage() . "\n";
        return false;
    }
    
    return true;
}

// Executar o teste
testSiteStructure();
?>