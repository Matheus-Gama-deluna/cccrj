<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    $year = $_GET['year'] ?? null;

    if (!$year) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ano é obrigatório']);
        exit;
    }

    $fileService = new LocalFileService();
    $structure = $fileService->listFilesByStructure('boletins', $year);

    if (!isset($structure[$year])) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'year' => $year,
            'total' => 0
        ]);
        exit;
    }

    // Extrair meses do ano específico
    $months = [];
    foreach ($structure[$year] as $monthDir => $monthData) {
        if (preg_match('/^(\d{2})_(.+)$/', $monthDir, $matches)) {
            $months[] = [
                'month' => $matches[1],
                'month_name' => $matches[2],
                'month_dir' => $monthDir,
                'count' => $monthData['count']
            ];
        }
    }

    // Ordenar meses por número
    usort($months, function($a, $b) {
        return $a['month'] <=> $b['month'];
    });

    echo json_encode([
        'success' => true,
        'data' => $months,
        'year' => $year,
        'total' => count($months)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
