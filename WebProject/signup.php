<?php
// signup.php
// Creates a new account.

header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$firstName     = trim($data['firstName'] ?? '');
$lastName      = trim($data['lastName'] ?? '');
$email         = trim($data['email'] ?? '');
$password      = $data['password'] ?? '';
$phone         = trim($data['phone'] ?? '');
$address       = trim($data['address'] ?? '');
$cityStateZip  = trim($data['cityStateZip'] ?? '');
$paymentMethod = trim($data['paymentMethod'] ?? '');

if ($firstName === '' || $lastName === '' || $email === '' || $password === '' ||
    $phone === '' || $address === '' || $cityStateZip === '' || $paymentMethod === '') {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please enter a valid email address.']);
    exit;
}

$allowedPayments = ['paypal', 'gcash', 'cash'];
if (!in_array($paymentMethod, $allowedPayments, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please choose a valid payment method.']);
    exit;
}

$emailLower = strtolower($email);

// check email isn't already used
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('s', $emailLower);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->close();
    http_response_code(409);
    echo json_encode(['error' => 'An account with this email already exists.']);
    exit;
}
$stmt->close();

// hash the password, never store the real one
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    'INSERT INTO users
        (first_name, last_name, email, password, phone_number, address, city_state_zip, payment_method)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param(
    'ssssssss',
    $firstName, $lastName, $emailLower, $passwordHash,
    $phone, $address, $cityStateZip, $paymentMethod
);

if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    http_response_code(500);
    echo json_encode(['error' => 'Something went wrong. Please try again.']);
    exit;
}

// log this so it shows up later in the user's "Recent Activity"
$logStmt = $conn->prepare('INSERT INTO activity_log (user_email, activity) VALUES (?, ?)');
if ($logStmt) {
    $activityText = 'Created account';
    $logStmt->bind_param('ss', $emailLower, $activityText);
    $logStmt->execute();
    $logStmt->close();
}

$stmt->close();
$conn->close();

http_response_code(201);
echo json_encode(['message' => 'Account created.']);