<?php
// api/history/list.php

// Endpoint para listar eventos históricos

require_once __DIR__ . '/../models/HistoricalEvent.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar autenticação se necessário
    // checkAuth();
    
    // Obter parâmetros da requisição
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : false;
    
    // Instanciar modelo
    $historicalEventModel = new HistoricalEvent();
    
    // Buscar eventos históricos
    $events = $historicalEventModel->getAll($limit, $offset);
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $events,
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