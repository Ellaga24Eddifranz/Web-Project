<?php
// user-data.php
// Sends back this user's own activity log and their own submitted
// briefs, for their dashboard. Only ever shows their own data.

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'NOT_LOGGED_IN']);
    exit;
}

require_once 'config.php';

$userEmail = $_SESSION['email'];

$activity = [];
$stmt = $conn->prepare('SELECT activity, created_at FROM activity_log WHERE user_email = ? ORDER BY created_at DESC LIMIT 10');
if ($stmt) {
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $activity[] = [
            'activity' => $row['activity'],
            'date' => date('M j, Y g:i A', strtotime($row['created_at']))
        ];
    }
    $stmt->close();
}

$projects = [];
$stmt = $conn->prepare('SELECT title, description FROM project_briefs WHERE user_email = ? ORDER BY created_at DESC');
if ($stmt) {
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    $stmt->close();
}

$conn->close();

echo json_encode([
    'activity' => $activity,
    'projects' => $projects
]);