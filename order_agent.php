<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';
$isLoggedIn = isset($_SESSION['user_id']);

if ($isLoggedIn) {
    $user = getUserById($_SESSION['user_id']);
    $userEmail = $user['email'];
    $userId = $user['id'];
} else {
    $userEmail = '';
    $userId = null;
}


// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $isLoggedIn ? $user['email'] : trim($_POST['email'] ?? '');
    $agentName = trim($_POST['agent_name'] ?? '');
    $agentDescription = trim($_POST['agent_description'] ?? '');
    $agentTasks = trim($_POST['agent_tasks'] ?? '');
    $agentStyle = trim($_POST['agent_style'] ?? '');
    $agentExtra = trim($_POST['agent_extra'] ?? '');
    
    // Валидация
    if (empty($agentName) || empty($agentDescription)) {
        $error = 'Заполните название и описание агента';
    } elseif (!$isLoggedIn && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $error = 'Введите корректный email';
    } else {
        try {
            $db = getDB();
            
            // Проверяем, есть ли колонка extra
            $columns = $db->query("SHOW COLUMNS FROM agent_requests LIKE 'extra'")->fetch();
            if (!$columns) {
                $db->exec("ALTER TABLE agent_requests ADD COLUMN extra TEXT AFTER style");
            }
            
            $stmt = $db->prepare("INSERT INTO agent_requests (user_id, email, agent_name, description, tasks, style, extra, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $result = $stmt->execute([$userId, $email, $agentName, $agentDescription, $agentTasks, $agentStyle, $agentExtra]);
            
            if ($result) {
                // Отправляем уведомление администратору
                $adminEmail = 'ejikovvladimir@yandex.ru';
                $subject = "📝 Новая заявка на кастомного агента!";
                $body = "
                    <h2>Новая заявка на AI-агента</h2>
                    <p><strong>Клиент:</strong> {$email}</p>
                    <p><strong>Статус:</strong> " . ($isLoggedIn ? 'Авторизован' : 'Гость') . "</p>
                    <p><strong>Название:</strong> {$agentName}</p>
                    <p><strong>Описание:</strong> {$agentDescription}</p>
                    <p><a href='https://ai.nkvopros.ru/admin_agents.php'>Перейти к заявкам</a></p>
                ";
                $headers = "From: ai-agent@ai.nkvopros.ru\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                mail($adminEmail, $subject, $body, $headers);
                
                $message = '✅ Ваша заявка отправлена! Мы свяжемся с вами по email.';
            } else {
                $error = '❌ Ошибка при сохранении заявки';
            }
        } catch (Exception $e) {
            $error = '❌ Ошибка: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<style>
    .order-form { max-width: 700px; margin: 0 auto; padding: 20px; }
    .order-form .form-group { margin-bottom: 20px; }
    .order-form label { display: block; margin-bottom: 8px; font-weight: 600; color: rgba(255,255,255,0.8); }
    .order-form input, .order-form textarea, .order-form select { 
        width: 100%; padding: 12px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(0,0,0,0.3); color: #fff; font-size: 16px; 
    }
    .order-form textarea { min-height: 120px; resize: vertical; }
    .order-form input:focus, .order-form textarea:focus { border-color: #4facfe; outline: none; }
    .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #4facfe, #00f2fe); border: none; border-radius: 12px; color: #fff; font-weight: 700; font-size: 18px; cursor: pointer; transition: all 0.3s; }
    .btn-submit:hover { transform: scale(1.02); }
    .success { color: #2ecc71; text-align: center; padding: 20px; background: rgba(46,204,113,0.1); border-radius: 12px; }
    .error { color: #ff4757; text-align: center; padding: 20px; background: rgba(255,71,87,0.1); border-radius: 12px; }
    .info-note { background: rgba(79,172,254,0.05); border: 1px solid rgba(79,172,254,0.1); border-radius: 12px; padding: 15px; margin-bottom: 20px; color: rgba(255,255,255,0.6); font-size: 14px; text-align: center; }
</style>

<div class="container">
    <div class="order-form">
        <h1 style="text-align:center; font-size:36px; font-weight:700; margin-bottom:10px;">
            📝 Заказать <span class="gradient-text">AI-агента</span>
        </h1>
        <p style="text-align:center; color:rgba(255,255,255,0.6); font-size:16px; margin-bottom:30px;">
            Опишите, какой AI-агент вам нужен, и мы создадим его специально для вас
        </p>
        
        <?php if ($message): ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!$message): ?>
        <form method="POST">
            <?php if (!$isLoggedIn): ?>
                <div class="form-group">
                    <label>Ваш email *</label>
                    <input type="email" name="email" placeholder="example@mail.com" required>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>Название агента *</label>
                <input type="text" name="agent_name" placeholder="Например: Финансовый консультант" required>
            </div>
            
            <div class="form-group">
                <label>Описание агента *</label>
                <textarea name="agent_description" placeholder="Опишите, что должен уметь агент, какую задачу решать" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Какие задачи должен решать?</label>
                <textarea name="agent_tasks" placeholder="Перечислите конкретные задачи, которые должен выполнять агент"></textarea>
            </div>
            
            <div class="form-group">
                <label>Стиль общения</label>
                <select name="agent_style">
                    <option value="">Выберите стиль</option>
                    <option value="professional">Профессиональный</option>
                    <option value="friendly">Дружелюбный</option>
                    <option value="strict">Строгий</option>
                    <option value="creative">Креативный</option>
                    <option value="custom">Свой вариант</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дополнительные требования</label>
                <textarea name="agent_extra" placeholder="Любые дополнительные пожелания"></textarea>
            </div>
            
            <button type="submit" class="btn-submit">🚀 Отправить заявку</button>
        </form>
        <?php endif; ?>

<!-- ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ -->
<div style="text-align:center; margin:20px 0; padding:15px; background:rgba(79,172,254,0.03); border-radius:12px; border:1px solid rgba(79,172,254,0.05);">
    <p style="color:rgba(255,255,255,0.6); font-size:14px; margin:0;">
        📌 <strong>Что дальше?</strong> Мы изучим ваш запрос и предложим лучшего AI-агента под ваши задачи.
    </p>
    <p style="color:rgba(255,255,255,0.4); font-size:12px; margin-top:5px;">
        Обычно мы отвечаем в течение 2-3 часов
    </p>
<p style="color:rgba(255,255,255,0.4); font-size:12px; margin-top:5px;">
        Обязательно проверяйте папку (СПАМ)
    </p>
</div>
        
        <div style="text-align:center; margin-top:20px;">
            <a href="/agent_select.php" style="color:#4facfe; text-decoration:none;">← Вернуться к выбору агентов</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
