<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Валидация
if (empty($email) || empty($password) || empty($password_confirm)) {
    header('Location: /register_page.php?error=Все поля обязательны');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /register_page.php?error=Неверный формат email');
    exit;
}

if (strlen($password) < 6) {
    header('Location: /register_page.php?error=Пароль должен быть минимум 6 символов');
    exit;
}

if ($password !== $password_confirm) {
    header('Location: /register_page.php?error=Пароли не совпадают');
    exit;
}

// Проверяем, существует ли пользователь
$existing = getUserByEmail($email);
if ($existing) {
    header('Location: /register_page.php?error=Пользователь с таким email уже существует');
    exit;
}

// Создаём пользователя
$userId = createUser($email, $password);

if ($userId) {
    // === ОТПРАВКА ПРИВЕТСТВЕННОГО ПИСЬМА ===
    $subject = "Добро пожаловать в AI Agent!";
    $message = "
        <html>
        <head><title>Добро пожаловать</title></head>
        <body>
            <h2>Добро пожаловать, {$email}!</h2>
            <p>Вы успешно зарегистрировались в AI Agent.</p>
            <p>Ваш тариф: <strong>Бесплатный</strong></p>
            <p><a href='" . APP_URL . "'>Перейти на сайт</a></p>
        </body>
        </html>
    ";
    $headers = "From: ai-agent@ai.nkvopros.ru\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    mail($email, $subject, $message, $headers);
    
    // Создаём сессию и входим автоматически
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $email;
    $_SESSION['plan'] = 'free';
    
    // Логируем событие
    require_once 'includes/logger.php';
    logEvent($userId, 'register', 'free', "Новый пользователь зарегистрировался");
    
    header('Location: /cabinet.php');
    exit;
} else {
    header('Location: /register_page.php?error=Ошибка при создании пользователя');
    exit;
}
?>
