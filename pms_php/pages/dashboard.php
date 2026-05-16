<?php
$db = getDB();
$user = getCurrentUser();

// Stats
$totalProperties = $db->query('SELECT COUNT(*) FROM properties')->fetchColumn();
$totalGuests     = $db->query('SELECT COUNT(*) FROM guests')->fetchColumn();
$todayCheckins   = $db->query("SELECT COUNT(*) FROM guests WHERE check_in = CURDATE()")->fetchColumn();
$todayCheckouts  = $db->query("SELECT COUNT(*) FROM guests WHERE check_out = CURDATE()")->fetchColumn();

// Recent submissions
$recentGuests = $db->query('SELECT g.*, p.name as property_name FROM guests g JOIN properties p ON g.property_id = p.id ORDER BY g.created_at DESC LIMIT 5')->fetchAll();

// Recent notifications
$notifications = $db->query('SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5')->fetchAll();
$unreadCount   = $db->query('SELECT COUNT(*)
 FROM notifications WHERE is_read = 0')->fetchColumn();
?>

<!-- Stats -->
<div class="page-header">
    <div>
        <h2>Dashboard</h2>
        <p>Welcome back, <?= h($user['username']) ?>! Here's what's happening today.</p>
    </div>
    <?php if (isAdmin()): ?>
    <div class="page-actions">
        <a href="index.php?page=properties" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Property
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Properties</div>
            <div class="stat-value"><?= $totalProperties ?></div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Guests</div>
            <div class="stat-value"><?= number_format($totalGuests) ?></div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Today's Check-ins</div>
            <div class="stat-value"><?= $todayCheckins ?></div>
        </div>
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Today's Check-outs</div>
            <div class="stat-value"><?= $todayCheckouts ?></div>
        </div>
        <div class="stat-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </div>
    </div>
</div>

<?php if ($notifications): ?>
<!-- Notifications -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <div style="display:flex;align-items:center;gap:8px;">
            <svg style="width:18px;height:18px;color:var(--blue-600);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <h3>Recent Notifications</h3>
            <?php if ($unreadCount > 0): ?>
            <span class="badge badge-red"><?= $unreadCount ?> new</span>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <?php foreach ($notifications as $n): ?>
        <a href="index.php?page=property-detail&id=<?= $n['property_id'] ?>" 
           style="display:flex;align-items:flex-start;gap:10px;padding:14px 20px;border-bottom:1px solid var(--gray-100);text-decoration:none;transition:background .15s;background:<?= !$n['is_read'] ? 'var(--blue-50)' : '#fff' ?>"
           onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='<?= !$n['is_read'] ? 'var(--blue-50)' : '#fff' ?>'">
            <?php if (!$n['is_read']): ?>
            <span style="width:8px;height:8px;background:var(--blue-600);border-radius:50%;flex-shrink:0;margin-top:5px;"></span>
            <?php else: ?>
            <span style="width:8px;flex-shrink:0;"></span>
            <?php endif; ?>
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--gray-900);margin-bottom:2px;"><?= h($n['property_name']) ?></div>
                <div style="font-size:13px;color:var(--gray-700);"><?= h($n['message']) ?></div>
                <div style="font-size:11px;color:var(--gray-500);margin-top:3px;">
                    ID: <?= h($n['id_booking'] ?? '') ?> &nbsp;·&nbsp; <?= h($n['created_at']) ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Recent Submissions -->
<div class="card">
    <div class="card-header">
        <h3>Recent Submissions</h3>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Guest Name</th>
                    <th>Property</th>
                    <th>Phone</th>
                    <th>Submission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentGuests as $g): ?>
                <tr>
                    <td class="bold"><?= h($g['name']) ?></td>
                    <td><?= h($g['property_name']) ?></td>
                    <td><?= h($g['phone']) ?></td>
                    <td><?= h($g['submission_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px;border-top:1px solid var(--gray-200);">
        <a href="index.php?page=guest-submissions" style="font-size:13px;color:var(--blue-600);font-weight:500;text-decoration:none;">
            View all submissions →
        </a>
    </div>
</div>
