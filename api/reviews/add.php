<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/ReviewModel.php';

header('Content-Type: application/json');

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login as customer to post a review.']);
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

$product_id = (int)($_POST['product_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$user_id = (int)$_SESSION['user_id'];
$reviewer_name = trim($_SESSION['name'] ?? 'Customer');

if ($product_id <= 0 || !get_product_details($conn, $product_id)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

if ($comment === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Review comment cannot be empty.']);
    exit;
}

if (strlen($comment) > 500) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Review comment must be within 500 characters.']);
    exit;
}

if (!create_review($conn, $product_id, $user_id, $reviewer_name, $comment)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save review.']);
    exit;
}

$review_id = $conn->insert_id;

echo json_encode([
    'success' => true,
    'message' => 'Review posted successfully.',
    'review' => [
        'id' => $review_id,
        'user_id' => $user_id,
        'reviewer_name' => $reviewer_name,
        'comment' => $comment,
        'created_at' => date('d M Y, h:i A')
    ]
]);