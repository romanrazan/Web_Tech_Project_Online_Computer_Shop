<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BrandModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';

function product_filter_data($conn) {
    return [
        'categories' => get_all_categories($conn),
        'brands' => get_all_brands($conn)
    ];
}

function show_customer_products_page($conn) {
    $filters = [
        'q' => trim($_GET['q'] ?? ''),
        'min_price' => trim($_GET['min_price'] ?? ''),
        'max_price' => trim($_GET['max_price'] ?? ''),
        'category_id' => (int)($_GET['category_id'] ?? 0),
        'brand_id' => (int)($_GET['brand_id'] ?? 0)
    ];

    $errors = [];

    if ($filters['min_price'] !== '' && (!is_numeric($filters['min_price']) || (float)$filters['min_price'] < 0)) {
        $errors[] = 'Minimum price must be a positive number.';
        $filters['min_price'] = '';
    }

    if ($filters['max_price'] !== '' && (!is_numeric($filters['max_price']) || (float)$filters['max_price'] < 0)) {
        $errors[] = 'Maximum price must be a positive number.';
        $filters['max_price'] = '';
    }

    if ($filters['min_price'] !== '' && $filters['max_price'] !== '' && (float)$filters['min_price'] > (float)$filters['max_price']) {
        $errors[] = 'Minimum price cannot be greater than maximum price.';
        $filters['min_price'] = '';
        $filters['max_price'] = '';
    }

    $products = get_public_products($conn, $filters);

    // 🔥 IMAGE PATH FIX
    foreach ($products as &$product) {
        if (!empty($product['image_path'])) {
            $product['image_path'] = 'public/uploads/products/' . $product['image_path'];
        }
    }
    unset($product);

    $filterData = product_filter_data($conn);
    $pageTitle = 'Products';
    require __DIR__ . '/../views/products/list.php';
}

function show_category_products_page($conn) {
    $category_id = (int)($_GET['id'] ?? $_GET['category_id'] ?? 0);

    if ($category_id <= 0) {
        set_flash('error', 'Invalid category selected.');
        redirect('index.php?page=products');
    }

    $category = find_category_by_id($conn, $category_id);

    if (!$category) {
        set_flash('error', 'Category not found.');
        redirect('index.php?page=products');
    }

    $filters = [
        'category_id' => $category_id,
        'q' => trim($_GET['q'] ?? ''),
        'min_price' => trim($_GET['min_price'] ?? ''),
        'max_price' => trim($_GET['max_price'] ?? ''),
        'brand_id' => (int)($_GET['brand_id'] ?? 0)
    ];

    $products = get_public_products($conn, $filters);

    // 🔥 IMAGE PATH FIX
    foreach ($products as &$product) {
        if (!empty($product['image_path'])) {
            $product['image_path'] = 'public/uploads/products/' . $product['image_path'];
        }
    }
    unset($product);

    $filterData = product_filter_data($conn);
    $pageTitle = 'Category: ' . $category['name'];
    require __DIR__ . '/../views/products/list.php';
}

function show_brand_products_page($conn) {
    $brand_id = (int)($_GET['id'] ?? $_GET['brand_id'] ?? 0);

    if ($brand_id <= 0) {
        set_flash('error', 'Invalid brand selected.');
        redirect('index.php?page=products');
    }

    $brand = find_brand_by_id($conn, $brand_id);

    if (!$brand) {
        set_flash('error', 'Brand not found.');
        redirect('index.php?page=products');
    }

    $filters = [
        'brand_id' => $brand_id,
        'q' => trim($_GET['q'] ?? ''),
        'min_price' => trim($_GET['min_price'] ?? ''),
        'max_price' => trim($_GET['max_price'] ?? ''),
        'category_id' => (int)($_GET['category_id'] ?? 0)
    ];

    $products = get_public_products($conn, $filters);

    // 🔥 IMAGE PATH FIX
    foreach ($products as &$product) {
        if (!empty($product['image_path'])) {
            $product['image_path'] = 'public/uploads/products/' . $product['image_path'];
        }
    }
    unset($product);

    $filterData = product_filter_data($conn);
    $pageTitle = 'Brand: ' . $brand['name'];
    require __DIR__ . '/../views/products/list.php';
}

function show_product_details_page($conn) {
    $product_id = (int)($_GET['id'] ?? 0);

    if ($product_id <= 0) {
        set_flash('error', 'Invalid product selected.');
        redirect('index.php?page=products');
    }

    $product = get_product_details($conn, $product_id);

    // 🔥 DETAILS PAGE FIX (optional but safe)
    if (!empty($product['image_path'])) {
        $product['image_path'] = 'public/uploads/products/' . $product['image_path'];
    }

    if (!$product) {
        set_flash('error', 'Product not found.');
        redirect('index.php?page=products');
    }

    $reviews = get_reviews_by_product($conn, $product_id);
    $pageTitle = $product['name'];
    require __DIR__ . '/../views/products/details.php';
}