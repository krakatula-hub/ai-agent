<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

$user = getUserById($_SESSION['user_id']);
$method = $_SERVER['REQUEST_METHOD'];

// === ПОЛУЧЕНИЕ КОЛИЧЕСТВА СООБЩЕНИЙ СЕГОДНЯ ===
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'count') {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM chat_messages WHERE user_id = ? AND DATE(created_at) = CURDATE()");
    $stmt->execute([$_SESSION['user_id']]);
    $count = $stmt->fetchColumn();
    
    echo json_encode(['success' => true, 'count' => (int)$count]);
    exit;
}

// === ПОЛУЧЕНИЕ СООБЩЕНИЙ ===
if ($method === 'GET') {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT cm.*, u.email, u.plan
        FROM chat_messages cm
        JOIN users u ON cm.user_id = u.id
        ORDER BY cm.created_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $messages = $stmt->fetchAll();
    $messages = array_reverse($messages);
    
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}

// === ОТПРАВКА СООБЩЕНИЯ ===
if ($method === 'POST' && !isset($_GET['action'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    $message = trim($data['message'] ?? '');
    
    if (empty($message)) {
        http_response_code(400);
        echo json_encode(['error' => 'Сообщение не может быть пустым']);
        exit;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM chat_messages WHERE user_id = ? AND DATE(created_at) = CURDATE()");
    $stmt->execute([$_SESSION['user_id']]);
    $todayCount = $stmt->fetchColumn();
    
    if ($todayCount >= 10) {
        http_response_code(403);
        echo json_encode(['error' => 'Вы превысили лимит сообщений в общем чате (10 в день)']);
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $message]);
    
    echo json_encode(['success' => true]);
    exit;
}

// === УДАЛЕНИЕ СООБЩЕНИЯ (ТОЛЬКО ДЛЯ АДМИНА) ===
if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (!isAdmin($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Доступ запрещён']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $messageId = (int)($data['message_id'] ?? 0);
    
    if (!$messageId) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указан ID сообщения']);
        exit;
    }
    
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM chat_messages WHERE id = ?");
    $stmt->execute([$messageId]);
    
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Метод не разрешён']);
?>
