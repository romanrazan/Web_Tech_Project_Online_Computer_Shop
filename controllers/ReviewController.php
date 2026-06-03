<?php
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

function require_customer() {
    require_login();

    if (($_SESSION['role'] ?? '') !== 'customer') {
        set_flash('error', 'Only customers can perform this action.');
        redirect('index.php?page=home');
    }
}

function can_customer_delete_review($review) {
    return is_logged_in()
        && ($_SESSION['role'] ?? '') === 'customer'
        && (int)$review['user_id'] === (int)$_SESSION['user_id'];
}

function show_my_reviews_page($conn) {
    require_customer();

    $reviews = get_reviews_by_user($conn, (int)$_SESSION['user_id']);
    $pageTitle = "My Reviews";

    require __DIR__ . '/../views/reviews/my_reviews.php';
}
