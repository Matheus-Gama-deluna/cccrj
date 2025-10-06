<?php
// api/about/details.php

// Endpoint para obter detalhes de uma seção sobre específica

require_once __DIR__ . '/../models/AboutSection.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar se o ID foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID da seção sobre não fornecido'
        ]);
        exit;
    }
    
    $id = (int)$_GET['id'];
    
    // Instanciar modelo
    $aboutSectionModel = new AboutSection();
    
    // Buscar seção sobre específica
    $section = $aboutSectionModel->getById($id);
    
    if (!$section) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Seção sobre não encontrada'
        ]);
        exit;
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $section
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>