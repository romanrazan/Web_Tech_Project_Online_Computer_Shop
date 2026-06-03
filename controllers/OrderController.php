<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CartModel.php';

function require_customer_for_order() {
    require_login();

    if (($_SESSION['role'] ?? '') !== 'customer') {
        set_flash('error', 'Only customers can place orders.');
        redirect('index.php?page=home');
    }
}

function handle_checkout($conn) {
    require_customer_for_order();

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid checkout request. Please try again.');
        redirect('index.php?page=cart');
    }

    $payment_method = $_POST['payment_method'] ?? '';
    $result = create_order_from_cart($conn, (int)$_SESSION['user_id'], $payment_method);

    if (!$result['success']) {
        set_flash('error', $result['message']);
        redirect('index.php?page=cart');
    }

    set_flash('success', 'Order placed successfully.');
    redirect('index.php?page=order-confirmation&id=' . (int)$result['order_id']);
}

function show_order_confirmation_page($conn) {
    require_customer_for_order();

    $order_id = (int)($_GET['id'] ?? 0);
    if ($order_id <= 0) {
        set_flash('error', 'Invalid order selected.');
        redirect('index.php?page=products');
    }

    $order = get_order_summary($conn, $order_id, (int)$_SESSION['user_id']);
    if (!$order) {
        set_flash('error', 'Order not found.');
        redirect('index.php?page=products');
    }

    $pageTitle = 'Order Confirmation';
    require __DIR__ . '/../views/orders/confirmation.php';
}

function show_my_orders_page($conn) {
    require_customer_for_order();

    $orders = get_customer_orders($conn, (int)$_SESSION['user_id']);
    $pageTitle = 'My Orders';
    require __DIR__ . '/../views/orders/my_orders.php';
}
