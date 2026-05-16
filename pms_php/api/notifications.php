<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? 'list';
$db = getDB();

if ($action === 'list') {
    $stmt = $db->query('SELECT * FROM notifications ORDER BY created_at DESC LIMIT 20');
    jsonResponse(['notifications' => $stmt->fetchAll()]);
}

if ($action === 'unread_count') {
    $stmt = $db->query('SELECT COUNT(*) as count FROM notifications WHERE is_read = 0');
    jsonResponse($stmt->fetch());
}

if ($action === 'mark_read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}

if ($action === 'mark_all_read') {
    $db->exec('UPDATE notifications SET is_read = 1');
    jsonResponse(['success' => true]);
}
