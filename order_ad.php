<?php
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
} else {
    $userEmail = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $isLoggedIn ? $user['email'] : trim($_POST['email'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $adType = trim($_POST['ad_type'] ?? '');
    $adText = trim($_POST['ad_text'] ?? '');
    $budget = trim($_POST['budget'] ?? '');
    $period = trim($_POST['period'] ?? '');
    $extra = trim($_POST['extra'] ?? '');
    
    if (empty($email) || empty($company) || empty($adText)) {
        $error = 'Заполните обязательные поля (email, компания, текст рекламы)';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email';
    } else {
        // Сохраняем заявку
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO ad_orders (user_id, email, company, website, ad_type, ad_text, budget, period, extra, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$isLoggedIn ? $user['id'] : null, $email, $company, $website, $adType, $adText, $budget, $period, $extra]);
        
        // Отправляем уведомление администратору
        $adminEmail = 'ejikovvladimir@yandex.ru'; // ВАШ EMAIL
        $subject = "📢 Новая заявка на рекламу!";
        $body = "
            <h2>Новая заявка на рекламу</h2>
            <p><strong>Компания:</strong> {$company}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Сайт:</strong> {$website}</p>
            <p><strong>Тип рекламы:</strong> {$adType}</p>
            <p><strong>Текст:</strong> {$adText}</p>
            <p><strong>Бюджет:</strong> {$budget}</p>
            <p><strong>Период:</strong> {$period}</p>
            <p><a href='https://ai.nkvopros.ru/admin_ads.php'>Перейти к заявкам</a></p>
        ";
        $headers = "From: no-reply@ai.nkvopros.ru\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        mail($adminEmail, $subject, $body, $headers);
        
        $message = '✅ Ваша заявка отправлена! Мы свяжемся с вами в ближайшее время.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<style>
    .form-container { max-width: 700px; margin: 0 auto; padding: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: rgba(255,255,255,0.8); }
    .form-group input, .form-group textarea, .form-group select { 
        width: 100%; padding: 12px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(0,0,0,0.3); color: #fff; font-size: 16px; 
    }
    .form-group textarea { min-height: 100px; resize: vertical; }
    .form-group input:focus, .form-group textarea:focus { border-color: #4facfe; outline: none; }
    .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #f39c12, #e67e22); border: none; border-radius: 12px; color: #fff; font-weight: 700; font-size: 18px; cursor: pointer; transition: all 0.3s; }
    .btn-submit:hover { transform: scale(1.02); }
    .success { color: #2ecc71; text-align: center; padding: 20px; background: rgba(46,204,113,0.1); border-radius: 12px; }
    .error { color: #ff4757; text-align: center; padding: 20px; background: rgba(255,71,87,0.1); border-radius: 12px; }
    .prices { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
    .price-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 20px; text-align: center; }
    .price-card .amount { font-size: 28px; font-weight: 800; color: #4facfe; }
    .price-card .desc { color: rgba(255,255,255,0.5); font-size: 14px; }
</style>

<div class="container">
    <div class="form-container">
        <h1 style="text-align:center; font-size:36px; font-weight:700; margin-bottom:10px;">
            📢 Заказать <span class="gradient-text">рекламу</span>
        </h1>
        <p style="text-align:center; color:rgba(255,255,255,0.6); font-size:16px; margin-bottom:30px;">
            Разместите рекламу на нашем сайте и привлеките новых клиентов
        </p>

        <div class="prices">
            <div class="price-card">
                <div class="amount">1 490 ₽</div>
                <div class="desc">Баннер на главной (7 дней)</div>
            </div>
            <div class="price-card">
                <div class="amount">2 990 ₽</div>
                <div class="desc">Баннер на всех страницах (14 дней)</div>
            </div>
            <div class="price-card">
                <div class="amount">4 990 ₽</div>
                <div class="desc">Премиум-размещение (30 дней)</div>
            </div>
        </div>
        
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
                <label>Название компании *</label>
                <input type="text" name="company" placeholder="ООО Ромашка" required>
            </div>
            
            <div class="form-group">
                <label>Сайт компании</label>
                <input type="url" name="website" placeholder="https://example.com">
            </div>
            
            <div class="form-group">
                <label>Тип рекламы *</label>
                <select name="ad_type" required>
                    <option value="banner">Баннер на главной</option>
                    <option value="banner_all">Баннер на всех страницах</option>
                    <option value="premium">Премиум-размещение</option>
                    <option value="custom">Индивидуальный</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Текст рекламы *</label>
                <textarea name="ad_text" placeholder="Напишите текст рекламного объявления" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Бюджет (₽)</label>
                <input type="number" name="budget" placeholder="Сумма, которую вы готовы потратить">
            </div>
            
            <div class="form-group">
                <label>Период размещения</label>
                <select name="period">
                    <option value="7">7 дней</option>
                    <option value="14">14 дней</option>
                    <option value="30">30 дней</option>
                    <option value="custom">Индивидуально</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дополнительные пожелания</label>
                <textarea name="extra" placeholder="Любые дополнительные требования"></textarea>
            </div>
            
            <button type="submit" class="btn-submit">🚀 Отправить заявку</button>
        </form>
        <?php endif; ?>
        
        <div style="text-align:center; margin-top:20px;">
            <a href="/" style="color:#4facfe; text-decoration:none;">← На главную</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
