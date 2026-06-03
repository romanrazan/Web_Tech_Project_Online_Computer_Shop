<?php
function find_user_by_email($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function find_user_by_id($conn, $id) {
    $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function email_exists($conn, $email, $exclude_id = null) {
    if ($exclude_id === null) {
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
    } else {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $exclude_id);
    }

    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function create_user($conn, $name, $email, $password_hash, $role) {
    $sql = "INSERT INTO users (name, email, password_hash, role, profile_picture, created_at)
            VALUES (?, ?, ?, ?, NULL, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password_hash, $role);

    return $stmt->execute();
}

function update_user_profile($conn, $id, $name, $email, $profile_picture = null) {
    if ($profile_picture === null) {
        $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $id);
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $profile_picture, $id);
    }

    return $stmt->execute();
}

function update_user_password($conn, $id, $password_hash) {
    $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $password_hash, $id);

    return $stmt->execute();
}

function save_remember_token($conn, $id, $remember_token_hash) {
    $sql = "UPDATE users SET remember_token = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $remember_token_hash, $id);

    return $stmt->execute();
}

function clear_remember_token($conn, $id) {
    $sql = "UPDATE users SET remember_token = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

function get_all_customers_admin($conn) {
    $stmt = $conn->prepare("SELECT id, name, email, profile_picture, created_at
                            FROM users
                            WHERE role = 'customer'
                            ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }

    return $customers;
}

function count_customer_orders($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)$row['total'];
}

function count_customer_reviews($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reviews WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int)$row['total'];
}

function delete_customer_by_id($conn, $user_id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->affected_rows === 1;
}
