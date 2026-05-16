<?php
// =============================================================
// config.php — Database connection & global configuration
// =============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change to your DB user
define('DB_PASS', '');           // Change to your DB password
define('DB_NAME', 'pms_db');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'Property Management System');
define('APP_VERSION', '1.0.0');
define('UPLOAD_DIR', __DIR__ . '/uploads/passports/');
define('UPLOAD_URL', 'uploads/passports/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PDO connection (singleton)
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// Auth helpers
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role'     => $_SESSION['role'],
        'email'    => $_SESSION['email'],
    ];
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php?page=dashboard&error=unauthorized');
        exit;
    }
}

// JSON API response helper
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Sanitize helper
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
