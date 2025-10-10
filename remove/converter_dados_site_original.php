<?php
// converter_dados_site_original.php

require_once 'utils/html_parser.php';

class ScrapeToJsonConverter {
    private $scrapingDir;
    private $outputDir;
    
    public function __construct($scrapingDir, $outputDir) {
        $this->scrapingDir = $scrapingDir;
        $this->outputDir = $outputDir;
        
        // Criar diretórios de saída se não existirem
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
        
        if (!is_dir($this->outputDir . '/content')) {
            mkdir($this->outputDir . '/content', 0755, true);
        }
    }
    
    public function convertAll() {
        echo "Iniciando conversão de dados do site original...\n";
        
        $this->convertClippings();
        $this->convertPublications();
        $this->convertHistory();
        $this->convertArchive();
        $this->convertAbout();
        $this->convertCrmc();
        
        echo "Conversão concluída!\n";
        
        // Atualizar índices
        $this->updateIndex();
    }
    
    private function convertClippings() {
        echo "Convertendo clippings...\n";
        $clippings = [];
        
        // Procurar arquivos HTML de clipping
        $files = glob($this->scrapingDir . '/clipping/*.htm*');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $title = HtmlParser::extractTitle($content);
            $textSummary = HtmlParser::extractTextSummary($content, 200);
            
            $clipping = [
                'id' => uniqid(),
                'title' => $title ?: 'Clipping sem título',
                'summary' => $textSummary,
                'content' => HtmlParser::extractContent($content),
                'source_url' => null,
                'date' => $this->extractDateFromFilename(basename($file)),
                'category' => 'Notícia',
                'is_active' => true
            ];
            
            $clippings[] = $clipping;
        }
        
        // Salvar em JSON
        $this->saveToJson('clipping.json', ['data' => $clippings]);
        echo "  - " . count($clippings) . " clippings convertidos\n";
    }
    
    private function convertPublications() {
        echo "Convertendo publicações...\n";
        $publications = [];
        
        // Procurar arquivos HTML de revistas
        $directories = glob($this->scrapingDir . '/revista/*', GLOB_ONLYDIR);
        
        foreach ($directories as $dir) {
            $files = glob($dir . '/inicio.htm');
            
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $title = HtmlParser::extractTitle($content);
                $textSummary = HtmlParser::extractTextSummary($content, 200);
                
                $publication = [
                    'id' => uniqid(),
                    'title' => $title ?: 'Publicação sem título',
                    'description' => $textSummary,
                    'file_path' => str_replace($this->scrapingDir . '/', '', $file),
                    'date' => $this->extractDateFromDirectory(basename($dir)),
                    'number' => basename($dir),
                    'type' => 'revista',
                    'is_active' => true
                ];
                
                $publications[] = $publication;
            }
        }
        
        // Salvar em JSON
        $this->saveToJson('publications.json', ['data' => $publications]);
        echo "  - " . count($publications) . " publicações convertidas\n";
    }
    
    private function convertHistory() {
        echo "Convertendo histórico...\n";
        $history = [];
        
        // Procurar arquivos HTML do diretório cccrj
        $files = glob($this->scrapingDir . '/cccrj/*.htm*');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $title = HtmlParser::extractTitle($content);
            $textSummary = HtmlParser::extractTextSummary($content, 200);
            
            $event = [
                'id' => uniqid(),
                'title' => $title ?: 'Evento sem título',
                'description' => $textSummary,
                'content' => HtmlParser::extractContent($content),
                'date' => $this->extractDateFromFilename(basename($file)),
                'event_type' => 'institucional',
                'image_url' => null,
                'is_featured' => 0,
                'is_active' => 1
            ];
            
            $history[] = $event;
        }
        
        // Salvar em JSON
        $this->saveToJson('history.json', ['data' => $history]);
        echo "  - " . count($history) . " eventos históricos convertidos\n";
    }
    
    private function convertArchive() {
        echo "Convertendo acervo...\n";
        $archive = [];
        
        // Procurar arquivos HTML do diretório acervo
        $files = glob($this->scrapingDir . '/acervo/*.htm*');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $title = HtmlParser::extractTitle($content);
            $textSummary = HtmlParser::extractTextSummary($content, 200);
            
            $item = [
                'id' => uniqid(),
                'title' => $title ?: 'Item de acervo sem título',
                'description' => $textSummary,
                'file_path' => str_replace($this->scrapingDir . '/', '', $file),
                'date' => $this->extractDateFromFilename(basename($file)),
                'item_type' => 'documento',
                'category' => 'institucional',
                'metadata' => null,
                'is_active' => 1
            ];
            
            $archive[] = $item;
        }
        
        // Salvar em JSON
        $this->saveToJson('archive.json', ['data' => $archive]);
        echo "  - " . count($archive) . " itens de acervo convertidos\n";
    }
    
    private function convertAbout() {
        echo "Convertendo seções sobre...\n";
        $about = [];
        
        // Incluir páginas institucionais do diretório cccrj
        $files = [
            $this->scrapingDir . '/cccrj/constituicao.htm',
            $this->scrapingDir . '/cccrj/estatuto.htm',
            $this->scrapingDir . '/cccrj/diretorias.htm',
            $this->scrapingDir . '/cccrj/centenario.htm',
            $this->scrapingDir . '/cccrj/lancamento.htm'
        ];
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $title = HtmlParser::extractTitle($content);
                $textSummary = HtmlParser::extractTextSummary($content, 200);
                
                $section = [
                    'id' => uniqid(),
                    'title' => $title ?: 'Seção sem título',
                    'content' => HtmlParser::extractContent($content),
                    'section_type' => 'institucional',
                    'order' => 0,
                    'is_active' => 1,
                    'image_url' => null
                ];
                
                $about[] = $section;
            }
        }
        
        // Salvar em JSON
        $this->saveToJson('about.json', ['data' => $about]);
        echo "  - " . count($about) . " seções sobre convertidas\n";
    }
    
    private function convertCrmc() {
        echo "Convertendo conteúdo do CRMC...\n";
        $crmc = [];
        
        // Procurar arquivos HTML do diretório crmc
        $files = glob($this->scrapingDir . '/crmc/*.htm*');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $title = HtmlParser::extractTitle($content);
            $textSummary = HtmlParser::extractTextSummary($content, 200);
            
            $item = [
                'id' => uniqid(),
                'title' => $title ?: 'Conteúdo do CRMC sem título',
                'description' => $textSummary,
                'content' => HtmlParser::extractContent($content),
                'category' => 'cultural',
                'image_url' => null,
                'file_path' => str_replace($this->scrapingDir . '/', '', $file),
                'is_active' => 1
            ];
            
            $crmc[] = $item;
        }
        
        // Salvar em JSON
        $this->saveToJson('crmc.json', ['data' => $crmc]);
        echo "  - " . count($crmc) . " itens do CRMC convertidos\n";
    }
    
    private function extractDateFromFilename($filename) {
        // Extrair data do nome do arquivo
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $filename, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }
        
        // Verificar outros formatos de data
        if (preg_match('/(\d{2})-(\d{2})-(\d{4})/', $filename, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        return date('Y-m-d');
    }
    
    private function extractDateFromDirectory($dirname) {
        // Tenta extrair data do nome do diretório (para revistas)
        if (preg_match('/^(\d+)$/', $dirname, $matches)) {
            // Supondo que o nome do diretório seja o número da edição
            // Vamos usar uma data padrão ou tentar encontrar um padrão
            return date('Y-m-d');
        }
        
        return date('Y-m-d');
    }
    
    private function saveToJson($filename, $data) {
        $path = $this->outputDir . '/content/' . $filename;
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    private function updateIndex() {
        echo "Atualizando índices...\n";
        
        // Carregar dados para calcular contagens
        $clippingContent = file_get_contents($this->outputDir . '/content/clipping.json');
        $clippings = json_decode($clippingContent, true);
        
        $publicationContent = file_get_contents($this->outputDir . '/content/publications.json');
        $publications = json_decode($publicationContent, true);
        
        $historyContent = file_get_contents($this->outputDir . '/content/history.json');
        $history = json_decode($historyContent, true);
        
        $archiveContent = file_get_contents($this->outputDir . '/content/archive.json');
        $archive = json_decode($archiveContent, true);
        
        $aboutContent = file_get_contents($this->outputDir . '/content/about.json');
        $about = json_decode($aboutContent, true);
        
        $crmcContent = file_get_contents($this->outputDir . '/content/crmc.json');
        $crmc = json_decode($crmcContent, true);
        
        $index = [
            'last_update' => date('Y-m-d H:i:s'),
            'content_types' => [
                'clipping' => isset($clippings['data']) ? count($clippings['data']) : 0,
                'publications' => isset($publications['data']) ? count($publications['data']) : 0,
                'history' => isset($history['data']) ? count($history['data']) : 0,
                'archive' => isset($archive['data']) ? count($archive['data']) : 0,
                'about' => isset($about['data']) ? count($about['data']) : 0,
                'crmc' => isset($crmc['data']) ? count($crmc['data']) : 0
            ]
        ];
        
        // Salvar índice
        $indexPath = $this->outputDir . '/metadata/content_index.json';
        file_put_contents($indexPath, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Salvar timestamp de última atualização
        $timestamp = ['timestamp' => date('Y-m-d H:i:s')];
        $timestampPath = $this->outputDir . '/metadata/last_updated.json';
        file_put_contents($timestampPath, json_encode($timestamp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "Índices atualizados.\n";
    }
}

// Executar a conversão
$converter = new ScrapeToJsonConverter(
    'scraping_cccrj/cccrj_content',
    'data'
);

$converter->convertAll();
?>