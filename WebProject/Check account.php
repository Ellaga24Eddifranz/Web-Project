<?php
// POST /check-account.php
// Expects JSON: { email }
// Used to re-validate a client-side "logged in" session against the
// actual database — e.g. if an account was deleted after the person
// logged in, this is what lets the site notice and log them out.

header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$email = strtolower(trim($data['email'] ?? ''));

if ($email === '') {
    http_response_code(400);
    echo json_encode(['error' => 'BAD_REQUEST']);
    exit;
}

$stmt = $conn->prepare('SELECT first_name, last_name, email, role FROM users WHERE email = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    http_response_code(200);
    echo json_encode(['exists' => false]);
    exit;
}

echo json_encode([
    'exists' => true,
    'user' => [
        'firstName' => $user['first_name'],
        'lastName'  => $user['last_name'],
        'email'     => $user['email'],
        'role'      => $user['role']
    ]
]);