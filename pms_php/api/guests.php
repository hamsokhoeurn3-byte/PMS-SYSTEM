<?php
require_once __DIR__ . '/../includes/config.php';

$action = $_GET['action'] ?? '';
$db = getDB();

// REGISTER (public — guest self-registration)
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertySlug = trim($_POST['property_slug'] ?? '');
    $name         = trim($_POST['name'] ?? '');
    $idBooking    = trim($_POST['id_booking'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $dob          = $_POST['date_of_birth'] ?? '';
    $age          = (int)($_POST['age'] ?? 0);
    $nationality  = trim($_POST['nationality'] ?? '');
    $occupation   = trim($_POST['occupation'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $room         = trim($_POST['room_number'] ?? '');
    $checkIn      = $_POST['check_in'] ?? '';
    $checkOut     = $_POST['check_out'] ?? '';
    $prevStay     = trim($_POST['previous_stay'] ?? '');
    $nextStay     = trim($_POST['next_stay'] ?? '');
    $japanAddr    = $_POST['has_japan_address'] ?? 'no';

    // Resolve property
    $stmt = $db->prepare('SELECT id, name FROM properties WHERE slug = ? LIMIT 1');
    $stmt->execute([$propertySlug]);
    $property = $stmt->fetch();
    if (!$property) {
        $_SESSION['reg_error'] = 'Invalid property.';
        header('Location: ../index.php?page=register&slug=' . urlencode($propertySlug));
        exit;
    }

    // Passport upload
    $passportPath = null;
    if ($japanAddr === 'no' && isset($_FILES['passport']) && $_FILES['passport']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array(strtolower($ext), $allowed)) {
            $_SESSION['reg_error'] = 'Invalid file type. Use JPG, PNG, or PDF.';
            header('Location: ../index.php?page=register&slug=' . urlencode($propertySlug));
            exit;
        }
        if ($_FILES['passport']['size'] > MAX_UPLOAD_SIZE) {
            $_SESSION['reg_error'] = 'File too large (max 5MB).';
            header('Location: ../index.php?page=register&slug=' . urlencode($propertySlug));
            exit;
        }
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
        $filename = uniqid('passport_') . '.' . $ext;
        move_uploaded_file($_FILES['passport']['tmp_name'], UPLOAD_DIR . $filename);
        $passportPath = $filename;
    }

    try {
        $stmt = $db->prepare('INSERT INTO guests 
            (property_id, name, id_booking, phone, email, date_of_birth, age, nationality, occupation, address, room_number, check_in, check_out, previous_stay_location, next_stay_location, has_japan_address, passport_photo, submission_date) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURDATE())');
        $stmt->execute([$property['id'], $name, $idBooking, $phone, $email, $dob ?: null, $age, $nationality, $occupation, $address, $room, $checkIn ?: null, $checkOut ?: null, $prevStay, $nextStay, $japanAddr, $passportPath]);

        // Create notification
        $db->prepare('INSERT INTO notifications (property_id, property_name, guest_name, guest_email, id_booking, message, type) VALUES (?,?,?,?,?,?,?)')
           ->execute([$property['id'], $property['name'], $name, $email, $idBooking, "New guest registration from $name", 'guest_submission']);

        $_SESSION['reg_success'] = $email;
        header('Location: ../index.php?page=register&slug=' . urlencode($propertySlug) . '&submitted=1');
        exit;
    } catch (PDOException $e) {
        $_SESSION['reg_error'] = 'Booking ID already exists or database error.';
        header('Location: ../index.php?page=register&slug=' . urlencode($propertySlug));
        exit;
    }
}

// EDIT guest (staff/admin)
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin();
    $id          = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $occupation  = trim($_POST['occupation'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $room        = trim($_POST['room_number'] ?? '');
    $checkIn     = $_POST['check_in'] ?? '';
    $checkOut    = $_POST['check_out'] ?? '';
    $prevStay    = trim($_POST['previous_stay'] ?? '');
    $nextStay    = trim($_POST['next_stay'] ?? '');

    $db->prepare('UPDATE guests SET name=?,phone=?,email=?,nationality=?,occupation=?,address=?,room_number=?,check_in=?,check_out=?,previous_stay_location=?,next_stay_location=? WHERE id=?')
       ->execute([$name, $phone, $email, $nationality, $occupation, $address, $room, $checkIn ?: null, $checkOut ?: null, $prevStay, $nextStay, $id]);
    jsonResponse(['success' => true, 'message' => 'Guest updated successfully!']);
}

// DELETE guest
if ($action === 'delete') {
    requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { header('Location: ../index.php?page=guest-submissions&error=invalid'); exit; }
    $db->prepare('DELETE FROM guests WHERE id = ?')->execute([$id]);
    header('Location: ../index.php?page=guest-submissions&success=deleted');
    exit;
}
