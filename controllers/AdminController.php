<?php
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BrandModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

function show_admin_dashboard($conn) {
    require_admin();

    $totalProducts = count_all_products($conn);
    $totalCategories = count_all_categories($conn);
    $totalBrands = count_all_brands($conn);
    $lowStockProducts = get_low_stock_products($conn, 10);
    $recentOrders = get_recent_orders($conn, 5);
    $recentReviews = get_recent_reviews($conn, 5);

    $pageTitle = "Admin Dashboard";
    require __DIR__ . '/../views/admin/dashboard.php';
}



function show_admin_customers_page($conn) {
    require_admin();

    $customers = get_all_customers_admin($conn);
    foreach ($customers as &$customer) {
        $customer['order_count'] = count_customer_orders($conn, (int)$customer['id']);
        $customer['review_count'] = count_customer_reviews($conn, (int)$customer['id']);
    }
    unset($customer);

    $pageTitle = "Customer Management";
    require __DIR__ . '/../views/admin/customers.php';
}

function show_admin_reviews_page($conn) {
    require_admin();

    $reviews = get_all_reviews_admin($conn);
    $pageTitle = "Review Management";
    require __DIR__ . '/../views/admin/reviews.php';
}

function show_admin_orders_page($conn) {
    require_admin();

    $orders = get_all_orders_admin($conn);
    $pageTitle = "Order Management";
    require __DIR__ . '/../views/admin/orders.php';
}

function handle_admin_order_action($conn) {
    require_admin();

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid request. Please refresh and try again.');
        redirect('index.php?page=admin-orders');
    }

    $action = $_POST['action'] ?? '';
    $orderId = (int)($_POST['order_id'] ?? 0);

    if ($orderId <= 0) {
        set_flash('error', 'Invalid order selected.');
        redirect('index.php?page=admin-orders');
    }

    if ($action === 'accept_order') {
        if (accept_order_admin($conn, $orderId)) {
            set_flash('success', 'Order accepted successfully. Customer can now see the updated status.');
        } else {
            set_flash('error', 'Order could not be accepted. It may already be accepted or not found.');
        }
        redirect('index.php?page=admin-orders');
    }

    if ($action === 'delete_order') {
        if (delete_order_admin($conn, $orderId)) {
            set_flash('success', 'Order deleted successfully.');
        } else {
            set_flash('error', 'Order could not be deleted.');
        }
        redirect('index.php?page=admin-orders');
    }

    set_flash('error', 'Invalid order action.');
    redirect('index.php?page=admin-orders');
}

function show_categories_page($conn, $errors = [], $old = []) {
    require_admin();

    $editCategory = null;
    if (isset($_GET['edit'])) {
        $editCategory = find_category_by_id($conn, (int)$_GET['edit']);
    }

    $categories = get_all_categories($conn);
    $pageTitle = "Category Management";
    require __DIR__ . '/../views/admin/categories.php';
}

function handle_category_action($conn) {
    require_admin();

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid request. Please refresh and try again.');
        redirect('index.php?page=admin-categories');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'save_category') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $parent_id_raw = $_POST['parent_id'] ?? '';
        $parent_id = ($parent_id_raw === '') ? null : (int)$parent_id_raw;

        $errors = [];
        $old = ['id' => $id, 'name' => $name, 'parent_id' => $parent_id];

        if ($name === '') {
            $errors['name'] = 'Category name is required.';
        } elseif (category_name_exists($conn, $name, $id)) {
            $errors['name'] = 'This category name already exists.';
        }

        if ($id > 0 && $parent_id !== null && $id === $parent_id) {
            $errors['parent_id'] = 'A category cannot be parent of itself.';
        }

        if ($parent_id !== null && !find_category_by_id($conn, $parent_id)) {
            $errors['parent_id'] = 'Selected parent category does not exist.';
        }

        if (!empty($errors)) {
            show_categories_page($conn, $errors, $old);
            return;
        }

        if ($id > 0) {
            update_category($conn, $id, $name, $parent_id);
            set_flash('success', 'Category updated successfully.');
        } else {
            create_category($conn, $name, $parent_id);
            set_flash('success', 'Category created successfully.');
        }

        redirect('index.php?page=admin-categories');
    }

    if ($action === 'delete_category') {
        $id = (int)($_POST['id'] ?? 0);

        if (!find_category_by_id($conn, $id)) {
            set_flash('error', 'Category not found.');
            redirect('index.php?page=admin-categories');
        }

        if (count_child_categories($conn, $id) > 0) {
            set_flash('error', 'This category cannot be deleted because it has child categories.');
            redirect('index.php?page=admin-categories');
        }

        if (count_products_by_category($conn, $id) > 0) {
            set_flash('error', 'This category cannot be deleted because products exist under it.');
            redirect('index.php?page=admin-categories');
        }

        delete_category($conn, $id);
        set_flash('success', 'Category deleted successfully.');
        redirect('index.php?page=admin-categories');
    }

    set_flash('error', 'Invalid category action.');
    redirect('index.php?page=admin-categories');
}

function show_brands_page($conn, $errors = [], $old = []) {
    require_admin();

    $editBrand = null;
    if (isset($_GET['edit'])) {
        $editBrand = find_brand_by_id($conn, (int)$_GET['edit']);
    }

    $categories = get_all_categories($conn);
    $brands = get_all_brands($conn);
    $pageTitle = "Brand Management";
    require __DIR__ . '/../views/admin/brands.php';
}

function handle_brand_action($conn) {
    require_admin();

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid request. Please refresh and try again.');
        redirect('index.php?page=admin-brands');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'save_brand') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);

        $errors = [];
        $old = ['id' => $id, 'name' => $name, 'category_id' => $category_id];

        if ($name === '') {
            $errors['name'] = 'Brand name is required.';
        }

        if ($category_id <= 0 || !find_category_by_id($conn, $category_id)) {
            $errors['category_id'] = 'Please select a valid category.';
        }

        if (empty($errors) && brand_name_exists_in_category($conn, $name, $category_id, $id)) {
            $errors['name'] = 'This brand already exists under the selected category.';
        }

        if (!empty($errors)) {
            show_brands_page($conn, $errors, $old);
            return;
        }

        if ($id > 0) {
            update_brand($conn, $id, $name, $category_id);
            set_flash('success', 'Brand updated successfully.');
        } else {
            create_brand($conn, $name, $category_id);
            set_flash('success', 'Brand created successfully.');
        }

        redirect('index.php?page=admin-brands');
    }

    if ($action === 'delete_brand') {
        $id = (int)($_POST['id'] ?? 0);

        if (!find_brand_by_id($conn, $id)) {
            set_flash('error', 'Brand not found.');
            redirect('index.php?page=admin-brands');
        }

        if (count_products_by_brand($conn, $id) > 0) {
            set_flash('error', 'This brand cannot be deleted because products exist under it.');
            redirect('index.php?page=admin-brands');
        }

        delete_brand($conn, $id);
        set_flash('success', 'Brand deleted successfully.');
        redirect('index.php?page=admin-brands');
    }

    set_flash('error', 'Invalid brand action.');
    redirect('index.php?page=admin-brands');
}

function show_products_page($conn, $errors = [], $old = []) {
    require_admin();

    $editProduct = null;
    if (isset($_GET['edit'])) {
        $editProduct = find_product_by_id($conn, (int)$_GET['edit']);
    }

    $categories = get_all_categories($conn);
    $brands = get_all_brands($conn);
    $products = get_all_products_admin($conn);
    $pageTitle = "Product Management";
    require __DIR__ . '/../views/admin/products.php';
}

function validate_product_image_upload(&$errors) {
    if (empty($_FILES['image']['name'])) {
        return null;
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors['image'] = 'Image upload failed.';
        return null;
    }

    $maxSize = 2 * 1024 * 1024;
    if ($_FILES['image']['size'] > $maxSize) {
        $errors['image'] = 'Product image must be 2MB or smaller.';
        return null;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    $mime = mime_content_type($_FILES['image']['tmp_name']);
    if (!array_key_exists($mime, $allowed)) {
        $errors['image'] = 'Only JPEG and PNG images are allowed.';
        return null;
    }

    $uploadDir = __DIR__ . '/../public/uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $fileName = 'product_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $errors['image'] = 'Could not save uploaded image.';
        return null;
    }

    return 'public/uploads/products/' . $fileName;
}

function remove_product_image_file($image_path) {
    if (empty($image_path)) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $image_path;
    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

function handle_product_action($conn) {
    require_admin();

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid request. Please refresh and try again.');
        redirect('index.php?page=admin-products');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'save_product') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $manufacturer_review = trim($_POST['manufacturer_review'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category_id = (int)($_POST['category_id'] ?? 0);
        $brand_id = (int)($_POST['brand_id'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        $errors = [];
        $old = [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'manufacturer_review' => $manufacturer_review,
            'price' => $price,
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'stock' => $stock
        ];

        if ($name === '') {
            $errors['name'] = 'Product name is required.';
        }

        if ($description === '') {
            $errors['description'] = 'Description is required.';
        }

        if ($manufacturer_review === '') {
            $errors['manufacturer_review'] = 'Manufacturer review is required.';
        }

        if ($price <= 0) {
            $errors['price'] = 'Price must be greater than 0.';
        }

        if ($stock < 0) {
            $errors['stock'] = 'Stock cannot be negative.';
        }

        if ($category_id <= 0 || !find_category_by_id($conn, $category_id)) {
            $errors['category_id'] = 'Please select a valid category.';
        }

        $brand = ($brand_id > 0) ? find_brand_by_id($conn, $brand_id) : null;
        if (!$brand) {
            $errors['brand_id'] = 'Please select a valid brand.';
        } elseif ($category_id > 0 && (int)$brand['category_id'] !== $category_id) {
            $errors['brand_id'] = 'Selected brand does not belong to the selected category.';
        }

        $existingProduct = null;
        $oldImagePath = null;
        if ($id > 0) {
            $existingProduct = find_product_by_id($conn, $id);
            if (!$existingProduct) {
                set_flash('error', 'Product not found.');
                redirect('index.php?page=admin-products');
            }
            $oldImagePath = $existingProduct['image_path'];
        }

        $newImagePath = validate_product_image_upload($errors);
        $finalImagePath = $newImagePath ?: $oldImagePath;

        if (!empty($errors)) {
            if ($newImagePath) {
                remove_product_image_file($newImagePath);
            }
            show_products_page($conn, $errors, $old);
            return;
        }

        if ($id > 0) {
            update_product($conn, $id, $name, $description, $manufacturer_review, $price, $category_id, $brand_id, $finalImagePath, $stock);
            if ($newImagePath && $oldImagePath && $newImagePath !== $oldImagePath) {
                remove_product_image_file($oldImagePath);
            }
            set_flash('success', 'Product updated successfully.');
        } else {
            create_product($conn, $name, $description, $manufacturer_review, $price, $category_id, $brand_id, $finalImagePath, $stock);
            set_flash('success', 'Product created successfully.');
        }

        redirect('index.php?page=admin-products');
    }

    if ($action === 'delete_product') {
        $id = (int)($_POST['id'] ?? 0);
        $product = find_product_by_id($conn, $id);

        if (!$product) {
            set_flash('error', 'Product not found.');
            redirect('index.php?page=admin-products');
        }

        if (delete_product($conn, $id)) {
            remove_product_image_file($product['image_path']);
            set_flash('success', 'Product deleted successfully.');
        } else {
            set_flash('error', 'Could not delete product. It may be used in an order.');
        }

        redirect('index.php?page=admin-products');
    }

    set_flash('error', 'Invalid product action.');
    redirect('index.php?page=admin-products');
}
