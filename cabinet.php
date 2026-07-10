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
if (!$user) {
    session_destroy();
    header('Location: /login_page.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Личный кабинет</title>
    <style>
    body { background: #0a0a0a; color: #fff; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 15px; }
    .card { background: #1a1a1a; padding: 30px; border-radius: 20px; max-width: 600px; width: 100%; border: 1px solid #333; box-sizing: border-box; }
    h1 { text-align: center; color: #4facfe; font-size: 28px; margin-top: 0; }
    .info { margin: 20px 0; padding: 15px; background: #222; border-radius: 10px; }
    .info-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #333; flex-wrap: wrap; gap: 5px; }
    .info-item:last-child { border-bottom: none; }
    .label { color: rgba(255,255,255,0.6); }
    .value { font-weight: 600; word-break: break-word; }
    .btn { display: inline-block; padding: 10px 20px; border: none; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.3s; flex: 1; min-width: 120px; }
    .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    .btn-primary { background: #4facfe; }
    .btn-success { background: #2ecc71; }
    .btn-danger { background: #ff4757; }
    .btn-warning { background: #f39c12; }
    .btn-purple { background: linear-gradient(135deg, #7c3aed, #4facfe); }
    .btn-orange { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
    .actions .btn { flex: 1 1 auto; min-width: 120px; }
    .plan-badge { display: inline-block; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .plan-free { background: rgba(255,255,255,0.1); color: #aaa; }
    .plan-pro { background: rgba(79,172,254,0.2); color: #4facfe; }
    .plan-business { background: rgba(46,204,113,0.2); color: #2ecc71; }
    @media (max-width: 480px) {
        .card { padding: 20px; }
        h1 { font-size: 22px; }
        .info-item { flex-direction: column; align-items: flex-start; gap: 2px; }
        .info-item .value { width: 100%; }
        .btn { min-width: 100%; }
        .actions { flex-direction: column; }
        .actions .btn { width: 100%; }
    }
</style>
</head>
<body>
    <div class="card">
        <h1>🧑‍💻 Личный кабинет</h1>
        
        <div class="info">
            <div class="info-item">
                <span class="label">Email</span>
                <span class="value"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Тариф</span>
                <span class="value"><?= getPlanName($user['plan']) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Подписка до</span>
                <span class="value"><?= $user['subscription_end'] ? date('d.m.Y', strtotime($user['subscription_end'])) : '—' ?></span>
            </div>
            <div class="info-item">
                <span class="label">Сообщений сегодня</span>
                <span class="value"><?= $user['messages_today'] ?> / <?= PLAN_LIMITS[$user['plan']] ?></span>
            </div>
<!-- ===== КНОПКИ ЗАПРОСА ОПЛАТЫ ===== -->
         <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
    <?php if ($user['plan'] !== 'pro'): ?>
        <a href="/request_payment.php?plan=pro" class="btn" style="background: #4facfe;">📩 Запросить PRO (7 990 ₽)</a>
    <?php endif; ?>
    
    <?php if ($user['plan'] !== 'business'): ?>
        <a href="/request_payment.php?plan=business" class="btn" style="background: #f39c12;">📩 Запросить Бизнес (15 000 ₽)</a>
    <?php endif; ?>
</div>
        </div>

        <!-- ===== КНОПКИ ЗАПРОСА ОПЛАТЫ ===== -->
<a href="/chat_common.php" class="btn" style="background: linear-gradient(135deg, #f39c12, #e67e22);">💬 Общий чат</a>
        <?php if ($user['plan'] !== 'free' || $user['messages_today'] < PLAN_LIMITS['free']): ?>
    <a href="/chat_openwebui.php" class="btn" style="background: linear-gradient(135deg, #7c3aed, #4facfe);">💬 Перейти в AI-агента</a>
<?php else: ?>
    <a href="/chat_openwebui.php" class="btn" style="background: #ff4757; opacity:0.6;">🔒 Чат недоступен</a>
<?php endif; ?>
        <!-- Обычные кнопки -->
        <div class="actions">
            <a href="/" class="btn btn-primary">🏠 На главную</a>
            <?php if (isAdmin($_SESSION['user_id'])): ?>
<a href="/agent_select.php" class="btn" style="background: linear-gradient(135deg, #7c3aed, #4facfe);">🤖 Выбрать AI-агента</a>
<a href="/order_agent.php" class="btn" style="background: linear-gradient(135deg, #f39c12, #e67e22);">📝 Заказать AI-агента</a>
                <a href="/admin.php" class="btn" style="background: #9b59b6;">📊 Админ-панель</a>
            <?php endif; ?>
            <a href="/logout.php" class="btn btn-danger">🚪 Выйти</a>
        </div>
    </div>
</body>
</html>
