<?php
session_start();

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

// Сохраняем запрос в базу
$db = getDB();
$stmt = $db->prepare("INSERT INTO payment_requests (user_id, plan, amount, status) VALUES (?, ?, ?, 'pending')");
$stmt->execute([$user['id'], $plan, $amount]);

// === ОТПРАВКА ПИСЬМА АДМИНИСТРАТОРУ ===
$adminEmail = 'ejikovvladimir@yandex.ru'; // ЗАМЕНИТЕ НА ВАШ EMAIL!
$subject = "💰 Новый запрос на оплату!";  // ← ТЕМА ПИСЬМА
$message = "
    <html>
    <head><title>Запрос на оплату</title></head>
    <body style='font-family: Arial;'>
        <h2 style='color: #4facfe;'>💰 Новый запрос на оплату</h2>
        <p><strong>Пользователь:</strong> {$user['email']}</p>
        <p><strong>Тариф:</strong> {$planName}</p>
        <p><strong>Сумма:</strong> {$amount} ₽</p>
        <p><strong>ID пользователя:</strong> {$user['id']}</p>
        <p><a href='" . APP_URL . "/admin.php' style='display:inline-block; padding:10px 20px; background:#4facfe; color:#fff; text-decoration:none; border-radius:5px;'>Перейти в админ-панель</a></p>
    </body>
    </html>
";
$headers = "From: ai-agent@ai.nkvopros.ru\r\n";
$headers .= "Content-Type: text/html; charset=utf-8\r\n";
mail($adminEmail, $subject, $message, $headers);

// === ОТПРАВКА ПОДТВЕРЖДЕНИЯ ПОЛЬЗОВАТЕЛЮ ===
$userSubject = "✅ Ваш запрос на оплату получен";  // ← ТЕМА ДЛЯ ПОЛЬЗОВАТЕЛЯ
$userMessage = "
    <html>
    <head><title>Запрос на оплату</title></head>
    <body style='font-family: Arial;'>
        <h2 style='color: #2ecc71;'>✅ Запрос на оплату получен</h2>
        <p>Мы получили ваш запрос на подключение тарифа <strong>{$planName}</strong>.</p>
        <p><strong>Сумма:</strong> {$amount} ₽</p>
        <p>Мы свяжемся с вами в ближайшее время для подтверждения оплаты.</p>
        <p><a href='" . APP_URL . "/cabinet.php' style='display:inline-block; padding:10px 20px; background:#4facfe; color:#fff; text-decoration:none; border-radius:5px;'>Перейти в кабинет</a></p>
    </body>
    </html>
";
mail($user['email'], $userSubject, $userMessage, $headers);

// === ВАШИ РЕКВИЗИТЫ ===
$paymentDetails = [
    'card' => '2204 1201 1194 5289',
    'perevod' => '+7 (923) 501-06-24',         
    'bank' => 'Юмани',
    'phone' => '+7 (923) 501-06-24',
    'comment' => 'Оплата тарифа ' . $planName
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Запрос на оплату</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #1a1a1a; padding: 40px; border-radius: 20px; max-width: 500px; width: 100%; border: 1px solid #333; }
        h1 { text-align: center; color: #4facfe; }
        .info { background: #222; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .info p { margin: 8px 0; }
        .info strong { color: #4facfe; }
        .btn { display: inline-block; padding: 12px 30px; background: #4facfe; color: #fff; text-decoration: none; border-radius: 10px; width: 100%; text-align: center; box-sizing: border-box; }
        .btn:hover { opacity: 0.8; }
        .back { display: block; text-align: center; margin-top: 15px; color: #4facfe; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>📩 Запрос на оплату</h1>
        
        <div class="info">
            <p><strong>Тариф:</strong> <?= $planName ?></p>
            <p><strong>Сумма:</strong> <?= $amount ?> ₽</p>
            <p><strong>Статус:</strong> ⏳ Ожидает оплаты</p>
        </div>
        
        <h3>💳 Реквизиты для оплаты</h3>
        <div class="info">
            <p><strong>Карта:</strong> <?= $paymentDetails['card'] ?></p>
            <p><strong>Перевод:</strong> <?= $paymentDetails['phone'] ?></p>
            <p><strong>Банк:</strong> <?= $paymentDetails['bank'] ?></p>
            <p><strong>Телефон:</strong> <?= $paymentDetails['phone'] ?></p>
            <p><strong>Назначение:</strong> <?= $paymentDetails['comment'] ?></p>
        </div>
<p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 14px;">
    Банк получателя (Юмани) <strong>Получатель: Владимир Владимирович Е.</strong>
</p>
        <p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 14px;">
    После оплаты, напишите нам в Telegram <strong>@agentiipro</strong>
</p>
        <p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 14px;">
            или  напочту <strong>ejikovvladimir@yandex.ru</strong>
        </p>
        
        <a href="/cabinet.php" class="btn">⬅️ Вернуться в кабинет</a>
        <a href="/" class="back">На главную</a>
    </div>
</body>
</html>
