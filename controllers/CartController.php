<?php
require_once __DIR__ . '/../models/CartModel.php';

function show_cart_page($conn) {
    require_login();

    $user_id = (int)$_SESSION['user_id'];
    $cartItems = get_cart_items($conn, $user_id);
    $cartTotal = get_cart_total($conn, $user_id);
    $pageTitle = 'My Cart';
    require __DIR__ . '/../views/cart/index.php';
}
