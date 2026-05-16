<?php
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php?page=properties'); exit; }

$property = $db->prepare('SELECT * FROM properties WHERE id = ?');
$property->execute([$id]);
$property = $property->fetch();
if (!$property) { header('Location: index.php?page=properties'); exit; }

$guests = $db->prepare('SELECT * FROM guests WHERE property_id = ? ORDER BY created_at DESC');
$guests->execute([$id]);
$guests = $guests->fetchAll();
$totalGuests = count($guests);
$activeGuests = array_filter($guests, fn($g) => $g['check_out'] >= date('Y-m-d'));
?>

<div class="page-header">
    <div>
        <a href="index.php?page=properties" style="font-size:13px;color:var(--blue-600);text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
            <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Properties
        </a>
        <h2><?= h($property['name']) ?></h2>
        <p><?= h($property['location']) ?> &nbsp;·&nbsp;
            <span class="badge <?= $property['status'] === 'Active' ? 'badge-green' : 'badge-gray' ?>">
                <?= h($property['status']) ?>
            </span>
        </p>
    </div>
    <div class="page-actions">
        <a href="index.php?page=register&slug=<?= h($property['slug']) ?>" target="_blank" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Guest Registration Link
        </a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Guests</div>
            <div class="stat-value"><?= $totalGuests ?></div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Currently Active</div>
            <div class="stat-value"><?= count($activeGuests) ?></div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Property Status</div>
            <div class="stat-value" style="font-size:18px;color:<?= $property['status'] === 'Active' ? 'var(--green-600)' : 'var(--gray-500)' ?>">
                <?= h($property['status']) ?>
            </div>
        </div>
        <div class="stat-icon <?= $property['status'] === 'Active' ? 'green' : 'orange' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
    </div>
</div>

<!-- Guest Registration QR/Link -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3>Guest Self-Registration</h3></div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--gray-600);margin-bottom:12px;">
            Share this link with guests so they can register their own information before check-in:
        </p>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <code style="background:var(--gray-100);padding:8px 14px;border-radius:6px;font-size:13px;flex:1;min-width:200px;" id="regLink">
                <?= h((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php?page=register&slug=' . $property['slug']) ?>
            </code>
            <button class="btn btn-secondary btn-sm" onclick="copyLink()">Copy Link</button>
        </div>
    </div>
</div>

<!-- Guest List -->
<div class="card">
    <div class="card-header">
        <h3>Guest Submissions</h3>
        <span class="badge badge-blue"><?= $totalGuests ?> total</span>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Guest Name</th>
                    <th>ID Booking</th>
                    <th>Phone</th>
                    <th>Nationality</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Room</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$guests): ?>
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--gray-400);">No guests yet for this property.</td></tr>
                <?php endif; ?>
                <?php foreach ($guests as $g): ?>
                <tr>
                    <td class="bold"><?= h($g['name']) ?></td>
                    <td><span class="mono"><?= h($g['id_booking']) ?></span></td>
                    <td><?= h($g['phone']) ?></td>
                    <td><?= h($g['nationality'] ?? 'N/A') ?></td>
                    <td><?= h($g['check_in'] ?? 'N/A') ?></td>
                    <td><?= h($g['check_out'] ?? 'N/A') ?></td>
                    <td><?= h($g['room_number'] ?? 'N/A') ?></td>
                    <td><?= h($g['submission_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function copyLink() {
    const link = document.getElementById('regLink').textContent.trim();
    navigator.clipboard.writeText(link).then(() => showToast('Link copied to clipboard!'));
}
</script>
