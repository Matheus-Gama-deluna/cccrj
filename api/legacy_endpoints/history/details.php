<?php
// api/history/details.php

// Endpoint para obter detalhes de um evento histórico específico

require_once __DIR__ . '/../models/HistoricalEvent.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar se o ID foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do evento histórico não fornecido'
        ]);
        exit;
    }
    
    $id = (int)$_GET['id'];
    
    // Instanciar modelo
    $historicalEventModel = new HistoricalEvent();
    
    // Buscar evento histórico específico
    $event = $historicalEventModel->getById($id);
    
    if (!$event) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Evento histórico não encontrado'
        ]);
        exit;
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $event
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>