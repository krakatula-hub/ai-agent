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
$action = $_GET['action'] ?? 'check';

if ($action === 'check') {
    $limit = PLAN_LIMITS[$user['plan']] ?? 5;
    $left = max(0, $limit - $user['messages_today']);
    
    echo json_encode([
        'success' => true,
        'can_chat' => $left > 0,
        'messages_left' => $left,
        'plan' => $user['plan'],
        'plan_name' => getPlanName($user['plan'])
    ]);
} elseif ($action === 'increment') {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET messages_today = messages_today + 1 WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    echo json_encode(['success' => true]);
}
?>
