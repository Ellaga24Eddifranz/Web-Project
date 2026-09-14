<?php
// POST /login.php
// Expects JSON: { username, password } — "username" here is the email
// the person signed up with (matches the front-end's login form).
//
// On success, this starts a real PHP session ($_SESSION) so the
// browser's session cookie carries the logged-in state across every
// page automatically — no more relying on the front-end's localStorage
// to remember who's logged in.

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

$stmt = $conn->prepare('SELECT first_name, last_name, email, password, role FROM users WHERE email = ?');
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

// Log this login — powers "Recent Activity" on the user dashboard and
// the "Total Logins" stat on the admin dashboard.
$logStmt = $conn->prepare('INSERT INTO activity_log (user_email, activity) VALUES (?, ?)');
if ($logStmt) {
    $activityText = 'Logged in';
    $logStmt->bind_param('ss', $user['email'], $activityText);
    $logStmt->execute();
    $logStmt->close();
}

$conn->close();

// Real server-side session — this is what actually persists across
// pages, via the PHPSESSID cookie the browser sends automatically.
$_SESSION['user_id']    = $user['email']; // no numeric id column yet; email is unique so it works as the session key
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name']  = $user['last_name'];
$_SESSION['email']      = $user['email'];
$_SESSION['role']       = $user['role'];

http_response_code(200);
echo json_encode([
    'message' => 'Login successful.',
    'user' => [
        'firstName' => $user['first_name'],
        'lastName'  => $user['last_name'],
        'email'     => $user['email'],
        'role'      => $user['role']
    ]
]);