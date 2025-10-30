<?php
// api/json/archive/list.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../utils/JsonCache.php';

// O caminho do arquivo JSON
$jsonFile = __DIR__ . '/../../../data/archive/items.json';
if (!file_exists($jsonFile)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Arquivo de dados não encontrado.'
    ]);
    exit();
}

header('Content-Type: application/json');

try {
    // Ler o arquivo JSON
    $jsonFile = realpath(__DIR__ . '/../../../data/archive/items.json');
    
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
    
    // Garantir que $data['data'] seja um array
    $allItems = is_array($data['data'] ?? null) ? $data['data'] : [];
    
    // Filtrar itens ativos
    $activeItems = [];
    if (!empty($allItems)) {
        $activeItems = array_filter($allItems, function($item) {
            return isset($item['is_active']) && $item['is_active'] === true;
        });
    }
    
    // Obter parâmetros de filtro
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';

    // Filtrar itens
    $filteredItems = $activeItems;

    if (!empty($search)) {
        $filteredItems = array_filter($filteredItems, function($item) use ($search) {
            return stristr($item['title'], $search) || stristr($item['description'], $search);
        });
    }

    if (!empty($type)) {
        $filteredItems = array_filter($filteredItems, function($item) use ($type) {
            return $item['item_type'] === $type;
        });
    }

    if (!empty($year)) {
        $filteredItems = array_filter($filteredItems, function($item) use ($year) {
            return date('Y', strtotime($item['date'])) === $year;
        });
    }

    // Paginar itens
    $items = array_slice($filteredItems, $offset, $limit);
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'data' => array_values($items),
        'total' => count($filteredItems),
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