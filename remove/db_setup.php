<?php
// db_config.php

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'cccrj_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar o banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");
    
    // Criar tabela de clippings
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
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Criar tabela de publicações
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
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Criar tabela de itens do acervo
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
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Criar tabela de eventos históricos
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
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Criar tabela de seções sobre
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
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Criar tabela de itens do CRMC
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
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    echo "Banco de dados e tabelas criados com sucesso!";
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>