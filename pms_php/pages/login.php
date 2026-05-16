<?php
require_once __DIR__ . '/../includes/config.php';

if (isLoggedIn()) {
    header('Location: index.php?page=dashboard');
    exit;
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <h1><?= APP_NAME ?></h1>
            <p>Sign in to access your account</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= h($error) ?>
        </div>
        <?php endif; ?>

        <form action="api/auth.php?action=login" method="POST">
            <div class="form-row">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required autofocus>
            </div>
            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:8px;font-size:15px;">
                Sign In
            </button>
        </form>

        <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--gray-200);">
            <button onclick="document.getElementById('demoBox').style.display=document.getElementById('demoBox').style.display==='none'?'block':'none'" 
                    style="background:none;border:none;color:var(--blue-600);font-size:13px;cursor:pointer;font-weight:500;">
                Show Demo Credentials
            </button>
            <div id="demoBox" style="display:none;margin-top:12px;">
                <div class="demo-box admin" style="margin-bottom:10px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div class="demo-title" style="color:#1e40af;">Administrator Account</div>
                            <div style="font-size:11px;color:var(--blue-600)">Full access to all features</div>
                        </div>
                        <button onclick="quickLogin('admin','admin123')" class="btn btn-primary btn-sm">Quick Login</button>
                    </div>
                    <div class="demo-creds" style="margin-top:8px;">
                        <div>Username: <strong>admin</strong></div>
                        <div>Password: <strong>admin123</strong></div>
                    </div>
                </div>
                <div class="demo-box staff">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div class="demo-title" style="color:#166534;">Staff Account</div>
                            <div style="font-size:11px;color:var(--green-600)">Limited access (view &amp; edit only)</div>
                        </div>
                        <button onclick="quickLogin('staff','staff123')" class="btn btn-sm" style="background:var(--green-600);color:#fff;">Quick Login</button>
                    </div>
                    <div class="demo-creds" style="margin-top:8px;">
                        <div>Username: <strong>staff</strong></div>
                        <div>Password: <strong>staff123</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function quickLogin(u, p) {
    document.getElementById('username').value = u;
    document.getElementById('password').value = p;
    document.querySelector('form').submit();
}
</script>
</body>
</html>
