<?php
$db = getDB();

// Guests per property
$byProperty = $db->query('SELECT p.name, COUNT(g.id) as guest_count FROM properties p LEFT JOIN guests g ON g.property_id = p.id GROUP BY p.id ORDER BY guest_count DESC')->fetchAll();
$maxGuests = max(array_column($byProperty, 'guest_count') ?: [1]);

// Guests per month (last 6 months)
$byMonth = $db->query("SELECT DATE_FORMAT(submission_date,'%b %Y') as month, COUNT(*) as count FROM guests WHERE submission_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY YEAR(submission_date), MONTH(submission_date) ORDER BY submission_date ASC")->fetchAll();
$maxMonth = max(array_column($byMonth, 'count') ?: [1]);

// Top nationalities
$byNationality = $db->query('SELECT nationality, COUNT(*) as count FROM guests WHERE nationality IS NOT NULL AND nationality != "" GROUP BY nationality ORDER BY count DESC LIMIT 5')->fetchAll();
$maxNat = max(array_column($byNationality, 'count') ?: [1]);

// Summary stats
$totalGuests     = $db->query('SELECT COUNT(*) FROM guests')->fetchColumn();
$totalProperties = $db->query('SELECT COUNT(*) FROM properties')->fetchColumn();
$activeProps     = $db->query('SELECT COUNT(*) FROM properties WHERE status="Active"')->fetchColumn();
$thisMonth       = $db->query("SELECT COUNT(*) FROM guests WHERE MONTH(submission_date)=MONTH(CURDATE()) AND YEAR(submission_date)=YEAR(CURDATE())")->fetchColumn();
?>

<div class="page-header">
    <div>
        <h2>Reports</h2>
        <p>Overview and analytics for your properties.</p>
    </div>
</div>

<!-- Summary -->
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div><div class="stat-label">Total Guests</div><div class="stat-value"><?= number_format($totalGuests) ?></div></div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">This Month</div><div class="stat-value"><?= number_format($thisMonth) ?></div></div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div><div class="stat-label">Active Properties</div><div class="stat-value"><?= $activeProps ?> / <?= $totalProperties ?></div></div>
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        </div>
    </div>
</div>

<div class="reports-grid">
    <!-- Guests by Property -->
    <div class="card">
        <div class="card-header"><h3>Guests by Property</h3></div>
        <div class="card-body bar-chart">
            <?php foreach ($byProperty as $row): ?>
            <div class="bar-row">
                <div class="bar-label"><?= h($row['name']) ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= $maxGuests > 0 ? round($row['guest_count']/$maxGuests*100) : 0 ?>%">
                        <?= $row['guest_count'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Guests by Month -->
    <div class="card">
        <div class="card-header"><h3>Submissions (Last 6 Months)</h3></div>
        <div class="card-body bar-chart">
            <?php if ($byMonth): ?>
            <?php foreach ($byMonth as $row): ?>
            <div class="bar-row">
                <div class="bar-label"><?= h($row['month']) ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= $maxMonth > 0 ? round($row['count']/$maxMonth*100) : 0 ?>%">
                        <?= $row['count'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p style="color:var(--gray-400);font-size:13px;padding:12px 0;">No data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Nationalities -->
    <div class="card">
        <div class="card-header"><h3>Top Nationalities</h3></div>
        <div class="card-body bar-chart">
            <?php if ($byNationality): ?>
            <?php foreach ($byNationality as $row): ?>
            <div class="bar-row">
                <div class="bar-label"><?= h($row['nationality']) ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= $maxNat > 0 ? round($row['count']/$maxNat*100) : 0 ?>%;background:var(--green-600);">
                        <?= $row['count'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p style="color:var(--gray-400);font-size:13px;padding:12px 0;">No data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Guest Registration Table -->
    <div class="card">
        <div class="card-header"><h3>Property Summary</h3></div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Status</th>
                        <th>Guests</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($byProperty as $row): ?>
                    <tr>
                        <td class="bold"><?= h($row['name']) ?></td>
                        <td></td>
                        <td><?= number_format($row['guest_count']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
