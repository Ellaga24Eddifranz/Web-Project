<?php
// admin-data.php
// Sends back the numbers, the list of registered users, and the list
// of briefs for the Admin Console. Only works if you're logged in as
// an admin — anyone else gets refused.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'FORBIDDEN', 'message' => 'Admin access required.']);
    exit;
}

require_once 'config.php';

// how many people have signed up
$totalUsers = 0;
if ($result = $conn->query('SELECT COUNT(*) AS c FROM users')) {
    $totalUsers = (int) $result->fetch_assoc()['c'];
}

// signups in the last 7 days
$newSignups = 0;
if ($result = $conn->query('SELECT COUNT(*) AS c FROM users WHERE created_at >= NOW() - INTERVAL 7 DAY')) {
    $newSignups = (int) $result->fetch_assoc()['c'];
}

// how many times anyone has logged in, ever
$totalLogins = 0;
if ($result = $conn->query("SELECT COUNT(*) AS c FROM activity_log WHERE activity = 'Logged in'")) {
    $totalLogins = (int) $result->fetch_assoc()['c'];
}

// briefs that aren't finished yet
$activeProjects = 0;
if ($result = $conn->query("SELECT COUNT(*) AS c FROM project_briefs WHERE status IN ('Pending', 'In Progress')")) {
    $activeProjects = (int) $result->fetch_assoc()['c'];
}

// the list of registered users, newest first
$users = [];
if ($result = $conn->query('SELECT first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 50')) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email'],
            'role' => $row['role'],
            'joined' => date('M j, Y', strtotime($row['created_at']))
        ];
    }
}

// the list of briefs for the management table
$briefs = [];
if ($result = $conn->query('SELECT title, status FROM project_briefs ORDER BY created_at DESC LIMIT 20')) {
    while ($row = $result->fetch_assoc()) {
        $briefs[] = $row;
    }
}

$conn->close();

echo json_encode([
    'stats' => [
        'totalUsers' => $totalUsers,
        'newSignups' => $newSignups,
        'totalLogins' => $totalLogins,
        'activeProjects' => $activeProjects
    ],
    'users' => $users,
    'briefs' => $briefs
]);