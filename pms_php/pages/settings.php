<?php
// Auth already checked in index.php before any HTML output
$success = $_GET['success'] ?? null;
?>

<style>
.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label { font-size: 13px; font-weight: 500; color: var(--gray-700); }
.toggle-field {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 0; border-bottom: 1px solid var(--gray-100); cursor: pointer; gap: 16px;
}
.toggle-field:last-of-type { border-bottom: none; }
.toggle-field > div { display: flex; flex-direction: column; gap: 2px; }
.toggle-field strong { font-size: 13px; font-weight: 600; color: var(--gray-900); }
.toggle-field > div > span { display: block; font-size: 12px; color: var(--gray-500); }
.toggle-field > span { position: relative; flex-shrink: 0; }
.toggle-field input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }
.switch-toggle {
    display: block; width: 44px; height: 24px; background: var(--gray-300);
    border-radius: 12px; position: relative; transition: background .2s; cursor: pointer;
}
.switch-toggle::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 18px; height: 18px; background: #fff; border-radius: 50%;
    transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-field input[type="checkbox"]:checked + .switch-toggle { background: var(--blue-600); }
.toggle-field input[type="checkbox"]:checked + .switch-toggle::after { transform: translateX(20px); }
.settings-actions { margin-top: 20px; }
@media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }
</style>

<div class="page-header">
    <div>
        <h2>Settings</h2>
        <p>Configure system preferences and options.</p>
    </div>
</div>

<?php if ($success): ?>
<div class="alert alert-success">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    Settings saved successfully.
</div>
<?php endif; ?>

<form method="POST" action="?page=settings&success=1">
<div class="settings-grid">

    <!-- General Settings -->
    <div class="card">
        <div class="card-header"><h3>General Settings</h3></div>
        <div class="card-body">
            <div class="form-grid" style="gap:14px;">
                <div class="form-group">
                    <label for="system_name">System Name</label>
                    <input id="system_name" type="text" name="system_name" class="form-control" value="Property Management System">
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input id="company_name" type="text" name="company_name" class="form-control" value="Your Hotel Group">
                </div>
                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select id="timezone" name="timezone" class="form-control">
                        <option value="America/Los_Angeles">Pacific Time (PT)</option>
                        <option value="UTC" selected>UTC</option>
                        <option value="America/New_York">Eastern Time (ET)</option>
                        <option value="Europe/London">GMT</option>
                        <option value="Asia/Tokyo">Asia/Tokyo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_format">Date Format</label>
                    <select id="date_format" name="date_format" class="form-control">
                        <option value="m/d/Y">MM/DD/YYYY</option>
                        <option value="d/m/Y">DD/MM/YYYY</option>
                        <option value="Y-m-d" selected>YYYY-MM-DD</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="card">
        <div class="card-header"><h3>Notification Settings</h3></div>
        <div class="card-body" style="padding-top:8px;padding-bottom:8px;">
            <label class="toggle-field">
                <div>
                    <strong>Email Notifications</strong>
                    <span>Receive email alerts for new submissions</span>
                </div>
                <span>
                    <input type="checkbox" name="email_notifications" checked>
                    <span class="switch-toggle"></span>
                </span>
            </label>
            <label class="toggle-field">
                <div>
                    <strong>Guest Notifications</strong>
                    <span>Send confirmation emails to guests</span>
                </div>
                <span>
                    <input type="checkbox" name="guest_notifications" checked>
                    <span class="switch-toggle"></span>
                </span>
            </label>
        </div>
    </div>

    <!-- System Settings -->
    <div class="card">
        <div class="card-header"><h3>System Settings</h3></div>
        <div class="card-body" style="padding-top:8px;padding-bottom:16px;">
            <label class="toggle-field">
                <div>
                    <strong>Automatic Backup</strong>
                    <span>Enable daily database backups</span>
                </div>
                <span>
                    <input type="checkbox" name="auto_backup" checked>
                    <span class="switch-toggle"></span>
                </span>
            </label>
            <label class="toggle-field">
                <div>
                    <strong>Maintenance Mode</strong>
                    <span>Disable guest registration temporarily</span>
                </div>
                <span>
                    <input type="checkbox" name="maintenance_mode">
                    <span class="switch-toggle"></span>
                </span>
            </label>
            <div class="settings-actions">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="card">
        <div class="card-header"><h3>System Information</h3></div>
        <div class="card-body">
            <?php
            $db = getDB();
            $mysqlVersion = $db->query('SELECT VERSION()')->fetchColumn();
            $guestCount   = $db->query('SELECT COUNT(*) FROM guests')->fetchColumn();
            $propCount    = $db->query('SELECT COUNT(*) FROM properties')->fetchColumn();
            $userCount    = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
            ?>
            <table class="data-table">
                <tbody>
                    <tr><td style="font-weight:600;width:55%">PHP Version</td><td><?= h(PHP_VERSION) ?></td></tr>
                    <tr><td style="font-weight:600">MySQL Version</td><td><?= h($mysqlVersion) ?></td></tr>
                    <tr><td style="font-weight:600">Total Properties</td><td><?= $propCount ?></td></tr>
                    <tr><td style="font-weight:600">Total Guests</td><td><?= number_format($guestCount) ?></td></tr>
                    <tr><td style="font-weight:600">Total Users</td><td><?= $userCount ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
</form>
