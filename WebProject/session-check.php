<?php
// GET /session-check.php
// Reads the real PHP session (via the browser's session cookie) and
// reports whether the visitor is logged in, and as whom. This is what
// every page asks on load to decide whether to show LOG-IN/SIGN-UP or
// the profile icon — no localStorage involved, so it can't drift out
// of sync with the actual server-side session.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

echo json_encode([
    'loggedIn' => true,
    'user' => [
        'firstName' => $_SESSION['first_name'],
        'lastName'  => $_SESSION['last_name'],
        'email'     => $_SESSION['email'],
        'role'      => $_SESSION['role']
    ]
]);