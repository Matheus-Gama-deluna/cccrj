<?php
// api/json/archive/details.php

require_once __DIR__ . '/../../../utils/JsonCache.php';

header('Content-Type: application/json');

try {
    // Obter ID do item
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do item não fornecido'
        ]);
        exit;
    }
    
    // Ler o arquivo JSON
    $jsonFile = __DIR__ . '/../../../data/content/archive.json';
    
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
    
    // Procurar item pelo ID
    $item = null;
    foreach ($data['data'] as $dataItem) {
        if ($dataItem['id'] == $id && $dataItem['is_active'] === true) {
            $item = $dataItem;
            break;
        }
    }
    
    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Item não encontrado'
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