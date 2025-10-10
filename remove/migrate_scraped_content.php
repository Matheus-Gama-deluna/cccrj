<?php
// migrate_scraped_content.php

// Script para migrar conteúdo raspado para o banco de dados

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';

try {
    $pdo = connectDatabase();
    
    // Função para extrair conteúdo de arquivos HTML raspados
    function extractContentFromFile($filePath) {
        if (!file_exists($filePath)) {
            return null;
        }
        
        $content = file_get_contents($filePath);
        
        // Remover tags HTML e extrair conteúdo textual
        $content = strip_tags($content);
        
        // Limpar e formatar o conteúdo
        $content = trim(preg_replace('/\s+/', ' ', $content));
        
        return $content;
    }
    
    // Função para extrair título de arquivos HTML
    function extractTitleFromFile($filePath) {
        if (!file_exists($filePath)) {
            return 'Título não disponível';
        }
        
        $content = file_get_contents($filePath);
        
        // Procurar por tag <title>
        if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $content, $matches)) {
            return trim($matches[1]);
        }
        
        // Se não encontrar <title>, procurar por cabeçalhos h1-h3
        if (preg_match('/<h[1-3][^>]*>(.*?)<\/h[1-3]>/si', $content, $matches)) {
            return trim(strip_tags($matches[1]));
        }
        
        return 'Título não disponível';
    }
    
    // Função para extrair data do nome do arquivo
    function extractDateFromFilename($filename) {
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
        
        // Se não encontrar data, retornar data atual
        return date('Y-m-d');
    }
    
    // Migrar conteúdo do diretório cccrj (informações institucionais)
    function migrateCccrjContent($pdo) {
        echo "Migrando conteúdo do CCCRJ...\n";
        
        $cccrjDir = 'scraping_cccrj/cccrj_content/cccrj';
        if (!is_dir($cccrjDir)) {
            echo "Diretório do CCCRJ não encontrado.\n";
            return;
        }
        
        $historicalEventModel = new HistoricalEvent();
        
        // Arquivos importantes do CCCRJ
        $importantFiles = [
            'constituicao.htm' => 'Constituição do CCCRJ',
            'estatuto.htm' => 'Estatuto do CCCRJ',
            'diretorias.htm' => 'Diretórios do CCCRJ',
            'centenario.htm' => 'Centenário do CCCRJ',
            'lancamento.htm' => 'Lançamento do Prédio Sede'
        ];
        
        foreach ($importantFiles as $file => $title) {
            $filePath = $cccrjDir . '/' . $file;
            if (file_exists($filePath)) {
                $content = extractContentFromFile($filePath);
                $date = extractDateFromFilename($file);
                
                // Criar evento histórico
                $data = [
                    'title' => $title,
                    'description' => substr($content, 0, 200) . '...',
                    'content' => $content,
                    'date' => $date,
                    'event_type' => 'institucional',
                    'image_url' => '',
                    'is_featured' => 1,
                    'is_active' => 1
                ];
                
                try {
                    $historicalEventModel->create($data);
                    echo "✓ Evento histórico '$title' criado com sucesso.\n";
                } catch (Exception $e) {
                    echo "✗ Erro ao criar evento histórico '$title': " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // Migrar conteúdo do CRMC
    function migrateCrmcContent($pdo) {
        echo "Migrando conteúdo do CRMC...\n";
        
        $crmcDir = 'scraping_cccrj/cccrj_content/crmc';
        if (!is_dir($crmcDir)) {
            echo "Diretório do CRMC não encontrado.\n";
            return;
        }
        
        $historicalEventModel = new HistoricalEvent();
        
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
                $content = extractContentFromFile($filePath);
                $date = extractDateFromFilename($file);
                
                // Criar evento histórico
                $data = [
                    'title' => $title,
                    'description' => substr($content, 0, 200) . '...',
                    'content' => $content,
                    'date' => $date,
                    'event_type' => 'cultural',
                    'image_url' => '',
                    'is_featured' => 0,
                    'is_active' => 1
                ];
                
                try {
                    $historicalEventModel->create($data);
                    echo "✓ Evento histórico '$title' criado com sucesso.\n";
                } catch (Exception $e) {
                    echo "✗ Erro ao criar evento histórico '$title': " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // Migrar revistas
    function migrateRevistasContent($pdo) {
        echo "Migrando conteúdo das Revistas...\n";
        
        $revistaDir = 'scraping_cccrj/cccrj_content/revista';
        if (!is_dir($revistaDir)) {
            echo "Diretório de revistas não encontrado.\n";
            return;
        }
        
        $publicationModel = new Publication();
        
        // Percorrer todas as edições de revista
        $editions = scandir($revistaDir);
        foreach ($editions as $edition) {
            if ($edition === '.' || $edition === '..') continue;
            
            $editionPath = $revistaDir . '/' . $edition;
            if (is_dir($editionPath)) {
                $inicioFile = $editionPath . '/inicio.htm';
                if (file_exists($inicioFile)) {
                    $title = extractTitleFromFile($inicioFile);
                    $content = extractContentFromFile($inicioFile);
                    $date = extractDateFromFilename($edition);
                    
                    // Criar publicação
                    $data = [
                        'title' => $title ?: "Revista do Café - Edição $edition",
                        'description' => "Edição $edition da Revista do Café do CCCRJ",
                        'file_path' => "revista/$edition/inicio.htm",
                        'date' => $date,
                        'number' => $edition,
                        'type' => 'revista',
                        'is_active' => 1
                    ];
                    
                    try {
                        $publicationModel->create($data);
                        echo "✓ Publicação 'Revista - Edição $edition' criada com sucesso.\n";
                    } catch (Exception $e) {
                        echo "✗ Erro ao criar publicação 'Revista - Edição $edition': " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
    
    // Migrar clipping
    function migrateClippingContent($pdo) {
        echo "Migrando conteúdo de Clipping...\n";
        
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
                        $title = extractTitleFromFile($filePath);
                        $content = extractContentFromFile($filePath);
                        $date = extractDateFromFilename($file);
                        
                        // Criar clipping
                        $data = [
                            'title' => $title ?: "Clipping - " . basename($file, '.htm'),
                            'summary' => substr($content, 0, 200) . '...',
                            'content' => $content,
                            'source_url' => '',
                            'date' => $date,
                            'category' => 'Notícia',
                            'is_active' => 1
                        ];
                        
                        try {
                            $clippingModel->create($data);
                            echo "✓ Clipping '" . basename($file, '.htm') . "' criado com sucesso.\n";
                        } catch (Exception $e) {
                            echo "✗ Erro ao criar clipping '" . basename($file, '.htm') . "': " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
        }
    }
    
    // Executar migrações
    migrateCccrjContent($pdo);
    migrateCrmcContent($pdo);
    migrateRevistasContent($pdo);
    migrateClippingContent($pdo);
    
    echo "Migração de conteúdo concluída!\n";
    
} catch (Exception $e) {
    echo "Erro durante a migração: " . $e->getMessage() . "\n";
}
?>