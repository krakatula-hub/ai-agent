<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

error_log("=== forgot_password.php ВЫЗВАН ===");
error_log("Метод: " . $_SERVER['REQUEST_METHOD']);
error_log("POST: " . print_r($_POST, true));

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    error_log("Email из POST: " . $email);
    
    if (empty($email)) {
        $error = 'Введите email';
        error_log("❌ Email пустой");
    } else {
        $user = getUserByEmail($email);
        error_log("Пользователь найден: " . ($user ? '✅' : '❌'));
        
        if (!$user) {
            $error = 'Пользователь с таким email не найден';
            error_log("❌ Пользователь не найден");
        } else {
            // Генерируем токен
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            error_log("Токен создан: " . $token);
            
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expires]);
            error_log("✅ Токен сохранён в БД");
            
            // === ОТПРАВКА ПИСЬМА ===
            $resetLink = APP_URL . "/reset_password.php?token=" . $token;
            $subject = "Восстановление пароля на AI Agent";
            $body = "
                <html>
                <head><title>Восстановление пароля</title></head>
                <body>
                    <h2>Восстановление пароля</h2>
                    <p>Вы запросили восстановление пароля. Перейдите по ссылке:</p>
                    <p><a href='{$resetLink}'>Сбросить пароль</a></p>
                    <p>Ссылка действительна 1 час.</p>
                </body>
                </html>
            ";
            $headers = "From: ejikovvladimir@yandex.ru\r\n";
            $headers .= "Content-Type: text/html; charset=utf-8\r\n";
            
            error_log("Попытка отправки письма на: " . $email);
            $result = mail($email, $subject, $body, $headers);
            error_log("Результат отправки: " . ($result ? '✅' : '❌'));
            
            if ($result) {
                $message = '✅ Ссылка для сброса пароля отправлена на ваш email';
                error_log("✅ Письмо отправлено успешно!");
            } else {
                $error = '❌ Не удалось отправить письмо. Попробуйте позже.';
                error_log("❌ Ошибка отправки письма!");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Восстановление пароля</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #1a1a1a; padding: 40px; border-radius: 20px; max-width: 400px; width: 100%; border: 1px solid #333; }
        h1 { text-align: center; color: #4facfe; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #333; border-radius: 10px; background: #0a0a0a; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #4facfe; border: none; border-radius: 10px; color: #fff; font-weight: bold; cursor: pointer; }
        button:hover { opacity: 0.8; }
        .success { color: #2ecc71; text-align: center; margin-top: 10px; }
        .error { color: #ff4757; text-align: center; margin-top: 10px; }
        .link { text-align: center; margin-top: 15px; }
        .link a { color: #4facfe; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔑 Восстановление пароля</h1>
        
        <?php if ($message): ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Ваш email" required />
            <button type="submit">Отправить ссылку</button>
        </form>
        
        <div class="link">
            <a href="/login_page.php">← Вспомнили пароль? Войти</a>
        </div>
    </div>
</body>
</html>
