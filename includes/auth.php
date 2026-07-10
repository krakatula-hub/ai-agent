<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken($userId, $email) {
    $payload = [
        'user_id' => $userId,
        'email' => $email,
        'exp' => time() + (30 * 24 * 60 * 60) // 30 дней
    ];
    return JWT::encode($payload, JWT_SECRET, 'HS256');
}

function verifyToken($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}

function getCurrentUser() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (strpos($authHeader, 'Bearer ') !== 0) {
        return null;
    }
    
    $token = substr($authHeader, 7);
    $payload = verifyToken($token);
    
    if (!$payload) {
        return null;
    }
    
    return getUserById($payload['user_id']);
}

function requireAuth() {
    $user = getCurrentUser();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Не авторизован']);
        exit;
    }
    return $user;
}

// Проверка лимитов пользователя
function checkUserLimits($user) {
    // Проверка и сброс лимитов
    $db = getDB();
    resetDailyLimit($user['id']);
    
    // Проверка подписки
    if ($user['plan'] !== 'free' && $user['subscription_end'] && strtotime($user['subscription_end']) < time()) {
        $stmt = $db->prepare("UPDATE users SET plan = 'free', subscription_end = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $user['plan'] = 'free';
        http_response_code(403);
        echo json_encode(['error' => 'Подписка истекла']);
        exit;
    }
    
    $limit = PLAN_LIMITS[$user['plan']];
    if ($user['messages_today'] >= $limit) {
        http_response_code(403);
        echo json_encode(['error' => 'Дневной лимит исчерпан']);
        exit;
    }
}
?>