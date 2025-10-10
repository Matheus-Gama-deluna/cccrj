<?php
// generate_system_report.php

// Script para gerar um relatório completo do sistema

echo "Gerando relatório completo do sistema...\n\n";

// 1. Informações do sistema
echo "1. Informações do sistema...\n";

$report = [
    'report_timestamp' => date('Y-m-d H:i:s'),
    'system_info' => [],
    'files_info' => [],
    'database_info' => [],
    'content_info' => []
];

$report['system_info'] = [
    'php_version' => phpversion(),
    'os' => PHP_OS,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
];

echo "✓ PHP Version: " . $report['system_info']['php_version'] . "\n";
echo "✓ OS: " . $report['system_info']['os'] . "\n";
echo "✓ Server Software: " . $report['system_info']['server_software'] . "\n";

echo "\n";

// 2. Informações sobre arquivos
echo "2. Informações sobre arquivos...\n";

function getDirectorySize($path) {
    $size = 0;
    foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : getDirectorySize($each);
    }
    return $size;
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

$directories = [
    'raiz' => '.',
    'api' => 'api',
    'assets' => 'assets',
    'scraping_cccrj' => 'scraping_cccrj',
    'uploads' => 'uploads'
];

$report['files_info'] = [
    'directories' => [],
    'total_files' => 0,
    'total_size' => 0
];

foreach ($directories as $name => $path) {
    if (is_dir($path)) {
        $size = getDirectorySize($path);
        $fileCount = 0;
        
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $fileCount++;
            }
        }
        
        $report['files_info']['directories'][$name] = [
            'path' => $path,
            'size' => $size,
            'size_formatted' => formatBytes($size),
            'file_count' => $fileCount
        ];
        
        $report['files_info']['total_size'] += $size;
        $report['files_info']['total_files'] += $fileCount;
        
        echo "✓ $name: " . formatBytes($size) . " ($fileCount arquivos)\n";
    }
}

echo "\nTotal de arquivos: " . $report['files_info']['total_files'] . "\n";
echo "Total de espaço utilizado: " . formatBytes($report['files_info']['total_size']) . "\n\n";

// 3. Informações do banco de dados
echo "3. Informações do banco de dados...\n";

try {
    require_once 'api/config/database.php';
    $pdo = connectDatabase();
    
    $stmt = $pdo->query("SELECT VERSION() as version");
    $dbVersion = $stmt->fetch()['version'];
    
    $report['database_info']['connection'] = 'success';
    $report['database_info']['version'] = $dbVersion;
    $report['database_info']['tables'] = [];
    
    echo "✓ Conexão com o banco de dados: OK\n";
    echo "✓ Versão do MySQL: $dbVersion\n";
    
    // Verificar tabelas
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cccrj_db'");
    if ($stmt->rowCount() > 0) {
        $pdo->exec("USE cccrj_db");
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch()['count'];
            
            $report['database_info']['tables'][$table] = $count;
            echo "✓ Tabela '$table': $count registros\n";
        }
    } else {
        echo "✗ Banco de dados 'cccrj_db' não encontrado\n";
    }
    
} catch (Exception $e) {
    $report['database_info']['connection'] = 'failed';
    $report['database_info']['error'] = $e->getMessage();
    echo "✗ Erro na conexão com o banco de dados: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Informações sobre o conteúdo
echo "4. Informações sobre o conteúdo...\n";

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

$report['content_info'] = [
    'directories' => [],
    'total_html_files' => 0,
    'total_image_files' => 0
];

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
        
        $report['content_info']['directories'][$description] = [
            'html_files' => $htmlCount,
            'image_files' => $imageCount
        ];
        
        $report['content_info']['total_html_files'] += $htmlCount;
        $report['content_info']['total_image_files'] += $imageCount;
        
        echo "✓ $description: $htmlCount arquivos HTML, $imageCount imagens\n";
    }
}

echo "\nTotal de arquivos HTML: " . $report['content_info']['total_html_files'] . "\n";
echo "Total de imagens: " . $report['content_info']['total_image_files'] . "\n\n";

// 5. Informações sobre os componentes
echo "5. Informações sobre os componentes...\n";

$componentFiles = [
    'assets/js/components/quotes.js' => 'Componente de Cotações',
    'assets/js/components/news.js' => 'Componente de Notícias',
    'assets/js/components/reports.js' => 'Componente de Relatórios',
    'assets/js/components/calculator.js' => 'Componente de Calculadora',
    'assets/js/components/about.js' => 'Componente Sobre',
    'assets/js/components/history.js' => 'Componente de História',
    'assets/js/components/publications.js' => 'Componente de Publicações',
    'assets/js/components/clipping.js' => 'Componente de Clipping'
];

$report['components_info'] = [
    'available_components' => 0,
    'components' => []
];

foreach ($componentFiles as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        $lines = count(file($file));
        
        $report['components_info']['components'][] = [
            'name' => $description,
            'file' => $file,
            'size' => $size,
            'lines' => $lines
        ];
        
        $report['components_info']['available_components']++;
        
        echo "✓ $description: " . formatBytes($size) . " ($lines linhas)\n";
    } else {
        echo "✗ $description: Arquivo não encontrado\n";
    }
}

echo "\nComponentes disponíveis: " . $report['components_info']['available_components'] . "\n\n";

// 6. Informações sobre a API
echo "6. Informações sobre a API...\n";

$apiDirectories = [
    'api/clipping' => 'API de Clipping',
    'api/publications' => 'API de Publicações',
    'api/archive' => 'API de Acervo',
    'api/history' => 'API de História',
    'api/about' => 'API Sobre',
    'api/crmc' => 'API do CRMC'
];

$report['api_info'] = [
    'endpoints' => 0,
    'directories' => []
];

foreach ($apiDirectories as $dir => $description) {
    if (is_dir($dir)) {
        $files = glob("$dir/*.php");
        $count = count($files);
        
        $report['api_info']['directories'][] = [
            'name' => $description,
            'directory' => $dir,
            'files' => $count
        ];
        
        $report['api_info']['endpoints'] += $count;
        
        echo "✓ $description: $count endpoints\n";
    }
}

echo "\nTotal de endpoints da API: " . $report['api_info']['endpoints'] . "\n\n";

// 7. Salvar relatório em arquivo
echo "7. Salvando relatório em arquivo...\n";

$reportsDir = 'reports';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

$timestamp = date('Y-m-d-H-i-s');
$reportFile = "$reportsDir/system_report_$timestamp.json";
file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ Relatório salvo em: $reportFile\n\n";

// 8. Criar relatório em formato legível
echo "8. Criando relatório em formato legível...\n";

$readableReport = "RELATÓRIO COMPLETO DO SISTEMA CCCRJ\n";
$readableReport .= "==================================\n\n";
$readableReport .= "Data da geração: " . $report['report_timestamp'] . "\n\n";

$readableReport .= "INFORMAÇÕES DO SISTEMA\n";
$readableReport .= "--------------------\n";
$readableReport .= "PHP Version: " . $report['system_info']['php_version'] . "\n";
$readableReport .= "Sistema Operacional: " . $report['system_info']['os'] . "\n";
$readableReport .= "Servidor: " . $report['system_info']['server_software'] . "\n\n";

$readableReport .= "INFORMAÇÕES DOS ARQUIVOS\n";
$readableReport .= "-----------------------\n";
$readableReport .= "Total de arquivos: " . $report['files_info']['total_files'] . "\n";
$readableReport .= "Espaço total utilizado: " . formatBytes($report['files_info']['total_size']) . "\n\n";

foreach ($report['files_info']['directories'] as $name => $info) {
    $readableReport .= "$name: " . $info['size_formatted'] . " (" . $info['file_count'] . " arquivos)\n";
}
$readableReport .= "\n";

$readableReport .= "INFORMAÇÕES DO BANCO DE DADOS\n";
$readableReport .= "----------------------------\n";
if ($report['database_info']['connection'] === 'success') {
    $readableReport .= "Conexão: OK\n";
    $readableReport .= "Versão do MySQL: " . $report['database_info']['version'] . "\n\n";
    
    foreach ($report['database_info']['tables'] as $table => $count) {
        $readableReport .= "$table: $count registros\n";
    }
} else {
    $readableReport .= "Conexão: Falhou\n";
    $readableReport .= "Erro: " . $report['database_info']['error'] . "\n";
}
$readableReport .= "\n";

$readableReport .= "INFORMAÇÕES DO CONTEÚDO\n";
$readableReport .= "----------------------\n";
$readableReport .= "Total de arquivos HTML: " . $report['content_info']['total_html_files'] . "\n";
$readableReport .= "Total de imagens: " . $report['content_info']['total_image_files'] . "\n\n";

foreach ($report['content_info']['directories'] as $name => $info) {
    $readableReport .= "$name: " . $info['html_files'] . " HTML, " . $info['image_files'] . " imagens\n";
}
$readableReport .= "\n";

$readableReport .= "INFORMAÇÕES DOS COMPONENTES\n";
$readableReport .= "---------------------------\n";
$readableReport .= "Componentes disponíveis: " . $report['components_info']['available_components'] . "\n\n";

foreach ($report['components_info']['components'] as $component) {
    $readableReport .= $component['name'] . ": " . formatBytes($component['size']) . " (" . $component['lines'] . " linhas)\n";
}
$readableReport .= "\n";

$readableReport .= "INFORMAÇÕES DA API\n";
$readableReport .= "-----------------\n";
$readableReport .= "Total de endpoints: " . $report['api_info']['endpoints'] . "\n\n";

foreach ($report['api_info']['directories'] as $api) {
    $readableReport .= $api['name'] . ": " . $api['files'] . " endpoints\n";
}

$readableReportFile = "$reportsDir/system_report_$timestamp.txt";
file_put_contents($readableReportFile, $readableReport);

echo "✓ Relatório legível salvo em: $readableReportFile\n\n";

echo "✅ Relatório completo do sistema gerado com sucesso!\n";
echo "\nRelatórios gerados:\n";
echo "- $reportFile (JSON)\n";
echo "- $readableReportFile (Texto)\n";

?>