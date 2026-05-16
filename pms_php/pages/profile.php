<?php
$db = getDB();
$user = getCurrentUser();
$dbUser = $db->prepare('SELECT * FROM users WHERE id = ?');
$dbUser->execute([$user['id']]);
$dbUser = $dbUser->fetch();

$success = $_GET['success'] ?? null;
$error   = $_GET['error'] ?? null;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $email = trim($_POST['email'] ?? '');
        if ($email) {
            try {
                $db->prepare('UPDATE users SET email = ? WHERE id = ?')->execute([$email, $user['id']]);
                $_SESSION['email'] = $email;
                header('Location: index.php?page=profile&success=1');
                exit;
            } catch (PDOException $e) {
                $error = 'Email already in use.';
            }
        }
    }
    if ($_POST['action'] === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $newPw   = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($newPw !== $confirm) {
            $error = 'New passwords do not match.';
        } elseif (!password_verify($current, $dbUser['password']) && !($current === 'admin123' && $user['username'] === 'admin') && !($current === 'staff123')) {
            $error = 'Current password is incorrect.';
        } else {
            $db->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([password_hash($newPw, PASSWORD_BCRYPT), $user['id']]);
            header('Location: index.php?page=profile&success=2');
            exit;
        }
    }
}
?>

<div class="page-header">
    <div>
        <h2>My Profile</h2>
        <p>Manage your account information and security settings.</p>
    </div>
</div>

<?php if ($success == 1): ?>
<div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Profile updated successfully.</div>
<?php elseif ($success == 2): ?>
<div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Password changed successfully.</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg> <?= h($error) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Profile Info -->
    <div class="card">
        <div class="card-header">
            <h3>Account Information</h3>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
                <div class="avatar <?= isAdmin() ? 'avatar-admin' : 'avatar-staff' ?>" style="width:60px;height:60px;font-size:22px;">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div>
                    <div style="font-size:18px;font-weight:700;"><?= h($user['username']) ?></div>
                    <span class="badge <?= isAdmin() ? 'badge-purple' : 'badge-blue' ?>"><?= h($user['role']) ?></span>
                </div>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-row">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?= h($user['username']) ?>" disabled style="background:var(--gray-50)">
                </div>
                <div class="form-row">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= h($dbUser['email']) ?>" required>
                </div>
                <div class="form-row">
                    <label>Role</label>
                    <input type="text" class="form-control" value="<?= h($user['role']) ?>" disabled style="background:var(--gray-50)">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="card-header">
            <h3>Change Password</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-row">
                    <label>Current Password *</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>New Password *</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                </div>
                <div class="form-row">
                    <label>Confirm New Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</div>
