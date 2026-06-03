<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/ProductModel.php';

header('Content-Type: application/json');

$filters = [
    'q' => trim($_GET['q'] ?? ''),
    'category_id' => (int)($_GET['category_id'] ?? 0),
    'brand_id' => (int)($_GET['brand_id'] ?? 0),
    'min_price' => trim($_GET['min_price'] ?? ''),
    'max_price' => trim($_GET['max_price'] ?? '')
];

$errors = [];

if ($filters['min_price'] !== '' && (!is_numeric($filters['min_price']) || (float)$filters['min_price'] < 0)) {
    $errors[] = 'Minimum price must be a positive number.';
}

if ($filters['max_price'] !== '' && (!is_numeric($filters['max_price']) || (float)$filters['max_price'] < 0)) {
    $errors[] = 'Maximum price must be a positive number.';
}

if ($filters['min_price'] !== '' && $filters['max_price'] !== '' && (float)$filters['min_price'] > (float)$filters['max_price']) {
    $errors[] = 'Minimum price cannot be greater than maximum price.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
    exit;
}

$products = get_public_products($conn, $filters);

foreach ($products as &$product) {
    $product['price_formatted'] = number_format((float)$product['price'], 2);
    $product['review_short'] = strlen($product['manufacturer_review']) > 95
        ? substr($product['manufacturer_review'], 0, 95) . '...'
        : $product['manufacturer_review'];
}

unset($product);

echo json_encode([
    'success' => true,
    'products' => $products,
    'count' => count($products),
    'logged_in' => is_logged_in(),
    'role' => $_SESSION['role'] ?? 'guest'
]);