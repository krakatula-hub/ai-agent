<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

session_start();

// Проверяем авторизацию
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
    http_response_code(401);
    echo json_encode(['error' => 'Пользователь не найден']);
    exit;
}

// === ПРОВЕРКА ЛИМИТОВ ===
$limits = PLAN_LIMITS;
$limit = $limits[$user['plan']] ?? 5;

if ($user['messages_today'] >= $limit) {
    http_response_code(403);
    echo json_encode(['error' => 'Дневной лимит исчерпан. Оформите подписку!']);
    exit;
}

// Сохраняем расход токенов
$tokensUsed = $data['usage']['total_tokens'] ?? 0;

// Отправляем уведомление при аномальном расходе
if ($tokensUsed > 5000) {
    error_log("⚠️ Аномальный расход токенов: $tokensUsed пользователь: {$user['id']}");
    // Отправить email или Telegram
}

// Получаем сообщение
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Сообщение не может быть пустым']);
    exit;
}

// Защита от флуда
session_start();
if (!isset($_SESSION['last_request'])) {
    $_SESSION['last_request'] = time();
}

if (time() - $_SESSION['last_request'] < 2) {
    http_response_code(429);
    echo json_encode(['error' => 'Слишком много запросов. Подождите 2 секунды.']);
    exit;
}
$_SESSION['last_request'] = time();


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
        ['role' => 'system', 'content' => 'Ты полезный AI-помощник. Отвечай кратко и по делу.'],
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
    echo json_encode(['error' => 'Ошибка API']);
    exit;
}

$data = json_decode($response, true);
$assistantMessage = $data['choices'][0]['message']['content'] ?? '';

// Обновляем счётчик сообщений
$db = getDB();
$stmt = $db->prepare("UPDATE users SET messages_today = messages_today + 1 WHERE id = ?");
$stmt->execute([$user['id']]);

// Сохраняем в историю
$stmt = $db->prepare("INSERT INTO chat_history (user_id, message, response, tokens) VALUES (?, ?, ?, ?)");
$stmt->execute([$user['id'], $message, $assistantMessage, $data['usage']['total_tokens'] ?? 0]);

echo json_encode([
    'success' => true,
    'response' => $assistantMessage,
    'messages_left' => $limit - ($user['messages_today'] + 1)
]);
?>
