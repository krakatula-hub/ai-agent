<?php
// В начале каждого файла
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$user = requireAuth();

// Проверка и сброс лимитов
resetDailyLimit($user['id']);
checkAndResetLimits($user['id']);

// Получение обновленных данных
$updatedUser = getUserById($user['id']);

echo json_encode([
    'id' => $updatedUser['id'],
    'email' => $updatedUser['email'],
    'plan' => $updatedUser['plan'],
    'subscription_end' => $updatedUser['subscription_end'],
    'messages_today' => (int)$updatedUser['messages_today'],
    'messages_left' => getMessagesLeft($updatedUser)
]);
?>