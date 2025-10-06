<?php
// api/publications/list.php

// Endpoint para listar publicações

require_once __DIR__ . '/../models/Publication.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar autenticação se necessário
    // checkAuth();
    
    // Obter parâmetros da requisição
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Instanciar modelo
    $publicationModel = new Publication();
    
    // Buscar publicações
    $publications = $publicationModel->getAll($limit, $offset);
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $publications,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>