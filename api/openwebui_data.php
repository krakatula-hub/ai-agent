<?php
header('Content-Type: application/json');
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

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Пользователь не найден']);
    exit;
}

// Лимиты
$limits = PLAN_LIMITS;
$messagesLimit = $limits[$user['plan']] ?? 5;
$messagesLeft = max(0, $messagesLimit - $user['messages_today']);

// Все AI-агенты
$agents = AI_AGENTS;

// Формируем ответ
echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'email' => $user['email'],
        'plan' => $user['plan'],
        'plan_name' => getPlanName($user['plan']),
        'messages_limit' => $messagesLimit,
        'messages_today' => (int)$user['messages_today'],
        'messages_left' => $messagesLeft,
        'subscription_end' => $user['subscription_end']
    ],
    'agents' => $agents,
    'prices' => PRICES,
    'limits' => PLAN_LIMITS
]);
?>
