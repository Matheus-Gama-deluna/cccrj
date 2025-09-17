<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once '../ftp_service.php';

try {
    $ftp = new FtpService(FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_PORT);
    $ftp->connect();
    
    $files = $ftp->listFiles(FTP_REPORTS_PATH);
    
    $reports = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
            $reports[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => $ftp->getFileSize($file),
                'modified' => $ftp->getFileModifiedTime($file)
            ];
        }
    }
    
    $ftp->close();
    
    echo json_encode($reports);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>