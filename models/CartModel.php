<?php
function get_cart_item($conn, $user_id, $product_id) {
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_cart_count($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)$row['total'];
}

function add_cart_item($conn, $user_id, $product_id, $quantity) {
    $existing = get_cart_item($conn, $user_id, $product_id);

    if ($existing) {
        $new_quantity = (int)$existing['quantity'] + $quantity;
        return update_cart_item_quantity($conn, $user_id, $product_id, $new_quantity);
    }

    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    return $stmt->execute();
}

function update_cart_item_quantity($conn, $user_id, $product_id, $quantity) {
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    return $stmt->execute();
}

function remove_cart_item($conn, $user_id, $product_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}

function get_cart_items($conn, $user_id) {
    $stmt = $conn->prepare("SELECT c.id AS cart_id, c.product_id, c.quantity,
                                   p.name, p.price, p.stock, p.image_path,
                                   b.name AS brand_name, cat.name AS category_name,
                                   (c.quantity * p.price) AS subtotal
                            FROM cart c
                            INNER JOIN products p ON c.product_id = p.id
                            LEFT JOIN brands b ON p.brand_id = b.id
                            LEFT JOIN categories cat ON p.category_id = cat.id
                            WHERE c.user_id = ?
                            ORDER BY c.added_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    return $items;
}

function get_cart_total($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(c.quantity * p.price), 0) AS total
                            FROM cart c
                            INNER JOIN products p ON c.product_id = p.id
                            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (float)$row['total'];
}
