<?php
// api/json/about/list.php

require_once __DIR__ . '/../../../utils/JsonCache.php';

header('Content-Type: application/json');

try {
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
    
    // Obter parâmetros de paginação
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Filtrar e paginar dados
    $allSections = $data['data'];
    $activeSections = array_filter($allSections, function($item) {
        return $item['is_active'] === true;
    });
    
    $sections = array_slice($activeSections, $offset, $limit);
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => array_values($sections),
        'total' => count($activeSections),
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