<?php
session_start();

require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header('Location: /login_page.php');
    exit;
}

require_once 'includes/header.php';
require_once 'includes/database.php';

$db = getDB();
$message = '';
$error = '';

// Обработка добавления статьи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        
        if (empty($title) || empty($content)) {
            $error = 'Заполните название и содержание';
        } else {
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]/', '-', $title), '-'));
            }
            
            // Проверка уникальности slug
            $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, author) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $content, $excerpt, $_SESSION['email'] ?? 'Admin']);
            
            $message = '✅ Статья опубликована!';
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $message = '✅ Статья удалена';
    }
    
    if ($action === 'toggle_publish') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("UPDATE blog_posts SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        $message = '✅ Статус обновлён';
    }
}

$posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
?>

<style>
    .admin-section { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 25px; margin-bottom: 30px; }
    .admin-section h2 { color: #4facfe; margin-bottom: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; background: rgba(0,0,0,0.3); color: #fff; }
    .form-group textarea { min-height: 200px; resize: vertical; }
    .btn { padding: 10px 24px; border: none; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; text-decoration: none; }
    .btn-primary { background: #4facfe; }
    .btn-success { background: #2ecc71; }
    .btn-danger { background: #ff4757; }
    .btn-sm { padding: 5px 12px; font-size: 12px; }
    .btn:hover { opacity: 0.8; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
    th { color: #4facfe; }
    .status-badge { padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .status-published { background: #2ecc71; color: #fff; }
    .status-draft { background: #f39c12; color: #fff; }
    .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .message-success { background: rgba(46,204,113,0.1); color: #2ecc71; border: 1px solid rgba(46,204,113,0.2); }
    .message-error { background: rgba(255,71,87,0.1); color: #ff4757; border: 1px solid rgba(255,71,87,0.2); }
</style>

<div class="container">
    <h1>📝 Управление блогом</h1>
    <a href="/blog.php" style="color:#4facfe;">← На главную блога</a>
    
    <?php if ($message): ?>
        <div class="message message-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message message-error">❌ <?= $error ?></div>
    <?php endif; ?>
    
    <!-- Создание статьи -->
    <div class="admin-section">
        <h2>✏️ Новая статья</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label>Название *</label>
                <input type="text" name="title" placeholder="Заголовок статьи" required>
            </div>
            <div class="form-group">
                <label>URL (slug)</label>
                <input type="text" name="slug" placeholder="avtomaticheskaya-generatsiya">
            </div>
            <div class="form-group">
                <label>Краткое описание</label>
                <input type="text" name="excerpt" placeholder="Краткое описание для анонса">
            </div>
            <div class="form-group">
                <label>Содержание *</label>
                <textarea name="content" placeholder="Текст статьи..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">📤 Опубликовать</button>
        </form>
    </div>
    
    <!-- Список статей -->
    <div class="admin-section">
        <h2>📋 Все статьи</h2>
        <?php if (empty($posts)): ?>
            <p style="color: rgba(255,255,255,0.5);">Нет статей</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Статус</th>
                        <th>Просмотры</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td><?= htmlspecialchars($post['title']) ?></td>
                            <td><span class="status-badge status-<?= $post['is_published'] ? 'published' : 'draft' ?>"><?= $post['is_published'] ? '✅ Опубликован' : '📝 Черновик' ?></span></td>
                            <td><?= $post['views'] ?></td>
                            <td><?= date('d.m.Y', strtotime($post['created_at'])) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_publish">
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">🔄</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить статью?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
