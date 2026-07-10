<?php
// includes/config.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// === ПРЯМАЯ ЗАГРУЗКА .env (без Dotenv) ===
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[$key] = trim($value);
            putenv("$key=" . trim($value));
        }
    }
}

// === НАСТРОЙКИ БАЗЫ ДАННЫХ ===
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'ai_agent');
define('DB_USER', $_ENV['DB_USER'] ?? 'ai_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? '2026GodGod');

// === JWT ===
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'супер_секретный_ключ_123456789');

// === URL ===
define('APP_URL', $_ENV['APP_URL'] ?? 'https://ai.nkvopros.ru');

// === DEEPSEEK ===
define('DEEPSEEK_API_KEY', $_ENV['DEEPSEEK_API_KEY'] ?? '');

// === ЮKASSA ===
define('YOOKASSA_SHOP_ID', $_ENV['YOOKASSA_SHOP_ID'] ?? '');
define('YOOKASSA_SECRET_KEY', $_ENV['YOOKASSA_SECRET_KEY'] ?? '');

// === ТАРИФЫ ===
define('PRICES', [
    'free' => 0,
    'pro' => 7990,
    'business' => 15000
]);

define('PLAN_LIMITS', [
    'free' => 5,
    'pro' => 500,
    'business' => 999999
]);

define('PLAN_DAYS', [
    'pro' => 30,
    'business' => 30
]);

// === НАСТРОЙКИ ===
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Сессия (НЕ СТАРТУЕМ ЗДЕСЬ!)

// === AI-АГЕНТЫ ===
define('AI_AGENTS', [
    'lawyer' => [
        'name' => 'Юрист',
        'icon' => '⚖️',
        'description' => 'Помощь в юридических вопросах, анализ договоров, составление документов',
        'prompt' => 'Ты профессиональный юрист. Помогай пользователям с юридическими вопросами, анализируй договоры, составляй документы. Отвечай структурированно, ссылайся на законы РФ.',
        'plan' => 'pro',
        'features' => ['Анализ договоров', 'Составление исков', 'Консультации по законодательству']
    ],
    'programmer' => [
        'name' => 'Программист',
        'icon' => '💻',
        'description' => 'Помощь в написании кода, отладке, архитектуре ПО',
        'prompt' => 'Ты опытный программист. Помогай с написанием кода, объясняй алгоритмы, проводи рефакторинг. Давай примеры кода.',
        'plan' => 'pro',
        'features' => ['Генерация кода', 'Объяснение алгоритмов', 'Рефакторинг']
    ],
    'marketer' => [
        'name' => 'Маркетолог',
        'icon' => '📊',
        'description' => 'Создание контент-стратегий, SEO, SMM, аналитика',
        'prompt' => 'Ты профессиональный маркетолог. Помогай с контент-стратегией, SEO-оптимизацией, SMM, анализируй конкурентов.',
        'plan' => 'pro',
        'features' => ['Контент-план', 'SEO-оптимизация', 'Анализ конкурентов']
    ],
    'designer' => [
        'name' => 'Дизайнер',
        'icon' => '🎨',
        'description' => 'Генерация идей для дизайна, подбор цветовых схем, UI/UX',
        'prompt' => 'Ты креативный дизайнер. Генерируй идеи для дизайна, подбирай цветовые палитры, консультируй по UI/UX.',
        'plan' => 'business',
        'features' => ['Подбор цветов', 'Идеи для макетов', 'UI/UX-консультации']
    ],
    'analyst' => [
        'name' => 'Аналитик данных',
        'icon' => '📈',
        'description' => 'Анализ данных, визуализация, прогнозирование',
        'prompt' => 'Ты аналитик данных. Помогай с обработкой данных, визуализацией, создавай прогнозы и дашборды.',
        'plan' => 'business',
        'features' => ['Обработка данных', 'Визуализация', 'Прогнозирование']
    ]
   
]);

?>

