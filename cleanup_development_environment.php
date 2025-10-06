<?php
// cleanup_development_environment.php

// Script para limpar o ambiente de desenvolvimento (USAR COM CUIDADO!)

echo "ATENÇÃO: Este script irá LIMPAR todo o ambiente de desenvolvimento!\n";
echo "Isso inclui:\n";
echo "- Exclusão do banco de dados cccrj_db\n";
echo "- Exclusão de todos os arquivos de upload\n";
echo "- Exclusão de logs e cache\n";
echo "\n";

echo "Tem certeza que deseja continuar? (digite 'SIM' para confirmar): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) !== 'SIM') {
    echo "Operação cancelada.\n";
    exit;
}

echo "\n";

// 1. Excluir banco de dados
echo "1. Excluindo banco de dados...\n";

try {
    // Conectar ao MySQL sem selecionar banco de dados
    $pdo = new PDO("mysql:host=localhost", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Excluir banco de dados
    $pdo->exec("DROP DATABASE IF EXISTS `cccrj_db`");
    echo "✓ Banco de dados 'cccrj_db' excluído\n";
} catch (Exception $e) {
    echo "✗ Erro ao excluir banco de dados: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Excluir diretórios de upload, logs e cache
echo "2. Excluindo diretórios temporários...\n";

$tempDirectories = [
    'uploads',
    'api/logs',
    'api/cache'
];

foreach ($tempDirectories as $dir) {
    if (is_dir($dir)) {
        // Função recursiva para excluir diretório e conteúdo
        function deleteDirectory($dir) {
            if (!is_dir($dir)) {
                return false;
            }
            
            $files = array_diff(scandir($dir), ['.', '..']);
            
            foreach ($files as $file) {
                $path = "$dir/$file";
                if (is_dir($path)) {
                    deleteDirectory($path);
                } else {
                    unlink($path);
                }
            }
            
            return rmdir($dir);
        }
        
        if (deleteDirectory($dir)) {
            echo "✓ Diretório '$dir' excluído\n";
        } else {
            echo "✗ Erro ao excluir diretório '$dir'\n";
        }
    } else {
        echo "? Diretório '$dir' não encontrado\n";
    }
}

echo "\n";

// 3. Excluir arquivos de configuração gerados
echo "3. Excluindo arquivos de configuração...\n";

$configFiles = [
    '.env',
    '.htaccess',
    'robots.txt',
    'humans.txt'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✓ Arquivo '$file' excluído\n";
        } else {
            echo "✗ Erro ao excluir arquivo '$file'\n";
        }
    } else {
        echo "? Arquivo '$file' não encontrado\n";
    }
}

echo "\n";

// 4. Excluir scripts de configuração
echo "4. Excluindo scripts de configuração...\n";

$setupScripts = [
    'db_setup.php',
    'populate_db.php',
    'populate_from_scraped_content.php',
    'create_database_tables.php',
    'setup_development_environment.php',
    'cleanup_development_environment.php',
    'test_database_connection.php',
    'check_database.php',
    'health_check.php'
];

foreach ($setupScripts as $script) {
    if (file_exists($script)) {
        if (unlink($script)) {
            echo "✓ Script '$script' excluído\n";
        } else {
            echo "✗ Erro ao excluir script '$script'\n";
        }
    }
}

echo "\n";

echo "✅ Ambiente de desenvolvimento limpo com sucesso!\n";
echo "\nPara reconfigurar o ambiente:\n";
echo "1. Execute o script setup_development_environment.php\n";
echo "2. Execute o script create_database_tables.php\n";
echo "3. Execute o script populate_db.php\n";
echo "4. Execute o script populate_from_scraped_content.php\n";
?>