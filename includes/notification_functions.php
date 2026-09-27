<?php
function sendNotification($userId, $title, $message, $type = 'info') {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $title, $message, $type]);
    } catch (PDOException $e) {
        error_log('Notification failed: ' . $e->getMessage());
        return false;
    }
}

function getUserNotifications($userId, $limit = null) {
    $pdo = getDB();
    $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getUnreadCount($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

function markAsRead($nid, $userId) {
    $pdo = getDB();
    return $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?")->execute([$nid, $userId]);
}

function markAsDone($nid, $userId) {
    $pdo = getDB();
    return $pdo->prepare("UPDATE notifications SET is_done = 1, is_read = 1 WHERE notification_id = ? AND user_id = ?")->execute([$nid, $userId]);
}

function clearNotification($nid, $userId) {
    $pdo = getDB();
    return $pdo->prepare("DELETE FROM notifications WHERE notification_id = ? AND user_id = ?")->execute([$nid, $userId]);
}

function clearAllNotifications($userId) {
    $pdo = getDB();
    return $pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$userId]);
}

function notifyAllStudents($title, $message, $type = 'info') {
    $pdo = getDB();
    $users = $pdo->query("SELECT user_id FROM users WHERE status = 'active'")->fetchAll();
    foreach ($users as $u) {
        sendNotification($u['user_id'], $title, $message, $type);
    }
    return count($users);
}
?>