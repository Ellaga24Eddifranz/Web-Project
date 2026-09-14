<?php
// logout.php
// Ends the session — this is what actually logs someone out.

session_start();
$_SESSION = [];
session_destroy();

header('Content-Type: application/json');
echo json_encode(['message' => 'Logged out.']);