<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Оплата прошла успешно</title>
    <style>
        body { background: #0a0a0a; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column; font-family: sans-serif; margin: 0; }
        h1 { color: #38ef7d; }
        a { color: #4facfe; text-decoration: none; }
        .btn { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; border-radius: 10px; margin-top: 20px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>✅ Оплата прошла успешно!</h1>
    <p>Подписка активирована. Перейдите в <a href="/cabinet.php">личный кабинет</a></p>
    <a href="/cabinet.php" class="btn">Перейти в кабинет</a>
    <script>
        if (typeof ym !== 'undefined') ym('reachGoal', 'payment_success');
    </script>
</body>
</html>