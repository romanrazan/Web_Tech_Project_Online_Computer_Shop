<?php
function get_featured_products($conn, $limit = 6) {
    $sql = "SELECT p.id, p.name, p.manufacturer_review, p.price, p.image_path,
                   c.name AS category_name, b.name AS brand_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            ORDER BY p.created_at DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();

    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}

function get_all_products_admin($conn) {
    $sql = "SELECT p.id, p.name, p.description, p.manufacturer_review, p.price, p.category_id,
                   p.brand_id, p.image_path, p.stock, p.created_at,
                   c.name AS category_name, b.name AS brand_name
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            INNER JOIN brands b ON p.brand_id = b.id
            ORDER BY p.created_at DESC";
    $result = $conn->query($sql);

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}

function find_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function create_product($conn, $name, $description, $manufacturer_review, $price, $category_id, $brand_id, $image_path, $stock) {
    $stmt = $conn->prepare("INSERT INTO products (name, description, manufacturer_review, price, category_id, brand_id, image_path, stock)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdiisi", $name, $description, $manufacturer_review, $price, $category_id, $brand_id, $image_path, $stock);
    return $stmt->execute();
}

function update_product($conn, $id, $name, $description, $manufacturer_review, $price, $category_id, $brand_id, $image_path, $stock) {
    $stmt = $conn->prepare("UPDATE products
                            SET name = ?, description = ?, manufacturer_review = ?, price = ?, category_id = ?, brand_id = ?, image_path = ?, stock = ?
                            WHERE id = ?");
    $stmt->bind_param("sssdiisii", $name, $description, $manufacturer_review, $price, $category_id, $brand_id, $image_path, $stock, $id);
    return $stmt->execute();
}

function delete_product($conn, $product_id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    return $stmt->execute();
}

function count_all_products($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM products");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
}

function count_all_categories($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM categories");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
}

function count_all_brands($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM brands");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
}

function get_low_stock_products($conn, $limit = 10) {
    $stmt = $conn->prepare("SELECT id, name, stock FROM products WHERE stock < 5 ORDER BY stock ASC, name ASC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}


// Task 3 public/customer product functions.
function get_child_category_ids_recursive($conn, $category_id) {
    $ids = [(int)$category_id];
    $stmt = $conn->prepare("SELECT id FROM categories WHERE parent_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $child_id = (int)$row['id'];
        $ids = array_merge($ids, get_child_category_ids_recursive($conn, $child_id));
    }

    return array_values(array_unique($ids));
}

function get_public_products($conn, $filters = []) {
    $sql = "SELECT p.id, p.name, p.description, p.manufacturer_review, p.price, p.image_path, p.stock,
                   p.category_id, p.brand_id, c.name AS category_name, b.name AS brand_name
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            INNER JOIN brands b ON p.brand_id = b.id
            WHERE 1 = 1";
    $types = "";
    $params = [];

    if (!empty($filters['q'])) {
        $keyword = '%' . $filters['q'] . '%';
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.manufacturer_review LIKE ? OR b.name LIKE ? OR c.name LIKE ?)";
        $types .= "sssss";
        array_push($params, $keyword, $keyword, $keyword, $keyword, $keyword);
    }

    if (!empty($filters['category_id'])) {
        $category_ids = get_child_category_ids_recursive($conn, (int)$filters['category_id']);
        $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
        $sql .= " AND p.category_id IN ($placeholders)";
        $types .= str_repeat('i', count($category_ids));
        foreach ($category_ids as $cat_id) {
            $params[] = $cat_id;
        }
    }

    if (!empty($filters['brand_id'])) {
        $sql .= " AND p.brand_id = ?";
        $types .= "i";
        $params[] = (int)$filters['brand_id'];
    }

    if (isset($filters['min_price']) && $filters['min_price'] !== '') {
        $sql .= " AND p.price >= ?";
        $types .= "d";
        $params[] = (float)$filters['min_price'];
    }

    if (isset($filters['max_price']) && $filters['max_price'] !== '') {
        $sql .= " AND p.price <= ?";
        $types .= "d";
        $params[] = (float)$filters['max_price'];
    }

    $sql .= " ORDER BY p.created_at DESC";

    $stmt = $conn->prepare($sql);

    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}

function get_product_details($conn, $product_id) {
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.manufacturer_review, p.price, p.image_path, p.stock,
                                   p.category_id, p.brand_id, c.name AS category_name, b.name AS brand_name
                            FROM products p
                            INNER JOIN categories c ON p.category_id = c.id
                            INNER JOIN brands b ON p.brand_id = b.id
                            WHERE p.id = ?
                            LIMIT 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_all_public_brands($conn) {
    $sql = "SELECT id, name FROM brands ORDER BY name ASC";
    $result = $conn->query($sql);

    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }

    return $brands;
}
