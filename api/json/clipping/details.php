<?php
// api/json/clipping/details.php

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
    // Validar e obter o ID do clipping
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($id === false || $id === null) {
        sendJsonResponse([
            'success' => false,
            'message' => 'ID do clipping inválido ou não fornecido.'
        ], 400);
    }
    
    // Caminho para o arquivo JSON
    $jsonFile = __DIR__ . '/../../../data/content/clipping.json';
    
    // Verificar se o arquivo existe
    if (!file_exists($jsonFile)) {
        sendJsonResponse([
            'success' => false,
            'message' => 'Arquivo de dados não encontrado.'
        ], 500);
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
    
    // Procurar clipping pelo ID
    $clipping = null;
    foreach ($data['data'] as $item) {
        if (isset($item['id']) && (int)$item['id'] === (int)$id) {
            if (isset($item['is_active']) && $item['is_active'] === false) {
                sendJsonResponse([
                    'success' => false,
                    'message' => 'Este clipping está desativado.'
                ], 403);
            }
            $clipping = $item;
            break;
        }
    }
    
    // Se não encontrou o clipping
    if ($clipping === null) {
        sendJsonResponse([
            'success' => false,
            'message' => 'Clipping não encontrado.'
        ], 404);
    }
    
    // Retornar resposta de sucesso com os dados do clipping
    sendJsonResponse([
        'success' => true,
        'data' => $clipping
    ]);
    
} catch (Exception $e) {
    // Log do erro
    error_log('Erro em /api/json/clipping/details.php: ' . $e->getMessage());
    
    // Retornar resposta de erro
    sendJsonResponse([
        'success' => false,
        'message' => 'Ocorreu um erro ao processar sua solicitação.',
        'error' => $e->getMessage()
    ], 500);
}
?>