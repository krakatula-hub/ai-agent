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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $challenge = trim($_POST['challenge'] ?? '');
        $solution = trim($_POST['solution'] ?? '');
        $result = trim($_POST['result'] ?? '');
        $client = trim($_POST['client'] ?? '');
        $industry = trim($_POST['industry'] ?? '');
        $technologies = trim($_POST['technologies'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        
        if (empty($title) || empty($description)) {
            $error = 'Заполните название и описание';
        } else {
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]/', '-', $title), '-'));
            }
            
            $stmt = $db->prepare("SELECT id FROM cases WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            $stmt = $db->prepare("INSERT INTO cases (title, slug, description, challenge, solution, result, client, industry, technologies) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $description, $challenge, $solution, $result, $client, $industry, $technologies]);
            $message = '✅ Кейс добавлен!';
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM cases WHERE id = ?");
        $stmt->execute([$id]);
        $message = '✅ Кейс удалён';
    }
    
    if ($action === 'toggle_publish') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("UPDATE cases SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        $message = '✅ Статус обновлён';
    }
}

$cases = $db->query("SELECT * FROM cases ORDER BY created_at DESC")->fetchAll();
?>

<style>
    .admin-section { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 25px; margin-bottom: 30px; }
    .admin-section h2 { color: #4facfe; margin-bottom: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; background: rgba(0,0,0,0.3); color: #fff; }
    .form-group textarea { min-height: 120px; resize: vertical; }
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
</style>

<div class="container">
    <h1>🏆 Управление кейсами</h1>
    <a href="/cases.php" style="color:#4facfe;">← На главную кейсов</a>
    
    <?php if ($message): ?>
        <div class="message message-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message" style="background: rgba(255,71,87,0.1); color: #ff4757; border: 1px solid rgba(255,71,87,0.2);">❌ <?= $error ?></div>
    <?php endif; ?>
    
    <!-- Создание кейса -->
    <div class="admin-section">
        <h2>✏️ Новый кейс</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label>Название кейса *</label>
                <input type="text" name="title" placeholder="Как AI помог увеличить продажи" required>
            </div>
            <div class="form-group">
                <label>URL (slug)</label>
                <input type="text" name="slug" placeholder="avtomaticheskaya-generatsiya">
            </div>
            <div class="form-group">
                <label>Клиент</label>
                <input type="text" name="client" placeholder="Название компании">
            </div>
            <div class="form-group">
                <label>Отрасль</label>
                <input type="text" name="industry" placeholder="Ритейл, IT, Маркетинг...">
            </div>
            <div class="form-group">
                <label>Технологии</label>
                <input type="text" name="technologies" placeholder="DeepSeek, PHP, AI...">
            </div>
            <div class="form-group">
                <label>Краткое описание *</label>
                <textarea name="description" placeholder="Краткое описание кейса" required></textarea>
            </div>
            <div class="form-group">
                <label>Задача / Проблема</label>
                <textarea name="challenge" placeholder="Какая задача стояла перед клиентом"></textarea>
            </div>
            <div class="form-group">
                <label>Решение</label>
                <textarea name="solution" placeholder="Как AI-агент решил проблему"></textarea>
            </div>
            <div class="form-group">
                <label>Результат</label>
                <textarea name="result" placeholder="Каких результатов удалось достичь"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">🏆 Добавить кейс</button>
        </form>
    </div>
    
    <!-- Список кейсов -->
    <div class="admin-section">
        <h2>📋 Все кейсы</h2>
        <?php if (empty($cases)): ?>
            <p style="color: rgba(255,255,255,0.5);">Нет кейсов</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Клиент</th>
                        <th>Статус</th>
                        <th>Просмотры</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cases as $case): ?>
                        <tr>
                            <td><?= $case['id'] ?></td>
                            <td><?= htmlspecialchars($case['title']) ?></td>
                            <td><?= htmlspecialchars($case['client'] ?? '—') ?></td>
                            <td><span class="status-badge status-<?= $case['is_published'] ? 'published' : 'draft' ?>"><?= $case['is_published'] ? '✅ Опубликован' : '📝 Черновик' ?></span></td>
                            <td><?= $case['views'] ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_publish">
                                    <input type="hidden" name="id" value="<?= $case['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">🔄</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $case['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить кейс?')">🗑️</button>
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
