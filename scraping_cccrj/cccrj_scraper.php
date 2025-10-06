<?php
/**
 * Scraper para o site CCCRJ (Centro de Comércio do Café do Rio de Janeiro)
 * Este script faz o scraping do site http://www.cccrj.com.br/
 * salvando todos os conteúdos e páginas secundárias em arquivos locais
 */

class CCCRJScraper {
    private $baseUrl = "http://www.cccrj.com.br/";
    private $outputDir = "cccrj_content";
    private $visitedUrls = [];
    private $delay = 1; // segundos entre requisições

    public function __construct() {
        // Criar diretório de saída
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    private function isValidUrl($url) {
        // Verificar se a URL pertence ao domínio do site
        return strpos($url, $this->baseUrl) === 0;
    }

    private function normalizeUrl($url) {
        // Remover fragmentos e normalizar a URL
        $url = strtok($url, '#');
        return $url;
    }

    private function getPage($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (compatible; CCCRJ-Scraper/1.0; +http://www.cccrj.com.br/)',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ],
                'timeout' => 30
            ]
        ]);

        $content = @file_get_contents($url, false, $context);
        return $content;
    }

    private function saveContent($content, $path) {
        // Criar diretório se não existir
        $fullPath = $this->outputDir . $path;
        $dir = dirname($fullPath);
        
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $content);
        echo "Salvo: $fullPath\n";
    }

    private function extractLinks($html, $baseUrl) {
        $links = [];
        $dom = new DOMDocument();
        
        // Suprimir erros de HTML inválido
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        $linkElements = $xpath->query('//a[@href]');
        
        foreach ($linkElements as $link) {
            $href = $link->getAttribute('href');
            if ($href) {
                $fullUrl = $this->resolveUrl($href, $baseUrl);
                if ($this->isValidUrl($fullUrl)) {
                    $normalizedUrl = $this->normalizeUrl($fullUrl);
                    $links[] = $normalizedUrl;
                }
            }
        }
        
        // Procurar também por links em outros possíveis elementos
        $otherElements = $xpath->query('//link[@href] | //script[@src] | //img[@src]');
        
        foreach ($otherElements as $element) {
            $attrName = $element->nodeName === 'img' ? 'src' : 'href';
            $attrValue = $element->getAttribute($attrName);
            if ($attrValue) {
                $fullUrl = $this->resolveUrl($attrValue, $baseUrl);
                if ($this->isValidUrl($fullUrl)) {
                    $normalizedUrl = $this->normalizeUrl($fullUrl);
                    $links[] = $normalizedUrl;
                }
            }
        }
        
        return array_unique($links);
    }

    private function resolveUrl($url, $baseUrl) {
        // Resolver URLs relativas
        if (strpos($url, 'http') === 0) {
            // URL absoluta
            return $url;
        } elseif (strpos($url, '//') === 0) {
            // URL com protocolo relativo
            $parsed = parse_url($baseUrl);
            return $parsed['scheme'] . ':' . $url;
        } elseif (strpos($url, '/') === 0) {
            // URL relativa à raiz
            $parsed = parse_url($baseUrl);
            return $parsed['scheme'] . '://' . $parsed['host'] . $url;
        } else {
            // URL relativa
            $path = dirname(parse_url($baseUrl, PHP_URL_PATH));
            if ($path === '/') {
                $path = '';
            }
            return dirname($baseUrl) . '/' . $url;
        }
    }

    private function isHtmlContent($content) {
        // Verificar se o conteúdo parece ser HTML
        return stripos($content, '<html') !== false || 
               stripos($content, '<head') !== false || 
               stripos($content, '<body') !== false ||
               stripos($content, '<!DOCTYPE') !== false;
    }

    public function scrapeUrl($url) {
        if (in_array($url, $this->visitedUrls)) {
            return [];
        }

        echo "Scrapping: $url\n";
        
        $content = $this->getPage($url);
        if ($content === false) {
            echo "Erro ao obter conteúdo de: $url\n";
            return [];
        }

        // Determinar o caminho do arquivo com base na URL
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'];
        
        // Determinar extensão se não estiver definida
        if (!preg_match('/\.[a-z0-9]+$/i', basename($path))) {
            if ($this->isHtmlContent($content)) {
                $path = rtrim($path, '/') . '/index.html';
            } else {
                // Por padrão, salva como HTML se não tiver extensão
                $path .= '.html';
            }
        }
        
        // Salvar o conteúdo da página
        $this->saveContent($content, $path);
        
        // Marcar como visitado
        $this->visitedUrls[] = $url;
        
        // Extrair links se for uma página HTML
        $links = [];
        if ($this->isHtmlContent($content)) {
            $links = $this->extractLinks($content, $url);
        }
        
        // Respeitar o delay entre requisições
        sleep($this->delay);
        
        return $links;
    }

    public function run() {
        // URLs iniciais para scraping
        $urlsToVisit = [
            $this->baseUrl,
            $this->baseUrl . 'index.htm',
            $this->baseUrl . 'mapa.htm',
            $this->baseUrl . 'fale.htm',
            $this->baseUrl . 'Style.css',
        ];
        
        $urlsToVisit = array_unique($urlsToVisit);
        
        while (!empty($urlsToVisit)) {
            $currentUrl = array_shift($urlsToVisit);
            
            if (!in_array($currentUrl, $this->visitedUrls)) {
                $newLinks = $this->scrapeUrl($currentUrl);
                
                // Adicionar novos links à fila de visitação,
                // desde que não tenham sido visitados ainda
                foreach ($newLinks as $link) {
                    if (!in_array($link, $this->visitedUrls) && 
                        !in_array($link, $urlsToVisit)) {
                        $urlsToVisit[] = $link;
                    }
                }
            }
        }
        
        // Criar um arquivo de resumo
        $this->createSummary();
    }

    private function createSummary() {
        $summaryPath = $this->outputDir . '/resumo_scraping.txt';
        $summaryContent = "Resumo do Scraping do Site CCCRJ\n";
        $summaryContent .= str_repeat("=", 40) . "\n";
        $summaryContent .= "Data do scraping: " . date('Y-m-d H:i:s') . "\n";
        $summaryContent .= "URL base: " . $this->baseUrl . "\n";
        $summaryContent .= "Total de páginas/arquivos processados: " . count($this->visitedUrls) . "\n";
        $summaryContent .= "\nPáginas/arquivos salvos:\n";
        
        foreach ($this->visitedUrls as $url) {
            $summaryContent .= "- $url\n";
        }
        
        file_put_contents($summaryPath, $summaryContent);
        echo "Arquivo de resumo criado: $summaryPath\n";
    }
}

/**
 * Script para encontrar e baixar imagens do site CCCRJ
 */
class ImageFinder {
    private $baseUrl = "http://www.cccrj.com.br/";
    private $outputDir = "cccrj_content/images";
    private $visitedPages = [];
    private $downloadedImages = [];
    private $delay = 1; // segundos entre requisições

    public function __construct() {
        // Criar diretório de saída para imagens
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    private function getPageContent($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (compatible; CCCRJ-Scraper/1.0; +http://www.cccrj.com.br/)',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ],
                'timeout' => 30
            ]
        ]);

        $content = @file_get_contents($url, false, $context);
        return $content;
    }

    private function findImagesInPage($html) {
        $images = [];
        $dom = new DOMDocument();
        
        // Suprimir erros de HTML inválido
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        $imgElements = $xpath->query('//img[@src]');
        
        foreach ($imgElements as $img) {
            $src = $img->getAttribute('src');
            if ($src) {
                $fullUrl = $this->resolveUrl($src, $this->baseUrl);
                if ($this->isValidImageUrl($fullUrl)) {
                    $images[] = $fullUrl;
                }
            }
        }
        
        return array_unique($images);
    }

    private function isValidImageUrl($url) {
        // Verificar se é uma URL válida e tem extensão de imagem
        $validExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp'];
        $extension = strtolower(strrchr(parse_url($url, PHP_URL_PATH), '.'));
        return in_array($extension, $validExtensions) && filter_var($url, FILTER_VALIDATE_URL);
    }

    private function resolveUrl($url, $baseUrl) {
        // Resolver URLs relativas
        if (strpos($url, 'http') === 0) {
            // URL absoluta
            return $url;
        } elseif (strpos($url, '//') === 0) {
            // URL com protocolo relativo
            $parsed = parse_url($baseUrl);
            return $parsed['scheme'] . ':' . $url;
        } elseif (strpos($url, '/') === 0) {
            // URL relativa à raiz
            $parsed = parse_url($baseUrl);
            return $parsed['scheme'] . '://' . $parsed['host'] . $url;
        } else {
            // URL relativa
            $basePath = dirname(parse_url($baseUrl, PHP_URL_PATH));
            if ($basePath === '/') {
                $basePath = '';
            }
            return dirname($baseUrl) . '/' . $url;
        }
    }

    public function downloadImage($url) {
        if (in_array($url, $this->downloadedImages)) {
            return;
        }

        echo "Baixando imagem: $url\n";
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (compatible; CCCRJ-Scraper/1.0; +http://www.cccrj.com.br/)',
                    'Accept: image/*'
                ],
                'timeout' => 30
            ]
        ]);

        $imageData = @file_get_contents($url, false, $context);
        if ($imageData !== false) {
            $filename = basename(parse_url($url, PHP_URL_PATH));
            if (!$filename || strpos($filename, '.') === false) {
                $filename = 'imagem_' . count($this->downloadedImages) . '.jpg';
            }
            
            $filepath = $this->outputDir . '/' . $filename;
            file_put_contents($filepath, $imageData);
            echo "Imagem salva: $filepath\n";
            
            $this->downloadedImages[] = $url;
        } else {
            echo "Erro ao baixar imagem: $url\n";
        }
        
        sleep($this->delay);
    }

    public function findAndDownloadImages() {
        // URLs de páginas para verificar por imagens
        $pages = [
            $this->baseUrl,
            $this->baseUrl . 'index.htm',
            $this->baseUrl . 'mapa.htm',
            $this->baseUrl . 'fale.htm',
            $this->baseUrl . 'cccrj/inicio.htm',
            $this->baseUrl . 'crmc/inicio.htm',
            $this->baseUrl . 'revista/inicio.htm',
            $this->baseUrl . 'terminal/inicio.htm',
            $this->baseUrl . 'rio/inicio.htm',
            $this->baseUrl . 'links/inicio.htm'
        ];
        
        foreach ($pages as $pageUrl) {
            if (in_array($pageUrl, $this->visitedPages)) {
                continue;
            }
            
            echo "Verificando imagens em: $pageUrl\n";
            $content = $this->getPageContent($pageUrl);
            
            if ($content !== false) {
                $images = $this->findImagesInPage($content);
                
                foreach ($images as $imageUrl) {
                    $this->downloadImage($imageUrl);
                }
                
                $this->visitedPages[] = $pageUrl;
            }
        }
    }
}

// Função principal para executar o scraping
function runScraper() {
    echo "Iniciando processo de scraping do site CCCRJ...\n";

    // Executar o scraper principal
    echo "\nEtapa 1: Executando scraper principal...\n";
    $start = microtime(true);

    $scraper = new CCCRJScraper();
    $scraper->run();

    $end = microtime(true);
    echo "Scraper principal concluído em " . ($end - $start) . " segundos\n";

    // Executar o finder de imagens
    echo "\nEtapa 2: Procurando e baixando imagens adicionais...\n";
    $start = microtime(true);

    $imageFinder = new ImageFinder();
    $imageFinder->findAndDownloadImages();

    $end = microtime(true);
    echo "Download de imagens concluído em " . ($end - $start) . " segundos\n";

    echo "\nProcesso de scraping concluído com sucesso!\n";
    echo "Conteúdos salvos na pasta: cccrj_content\n";
}

// Executar o scraping se chamado diretamente
if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__) || php_sapi_name() === 'cli') {
    runScraper();
}
?>