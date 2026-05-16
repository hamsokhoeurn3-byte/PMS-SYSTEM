<?php
require_once __DIR__ . '/includes/config.php';

$page = $_GET['page'] ?? 'dashboard';

// Public pages (no login required)
$publicPages = ['login', 'register'];

if (!in_array($page, $publicPages) && !isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

// Admin-only pages — checked HERE before any HTML is output
$adminPages = ['user-management', 'settings'];
if (in_array($page, $adminPages) && !isAdmin()) {
    header('Location: index.php?page=dashboard&error=unauthorized');
    exit;
}

// Route to the right page file
$pageMap = [
    'login'           => 'pages/login.php',
    'dashboard'       => 'pages/dashboard.php',
    'properties'      => 'pages/properties.php',
    'property-detail' => 'pages/property_detail.php',
    'guest-submissions' => 'pages/guest_submissions.php',
    'user-management' => 'pages/user_management.php',
    'reports'         => 'pages/reports.php',
    'settings'        => 'pages/settings.php',
    'profile'         => 'pages/profile.php',
    'register'        => 'pages/guest_register.php',
];

$pageFile = $pageMap[$page] ?? 'pages/dashboard.php';

if ($page === 'login' || $page === 'register') {
    // No layout for these pages
    require_once __DIR__ . '/' . $pageFile;
} else {
    require_once __DIR__ . '/includes/layout.php';
}
