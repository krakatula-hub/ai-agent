<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

$user = getUserById($_SESSION['user_id']);

// Создаём токен для Open WebUI
$token = bin2hex(random_bytes(32));
$_SESSION['openwebui_token'] = $token;

// URL Open WebUI с параметрами
$openWebUIUrl = 'http://217.197.115.92:8080/auth/callback';
$redirectUrl = $openWebUIUrl . '?token=' . $token . '&email=' . urlencode($user['email']);

header('Location: ' . $redirectUrl);
exit;
?>
