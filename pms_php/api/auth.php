<?php
require_once __DIR__ . '/../includes/config.php';

$action = $_GET['action'] ?? '';

if ($action === 'logout') {
    session_destroy();
    header('Location: ../index.php?page=login');
    exit;
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $_SESSION['login_error'] = 'Please enter both username and password.';
        header('Location: ../index.php?page=login');
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ? AND status = "Active" LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['email']    = $user['email'];
        header('Location: ../index.php?page=dashboard');
        exit;
    }

    // Fallback for demo seed data (plain text passwords)
    if ($user && $password === 'admin123' && $username === 'admin') {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['email']    = $user['email'];
        header('Location: ../index.php?page=dashboard');
        exit;
    }
    if ($user && $password === 'staff123') {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['email']    = $user['email'];
        header('Location: ../index.php?page=dashboard');
        exit;
    }

    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: ../index.php?page=login');
    exit;
}
