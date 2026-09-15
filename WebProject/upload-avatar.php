<?php
// upload-avatar.php
// Saves a profile picture for whoever is logged in. One picture per
// account, stored on the server and saved to the users table — so it
// always shows the right picture for the right account, on any page,
// after any login.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'NOT_LOGGED_IN', 'message' => 'Please log in first.']);
    exit;
}

require_once 'config.php';

if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'BAD_REQUEST', 'message' => 'No image received.']);
    exit;
}

$allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
$mime = mime_content_type($_FILES['avatar']['tmp_name']);

if (!isset($allowedTypes[$mime])) {
    http_response_code(400);
    echo json_encode(['error' => 'BAD_FILE_TYPE', 'message' => 'Please upload a JPG or PNG image.']);
    exit;
}

$maxBytes = 5 * 1024 * 1024; // 5MB is plenty for a profile picture
if ($_FILES['avatar']['size'] > $maxBytes) {
    http_response_code(400);
    echo json_encode(['error' => 'TOO_BIG', 'message' => 'Image must be under 5MB.']);
    exit;
}

$userEmail = $_SESSION['email'];
$ext = $allowedTypes[$mime];

// name the file after the account so a new upload just replaces the old
// picture instead of piling up files forever
$safeName = md5($userEmail) . '.' . $ext;
$uploadDir = __DIR__ . '/uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$destPath = $uploadDir . $safeName;

// clean up an old picture if this account previously uploaded the other file type
foreach (['jpg', 'png'] as $oldExt) {
    $oldFile = $uploadDir . md5($userEmail) . '.' . $oldExt;
    if ($oldExt !== $ext && file_exists($oldFile)) {
        unlink($oldFile);
    }
}

if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'UPLOAD_FAILED', 'message' => 'Could not save the image.']);
    exit;
}

// the ?v= bit forces the browser to grab the new picture instead of an old cached one
$relativePath = 'uploads/avatars/' . $safeName . '?v=' . time();

$stmt = $conn->prepare('UPDATE users SET avatar_path = ? WHERE email = ?');
if ($stmt) {
    $stmt->bind_param('ss', $relativePath, $userEmail);
    $stmt->execute();
    $stmt->close();
}
$conn->close();

// keep the session in sync too so the picture shows up right away everywhere
$_SESSION['avatar_path'] = $relativePath;

echo json_encode([
    'message' => 'Avatar updated.',
    'avatarPath' => $relativePath
]);