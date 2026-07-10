<?php
$user = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/functions.php';
    $user = getUserById($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Agent — Ваш личный AI-помощник</title>
    <style>
        /* ===== ROOT ===== */
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #121212;
            --bg-card: rgba(255,255,255,0.03);
            --border-color: rgba(255,255,255,0.06);
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.6);
            --accent: #4facfe;
            --accent-glow: rgba(79,172,254,0.3);
            --gradient: linear-gradient(135deg, #4facfe, #00f2fe, #7c3aed, #f472b6, #4facfe);
            --font: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ===== RESET ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font);
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }

        /* ===== NEURAL BACKGROUND ===== */
        #neural-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .neural-glow-1 {
            position: absolute;
            top: -40%;
            left: -20%;
            width: 80%;
            height: 100%;
            background: radial-gradient(ellipse at 30% 50%, rgba(79,172,254,0.15), rgba(0,242,254,0.05), transparent 70%);
            animation: glow-pulse-1 8s ease-in-out infinite alternate;
        }
        .neural-glow-2 {
            position: absolute;
            top: 10%;
            left: -10%;
            width: 60%;
            height: 80%;
            background: radial-gradient(ellipse at 20% 60%, rgba(124,58,237,0.1), rgba(244,114,182,0.03), transparent 70%);
            animation: glow-pulse-2 10s ease-in-out infinite alternate;
        }
        .neural-glow-3 {
            position: absolute;
            top: 30%;
            left: -30%;
            width: 70%;
            height: 70%;
            background: radial-gradient(ellipse at 10% 40%, rgba(0,242,254,0.08), transparent 70%);
            animation: glow-pulse-3 12s ease-in-out infinite alternate;
        }
        @keyframes glow-pulse-1 {
            0% { transform: scale(1) rotate(0deg); opacity: 0.6; }
            100% { transform: scale(1.4) rotate(10deg); opacity: 1; }
        }
        @keyframes glow-pulse-2 {
            0% { transform: scale(1) rotate(-5deg); opacity: 0.4; }
            100% { transform: scale(1.5) rotate(15deg); opacity: 0.9; }
        }
        @keyframes glow-pulse-3 {
            0% { transform: scale(1) rotate(5deg); opacity: 0.3; }
            100% { transform: scale(1.6) rotate(-10deg); opacity: 0.8; }
        }

        /* ===== NEURAL PARTICLES ===== */
        .neural-particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(79,172,254,0.3);
            box-shadow: 0 0 20px rgba(79,172,254,0.1);
            animation: float-particle linear infinite;
        }
        @keyframes float-particle {
            0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            50% { opacity: 0.8; }
            100% { transform: translate(60px, -80px) scale(0); opacity: 0; }
        }

        /* ===== NEURAL LINES ===== */
        .neural-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(79,172,254,0.15), rgba(0,242,254,0.1), transparent);
            animation: pulse-line 4s ease-in-out infinite;
        }
        @keyframes pulse-line {
            0%, 100% { opacity: 0.1; transform: scaleX(0.3); }
            50% { opacity: 0.5; transform: scaleX(1); }
        }

       
        /* ===== PULSE TEXT ===== */
        .pulse-text {
            display: inline-block;
            animation: pulse-text 3s ease-in-out infinite;
        }
        @keyframes pulse-text {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.02); }
        }

        /* ===== GRADIENT TEXT ===== */
        .gradient-text {
            background: var(--gradient);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s ease-in-out infinite;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* ===== PULSE ARROW ===== */
        .pulse-arrow {
            display: inline-block;
            animation: pulse-arrow 2s ease-in-out infinite;
            font-size: 20px;
            margin-left: 6px;
        }
        @keyframes pulse-arrow {
            0%, 100% { transform: translateX(0); opacity: 0.6; }
            50% { transform: translateX(8px); opacity: 1; }
        }

        /* ===== BUTTON PULSE ===== */
        .btn-pulse {
            position: relative;
            overflow: hidden;
        }
        .btn-pulse::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(79,172,254,0.2), transparent 60%);
            animation: btn-pulse-ring 2.5s ease-in-out infinite;
            border-radius: 50%;
        }
        @keyframes btn-pulse-ring {
            0%, 100% { transform: scale(0.8); opacity: 0.2; }
            50% { transform: scale(1.4); opacity: 0.8; }
        }

        /* ===== CONTAINER ===== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* ===== HEADER ===== */
        header {
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            background: rgba(10,10,10,0.75);
            backdrop-filter: blur(20px);
            z-index: 1000;
        }
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            background: var(--gradient);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s ease-in-out infinite;
            letter-spacing: -0.5px;
        }
        .logo span { -webkit-text-fill-color: rgba(255,255,255,0.2); }

        nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        nav a {
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        nav a:hover,
        nav a.active {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }
        .btn {
            display: inline-block;
            padding: 10px 24px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .btn-primary {
            background: var(--gradient);
            background-size: 200% 200%;
            color: #fff;
            box-shadow: 0 4px 20px var(--accent-glow);
            animation: gradient-shift 3s ease-in-out infinite;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--accent-glow);
        }
        .btn-glass {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        .btn-glass:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        .btn-outline {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.05);
        }

        /* ===== NEURAL LINK ===== */
        .neural-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.7);
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 500;
        }
        .neural-link:hover {
            color: #4facfe;
        }
        .neural-link .pulse-arrow {
            font-size: 16px;
            animation: pulse-arrow 1.5s ease-in-out infinite;
        }

       
    </style>
<link rel="stylesheet" href="/assets/style.css?v=2">
</head>

<?php
// === БАННЕРЫ ===
require_once __DIR__ . '/banners.php';
echo showBanners('header');
?>

<body>
<script>

</script>
<!-- ===== NEURAL BACKGROUND ===== -->
<div id="neural-bg">
    <div class="neural-glow-1"></div>
    <div class="neural-glow-2"></div>
    <div class="neural-glow-3"></div>
    <!-- Партиклы и линии создаются через JS -->
</div>

<script>
    // ===== NEURAL BACKGROUND =====
    (function createNeuralBg() {
        const bg = document.getElementById('neural-bg');
        
        // Создаём партиклы
        const particleCount = window.innerWidth < 768 ? 40 : 80;
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'neural-particle';
            const size = Math.random() * 6 + 2;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            particle.style.left = (Math.random() * 60 + 10) + '%';
            particle.style.top = (Math.random() * 100) + '%';
            particle.style.animationDuration = (Math.random() * 20 + 15) + 's';
            particle.style.animationDelay = (Math.random() * 15) + 's';
            particle.style.opacity = Math.random() * 0.5 + 0.1;
            particle.style.background = ['rgba(79,172,254,0.3)', 'rgba(0,242,254,0.2)', 'rgba(124,58,237,0.2)'][Math.floor(Math.random() * 3)];
            bg.appendChild(particle);
        }
        
        // Создаём нейронные линии
        const lineCount = window.innerWidth < 768 ? 8 : 15;
        for (let i = 0; i < lineCount; i++) {
            const line = document.createElement('div');
            line.className = 'neural-line';
            line.style.width = (Math.random() * 250 + 80) + 'px';
            line.style.left = (Math.random() * 50 + 5) + '%';
            line.style.top = (Math.random() * 100) + '%';
            line.style.transform = 'rotate(' + (Math.random() * 360) + 'deg)';
            line.style.animationDelay = (Math.random() * 4) + 's';
            bg.appendChild(line);
        }
    })();

</script>

<div class="container">
    <?php
$user = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/functions.php';
    $user = getUserById($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ===== SEO ===== -->
    <title>AI Agent — Ваш личный AI-агент для бизнеса</title>
    <meta name="description" content="AI-агент для бизнеса: автоматизация, анализ данных, решения в 10 раз быстрее. Работает на DeepSeek API.">
    <meta name="keywords" content="AI, агент, бизнес, автоматизация, DeepSeek, искусственный интеллект">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://ai.nkvopros.ru/">

    <!-- ===== Open Graph (соцсети) ===== -->
    <meta property="og:title" content="AI Agent — Ваш личный AI-агент для бизнеса">
    <meta property="og:description" content="Автоматизируйте рутину, анализируйте данные и принимайте решения в 10 раз быстрее.">
    <meta property="og:url" content="https://ai.nkvopros.ru/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="AI Agent">

    <!-- ===== Twitter ===== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AI Agent — Ваш личный AI-агент">
    <meta name="twitter:description" content="Автоматизируйте рутину с AI-агентом.">

    <!-- ===== Дополнительно ===== -->
    <meta name="theme-color" content="#0a0a0a">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <style>
        /* ===== ROOT ===== */
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #121212;
            --bg-card: rgba(255,255,255,0.03);
            --border-color: rgba(255,255,255,0.06);
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.6);
            --accent: #4facfe;
            --accent-glow: rgba(79,172,254,0.3);
            --gradient: linear-gradient(135deg, #4facfe, #00f2fe, #7c3aed, #f472b6, #4facfe);
            --font: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ===== RESET ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font);
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }

        /* ===== CONTAINER ===== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* ===== HEADER ===== */
        header {
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            background: rgba(10,10,10,0.75);
            backdrop-filter: blur(20px);
            z-index: 1000;
        }
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            background: var(--gradient);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s ease-in-out infinite;
            letter-spacing: -0.5px;
        }
        .logo span { -webkit-text-fill-color: rgba(255,255,255,0.2); }

        nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        nav a {
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        nav a:hover,
        nav a.active {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }
        .btn {
            display: inline-block;
            padding: 8px 20px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .btn-primary {
            background: var(--gradient);
            background-size: 200% 200%;
            color: #fff;
            box-shadow: 0 4px 20px var(--accent-glow);
            animation: gradient-shift 3s ease-in-out infinite;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--accent-glow);
        }
        .btn-glass {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        .btn-glass:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        /* ===== ГАМБУРГЕР ===== */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 4px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
        }
        .hamburger span {
            display: block;
            width: 28px;
            height: 2px;
            background: #fff;
            border-radius: 2px;
            transition: 0.3s;
        }
        .hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(4px, 4px); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(4px, -4px); }

        /* ===== МОБИЛЬНАЯ АДАПТАЦИЯ ===== */
        @media (max-width: 768px) {
            .hamburger { display: flex; }
            nav {
                display: none;
                flex-direction: column;
                width: 100%;
                padding: 16px 0;
                gap: 8px;
                background: rgba(10,10,10,0.98);
                border-radius: 12px;
                margin-top: 10px;
            }
            nav.open { display: flex; }
            nav a {
                padding: 10px 16px;
                width: 100%;
                text-align: center;
            }
            nav .btn { width: 100%; text-align: center; }
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="header-inner">
            <a href="/" class="logo">
    <img src="/assets/images/logo.png" alt="AI Agent" style="height: 100px; width: auto; display: block;">
</a>

            <button class="hamburger" onclick="this.classList.toggle('open'); document.querySelector('nav').classList.toggle('open')" aria-label="Меню">
                <span></span><span></span><span></span>
            </button>

            <nav>
                <!-- Основные -->
                <a href="/" <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : '' ?>>Главная</a>
                <a href="/agents.php" <?= basename($_SERVER['PHP_SELF']) == 'agents.php' ? 'class="active"' : '' ?>>🤖 AI-Агенты</a>

                <!-- Чат (кнопка) -->
                <a href="/chat_common.php" class="btn" style="background: linear-gradient(135deg, #f39c12, #e67e22); padding:8px 18px; border-radius:50px; color:#fff; font-weight:600;">💬 Общий чат</a>

                <!-- Контент -->
                <a href="/blog.php" <?= basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'class="active"' : '' ?>>📝 Блог</a>
                <a href="/cases.php" <?= basename($_SERVER['PHP_SELF']) == 'cases.php' ? 'class="active"' : '' ?>>🏆 Кейсы</a>
                <a href="/#pricing">💰 Цены</a>

                <!-- Услуги -->
                <a href="/order_agent.php" <?= basename($_SERVER['PHP_SELF']) == 'order_agent.php' ? 'class="active"' : '' ?>>⚙️ Собрать агента</a>
                <a href="/test-chat.php" <?= basename($_SERVER['PHP_SELF']) == 'test-chat.php' ? 'class="active"' : '' ?>>🆓 Тест-чат</a>

                <!-- Авторизация -->
                <?php if ($user): ?>
                    <a href="/cabinet.php" <?= basename($_SERVER['PHP_SELF']) == 'cabinet.php' ? 'class="active"' : '' ?>>👤 Кабинет</a>
                    <?php if (isAdmin($_SESSION['user_id'])): ?>
                        <a href="/admin.php">⚙️ Админ</a>
                    <?php endif; ?>
                    <a href="/logout.php" class="btn btn-glass" style="padding:8px 18px;">🚪 Выйти</a>
                <?php else: ?>
                    <a href="/login_page.php">🔑 Войти</a>
                    <a href="/register_page.php" class="btn btn-primary" style="padding:8px 20px;">🚀 Начать</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
<?php
// Закрываем header.php — дальше идёт контент страницы
// Футер будет в footer.php
?>
