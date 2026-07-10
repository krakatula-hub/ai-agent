<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>

<!-- Герой -->
<section style="text-align: center; padding: 40px 0 30px;">
    <h1 style="font-size: 48px; font-weight: 800; margin-bottom: 20px;">
        Выберите своего <span style="background: linear-gradient(135deg, #4facfe, #00f2fe); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">AI-агента</span>
    </h1>
    <p style="font-size: 20px; color: rgba(255,255,255,0.7); max-width: 700px; margin: 0 auto 30px;">
        Профессиональные помощники для любых задач — от бизнес-аналитики до творчества
    </p>
</section>

<!-- Фильтры -->
<div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 40px;">
    <button class="filter-btn active" data-filter="all" style="padding: 8px 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; background: transparent; color: rgba(255,255,255,0.6); cursor: pointer; transition: all 0.3s;">Все</button>
    <button class="filter-btn" data-filter="business" style="padding: 8px 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; background: transparent; color: rgba(255,255,255,0.6); cursor: pointer; transition: all 0.3s;">Бизнес</button>
    <button class="filter-btn" data-filter="creative" style="padding: 8px 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; background: transparent; color: rgba(255,255,255,0.6); cursor: pointer; transition: all 0.3s;">Креативные</button>
    <button class="filter-btn" data-filter="technical" style="padding: 8px 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; background: transparent; color: rgba(255,255,255,0.6); cursor: pointer; transition: all 0.3s;">Технические</button>
    <button class="filter-btn" data-filter="education" style="padding: 8px 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; background: transparent; color: rgba(255,255,255,0.6); cursor: pointer; transition: all 0.3s;">Образование</button>
</div>

<!-- Агенты -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;" id="agentsGrid">
    <!-- Бизнес-консультант -->
    <div class="agent-card" data-category="business" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; transition: all 0.3s;">
        <div style="font-size: 50px; margin-bottom: 15px;">💼</div>
        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Бизнес-консультант</h3>
        <span style="display: inline-block; padding: 3px 12px; background: rgba(79,172,254,0.15); color: #4facfe; border-radius: 20px; font-size: 12px; margin-bottom: 12px;">Бизнес</span>
        <span style="display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f39c12; color: #fff; margin-left: 8px;">Популярный</span>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 15px; line-height: 1.6;">Профессиональный помощник для бизнес-аналитики, стратегического планирования и управления проектами.</p>
        <ul style="list-style: none; margin: 15px 0;">
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Анализ бизнес-процессов</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Финансовое моделирование</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Стратегическое планирование</li>
        </ul>
        <div style="font-size: 20px; font-weight: 700; color: #4facfe; margin: 10px 0;">Входит в тариф PRO</div>
        <a href="/register_page.php" class="btn btn-primary" style="width:100%; text-align:center;">Выбрать</a>
    </div>
    
    <!-- Маркетолог -->
    <div class="agent-card" data-category="business" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; transition: all 0.3s;">
        <div style="font-size: 50px; margin-bottom: 15px;">📊</div>
        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Маркетолог</h3>
        <span style="display: inline-block; padding: 3px 12px; background: rgba(79,172,254,0.15); color: #4facfe; border-radius: 20px; font-size: 12px; margin-bottom: 12px;">Бизнес</span>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 15px; line-height: 1.6;">AI-специалист по маркетингу, который поможет с контент-стратегией, SMM и аналитикой.</p>
        <ul style="list-style: none; margin: 15px 0;">
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Контент-план на месяц</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ SEO-оптимизация</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Анализ конкурентов</li>
        </ul>
        <div style="font-size: 20px; font-weight: 700; color: #4facfe; margin: 10px 0;">Входит в тариф PRO</div>
        <a href="/register_page.php" class="btn btn-primary" style="width:100%; text-align:center;">Выбрать</a>
    </div>
    
    <!-- Программист -->
    <div class="agent-card" data-category="technical" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; transition: all 0.3s;">
        <div style="font-size: 50px; margin-bottom: 15px;">💻</div>
        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Программист</h3>
        <span style="display: inline-block; padding: 3px 12px; background: rgba(79,172,254,0.15); color: #4facfe; border-radius: 20px; font-size: 12px; margin-bottom: 12px;">Технические</span>
        <span style="display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #2ecc71; color: #fff; margin-left: 8px;">Новый</span>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 15px; line-height: 1.6;">Помощник в написании кода, отладке и архитектуре программных решений.</p>
        <ul style="list-style: none; margin: 15px 0;">
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Генерация кода</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Объяснение алгоритмов</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Рефакторинг</li>
        </ul>
        <div style="font-size: 20px; font-weight: 700; color: #4facfe; margin: 10px 0;">Входит в тариф Бизнес</div>
        <a href="/register_page.php" class="btn btn-primary" style="width:100%; text-align:center;">Выбрать</a>
    </div>
    
    <!-- Креативный дизайнер -->
    <div class="agent-card" data-category="creative" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; transition: all 0.3s;">
        <div style="font-size: 50px; margin-bottom: 15px;">🎨</div>
        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Креативный дизайнер</h3>
        <span style="display: inline-block; padding: 3px 12px; background: rgba(79,172,254,0.15); color: #4facfe; border-radius: 20px; font-size: 12px; margin-bottom: 12px;">Креативные</span>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 15px; line-height: 1.6;">Генерирует идеи для дизайна, подбирает цветовые схемы и создаёт концепции.</p>
        <ul style="list-style: none; margin: 15px 0;">
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Подбор цветовых палитр</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Генерация идей для макетов</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ UI/UX-консультации</li>
        </ul>
        <div style="font-size: 20px; font-weight: 700; color: #4facfe; margin: 10px 0;">Входит в тариф PRO</div>
        <a href="/register_page.php" class="btn btn-primary" style="width:100%; text-align:center;">Выбрать</a>
    </div>
    
    <!-- Юридический ассистент -->
    <div class="agent-card" data-category="business" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; transition: all 0.3s;">
        <div style="font-size: 50px; margin-bottom: 15px;">⚖️</div>
        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Юридический ассистент</h3>
        <span style="display: inline-block; padding: 3px 12px; background: rgba(79,172,254,0.15); color: #4facfe; border-radius: 20px; font-size: 12px; margin-bottom: 12px;">Бизнес</span>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 15px; line-height: 1.6;">Помощь в юридических вопросах, составлении документов и анализе законодательства.</p>
        <ul style="list-style: none; margin: 15px 0;">
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Анализ договоров</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Составление исков</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Проверка документов</li>
        </ul>
        <div style="font-size: 20px; font-weight: 700; color: #4facfe; margin: 10px 0;">Входит в тариф Бизнес</div>
        <a href="/register_page.php" class="btn btn-primary" style="width:100%; text-align:center;">Выбрать</a>
    </div>
    
    <!-- Виртуальный ассистент -->
    <div class="agent-card" data-category="business" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 30px; transition: all 0.3s;">
        <div style="font-size: 50px; margin-bottom: 15px;">🤖</div>
        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Виртуальный ассистент</h3>
        <span style="display: inline-block; padding: 3px 12px; background: rgba(79,172,254,0.15); color: #4facfe; border-radius: 20px; font-size: 12px; margin-bottom: 12px;">Бизнес</span>
        <span style="display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f39c12; color: #fff; margin-left: 8px;">Популярный</span>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 15px; line-height: 1.6;">Универсальный помощник для работы с документами, почтой и повседневными задачами.</p>
        <ul style="list-style: none; margin: 15px 0;">
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Работа с документами</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Организация встреч</li>
            <li style="padding: 6px 0; color: rgba(255,255,255,0.6); font-size: 14px;">✅ Управление проектами</li>
        </ul>
        <div style="font-size: 20px; font-weight: 700; color: #4facfe; margin: 10px 0;">Входит в тариф PRO</div>
        <a href="/register_page.php" class="btn btn-primary" style="width:100%; text-align:center;">Выбрать</a>
    </div>
</div>

<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.style.background = 'transparent';
            b.style.color = 'rgba(255,255,255,0.6)';
        });
        this.style.background = '#4facfe';
        this.style.color = '#fff';
        
        const filter = this.dataset.filter;
        document.querySelectorAll('.agent-card').forEach(card => {
            if (filter === 'all' || card.dataset.category === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
