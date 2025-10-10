<?php
// test_database_connection.php

// Script para testar a conexão com o banco de dados

require_once 'api/config/database.php';

try {
    echo "Testando conexão com o banco de dados...\n\n";
    
    // Tentar conectar ao banco de dados
    $pdo = connectDatabase();
    
    echo "✓ Conexão estabelecida com sucesso!\n\n";
    
    // Obter informações do servidor
    $serverInfo = $pdo->getAttribute(PDO::ATTR_SERVER_INFO);
    $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $clientVersion = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
    
    echo "Informações do servidor:\n";
    echo "- Versão do servidor: $serverVersion\n";
    echo "- Informações do servidor: $serverInfo\n";
    echo "- Versão do cliente: $clientVersion\n\n";
    
    // Listar bancos de dados disponíveis
    echo "Bancos de dados disponíveis:\n";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($databases as $database) {
        echo "- $database\n";
    }
    
    echo "\n";
    
    // Verificar se o banco de dados do CCCRJ existe
    if (in_array('cccrj_db', $databases)) {
        echo "✓ Banco de dados 'cccrj_db' encontrado!\n\n";
        
        // Selecionar o banco de dados
        $pdo->exec("USE cccrj_db");
        
        // Listar tabelas
        echo "Tabelas no banco de dados 'cccrj_db':\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                echo "- $table\n";
            }
        } else {
            echo "Nenhuma tabela encontrada.\n";
        }
    } else {
        echo "⚠ Banco de dados 'cccrj_db' não encontrado.\n";
        echo "Execute o script db_setup.php para criar o banco de dados.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro ao conectar ao banco de dados: " . $e->getMessage() . "\n";
    echo "Verifique as configurações em api/config/database.php\n";
}
?>