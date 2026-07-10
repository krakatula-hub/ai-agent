<?php
// includes/banners.php - вывод баннеров на всех страницах

function showBanners($position = 'header') {
    require_once __DIR__ . '/database.php';
    $db = getDB();
    $banners = $db->query("
        SELECT * FROM ad_banners 
        WHERE is_active = 1 AND position = '$position' AND (end_date IS NULL OR end_date > NOW())
        ORDER BY priority DESC
    ")->fetchAll();
    
    if (empty($banners)) return '';
    
    $html = '<div style="max-width: 1200px; margin: 10px auto; padding: 0 20px;">';
    foreach ($banners as $banner) {
        $html .= '
            <div style="background: rgba(79,172,254,0.05); border: 1px solid rgba(79,172,254,0.1); border-radius: 12px; padding: 15px; text-align: center; margin-bottom: 10px;">
                <a href="/ad_click.php?id=' . $banner['id'] . '" target="_blank" style="color: #fff; text-decoration: none;">
                    <strong>' . htmlspecialchars($banner['title']) . '</strong><br>
                    <span style="color: rgba(255,255,255,0.7);">' . htmlspecialchars($banner['content']) . '</span>
                </a>
            </div>
        ';
    }
    $html .= '</div>';
    return $html;
}
?>
