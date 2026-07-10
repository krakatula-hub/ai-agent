<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/database.php';

$db = getDB();
$cases = $db->query("
    SELECT * FROM cases 
    WHERE is_published = 1 
    ORDER BY created_at DESC
")->fetchAll();
?>

<style>
    .cases-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 30px; padding: 20px 0; }
    .case-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 25px; transition: all 0.3s; }
    .case-card:hover { transform: translateY(-6px); border-color: rgba(79,172,254,0.3); background: rgba(255,255,255,0.05); }
    .case-card .client { color: #4facfe; font-size: 14px; font-weight: 600; }
    .case-card .industry { display: inline-block; padding: 2px 12px; background: rgba(79,172,254,0.1); color: #4facfe; border-radius: 12px; font-size: 12px; margin-top: 8px; }
    .case-card h2 { font-size: 22px; margin: 10px 0; }
    .case-card h2 a { color: #fff; text-decoration: none; transition: color 0.3s; }
    .case-card h2 a:hover { color: #4facfe; }
    .case-card p { color: rgba(255,255,255,0.6); font-size: 15px; line-height: 1.6; }
    .case-card .btn { display: inline-block; padding: 8px 20px; border: none; border-radius: 50px; background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; font-weight: 600; text-decoration: none; margin-top: 15px; transition: all 0.3s; }
    .case-card .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(79,172,254,0.4); }
    .empty-cases { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.5); }
    .empty-cases .icon { font-size: 48px; margin-bottom: 15px; }
    .stats { display: flex; gap: 40px; justify-content: center; flex-wrap: wrap; margin: 30px 0; }
    .stats .stat { text-align: center; }
    .stats .stat .number { font-size: 36px; font-weight: 800; background: linear-gradient(135deg, #4facfe, #00f2fe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .stats .stat .label { display: block; color: rgba(255,255,255,0.5); font-size: 14px; }
</style>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin: 20px 0;">
        <h1 style="font-size: 36px; font-weight: 700;">🏆 <span class="gradient-text">Кейсы</span></h1>
        <?php if (isset($_SESSION['user_id']) && isAdmin($_SESSION['user_id'])): ?>
            <a href="/cases_admin.php" class="btn" style="background: #f39c12; padding:10px 24px; border-radius:50px; color:#fff; text-decoration:none;">✏️ Добавить кейс</a>
        <?php endif; ?>
    </div>
    <p style="color: rgba(255,255,255,0.6); font-size: 18px; margin-bottom: 10px;">
        Реальные примеры использования AI-агентов в бизнесе
    </p>

    <?php if (empty($cases)): ?>
        <div class="empty-cases">
            <div class="icon">🏆</div>
            <h2>Кейсы пока не добавлены</h2>
            <p>Скоро здесь появятся примеры использования</p>
        </div>
    <?php else: ?>
        <div class="stats">
            <div class="stat">
                <div class="number"><?= count($cases) ?></div>
                <div class="label">Решённых задач</div>
            </div>
            <div class="stat">
                <div class="number"><?= array_sum(array_column($cases, 'views')) ?></div>
                <div class="label">Всего просмотров</div>
            </div>
        </div>
        
        <div class="cases-grid">
            <?php foreach ($cases as $case): ?>
                <div class="case-card">
                    <div class="client">🏢 <?= htmlspecialchars($case['client'] ?? 'Компания') ?></div>
                    <?php if ($case['industry']): ?>
                        <span class="industry"><?= htmlspecialchars($case['industry']) ?></span>
                    <?php endif; ?>
                    <h2><a href="/case.php?slug=<?= $case['slug'] ?>"><?= htmlspecialchars($case['title']) ?></a></h2>
                    <p><?= htmlspecialchars($case['description']) ?></p>
                    <a href="/case.php?slug=<?= $case['slug'] ?>" class="btn">Подробнее →</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
