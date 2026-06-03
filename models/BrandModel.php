<?php
function get_all_brands($conn) {
    $sql = "SELECT b.id, b.name, b.category_id, b.created_at, c.name AS category_name
            FROM brands b
            INNER JOIN categories c ON b.category_id = c.id
            ORDER BY c.name ASC, b.name ASC";
    $result = $conn->query($sql);

    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }

    return $brands;
}

function get_brands_by_category($conn, $category_id) {
    $stmt = $conn->prepare("SELECT id, name FROM brands WHERE category_id = ? ORDER BY name ASC");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }

    return $brands;
}

function find_brand_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT id, name, category_id FROM brands WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function brand_name_exists_in_category($conn, $name, $category_id, $exclude_id = 0) {
    $stmt = $conn->prepare("SELECT id FROM brands WHERE name = ? AND category_id = ? AND id <> ? LIMIT 1");
    $stmt->bind_param("sii", $name, $category_id, $exclude_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function create_brand($conn, $name, $category_id) {
    $stmt = $conn->prepare("INSERT INTO brands (name, category_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $category_id);
    return $stmt->execute();
}

function update_brand($conn, $id, $name, $category_id) {
    $stmt = $conn->prepare("UPDATE brands SET name = ?, category_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $name, $category_id, $id);
    return $stmt->execute();
}

function count_products_by_brand($conn, $brand_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE brand_id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)$row['total'];
}

function delete_brand($conn, $brand_id) {
    $stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->bind_param("i", $brand_id);
    return $stmt->execute();
}
