<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    // Validar parâmetros
    $file = $_FILES['file'] ?? null;
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? ''; // YYYY-MM-DD
    $type = $_POST['type'] ?? 'boletins';

    if (!$file || !$date) {
        throw new Exception('Arquivo e data são obrigatórios');
    }

    $fileService = new LocalFileService();
    $result = $fileService->uploadFileByDate($file, $title, $date, $type);

    echo json_encode([
        'success' => true,
        'message' => 'Boletim enviado com sucesso!',
        'file' => $result
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
