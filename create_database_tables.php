<?php
// create_database_tables.php

// Script para criar o banco de dados e tabelas necessárias

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'cccrj_db';
$username = 'root';
$password = '';

try {
    echo "Criando banco de dados e tabelas...\n\n";
    
    // Conectar ao MySQL sem selecionar um banco de dados específico
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar o banco de dados se não existir
    echo "1. Criando banco de dados '$dbname'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Banco de dados '$dbname' criado ou já existente.\n\n";
    
    // Selecionar o banco de dados
    $pdo->exec("USE `$dbname`");
    
    // Criar tabela de clippings
    echo "2. Criando tabela 'clippings'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `clippings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `summary` TEXT,
            `content` TEXT,
            `source_url` VARCHAR(500),
            `date` DATE,
            `category` VARCHAR(100),
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_date` (`date`),
            INDEX `idx_category` (`category`),
            INDEX `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Tabela 'clippings' criada ou já existente.\n\n";
    
    // Criar tabela de publicações
    echo "3. Criando tabela 'publications'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `publications` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `file_path` VARCHAR(500),
            `date` DATE,
            `number` VARCHAR(50),
            `type` VARCHAR(50),
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_date` (`date`),
            INDEX `idx_type` (`type`),
            INDEX `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Tabela 'publications' criada ou já existente.\n\n";
    
    // Criar tabela de itens do acervo
    echo "4. Criando tabela 'archive_items'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `archive_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `file_path` VARCHAR(500),
            `date` DATE,
            `item_type` VARCHAR(100),
            `category` VARCHAR(100),
            `metadata` TEXT,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_date` (`date`),
            INDEX `idx_item_type` (`item_type`),
            INDEX `idx_category` (`category`),
            INDEX `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Tabela 'archive_items' criada ou já existente.\n\n";
    
    // Criar tabela de eventos históricos
    echo "5. Criando tabela 'historical_events'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `historical_events` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `content` TEXT,
            `date` DATE,
            `event_type` VARCHAR(100),
            `image_url` VARCHAR(500),
            `is_featured` TINYINT(1) DEFAULT 0,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_date` (`date`),
            INDEX `idx_event_type` (`event_type`),
            INDEX `idx_is_featured` (`is_featured`),
            INDEX `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Tabela 'historical_events' criada ou já existente.\n\n";
    
    // Criar tabela de seções sobre
    echo "6. Criando tabela 'about_sections'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `about_sections` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `content` TEXT,
            `section_type` VARCHAR(100),
            `order` INT DEFAULT 0,
            `is_active` TINYINT(1) DEFAULT 1,
            `image_url` VARCHAR(500),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_section_type` (`section_type`),
            INDEX `idx_order` (`order`),
            INDEX `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Tabela 'about_sections' criada ou já existente.\n\n";
    
    // Criar tabela de itens do CRMC
    echo "7. Criando tabela 'crmc_items'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `crmc_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `content` TEXT,
            `category` VARCHAR(100),
            `image_url` VARCHAR(500),
            `file_path` VARCHAR(500),
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_category` (`category`),
            INDEX `idx_is_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Tabela 'crmc_items' criada ou já existente.\n\n";
    
    echo "✅ Todas as tabelas foram criadas com sucesso!\n";
    echo "\nEstrutura do banco de dados:\n";
    echo "- Banco de dados: $dbname\n";
    echo "- Tabelas criadas: clippings, publications, archive_items, historical_events, about_sections, crmc_items\n";
    
} catch (PDOException $e) {
    echo "✗ Erro ao criar banco de dados e tabelas: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "✗ Erro inesperado: " . $e->getMessage() . "\n";
}
?>