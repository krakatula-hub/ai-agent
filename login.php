<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$user = getUserByEmail($email);

if ($user && verifyPassword($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['plan'] = $user['plan'];
    
    // Перенаправляем в кабинет
    header('Location: /cabinet.php');
    exit;
}

// Если ошибка — возвращаем на страницу входа с ошибкой
header('Location: /login_page.php?error=1');
exit;
?>
