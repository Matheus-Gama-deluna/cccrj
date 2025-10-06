<?php
// api/about/list.php

// Endpoint para listar seções sobre

require_once __DIR__ . '/../models/AboutSection.php';
require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    // Verificar autenticação se necessário
    // checkAuth();
    
    // Obter parâmetros da requisição
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $sectionType = isset($_GET['section_type']) ? $_GET['section_type'] : null;
    
    // Instanciar modelo
    $aboutSectionModel = new AboutSection();
    
    // Buscar seções sobre
    if ($sectionType) {
        $sections = $aboutSectionModel->getByType($sectionType);
    } else {
        $sections = $aboutSectionModel->getAll($limit, $offset);
    }
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => $sections,
        'limit' => $limit,
        'offset' => $offset,
        'section_type' => $sectionType
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>