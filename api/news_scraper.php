<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

class NewsScraper {
    private $baseUrl = 'https://www.cecafe.com.br';
    private $newsUrl = 'https://www.cecafe.com.br/secao/publicacoes/noticias';
    private $cacheDir = '../cache/';
    private $dataDir = '../data/';
    
    public function __construct() {
        // Criar diretórios se não existirem
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    // Função para forçar atualização manual - irá buscar notícias e salvar em JSON estático
    public function forceUpdate() {
        try {
            // Buscar notícias atuais
            $html = $this->fetchPage($this->newsUrl);
            $newNews = $this->extractNews($html);
            
            // Salvar todas as notícias como arquivo JSON estático
            $jsonFile = $this->dataDir . "news.json";
            $result = [
                'noticias' => $newNews,
                'ultima_atualizacao' => date('Y-m-d H:i:s')
            ];
            
            file_put_contents($jsonFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Registrar data de atualização
            $lastUpdateFile = $this->cacheDir . 'last_update.txt';
            file_put_contents($lastUpdateFile, date('Y-m-d'));
            
            return [
                'updated' => true,
                'items' => count($newNews),
                'message' => 'Notícias atualizadas manualmente e salvas em JSON estático',
                'json_file' => $jsonFile
            ];
        } catch (Exception $e) {
            return ['updated' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function fetchPage($url) {
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
    
    private function extractNews($html) {
        $news = [];
        
        // Usar DOMDocument para parsing HTML
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suprimir erros de parsing
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        // Localizar elementos das notícias
        // Com base nos testes, as notícias estão em elementos <article>
        $xpath = new DOMXPath($dom);
        $newsItems = $xpath->query("//article");
        
        foreach ($newsItems as $item) {
            try {
                $newsItem = $this->parseNewsItem($item, $xpath);
                if ($newsItem) {
                    $news[] = $newsItem;
                }
            } catch (Exception $e) {
                // Ignorar itens que não puderam ser parseados
                continue;
            }
        }
        
        return $news;
    }
    
    private function parseNewsItem($item, $xpath) {
        // Extrair dados da notícia
        // Com base nos testes, identificamos os seletores corretos
        
        // Título
        $titleNode = $xpath->query(".//h2[contains(@class, 'title') or contains(@class, 'titulo')] | .//h2", $item)->item(0);
        if (!$titleNode) return null;
        
        $title = trim($titleNode->textContent);
        
        // Link
        $linkNode = $xpath->query(".//h2/a | .//h3/a | .//a", $item)->item(0);
        if ($linkNode) {
            $href = $linkNode->getAttribute('href');
            // Verificar se o link já é absoluto
            if (strpos($href, 'http') === 0) {
                $link = $href;
            } else {
                $link = $this->baseUrl . $href;
            }
        } else {
            $link = '';
        }
        
        // Data - tentar encontrar no HTML primeiro, se não encontrar ou se não for parseável, tentar extrair do URL
        $date = date('Y-m-d'); // Valor padrão
        $dateNode = $xpath->query(".//time | .//*[@class='date' or contains(@class, 'data') or contains(@class, 'published') or contains(@class, 'pub-date') or contains(@class, 'post-date')] | .//*[@datetime] | .//*[contains(text(), '/') and string-length(text()) < 20]", $item)->item(0);
        
        if ($dateNode) {
            // Primeiro tenta parsear a data do HTML
            $htmlDate = $this->parseDate(trim($dateNode->textContent));
            // Se a data parseada é diferente da data padrão, significa que foi parseada com sucesso
            if ($htmlDate != date('Y-m-d')) {
                $date = $htmlDate;
            } else {
                // A data do HTML não foi parseada com sucesso, tentar extrair do URL
                if (preg_match('/(\d{4})(\d{2})(\d{2})\/$/', $link, $dateMatches)) {
                    $year = $dateMatches[1];
                    $month = $dateMatches[2];
                    $day = $dateMatches[3];
                    $date = "$year-$month-$day";
                } elseif (preg_match('/(\d{4})(\d{2})(\d{2})(?:\/|$)/', $link, $dateMatches)) {
                    // Segunda tentativa: captura data no formato YYYYMMDD seguida de / ou fim da string
                    $year = $dateMatches[1];
                    $month = $dateMatches[2];
                    $day = $dateMatches[3];
                    $date = "$year-$month-$day";
                }
            }
        } else {
            // Nenhum elemento de data encontrado no HTML, tentar extrair do URL
            if (preg_match('/(\d{4})(\d{2})(\d{2})\/$/', $link, $dateMatches)) {
                $year = $dateMatches[1];
                $month = $dateMatches[2];
                $day = $dateMatches[3];
                $date = "$year-$month-$day";
            } elseif (preg_match('/(\d{4})(\d{2})(\d{2})(?:\/|$)/', $link, $dateMatches)) {
                // Segunda tentativa: captura data no formato YYYYMMDD seguida de / ou fim da string
                $year = $dateMatches[1];
                $month = $dateMatches[2];
                $day = $dateMatches[3];
                $date = "$year-$month-$day";
            }
        }
        
        // Resumo
        $contentNodes = $xpath->query(".//div[contains(@class, 'content')] | .//div[contains(@class, 'excerpt')] | .//div[contains(@class, 'resumo')] | .//p", $item);
        $summary = '';
        if ($contentNodes->length > 0) {
            $summary = $this->createSummary($contentNodes->item(0)->textContent);
        }
        
        // Categoria (tentar determinar a partir do conteúdo)
        $category = $this->categorizeNews($title, $summary);
        
        return [
            'id' => crc32($link), // Gerar ID baseado no link
            'categoria' => $category,
            'data' => $date,
            'titulo' => $title,
            'resumo' => $summary,
            'conteudo' => '', // Será preenchido quando buscar notícia individual
            'icone' => $this->getIconByCategory($category),
            'link' => $link
        ];
    }
    
    private function parseDate($dateString) {
        // Remover espaços extras e caracteres especiais
        $dateString = trim($dateString);
        
        // Converter datas em vários formatos para "YYYY-MM-DD"
        $months = [
            'jan' => '01', 'fev' => '02', 'mar' => '03', 'abr' => '04',
            'mai' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
            'set' => '09', 'out' => '10', 'nov' => '11', 'dez' => '12',
            'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04',
            'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
            'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12',
            'ene' => '01', 'feb' => '02', 'mar' => '03', 'abr' => '04', 'abr' => '04', 
            'may' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
            'sep' => '09', 'oct' => '10', 'nov' => '11', 'dic' => '12'
        ];
        
        // Formato: "9 set / 2025" ou "9 set 2025"
        if (preg_match('/(\d{1,2})\s+([a-z]{3,9})\s*[\/\-\s]\s*(\d{4})/i', strtolower($dateString), $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = $months[$matches[2]] ?? '01';
            $year = $matches[3];
            return "$year-$month-$day";
        }
        
        // Formato: "9 de setembro de 2025"
        if (preg_match('/(\d{1,2})\s+de\s+([a-zç]+)\s+de\s+(\d{4})/i', strtolower($dateString), $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            // Transformar o nome do mês para a abreviação de 3 letras para buscar no array
            $monthName = substr($matches[2], 0, 3);
            $month = $months[$monthName] ?? '01';
            $year = $matches[3];
            return "$year-$month-$day";
        }
        
        // Formato: "2025-09-29" (já no formato correto)
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $dateString)) {
            return substr($dateString, 0, 10); // Retorna apenas a parte da data
        }
        
        // Formato: "29/09/2025"
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $dateString, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            return "$year-$month-$day";
        }
        
        // Formato: "09/29/2025" (MM/DD/YYYY)
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $dateString, $matches)) {
            $month = $matches[1];
            $day = $matches[2];
            $year = $matches[3];
            // Verificar se o primeiro número é maior que 12, então é dia/mês em vez de mês/dia
            if ($month > 12) {
                // É formato DD/MM/YYYY
                $aux = $month;
                $month = $day;
                $day = $aux;
            }
            return "$year-$month-$day";
        }
        
        // Formato ISO com timezone "2025-09-29T12:30:00-03:00" ou "2025-09-29T12:30:00"
        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $dateString, $matches)) {
            return $matches[1];
        }
        
        // Formato: "September 29, 2025" ou "29 September 2025"
        if (preg_match('/([a-zç]+)\s+(\d{1,2}),?\s+(\d{4})/i', $dateString, $matches) || 
            preg_match('/(\d{1,2})\s+([a-zç]+)\s+(\d{4})/i', $dateString, $matches)) {
            // $matches[0] = full match
            // $matches[1] and $matches[3] contain either day or month name
            // $matches[2] and $matches[3] contain either year or month name/day
            
            // Determine which is the day and which is the month name based on content
            if (is_numeric($matches[1])) {
                // Format: "Month Day, Year" or "Day Month Year"
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $monthName = substr(strtolower($matches[2]), 0, 3);
                $year = $matches[3];
            } else {
                $day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $monthName = substr(strtolower($matches[1]), 0, 3);
                $year = $matches[3];
            }
            
            $month = $months[$monthName] ?? '01';
            return "$year-$month-$day";
        }
        
        // Se não conseguir parsear, retornar data atual
        return date('Y-m-d');
    }
    
    private function categorizeNews($title, $summary) {
        $text = strtolower($title . ' ' . $summary);
        
        if (strpos($text, 'produção') !== false || strpos($text, 'colheit') !== false) {
            return 'Produção';
        }
        
        if (strpos($text, 'exportação') !== false || strpos($text, 'export') !== false) {
            return 'Exportação';
        }
        
        if (strpos($text, 'evento') !== false || strpos($text, 'feira') !== false ||
            strpos($text, 'congresso') !== false || strpos($text, 'seminário') !== false) {
            return 'Evento';
        }
        
        return 'Produção'; // Categoria padrão
    }
    
    private function createSummary($content) {
        // Limitar a 100 caracteres e remover tags HTML
        $cleanContent = strip_tags($content);
        if (strlen($cleanContent) > 100) {
            return substr($cleanContent, 0, 97) . '...';
        }
        return $cleanContent;
    }
    
    private function getIconByCategory($category) {
        $icons = [
            'Produção' => 'agriculture',
            'Exportação' => 'article',
            'Evento' => 'event'
        ];
        
        return $icons[$category] ?? 'article';
    }
    
    // Função para obter notícias do arquivo JSON estático
    public function getNewsFromStaticFile() {
        $jsonFile = $this->dataDir . "news.json";
        if (file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            return json_decode($jsonContent, true);
        }
        return null;
    }
    
    public function fetchFullNews($url) {
        try {
            $html = $this->fetchPage($url);
            
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            
            $xpath = new DOMXPath($dom);
            
            // Extrair conteúdo completo da notícia
            // Nota: O seletor precisa ser ajustado com base na estrutura real do site
            $contentNodes = $xpath->query("//div[contains(@class, 'content') or contains(@class, 'noticia-conteudo')]");
            
            if ($contentNodes->length > 0) {
                $content = '';
                foreach ($contentNodes as $node) {
                    $content .= $dom->saveHTML($node);
                }
                return $content;
            }
            
            return 'Conteúdo não disponível';
        } catch (Exception $e) {
            return 'Erro ao carregar conteúdo: ' . $e->getMessage();
        }
    }
    
    // Função para log de erros
    private function logError($message) {
        $logFile = $this->cacheDir . "../logs/news_scraper.log";
        error_log("[" . date('Y-m-d H:i:s') . "] NewsScraper Error: " . $message . "\n", 3, $logFile);
    }
}

// Processar requisição
try {
    $scraper = new NewsScraper();
    
    // Verificar parâmetros de requisição
    if (isset($_GET['url'])) {
        // Buscar conteúdo completo de uma notícia
        $content = $scraper->fetchFullNews($_GET['url']);
        $response = ['conteudo' => $content];
    } else if (isset($_GET['force_update'])) {
        // Forçar atualização manual - busca e salva em JSON estático
        $result = $scraper->forceUpdate();
        $response = $result;
    } else if (isset($_GET['get_static_news'])) {
        // Obter notícias do arquivo JSON estático
        $result = $scraper->getNewsFromStaticFile();
        if ($result) {
            $response = $result;
        } else {
            $response = ['noticias' => [], 'ultima_atualizacao' => null];
        }
    } else {
        // Por padrão, obter notícias do arquivo estático
        $result = $scraper->getNewsFromStaticFile();
        if ($result) {
            $response = $result;
        } else {
            // Se não houver arquivo, tentar atualizar
            $updateResult = $scraper->forceUpdate();
            if ($updateResult['updated']) {
                $result = $scraper->getNewsFromStaticFile();
                $response = $result ?: ['noticias' => [], 'ultima_atualizacao' => null];
            } else {
                $response = ['noticias' => [], 'ultima_atualizacao' => null, 'error' => $updateResult['error']];
            }
        }
    }
    
    // Garantir que a saída seja sempre JSON válido
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>