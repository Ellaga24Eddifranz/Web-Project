<?php
// session-check.php
// Tells the page "are you logged in, and who are you?" by reading the
// session. Every page calls this on load so the header/dashboard know
// what to show.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

echo json_encode([
    'loggedIn' => true,
    'user' => [
        'firstName'  => $_SESSION['first_name'],
        'lastName'   => $_SESSION['last_name'],
        'email'      => $_SESSION['email'],
        'role'       => $_SESSION['role'],
        'avatarPath' => $_SESSION['avatar_path'] ?? null
    ]
]);