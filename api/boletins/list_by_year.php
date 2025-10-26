<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    $fileService = new LocalFileService();
    $structure = $fileService->listFilesByStructure('boletins');

    // Extrair apenas os anos da estrutura
    $years = array_keys($structure);

    // Ordenar anos decrescente
    rsort($years);

    echo json_encode([
        'success' => true,
        'data' => $years,
        'total' => count($years)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
