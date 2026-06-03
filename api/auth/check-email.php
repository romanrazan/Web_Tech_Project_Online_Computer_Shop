<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/UserModel.php';

header('Content-Type: application/json');

$email = trim($_GET['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'exists' => false,
        'message' => 'Invalid email.'
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'exists' => email_exists($conn, $email)
]);
