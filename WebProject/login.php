<?php
// login.php
// Checks email + password. If correct, starts a session so the person
// stays logged in on every page (no more localStorage tricks).

session_start();
header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'BAD_REQUEST', 'message' => 'Username and password are required.']);
    exit;
}

$emailLower = strtolower($username);

$stmt = $conn->prepare('SELECT first_name, last_name, email, password, role, avatar_path FROM users WHERE email = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('s', $emailLower);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $conn->close();
    http_response_code(404);
    echo json_encode(['error' => 'NO_ACCOUNT', 'message' => 'No account found for this username.']);
    exit;
}

if (!password_verify($password, $user['password'])) {
    $conn->close();
    http_response_code(401);
    echo json_encode(['error' => 'WRONG_PASSWORD', 'message' => 'Incorrect password.']);
    exit;
}

// save this login so it shows up in "Recent Activity" and the admin's login count
$logStmt = $conn->prepare('INSERT INTO activity_log (user_email, activity) VALUES (?, ?)');
if ($logStmt) {
    $activityText = 'Logged in';
    $logStmt->bind_param('ss', $user['email'], $activityText);
    $logStmt->execute();
    $logStmt->close();
}

$conn->close();

// save who's logged in — the browser gets a session cookie automatically
$_SESSION['user_id']     = $user['email']; // no id column yet, email is unique so this works
$_SESSION['first_name']  = $user['first_name'];
$_SESSION['last_name']   = $user['last_name'];
$_SESSION['email']       = $user['email'];
$_SESSION['role']        = $user['role'];
$_SESSION['avatar_path'] = $user['avatar_path'];

http_response_code(200);
echo json_encode([
    'message' => 'Login successful.',
    'user' => [
        'firstName'  => $user['first_name'],
        'lastName'   => $user['last_name'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'avatarPath' => $user['avatar_path']
    ]
]);