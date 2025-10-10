<?php
// api/json/clipping/list.php

// Configurações de cabeçalho para CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responde imediatamente para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../../utils/JsonCache.php';

// Função para enviar respostas padronizadas
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

try {
    // Caminho para o arquivo JSON
    $jsonFile = __DIR__ . '/../../../data/content/clipping.json';
    
    // Verificar se o arquivo existe
    if (!file_exists($jsonFile)) {
        sendJsonResponse([
            'success' => false,
            'message' => 'Arquivo de dados não encontrado.'
        ], 404);
    }
    
    // Instanciar o cache
    $cache = new JsonCache();
    
    // Tentar obter dados do cache
    $data = $cache->getCachedData($jsonFile);
    
    if ($data === null) {
        // Cache não encontrado ou expirado, ler do arquivo
        $jsonContent = file_get_contents($jsonFile);
        if ($jsonContent === false) {
            throw new Exception('Não foi possível ler o arquivo de dados.');
        }
        
        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Erro ao decodificar o JSON: ' . json_last_error_msg());
        }
        
        // Validar estrutura básica dos dados
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new Exception('Estrutura de dados inválida.');
        }
        
        // Salvar no cache
        $cache->cacheData($jsonFile, $data);
    }
    
    // Obter parâmetros de paginação com valores padrão
    $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 10,
            'min_range' => 1,
            'max_range' => 100
        ]
    ]);
    
    $offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 0,
            'min_range' => 0
        ]
    ]);
    
    // Filtrar itens ativos
    $allClippings = $data['data'] ?? [];
    $activeClippings = array_values(array_filter($allClippings, function($item) {
        return isset($item['is_active']) && $item['is_active'] === true;
    }));
    
    // Paginar resultados
    $totalItems = count($activeClippings);
    $clippings = array_slice($activeClippings, $offset, $limit);
    
    // Retornar resposta de sucesso
    sendJsonResponse([
        'success' => true,
        'data' => $clippings,
        'meta' => [
            'total' => $totalItems,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $totalItems
        ]
    ]);
    
} catch (Exception $e) {
    // Log do erro (você pode implementar um sistema de logs mais robusto aqui)
    error_log('Erro em /api/json/clipping/list.php: ' . $e->getMessage());
    
    // Retornar resposta de erro
    sendJsonResponse([
        'success' => false,
        'message' => 'Ocorreu um erro ao processar sua solicitação.',
        'error' => $e->getMessage()
    ]);
}
?>