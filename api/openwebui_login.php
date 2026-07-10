<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login_page.php');
    exit;
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$user = getUserById($_SESSION['user_id']);

// Генерируем временный токен
$token = bin2hex(random_bytes(32));
$_SESSION['openwebui_token'] = $token;

// Перенаправляем в Open WebUI с токеном
$openWebUIUrl = 'http://217.197.115.92:8080/auth/callback';
$redirectUrl = $openWebUIUrl . '?token=' . $token . '&email=' . urlencode($user['email']);

header('Location: ' . $redirectUrl);
exit;
?>
