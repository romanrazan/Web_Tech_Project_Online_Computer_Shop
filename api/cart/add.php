<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/ProductModel.php';
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
$quantity = (int)($_POST['quantity'] ?? 1);

if ($product_id <= 0 || $quantity <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    exit;
}

$product = get_product_details($conn, $product_id);

if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

$existing = get_cart_item($conn, $user_id, $product_id);
$current_quantity = $existing ? (int)$existing['quantity'] : 0;
$new_quantity = $current_quantity + $quantity;

if ($new_quantity > (int)$product['stock']) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Quantity cannot exceed available stock.']);
    exit;
}

if (!add_cart_item($conn, $user_id, $product_id, $quantity)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not add product to cart.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Product added to cart.',
    'cart_count' => get_cart_count($conn, $user_id),
    'cart_total' => number_format(get_cart_total($conn, $user_id), 2)
]);