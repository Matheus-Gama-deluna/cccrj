<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    // Incluir o LocalFileService
    require_once __DIR__ . '/services/LocalFileService.php';

    // Verificar se um arquivo foi enviado
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Nenhum arquivo foi enviado ou ocorreu um erro no upload');
    }

    $file = $_FILES['file'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $isBoletim = filter_var($_POST['is_boletim'] ?? false, FILTER_VALIDATE_BOOLEAN);

    // Usar LocalFileService para processar o upload
    $fileService = new LocalFileService();
    $result = $fileService->uploadFile($file, $title, $isBoletim);

    // Verificar se o arquivo original era boletim.pdf e salvá-lo com nome específico
    $originalFileName = basename($file['name'], '.' . pathinfo($file['name'], PATHINFO_EXTENSION));
    if (strtolower($originalFileName) === 'boletim') {
        $boletimPath = '../boletim.pdf';
        if (!copy($result['path'], $boletimPath)) {
            error_log("Falha ao copiar boletim.pdf para a raiz");
        }
    }

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Relatório enviado com sucesso!',
        'file' => [
            'name' => $result['name'],
            'size' => $result['size'],
            'uploaded_at' => $result['uploaded_at'],
            'is_boletim' => $isBoletim
        ]
    ]);

} catch (Exception $e) {
    error_log("Erro no upload: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>