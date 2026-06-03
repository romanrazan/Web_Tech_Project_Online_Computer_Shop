<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/ReviewController.php';
require_once __DIR__ . '/controllers/OrderController.php';

auto_login_from_remember_cookie($conn);

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        show_home_page($conn);
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_register($conn);
        } else {
            show_register_page();
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_login($conn);
        } else {
            show_login_page();
        }
        break;

    case 'profile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_profile_update($conn);
        } else {
            show_profile_page($conn);
        }
        break;

    case 'logout':
        handle_logout($conn);
        break;

    // Task 2 admin routes
    case 'admin-dashboard':
        show_admin_dashboard($conn);
        break;

    case 'admin-categories':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_category_action($conn);
        } else {
            show_categories_page($conn);
        }
        break;

    case 'admin-brands':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_brand_action($conn);
        } else {
            show_brands_page($conn);
        }
        break;

    case 'admin-products':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_product_action($conn);
        } else {
            show_products_page($conn);
        }
        break;

    // Task 3 customer/product/cart routes
    case 'products':
        show_customer_products_page($conn);
        break;

    case 'category':
        show_category_products_page($conn);
        break;

    case 'brand':
        show_brand_products_page($conn);
        break;

    case 'product-details':
        show_product_details_page($conn);
        break;

    case 'cart':
        show_cart_page($conn);
        break;

    // Task 4 review/order/admin management routes
    case 'checkout':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_checkout($conn);
        } else {
            redirect('index.php?page=cart');
        }
        break;

    case 'order-confirmation':
        show_order_confirmation_page($conn);
        break;

    case 'admin-customers':
        show_admin_customers_page($conn);
        break;

    case 'admin-reviews':
        show_admin_reviews_page($conn);
        break;

    case 'admin-orders':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_admin_order_action($conn);
        } else {
            show_admin_orders_page($conn);
        }
        break;

    case 'my-orders':
        show_my_orders_page($conn);
        break;

    case 'my-reviews':
        show_my_reviews_page($conn);
        break;

    default:
        $pageTitle = "404";
        require __DIR__ . '/views/errors/404.php';
        break;
}
