<?php
// POST /logout.php
// Destroys the real PHP session server-side. This is what actually
// logs someone out now — clearing localStorage alone wouldn't be
// enough once the server is the source of truth.

session_start();
$_SESSION = [];
session_destroy();

header('Content-Type: application/json');
echo json_encode(['message' => 'Logged out.']);