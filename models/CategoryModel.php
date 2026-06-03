<?php
function get_top_level_categories($conn) {
    $sql = "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC";
    $result = $conn->query($sql);

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

function get_all_categories($conn) {
    $sql = "SELECT c.id, c.name, c.parent_id, c.created_at, p.name AS parent_name
            FROM categories c
            LEFT JOIN categories p ON c.parent_id = p.id
            ORDER BY COALESCE(p.name, c.name), c.parent_id IS NOT NULL, c.name ASC";
    $result = $conn->query($sql);

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

function find_category_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT id, name, parent_id FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function category_name_exists($conn, $name, $exclude_id = 0) {
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id <> ? LIMIT 1");
    $stmt->bind_param("si", $name, $exclude_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function create_category($conn, $name, $parent_id) {
    if ($parent_id === null) {
        $stmt = $conn->prepare("INSERT INTO categories (name, parent_id) VALUES (?, NULL)");
        $stmt->bind_param("s", $name);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $parent_id);
    }

    return $stmt->execute();
}

function update_category($conn, $id, $name, $parent_id) {
    if ($parent_id === null) {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, parent_id = NULL WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
    } else {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, parent_id = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $parent_id, $id);
    }

    return $stmt->execute();
}

function count_child_categories($conn, $category_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM categories WHERE parent_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)$row['total'];
}

function count_products_by_category($conn, $category_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)$row['total'];
}

function delete_category($conn, $category_id) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    return $stmt->execute();
}
