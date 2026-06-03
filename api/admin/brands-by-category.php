<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/BrandModel.php';

header('Content-Type: application/json');

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Admin access required.'
    ]);
    exit();
}

$category_id = (int)($_GET['category_id'] ?? 0);

if ($category_id <= 0) {
    echo json_encode([
        'success' => false,
        'brands' => [],
        'message' => 'Invalid category.'
    ]);
    exit();
}

$brands = get_brands_by_category($conn, $category_id);

echo json_encode([
    'success' => true,
    'brands' => $brands
]);
