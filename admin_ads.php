<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login_page.php');
    exit;
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

if (!isAdmin($_SESSION['user_id'])) {
    header('Location: /cabinet.php');
    exit;
}

$db = getDB();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve_ad') {
        $orderId = $_POST['order_id'] ?? 0;
        $stmt = $db->prepare("UPDATE ad_orders SET status = 'approved' WHERE id = ?");
        $stmt->execute([$orderId]);
        header('Location: /admin_ads.php?success=Заявка одобрена');
        exit;
    }
    
    if ($action === 'reject_ad') {
        $orderId = $_POST['order_id'] ?? 0;
        $stmt = $db->prepare("UPDATE ad_orders SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$orderId]);
        header('Location: /admin_ads.php?success=Заявка отклонена');
        exit;
    }
    
    if ($action === 'create_banner') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $link = $_POST['link'] ?? '';
        $position = $_POST['position'] ?? 'sidebar';
        $days = (int)($_POST['days'] ?? 7);
        
        $stmt = $db->prepare("
            INSERT INTO ad_banners (title, content, link, position, end_date) 
            VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL ? DAY))
        ");
        $stmt->execute([$title, $content, $link, $position, $days]);
        header('Location: /admin_ads.php?success=Баннер создан');
        exit;
    }
    
    if ($action === 'delete_banner') {
        $bannerId = $_POST['banner_id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM ad_banners WHERE id = ?");
        $stmt->execute([$bannerId]);
        header('Location: /admin_ads.php?success=Баннер удалён');
        exit;
    }
}

$orders = $db->query("
    SELECT ao.*, u.email as user_email 
    FROM ad_orders ao
    LEFT JOIN users u ON ao.user_id = u.id
    ORDER BY ao.created_at DESC
")->fetchAll();

$banners = $db->query("SELECT * FROM ad_banners ORDER BY priority DESC, created_at DESC")->fetchAll();
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Управление рекламой</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: Arial; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 25px; margin-bottom: 30px; }
        .section h2 { color: #4facfe; margin-bottom: 20px; }
        .card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 15px; margin-bottom: 15px; }
        .card .header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .status-pending { background: #f39c12; color: #fff; padding: 3px 12px; border-radius: 20px; font-size: 12px; }
        .status-approved { background: #2ecc71; color: #fff; padding: 3px 12px; border-radius: 20px; font-size: 12px; }
        .status-rejected { background: #ff4757; color: #fff; padding: 3px 12px; border-radius: 20px; font-size: 12px; }
        .status-active { background: #4facfe; color: #fff; padding: 3px 12px; border-radius: 20px; font-size: 12px; }
        .btn { padding: 8px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; }
        .btn-success { background: #2ecc71; color: #fff; }
        .btn-danger { background: #ff4757; color: #fff; }
        .btn-primary { background: #4facfe; color: #fff; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .btn:hover { opacity: 0.8; }
        .success-msg { color: #2ecc71; padding: 10px; background: rgba(46,204,113,0.1); border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: #4facfe; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; padding: 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; background: rgba(0,0,0,0.3); color: #fff; 
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>📢 Управление рекламой</h1>
        <a href="/admin.php" style="color:#4facfe;">← Назад в админ-панель</a>
        
        <?php if ($success): ?>
            <div class="success-msg">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <!-- Заявки -->
        <div class="section">
            <h2>📩 Заявки на рекламу</h2>
            <?php if (empty($orders)): ?>
                <p style="color: rgba(255,255,255,0.5);">Нет заявок</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="card">
                        <div class="header">
                            <div>
                                <strong><?= htmlspecialchars($order['company']) ?></strong>
                                <span class="status-<?= $order['status'] ?>"><?= $order['status'] ?></span>
                            </div>
                            <span style="color: rgba(255,255,255,0.5); font-size:14px;">
                                <?= htmlspecialchars($order['email'] ?? $order['user_email']) ?>
                            </span>
                        </div>
                        <p style="margin: 8px 0; color: rgba(255,255,255,0.7);">
                            <strong>Текст:</strong> <?= nl2br(htmlspecialchars($order['ad_text'])) ?>
                        </p>
                        <p style="color: rgba(255,255,255,0.5); font-size:14px;">
                            <strong>Тип:</strong> <?= $order['ad_type'] ?> |
                            <strong>Бюджет:</strong> <?= $order['budget'] ?> ₽ |
                            <strong>Период:</strong> <?= $order['period'] ?> дней
                        </p>
                        
                        <?php if ($order['status'] === 'pending'): ?>
                            <div style="margin-top: 10px; display: flex; gap: 10px;">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="approve_ad">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">✅ Одобрить</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="reject_ad">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">❌ Отклонить</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Создание баннера -->
        <div class="section">
            <h2>🖼️ Создать баннер</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create_banner">
                <div class="form-row">
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" name="title" placeholder="Реклама компании" required>
                    </div>
                    <div class="form-group">
                        <label>Ссылка</label>
                        <input type="url" name="link" placeholder="https://example.com">
                    </div>
                </div>
                <div class="form-group">
                    <label>Текст баннера</label>
                    <textarea name="content" placeholder="Текст рекламного объявления" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Позиция</label>
                        <select name="position">
                            <option value="header">Шапка</option>
                            <option value="sidebar">Сайдбар</option>
                            <option value="footer">Подвал</option>
                            <option value="between">Между блоками</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дней размещения</label>
                        <input type="number" name="days" value="7" min="1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">➕ Создать баннер</button>
            </form>
        </div>
        
        <!-- Активные баннеры -->
        <div class="section">
            <h2>🖼️ Активные баннеры</h2>
            <?php if (empty($banners)): ?>
                <p style="color: rgba(255,255,255,0.5);">Нет активных баннеров</p>
            <?php else: ?>
                <table>
                    <thead><tr><th>Название</th><th>Позиция</th><th>До</th><th>Просмотры</th><th>Клики</th><th>Действия</th></tr></thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['title']) ?></td>
                                <td><?= $b['position'] ?></td>
                                <td><?= $b['end_date'] ? date('d.m.Y', strtotime($b['end_date'])) : '∞' ?></td>
                                <td><?= $b['views'] ?></td>
                                <td><?= $b['clicks'] ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_banner">
                                        <input type="hidden" name="banner_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
