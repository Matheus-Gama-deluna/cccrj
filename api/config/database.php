<?php
// api/config/database.php

// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'cccrj_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Função para conectar ao banco de dados
function connectDatabase() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
        throw new Exception("Não foi possível conectar ao banco de dados.");
    }
}
?>