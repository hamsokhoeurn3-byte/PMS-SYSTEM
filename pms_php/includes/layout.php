<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = getCurrentUser();
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <svg class="logo-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="14" width="24" height="16" rx="2" fill="currentColor" opacity="0.15"/>
                <path d="M2 16L16 4L30 16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="12" y="20" width="8" height="10" rx="1" fill="currentColor"/>
                <rect x="6" y="18" width="6" height="5" rx="1" fill="currentColor" opacity="0.6"/>
                <rect x="20" y="18" width="6" height="5" rx="1" fill="currentColor" opacity="0.6"/>
            </svg>
            <span class="logo-text">PMS</span>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php?page=dashboard" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="index.php?page=properties" class="nav-item <?= $currentPage === 'properties' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Properties
            </a>
            <a href="index.php?page=guest-submissions" class="nav-item <?= $currentPage === 'guest-submissions' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Guest Submissions
            </a>
            <?php if (isAdmin()): ?>
            <a href="index.php?page=user-management" class="nav-item <?= $currentPage === 'user-management' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                User Management
            </a>
            <?php endif; ?>
            <a href="index.php?page=reports" class="nav-item <?= $currentPage === 'reports' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Reports
            </a>
            <a href="index.php?page=settings" class="nav-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="index.php?page=profile" class="nav-item <?= $currentPage === 'profile' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile
            </a>
            <a href="api/auth.php?action=logout" class="nav-item logout-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        <!-- TOP HEADER -->
        <header class="topbar">
            <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1 class="topbar-title"><?= h(APP_NAME) ?></h1>
            <div class="topbar-right">
                <!-- Notification Bell -->
                <div class="notif-wrapper" id="notifWrapper">
                    <button class="notif-btn" onclick="toggleNotifications()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span class="notif-badge" id="notifBadge" style="display:none">0</span>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <button onclick="markAllRead()" class="mark-read-btn">Mark all read</button>
                        </div>
                        <div id="notifList" class="notif-list">
                            <div class="notif-empty">Loading...</div>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="profile-wrapper" id="profileWrapper">
                    <button class="profile-btn" onclick="toggleProfile()">
                        <div class="avatar <?= isAdmin() ? 'avatar-admin' : 'avatar-staff' ?>">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                        <div class="profile-info">
                            <span class="profile-name"><?= h($user['username']) ?></span>
                            <span class="profile-role"><?= h($user['role']) ?></span>
                        </div>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="index.php?page=profile">View Profile</a>
                        <a href="index.php?page=profile">Edit Profile</a>
                        <a href="index.php?page=profile">Change Password</a>
                        <hr>
                        <a href="api/auth.php?action=logout" class="logout-link">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="page-content">
            <?php require_once __DIR__ . '/../' . $pageFile; ?>
        </main>
    </div>
</div>

<!-- GLOBAL MODAL OVERLAY -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>

<link rel="stylesheet" href="assets/css/app.css">
<script src="assets/js/app.js"></script>
</body>
</html>
