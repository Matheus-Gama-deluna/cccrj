<?php
// api/utils/functions.php

// Função para validar e limpar dados de entrada
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Função para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Função para validar URL
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

// Função para formatar datas
function formatDate($dateString, $format = 'd/m/Y') {
    $date = new DateTime($dateString);
    return $date->format($format);
}

// Função para calcular tempo de leitura
function calculateReadingTime($text) {
    $wordsPerMinute = 200;
    $words = str_word_count(strip_tags($text));
    $minutes = ceil($words / $wordsPerMinute);
    return $minutes;
}

// Função para gerar slug
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

// Função para verificar se é uma requisição AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Função para enviar resposta JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Função para enviar resposta de erro
function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse([
        'success' => false,
        'message' => $message
    ], $statusCode);
}

// Função para enviar resposta de sucesso
function sendSuccessResponse($data = [], $message = '') {
    $response = ['success' => true];
    
    if (!empty($message)) {
        $response['message'] = $message;
    }
    
    if (!empty($data)) {
        $response['data'] = $data;
    }
    
    sendJsonResponse($response);
}
?>