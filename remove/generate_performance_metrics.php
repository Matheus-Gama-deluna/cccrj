<?php
// generate_performance_metrics.php

// Script para gerar métricas de desempenho do sistema

echo "Gerando métricas de desempenho do sistema...\n\n";

// 1. Medir tempo de carregamento das páginas
echo "1. Medindo tempo de carregamento das páginas...\n";

function measurePageLoadTime($url) {
    $startTime = microtime(true);
    
    // Realizar requisição HTTP
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 30,
            'header' => [
                'User-Agent: PerformanceMetricsBot/1.0'
            ]
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    $endTime = microtime(true);
    
    if ($content === false) {
        return ['error' => 'Falha ao carregar a página'];
    }
    
    $loadTime = ($endTime - $startTime) * 1000; // Converter para milissegundos
    $contentSize = strlen($content);
    
    return [
        'load_time_ms' => $loadTime,
        'content_size_bytes' => $contentSize,
        'content_size_formatted' => formatBytes($contentSize),
        'status' => 'success'
    ];
}

function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

// URLs para testar
$testUrls = [
    'http://localhost/cccrj/' => 'Página Principal',
    'http://localhost/cccrj/#cotacao' => 'Seção de Cotações',
    'http://localhost/cccrj/#noticias' => 'Seção de Notícias',
    'http://localhost/cccrj/#relatorios' => 'Seção de Relatórios',
    'http://localhost/cccrj/#sobre' => 'Seção Sobre Nós',
    'http://localhost/cccrj/#contato' => 'Seção de Contato'
];

$pageLoadMetrics = [];

foreach ($testUrls as $url => $description) {
    echo "✓ Testando $description...\n";
    $metrics = measurePageLoadTime($url);
    $pageLoadMetrics[$description] = $metrics;
    
    if ($metrics['status'] === 'success') {
        echo "  Tempo de carregamento: " . number_format($metrics['load_time_ms'], 2) . " ms\n";
        echo "  Tamanho do conteúdo: " . $metrics['content_size_formatted'] . "\n\n";
    } else {
        echo "  Erro: " . $metrics['error'] . "\n\n";
    }
}

echo "\n";

// 2. Medir desempenho do banco de dados
echo "2. Medindo desempenho do banco de dados...\n";

function measureDatabasePerformance() {
    try {
        require_once 'api/config/database.php';
        $pdo = connectDatabase();
        
        $dbMetrics = [
            'connection_time_ms' => 0,
            'query_performance' => [],
            'table_sizes' => []
        ];
        
        // Medir tempo de conexão
        $startTime = microtime(true);
        $pdo = connectDatabase();
        $endTime = microtime(true);
        $dbMetrics['connection_time_ms'] = ($endTime - $startTime) * 1000;
        
        echo "✓ Tempo de conexão: " . number_format($dbMetrics['connection_time_ms'], 2) . " ms\n";
        
        // Testar desempenho de consultas
        $queries = [
            'SELECT COUNT(*) FROM clippings' => 'Contagem de clippings',
            'SELECT COUNT(*) FROM publications' => 'Contagem de publicações',
            'SELECT COUNT(*) FROM archive_items' => 'Contagem de itens do acervo',
            'SELECT COUNT(*) FROM historical_events' => 'Contagem de eventos históricos'
        ];
        
        foreach ($queries as $query => $description) {
            $startTime = microtime(true);
            $stmt = $pdo->query($query);
            $result = $stmt->fetchColumn();
            $endTime = microtime(true);
            
            $queryTime = ($endTime - $startTime) * 1000;
            $dbMetrics['query_performance'][$description] = [
                'query' => $query,
                'time_ms' => $queryTime,
                'result' => $result
            ];
            
            echo "✓ $description: " . number_format($queryTime, 2) . " ms (resultado: $result)\n";
        }
        
        // Obter tamanho das tabelas
        $stmt = $pdo->query("SHOW TABLE STATUS");
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tables as $table) {
            $tableName = $table['Name'];
            $tableSize = $table['Data_length'] + $table['Index_length'];
            
            $dbMetrics['table_sizes'][$tableName] = [
                'rows' => $table['Rows'],
                'size_bytes' => $tableSize,
                'size_formatted' => formatBytes($tableSize),
                'engine' => $table['Engine']
            ];
            
            echo "✓ Tabela '$tableName': " . $table['Rows'] . " registros, " . formatBytes($tableSize) . "\n";
        }
        
        return $dbMetrics;
        
    } catch (Exception $e) {
        return ['error' => 'Erro ao medir desempenho do banco de dados: ' . $e->getMessage()];
    }
}

$dbMetrics = measureDatabasePerformance();
echo "\n";

// 3. Medir desempenho do frontend
echo "3. Medindo desempenho do frontend...\n";

function measureFrontendPerformance() {
    $frontendMetrics = [
        'js_files' => 0,
        'css_files' => 0,
        'image_files' => 0,
        'total_assets_size' => 0,
        'largest_assets' => []
    ];
    
    // Contar e medir arquivos JavaScript
    $jsFiles = glob('assets/js/**/*.js', GLOB_BRACE);
    $frontendMetrics['js_files'] = count($jsFiles);
    
    $jsSize = 0;
    foreach ($jsFiles as $file) {
        $size = filesize($file);
        $jsSize += $size;
        
        // Adicionar aos maiores assets se for grande
        if ($size > 100000) { // Arquivos maiores que 100KB
            $frontendMetrics['largest_assets'][] = [
                'file' => $file,
                'size_bytes' => $size,
                'size_formatted' => formatBytes($size),
                'type' => 'js'
            ];
        }
    }
    
    $frontendMetrics['js_size'] = $jsSize;
    $frontendMetrics['js_size_formatted'] = formatBytes($jsSize);
    
    echo "✓ Arquivos JavaScript: " . $frontendMetrics['js_files'] . " (" . $frontendMetrics['js_size_formatted'] . ")\n";
    
    // Contar e medir arquivos CSS
    $cssFiles = glob('assets/css/**/*.css', GLOB_BRACE);
    $frontendMetrics['css_files'] = count($cssFiles);
    
    $cssSize = 0;
    foreach ($cssFiles as $file) {
        $size = filesize($file);
        $cssSize += $size;
        
        // Adicionar aos maiores assets se for grande
        if ($size > 50000) { // Arquivos maiores que 50KB
            $frontendMetrics['largest_assets'][] = [
                'file' => $file,
                'size_bytes' => $size,
                'size_formatted' => formatBytes($size),
                'type' => 'css'
            ];
        }
    }
    
    $frontendMetrics['css_size'] = $cssSize;
    $frontendMetrics['css_size_formatted'] = formatBytes($cssSize);
    
    echo "✓ Arquivos CSS: " . $frontendMetrics['css_files'] . " (" . $frontendMetrics['css_size_formatted'] . ")\n";
    
    // Contar e medir imagens
    $imageFiles = glob('assets/images/**/*.{jpg,jpeg,png,gif,svg,bmp}', GLOB_BRACE);
    $frontendMetrics['image_files'] = count($imageFiles);
    
    $imageSize = 0;
    foreach ($imageFiles as $file) {
        $size = filesize($file);
        $imageSize += $size;
        
        // Adicionar aos maiores assets se for grande
        if ($size > 200000) { // Arquivos maiores que 200KB
            $frontendMetrics['largest_assets'][] = [
                'file' => $file,
                'size_bytes' => $size,
                'size_formatted' => formatBytes($size),
                'type' => 'image'
            ];
        }
    }
    
    $frontendMetrics['image_size'] = $imageSize;
    $frontendMetrics['image_size_formatted'] = formatBytes($imageSize);
    
    echo "✓ Arquivos de Imagem: " . $frontendMetrics['image_files'] . " (" . $frontendMetrics['image_size_formatted'] . ")\n";
    
    // Calcular tamanho total de assets
    $frontendMetrics['total_assets_size'] = $jsSize + $cssSize + $imageSize;
    $frontendMetrics['total_assets_size_formatted'] = formatBytes($frontendMetrics['total_assets_size']);
    
    echo "✓ Tamanho total de assets: " . $frontendMetrics['total_assets_size_formatted'] . "\n";
    
    // Ordenar maiores assets por tamanho
    usort($frontendMetrics['largest_assets'], function($a, $b) {
        return $b['size_bytes'] - $a['size_bytes'];
    });
    
    // Limitar a 10 maiores assets
    $frontendMetrics['largest_assets'] = array_slice($frontendMetrics['largest_assets'], 0, 10);
    
    return $frontendMetrics;
}

$frontendMetrics = measureFrontendPerformance();
echo "\n";

// 4. Medir desempenho da API
echo "4. Medindo desempenho da API...\n";

function measureApiPerformance() {
    $apiMetrics = [
        'endpoints_tested' => 0,
        'average_response_time_ms' => 0,
        'slowest_endpoints' => [],
        'fastest_endpoints' => []
    ];
    
    // Endpoints da API para testar
    $apiEndpoints = [
        '/api/clipping/list.php' => 'Listagem de clippings',
        '/api/publications/list.php' => 'Listagem de publicações',
        '/api/archive/list.php' => 'Listagem de itens do acervo',
        '/api/history/list.php' => 'Listagem de eventos históricos',
        '/api/about/list.php' => 'Listagem de seções sobre',
        '/api/crmc/list.php' => 'Listagem de itens do CRMC'
    ];
    
    $totalTime = 0;
    $endpointTimes = [];
    
    foreach ($apiEndpoints as $endpoint => $description) {
        $url = 'http://localhost/cccrj' . $endpoint;
        
        $startTime = microtime(true);
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 30,
                'header' => [
                    'User-Agent: PerformanceMetricsBot/1.0',
                    'Accept: application/json'
                ]
            ]
        ]);
        
        $content = @file_get_contents($url, false, $context);
        $endTime = microtime(true);
        
        if ($content !== false) {
            $responseTime = ($endTime - $startTime) * 1000;
            $totalTime += $responseTime;
            $endpointTimes[$description] = $responseTime;
            $apiMetrics['endpoints_tested']++;
            
            echo "✓ $description: " . number_format($responseTime, 2) . " ms\n";
        } else {
            echo "✗ $description: Falha ao carregar\n";
        }
    }
    
    if ($apiMetrics['endpoints_tested'] > 0) {
        $apiMetrics['average_response_time_ms'] = $totalTime / $apiMetrics['endpoints_tested'];
        
        // Encontrar endpoints mais rápidos e lentos
        asort($endpointTimes);
        $apiMetrics['fastest_endpoints'] = array_slice($endpointTimes, 0, 3, true);
        
        arsort($endpointTimes);
        $apiMetrics['slowest_endpoints'] = array_slice($endpointTimes, 0, 3, true);
    }
    
    return $apiMetrics;
}

$apiMetrics = measureApiPerformance();
echo "\n";

// 5. Gerar relatório de métricas
echo "5. Gerando relatório de métricas...\n";

$performanceMetrics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'page_load_times' => $pageLoadMetrics,
    'database_performance' => $dbMetrics,
    'frontend_performance' => $frontendMetrics,
    'api_performance' => $apiMetrics
];

// Salvar métricas em arquivo JSON
$reportsDir = 'reports';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

$timestamp = date('Y-m-d-H-i-s');
$metricsFile = "$reportsDir/performance_metrics_$timestamp.json";
file_put_contents($metricsFile, json_encode($performanceMetrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ Métricas de desempenho salvas em: $metricsFile\n\n";

// 6. Gerar relatório em formato legível
echo "6. Gerando relatório em formato legível...\n";

$readableReport = "RELATÓRIO DE MÉTRICAS DE DESEMPENHO\n";
$readableReport .= "==================================\n\n";
$readableReport .= "Data da geração: " . $performanceMetrics['timestamp'] . "\n\n";

$readableReport .= "TEMPO DE CARREGAMENTO DAS PÁGINAS\n";
$readableReport .= "--------------------------------\n";

$totalLoadTime = 0;
$pageCount = 0;

foreach ($performanceMetrics['page_load_times'] as $page => $metrics) {
    if ($metrics['status'] === 'success') {
        $readableReport .= "✓ $page: " . number_format($metrics['load_time_ms'], 2) . " ms (" . $metrics['content_size_formatted'] . ")\n";
        $totalLoadTime += $metrics['load_time_ms'];
        $pageCount++;
    } else {
        $readableReport .= "✗ $page: Erro (" . $metrics['error'] . ")\n";
    }
}

$averageLoadTime = $pageCount > 0 ? $totalLoadTime / $pageCount : 0;
$readableReport .= "\nTempo médio de carregamento: " . number_format($averageLoadTime, 2) . " ms\n\n";

$readableReport .= "DESEMPENHO DO BANCO DE DADOS\n";
$readableReport .= "---------------------------\n";

if (isset($performanceMetrics['database_performance']['error'])) {
    $readableReport .= "✗ Erro: " . $performanceMetrics['database_performance']['error'] . "\n\n";
} else {
    $readableReport .= "✓ Tempo de conexão: " . number_format($performanceMetrics['database_performance']['connection_time_ms'], 2) . " ms\n\n";
    
    $readableReport .= "Desempenho de consultas:\n";
    foreach ($performanceMetrics['database_performance']['query_performance'] as $query => $metrics) {
        $readableReport .= "✓ $query: " . number_format($metrics['time_ms'], 2) . " ms (resultado: " . $metrics['result'] . ")\n";
    }
    
    $readableReport .= "\nTamanho das tabelas:\n";
    foreach ($performanceMetrics['database_performance']['table_sizes'] as $table => $metrics) {
        $readableReport .= "✓ $table: " . $metrics['rows'] . " registros, " . $metrics['size_formatted'] . "\n";
    }
}

$readableReport .= "\nDESEMPENHO DO FRONTEND\n";
$readableReport .= "---------------------\n";

$readableReport .= "Arquivos JavaScript: " . $performanceMetrics['frontend_performance']['js_files'] . " (" . $performanceMetrics['frontend_performance']['js_size_formatted'] . ")\n";
$readableReport .= "Arquivos CSS: " . $performanceMetrics['frontend_performance']['css_files'] . " (" . $performanceMetrics['frontend_performance']['css_size_formatted'] . ")\n";
$readableReport .= "Arquivos de Imagem: " . $performanceMetrics['frontend_performance']['image_files'] . " (" . $performanceMetrics['frontend_performance']['image_size_formatted'] . ")\n";
$readableReport .= "Tamanho total de assets: " . $performanceMetrics['frontend_performance']['total_assets_size_formatted'] . "\n\n";

$readableReport .= "Maiores assets:\n";
foreach ($performanceMetrics['frontend_performance']['largest_assets'] as $asset) {
    $readableReport .= "✓ " . $asset['file'] . ": " . $asset['size_formatted'] . " (" . strtoupper($asset['type']) . ")\n";
}

$readableReport .= "\nDESEMPENHO DA API\n";
$readableReport .= "----------------\n";

$readableReport .= "Endpoints testados: " . $performanceMetrics['api_performance']['endpoints_tested'] . "\n";
$readableReport .= "Tempo médio de resposta: " . number_format($performanceMetrics['api_performance']['average_response_time_ms'], 2) . " ms\n\n";

$readableReport .= "Endpoints mais rápidos:\n";
foreach ($performanceMetrics['api_performance']['fastest_endpoints'] as $endpoint => $time) {
    $readableReport .= "✓ $endpoint: " . number_format($time, 2) . " ms\n";
}

$readableReport .= "\nEndpoints mais lentos:\n";
foreach ($performanceMetrics['api_performance']['slowest_endpoints'] as $endpoint => $time) {
    $readableReport .= "✓ $endpoint: " . number_format($time, 2) . " ms\n";
}

$readableReport .= "\nRECOMENDAÇÕES DE OTIMIZAÇÃO\n";
$readableReport .= "---------------------------\n";

// Recomendações baseadas nas métricas
if ($averageLoadTime > 3000) {
    $readableReport .= "1. Tempo de carregamento elevado - otimize recursos estáticos\n";
} elseif ($averageLoadTime > 2000) {
    $readableReport .= "1. Tempo de carregamento aceitável - considere otimizações adicionais\n";
} else {
    $readableReport .= "1. Tempo de carregamento ótimo - mantenha boas práticas\n";
}

if ($performanceMetrics['api_performance']['average_response_time_ms'] > 500) {
    $readableReport .= "2. Tempo de resposta da API elevado - otimize consultas ao banco de dados\n";
} elseif ($performanceMetrics['api_performance']['average_response_time_ms'] > 300) {
    $readableReport .= "2. Tempo de resposta da API aceitável - monitorize continuamente\n";
} else {
    $readableReport .= "2. Tempo de resposta da API ótimo - mantenha boas práticas\n";
}

if ($performanceMetrics['database_performance']['connection_time_ms'] > 100) {
    $readableReport .= "3. Tempo de conexão com o banco de dados elevado - otimize configurações\n";
} else {
    $readableReport .= "3. Tempo de conexão com o banco de dados ótimo\n";
}

// Recomendações baseadas no tamanho dos assets
if ($performanceMetrics['frontend_performance']['total_assets_size'] > 5000000) { // 5MB
    $readableReport .= "4. Tamanho total de assets elevado - otimize imagens e minifique CSS/JS\n";
} elseif ($performanceMetrics['frontend_performance']['total_assets_size'] > 3000000) { // 3MB
    $readableReport .= "4. Tamanho total de assets aceitável - considere otimizações adicionais\n";
} else {
    $readableReport .= "4. Tamanho total de assets ótimo\n";
}

$readableReport .= "\nMETAS DE DESEMPENHO\n";
$readableReport .= "------------------\n";
$readableReport .= "Tempo de carregamento da página principal: < 3 segundos\n";
$readableReport .= "Tempo de resposta da API: < 500 ms\n";
$readableReport .= "Tempo de conexão com o banco de dados: < 100 ms\n";
$readableReport .= "Tamanho total de assets: < 5 MB\n";

$readableReportFile = "$reportsDir/performance_metrics_$timestamp.txt";
file_put_contents($readableReportFile, $readableReport);

echo "✓ Relatório legível salvo em: $readableReportFile\n\n";

echo "✅ Relatório de métricas de desempenho gerado com sucesso!\n";
echo "\nRelatórios gerados:\n";
echo "- $metricsFile (JSON)\n";
echo "- $readableReportFile (Texto)\n";
echo "\nResultados resumidos:\n";
echo "- Tempo médio de carregamento: " . number_format($averageLoadTime, 2) . " ms\n";
echo "- Tempo médio de resposta da API: " . number_format($performanceMetrics['api_performance']['average_response_time_ms'], 2) . " ms\n";
echo "- Tempo de conexão com o banco de dados: " . number_format($performanceMetrics['database_performance']['connection_time_ms'], 2) . " ms\n";
echo "- Tamanho total de assets: " . $performanceMetrics['frontend_performance']['total_assets_size_formatted'] . "\n";

?>