<?php
function get_recent_orders($conn, $limit = 5) {
    $stmt = $conn->prepare("SELECT o.id, o.user_id, o.total_amount, o.payment_method, o.status, o.order_date,
                                   u.name AS customer_name, u.email AS customer_email
                            FROM orders o
                            INNER JOIN users u ON o.user_id = u.id
                            ORDER BY o.order_date DESC
                            LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}

function create_order_from_cart($conn, $user_id, $payment_method) {
    require_once __DIR__ . '/CartModel.php';

    $allowedMethods = ['cash_on_delivery', 'bkash', 'nagad', 'dbbl'];
    if (!in_array($payment_method, $allowedMethods, true)) {
        return ['success' => false, 'message' => 'Invalid payment method selected. Please choose Cash on Delivery, bKash, Nagad, or DBBL/Rocket.'];
    }

    $cartItems = get_cart_items($conn, $user_id);
    if (empty($cartItems)) {
        return ['success' => false, 'message' => 'Your cart is empty.'];
    }

    foreach ($cartItems as $item) {
        if ((int)$item['quantity'] <= 0) {
            return ['success' => false, 'message' => 'Invalid cart quantity found.'];
        }

        if ((int)$item['quantity'] > (int)$item['stock']) {
            return ['success' => false, 'message' => $item['name'] . ' quantity exceeds available stock.'];
        }
    }

    $totalAmount = get_cart_total($conn, $user_id);
    if ($totalAmount <= 0) {
        return ['success' => false, 'message' => 'Order total is invalid.'];
    }

    $conn->begin_transaction();

    try {
        $status = 'pending';
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status, order_date)
                                VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idss", $user_id, $totalAmount, $payment_method, $status);
        $stmt->execute();
        $orderId = $conn->insert_id;

        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                                    VALUES (?, ?, ?, ?)");
        $stockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

        foreach ($cartItems as $item) {
            $productId = (int)$item['product_id'];
            $quantity = (int)$item['quantity'];
            $unitPrice = (float)$item['price'];

            $itemStmt->bind_param("iiid", $orderId, $productId, $quantity, $unitPrice);
            $itemStmt->execute();

            $stockStmt->bind_param("iii", $quantity, $productId, $quantity);
            $stockStmt->execute();

            if ($stockStmt->affected_rows !== 1) {
                throw new Exception('Could not update stock for ' . $item['name']);
            }
        }

        $clearStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearStmt->bind_param("i", $user_id);
        $clearStmt->execute();

        $conn->commit();

        return ['success' => true, 'order_id' => $orderId];
    } catch (Throwable $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Order placement failed. ' . $e->getMessage()];
    }
}

function get_order_summary($conn, $order_id, $user_id = null) {
    if ($user_id === null) {
        $stmt = $conn->prepare("SELECT o.id, o.user_id, o.total_amount, o.payment_method, o.status, o.order_date,
                                       u.name AS customer_name, u.email AS customer_email
                                FROM orders o
                                INNER JOIN users u ON o.user_id = u.id
                                WHERE o.id = ?
                                LIMIT 1");
        $stmt->bind_param("i", $order_id);
    } else {
        $stmt = $conn->prepare("SELECT o.id, o.user_id, o.total_amount, o.payment_method, o.status, o.order_date,
                                       u.name AS customer_name, u.email AS customer_email
                                FROM orders o
                                INNER JOIN users u ON o.user_id = u.id
                                WHERE o.id = ? AND o.user_id = ?
                                LIMIT 1");
        $stmt->bind_param("ii", $order_id, $user_id);
    }

    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        return null;
    }

    $itemStmt = $conn->prepare("SELECT oi.product_id, oi.quantity, oi.unit_price,
                                      (oi.quantity * oi.unit_price) AS subtotal,
                                      p.name AS product_name
                               FROM order_items oi
                               INNER JOIN products p ON oi.product_id = p.id
                               WHERE oi.order_id = ?
                               ORDER BY oi.id ASC");
    $itemStmt->bind_param("i", $order_id);
    $itemStmt->execute();
    $result = $itemStmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $order['items'] = $items;
    return $order;
}

function get_all_orders_admin($conn) {
    $stmt = $conn->prepare("SELECT o.id, o.user_id, o.total_amount, o.payment_method, o.status, o.order_date,
                                   u.name AS customer_name, u.email AS customer_email,
                                   COUNT(oi.id) AS item_count
                            FROM orders o
                            INNER JOIN users u ON o.user_id = u.id
                            LEFT JOIN order_items oi ON o.id = oi.order_id
                            GROUP BY o.id, o.user_id, o.total_amount, o.payment_method, o.status, o.order_date, u.name, u.email
                            ORDER BY o.order_date DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}

function accept_order_admin($conn, $order_id) {
    $status = 'accepted';
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    return $stmt->affected_rows === 1;
}

function delete_order_admin($conn, $order_id) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    return $stmt->affected_rows === 1;
}

function get_customer_orders($conn, $user_id) {
    $stmt = $conn->prepare("SELECT o.id, o.total_amount, o.payment_method, o.status, o.order_date,
                                   COUNT(oi.id) AS item_count
                            FROM orders o
                            LEFT JOIN order_items oi ON o.id = oi.order_id
                            WHERE o.user_id = ?
                            GROUP BY o.id, o.total_amount, o.payment_method, o.status, o.order_date
                            ORDER BY o.order_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}
