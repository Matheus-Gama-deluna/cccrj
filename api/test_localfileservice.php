<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Incluir o LocalFileService
    require_once __DIR__ . '/services/LocalFileService.php';

    $fileService = new LocalFileService();

    // Testar listagem de arquivos
    $files = $fileService->listFiles('reports');

    echo json_encode([
        'success' => true,
        'message' => 'LocalFileService funcionando corretamente',
        'data' => [
            'total_files' => count($files),
            'files' => $files,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro no LocalFileService',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
