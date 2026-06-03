<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/CartModel.php';

header('Content-Type: application/json');

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login as customer first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token. Refresh the page and try again.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if (!remove_cart_item($conn, $user_id, $product_id)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not remove cart item.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Item removed from cart.',
    'cart_total' => number_format(get_cart_total($conn, $user_id), 2),
    'cart_count' => get_cart_count($conn, $user_id)
]);