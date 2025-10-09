<?php
// api/crmc/list.php

// Endpoint para listar itens do CRMC

require_once __DIR__ . '/../models/CrmcItem.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar autenticação se necessário
    // checkAuth();
    
    // Obter parâmetros da requisição
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $isActive = isset($_GET['is_active']) ? (bool)$_GET['is_active'] : true;
    
    // Instanciar modelo
    $crmcItemModel = new CrmcItem();
    
    // Buscar itens do CRMC
    if ($category) {
        $items = $crmcItemModel->getByCategory($category);
    } else {
        $items = $crmcItemModel->getAll($limit, $offset);
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $items,
        'limit' => $limit,
        'offset' => $offset,
        'category' => $category,
        'is_active' => $isActive
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>