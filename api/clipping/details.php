<?php
// api/clipping/details.php

// Endpoint para obter detalhes de um clipping específico

require_once __DIR__ . '/../models/Clipping.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar se o ID foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do clipping não fornecido'
        ]);
        exit;
    }
    
    $id = (int)$_GET['id'];
    
    // Instanciar modelo
    $clippingModel = new Clipping();
    
    // Buscar clipping específico
    $clipping = $clippingModel->getById($id);
    
    if (!$clipping) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Clipping não encontrado'
        ]);
        exit;
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $clipping
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>