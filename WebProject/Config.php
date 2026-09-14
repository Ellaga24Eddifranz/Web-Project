<?php

$host = "localhost";
$dbusername = "tpstudio_admin";
$dbpassword = "vRnksM2CXBvt7J5";
$dbname = "tpstudio";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'DB_CONNECTION_FAILED',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

?>