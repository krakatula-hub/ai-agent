<?php
// ad_click.php - обработка кликов по баннерам

require_once 'includes/config.php';
require_once 'includes/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die('❌ Не указан ID баннера');
}

// Получаем баннер
$db = getDB();
$stmt = $db->prepare("SELECT * FROM ad_banners WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$banner = $stmt->fetch();

if (!$banner) {
    die('❌ Баннер не найден или неактивен');
}

// Увеличиваем счётчик кликов
$stmt = $db->prepare("UPDATE ad_banners SET clicks = clicks + 1 WHERE id = ?");
$stmt->execute([$id]);

// Перенаправляем по ссылке
$link = $banner['link'] ?? '/';
header('Location: ' . $link);
exit;
?>
