<?php
// api/config/auth.php

session_start();

// Função para verificar se o usuário está autenticado
function isAuthenticated() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

// Função para verificar se o usuário é administrador
function isAdmin() {
    return isAuthenticated() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Função para exigir autenticação
function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Autenticação necessária']);
        exit;
    }
}

// Função para exigir permissões de administrador
function requireAdmin() {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permissões de administrador necessárias']);
        exit;
    }
}

// Função para fazer login
function login($username, $password) {
    // Em uma implementação real, você faria a verificação no banco de dados
    // Aqui estamos usando credenciais hardcoded para fins de demonstração
    
    // Credenciais de exemplo - em produção, isso viria do banco de dados
    $validUsers = [
        'admin' => [
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin'
        ]
    ];
    
    if (isset($validUsers[$username]) && password_verify($password, $validUsers[$username]['password'])) {
        $_SESSION['user_id'] = 1; // ID de exemplo
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $validUsers[$username]['role'];
        return true;
    }
    
    return false;
}

// Função para fazer logout
function logout() {
    session_destroy();
}

// Função para obter informações do usuário logado
function getCurrentUser() {
    if (isAuthenticated()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }
    
    return null;
}
?>