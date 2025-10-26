<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    $fileService = new LocalFileService();

    // Listar arquivos na pasta legada
    $currentFiles = $fileService->listFiles('boletins');
    $migrated = 0;
    $errors = [];

    foreach ($currentFiles as $file) {
        try {
            // Extrair data do nome do arquivo
            $date = $fileService->extractDateFromFilename($file['name']);

            if ($date) {
                // Migrar arquivo para nova estrutura
                $fileService->migrateFileToDateStructure($file['name'], $date, 'boletins');
                $migrated++;
            } else {
                $errors[] = "Não foi possível extrair data do arquivo: {$file['name']}";
            }
        } catch (Exception $e) {
            $errors[] = "Erro migrando {$file['name']}: " . $e->getMessage();
        }
    }

    echo json_encode([
        'success' => true,
        'migrated' => $migrated,
        'errors' => $errors,
        'total_files' => count($currentFiles),
        'message' => "Migração concluída: {$migrated} arquivos migrados"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
