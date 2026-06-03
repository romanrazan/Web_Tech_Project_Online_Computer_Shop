<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/UserModel.php';

header('Content-Type: application/json');

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only admin can delete customers.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token. Refresh and try again.']);
    exit;
}

$customer_id = (int)($_POST['customer_id'] ?? 0);
$customer = find_user_by_id($conn, $customer_id);

if (!$customer || ($customer['role'] ?? '') !== 'customer') {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
    exit;
}

if (!delete_customer_by_id($conn, $customer_id)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not delete customer.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Customer and related data deleted successfully.']);