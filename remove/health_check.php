<?php
// health_check.php

// Script para verificar a saúde do sistema

echo "Verificando a saúde do sistema...\n\n";

// 1. Verificar se os arquivos principais existem
echo "1. Verificando arquivos principais...\n";

$requiredFiles = [
    'index.html',
    'assets/js/main.js',
    'assets/js/components/quotes.js',
    'assets/js/components/news.js',
    'assets/js/components/reports.js',
    'assets/js/components/calculator.js',
    'assets/js/components/about.js',
    'assets/js/components/history.js',
    'assets/js/components/publications.js',
    'assets/js/components/clipping.js',
    'api/config/database.php',
    'api/models/Clipping.php',
    'api/models/Publication.php',
    'api/models/HistoricalEvent.php',
    'api/models/AboutSection.php',
    'api/models/CrmcItem.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file\n";
    } else {
        echo "✗ $file (arquivo não encontrado)\n";
    }
}

echo "\n";

// 2. Verificar conectividade com o banco de dados
echo "2. Verificando conectividade com o banco de dados...\n";

try {
    require_once 'api/config/database.php';
    $pdo = connectDatabase();
    echo "✓ Conexão com o banco de dados estabelecida\n";
    
    // Verificar se o banco de dados existe
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cccrj_db'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Banco de dados 'cccrj_db' encontrado\n";
        
        // Verificar tabelas
        $pdo->exec("USE cccrj_db");
        $tables = [
            'clippings',
            'publications',
            'archive_items',
            'historical_events',
            'about_sections',
            'crmc_items'
        ];
        
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("DESCRIBE `$table`");
                echo "✓ Tabela '$table' encontrada\n";
            } catch (PDOException $e) {
                echo "✗ Tabela '$table' não encontrada\n";
            }
        }
    } else {
        echo "✗ Banco de dados 'cccrj_db' não encontrado\n";
    }
} catch (Exception $e) {
    echo "✗ Erro na conexão com o banco de dados: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Verificar endpoints da API
echo "3. Verificando endpoints da API...\n";

$apiEndpoints = [
    'api/clipping/list.php',
    'api/publications/list.php',
    'api/archive/list.php',
    'api/history/list.php',
    'api/about/list.php',
    'api/crmc/list.php'
];

foreach ($apiEndpoints as $endpoint) {
    if (file_exists($endpoint)) {
        echo "✓ $endpoint\n";
    } else {
        echo "✗ $endpoint (endpoint não encontrado)\n";
    }
}

echo "\n";

// 4. Verificar permissões de diretórios
echo "4. Verificando permissões de diretórios...\n";

$writableDirs = [
    'uploads/',
    'api/logs/'
];

foreach ($writableDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✓ $dir (gravável)\n";
        } else {
            echo "✗ $dir (não gravável)\n";
        }
    } else {
        echo "? $dir (diretório não encontrado)\n";
    }
}

echo "\n";

// 5. Verificar dependências do sistema
echo "5. Verificando dependências do sistema...\n";

// Verificar versão do PHP
$requiredPhpVersion = '7.4.0';
$currentPhpVersion = PHP_VERSION;

if (version_compare($currentPhpVersion, $requiredPhpVersion, '>=')) {
    echo "✓ PHP $currentPhpVersion (versão mínima requerida: $requiredPhpVersion)\n";
} else {
    echo "✗ PHP $currentPhpVersion (versão mínima requerida: $requiredPhpVersion)\n";
}

// Verificar extensões PHP necessárias
$requiredExtensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring'];

foreach ($requiredExtensions as $extension) {
    if (extension_loaded($extension)) {
        echo "✓ Extensão PHP '$extension' carregada\n";
    } else {
        echo "✗ Extensão PHP '$extension' não carregada\n";
    }
}

echo "\n";

echo "Verificação concluída!\n";
echo "Se todos os itens estão marcados com ✓, o sistema está funcionando corretamente.\n";
?>