<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Include the LocalFileService
    require_once __DIR__ . '/services/LocalFileService.php';

    // Check if a file was sent
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file was sent or an error occurred during upload');
    }

    $file = $_FILES['file'];
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? '';

    // Validate the date
    if (empty($date)) {
        throw new Exception('Date is required');
    }

    // Use LocalFileService to process the upload
    $fileService = new LocalFileService();
    $result = $fileService->uploadFileByDate($file, $title, $date, 'boletins');

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Boletim sent successfully!',
        'file' => [
            'name' => $result['name'],
            'size' => $result['size'],
            'uploaded_at' => $result['uploaded_at'],
            'date' => $result['date'],
        ]
    ]);

} catch (Exception $e) {
    error_log("Upload error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>