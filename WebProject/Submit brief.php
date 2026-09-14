<?php
// POST /submit-brief.php
// Expects multipart/form-data: title, description, files[] (0+ files)
// Requires an active login session — briefs are tied to the submitting
// user's email so they show up in that user's "My Project" dashboard
// panel and in the admin's "Project Brief Management" table.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'NOT_LOGGED_IN', 'message' => 'Please log in to submit a project brief.']);
    exit;
}

require_once 'config.php';

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '' || $description === '') {
    http_response_code(400);
    echo json_encode(['error' => 'BAD_REQUEST', 'message' => 'Title and description are required.']);
    exit;
}

$userEmail = $_SESSION['email'];

$stmt = $conn->prepare('INSERT INTO project_briefs (user_email, title, description, status) VALUES (?, ?, ?, ?)');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}
$status = 'Pending';
$stmt->bind_param('ssss', $userEmail, $title, $description, $status);
if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    http_response_code(500);
    echo json_encode(['error' => 'Something went wrong saving the brief.']);
    exit;
}
$briefId = $stmt->insert_id;
$stmt->close();

// Handle uploaded files, if any
$uploadedFiles = [];
if (!empty($_FILES['files']) && is_array($_FILES['files']['name'])) {
    $uploadDir = __DIR__ . '/uploads/briefs/' . $briefId . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedExt = ['jpg', 'jpeg', 'png', 'mp4'];
    $fileCount = count($_FILES['files']['name']);
    $fileStmt = $conn->prepare('INSERT INTO brief_files (brief_id, file_name, stored_path) VALUES (?, ?, ?)');

    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        $originalName = basename($_FILES['files']['name'][$i]);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            continue;
        }
        $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $originalName);
        $destPath = $uploadDir . $safeName;

        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $destPath)) {
            $relativePath = 'uploads/briefs/' . $briefId . '/' . $safeName;
            if ($fileStmt) {
                $fileStmt->bind_param('iss', $briefId, $originalName, $relativePath);
                $fileStmt->execute();
            }
            $uploadedFiles[] = $originalName;
        }
    }
    if ($fileStmt) {
        $fileStmt->close();
    }
}

// Log the activity — powers "Recent Activity" on the user dashboard.
$logStmt = $conn->prepare('INSERT INTO activity_log (user_email, activity) VALUES (?, ?)');
if ($logStmt) {
    $activityText = 'Submitted project brief: ' . $title;
    $logStmt->bind_param('ss', $userEmail, $activityText);
    $logStmt->execute();
    $logStmt->close();
}

$conn->close();

http_response_code(201);
echo json_encode([
    'message' => 'Brief submitted.',
    'briefId' => $briefId,
    'uploadedFiles' => $uploadedFiles
]);