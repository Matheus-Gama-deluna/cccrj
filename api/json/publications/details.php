<?php
// api/json/publications/details.php

require_once __DIR__ . '/../../../utils/JsonCache.php';

header('Content-Type: application/json');

try {
    // Obter ID da publicação
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID da publicação não fornecido'
        ]);
        exit;
    }
    
    // Ler o arquivo JSON
    $jsonFile = __DIR__ . '/../../../data/content/publications.json';
    
    // Instanciar o cache
    $cache = new JsonCache();
    
    // Tentar obter dados do cache
    $data = $cache->getCachedData($jsonFile);
    
    if ($data === null) {
        // Cache não encontrado ou expirado, ler do arquivo
        $data = json_decode(file_get_contents($jsonFile), true);
        
        // Salvar no cache
        $cache->cacheData($jsonFile, $data);
    }
    
    // Procurar publicação pelo ID
    $publication = null;
    foreach ($data['data'] as $item) {
        if ($item['id'] == $id && $item['is_active'] === true) {
            $publication = $item;
            break;
        }
    }
    
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