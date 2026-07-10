<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: /login_page.php');
    exit;
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$user = getUserById($_SESSION['user_id']);
$plan = $_GET['plan'] ?? '';

if (!in_array($plan, ['pro', 'business'])) {
    die('❌ Неверный тариф');
}

$amount = PRICES[$plan];
$planName = getPlanName($plan);

// Создаём платеж в ЮKassa
require_once 'includes/yookassa.php';

$paymentId = 'pay_' . uniqid() . '_' . $user['id'];
$returnUrl = APP_URL . '/payment_success.php?payment_id=' . $paymentId;

$payment = createPayment($amount, $paymentId, $user['id'], $plan, $returnUrl);

if (!$payment) {
    die('❌ Ошибка создания платежа. Попробуйте позже.');
}

// Сохраняем платёж в базу
$db = getDB();
$stmt = $db->prepare("INSERT INTO payments (user_id, payment_id, plan, amount, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->execute([$user['id'], $paymentId, $plan, $amount]);

// Перенаправляем на оплату
header('Location: ' . $payment['confirmation_url']);
exit;
?>
