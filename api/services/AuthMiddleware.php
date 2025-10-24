<?php

/**
 * Middleware de autenticação para APIs
 */
function requireAuth() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    $authService = new AuthService();
    $user = $authService->getUserFromToken($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token inválido']);
        exit;
    }

    return $user;
}

function requirePermission($permission) {
    $user = requireAuth();

    if (!$user['role'] || !in_array($permission, getRolePermissions($user['role']))) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permissão negada']);
        exit;
    }

    return $user;
}

function getRolePermissions($role) {
    $roles = [
        'admin' => ['upload', 'delete', 'edit', 'view'],
        'editor' => ['upload', 'edit', 'view'],
        'viewer' => ['view']
    ];

    return $roles[$role] ?? [];
}
?>
