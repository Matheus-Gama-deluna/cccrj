<?php
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

    // Verificar se o arquivo foi especificado
    if (!isset($_GET['file']) || empty($_GET['file'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nome do arquivo não especificado.'
        ]);
        exit();
    }

    $fileService = new LocalFileService();
    $fileName = basename($_GET['file']);
    $type = $_GET['type'] ?? 'reports';
    $path = isset($_GET['path']) ? trim($_GET['path']) : null;

    // Obter o caminho do arquivo
    if ($path && $type === 'boletins') {
        $candidate = $fileService->resolveBoletimPath($path, $fileName);
        if ($candidate) {
            $filePath = $candidate;
        }
    }

    if (empty($filePath)) {
        $filePath = $fileService->downloadFile($fileName, $type);
    }

    // Verificar se o arquivo existe
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Arquivo não encontrado.'
        ]);
        exit();
    }

    // Enviar headers para download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Enviar o arquivo
    readfile($filePath);
    exit();

} catch (Exception $e) {
    error_log("Erro no download: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage()
    ]);
}
?>
?>