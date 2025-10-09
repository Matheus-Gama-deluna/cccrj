<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Simple test to verify PHP is working
echo json_encode([
    'status' => 'success',
    'message' => 'PHP is working correctly',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>