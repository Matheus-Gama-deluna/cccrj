<?php
// api/crmc/details.php

// Endpoint para obter detalhes de um item do CRMC específico

require_once __DIR__ . '/../models/CrmcItem.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar se o ID foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do item do CRMC não fornecido'
        ]);
        exit;
    }
    
    $id = (int)$_GET['id'];
    
    // Instanciar modelo
    $crmcItemModel = new CrmcItem();
    
    // Buscar item do CRMC específico
    $item = $crmcItemModel->getById($id);
    
    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Item do CRMC não encontrado'
        ]);
        exit;
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $item
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>