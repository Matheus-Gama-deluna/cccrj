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
    // Verificar se um arquivo foi enviado
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Nenhum arquivo foi enviado ou ocorreu um erro no upload');
    }

    $file = $_FILES['file'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $isBoletim = $_POST['is_boletim'] ?? '0';  // Este parâmetro não será mais usado

    // Validar tipo de arquivo
    $fileType = mime_content_type($file['tmp_name']);
    if ($fileType !== 'application/pdf') {
        throw new Exception('Apenas arquivos PDF são permitidos');
    }

    // Validar tamanho do arquivo (máximo 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        throw new Exception('O arquivo deve ter no máximo 10MB');
    }

    // Diretórios para armazenamento
    $uploadDir = '../reports/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Gerar nome único para o arquivo
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $title) . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    // Mover arquivo para o diretório de destino
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Falha ao salvar o arquivo');
    }
    
    // Verificar se o arquivo original era boletim.pdf e salvá-lo com nome específico
    $originalFileName = basename($file['name'], '.' . $fileExtension);
    if (strtolower($originalFileName) === 'boletim') {
        $boletimPath = '../boletim.pdf';
        if (!copy($filePath, $boletimPath)) {
            error_log("Falha ao copiar boletim.pdf para a raiz");
        }
    }

    // Confirmação de upload para qualquer tipo de arquivo PDF
    echo json_encode([
        'success' => true,
        'message' => 'Relatório enviado com sucesso!',
        'file' => $fileName
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>