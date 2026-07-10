<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/database.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: /cases.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM cases WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$case = $stmt->fetch();

if (!$case) {
    header('Location: /cases.php');
    exit;
}

// Увеличиваем счётчик просмотров
$stmt = $db->prepare("UPDATE cases SET views = views + 1 WHERE id = ?");
$stmt->execute([$case['id']]);
?>

<style>
    .case-page { max-width: 800px; margin: 0 auto; padding: 20px 0; }
    .case-page .meta { display: flex; gap: 20px; flex-wrap: wrap; margin: 15px 0; color: rgba(255,255,255,0.5); }
    .case-page .meta span { display: flex; align-items: center; gap: 6px; }
    .case-page h1 { font-size: 40px; font-weight: 800; margin: 15px 0; line-height: 1.2; }
    .case-page .section { margin: 30px 0; }
    .case-page .section h2 { color: #4facfe; margin-bottom: 10px; font-size: 22px; }
    .case-page .section p { color: rgba(255,255,255,0.8); font-size: 17px; line-height: 1.8; }
    .case-page .section ul { padding-left: 20px; margin: 10px 0; }
    .case-page .section ul li { color: rgba(255,255,255,0.8); margin-bottom: 8px; }
    .case-page .back-btn { display: inline-block; padding: 10px 24px; background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s; }
    .case-page .back-btn:hover { background: rgba(255,255,255,0.1); }
    .case-page .views { color: rgba(255,255,255,0.3); font-size: 14px; margin-top: 30px; }
    .case-page .tags { display: flex; gap: 10px; flex-wrap: wrap; margin: 15px 0; }
    .case-page .tag { padding: 4px 14px; background: rgba(79,172,254,0.1); color: #4facfe; border-radius: 20px; font-size: 13px; }
</style>

<div class="container">
    <div class="case-page">
        <a href="/cases.php" class="back-btn">← Назад к кейсам</a>
        
        <div class="meta">
            <span>🏢 <?= htmlspecialchars($case['client'] ?? 'Компания') ?></span>
            <?php if ($case['industry']): ?>
                <span>📂 <?= htmlspecialchars($case['industry']) ?></span>
            <?php endif; ?>
            <span>📅 <?= date('d.m.Y', strtotime($case['created_at'])) ?></span>
        </div>
        
        <?php if ($case['technologies']): ?>
            <div class="tags">
                <?php foreach (explode(',', $case['technologies']) as $tech): ?>
                    <span class="tag"><?= htmlspecialchars(trim($tech)) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <h1><?= htmlspecialchars($case['title']) ?></h1>
        
        <div class="section">
            <h2>📋 Описание</h2>
            <p><?= nl2br(htmlspecialchars($case['description'])) ?></p>
        </div>
        
        <?php if ($case['challenge']): ?>
            <div class="section">
                <h2>🚧 Задача</h2>
                <p><?= nl2br(htmlspecialchars($case['challenge'])) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($case['solution']): ?>
            <div class="section">
                <h2>💡 Решение</h2>
                <p><?= nl2br(htmlspecialchars($case['solution'])) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($case['result']): ?>
            <div class="section">
                <h2>📊 Результат</h2>
                <p><?= nl2br(htmlspecialchars($case['result'])) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="views">👁️ <?= $case['views'] ?> просмотров</div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
