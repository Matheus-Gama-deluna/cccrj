<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responde para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Incluir o LocalFileService
    require_once __DIR__ . '/../services/LocalFileService.php';

    $fileService = new LocalFileService();
    $type = $_GET['type'] ?? 'reports';

    // Implementar paginação
    $page = (int)($_GET['page'] ?? 1);
    $perPage = (int)($_GET['per_page'] ?? 10);

    $allFiles = $fileService->listFiles($type);
    $offset = ($page - 1) * $perPage;
    $files = array_slice($allFiles, $offset, $perPage);

    // Resposta JSON com paginação
    echo json_encode([
        'success' => true,
        'data' => $files,
        'meta' => [
            'total' => count($allFiles),
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil(count($allFiles) / $perPage),
            'timestamp' => date('c')
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Erro na listagem de arquivos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage()
    ]);
}
?>
