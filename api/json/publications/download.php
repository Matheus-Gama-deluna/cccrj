<?php
// api/json/publications/download.php

try {
    // Obter caminho do arquivo
    $filePath = $_GET['file'] ?? null;
    
    if (!$filePath) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Caminho do arquivo não fornecido'
        ]);
        exit;
    }
    
    // Sanitizar o caminho para evitar problemas de segurança
    $filePath = basename($filePath); // Isso remove qualquer diretório superior
    
    // Caminho completo do arquivo
    $fullPath = __DIR__ . '/../../../scraping_cccrj/cccrj_content/' . $filePath;
    
    // Verificar se o arquivo existe
    if (!file_exists($fullPath)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Arquivo não encontrado'
        ]);
        exit;
    }
    
    // Obter informações do arquivo
    $mimeType = mime_content_type($fullPath);
    $fileName = basename($fullPath);
    
    // Forçar o download
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . filesize($fullPath));
    
    readfile($fullPath);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>