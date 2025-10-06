<?php
// api/publications/details.php

// Endpoint para obter detalhes de uma publicação específica

require_once __DIR__ . '/../models/Publication.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar se o ID foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID da publicação não fornecido'
        ]);
        exit;
    }
    
    $id = (int)$_GET['id'];
    
    // Instanciar modelo
    $publicationModel = new Publication();
    
    // Buscar publicação específica
    $publication = $publicationModel->getById($id);
    
    if (!$publication) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Publicação não encontrada'
        ]);
        exit;
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $publication
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>