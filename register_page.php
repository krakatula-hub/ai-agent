<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
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
        <h1>📝 Регистрация</h1>
        <?php if (isset($_GET['error'])): ?>
            <div class="error">❌ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <form action="/register.php" method="POST">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Пароль" required />
            <input type="password" name="password_confirm" placeholder="Подтвердите пароль" required />
            <button type="submit">Зарегистрироваться</button>
        </form>
        <div class="link">
            Уже есть аккаунт? <a href="/login_page.php">Войти</a>
        </div>
        <div class="link">
            <a href="/">На главную</a>
        </div>
    </div>
</body>
</html>
