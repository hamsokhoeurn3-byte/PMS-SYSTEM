<?php
require_once __DIR__ . '/../includes/config.php';

$slug = $_GET['slug'] ?? '';
$submitted = isset($_GET['submitted']);

$db = getDB();
$property = null;
if ($slug) {
    $stmt = $db->prepare('SELECT * FROM properties WHERE slug = ? AND status = "Active" LIMIT 1');
    $stmt->execute([$slug]);
    $property = $stmt->fetch();
}

$error   = $_SESSION['reg_error'] ?? null;
$success = $_SESSION['reg_success'] ?? null;
unset($_SESSION['reg_error'], $_SESSION['reg_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Registration<?= $property ? ' — ' . h($property['name']) : '' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div class="register-page">
    <div class="register-container">

        <?php if ($submitted && $success): ?>
        <!-- Success State -->
        <div class="card">
            <div class="card-body success-box">
                <div class="success-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h2 style="font-size:22px;font-weight:700;margin-bottom:8px;">Registration Complete!</h2>
                <p style="color:var(--gray-500);margin-bottom:12px;">Thank you for submitting your information. We look forward to welcoming you!</p>
                <p style="font-size:13px;color:var(--gray-400);">A confirmation will be sent to <?= h($success) ?></p>
            </div>
        </div>

        <?php elseif (!$property): ?>
        <!-- Invalid Property -->
        <div class="card">
            <div class="card-body" style="text-align:center;padding:48px;">
                <h2 style="color:var(--red-600);margin-bottom:8px;">Property Not Found</h2>
                <p style="color:var(--gray-500);">The registration link is invalid or the property is no longer active.</p>
            </div>
        </div>

        <?php else: ?>
        <!-- Header -->
        <div class="register-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <div>
                <h1><?= h($property['name']) ?></h1>
                <p>Guest Registration Form</p>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= h($error) ?>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form class="register-form" method="POST" action="api/guests.php?action=register" enctype="multipart/form-data">
            <input type="hidden" name="property_slug" value="<?= h($slug) ?>">

            <!-- Guest Information -->
            <div class="form-section">
                <h3>Guest Information</h3>
                <div class="form-row">
                    <label>Guest's Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>
                <div class="form-row">
                    <label>ID Booking *</label>
                    <input type="text" name="id_booking" class="form-control" placeholder="Enter your booking ID" required>
                </div>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 123-4567" required>
                    </div>
                    <div class="form-row">
                        <label>Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" id="dob_input" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <label>Age <small style="color:var(--gray-400)">(auto-calculated)</small></label>
                        <input type="text" name="age" id="age_input" class="form-control" readonly style="background:var(--gray-50)" placeholder="Auto-calculated">
                    </div>
                </div>
                <div class="form-row">
                    <label>Guest's Address *</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Enter your full address" required></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Occupation *</label>
                        <input type="text" name="occupation" class="form-control" placeholder="e.g. Software Engineer" required>
                    </div>
                    <div class="form-row">
                        <label>Nationality *</label>
                        <input type="text" name="nationality" class="form-control" placeholder="e.g. USA, Japan, UK" required>
                    </div>
                </div>
            </div>

            <!-- Stay Information -->
            <div class="form-section">
                <h3>Stay Information</h3>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Check-in Date *</label>
                        <input type="date" name="check_in" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <label>Check-out Date *</label>
                        <input type="date" name="check_out" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <label>Previous Stay Location</label>
                    <input type="text" name="previous_stay" class="form-control" placeholder="Where did you stay before this?">
                </div>
                <div class="form-row">
                    <label>Next Stay's Location</label>
                    <input type="text" name="next_stay" class="form-control" placeholder="Where will you stay next?">
                </div>
            </div>

            <!-- Additional Requirements -->
            <div class="form-section">
                <h3>Additional Requirements</h3>
                <div class="form-row">
                    <label>Do you have an address in Japan? *</label>
                    <div style="display:flex;gap:24px;margin-top:8px;">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;">
                            <input type="radio" name="has_japan_address" value="yes" required style="width:16px;height:16px;accent-color:var(--blue-600);">
                            Yes
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;">
                            <input type="radio" name="has_japan_address" value="no" required style="width:16px;height:16px;accent-color:var(--blue-600);">
                            No
                        </label>
                    </div>
                </div>

                <!-- Passport upload (shown if "No") -->
                <div id="passportBox" style="display:none;">
                    <div class="alert alert-warning" style="margin-bottom:12px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Since you do not have an address in Japan, please upload a clear photo of your passport.
                    </div>
                    <label class="upload-zone" for="passportFile">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                        <p id="uploadLabel" style="font-size:13px;color:var(--gray-600);margin-bottom:4px;">Click to upload passport photo</p>
                        <p style="font-size:11px;color:var(--gray-400);">PNG, JPG or PDF (max. 5MB)</p>
                        <input type="file" id="passportFile" name="passport" accept="image/*,.pdf"
                               onchange="document.getElementById('uploadLabel').textContent = this.files[0]?.name || 'Click to upload passport photo'">
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px;">
                Submit Registration
            </button>
        </form>
        <?php endif; ?>

    </div>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
