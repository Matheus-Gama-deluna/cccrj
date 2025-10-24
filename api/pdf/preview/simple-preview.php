<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $fileName = $_GET['file'] ?? '';
    $type = $_GET['type'] ?? 'boletins';
    $thumbnail = $_GET['thumbnail'] ?? false;

    if (empty($fileName)) {
        throw new Exception('Nome do arquivo não especificado');
    }

    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/../data/' . $type . '/' . basename($fileName);

    // Verificar se o arquivo existe no diretório de dados
    if (!file_exists($filePath)) {
        // Tentar também no diretório reports
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/../data/reports/' . basename($fileName);
    }

    if ($thumbnail) {
        // Para thumbnail, retornar uma imagem simples por enquanto
        header('Content-Type: image/jpeg');
        echo "Thumbnail placeholder";
        exit();
    } else {
        // Servir PDF para preview
        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($filePath);
            exit();
        }
    }

    throw new Exception('Arquivo não encontrado');

} catch (Exception $e) {
    http_response_code(200); // Retorna 200 para evitar erro no iframe
    header('Content-Type: text/html');
    echo "
    <!DOCTYPE html>
    <html>
    <head><title>Erro - Debug Info</title></head>
    <body>
        <h1>Debug Information</h1>
        <p><strong>Arquivo:</strong> {$fileName}</p>
        <p><strong>Tipo:</strong> {$type}</p>
        <p><strong>Miniatura:</strong> " . ($thumbnail ? 'Sim' : 'Não') . "</p>
        <p><strong>Erro:</strong> " . $e->getMessage() . "</p>
        <p><strong>Caminho:</strong> {$filePath}</p>
        <p><strong>Arquivo existe:</strong> " . (file_exists($filePath) ? 'SIM' : 'NÃO') . "</p>
    </body>
    </html>";
}
?>
