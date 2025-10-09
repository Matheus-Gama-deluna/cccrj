<?php
// api/publications/download.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/functions.php';

try {
    // Verificar se o arquivo foi especificado
    if (!isset($_GET['file'])) {
        http_response_code(400);
        die('Arquivo não especificado');
    }
    
    // Sanitizar o nome do arquivo
    $fileName = sanitizeInput($_GET['file']);
    
    // Verificar se o arquivo existe
    $filePath = __DIR__ . '/../../../uploads/' . $fileName;
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        die('Arquivo não encontrado');
    }
    
    // Definir cabeçalhos para download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    
    // Ler e enviar o arquivo
    readfile($filePath);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    die('Erro ao baixar o arquivo: ' . $e->getMessage());
}
?>