<?php
// populate_db_with_scraped_content.php

// Script para popular o banco de dados com conteúdo raspado do site original

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';
require_once 'api/models/AboutSection.php';
require_once 'api/models/CrmcItem.php';
require_once 'api/models/ArchiveItem.php';

try {
    echo "Populando banco de dados com conteúdo raspado...\n\n";
    
    $pdo = connectDatabase();
    
    // Função para extrair texto de HTML
    function extractTextFromHtml($html) {
        // Remover scripts e estilos
        $html = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $html);
        
        // Converter entidades HTML
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        
        // Remover tags HTML
        $text = strip_tags($html);
        
        // Limpar espaços extras
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
    
    // Função para extrair título de HTML
    function extractTitleFromHtml($html) {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $matches)) {
            return trim(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'));
        }
        
        // Se não encontrar title, procurar por h1
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/si', $html, $matches)) {
            return trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
        }
        
        return 'Título não disponível';
    }
    
    // Função para extrair data do nome do arquivo
    function extractDateFromFile($filename) {
        // Padrões comuns de data nos nomes de arquivos
        $patterns = [
            '/(\d{4})-(\d{2})-(\d{2})/',
            '/(\d{2})-(\d{2})-(\d{4})/',
            '/(\d{4})_(\d{2})_(\d{2})/',
            '/(\d{2})_(\d{2})_(\d{4})/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $filename, $matches)) {
                if (strlen($matches[1]) == 4) {
                    // Formato AAAA-MM-DD
                    return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                } else {
                    // Formato DD-MM-AAAA
                    return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                }
            }
        }
        
        // Se não encontrar data, usar data atual
        return date('Y-m-d');
    }
    
    // Função para ler conteúdo de arquivo HTML
    function readHtmlFile($filePath) {
        if (!file_exists($filePath)) {
            return null;
        }
        
        return file_get_contents($filePath);
    }
    
    // Popular conteúdo do CCCRJ
    function populateCccrjContent($pdo) {
        echo "Populando conteúdo do CCCRJ...\n";
        
        $cccrjDir = 'scraping_cccrj/cccrj_content/cccrj';
        if (!is_dir($cccrjDir)) {
            echo "Diretório do CCCRJ não encontrado.\n";
            return;
        }
        
        $historicalEventModel = new HistoricalEvent();
        $aboutSectionModel = new AboutSection();
        
        // Arquivos importantes do CCCRJ
        $importantFiles = [
            'constituicao.htm' => [
                'title' => 'Constituição do CCCRJ',
                'type' => 'institucional',
                'featured' => true
            ],
            'estatuto.htm' => [
                'title' => 'Estatuto do CCCRJ',
                'type' => 'institucional',
                'featured' => true
            ],
            'diretorias.htm' => [
                'title' => 'Diretórios do CCCRJ',
                'type' => 'institucional',
                'featured' => false
            ],
            'centenario.htm' => [
                'title' => 'Centenário do CCCRJ',
                'type' => 'historico',
                'featured' => true
            ],
            'lancamento.htm' => [
                'title' => 'Lançamento do Prédio Sede',
                'type' => 'historico',
                'featured' => true
            ]
        ];
        
        foreach ($importantFiles as $file => $info) {
            $filePath = $cccrjDir . '/' . $file;
            if (file_exists($filePath)) {
                $html = readHtmlFile($filePath);
                if ($html) {
                    $title = extractTitleFromHtml($html) ?: $info['title'];
                    $content = extractTextFromHtml($html);
                    $date = extractDateFromFile($file);
                    
                    // Criar evento histórico
                    $historicalEventData = [
                        'title' => $title,
                        'description' => substr($content, 0, 200) . '...',
                        'content' => $content,
                        'date' => $date,
                        'event_type' => $info['type'],
                        'image_url' => '',
                        'is_featured' => $info['featured'] ? 1 : 0,
                        'is_active' => 1
                    ];
                    
                    try {
                        $historicalEventModel->create($historicalEventData);
                        echo "✓ Evento histórico '$title' criado.\n";
                    } catch (Exception $e) {
                        echo "✗ Erro ao criar evento histórico '$title': " . $e->getMessage() . "\n";
                    }
                    
                    // Criar seção sobre
                    $aboutSectionData = [
                        'title' => $title,
                        'content' => $content,
                        'section_type' => $info['type'],
                        'order' => 0,
                        'is_active' => 1,
                        'image_url' => ''
                    ];
                    
                    try {
                        $aboutSectionModel->create($aboutSectionData);
                        echo "✓ Seção sobre '$title' criada.\n";
                    } catch (Exception $e) {
                        echo "✗ Erro ao criar seção sobre '$title': " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
    
    // Popular conteúdo do CRMC
    function populateCrmcContent($pdo) {
        echo "\nPopulando conteúdo do CRMC...\n";
        
        $crmcDir = 'scraping_cccrj/cccrj_content/crmc';
        if (!is_dir($crmcDir)) {
            echo "Diretório do CRMC não encontrado.\n";
            return;
        }
        
        $crmcItemModel = new CrmcItem();
        
        // Arquivos importantes do CRMC
        $importantFiles = [
            'biblioteca.htm' => 'Biblioteca do CRMC',
            'cafeteria.htm' => 'Cafeteria Temática',
            'cultural.htm' => 'Programação Cultural',
            'dicas.htm' => 'Dicas do CRMC',
            'exposicao.htm' => 'Exposições',
            'fotos.htm' => 'Galeria de Fotos',
            'visitas.htm' => 'Visitas Guiadas'
        ];
        
        foreach ($importantFiles as $file => $title) {
            $filePath = $crmcDir . '/' . $file;
            if (file_exists($filePath)) {
                $html = readHtmlFile($filePath);
                if ($html) {
                    $content = extractTextFromHtml($html);
                    $date = extractDateFromFile($file);
                    
                    // Criar item do CRMC
                    $crmcItemData = [
                        'title' => $title,
                        'description' => substr($content, 0, 200) . '...',
                        'content' => $content,
                        'category' => 'cultural',
                        'image_url' => '',
                        'file_path' => "crmc/$file",
                        'is_active' => 1
                    ];
                    
                    try {
                        $crmcItemModel->create($crmcItemData);
                        echo "✓ Item do CRMC '$title' criado.\n";
                    } catch (Exception $e) {
                        echo "✗ Erro ao criar item do CRMC '$title': " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
    
    // Popular revistas
    function populateRevistasContent($pdo) {
        echo "\nPopulando conteúdo das Revistas...\n";
        
        $revistaDir = 'scraping_cccrj/cccrj_content/revista';
        if (!is_dir($revistaDir)) {
            echo "Diretório de revistas não encontrado.\n";
            return;
        }
        
        $publicationModel = new Publication();
        
        // Percorrer todas as edições
        $editions = scandir($revistaDir);
        foreach ($editions as $edition) {
            if ($edition === '.' || $edition === '..') continue;
            
            $editionPath = $revistaDir . '/' . $edition;
            if (is_dir($editionPath)) {
                $inicioFile = $editionPath . '/inicio.htm';
                if (file_exists($inicioFile)) {
                    $html = readHtmlFile($inicioFile);
                    if ($html) {
                        $title = extractTitleFromHtml($html) ?: "Revista do Café - Edição $edition";
                        $content = extractTextFromHtml($html);
                        $date = extractDateFromFile($edition);
                        
                        // Criar publicação
                        $publicationData = [
                            'title' => $title,
                            'description' => "Edição $edition da Revista do Café do CCCRJ",
                            'file_path' => "revista/$edition/inicio.htm",
                            'date' => $date,
                            'number' => $edition,
                            'type' => 'revista',
                            'is_active' => 1
                        ];
                        
                        try {
                            $publicationModel->create($publicationData);
                            echo "✓ Publicação 'Revista - Edição $edition' criada.\n";
                        } catch (Exception $e) {
                            echo "✗ Erro ao criar publicação 'Revista - Edição $edition': " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
        }
    }
    
    // Popular clipping
    function populateClippingContent($pdo) {
        echo "\nPopulando conteúdo de Clipping...\n";
        
        // Diretórios que podem conter clipping
        $clippingDirs = [
            'scraping_cccrj/cccrj_content/clipping',
            'scraping_cccrj/cccrj_content/noticias'
        ];
        
        $clippingModel = new Clipping();
        
        foreach ($clippingDirs as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    
                    $filePath = $dir . '/' . $file;
                    if (is_file($filePath) && preg_match('/\.(htm|html)$/i', $file)) {
                        $html = readHtmlFile($filePath);
                        if ($html) {
                            $title = extractTitleFromHtml($html) ?: "Clipping - " . basename($file, '.htm');
                            $content = extractTextFromHtml($html);
                            $date = extractDateFromFile($file);
                            
                            // Criar clipping
                            $clippingData = [
                                'title' => $title,
                                'summary' => substr($content, 0, 200) . '...',
                                'content' => $content,
                                'source_url' => '',
                                'date' => $date,
                                'category' => 'Notícia',
                                'is_active' => 1
                            ];
                            
                            try {
                                $clippingModel->create($clippingData);
                                echo "✓ Clipping '" . basename($file, '.htm') . "' criado.\n";
                            } catch (Exception $e) {
                                echo "✗ Erro ao criar clipping '" . basename($file, '.htm') . "': " . $e->getMessage() . "\n";
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Popular conteúdo do café no Rio
    function populateRioContent($pdo) {
        echo "\nPopulando conteúdo do Café no Rio...\n";
        
        $rioDir = 'scraping_cccrj/cccrj_content/rio';
        if (!is_dir($rioDir)) {
            echo "Diretório do Café no Rio não encontrado.\n";
            return;
        }
        
        $historicalEventModel = new HistoricalEvent();
        $aboutSectionModel = new AboutSection();
        
        // Arquivos importantes do Café no Rio
        $importantFiles = [
            'cafe.htm' => 'Café no Rio de Janeiro',
            'cidade.htm' => 'O Café e a Cidade',
            'exportacao.htm' => 'Exportação do Café',
            'historia.htm' => 'História do Café no Rio',
            'orgulho.htm' => 'Orgulho Carioca',
            'ousadia.htm' => 'Ousadia e Iniciativa',
            'producao.htm' => 'Produção de Café'
        ];
        
        foreach ($importantFiles as $file => $title) {
            $filePath = $rioDir . '/' . $file;
            if (file_exists($filePath)) {
                $html = readHtmlFile($filePath);
                if ($html) {
                    $content = extractTextFromHtml($html);
                    $date = extractDateFromFile($file);
                    
                    // Criar evento histórico
                    $historicalEventData = [
                        'title' => $title,
                        'description' => substr($content, 0, 200) . '...',
                        'content' => $content,
                        'date' => $date,
                        'event_type' => 'historico',
                        'image_url' => '',
                        'is_featured' => false,
                        'is_active' => 1
                    ];
                    
                    try {
                        $historicalEventModel->create($historicalEventData);
                        echo "✓ Evento histórico '$title' criado.\n";
                    } catch (Exception $e) {
                        echo "✗ Erro ao criar evento histórico '$title': " . $e->getMessage() . "\n";
                    }
                    
                    // Criar seção sobre
                    $aboutSectionData = [
                        'title' => $title,
                        'content' => $content,
                        'section_type' => 'historico',
                        'order' => 0,
                        'is_active' => 1,
                        'image_url' => ''
                    ];
                    
                    try {
                        $aboutSectionModel->create($aboutSectionData);
                        echo "✓ Seção sobre '$title' criada.\n";
                    } catch (Exception $e) {
                        echo "✗ Erro ao criar seção sobre '$title': " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
    
    // Executar população
    populateCccrjContent($pdo);
    populateCrmcContent($pdo);
    populateRevistasContent($pdo);
    populateClippingContent($pdo);
    populateRioContent($pdo);
    
    echo "\n✅ População do banco de dados concluída!\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante a população: " . $e->getMessage() . "\n";
}
?>