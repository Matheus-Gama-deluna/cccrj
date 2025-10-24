<?php
// API Router - Redireciona para os endpoints corretos
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Se a requisição for para uma subpasta que existe, não processar aqui
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Se não for uma requisição para a raiz da API, deixar o Apache processar normalmente
if ($requestUri !== $scriptName && $requestUri !== $scriptName . '/') {
    return false;
}

// API Router principal
echo json_encode([
    'status' => 'API Router',
    'message' => 'CCCRJ API is running',
    'version' => '1.0',
    'endpoints' => [
        'reports' => '/api/reports/',
        'pdf' => '/api/pdf/',
        'news' => '/api/news/'
    ]
]);
?>
