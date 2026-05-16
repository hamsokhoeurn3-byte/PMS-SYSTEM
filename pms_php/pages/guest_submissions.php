<?php
$db = getDB();

$guests = $db->query('SELECT g.*, p.name as property_name FROM guests g JOIN properties p ON g.property_id = p.id ORDER BY g.created_at DESC')->fetchAll();
$properties = $db->query('SELECT name FROM properties ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$total = count($guests);
?>

<div class="page-header">
    <div>
        <h2>Guest Submissions</h2>
        <p>View and manage all guest information submissions.</p>
    </div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
<div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Guest deleted successfully.</div>
<?php endif; ?>

<!-- Filters -->
<div class="filter-bar" style="flex-wrap:wrap;gap:12px;">
    <div class="filter-group" style="flex:2;min-width:200px;">
        <label>Search</label>
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, phone, booking ID..." oninput="applyFilters()">
        </div>
    </div>
    <div class="filter-group" style="min-width:160px;">
        <label>Filter by Property</label>
        <select id="propertyFilter" class="form-control" onchange="applyFilters()">
            <option value="All Properties">All Properties</option>
            <?php foreach ($properties as $prop): ?>
            <option value="<?= h($prop) ?>"><?= h($prop) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group" style="min-width:140px;">
        <label>Start Date</label>
        <input type="date" id="dateStart" class="form-control" onchange="applyFilters()">
    </div>
    <div class="filter-group" style="min-width:140px;">
        <label>End Date</label>
        <input type="date" id="dateEnd" class="form-control" onchange="applyFilters()">
    </div>
</div>

<div class="result-count" id="resultCount">Showing <?= $total ?> of <?= $total ?> submissions</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table" id="mainTable">
            <thead>
                <tr>
                    <th>Guest Name</th>
                    <th>ID Booking</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Property</th>
                    <th>Submission Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guests as $g): ?>
                <tr data-property="<?= h($g['property_name']) ?>" data-date="<?= h($g['submission_date']) ?>">
                    <td>
                        <button class="guest-link" onclick='openGuestDetail(<?= json_encode($g) ?>)'>
                            <?= h($g['name']) ?>
                        </button>
                    </td>
                    <td><span class="mono"><?= h($g['id_booking']) ?></span></td>
                    <td><?= h($g['phone']) ?></td>
                    <td><?= h($g['email']) ?></td>
                    <td><?= h($g['property_name']) ?></td>
                    <td><?= h($g['submission_date']) ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="edit-btn" onclick='openEditGuest(<?= json_encode($g) ?>)' title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <?php if (isAdmin()): ?>
                            <button class="del-btn" title="Delete" onclick="confirmDelete('api/guests.php?action=delete&id=<?= $g['id'] ?>', '<?= h(addslashes($g['name'])) ?>')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- GUEST DETAIL MODAL -->
<div class="modal" id="guestDetailModal">
    <div class="modal-box modal-lg">
        <div class="modal-header" style="background:var(--blue-600);color:#fff;">
            <h3 style="color:#fff;">Guest Complete Information</h3>
            <button class="modal-close" onclick="closeModal('guestDetailModal')" style="color:#fff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body" id="guestDetailBody"></div>
        <div class="modal-footer" id="guestDetailFooter"></div>
    </div>
</div>

<!-- EDIT GUEST MODAL -->
<div class="modal" id="editGuestModal">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3>Edit Guest Information</h3>
            <button class="modal-close" onclick="closeModal('editGuestModal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editGuestForm" onsubmit="saveGuest(event)">
            <input type="hidden" name="id" id="eg_id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-row">
                        <label>Full Name *</label>
                        <input type="text" name="name" id="eg_name" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <label>Phone</label>
                        <input type="text" name="phone" id="eg_phone" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Email</label>
                        <input type="email" name="email" id="eg_email" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Nationality</label>
                        <input type="text" name="nationality" id="eg_nationality" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Occupation</label>
                        <input type="text" name="occupation" id="eg_occupation" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Room Number</label>
                        <input type="text" name="room_number" id="eg_room" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Check-in Date</label>
                        <input type="date" name="check_in" id="eg_checkin" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Check-out Date</label>
                        <input type="date" name="check_out" id="eg_checkout" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <label>Address</label>
                    <textarea name="address" id="eg_address" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Previous Stay Location</label>
                        <input type="text" name="previous_stay" id="eg_prev" class="form-control">
                    </div>
                    <div class="form-row">
                        <label>Next Stay Location</label>
                        <input type="text" name="next_stay" id="eg_next" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editGuestModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
const isAdmin = <?= isAdmin() ? 'true' : 'false' ?>;

function openGuestDetail(g) {
    const nights = g.check_in && g.check_out
        ? Math.ceil((new Date(g.check_out) - new Date(g.check_in)) / 86400000)
        : 'N/A';

    document.getElementById('guestDetailBody').innerHTML = `
        <div class="detail-section">
            <h4>Basic Information</h4>
            <div class="detail-grid">
                <div class="detail-item"><div class="label">Full Name</div><div class="value" style="font-size:16px;font-weight:700;">${escHtml(g.name)}</div></div>
                <div class="detail-item"><div class="label">ID Booking</div><div class="value"><span class="badge badge-gray mono">${escHtml(g.id_booking)}</span></div></div>
                <div class="detail-item"><div class="label">Date of Birth</div><div class="value">${escHtml(g.date_of_birth||'N/A')}</div></div>
                <div class="detail-item"><div class="label">Age</div><div class="value">${escHtml(String(g.age||'N/A'))} years old</div></div>
                <div class="detail-item"><div class="label">Nationality</div><div class="value"><span class="badge badge-blue">${escHtml(g.nationality||'N/A')}</span></div></div>
                <div class="detail-item"><div class="label">Occupation</div><div class="value">${escHtml(g.occupation||'N/A')}</div></div>
            </div>
        </div>
        <div class="detail-section">
            <h4>Contact Information</h4>
            <div class="detail-grid">
                <div class="detail-item"><div class="label">Phone Number</div><div class="value">${escHtml(g.phone)}</div></div>
                <div class="detail-item"><div class="label">Email Address</div><div class="value">${escHtml(g.email)}</div></div>
                <div class="detail-item full"><div class="label">Address</div><div class="value">${escHtml(g.address||'N/A')}</div></div>
            </div>
        </div>
        <div class="detail-section">
            <h4>Property & Stay Information</h4>
            <div class="detail-grid-3">
                <div class="detail-item"><div class="label">Property</div><div class="value" style="font-weight:600;">${escHtml(g.property_name)}</div></div>
                <div class="detail-item"><div class="label">Room Number</div><div class="value" style="font-size:18px;font-weight:700;color:var(--blue-600);">${escHtml(g.room_number||'N/A')}</div></div>
                <div class="detail-item"><div class="label">Submission Date</div><div class="value">${escHtml(g.submission_date)}</div></div>
                <div class="detail-item"><div class="label">Check-in Date</div><div class="value">${escHtml(g.check_in||'N/A')}</div></div>
                <div class="detail-item"><div class="label">Check-out Date</div><div class="value">${escHtml(g.check_out||'N/A')}</div></div>
                <div class="detail-item"><div class="label">Duration</div><div class="value">${nights} night(s)</div></div>
                <div class="detail-item full"><div class="label">Previous Stay Location</div><div class="value">${escHtml(g.previous_stay_location||'N/A')}</div></div>
                <div class="detail-item full"><div class="label">Next Stay's Location</div><div class="value">${escHtml(g.next_stay_location||'N/A')}</div></div>
            </div>
        </div>
        <div class="detail-section">
            <h4>Additional Requirements</h4>
            <div class="detail-item" style="margin-bottom:12px;">
                <div class="label">Has Address in Japan?</div>
                <div class="value" style="margin-top:4px;">
                    <span class="badge ${g.has_japan_address === 'yes' ? 'badge-green' : 'badge-yellow'}">
                        ${g.has_japan_address === 'yes' ? 'Yes' : 'No'}
                    </span>
                </div>
            </div>
            ${g.has_japan_address === 'no' && g.passport_photo ? `
            <div class="passport-box">
                <span class="badge badge-yellow">Required Document</span>
                <strong style="margin-left:10px;font-size:13px;">Passport Photo</strong>
                <br><img src="uploads/passports/${escHtml(g.passport_photo)}" alt="Passport" style="margin-top:12px;max-width:320px;border-radius:6px;border:2px solid var(--gray-200);">
                <p style="font-size:12px;color:var(--gray-500);margin-top:8px;">Official passport identification photo (uploaded by guest)</p>
            </div>` : ''}
            ${g.has_japan_address === 'yes' ? `
            <div class="alert alert-success">✓ Guest has an address in Japan. No passport photo required.</div>` : ''}
        </div>
    `;

    const footer = document.getElementById('guestDetailFooter');
    footer.innerHTML = `
        <button class="btn btn-secondary" onclick="closeModal('guestDetailModal')">Close</button>
        <button class="btn btn-primary" onclick="closeModal('guestDetailModal');openEditGuest(currentGuest)">Edit Guest Info</button>
        ${isAdmin ? `<button class="btn btn-danger" onclick="closeModal('guestDetailModal');confirmDelete('api/guests.php?action=delete&id=${g.id}','${escHtml(g.name)}')">Delete Guest</button>` : ''}
    `;

    window.currentGuest = g;
    openModal('guestDetailModal');
}

function openEditGuest(g) {
    document.getElementById('eg_id').value        = g.id;
    document.getElementById('eg_name').value      = g.name;
    document.getElementById('eg_phone').value     = g.phone;
    document.getElementById('eg_email').value     = g.email;
    document.getElementById('eg_nationality').value = g.nationality || '';
    document.getElementById('eg_occupation').value  = g.occupation || '';
    document.getElementById('eg_room').value      = g.room_number || '';
    document.getElementById('eg_checkin').value   = g.check_in || '';
    document.getElementById('eg_checkout').value  = g.check_out || '';
    document.getElementById('eg_address').value   = g.address || '';
    document.getElementById('eg_prev').value      = g.previous_stay_location || '';
    document.getElementById('eg_next').value      = g.next_stay_location || '';
    openModal('editGuestModal');
}

async function saveGuest(e) {
    e.preventDefault();
    await submitForm('editGuestForm', 'api/guests.php?action=edit', () => {
        closeModal('editGuestModal');
        setTimeout(() => location.reload(), 600);
    });
}

// Update result count with filters
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#mainTable tbody tr');
    document.getElementById('resultCount').textContent = `Showing ${rows.length} of ${rows.length} submissions`;
});
</script>
