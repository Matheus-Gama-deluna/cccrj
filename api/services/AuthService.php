<?php
require_once 'vendor/autoload.php'; // Composer: composer require firebase/php-jwt

use Firebase\JWT\JWT;

class AuthService {
    private $secretKey;
    private $algorithm = 'HS256';

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET') ?: 'fallback-secret-key';
    }

    public function generateToken($userId, $role = 'viewer') {
        $issuedAt = time();
        $expirationTime = $issuedAt + (int)(getenv('SESSION_TIMEOUT') ?: 3600);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, $this->secretKey, [$this->algorithm]);
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUserFromToken($token) {
        $decoded = $this->validateToken($token);
        return $decoded ? $decoded : null;
    }
}
?>
