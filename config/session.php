<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash() {
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'Please login first.');
        redirect('index.php?page=login');
    }
}

function require_admin() {
    require_login();

    if (($_SESSION['role'] ?? '') !== 'admin') {
        set_flash('error', 'Only admin can access this page.');
        redirect('index.php?page=home');
    }
}

function payment_method_label($method) {
    $labels = [
        'cash_on_delivery' => 'Cash on Delivery',
        'bkash' => 'bKash',
        'nagad' => 'Nagad',
        'dbbl' => 'DBBL / Rocket',
        'online_wallet' => 'Online Wallet'
    ];

    if (isset($labels[$method])) {
        return $labels[$method];
    }

    return ucwords(str_replace('_', ' ', (string)$method));
}

