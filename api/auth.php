<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if ($action === 'login') {
    $user = getUserByEmail($email);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Пользователь не найден']);
        exit;
    }
    
    if (!verifyPassword($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Неверный пароль']);
        exit;
    }
    
    // СОЗДАЁМ СЕССИЮ
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['plan'] = $user['plan'];
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'plan' => $user['plan']
        ]
    ]);
    
} elseif ($action === 'register') {
    $existing = getUserByEmail($email);
    if ($existing) {
        http_response_code(400);
        echo json_encode(['error' => 'Пользователь уже существует']);
        exit;
    }
    
    $userId = createUser($email, $password);
    
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $email;
    $_SESSION['plan'] = 'free';
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userId,
            'email' => $email,
            'plan' => 'free'
        ]
    ]);
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Неизвестное действие']);
}
?>
