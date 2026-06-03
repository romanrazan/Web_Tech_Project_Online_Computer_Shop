<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/ReviewModel.php';

header('Content-Type: application/json');

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only admin can delete reviews.']);
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

$review_id = (int)($_POST['review_id'] ?? 0);
if (!find_review_by_id($conn, $review_id)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Review not found.']);
    exit;
}

if (!delete_review($conn, $review_id)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not delete review.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Review deleted successfully.']);