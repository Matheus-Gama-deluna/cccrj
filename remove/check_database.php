<?php
// check_database.php

// Script para verificar a estrutura do banco de dados

require_once 'api/config/database.php';

try {
    $pdo = connectDatabase();
    
    // Verificar se as tabelas existem
    $tables = [
        'clippings',
        'publications',
        'archive_items',
        'historical_events',
        'about_sections',
        'crmc_items'
    ];
    
    echo "Verificando estrutura do banco de dados...\n\n";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->prepare("DESCRIBE `$table`");
            $stmt->execute();
            $columns = $stmt->fetchAll();
            
            echo "✓ Tabela '$table' encontrada com " . count($columns) . " colunas:\n";
            
            foreach ($columns as $column) {
                echo "  - {$column['Field']} ({$column['Type']})\n";
            }
            
            echo "\n";
        } catch (PDOException $e) {
            echo "✗ Tabela '$table' não encontrada ou erro: " . $e->getMessage() . "\n\n";
        }
    }
    
    // Verificar conexão com o banco de dados
    echo "Verificando conexão com o banco de dados...\n";
    $stmt = $pdo->prepare("SELECT 1");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✓ Conexão com o banco de dados estabelecida com sucesso!\n";
    } else {
        echo "✗ Não foi possível estabelecer conexão com o banco de dados.\n";
    }
    
} catch (Exception $e) {
    echo "Erro ao verificar o banco de dados: " . $e->getMessage() . "\n";
}
?>