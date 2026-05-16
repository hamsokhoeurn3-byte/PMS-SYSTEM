<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$action = $_GET['action'] ?? '';
$db = getDB();

// ADD property
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireAdmin();
    $name     = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status   = $_POST['status'] ?? 'Active';

    if (!$name || !$location) {
        jsonResponse(['success' => false, 'error' => 'Name and location are required.']);
    }

    // Generate slug
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name)) . '-' . time();
    $stmt = $db->prepare('INSERT INTO properties (name, location, slug, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $location, $slug, $status]);
    jsonResponse(['success' => true, 'message' => 'Property added successfully!', 'id' => $db->lastInsertId()]);
}

// EDIT property
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $name     = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status   = $_POST['status'] ?? 'Active';

    if (!$id || !$name || !$location) {
        jsonResponse(['success' => false, 'error' => 'All fields are required.']);
    }

    $db->prepare('UPDATE properties SET name=?, location=?, status=? WHERE id=?')
       ->execute([$name, $location, $status, $id]);
    jsonResponse(['success' => true, 'message' => 'Property updated successfully!']);
}

// DELETE property
if ($action === 'delete') {
    requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { header('Location: ../index.php?page=properties&error=invalid'); exit; }
    $db->prepare('DELETE FROM properties WHERE id = ?')->execute([$id]);
    header('Location: ../index.php?page=properties&success=deleted');
    exit;
}
