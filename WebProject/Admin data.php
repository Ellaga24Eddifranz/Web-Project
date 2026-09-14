<?php
// GET /admin-data.php
// Returns live stats + recent project briefs for the Admin Console.
// Only accessible to a logged-in user with role = 'admin'.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'FORBIDDEN', 'message' => 'Admin access required.']);
    exit;
}

require_once 'config.php';

// Total registered users
$totalUsers = 0;
if ($result = $conn->query('SELECT COUNT(*) AS c FROM users')) {
    $totalUsers = (int) $result->fetch_assoc()['c'];
}

// New sign ups in the last 7 days
$newSignups = 0;
if ($result = $conn->query('SELECT COUNT(*) AS c FROM users WHERE created_at >= NOW() - INTERVAL 7 DAY')) {
    $newSignups = (int) $result->fetch_assoc()['c'];
}

// Total logins across all users (all-time)
$totalLogins = 0;
if ($result = $conn->query("SELECT COUNT(*) AS c FROM activity_log WHERE activity = 'Logged in'")) {
    $totalLogins = (int) $result->fetch_assoc()['c'];
}

// Active projects (not yet completed)
$activeProjects = 0;
if ($result = $conn->query("SELECT COUNT(*) AS c FROM project_briefs WHERE status IN ('Pending', 'In Progress')")) {
    $activeProjects = (int) $result->fetch_assoc()['c'];
}

// Recent briefs for the management table
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
    'briefs' => $briefs
]);