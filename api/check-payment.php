<?php
// В начале каждого файла
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

use YooKassa\Client;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

$user = requireAuth();

$paymentId = $_GET['payment_id'] ?? '';

if (empty($paymentId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Не указан ID платежа']);
    exit;
}

// Проверка, что платеж принадлежит пользователю
$db = getDB();
$stmt = $db->prepare("SELECT * FROM payments WHERE payment_id = ? AND user_id = ?");
$stmt->execute([$paymentId, $user['id']]);
$payment = $stmt->fetch();

if (!$payment) {
    http_response_code(404);
    echo json_encode(['error' => 'Платеж не найден']);
    exit;
}

// Проверка статуса в ЮKassa
try {
    $client = new Client();
    $client->setAuth(YOOKASSA_SHOP_ID, YOOKASSA_SECRET_KEY);
    
    $paymentInfo = $client->getPaymentInfo($paymentId);
    $status = $paymentInfo->getStatus();
    
    // Обновление статуса в БД
    $stmt = $db->prepare("UPDATE payments SET status = ? WHERE payment_id = ?");
    $stmt->execute([$status, $paymentId]);
    
    // Если платеж успешен - активируем подписку
    if ($status === 'succeeded') {
        $plan = $payment['plan'];
        updateUserPlan($user['id'], $plan);
        
        // Сброс лимитов
        $stmt = $db->prepare("UPDATE users SET messages_today = 0 WHERE id = ?");
        $stmt->execute([$user['id']]);
    }
    
    echo json_encode([
        'status' => $status,
        'plan' => $payment['plan']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка проверки: ' . $e->getMessage()]);
}
?>