<?php
// generate_scraped_content_stats.php

// Script para gerar estatísticas do conteúdo raspado

echo "Gerando estatísticas do conteúdo raspado...\n\n";

// 1. Coletar estatísticas gerais
echo "1. Coletando estatísticas gerais...\n";

function collectGeneralStats() {
    $stats = [
        'timestamp' => date('Y-m-d H:i:s'),
        'total_files' => 0,
        'total_size' => 0,
        'file_types' => [],
        'directories' => [],
        'content_analysis' => []
    ];
    
    // Diretórios principais do conteúdo raspado
    $contentDirs = [
        'scraping_cccrj/cccrj_content/cccrj' => 'CCCRJ',
        'scraping_cccrj/cccrj_content/crmc' => 'CRMC',
        'scraping_cccrj/cccrj_content/revista' => 'Revista',
        'scraping_cccrj/cccrj_content/rio' => 'Café no Rio',
        'scraping_cccrj/cccrj_content/clipping' => 'Clipping',
        'scraping_cccrj/cccrj_content/noticias' => 'Notícias',
        'scraping_cccrj/cccrj_content/boletim' => 'Boletim',
        'scraping_cccrj/cccrj_content/acervo' => 'Acervo',
        'scraping_cccrj/cccrj_content/links' => 'Links',
        'scraping_cccrj/cccrj_content/fale' => 'Fale Conosco'
    ];
    
    foreach ($contentDirs as $dir => $description) {
        if (is_dir($dir)) {
            echo "✓ Analisando diretório '$description'...\n";
            $dirStats = analyzeDirectory($dir);
            $stats['directories'][$description] = $dirStats;
            
            $stats['total_files'] += $dirStats['file_count'];
            $stats['total_size'] += $dirStats['total_size'];
            
            // Consolidar tipos de arquivo
            foreach ($dirStats['file_types'] as $type => $count) {
                if (!isset($stats['file_types'][$type])) {
                    $stats['file_types'][$type] = 0;
                }
                $stats['file_types'][$type] += $count;
            }
        } else {
            echo "✗ Diretório '$description' não encontrado\n";
        }
    }
    
    $stats['total_size_formatted'] = formatBytes($stats['total_size']);
    
    return $stats;
}

function analyzeDirectory($dirPath) {
    $dirStats = [
        'file_count' => 0,
        'total_size' => 0,
        'file_types' => [],
        'subdirectories' => 0,
        'html_files' => 0,
        'image_files' => 0,
        'pdf_files' => 0,
        'other_files' => 0
    ];
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $dirStats['file_count']++;
            $size = $file->getSize();
            $dirStats['total_size'] += $size;
            
            // Obter extensão do arquivo
            $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            
            if ($extension) {
                if (!isset($dirStats['file_types'][$extension])) {
                    $dirStats['file_types'][$extension] = 0;
                }
                $dirStats['file_types'][$extension]++;
            }
            
            // Classificar por tipo
            switch ($extension) {
                case 'htm':
                case 'html':
                    $dirStats['html_files']++;
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'bmp':
                case 'svg':
                    $dirStats['image_files']++;
                    break;
                case 'pdf':
                    $dirStats['pdf_files']++;
                    break;
                default:
                    $dirStats['other_files']++;
                    break;
            }
        }
    }
    
    // Contar subdiretórios
    $subdirs = glob("$dirPath/*", GLOB_ONLYDIR);
    $dirStats['subdirectories'] = count($subdirs);
    
    $dirStats['total_size_formatted'] = formatBytes($dirStats['total_size']);
    
    return $dirStats;
}

function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

// Coletar estatísticas gerais
$generalStats = collectGeneralStats();
echo "\n✓ Estatísticas gerais coletadas\n\n";

// 2. Análise de conteúdo HTML
echo "2. Analisando conteúdo HTML...\n";

function analyzeHtmlContent($stats) {
    $htmlAnalysis = [
        'total_html_files' => 0,
        'avg_file_size' => 0,
        'largest_file' => '',
        'largest_file_size' => 0,
        'smallest_file' => '',
        'smallest_file_size' => PHP_INT_MAX,
        'common_tags' => [],
        'content_patterns' => []
    ];
    
    // Padrões comuns de conteúdo
    $contentPatterns = [
        'tables' => '/<table[^>]*>.*?<\/table>/si',
        'images' => '/<img[^>]*>/i',
        'links' => '/<a[^>]*>.*?<\/a>/si',
        'forms' => '/<form[^>]*>.*?<\/form>/si',
        'scripts' => '/<script[^>]*>.*?<\/script>/si',
        'styles' => '/<style[^>]*>.*?<\/style>/si',
        'frames' => '/<frame[^>]*>/i',
        'iframes' => '/<iframe[^>]*>.*?<\/iframe>/si'
    ];
    
    $totalHtmlSize = 0;
    $htmlFileCount = 0;
    
    foreach ($stats['directories'] as $dirName => $dirStats) {
        if ($dirStats['html_files'] > 0) {
            $htmlAnalysis['total_html_files'] += $dirStats['html_files'];
            
            // Analisar arquivos HTML individuais
            $dirPath = array_search($dirName, [
                'scraping_cccrj/cccrj_content/cccrj' => 'CCCRJ',
                'scraping_cccrj/cccrj_content/crmc' => 'CRMC',
                'scraping_cccrj/cccrj_content/revista' => 'Revista',
                'scraping_cccrj/cccrj_content/rio' => 'Café no Rio',
                'scraping_cccrj/cccrj_content/clipping' => 'Clipping',
                'scraping_cccrj/cccrj_content/noticias' => 'Notícias',
                'scraping_cccrj/cccrj_content/boletim' => 'Boletim',
                'scraping_cccrj/cccrj_content/acervo' => 'Acervo',
                'scraping_cccrj/cccrj_content/links' => 'Links',
                'scraping_cccrj/cccrj_content/fale' => 'Fale Conosco'
            ]);
            
            if ($dirPath && is_dir($dirPath)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && preg_match('/\.(htm|html)$/i', $file->getFilename())) {
                        $htmlFileCount++;
                        $size = $file->getSize();
                        $totalHtmlSize += $size;
                        
                        // Atualizar maior e menor arquivos
                        if ($size > $htmlAnalysis['largest_file_size']) {
                            $htmlAnalysis['largest_file'] = $file->getPathname();
                            $htmlAnalysis['largest_file_size'] = $size;
                        }
                        
                        if ($size < $htmlAnalysis['smallest_file_size']) {
                            $htmlAnalysis['smallest_file'] = $file->getPathname();
                            $htmlAnalysis['smallest_file_size'] = $size;
                        }
                        
                        // Analisar conteúdo do arquivo
                        $content = file_get_contents($file->getPathname());
                        
                        // Contar tags comuns
                        $commonTags = ['div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'table', 'tr', 'td', 'th'];
                        foreach ($commonTags as $tag) {
                            $count = preg_match_all("/<$tag[^>]*>/i", $content);
                            if ($count > 0) {
                                if (!isset($htmlAnalysis['common_tags'][$tag])) {
                                    $htmlAnalysis['common_tags'][$tag] = 0;
                                }
                                $htmlAnalysis['common_tags'][$tag] += $count;
                            }
                        }
                        
                        // Verificar padrões de conteúdo
                        foreach ($contentPatterns as $patternName => $pattern) {
                            $count = preg_match_all($pattern, $content);
                            if ($count > 0) {
                                if (!isset($htmlAnalysis['content_patterns'][$patternName])) {
                                    $htmlAnalysis['content_patterns'][$patternName] = 0;
                                }
                                $htmlAnalysis['content_patterns'][$patternName] += $count;
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Calcular tamanho médio
    if ($htmlFileCount > 0) {
        $htmlAnalysis['avg_file_size'] = $totalHtmlSize / $htmlFileCount;
        $htmlAnalysis['avg_file_size_formatted'] = formatBytes($htmlAnalysis['avg_file_size']);
    }
    
    $htmlAnalysis['largest_file_size_formatted'] = formatBytes($htmlAnalysis['largest_file_size']);
    $htmlAnalysis['smallest_file_size_formatted'] = formatBytes($htmlAnalysis['smallest_file_size']);
    
    return $htmlAnalysis;
}

$htmlAnalysis = analyzeHtmlContent($generalStats);
echo "✓ Análise de conteúdo HTML concluída\n\n";

// 3. Análise de imagens
echo "3. Analisando imagens...\n";

function analyzeImages($stats) {
    $imageAnalysis = [
        'total_images' => 0,
        'image_formats' => [],
        'total_image_size' => 0,
        'avg_image_size' => 0,
        'largest_image' => '',
        'largest_image_size' => 0,
        'smallest_image' => '',
        'smallest_image_size' => PHP_INT_MAX
    ];
    
    $totalImageSize = 0;
    $imageCount = 0;
    
    foreach ($stats['directories'] as $dirName => $dirStats) {
        if ($dirStats['image_files'] > 0) {
            $imageAnalysis['total_images'] += $dirStats['image_files'];
            
            // Analisar arquivos de imagem individuais
            $dirPath = array_search($dirName, [
                'scraping_cccrj/cccrj_content/cccrj' => 'CCCRJ',
                'scraping_cccrj/cccrj_content/crmc' => 'CRMC',
                'scraping_cccrj/cccrj_content/revista' => 'Revista',
                'scraping_cccrj/cccrj_content/rio' => 'Café no Rio',
                'scraping_cccrj/cccrj_content/clipping' => 'Clipping',
                'scraping_cccrj/cccrj_content/noticias' => 'Notícias',
                'scraping_cccrj/cccrj_content/boletim' => 'Boletim',
                'scraping_cccrj/cccrj_content/acervo' => 'Acervo',
                'scraping_cccrj/cccrj_content/links' => 'Links',
                'scraping_cccrj/cccrj_content/fale' => 'Fale Conosco'
            ]);
            
            if ($dirPath && is_dir($dirPath)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && preg_match('/\.(jpg|jpeg|png|gif|bmp|svg)$/i', $file->getFilename())) {
                        $imageCount++;
                        $size = $file->getSize();
                        $totalImageSize += $size;
                        
                        // Atualizar maior e menor imagens
                        if ($size > $imageAnalysis['largest_image_size']) {
                            $imageAnalysis['largest_image'] = $file->getPathname();
                            $imageAnalysis['largest_image_size'] = $size;
                        }
                        
                        if ($size < $imageAnalysis['smallest_image_size']) {
                            $imageAnalysis['smallest_image'] = $file->getPathname();
                            $imageAnalysis['smallest_image_size'] = $size;
                        }
                        
                        // Contar formatos de imagem
                        $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                        if (!isset($imageAnalysis['image_formats'][$extension])) {
                            $imageAnalysis['image_formats'][$extension] = 0;
                        }
                        $imageAnalysis['image_formats'][$extension]++;
                    }
                }
            }
        }
    }
    
    // Calcular tamanho médio
    if ($imageCount > 0) {
        $imageAnalysis['total_image_size'] = $totalImageSize;
        $imageAnalysis['avg_image_size'] = $totalImageSize / $imageCount;
        $imageAnalysis['avg_image_size_formatted'] = formatBytes($imageAnalysis['avg_image_size']);
        $imageAnalysis['total_image_size_formatted'] = formatBytes($imageAnalysis['total_image_size']);
    }
    
    $imageAnalysis['largest_image_size_formatted'] = formatBytes($imageAnalysis['largest_image_size']);
    $imageAnalysis['smallest_image_size_formatted'] = formatBytes($imageAnalysis['smallest_image_size']);
    
    return $imageAnalysis;
}

$imageAnalysis = analyzeImages($generalStats);
echo "✓ Análise de imagens concluída\n\n";

// 4. Análise de PDFs
echo "4. Analisando PDFs...\n";

function analyzePdfs($stats) {
    $pdfAnalysis = [
        'total_pdfs' => 0,
        'total_pdf_size' => 0,
        'avg_pdf_size' => 0,
        'largest_pdf' => '',
        'largest_pdf_size' => 0,
        'smallest_pdf' => '',
        'smallest_pdf_size' => PHP_INT_MAX
    ];
    
    $totalPdfSize = 0;
    $pdfCount = 0;
    
    foreach ($stats['directories'] as $dirName => $dirStats) {
        if ($dirStats['pdf_files'] > 0) {
            $pdfAnalysis['total_pdfs'] += $dirStats['pdf_files'];
            
            // Analisar arquivos PDF individuais
            $dirPath = array_search($dirName, [
                'scraping_cccrj/cccrj_content/cccrj' => 'CCCRJ',
                'scraping_cccrj/cccrj_content/crmc' => 'CRMC',
                'scraping_cccrj/cccrj_content/revista' => 'Revista',
                'scraping_cccrj/cccrj_content/rio' => 'Café no Rio',
                'scraping_cccrj/cccrj_content/clipping' => 'Clipping',
                'scraping_cccrj/cccrj_content/noticias' => 'Notícias',
                'scraping_cccrj/cccrj_content/boletim' => 'Boletim',
                'scraping_cccrj/cccrj_content/acervo' => 'Acervo',
                'scraping_cccrj/cccrj_content/links' => 'Links',
                'scraping_cccrj/cccrj_content/fale' => 'Fale Conosco'
            ]);
            
            if ($dirPath && is_dir($dirPath)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && preg_match('/\.pdf$/i', $file->getFilename())) {
                        $pdfCount++;
                        $size = $file->getSize();
                        $totalPdfSize += $size;
                        
                        // Atualizar maior e menor PDFs
                        if ($size > $pdfAnalysis['largest_pdf_size']) {
                            $pdfAnalysis['largest_pdf'] = $file->getPathname();
                            $pdfAnalysis['largest_pdf_size'] = $size;
                        }
                        
                        if ($size < $pdfAnalysis['smallest_pdf_size']) {
                            $pdfAnalysis['smallest_pdf'] = $file->getPathname();
                            $pdfAnalysis['smallest_pdf_size'] = $size;
                        }
                    }
                }
            }
        }
    }
    
    // Calcular tamanho médio
    if ($pdfCount > 0) {
        $pdfAnalysis['total_pdf_size'] = $totalPdfSize;
        $pdfAnalysis['avg_pdf_size'] = $totalPdfSize / $pdfCount;
        $pdfAnalysis['avg_pdf_size_formatted'] = formatBytes($pdfAnalysis['avg_pdf_size']);
        $pdfAnalysis['total_pdf_size_formatted'] = formatBytes($pdfAnalysis['total_pdf_size']);
    }
    
    $pdfAnalysis['largest_pdf_size_formatted'] = formatBytes($pdfAnalysis['largest_pdf_size']);
    $pdfAnalysis['smallest_pdf_size_formatted'] = formatBytes($pdfAnalysis['smallest_pdf_size']);
    
    return $pdfAnalysis;
}

$pdfAnalysis = analyzePdfs($generalStats);
echo "✓ Análise de PDFs concluída\n\n";

// 5. Gerar relatório de estatísticas
echo "5. Gerando relatório de estatísticas...\n";

$statsReport = [
    'general_stats' => $generalStats,
    'html_analysis' => $htmlAnalysis,
    'image_analysis' => $imageAnalysis,
    'pdf_analysis' => $pdfAnalysis
];

// Salvar relatório em arquivo JSON
$reportsDir = 'reports';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

$timestamp = date('Y-m-d-H-i-s');
$statsFile = "$reportsDir/scraped_content_stats_$timestamp.json";
file_put_contents($statsFile, json_encode($statsReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ Relatório de estatísticas salvo em: $statsFile\n\n";

// 6. Gerar relatório em formato legível
echo "6. Gerando relatório em formato legível...\n";

$readableReport = "RELATÓRIO DE ESTATÍSTICAS DO CONTEÚDO RASPADO\n";
$readableReport .= "============================================\n\n";
$readableReport .= "Data da geração: " . $statsReport['general_stats']['timestamp'] . "\n\n";

$readableReport .= "ESTATÍSTICAS GERAIS\n";
$readableReport .= "------------------\n";
$readableReport .= "Total de arquivos: " . number_format($statsReport['general_stats']['total_files']) . "\n";
$readableReport .= "Tamanho total: " . $statsReport['general_stats']['total_size_formatted'] . "\n\n";

$readableReport .= "Diretórios analisados:\n";
foreach ($statsReport['general_stats']['directories'] as $name => $dirStats) {
    $readableReport .= "✓ $name: " . number_format($dirStats['file_count']) . " arquivos, " . $dirStats['total_size_formatted'] . "\n";
}
$readableReport .= "\n";

$readableReport .= "Tipos de arquivo:\n";
arsort($statsReport['general_stats']['file_types']);
foreach ($statsReport['general_stats']['file_types'] as $type => $count) {
    $readableReport .= "✓ .$type: " . number_format($count) . " arquivos\n";
}
$readableReport .= "\n";

$readableReport .= "ANÁLISE DE CONTEÚDO HTML\n";
$readableReport .= "-----------------------\n";
$readableReport .= "Total de arquivos HTML: " . number_format($statsReport['html_analysis']['total_html_files']) . "\n";
$readableReport .= "Tamanho médio de arquivos HTML: " . $statsReport['html_analysis']['avg_file_size_formatted'] . "\n";
$readableReport .= "Maior arquivo HTML: " . $statsReport['html_analysis']['largest_file_size_formatted'] . "\n";
$readableReport .= "Menor arquivo HTML: " . $statsReport['html_analysis']['smallest_file_size_formatted'] . "\n\n";

$readableReport .= "Tags HTML mais comuns:\n";
arsort($statsReport['html_analysis']['common_tags']);
foreach (array_slice($statsReport['html_analysis']['common_tags'], 0, 10) as $tag => $count) {
    $readableReport .= "✓ <$tag>: " . number_format($count) . " ocorrências\n";
}
$readableReport .= "\n";

$readableReport .= "Padrões de conteúdo encontrados:\n";
arsort($statsReport['html_analysis']['content_patterns']);
foreach ($statsReport['html_analysis']['content_patterns'] as $pattern => $count) {
    $readableReport .= "✓ $pattern: " . number_format($count) . " ocorrências\n";
}
$readableReport .= "\n";

$readableReport .= "ANÁLISE DE IMAGENS\n";
$readableReport .= "-----------------\n";
$readableReport .= "Total de imagens: " . number_format($statsReport['image_analysis']['total_images']) . "\n";
$readableReport .= "Tamanho total de imagens: " . $statsReport['image_analysis']['total_image_size_formatted'] . "\n";
$readableReport .= "Tamanho médio de imagens: " . $statsReport['image_analysis']['avg_image_size_formatted'] . "\n";
$readableReport .= "Maior imagem: " . $statsReport['image_analysis']['largest_image_size_formatted'] . "\n";
$readableReport .= "Menor imagem: " . $statsReport['image_analysis']['smallest_image_size_formatted'] . "\n\n";

$readableReport .= "Formatos de imagem:\n";
arsort($statsReport['image_analysis']['image_formats']);
foreach ($statsReport['image_analysis']['image_formats'] as $format => $count) {
    $readableReport .= "✓ .$format: " . number_format($count) . " arquivos\n";
}
$readableReport .= "\n";

$readableReport .= "ANÁLISE DE PDFs\n";
$readableReport .= "--------------\n";
$readableReport .= "Total de PDFs: " . number_format($statsReport['pdf_analysis']['total_pdfs']) . "\n";
$readableReport .= "Tamanho total de PDFs: " . $statsReport['pdf_analysis']['total_pdf_size_formatted'] . "\n";
$readableReport .= "Tamanho médio de PDFs: " . $statsReport['pdf_analysis']['avg_pdf_size_formatted'] . "\n";
$readableReport .= "Maior PDF: " . $statsReport['pdf_analysis']['largest_pdf_size_formatted'] . "\n";
$readableReport .= "Menor PDF: " . $statsReport['pdf_analysis']['smallest_pdf_size_formatted'] . "\n\n";

$readableReportFile = "$reportsDir/scraped_content_stats_$timestamp.txt";
file_put_contents($readableReportFile, $readableReport);

echo "✓ Relatório legível salvo em: $readableReportFile\n\n";

echo "✅ Relatório de estatísticas do conteúdo raspado gerado com sucesso!\n";
echo "\nRelatórios gerados:\n";
echo "- $statsFile (JSON)\n";
echo "- $readableReportFile (Texto)\n";
echo "\nResultados resumidos:\n";
echo "- Total de arquivos: " . number_format($statsReport['general_stats']['total_files']) . "\n";
echo "- Tamanho total: " . $statsReport['general_stats']['total_size_formatted'] . "\n";
echo "- Arquivos HTML: " . number_format($statsReport['html_analysis']['total_html_files']) . "\n";
echo "- Imagens: " . number_format($statsReport['image_analysis']['total_images']) . "\n";
echo "- PDFs: " . number_format($statsReport['pdf_analysis']['total_pdfs']) . "\n";

?>