<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../../services/PDFPreviewService.php';
    require_once __DIR__ . '/../../services/LocalFileService.php';

    $fileName = $_GET['file'] ?? '';
    $type = $_GET['type'] ?? 'reports';
    $thumbnail = filter_var($_GET['thumbnail'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $thumbnailWidth = isset($_GET['width']) ? (int) $_GET['width'] : 300;

    if (empty($fileName)) {
        throw new Exception('Nome do arquivo não especificado');
    }

    $previewService = new PDFPreviewService();
    $fileService = new LocalFileService();

    $filePath = $fileService->downloadFile($fileName, $type);

    if ($thumbnail) {
        $thumbnailWidth = max(100, $thumbnailWidth); // largura mínima para imagens
        $thumbnailPath = $previewService->generateThumbnail($filePath, $fileName, $thumbnailWidth);

        if ($thumbnailPath && file_exists($thumbnailPath)) {
            header('Content-Type: image/jpeg');
            header('Cache-Control: public, max-age=86400');
            readfile($thumbnailPath);
            exit();
        }

        throw new Exception('Não foi possível gerar a miniatura');
    }

    // Servir PDF inline para visualização
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($fileName) . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($filePath);
    exit();

} catch (Exception $e) {
    error_log("Erro no preview: " . $e->getMessage());
    http_response_code(200); // Retorna 200 para evitar erro no iframe
    header('Content-Type: text/html');
    echo "
    <!DOCTYPE html>
    <html>
    <head><title>Erro</title></head>
    <body>
        <h1>Arquivo não encontrado</h1>
        <p>O arquivo solicitado não foi encontrado no servidor.</p>
        <p><strong>Arquivo:</strong> {$fileName}</p>
        <p><strong>Erro:</strong> " . $e->getMessage() . "</p>
    </body>
    </html>";
}
?>
