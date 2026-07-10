<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🚀 Запуск генерации sitemap...\n";

require_once 'includes/config.php';
require_once 'includes/database.php';

echo "✅ База данных подключена\n";

$db = getDB();

// Статические страницы
$pages = [
    '/' => ['priority' => 1.0, 'changefreq' => 'daily'],
    '/agents.php' => ['priority' => 0.9, 'changefreq' => 'weekly'],
    '/blog.php' => ['priority' => 0.8, 'changefreq' => 'weekly'],
    '/cases.php' => ['priority' => 0.8, 'changefreq' => 'weekly'],
    '/test-chat.php' => ['priority' => 0.6, 'changefreq' => 'weekly'],
    '/order_agent.php' => ['priority' => 0.6, 'changefreq' => 'weekly'],
    '/order_ad.php' => ['priority' => 0.6, 'changefreq' => 'weekly'],
];

echo "📄 Статические страницы: " . count($pages) . "\n";

// Получаем статьи из блога
try {
    $blogPosts = $db->query("SELECT slug, updated_at FROM blog_posts WHERE is_published = 1")->fetchAll();
    echo "📝 Статей в блоге: " . count($blogPosts) . "\n";
} catch (Exception $e) {
    echo "❌ Ошибка получения статей: " . $e->getMessage() . "\n";
    $blogPosts = [];
}

// Получаем кейсы
try {
    $cases = $db->query("SELECT slug, updated_at FROM cases WHERE is_published = 1")->fetchAll();
    echo "🏆 Кейсов: " . count($cases) . "\n";
} catch (Exception $e) {
    echo "❌ Ошибка получения кейсов: " . $e->getMessage() . "\n";
    $cases = [];
}

// Генерируем XML
echo "📝 Генерация XML...\n";

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($pages as $url => $data) {
    $xml .= '
    <url>
        <loc>https://ai.nkvopros.ru' . $url . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>' . $data['changefreq'] . '</changefreq>
        <priority>' . $data['priority'] . '</priority>
    </url>';
}

foreach ($blogPosts as $post) {
    $xml .= '
    <url>
        <loc>https://ai.nkvopros.ru/blog_post.php?slug=' . $post['slug'] . '</loc>
        <lastmod>' . date('Y-m-d', strtotime($post['updated_at'] ?? 'now')) . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>';
}

foreach ($cases as $case) {
    $xml .= '
    <url>
        <loc>https://ai.nkvopros.ru/case.php?slug=' . $case['slug'] . '</loc>
        <lastmod>' . date('Y-m-d', strtotime($case['updated_at'] ?? 'now')) . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>';
}

$xml .= '
</urlset>';

// Сохраняем в файл
$result = file_put_contents(__DIR__ . '/sitemap.xml', $xml);

if ($result) {
    echo "✅ Sitemap успешно сохранён! Размер: " . $result . " байт\n";
    echo "📂 Путь: " . __DIR__ . "/sitemap.xml\n";
} else {
    echo "❌ Ошибка сохранения sitemap.xml\n";
}
