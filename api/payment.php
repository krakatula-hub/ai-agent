<?php
// В начале каждого файла
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

use YooKassa\Client;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

$user = requireAuth();

$data = json_decode(file_get_contents('php://input'), true);
$plan = $data['plan'] ?? '';

if (!in_array($plan, ['pro', 'business'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный тариф']);
    exit;
}

$amount = PRICES[$plan];

// Создание клиента ЮKassa
$client = new Client();
$client->setAuth(YOOKASSA_SHOP_ID, YOOKASSA_SECRET_KEY);

// Уникальный ID платежа
$paymentId = uniqid('pay_');

try {
    $payment = $client->createPayment([
        'amount' => [
            'value' => (string)$amount,
            'currency' => 'RUB'
        ],
        'confirmation' => [
            'type' => 'redirect',
            'return_url' => APP_URL . '/success.php?payment_id=' . $paymentId
        ],
        'capture' => true,
        'description' => 'Подписка AI Agent - ' . strtoupper($plan),
        'metadata' => [
            'user_id' => (string)$user['id'],
            'plan' => $plan
        ]
    ], $paymentId);

    // Сохранение в историю
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO payments (user_id, payment_id, plan, amount, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$user['id'], $paymentId, $plan, $amount]);

    echo json_encode([
        'payment_id' => $paymentId,
        'confirmation_url' => $payment->getConfirmation()->getConfirmationUrl(),
        'amount' => $amount
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка создания платежа: ' . $e->getMessage()]);
}
?>