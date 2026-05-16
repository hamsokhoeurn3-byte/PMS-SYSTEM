/* =============================================
   PMS — app.js
   ============================================= */

// ---- DROPDOWNS ----
function toggleNotifications() {
    document.getElementById('notifDropdown').classList.toggle('open');
    document.getElementById('profileDropdown').classList.remove('open');
    if (document.getElementById('notifDropdown').classList.contains('open')) {
        loadNotifications();
    }
}

function toggleProfile() {
    document.getElementById('profileDropdown').classList.toggle('open');
    document.getElementById('notifDropdown').classList.remove('open');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#notifWrapper')) {
        document.getElementById('notifDropdown')?.classList.remove('open');
    }
    if (!e.target.closest('#profileWrapper')) {
        document.getElementById('profileDropdown')?.classList.remove('open');
    }
    if (!e.target.closest('.sidebar') && !e.target.closest('.menu-toggle')) {
        document.getElementById('sidebar')?.classList.remove('open');
    }
});

// ---- NOTIFICATIONS ----
async function loadNotifications() {
    try {
        const res = await fetch('api/notifications.php?action=list');
        const data = await res.json();
        const list = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');

        if (!data.notifications || data.notifications.length === 0) {
            list.innerHTML = '<div class="notif-empty">No notifications yet</div>';
            badge.style.display = 'none';
            return;
        }

        const unread = data.notifications.filter(n => !n.is_read).length;
        if (unread > 0) {
            badge.textContent = unread;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }

        list.innerHTML = data.notifications.map(n => `
            <div class="notif-item ${!n.is_read ? 'unread' : ''}" onclick="openNotif(${n.property_id}, ${n.id})">
                ${!n.is_read ? '<span class="notif-dot"></span>' : '<span style="width:8px;flex-shrink:0"></span>'}
                <div class="notif-content">
                    <p>${escHtml(n.property_name)}</p>
                    <span>${escHtml(n.message)} &nbsp;·&nbsp; ${escHtml(n.id_booking || '')} &nbsp;·&nbsp; ${escHtml(n.created_at || '')}</span>
                </div>
            </div>
        `).join('');
    } catch(e) {
        console.error('Notifications error', e);
    }
}

async function markAllRead() {
    await fetch('api/notifications.php?action=mark_all_read', { method: 'POST' });
    loadNotifications();
}

function openNotif(propertyId, notifId) {
    fetch(`api/notifications.php?action=mark_read&id=${notifId}`, { method: 'POST' });
    window.location.href = `index.php?page=property-detail&id=${propertyId}`;
}

// Load badge count on page load
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const res = await fetch('api/notifications.php?action=unread_count');
        const data = await res.json();
        const badge = document.getElementById('notifBadge');
        if (badge && data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'flex';
        }
    } catch(e) {}
});

// ---- MODALS ----
function openModal(id) {
    document.getElementById(id)?.classList.add('open');
    document.getElementById('modalOverlay')?.classList.add('open');
}
function closeModal(id) {
    if (id) {
        document.getElementById(id)?.classList.remove('open');
    } else {
        document.querySelectorAll('.modal.open').forEach(m => m.classList.remove('open'));
    }
    document.getElementById('modalOverlay')?.classList.remove('open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// ---- TOAST ----
function showToast(msg, type = 'success') {
    let el = document.getElementById('toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'toast';
        document.body.appendChild(el);
    }
    el.textContent = msg;
    el.className = `show ${type}`;
    setTimeout(() => { el.classList.remove('show'); }, 3000);
}

// ---- HTML ESCAPE ----
function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ---- SEARCH FILTER (client-side table filtering) ----
function initTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        let visible = 0;
        rows.forEach(row => {
            const match = row.textContent.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        const counter = document.getElementById('resultCount');
        if (counter) counter.textContent = `Showing ${visible} of ${rows.length} results`;
    });
}

// ---- PROPERTY FILTER ----
function filterByProperty(selectId, tableId, colIndex) {
    const select = document.getElementById(selectId);
    if (!select) return;
    select.addEventListener('change', applyFilters);
}

function applyFilters() {
    const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const property = document.getElementById('propertyFilter')?.value || '';
    const dateStart = document.getElementById('dateStart')?.value || '';
    const dateEnd = document.getElementById('dateEnd')?.value || '';
    const table = document.getElementById('mainTable');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    let visible = 0;
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const propCell = row.dataset.property || '';
        const dateCell = row.dataset.date || '';

        const matchSearch = !search || text.includes(search);
        const matchProp = !property || property === 'All Properties' || propCell === property;
        const matchStart = !dateStart || dateCell >= dateStart;
        const matchEnd = !dateEnd || dateCell <= dateEnd;

        const show = matchSearch && matchProp && matchStart && matchEnd;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const counter = document.getElementById('resultCount');
    if (counter) counter.textContent = `Showing ${visible} of ${rows.length} submissions`;
}

// ---- DATE OF BIRTH / AGE CALCULATION ----
function calcAge(dobInput, ageInput) {
    const dob = document.getElementById(dobInput);
    const age = document.getElementById(ageInput);
    if (!dob || !age) return;
    dob.addEventListener('change', function() {
        if (!this.value) { age.value = ''; return; }
        const birthDate = new Date(this.value);
        const today = new Date();
        let a = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) a--;
        age.value = a;
    });
}

// ---- PASSPORT UPLOAD TOGGLE ----
function initJapanAddressToggle() {
    const radios = document.querySelectorAll('input[name="has_japan_address"]');
    const passportBox = document.getElementById('passportBox');
    if (!radios.length || !passportBox) return;
    radios.forEach(r => {
        r.addEventListener('change', function() {
            passportBox.style.display = this.value === 'no' ? 'block' : 'none';
        });
    });
}

// ---- CONFIRM DELETE ----
function confirmDelete(url, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        window.location.href = url;
    }
}

// ---- AJAX FORM SUBMIT ----
async function submitForm(formId, url, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;
    const data = new FormData(form);
    try {
        const res = await fetch(url, { method: 'POST', body: data });
        const json = await res.json();
        if (json.success) {
            showToast(json.message || 'Saved successfully!', 'success');
            if (onSuccess) onSuccess(json);
        } else {
            showToast(json.error || 'An error occurred', 'error');
        }
    } catch(e) {
        showToast('Network error', 'error');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    calcAge('dob_input', 'age_input');
    initJapanAddressToggle();
});
