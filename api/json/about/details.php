<?php
// api/json/about/details.php

require_once __DIR__ . '/../../../utils/JsonCache.php';

header('Content-Type: application/json');

try {
    // Obter ID da seção
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID da seção não fornecido'
        ]);
        exit;
    }
    
    // Ler o arquivo JSON
    $jsonFile = __DIR__ . '/../../../data/content/about.json';
    
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
    
    // Procurar seção pelo ID
    $section = null;
    foreach ($data['data'] as $item) {
        if ($item['id'] == $id && $item['is_active'] === true) {
            $section = $item;
            break;
        }
    }
    
    if (!$section) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Seção não encontrada'
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