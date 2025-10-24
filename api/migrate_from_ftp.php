<?php
header('Content-Type: application/json');

// Incluir dependências
require_once __DIR__ . '/config/ftp_config.php';
require_once __DIR__ . '/ftp_service.php';
require_once __DIR__ . '/services/LocalFileService.php';

try {
    $ftp = new FtpService(FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_PORT);
    $ftp->connect();

    $fileService = new LocalFileService();

    // Listar arquivos no FTP
    $ftpFiles = $ftp->listFiles(FTP_REPORTS_PATH);
    $migrated = 0;
    $errors = [];

    foreach ($ftpFiles as $file) {
        try {
            // Verificar se é um arquivo PDF
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                // Baixar do FTP para arquivo temporário
                $tempFile = tempnam(sys_get_temp_dir(), 'ftp_migration_');
                $ftp->downloadFile($file, $tempFile);

                // Preparar dados para upload local
                $fileInfo = [
                    'name' => basename($file),
                    'tmp_name' => $tempFile,
                    'size' => filesize($tempFile),
                    'error' => UPLOAD_ERR_OK
                ];

                // Fazer upload para sistema local
                $result = $fileService->uploadFile($fileInfo, pathinfo($file, PATHINFO_FILENAME), false);

                // Remover arquivo temporário
                unlink($tempFile);

                $migrated++;
                echo "✅ Migrado: " . basename($file) . "\n";
            }
        } catch (Exception $e) {
            $errors[] = "❌ Erro ao migrar {$file}: " . $e->getMessage();
            error_log("Erro na migração de {$file}: " . $e->getMessage());
        }
    }

    $ftp->close();

    // Resposta final
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "MIGRAÇÃO CONCLUÍDA\n";
    echo str_repeat("=", 50) . "\n";
    echo "Arquivos migrados: {$migrated}\n";
    echo "Erros: " . count($errors) . "\n";

    if (!empty($errors)) {
        echo "\nErros encontrados:\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }

    echo json_encode([
        'success' => true,
        'migrated' => $migrated,
        'errors' => $errors,
        'message' => "Migração concluída: {$migrated} arquivos migrados"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $ftp->close();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'errors' => ['Erro crítico na migração']
    ], JSON_PRETTY_PRINT);
}
?>
