<?php
function get_reviews_by_product($conn, $product_id) {
    $stmt = $conn->prepare("SELECT r.id, r.product_id, r.user_id, r.reviewer_name, r.comment, r.created_at,
                                   u.email AS reviewer_email
                            FROM reviews r
                            INNER JOIN users u ON r.user_id = u.id
                            WHERE r.product_id = ?
                            ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    return $reviews;
}

function find_review_by_id($conn, $review_id) {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function create_review($conn, $product_id, $user_id, $reviewer_name, $comment) {
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, reviewer_name, comment, created_at)
                            VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $product_id, $user_id, $reviewer_name, $comment);
    return $stmt->execute();
}

function delete_review($conn, $review_id) {
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    return $stmt->execute();
}

function get_reviews_by_user($conn, $user_id) {
    $stmt = $conn->prepare("SELECT r.id, r.product_id, r.user_id, r.reviewer_name, r.comment, r.created_at,
                                   p.name AS product_name, p.price AS product_price, p.image_path
                            FROM reviews r
                            INNER JOIN products p ON r.product_id = p.id
                            WHERE r.user_id = ?
                            ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    return $reviews;
}

function get_all_reviews_admin($conn) {
    $sql = "SELECT r.id, r.product_id, r.user_id, r.reviewer_name, r.comment, r.created_at,
                   p.name AS product_name, u.email AS user_email
            FROM reviews r
            INNER JOIN products p ON r.product_id = p.id
            INNER JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    return $reviews;
}

function get_recent_reviews($conn, $limit = 5) {
    $stmt = $conn->prepare("SELECT r.id, r.reviewer_name, r.comment, r.created_at,
                                   p.name AS product_name
                            FROM reviews r
                            INNER JOIN products p ON r.product_id = p.id
                            ORDER BY r.created_at DESC
                            LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    return $reviews;
}
