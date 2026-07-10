<?php
// includes/logger.php - логирование событий

require_once __DIR__ . '/database.php';

function logEvent($userId, $eventType, $plan = null, $message = null) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, event_type, plan, message) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $eventType, $plan, $message]);
    } catch (Exception $e) {
        // Логируем ошибку, но не прерываем работу
        error_log("Ошибка логирования: " . $e->getMessage());
    }
}

function getUnreadNotifications($limit = 50) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT n.*, u.email 
        FROM notifications n
        LEFT JOIN users u ON n.user_id = u.id
        WHERE n.is_read = 0 
        ORDER BY n.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getAllNotifications($limit = 100, $offset = 0) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT n.*, u.email 
        FROM notifications n
        LEFT JOIN users u ON n.user_id = u.id
        ORDER BY n.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

function markAllAsRead($userId = null) {
    $db = getDB();
    if ($userId) {
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$userId]);
    } else {
        $db->query("UPDATE notifications SET is_read = 1");
    }
}

function getNotificationsCount() {
    $db = getDB();
    return $db->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
}
?>
