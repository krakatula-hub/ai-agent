<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/database.php';

$db = getDB();
$posts = $db->query("
    SELECT * FROM blog_posts 
    WHERE is_published = 1 
    ORDER BY created_at DESC
")->fetchAll();
?>

<style>
    .blog-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; padding: 20px 0; }
    .blog-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 25px; transition: all 0.3s; }
    .blog-card:hover { transform: translateY(-6px); border-color: rgba(79,172,254,0.3); background: rgba(255,255,255,0.05); }
    .blog-card .date { color: rgba(255,255,255,0.4); font-size: 13px; }
    .blog-card h2 { font-size: 22px; margin: 10px 0; }
    .blog-card h2 a { color: #fff; text-decoration: none; transition: color 0.3s; }
    .blog-card h2 a:hover { color: #4facfe; }
    .blog-card p { color: rgba(255,255,255,0.6); font-size: 15px; line-height: 1.6; }
    .blog-card .btn { display: inline-block; padding: 8px 20px; border: none; border-radius: 50px; background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; font-weight: 600; text-decoration: none; margin-top: 15px; transition: all 0.3s; }
    .blog-card .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(79,172,254,0.4); }
    .empty-blog { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.5); }
    .empty-blog .icon { font-size: 48px; margin-bottom: 15px; }
</style>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin: 20px 0;">
        <h1 style="font-size: 36px; font-weight: 700;">📝 <span class="gradient-text">Блог</span></h1>
        <?php if (isset($_SESSION['user_id']) && isAdmin($_SESSION['user_id'])): ?>
            <a href="/blog_admin.php" class="btn" style="background: #f39c12; padding:10px 24px; border-radius:50px; color:#fff; text-decoration:none;">✏️ Написать статью</a>
        <?php endif; ?>
    </div>
    <p style="color: rgba(255,255,255,0.6); font-size: 18px; margin-bottom: 30px;">
        Полезные статьи, новости и советы по использованию AI-агентов
    </p>

    <?php if (empty($posts)): ?>
        <div class="empty-blog">
            <div class="icon">📝</div>
            <h2>Статей пока нет</h2>
            <p>Скоро здесь появятся полезные материалы</p>
        </div>
    <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
                <div class="blog-card">
                    <div class="date"><?= date('d.m.Y', strtotime($post['created_at'])) ?></div>
                    <h2><a href="/blog_post.php?slug=<?= $post['slug'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <p><?= htmlspecialchars($post['excerpt'] ?: substr($post['content'], 0, 150) . '...') ?></p>
                    <a href="/blog_post.php?slug=<?= $post['slug'] ?>" class="btn">Читать →</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
