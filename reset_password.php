<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    die('❌ Токен не указан');
}

// Проверяем токен
$db = getDB();
$stmt = $db->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    die('❌ Ссылка недействительна или истекла. <a href="/forgot_password.php">Запросить новую</a>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (empty($password) || strlen($password) < 6) {
        $error = 'Пароль должен быть минимум 6 символов';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } else {
        // Обновляем пароль
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $reset['user_id']]);
        
        // Удаляем использованный токен
        $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        
        $success = '✅ Пароль успешно изменён! <a href="/login_page.php">Войти</a>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Сброс пароля</title>
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
        <h1>🔐 Сброс пароля</h1>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="password" name="password" placeholder="Новый пароль (мин. 6 символов)" required />
                <input type="password" name="confirm" placeholder="Подтвердите пароль" required />
                <button type="submit">Сбросить пароль</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
