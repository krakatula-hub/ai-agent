<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login_page.php');
    exit;
}

// Подключаем файлы с функциями
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/logger.php';

// Проверяем, что функция isAdmin() существует
if (!function_exists('isAdmin')) {
    die('❌ Ошибка: функция isAdmin() не найдена в functions.php');
}

if (!isAdmin($_SESSION['user_id'])) {
    header('Location: /cabinet.php');
    exit;
}

// === ОБРАБОТКА POST-ЗАПРОСОВ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // === АКТИВАЦИЯ ОПЛАТЫ ===
    if ($action === 'approve_payment') {
        $requestId = $_POST['request_id'] ?? 0;
        $userId = $_POST['user_id'] ?? 0;
        $plan = $_POST['plan'] ?? '';
        
        if ($userId && $plan) {
            $days = PLAN_DAYS[$plan];
            $db = getDB();
            
            // Активируем подписку
            $stmt = $db->prepare("UPDATE users SET plan = ?, subscription_end = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id = ?");
            $stmt->execute([$plan, $days, $userId]);
            
            // Обновляем статус заявки
            $stmt = $db->prepare("UPDATE payment_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$requestId]);
            
            header('Location: /admin.php?success=Подписка активирована');
            exit;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? 0;
    $newPlan = $_POST['plan'] ?? '';
    
    // Смена тарифа
    if ($action === 'change_plan' && $userId && $newPlan) {
        updateUserPlan($userId, $newPlan);
        $user = getUserById($userId);
        logEvent($userId, 'plan_change', $newPlan, "Администратор сменил тариф на {$newPlan}");
        header('Location: /admin.php?success=План обновлён');
        exit;
    }
    
    // Отметить все уведомления
    if ($action === 'mark_read') {
        markAllAsRead();
        header('Location: /admin.php');
        exit;
    }
    
    // Одобрение оплаты
    if ($action === 'approve_payment') {
        $requestId = $_POST['request_id'] ?? 0;
        $userId = $_POST['user_id'] ?? 0;
        $plan = $_POST['plan'] ?? '';
        
        if ($userId && $plan) {
            $days = PLAN_DAYS[$plan];
            $db = getDB();
            
            $stmt = $db->prepare("UPDATE users SET plan = ?, subscription_end = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id = ?");
            $stmt->execute([$plan, $days, $userId]);
            
            $stmt = $db->prepare("UPDATE payment_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$requestId]);
            
            logEvent($userId, 'plan_change', $plan, "Одобрено администратором");
            
            header('Location: /admin.php?success=Подписка активирована');
            exit;
        }
    }
}

// === ПОЛУЧАЕМ ДАННЫЕ ===
$stats = getStats();
$notifications = getUnreadNotifications();
$allUsers = getAllUsers(20);

$db = getDB();
$requests = $db->query("
    SELECT pr.*, u.email 
    FROM payment_requests pr
    JOIN users u ON pr.user_id = u.id
    WHERE pr.status = 'pending'
    ORDER BY pr.created_at DESC
")->fetchAll();

$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Админ-панель</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 1px solid #333; margin-bottom: 30px; }
        .header h1 { color: #4facfe; }
        .header a { color: #4facfe; text-decoration: none; margin-left: 20px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: #1a1a1a; padding: 20px; border-radius: 12px; border: 1px solid #333; text-align: center; }
        .stat-card .number { font-size: 30px; font-weight: 800; color: #4facfe; }
        .stat-card .label { color: #666; font-size: 14px; }
        .section { background: #1a1a1a; padding: 25px; border-radius: 12px; border: 1px solid #333; margin-bottom: 30px; }
        .section h2 { margin-bottom: 20px; color: #4facfe; font-size: 20px; display: flex; align-items: center; gap: 10px; }
        .badge { background: #ff4757; color: #fff; padding: 2px 12px; border-radius: 50%; font-size: 14px; }
        .success { color: #2ecc71; padding: 10px; background: rgba(46,204,113,0.1); border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        th { color: #4facfe; }
        .btn { padding: 6px 16px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-success { background: #2ecc71; color: #fff; }
        .btn-primary { background: #4facfe; color: #fff; }
        .btn-small { padding: 4px 12px; font-size: 12px; }
        .btn:hover { opacity: 0.8; }
        .plan-badge { background: #4facfe; padding: 2px 10px; border-radius: 12px; font-size: 12px; color: #fff; }
        .notification-item { padding: 10px; background: #222; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid #4facfe; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Админ-панель</h1>
            <div>
                <a href="/cabinet.php">👤 Кабинет</a>
                <a href="/logout.php">🚪 Выход</a>
                <a href="/admin_ads.php" class="btn" style="background: #f39c12;">📢 Реклама</a>
                <a href="/blog_admin.php" class="btn" style="background: #9b59b6;">📝 Управление блогом</a>
            </div>
<!-- ===== ЗАЯВКИ НА ОПЛАТУ ===== -->
<div class="section">
    <h2>📩 Заявки на оплату</h2>
    <?php
    $db = getDB();
    $requests = $db->query("
        SELECT pr.*, u.email 
        FROM payment_requests pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.status = 'pending'
        ORDER BY pr.created_at DESC
    ")->fetchAll();
    
    if (empty($requests)): ?>
        <p style="color: #666;">✅ Нет новых заявок</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Пользователь</th><th>Тариф</th><th>Сумма</th><th>Дата</th><th>Действия</th></tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><span class="plan-badge"><?= $r['plan'] ?></span></td>
                    <td><?= $r['amount'] ?> ₽</td>
                    <td><?= $r['created_at'] ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="approve_payment">
                            <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="user_id" value="<?= $r['user_id'] ?>">
                            <input type="hidden" name="plan" value="<?= $r['plan'] ?>">
                            <button type="submit" class="btn btn-success btn-small">✅ Активировать</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

        </div>
        
        <?php if ($success): ?>
            <div class="success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <!-- Статистика -->
        <div class="stats">
            <div class="stat-card"><div class="number"><?= $stats['total'] ?></div><div class="label">Всего</div></div>
            <div class="stat-card"><div class="number"><?= $stats['free'] ?></div><div class="label">Бесплатные</div></div>
            <div class="stat-card"><div class="number"><?= $stats['pro'] ?></div><div class="label">PRO</div></div>
            <div class="stat-card"><div class="number"><?= $stats['business'] ?></div><div class="label">Бизнес</div></div>
            <div class="stat-card"><div class="number"><?= $stats['today'] ?></div><div class="label">Новых сегодня</div></div>
        </div>
        
        <!-- Заявки на оплату -->
        <div class="section">
            <h2>📩 Заявки на оплату <span class="badge"><?= count($requests) ?></span></h2>
            <?php if (empty($requests)): ?>
                <p style="color: #666;">✅ Нет новых заявок</p>
            <?php else: ?>
                <table>
                    <thead><tr><th>ID</th><th>Пользователь</th><th>Тариф</th><th>Сумма</th><th>Дата</th><th>Действия</th></tr></thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><span class="plan-badge"><?= $r['plan'] ?></span></td>
                            <td><?= $r['amount'] ?> ₽</td>
                            <td><?= $r['created_at'] ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="approve_payment">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $r['user_id'] ?>">
                                    <input type="hidden" name="plan" value="<?= $r['plan'] ?>">
                                    <button type="submit" class="btn btn-success btn-small">✅ Одобрить</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Уведомления -->
        <div class="section">
            <h2>📨 Уведомления <span class="badge"><?= count($notifications) ?></span></h2>
            <?php if (empty($notifications)): ?>
                <p style="color: #666;">✅ Нет новых уведомлений</p>
            <?php else: ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="notification-item">
                        <strong><?= htmlspecialchars($n['email']) ?></strong>
                        <span class="plan-badge"><?= $n['plan'] ?? '—' ?></span>
                        <span style="color: #666; font-size: 12px; margin-left: 10px;"><?= $n['created_at'] ?></span>
                        <div style="font-size: 14px; color: #aaa;"><?= htmlspecialchars($n['message'] ?? $n['event_type']) ?></div>
                    </div>
                <?php endforeach; ?>
                <form method="POST" style="margin-top: 15px;">
                    <input type="hidden" name="action" value="mark_read">
                    <button type="submit" class="btn btn-primary">✓ Отметить все</button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Пользователи -->
        <div class="section">
            <h2>👥 Пользователи</h2>
            <table>
                <thead><tr><th>ID</th><th>Email</th><th>Тариф</th><th>Подписка до</th><th>Действия</th></tr></thead>
                <tbody>
                    <?php foreach ($allUsers as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 5px; align-items: center;">
                                <input type="hidden" name="action" value="change_plan">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="plan">
                                    <option value="free" <?= $u['plan'] === 'free' ? 'selected' : '' ?>>Бесплатный</option>
                                    <option value="pro" <?= $u['plan'] === 'pro' ? 'selected' : '' ?>>PRO</option>
                                    <option value="business" <?= $u['plan'] === 'business' ? 'selected' : '' ?>>Бизнес</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-small">Изменить</button>
                            </form>
                        </td>
                        <td><?= $u['subscription_end'] ? date('d.m.Y', strtotime($u['subscription_end'])) : '—' ?></td>
                        <td><a href="/cabinet.php?user=<?= $u['id'] ?>" class="btn btn-primary btn-small">👁</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
