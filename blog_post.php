<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/database.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: /blog.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: /blog.php');
    exit;
}

// Увеличиваем счётчик просмотров
$stmt = $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
$stmt->execute([$post['id']]);
?>

<style>
    .blog-post { max-width: 800px; margin: 0 auto; padding: 20px 0; }
    .blog-post .date { color: rgba(255,255,255,0.4); font-size: 14px; }
    .blog-post h1 { font-size: 40px; font-weight: 800; margin: 15px 0; line-height: 1.2; }
    .blog-post .content { color: rgba(255,255,255,0.8); font-size: 18px; line-height: 1.8; margin: 30px 0; }
    .blog-post .content p { margin-bottom: 20px; }
    .blog-post .content h2 { color: #4facfe; margin: 30px 0 15px; }
    .blog-post .content ul { padding-left: 20px; margin: 15px 0; }
    .blog-post .content ul li { margin-bottom: 8px; }
    .back-btn { display: inline-block; padding: 10px 24px; background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s; }
    .back-btn:hover { background: rgba(255,255,255,0.1); }
    .views { color: rgba(255,255,255,0.3); font-size: 14px; margin-top: 20px; }
</style>

<div class="container">
    <div class="blog-post">
        <a href="/blog.php" class="back-btn">← Назад к списку</a>
        
        <div class="date"><?= date('d.m.Y', strtotime($post['created_at'])) ?></div>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
        
        <div class="views">👁️ <?= $post['views'] ?> просмотров</div>
    </div>
     <!-- ===== РСЯ БЛОК 2 (под статьёй) ===== -->
<div style="max-width: 800px; margin: 30px auto; padding: 0 20px;">
    <div id="yandex_rtb_2"></div>
    <script>
        window.yaContextCb = window.yaContextCb || [];
        window.yaContextCb.push(() => {
            Ya.Context.AdvManager.render({
                blockId: 'R-A-19575210-2',
                renderTo: 'yandex_rtb_2',
                type: 'feed'
            });
        });
    </script>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>
