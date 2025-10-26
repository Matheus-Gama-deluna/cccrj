<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    $year = $_GET['year'] ?? null;
    $month = $_GET['month'] ?? null; // formato: 01_Janeiro

    if (!$year || !$month) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ano e mês são obrigatórios']);
        exit;
    }

    $fileService = new LocalFileService();
    $files = $fileService->listFilesByStructure('boletins', $year, $month);

    echo json_encode([
        'success' => true,
        'data' => $files,
        'year' => $year,
        'month' => $month,
        'total' => count($files)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
