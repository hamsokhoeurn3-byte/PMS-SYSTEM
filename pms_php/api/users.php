<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$action = $_GET['action'] ?? '';
$db = getDB();

// ADD
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'Staff';
    $status   = $_POST['status'] ?? 'Active';

    if (!$username || !$email || !$password) {
        jsonResponse(['success' => false, 'error' => 'All fields are required.']);
    }

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    try {
        $db->prepare('INSERT INTO users (username, email, password, role, status) VALUES (?,?,?,?,?)')
           ->execute([$username, $email, $hashed, $role, $status]);
        jsonResponse(['success' => true, 'message' => 'User created successfully!']);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'error' => 'Username or email already exists.']);
    }
}

// EDIT
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $email  = trim($_POST['email'] ?? '');
    $role   = $_POST['role'] ?? 'Staff';
    $status = $_POST['status'] ?? 'Active';

    $db->prepare('UPDATE users SET email=?, role=?, status=? WHERE id=?')
       ->execute([$email, $role, $status, $id]);

    if (!empty($_POST['password'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $db->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hashed, $id]);
    }

    jsonResponse(['success' => true, 'message' => 'User updated successfully!']);
}

// DELETE
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { header('Location: ../index.php?page=user-management&error=invalid'); exit; }
    $db->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    header('Location: ../index.php?page=user-management&success=deleted');
    exit;
}
