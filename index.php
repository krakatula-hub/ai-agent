<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>
<style>
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
    .gradient-text {
        background: linear-gradient(135deg, #4facfe, #00f2fe, #7c3aed, #f472b6, #4facfe);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradient-shift 4s ease-in-out infinite;
    }
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .hero {
        padding: 60px 0;
        text-align: center;
    }
    .hero h1 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 20px;
        line-height: 1.2;
    }
    .hero p {
        font-size: 18px;
        color: rgba(255,255,255,0.7);
        max-width: 600px;
        margin: 0 auto 30px;
    }
    .badge {
        display: inline-block;
        background: rgba(79,172,254,0.15);
        color: #4facfe;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 14px;
        border: 1px solid rgba(79,172,254,0.2);
        margin-bottom: 24px;
    }
    .hero-buttons {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }
    .btn {
        display: inline-block;
        padding: 12px 30px;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
    }
    .btn-primary {
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        color: #fff;
        box-shadow: 0 4px 20px rgba(79,172,254,0.3);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(79,172,254,0.5);
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
    .hero-stats {
        display: flex;
        gap: 40px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .stat .number {
        font-size: 28px;
        font-weight: 800;
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .stat .label {
        display: block;
        font-size: 14px;
        color: rgba(255,255,255,0.5);
    }
    .features {
        padding: 60px 0;
        border-top: 1px solid rgba(255,255,255,0.05);
    }
    .section-header {
        text-align: center;
        margin-bottom: 48px;
    }
    .section-header h2 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .section-header p {
        color: rgba(255,255,255,0.6);
        font-size: 18px;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
    }
    .feature-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-6px);
        border-color: rgba(79,172,254,0.3);
        background: rgba(255,255,255,0.05);
    }
    .feature-card .icon { font-size: 40px; margin-bottom: 16px; }
    .feature-card h3 { font-size: 20px; margin-bottom: 8px; }
    .feature-card p { color: rgba(255,255,255,0.6); font-size: 15px; }
    .agents-preview { padding: 60px 0; border-top: 1px solid rgba(255,255,255,0.05); }
    .agents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .agent-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: all 0.3s;
        position: relative;
    }
    .agent-card:hover {
        transform: translateY(-4px);
        border-color: rgba(79,172,254,0.3);
    }
    .agent-card .icon { font-size: 36px; margin-bottom: 8px; }
    .agent-card h3 { font-size: 18px; }
    .agent-card p { font-size: 14px; color: rgba(255,255,255,0.5); }
    .agent-card .tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 12px;
        border-radius: 50px;
        margin-top: 8px;
    }
    .agent-card .tag.popular { background: #f39c12; color: #fff; }
    .agent-card .tag.new { background: #2ecc71; color: #fff; }
    .pricing { padding: 60px 0; border-top: 1px solid rgba(255,255,255,0.05); }
    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }
    .pricing-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: all 0.3s;
        position: relative;
    }
    .pricing-card:hover {
        transform: translateY(-6px);
        border-color: rgba(79,172,254,0.3);
    }
    .pricing-card.popular {
        border-color: #4facfe;
        background: rgba(79,172,254,0.05);
    }
    .pricing-card .badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        color: #fff;
        padding: 4px 20px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
    .pricing-card h3 { font-size: 22px; margin-bottom: 8px; }
    .pricing-card .price {
        font-size: 40px;
        font-weight: 800;
        margin: 16px 0;
    }
    .pricing-card .price span {
        font-size: 16px;
        font-weight: 400;
        color: rgba(255,255,255,0.5);
    }
    .pricing-card ul {
        list-style: none;
        text-align: left;
        margin: 24px 0;
    }
    .pricing-card ul li {
        padding: 8px 0;
        color: rgba(255,255,255,0.7);
        font-size: 15px;
    }
    .pricing-card .btn { width: 100%; }
    .text-center { text-align: center; }
    .neural-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: rgba(255,255,255,0.7);
        transition: all 0.3s;
        text-decoration: none;
        font-weight: 500;
    }
    .neural-link:hover { color: #4facfe; }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        position: relative;
        z-index: 1;
    }
    @media (max-width: 768px) {
        .hero h1 { font-size: 32px; }
        .section-header h2 { font-size: 28px; }
        .pricing-grid { grid-template-columns: 1fr; }
        .hero-stats { gap: 20px; }
    }
    @media (max-width: 480px) {
        .hero h1 { font-size: 24px; }
        .hero-buttons .btn { width: 100%; }
        .container { padding: 0 12px; }
    }
</style>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="container">
        <span class="badge">🚀 Сделано с ❤️ в России.</span>
        <h1>Ваш личный <span class="gradient-text">AI-агент</span> для бизнеса</h1>
        <p>Автоматизируйте рутину, анализируйте данные и принимайте решения в 10 раз быстрее.</p>
        <div class="hero-buttons">
            <a href="/register_page.php" class="btn btn-primary btn-pulse">Начать бесплатно <span class="pulse-arrow">→</span></a>
            <a href="/test-chat.php" class="btn btn-glass">💬 Попробовать чат</a>
        </div>
        <div class="hero-stats">
            <div class="stat"><span class="number">50K+</span><span class="label">Запросов в день</span></div>
            <div class="stat"><span class="number">99.9%</span><span class="label">Доступность</span></div>
            <div class="stat"><span class="number">4.9 ★</span><span class="label">Рейтинг</span></div>
        </div>
    </div>
</section>

<!-- ===== FEATURES ===== -->
<section class="features">
    <div class="container">
        <div class="section-header">
            <h2>Почему выбирают <span class="gradient-text">AI Agent</span></h2>
            <p>Мощные возможности для решения любых задач</p>
        </div>
        <div class="features-grid">
            <div class="feature-card"><div class="icon">🧠</div><h3>Глубокое понимание</h3><p>Обрабатывает сложные запросы, удерживает контекст до 1M токенов</p></div>
            <div class="feature-card"><div class="icon">🔧</div><h3>Инструменты и функции</h3><p>Поиск, вычисления, анализ файлов — всё в одном месте</p></div>
            <div class="feature-card"><div class="icon">💬</div><h3>Естественный диалог</h3><p>Общайтесь как с человеком — AI понимает эмоции и намёки</p></div>
            <div class="feature-card"><div class="icon">🛡️</div><h3>Безопасность данных</h3><p>Все данные шифруются, мы не храним вашу историю без разрешения</p></div>
        </div>
    </div>
</section>

<!-- ===== POPULAR AGENTS ===== -->
<section class="agents-preview">
    <div class="container">
        <div class="section-header">
            <h2>Популярные <span class="gradient-text">AI-агенты</span></h2>
            <p>Профессиональные помощники для разных задач</p>
        </div>
        <div class="agents-grid">
            <div class="agent-card"><div class="icon">💼</div><h3>Бизнес-консультант</h3><p>Аналитика, стратегия, управление</p><span class="tag popular">Популярный</span></div>
            <div class="agent-card"><div class="icon">💻</div><h3>Программист</h3><p>Код, алгоритмы, архитектура</p><span class="tag new">Новый</span></div>
            <div class="agent-card"><div class="icon">📊</div><h3>Маркетолог</h3><p>Контент, SEO, SMM</p></div>
            <div class="agent-card"><div class="icon">🤖</div><h3>Виртуальный ассистент</h3><p>Документы, задачи, встречи</p><span class="tag popular">Популярный</span></div>
        </div>
        <div class="text-center">
            <a href="/agents.php" class="neural-link">Смотреть всех агентов <span class="pulse-arrow">→</span></a>
        </div>
    </div>
</section>

<!-- ===== PRICING ===== -->
<section class="pricing" id="pricing">
    <div class="container">
        <div class="section-header">
            <h2>Выберите свой <span class="gradient-text">тариф</span></h2>
            <p>Гибкие цены для любого бизнеса</p>
        </div>
        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Бесплатный</h3>
                <div class="price">0 ₽<span>/мес</span></div>
                <ul><li>✅ 5 сообщений в день</li><li>✅ До 2K токенов на ответ</li><li>✅ Базовая поддержка</li></ul>
                <a href="/register_page.php" class="btn btn-outline">Начать <span class="pulse-arrow">→</span></a>
            </div>
            <div class="pricing-card popular">
                <div class="badge">🔥 Популярный</div>
                <h3>PRO</h3>
                <div class="price">7 990 ₽<span>/мес</span></div>
                <ul><li>✅ 500 сообщений в месяц</li><li>✅ До 8K токенов на ответ</li><li>✅ Приоритетная очередь</li><li>✅ Все инструменты</li><li>✅ Поддержка 24/7</li></ul>
                <a href="/register_page.php" class="btn btn-primary btn-pulse">Купить PRO <span class="pulse-arrow">→</span></a>
            </div>
            <div class="pricing-card">
                <h3>Бизнес</h3>
                <div class="price">15 000 ₽<span>/мес</span></div>
                <ul><li>✅ Неограниченные сообщения</li><li>✅ До 32K токенов на ответ</li><li>✅ Свои инструменты</li><li>✅ API-доступ</li><li>✅ Выделенный менеджер</li></ul>
                <a href="/register_page.php" class="btn btn-glass">Связаться <span class="pulse-arrow">→</span></a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
