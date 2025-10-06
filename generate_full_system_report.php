<?php
// generate_full_system_report.php

// Script para gerar um relatório completo do sistema

echo "Gerando relatório completo do sistema...\n\n";

// 1. Informações do sistema básico
echo "1. Informações do sistema básico...\n";

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'system_info' => [
        'php_version' => phpversion(),
        'operating_system' => PHP_OS,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
        'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown'
    ],
    'directories' => [],
    'files' => [],
    'database' => [],
    'components' => [],
    'api_endpoints' => [],
    'content' => [],
    'statistics' => []
];

echo "✓ PHP Version: " . $report['system_info']['php_version'] . "\n";
echo "✓ Operating System: " . $report['system_info']['operating_system'] . "\n";
echo "✓ Server Software: " . $report['system_info']['server_software'] . "\n";

echo "\n";

// 2. Análise de diretórios e arquivos
echo "2. Análise de diretórios e arquivos...\n";

function analyzeDirectories($path, &$report) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    $fileCount = 0;
    $totalSize = 0;
    $fileTypes = [];
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $fileCount++;
            $size = $file->getSize();
            $totalSize += $size;
            
            // Obtém a extensão do arquivo
            $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if ($extension) {
                if (!isset($fileTypes[$extension])) {
                    $fileTypes[$extension] = 0;
                }
                $fileTypes[$extension]++;
            }
        }
    }
    
    $report['directories'][$path] = [
        'file_count' => $fileCount,
        'total_size' => $totalSize,
        'size_formatted' => formatBytes($totalSize),
        'file_types' => $fileTypes
    ];
    
    return $fileCount;
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

$directories = [
    '.' => 'Root Directory',
    'api' => 'API Directory',
    'assets' => 'Assets Directory',
    'scraping_cccrj' => 'Scraped Content Directory',
    'uploads' => 'Uploads Directory'
];

$totalFiles = 0;
foreach ($directories as $directory => $description) {
    if (is_dir($directory)) {
        $fileCount = analyzeDirectories($directory, $report);
        $totalFiles += $fileCount;
        echo "✓ $description: $fileCount files\n";
    }
}

echo "\nTotal de arquivos: $totalFiles\n\n";

// 3. Análise da estrutura do banco de dados
echo "3. Análise da estrutura do banco de dados...\n";

try {
    require_once 'api/config/database.php';
    $pdo = connectDatabase();
    
    $report['database']['connection'] = 'connected';
    $report['database']['version'] = $pdo->query("SELECT VERSION()")->fetchColumn();
    
    echo "✓ Conexão com o banco de dados estabelecida\n";
    echo "✓ Versão do MySQL: " . $report['database']['version'] . "\n";
    
    // Verificar tabelas existentes
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cccrj_db'");
    if ($stmt->rowCount() > 0) {
        $pdo->exec("USE cccrj_db");
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch()['count'];
            
            $report['database']['tables'][$table] = $count;
            
            echo "✓ Tabela '$table': $count registros\n";
        }
    } else {
        echo "✗ Banco de dados 'cccrj_db' não encontrado\n";
    }
    
} catch (Exception $e) {
    $report['database']['connection'] = 'failed';
    $report['database']['error'] = $e->getMessage();
    echo "✗ Erro na conexão com o banco de dados: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Análise dos componentes JavaScript
echo "4. Análise dos componentes JavaScript...\n";

$jsComponents = [
    'assets/js/components/quotes.js' => 'Coffee Quotes',
    'assets/js/components/news.js' => 'News Manager',
    'assets/js/components/reports.js' => 'Reports Manager',
    'assets/js/components/calculator.js' => 'Coffee Calculator',
    'assets/js/components/about.js' => 'About Manager',
    'assets/js/components/history.js' => 'History Manager',
    'assets/js/components/publications.js' => 'Publications Manager',
    'assets/js/components/clipping.js' => 'Clipping Manager'
];

foreach ($jsComponents as $file => $componentName) {
    if (file_exists($file)) {
        $size = filesize($file);
        $lines = count(file($file));
        
        $report['components'][basename($file, '.js')] = [
            'file' => $file,
            'size' => $size,
            'size_formatted' => formatBytes($size),
            'lines' => $lines,
            'status' => 'found'
        ];
        
        echo "✓ $componentName: " . formatBytes($size) . " ($lines linhas)\n";
    } else {
        $report['components'][basename($file, '.js')] = [
            'file' => $file,
            'status' => 'missing'
        ];
        
        echo "✗ $componentName: Arquivo não encontrado\n";
    }
}

echo "\n";

// 5. Análise dos endpoints da API
echo "5. Análise dos endpoints da API...\n";

$apiEndpoints = [
    'api/clipping/list.php' => 'Listagem de clippings',
    'api/clipping/details.php' => 'Detalhes de clipping',
    'api/publications/list.php' => 'Listagem de publicações',
    'api/publications/details.php' => 'Detalhes de publicação',
    'api/archive/list.php' => 'Listagem de acervo',
    'api/archive/details.php' => 'Detalhes de item do acervo',
    'api/history/list.php' => 'Listagem de eventos históricos',
    'api/history/details.php' => 'Detalhes de evento histórico',
    'api/about/list.php' => 'Listagem de seções sobre',
    'api/about/details.php' => 'Detalhes de seção sobre',
    'api/crmc/list.php' => 'Listagem de itens do CRMC',
    'api/crmc/details.php' => 'Detalhes de item do CRMC'
];

foreach ($apiEndpoints as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        
        $report['api_endpoints'][basename($file, '.php')] = [
            'file' => $file,
            'size' => $size,
            'size_formatted' => formatBytes($size),
            'description' => $description,
            'status' => 'found'
        ];
        
        echo "✓ $description: " . formatBytes($size) . "\n";
    } else {
        $report['api_endpoints'][basename($file, '.php')] = [
            'file' => $file,
            'description' => $description,
            'status' => 'missing'
        ];
        
        echo "✗ $description: Endpoint não encontrado\n";
    }
}

echo "\n";

// 6. Análise do conteúdo raspado
echo "6. Análise do conteúdo raspado...\n";

$contentDirectories = [
    'scraping_cccrj/cccrj_content/cccrj' => 'Conteúdo do CCCRJ',
    'scraping_cccrj/cccrj_content/crmc' => 'Conteúdo do CRMC',
    'scraping_cccrj/cccrj_content/revista' => 'Revistas',
    'scraping_cccrj/cccrj_content/clipping' => 'Clipping',
    'scraping_cccrj/cccrj_content/noticias' => 'Notícias',
    'scraping_cccrj/cccrj_content/boletim' => 'Boletins',
    'scraping_cccrj/cccrj_content/rio' => 'Café no Rio',
    'scraping_cccrj/cccrj_content/acervo' => 'Acervo'
];

$totalHtmlFiles = 0;
$totalImageFiles = 0;

foreach ($contentDirectories as $dir => $description) {
    if (is_dir($dir)) {
        $htmlCount = 0;
        $imageCount = 0;
        
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                if (preg_match('/\.(htm|html)$/i', $file->getFilename())) {
                    $htmlCount++;
                } elseif (preg_match('/\.(jpg|jpeg|png|gif|bmp|svg)$/i', $file->getFilename())) {
                    $imageCount++;
                }
            }
        }
        
        $report['content'][$description] = [
            'html_files' => $htmlCount,
            'image_files' => $imageCount,
            'total_files' => $htmlCount + $imageCount
        ];
        
        $totalHtmlFiles += $htmlCount;
        $totalImageFiles += $imageCount;
        
        echo "✓ $description: $htmlCount arquivos HTML, $imageCount imagens\n";
    }
}

$report['statistics']['content'] = [
    'total_html_files' => $totalHtmlFiles,
    'total_image_files' => $totalImageFiles,
    'total_files' => $totalHtmlFiles + $totalImageFiles
];

echo "\n";

// 7. Gerar estatísticas gerais
echo "7. Gerando estatísticas gerais...\n";

$report['statistics']['general'] = [
    'total_files' => $totalFiles,
    'total_directories_analyzed' => count($directories),
    'total_js_components' => count(array_filter($report['components'], function($component) {
        return isset($component['status']) && $component['status'] === 'found';
    })),
    'total_api_endpoints' => count(array_filter($report['api_endpoints'], function($endpoint) {
        return isset($endpoint['status']) && $endpoint['status'] === 'found';
    })),
    'total_content_directories' => count(array_filter($report['content'], function($content) {
        return isset($content['total_files']) && $content['total_files'] > 0;
    }))
];

echo "✓ Estatísticas gerais geradas\n\n";

// 8. Salvar relatório em arquivo JSON
echo "8. Salvando relatório em arquivo JSON...\n";

$reportsDir = 'reports';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

$timestamp = date('Y-m-d-H-i-s');
$reportFile = "$reportsDir/full_system_report_$timestamp.json";
file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ Relatório salvo em: $reportFile\n\n";

// 9. Gerar relatório em formato legível
echo "9. Gerando relatório em formato legível...\n";

$readableReport = "RELATÓRIO COMPLETO DO SISTEMA CCCRJ\n";
$readableReport .= "==================================\n\n";
$readableReport .= "Data da geração: " . $report['timestamp'] . "\n\n";

$readableReport .= "INFORMAÇÕES DO SISTEMA\n";
$readableReport .= "--------------------\n";
$readableReport .= "PHP Version: " . $report['system_info']['php_version'] . "\n";
$readableReport .= "Sistema Operacional: " . $report['system_info']['operating_system'] . "\n";
$readableReport .= "Servidor: " . $report['system_info']['server_software'] . "\n";
$readableReport .= "Document Root: " . $report['system_info']['document_root'] . "\n";
$readableReport .= "Host HTTP: " . $report['system_info']['http_host'] . "\n";
$readableReport .= "Porta do Servidor: " . $report['system_info']['server_port'] . "\n\n";

$readableReport .= "ESTRUTURA DE DIRETÓRIOS\n";
$readableReport .= "---------------------\n";
foreach ($report['directories'] as $path => $info) {
    $readableReport .= "$path: " . $info['file_count'] . " arquivos, " . $info['size_formatted'] . "\n";
}
$readableReport .= "\n";

$readableReport .= "BANCO DE DADOS\n";
$readableReport .= "-------------\n";
if ($report['database']['connection'] === 'connected') {
    $readableReport .= "Conexão: Estabelecida\n";
    $readableReport .= "Versão do MySQL: " . $report['database']['version'] . "\n\n";
    
    if (isset($report['database']['tables'])) {
        $readableReport .= "Tabelas:\n";
        foreach ($report['database']['tables'] as $table => $count) {
            $readableReport .= "- $table: $count registros\n";
        }
    }
} else {
    $readableReport .= "Conexão: Falhou\n";
    $readableReport .= "Erro: " . ($report['database']['error'] ?? 'Desconhecido') . "\n";
}
$readableReport .= "\n";

$readableReport .= "COMPONENTES JAVASCRIPT\n";
$readableReport .= "----------------------\n";
foreach ($report['components'] as $name => $component) {
    if (isset($component['status']) && $component['status'] === 'found') {
        $readableReport .= "✓ " . ucfirst($name) . ": " . $component['size_formatted'] . " (" . $component['lines'] . " linhas)\n";
    } else {
        $readableReport .= "✗ " . ucfirst($name) . ": Arquivo não encontrado\n";
    }
}
$readableReport .= "\n";

$readableReport .= "ENDPOINTS DA API\n";
$readableReport .= "---------------\n";
foreach ($report['api_endpoints'] as $name => $endpoint) {
    if (isset($endpoint['status']) && $endpoint['status'] === 'found') {
        $readableReport .= "✓ " . $endpoint['description'] . ": " . $endpoint['size_formatted'] . "\n";
    } else {
        $readableReport .= "✗ " . $endpoint['description'] . ": Endpoint não encontrado\n";
    }
}
$readableReport .= "\n";

$readableReport .= "CONTEÚDO RASPADO\n";
$readableReport .= "--------------\n";
foreach ($report['content'] as $name => $content) {
    if (isset($content['total_files']) && $content['total_files'] > 0) {
        $readableReport .= "✓ $name: " . $content['html_files'] . " arquivos HTML, " . $content['image_files'] . " imagens\n";
    }
}
$readableReport .= "\n";

$readableReport .= "ESTATÍSTICAS GERAIS\n";
$readableReport .= "------------------\n";
$readableReport .= "Total de arquivos: " . $report['statistics']['general']['total_files'] . "\n";
$readableReport .= "Diretórios analisados: " . $report['statistics']['general']['total_directories_analyzed'] . "\n";
$readableReport .= "Componentes JavaScript: " . $report['statistics']['general']['total_js_components'] . "\n";
$readableReport .= "Endpoints da API: " . $report['statistics']['general']['total_api_endpoints'] . "\n";
$readableReport .= "Diretórios de conteúdo: " . $report['statistics']['general']['total_content_directories'] . "\n";
$readableReport .= "Arquivos HTML no conteúdo: " . ($report['statistics']['content']['total_html_files'] ?? 0) . "\n";
$readableReport .= "Imagens no conteúdo: " . ($report['statistics']['content']['total_image_files'] ?? 0) . "\n";

$readableReportFile = "$reportsDir/full_system_report_$timestamp.txt";
file_put_contents($readableReportFile, $readableReport);

echo "✓ Relatório legível salvo em: $readableReportFile\n\n";

// 10. Exibir resumo final
echo "10. Resumo final...\n";

echo "✅ Relatório completo do sistema gerado com sucesso!\n";
echo "\nRelatórios gerados:\n";
echo "- $reportFile (JSON)\n";
echo "- $readableReportFile (Texto)\n";
echo "\nEstatísticas principais:\n";
echo "- Total de arquivos: " . $report['statistics']['general']['total_files'] . "\n";
echo "- Componentes JavaScript: " . $report['statistics']['general']['total_js_components'] . "/" . count($jsComponents) . "\n";
echo "- Endpoints da API: " . $report['statistics']['general']['total_api_endpoints'] . "/" . count($apiEndpoints) . "\n";
echo "- Diretórios de conteúdo: " . $report['statistics']['general']['total_content_directories'] . "/" . count($contentDirectories) . "\n";
echo "- Arquivos HTML: " . ($report['statistics']['content']['total_html_files'] ?? 0) . "\n";
echo "- Imagens: " . ($report['statistics']['content']['total_image_files'] ?? 0) . "\n";

?>