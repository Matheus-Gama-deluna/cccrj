<?php
// maintenance_tasks.php

// Script para executar todas as tarefas de manutenção do sistema

echo "Executando tarefas de manutenção do sistema...\n\n";

// 1. Verificar integridade do sistema
echo "1. Verificando integridade do sistema...\n";

$checks = [
    'Verificar arquivos do sistema' => function() {
        $requiredFiles = [
            'index.html',
            'assets/js/main.js',
            'assets/js/components/quotes.js',
            'assets/js/components/news.js',
            'assets/js/components/reports.js',
            'assets/js/components/calculator.js',
            'api/config/database.php'
        ];
        
        $missing = [];
        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                $missing[] = $file;
            }
        }
        
        return empty($missing) ? ['status' => 'ok', 'message' => 'Todos os arquivos necessários presentes'] : 
                                ['status' => 'error', 'message' => 'Arquivos faltando: ' . implode(', ', $missing)];
    },
    
    'Verificar conexão com o banco de dados' => function() {
        try {
            require_once 'api/config/database.php';
            $pdo = connectDatabase();
            return ['status' => 'ok', 'message' => 'Conexão com o banco de dados estabelecida'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro na conexão: ' . $e->getMessage()];
        }
    },
    
    'Verificar diretórios de conteúdo' => function() {
        $contentDirs = [
            'scraping_cccrj/cccrj_content/cccrj',
            'scraping_cccrj/cccrj_content/crmc',
            'scraping_cccrj/cccrj_content/revista'
        ];
        
        $missing = [];
        foreach ($contentDirs as $dir) {
            if (!is_dir($dir)) {
                $missing[] = $dir;
            }
        }
        
        return empty($missing) ? ['status' => 'ok', 'message' => 'Todos os diretórios de conteúdo presentes'] : 
                                ['status' => 'warning', 'message' => 'Diretórios faltando: ' . implode(', ', $missing)];
    }
];

foreach ($checks as $name => $check) {
    $result = $check();
    $status = $result['status'];
    $message = $result['message'];
    
    switch ($status) {
        case 'ok':
            echo "✓ $name: $message\n";
            break;
        case 'warning':
            echo "⚠ $name: $message\n";
            break;
        case 'error':
            echo "✗ $name: $message\n";
            break;
    }
}

echo "\n";

// 2. Limpar caches e arquivos temporários
echo "2. Limpando caches e arquivos temporários...\n";

$caches = [
    'api/cache' => 'Cache da API',
    'api/logs' => 'Logs da API',
    'uploads/temp' => 'Arquivos temporários'
];

foreach ($caches as $dir => $description) {
    if (is_dir($dir)) {
        $files = glob("$dir/*");
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $deleted++;
            }
        }
        
        echo "✓ $description: $deleted arquivos limpos\n";
    } else {
        echo "? $description: diretório não encontrado\n";
    }
}

echo "\n";

// 3. Verificar espaço em disco
echo "3. Verificando espaço em disco...\n";

$diskSpace = disk_free_space('.');
$totalSpace = disk_total_space('.');
$freePercentage = round(($diskSpace / $totalSpace) * 100, 2);

echo "✓ Espaço total: " . formatBytes($totalSpace) . "\n";
echo "✓ Espaço livre: " . formatBytes($diskSpace) . " ($freePercentage%)\n";

if ($freePercentage < 10) {
    echo "⚠ Aviso: Espaço em disco baixo ($freePercentage%)\n";
}

echo "\n";

// 4. Verificar permissões de arquivos
echo "4. Verificando permissões de arquivos...\n";

$writableDirs = [
    'uploads' => 'Diretório de uploads',
    'api/logs' => 'Diretório de logs',
    'api/cache' => 'Diretório de cache'
];

foreach ($writableDirs as $dir => $description) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✓ $description: permissões OK\n";
        } else {
            echo "✗ $description: sem permissão de escrita\n";
        }
    } else {
        echo "? $description: diretório não encontrado\n";
    }
}

echo "\n";

// 5. Verificar versões de software
echo "5. Verificando versões de software...\n";

echo "✓ PHP Version: " . phpversion() . "\n";
echo "✓ Sistema Operacional: " . PHP_OS . "\n";

// Verificar extensões necessárias
$requiredExtensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ Extensão PHP '$ext': carregada\n";
    } else {
        echo "✗ Extensão PHP '$ext': não carregada\n";
    }
}

echo "\n";

// 6. Verificar conteúdo do banco de dados
echo "6. Verificando conteúdo do banco de dados...\n";

try {
    require_once 'api/config/database.php';
    $pdo = connectDatabase();
    $pdo->exec("USE cccrj_db");
    
    $tables = [
        'historical_events' => 'Eventos Históricos',
        'publications' => 'Publicações',
        'archive_items' => 'Itens do Acervo',
        'clippings' => 'Clippings',
        'about_sections' => 'Seções Sobre',
        'crmc_items' => 'Itens do CRMC'
    ];
    
    $totalRecords = 0;
    foreach ($tables as $table => $description) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $stmt->fetch()['count'];
        $totalRecords += $count;
        
        echo "✓ $description: $count registros\n";
    }
    
    echo "\nTotal de registros no banco de dados: $totalRecords\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao verificar conteúdo do banco de dados: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. Verificar integridade dos arquivos de conteúdo
echo "7. Verificando integridade dos arquivos de conteúdo...\n";

$contentDirs = [
    'scraping_cccrj/cccrj_content/cccrj' => 'Conteúdo do CCCRJ',
    'scraping_cccrj/cccrj_content/crmc' => 'Conteúdo do CRMC',
    'scraping_cccrj/cccrj_content/revista' => 'Revistas'
];

foreach ($contentDirs as $dir => $description) {
    if (is_dir($dir)) {
        $htmlFiles = glob("$dir/*.htm*");
        $otherFiles = count(glob("$dir/*")) - count($htmlFiles);
        
        echo "✓ $description: " . count($htmlFiles) . " arquivos HTML, $otherFiles outros arquivos\n";
    } else {
        echo "? $description: diretório não encontrado\n";
    }
}

echo "\n";

// 8. Verificar componentes JavaScript
echo "8. Verificando componentes JavaScript...\n";

$jsComponents = [
    'assets/js/components/quotes.js' => 'Componente de Cotações',
    'assets/js/components/news.js' => 'Componente de Notícias',
    'assets/js/components/reports.js' => 'Componente de Relatórios',
    'assets/js/components/calculator.js' => 'Componente de Calculadora',
    'assets/js/components/about.js' => 'Componente Sobre',
    'assets/js/components/history.js' => 'Componente de História',
    'assets/js/components/publications.js' => 'Componente de Publicações',
    'assets/js/components/clipping.js' => 'Componente de Clipping'
];

$componentsFound = 0;
foreach ($jsComponents as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        $lines = count(file($file));
        echo "✓ $description: " . formatBytes($size) . " ($lines linhas)\n";
        $componentsFound++;
    } else {
        echo "✗ $description: arquivo não encontrado\n";
    }
}

echo "\nComponentes JavaScript encontrados: $componentsFound/" . count($jsComponents) . "\n\n";

// 9. Verificar endpoints da API
echo "9. Verificando endpoints da API...\n";

$apiEndpoints = [
    'api/clipping/list.php' => 'Endpoint de Clipping',
    'api/publications/list.php' => 'Endpoint de Publicações',
    'api/archive/list.php' => 'Endpoint de Acervo',
    'api/history/list.php' => 'Endpoint de História',
    'api/about/list.php' => 'Endpoint Sobre',
    'api/crmc/list.php' => 'Endpoint do CRMC'
];

$endpointsFound = 0;
foreach ($apiEndpoints as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description: endpoint disponível\n";
        $endpointsFound++;
    } else {
        echo "✗ $description: endpoint não encontrado\n";
    }
}

echo "\nEndpoints da API encontrados: $endpointsFound/" . count($apiEndpoints) . "\n\n";

// 10. Resumo da manutenção
echo "10. Resumo da manutenção...\n";

echo "✅ Tarefas de manutenção concluídas!\n";
echo "\nResumo:\n";
echo "- Integridade do sistema: verificada\n";
echo "- Caches limpos: " . count($caches) . " diretórios\n";
echo "- Espaço em disco: " . formatBytes($diskSpace) . " livres\n";
echo "- Permissões verificadas: " . count($writableDirs) . " diretórios\n";
echo "- Extensões PHP: " . count($requiredExtensions) . " verificadas\n";
echo "- Registros no banco de dados: $totalRecords\n";
echo "- Diretórios de conteúdo: " . count($contentDirs) . " verificados\n";
echo "- Componentes JavaScript: $componentsFound/" . count($jsComponents) . " encontrados\n";
echo "- Endpoints da API: $endpointsFound/" . count($apiEndpoints) . " encontrados\n";

echo "\n💡 Próximos passos recomendados:\n";
echo "1. Executar o script de backup regularmente\n";
echo "2. Verificar logs de erros periodicamente\n";
echo "3. Atualizar conteúdo conforme necessário\n";
echo "4. Monitorar espaço em disco\n";

// Função auxiliar para formatar bytes
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

?>