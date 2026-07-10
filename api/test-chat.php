<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Получаем сообщение
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Сообщение не может быть пустым']);
    exit;
}

// === ПРОВЕРКА ЛИМИТА ДЛЯ БЕСПЛАТНЫХ ===
$isLoggedIn = isset($_SESSION['user_id']);
$limitExceeded = false;

if ($isLoggedIn) {
    $user = getUserById($_SESSION['user_id']);
    if ($user['plan'] === 'free') {
        $limit = 5;
        if ($user['messages_today'] >= $limit) {
            $limitExceeded = true;
        }
    }
}

if ($limitExceeded) {
    http_response_code(403);
    echo json_encode(['error' => 'Лимит сообщений исчерпан. Оформите подписку!']);
    exit;
}

// Проверяем DeepSeek API ключ
if (empty(DEEPSEEK_API_KEY)) {
    http_response_code(500);
    echo json_encode(['error' => 'API ключ не настроен']);
    exit;
}

// Запрос к DeepSeek API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.deepseek.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . DEEPSEEK_API_KEY
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'deepseek-chat',
    'messages' => [
        ['role' => 'system', 'content' => 'Ты полезный AI-помощник. Отвечай кратко и по делу. Если вопрос неясен — уточни.'],
        ['role' => 'user', 'content' => $message]
    ],
    'temperature' => 0.7,
    'max_tokens' => 500
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка AI-сервиса']);
    exit;
}

$data = json_decode($response, true);
$assistantMessage = $data['choices'][0]['message']['content'] ?? '';

// === ОБНОВЛЯЕМ СЧЁТЧИК В БАЗЕ ===
if ($isLoggedIn) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET messages_today = messages_today + 1 WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

echo json_encode([
    'success' => true,
    'response' => $assistantMessage
]);
?>
