<?php
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

function show_home_page($conn) {
    $categories = get_top_level_categories($conn);
    $featuredProducts = get_featured_products($conn, 6);

    $pageTitle = "Home";
    require __DIR__ . '/../views/home.php';
}
