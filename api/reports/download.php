<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once '../ftp_service.php';

if (!isset($_GET['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nome do arquivo não especificado']);
    exit;
}

$filename = basename($_GET['file']); // Sanitizar o nome do arquivo
$remoteFile = FTP_REPORTS_PATH . $filename;

try {
    // Verificar se o arquivo existe
    if (pathinfo($filename, PATHINFO_EXTENSION) !== 'pdf') {
        http_response_code(400);
        echo json_encode(['error' => 'Somente arquivos PDF são permitidos']);
        exit;
    }
    
    $ftp = new FtpService(FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_PORT);
    $ftp->connect();
    
    // Certificar-se de que o diretório temporário existe
    if (!file_exists(TEMP_DIR)) {
        mkdir(TEMP_DIR, 0755, true);
    }
    
    // Caminho temporário local para armazenar o arquivo
    $localFile = TEMP_DIR . $filename;
    
    // Baixar o arquivo do FTP
    if ($ftp->downloadFile($remoteFile, $localFile)) {
        $ftp->close();
        
        // Definir headers para download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($localFile));
        
        // Ler e enviar o arquivo
        readfile($localFile);
        
        // Remover o arquivo temporário
        unlink($localFile);
        exit;
    } else {
        $ftp->close();
        http_response_code(500);
        echo json_encode(['error' => 'Não foi possível baixar o arquivo']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>