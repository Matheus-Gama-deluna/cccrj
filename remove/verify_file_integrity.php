<?php
// verify_file_integrity.php

// Script para verificar a integridade dos arquivos migrados

try {
    echo "Verificando integridade dos arquivos migrados...\n\n";
    
    // 1. Verificar diretórios de conteúdo
    echo "1. Verificando diretórios de conteúdo...\n";
    
    $contentDirs = [
        'scraping_cccrj/cccrj_content/cccrj' => 'Conteúdo do CCCRJ',
        'scraping_cccrj/cccrj_content/crmc' => 'Conteúdo do CRMC',
        'scraping_cccrj/cccrj_content/revista' => 'Revistas',
        'scraping_cccrj/cccrj_content/clipping' => 'Clipping',
        'scraping_cccrj/cccrj_content/noticias' => 'Notícias',
        'scraping_cccrj/cccrj_content/boletim' => 'Boletins',
        'scraping_cccrj/cccrj_content/rio' => 'Café no Rio',
        'scraping_cccrj/cccrj_content/acervo' => 'Acervo'
    ];
    
    foreach ($contentDirs as $dir => $description) {
        if (is_dir($dir)) {
            $fileCount = count(scandir($dir)) - 2; // Subtrair . e ..
            echo "✓ $description: $fileCount arquivos encontrados\n";
        } else {
            echo "✗ $description: diretório não encontrado\n";
        }
    }
    
    echo "\n";
    
    // 2. Verificar arquivos HTML importantes
    echo "2. Verificando arquivos HTML importantes...\n";
    
    $importantFiles = [
        'scraping_cccrj/cccrj_content/cccrj/constituicao.htm' => 'Constituição do CCCRJ',
        'scraping_cccrj/cccrj_content/cccrj/estatuto.htm' => 'Estatuto do CCCRJ',
        'scraping_cccrj/cccrj_content/cccrj/diretorias.htm' => 'Diretórios do CCCRJ',
        'scraping_cccrj/cccrj_content/cccrj/centenario.htm' => 'Centenário do CCCRJ',
        'scraping_cccrj/cccrj_content/crmc/inicio.htm' => 'Início do CRMC',
        'scraping_cccrj/cccrj_content/crmc/biblioteca.htm' => 'Biblioteca do CRMC',
        'scraping_cccrj/cccrj_content/crmc/cafeteria.htm' => 'Cafeteria do CRMC',
        'scraping_cccrj/cccrj_content/crmc/cultural.htm' => 'Programação Cultural do CRMC'
    ];
    
    foreach ($importantFiles as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            echo "✓ $description: " . number_format($size) . " bytes\n";
        } else {
            echo "✗ $description: arquivo não encontrado\n";
        }
    }
    
    echo "\n";
    
    // 3. Verificar estrutura de revistas
    echo "3. Verificando estrutura de revistas...\n";
    
    $revistaDir = 'scraping_cccrj/cccrj_content/revista';
    if (is_dir($revistaDir)) {
        $editions = array_filter(scandir($revistaDir), function($item) use ($revistaDir) {
            return $item !== '.' && $item !== '..' && is_dir("$revistaDir/$item");
        });
        
        echo "✓ " . count($editions) . " edições de revista encontradas\n";
        
        // Verificar algumas edições aleatórias
        $sampleEditions = array_slice($editions, 0, min(5, count($editions)));
        foreach ($sampleEditions as $edition) {
            $editionPath = "$revistaDir/$edition";
            if (is_dir($editionPath)) {
                $files = scandir($editionPath);
                $htmlFiles = array_filter($files, function($file) {
                    return preg_match('/\.(htm|html)$/i', $file);
                });
                
                echo "  - Edição $edition: " . count($htmlFiles) . " arquivos HTML\n";
            }
        }
    } else {
        echo "✗ Diretório de revistas não encontrado\n";
    }
    
    echo "\n";
    
    // 4. Verificar integridade dos arquivos HTML
    echo "4. Verificando integridade dos arquivos HTML...\n";
    
    function checkHtmlIntegrity($filePath) {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $content = file_get_contents($filePath);
        
        // Verificar se é um documento HTML válido
        $hasDoctype = stripos($content, '<!DOCTYPE') !== false;
        $hasHtmlTag = stripos($content, '<html') !== false;
        $hasHeadTag = stripos($content, '<head') !== false;
        $hasBodyTag = stripos($content, '<body') !== false;
        
        return $hasDoctype || $hasHtmlTag || $hasHeadTag || $hasBodyTag;
    }
    
    $sampleFiles = [
        'scraping_cccrj/cccrj_content/cccrj/constituicao.htm',
        'scraping_cccrj/cccrj_content/crmc/inicio.htm',
        'scraping_cccrj/cccrj_content/rio/inicio.htm'
    ];
    
    foreach ($sampleFiles as $file) {
        if (file_exists($file)) {
            if (checkHtmlIntegrity($file)) {
                echo "✓ $file: documento HTML válido\n";
            } else {
                echo "✗ $file: documento HTML inválido ou incompleto\n";
            }
        }
    }
    
    echo "\n";
    
    // 5. Verificar codificação dos arquivos
    echo "5. Verificando codificação dos arquivos...\n";
    
    function detectEncoding($filePath) {
        if (!file_exists($filePath)) {
            return 'Arquivo não encontrado';
        }
        
        $content = file_get_contents($filePath);
        
        // Detectar codificação
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252'];
        
        foreach ($encodings as $encoding) {
            if (mb_check_encoding($content, $encoding)) {
                // Verificar se há caracteres especiais
                if (preg_match('/[À-ÿ]/', $content)) {
                    return $encoding . ' (com caracteres acentuados)';
                }
                return $encoding;
            }
        }
        
        return 'Codificação desconhecida';
    }
    
    foreach ($sampleFiles as $file) {
        if (file_exists($file)) {
            $encoding = detectEncoding($file);
            echo "✓ $file: $encoding\n";
        }
    }
    
    echo "\n";
    
    // 6. Verificar tamanho total dos arquivos
    echo "6. Verificando tamanho total dos arquivos...\n";
    
    function getDirectorySize($path) {
        $size = 0;
        foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : getDirectorySize($each);
        }
        return $size;
    }
    
    foreach ($contentDirs as $dir => $description) {
        if (is_dir($dir)) {
            $size = getDirectorySize($dir);
            $sizeFormatted = formatBytes($size);
            echo "✓ $description: $sizeFormatted\n";
        }
    }
    
    echo "\n";
    
    // 7. Verificar links internos
    echo "7. Verificando links internos...\n";
    
    function extractInternalLinks($filePath) {
        if (!file_exists($filePath)) {
            return [];
        }
        
        $content = file_get_contents($filePath);
        $links = [];
        
        // Extrair links internos
        preg_match_all('/href=[\'"](.*?)[\'"]/i', $content, $matches);
        if (isset($matches[1])) {
            foreach ($matches[1] as $link) {
                // Verificar se é um link interno (começa com / ou .. ou é relativo)
                if (preg_match('/^(\/|\.\.|[^:\/])/i', $link)) {
                    $links[] = $link;
                }
            }
        }
        
        return array_unique($links);
    }
    
    $mainFiles = [
        'scraping_cccrj/cccrj_content/index.htm',
        'scraping_cccrj/cccrj_content/cccrj/inicio.htm',
        'scraping_cccrj/cccrj_content/crmc/inicio.htm'
    ];
    
    foreach ($mainFiles as $file) {
        if (file_exists($file)) {
            $links = extractInternalLinks($file);
            echo "✓ $file: " . count($links) . " links internos encontrados\n";
            
            // Verificar alguns links aleatórios
            $sampleLinks = array_slice($links, 0, min(3, count($links)));
            foreach ($sampleLinks as $link) {
                $targetPath = dirname($file) . '/' . $link;
                if (file_exists($targetPath) || is_dir($targetPath)) {
                    echo "    ✓ Link válido: $link\n";
                } else {
                    echo "    ✗ Link inválido: $link\n";
                }
            }
        }
    }
    
    echo "\n";
    
    // 8. Verificar imagens
    echo "8. Verificando imagens...\n";
    
    function findImages($dir) {
        $images = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/\.(jpg|jpeg|png|gif|bmp|svg)$/i', $file->getFilename())) {
                $images[] = $file->getPathname();
            }
        }
        
        return $images;
    }
    
    $imageDirs = [
        'scraping_cccrj/cccrj_content/Imagens' => 'Imagens gerais',
        'scraping_cccrj/cccrj_content/cccrj/Imagens' => 'Imagens do CCCRJ',
        'scraping_cccrj/cccrj_content/crmc/Imagens' => 'Imagens do CRMC'
    ];
    
    $totalImages = 0;
    foreach ($imageDirs as $dir => $description) {
        if (is_dir($dir)) {
            $images = findImages($dir);
            echo "✓ $description: " . count($images) . " imagens encontradas\n";
            $totalImages += count($images);
        }
    }
    
    echo "\nTotal de imagens encontradas: $totalImages\n\n";
    
    echo "✅ Verificação de integridade concluída!\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante a verificação: " . $e->getMessage() . "\n";
}

// Função auxiliar para formatar bytes
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}
?>