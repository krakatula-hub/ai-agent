<!DOCTYPE html>
<html>
<head>
    <title>Вход в систему</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #1a1a1a; padding: 40px; border-radius: 20px; max-width: 400px; width: 100%; border: 1px solid #333; }
        h1 { text-align: center; color: #4facfe; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #333; border-radius: 10px; background: #0a0a0a; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #4facfe; border: none; border-radius: 10px; color: #fff; font-weight: bold; cursor: pointer; }
        button:hover { opacity: 0.8; }
        .error { color: #ff4757; text-align: center; margin-top: 10px; }
        .link { text-align: center; margin-top: 15px; }
        .link a { color: #4facfe; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔑 Вход</h1>
        <form action="/login.php" method="POST">
            <input type="email" name="email" placeholder="Email" value="admin@test.com" required />
            <input type="password" name="password" placeholder="Пароль" value="admin123" required />
            <button type="submit">Войти</button>
        </form>
        <?php if (isset($_GET['error'])): ?>
            <div class="error">❌ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <div class="link">
            <a href="/register_page.php">📝 Зарегистрироваться</a>
        </div>
        <div class="link">
    <a href="/forgot_password.php">🔑 Забыли пароль?</a>
       </div>
        <div class="link">
            <a href="/">🏠 На главную</a>
        </div>
    </div>
</body>
</html>
