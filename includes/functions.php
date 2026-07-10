<?php
// includes/functions.php

require_once __DIR__ . '/database.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function getUserByEmail($email) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function getUserById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createUser($email, $password) {
    $db = getDB();
    $hashed = hashPassword($password);
    $stmt = $db->prepare("INSERT INTO users (email, password_hash, plan) VALUES (?, ?, 'free')");
    $stmt->execute([$email, $hashed]);
    return $db->lastInsertId();
}

function updateUserPlan($userId, $newPlan) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET plan = ? WHERE id = ?");
    $stmt->execute([$newPlan, $userId]);
}

function resetDailyLimit($userId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET messages_today = 0, last_reset = NOW() WHERE id = ? AND DATE(last_reset) < CURDATE()");
    $stmt->execute([$userId]);
}

function checkAndResetLimits($userId) {
    $db = getDB();
    resetDailyLimit($userId);
    
    $user = getUserById($userId);
    if ($user && $user['subscription_end'] && strtotime($user['subscription_end']) < time()) {
        $stmt = $db->prepare("UPDATE users SET plan = 'free', subscription_end = NULL WHERE id = ?");
        $stmt->execute([$userId]);
        return false;
    }
    return true;
}

function getMessagesLeft($user) {
    $limits = [
        'free' => 5,
        'pro' => 500,
        'business' => 999999
    ];
    $limit = $limits[$user['plan']] ?? 5;
    return max(0, $limit - $user['messages_today']);
}

function getPlanName($plan) {
    $names = [
        'free' => 'Бесплатный',
        'pro' => 'PRO',
        'business' => 'Бизнес'
    ];
    return $names[$plan] ?? 'Бесплатный';
}

function getPlanClass($plan) {
    $classes = [
        'free' => 'plan-free',
        'pro' => 'plan-pro',
        'business' => 'plan-business'
    ];
    return $classes[$plan] ?? 'plan-free';
}

// === АДМИНИСТРАТОР ===

function isAdmin($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result && $result['is_admin'] == 1;
}

function getAllUsers($limit = 100) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users ORDER BY id DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getStats() {
    $db = getDB();
    
    $total = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $free = $db->query("SELECT COUNT(*) FROM users WHERE plan = 'free'")->fetchColumn();
    $pro = $db->query("SELECT COUNT(*) FROM users WHERE plan = 'pro'")->fetchColumn();
    $business = $db->query("SELECT COUNT(*) FROM users WHERE plan = 'business'")->fetchColumn();
    $today = $db->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    
    return [
        'total' => $total,
        'free' => $free,
        'pro' => $pro,
        'business' => $business,
        'today' => $today
    ];
}
?>
