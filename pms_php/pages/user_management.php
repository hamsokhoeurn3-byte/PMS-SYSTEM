<?php
// Auth already checked in index.php before any HTML output
$db = getDB();
$users = $db->query('SELECT * FROM users ORDER BY id')->fetchAll();
?>

<div class="page-header">
    <div>
        <h2>User Management</h2>
        <p>Manage system users and their permissions.</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openModal('addUserModal')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create User
        </button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> User deleted successfully.</div>
<?php endif; ?>

<div class="filter-bar">
    <div class="filter-group" style="flex:1;">
        <label>Search Users</label>
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" class="form-control" placeholder="Search by username, email, or role..." oninput="applyFilters()">
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table" id="mainTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="bold"><?= h($u['username']) ?></td>
                    <td><?= h($u['email']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'Admin' ? 'badge-purple' : 'badge-blue' ?>">
                            <?= h($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $u['status'] === 'Active' ? 'badge-green' : 'badge-gray' ?>">
                            <?= h($u['status']) ?>
                        </span>
                    </td>
                    <td><?= h(date('Y-m-d', strtotime($u['created_at']))) ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="edit-btn" onclick='openEditUser(<?= json_encode($u) ?>)' title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button class="del-btn" title="Delete" onclick="confirmDelete('api/users.php?action=delete&id=<?= $u['id'] ?>', '<?= h(addslashes($u['username'])) ?>')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD USER MODAL -->
<div class="modal" id="addUserModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Create New User</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="addUserForm" onsubmit="submitUserForm(event,'add')">
            <div class="modal-body">
                <div class="form-row">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal" id="editUserModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit User</h3>
            <button class="modal-close" onclick="closeModal('editUserModal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editUserForm" onsubmit="submitUserForm(event,'edit')">
            <input type="hidden" name="id" id="eu_id">
            <div class="modal-body">
                <div class="form-row">
                    <label>Username</label>
                    <input type="text" id="eu_username" class="form-control" disabled style="background:var(--gray-50)">
                </div>
                <div class="form-row">
                    <label>Email *</label>
                    <input type="email" name="email" id="eu_email" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>New Password <small style="color:var(--gray-400)">(leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="New password (optional)">
                </div>
                <div class="form-grid">
                    <div class="form-row">
                        <label>Role</label>
                        <select name="role" id="eu_role" class="form-control">
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Status</label>
                        <select name="status" id="eu_status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditUser(u) {
    document.getElementById('eu_id').value       = u.id;
    document.getElementById('eu_username').value = u.username;
    document.getElementById('eu_email').value    = u.email;
    document.getElementById('eu_role').value     = u.role;
    document.getElementById('eu_status').value   = u.status;
    openModal('editUserModal');
}
async function submitUserForm(e, action) {
    e.preventDefault();
    const formId = action === 'add' ? 'addUserForm' : 'editUserForm';
    await submitForm(formId, `api/users.php?action=${action}`, () => {
        closeModal();
        setTimeout(() => location.reload(), 600);
    });
}
</script>
