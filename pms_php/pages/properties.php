<?php
$db = getDB();

// Flash messages
$success = $_GET['success'] ?? null;
$error   = $_GET['error'] ?? null;

// Fetch properties with guest count
$properties = $db->query('SELECT p.*, COUNT(g.id) as total_guests FROM properties p LEFT JOIN guests g ON g.property_id = p.id GROUP BY p.id ORDER BY p.id DESC')->fetchAll();
?>

<div class="page-header">
    <div>
        <h2>Properties</h2>
        <p>Manage all your properties in one place.</p>
    </div>
    <?php if (isAdmin()): ?>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openModal('addPropertyModal')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Property
        </button>
    </div>
    <?php endif; ?>
</div>

<?php if ($success === 'deleted'): ?>
<div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Property deleted successfully.</div>
<?php endif; ?>

<!-- Search -->
<div class="filter-bar">
    <div class="filter-group" style="flex:1;">
        <label>Search Properties</label>
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" class="form-control" placeholder="Search by name or location..." oninput="applyFilters()">
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data-table" id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Property Name</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Total Guests</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $i => $p): ?>
                <tr>
                    <td style="color:var(--gray-400);font-size:12px;"><?= $i + 1 ?></td>
                    <td class="bold"><?= h($p['name']) ?></td>
                    <td><?= h($p['location']) ?></td>
                    <td>
                        <span class="badge <?= $p['status'] === 'Active' ? 'badge-green' : 'badge-gray' ?>">
                            <?= h($p['status']) ?>
                        </span>
                    </td>
                    <td><?= number_format($p['total_guests']) ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="index.php?page=property-detail&id=<?= $p['id'] ?>" class="view-btn" title="View Details">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button class="edit-btn" title="Edit" onclick='openEditProperty(<?= json_encode($p) ?>)'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <?php if (isAdmin()): ?>
                            <button class="del-btn" title="Delete" onclick="confirmDelete('api/properties.php?action=delete&id=<?= $p['id'] ?>', '<?= h(addslashes($p['name'])) ?>')">
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

<!-- ADD PROPERTY MODAL -->
<div class="modal" id="addPropertyModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add New Property</h3>
            <button class="modal-close" onclick="closeModal('addPropertyModal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="addPropertyForm" onsubmit="submitPropertyForm(event, 'add')">
            <div class="modal-body">
                <div class="form-row">
                    <label>Property Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Sunset Villa" required>
                </div>
                <div class="form-row">
                    <label>Location *</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Los Angeles, CA" required>
                </div>
                <div class="form-row">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addPropertyModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Property</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT PROPERTY MODAL -->
<div class="modal" id="editPropertyModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Property</h3>
            <button class="modal-close" onclick="closeModal('editPropertyModal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editPropertyForm" onsubmit="submitPropertyForm(event, 'edit')">
            <input type="hidden" name="id" id="editPropId">
            <div class="modal-body">
                <div class="form-row">
                    <label>Property Name *</label>
                    <input type="text" name="name" id="editPropName" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>Location *</label>
                    <input type="text" name="location" id="editPropLocation" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>Status</label>
                    <select name="status" id="editPropStatus" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editPropertyModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditProperty(p) {
    document.getElementById('editPropId').value = p.id;
    document.getElementById('editPropName').value = p.name;
    document.getElementById('editPropLocation').value = p.location;
    document.getElementById('editPropStatus').value = p.status;
    openModal('editPropertyModal');
}

async function submitPropertyForm(e, action) {
    e.preventDefault();
    const formId = action === 'add' ? 'addPropertyForm' : 'editPropertyForm';
    await submitForm(formId, `api/properties.php?action=${action}`, () => {
        closeModal();
        setTimeout(() => location.reload(), 600);
    });
}
</script>
