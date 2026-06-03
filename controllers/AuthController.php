<?php
require_once __DIR__ . '/../models/UserModel.php';

function start_user_session($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
}

function create_remember_cookie($conn, $user_id) {
    $plain_token = bin2hex(random_bytes(32));
    $token_hash = password_hash($plain_token, PASSWORD_DEFAULT);

    save_remember_token($conn, $user_id, $token_hash);

    $cookie_value = $user_id . ':' . $plain_token;

    setcookie('remember_me', $cookie_value, [
        'expires' => time() + (60 * 60 * 24 * 30),
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ]);
}

function auto_login_from_remember_cookie($conn) {
    if (isset($_SESSION['user_id']) || empty($_COOKIE['remember_me'])) {
        return;
    }

    $parts = explode(':', $_COOKIE['remember_me'], 2);

    if (count($parts) !== 2) {
        return;
    }

    $user_id = (int)$parts[0];
    $plain_token = $parts[1];

    $user = find_user_by_id($conn, $user_id);

    if ($user && !empty($user['remember_token']) && password_verify($plain_token, $user['remember_token'])) {
        start_user_session($user);

        // Rotate token after automatic login.
        create_remember_cookie($conn, $user['id']);
    }
}

function show_register_page($errors = [], $old = []) {
    $pageTitle = "Register";
    require __DIR__ . '/../views/auth/register.php';
}

function handle_register($conn) {
    $errors = [];
    $old = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'role' => $_POST['role'] ?? ''
    ];

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = "Invalid request. Please refresh the page and try again.";
    }

    if ($old['name'] === '') {
        $errors['name'] = "Name is required.";
    }

    if ($old['email'] === '') {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email address.";
    } elseif (email_exists($conn, $old['email'])) {
        $errors['email'] = "This email is already registered.";
    }

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Password and confirm password do not match.";
    }

    if (!in_array($old['role'], ['admin', 'customer'], true)) {
        $errors['role'] = "Please select a valid role.";
    }

    if (!empty($errors)) {
        show_register_page($errors, $old);
        return;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (create_user($conn, $old['name'], $old['email'], $password_hash, $old['role'])) {
        set_flash('success', 'Registration successful. Please login.');
        redirect('index.php?page=login');
    }

    $errors['general'] = "Registration failed. Please try again.";
    show_register_page($errors, $old);
}

function show_login_page($errors = [], $old = []) {
    $pageTitle = "Login";
    require __DIR__ . '/../views/auth/login.php';
}

function handle_login($conn) {
    $errors = [];
    $old = [
        'email' => trim($_POST['email'] ?? '')
    ];

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = "Invalid request. Please refresh the page and try again.";
    }

    if ($old['email'] === '') {
        $errors['email'] = "Email is required.";
    }

    $password = $_POST['password'] ?? '';

    if ($password === '') {
        $errors['password'] = "Password is required.";
    }

    if (!empty($errors)) {
        show_login_page($errors, $old);
        return;
    }

    $user = find_user_by_email($conn, $old['email']);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors['general'] = "Invalid email or password.";
        show_login_page($errors, $old);
        return;
    }

    start_user_session($user);

    if (!empty($_POST['remember_me'])) {
        create_remember_cookie($conn, $user['id']);
    }

    set_flash('success', 'Login successful.');
    redirect('index.php?page=home');
}

function validate_profile_picture($file, &$errors) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors['profile_picture'] = "Profile picture upload failed.";
        return null;
    }

    $max_size = 2 * 1024 * 1024; // 2MB

    if ($file['size'] > $max_size) {
        $errors['profile_picture'] = "Profile picture must be 2MB or smaller.";
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    if (!array_key_exists($mime_type, $allowed)) {
        $errors['profile_picture'] = "Only JPEG and PNG images are allowed.";
        return null;
    }

    $upload_dir = __DIR__ . '/../public/uploads/profiles/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = $allowed[$mime_type];
    $filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors['profile_picture'] = "Could not save uploaded image.";
        return null;
    }

    return 'public/uploads/profiles/' . $filename;
}

function show_profile_page($conn, $errors = [], $old = []) {
    require_login();

    $user = find_user_by_id($conn, (int)$_SESSION['user_id']);

    if (!$user) {
        set_flash('error', 'User not found. Please login again.');
        redirect('index.php?page=logout');
    }

    if (empty($old)) {
        $old = [
            'name' => $user['name'],
            'email' => $user['email']
        ];
    }

    $pageTitle = "Profile";
    require __DIR__ . '/../views/auth/profile.php';
}

function handle_profile_update($conn) {
    require_login();

    $user_id = (int)$_SESSION['user_id'];
    $user = find_user_by_id($conn, $user_id);

    if (!$user) {
        set_flash('error', 'User not found. Please login again.');
        redirect('index.php?page=logout');
    }

    $errors = [];
    $old = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? '')
    ];

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = "Invalid request. Please refresh the page and try again.";
    }

    if ($old['name'] === '') {
        $errors['name'] = "Name is required.";
    }

    if ($old['email'] === '') {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email address.";
    } elseif (email_exists($conn, $old['email'], $user_id)) {
        $errors['email'] = "This email is already used by another account.";
    }

    $profile_picture = validate_profile_picture($_FILES['profile_picture'] ?? null, $errors);

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    $wants_password_change = ($current_password !== '' || $new_password !== '' || $confirm_new_password !== '');

    if ($wants_password_change) {
        if ($current_password === '') {
            $errors['current_password'] = "Current password is required.";
        } elseif (!password_verify($current_password, $user['password_hash'])) {
            $errors['current_password'] = "Current password is incorrect.";
        }

        if (strlen($new_password) < 8) {
            $errors['new_password'] = "New password must be at least 8 characters.";
        }

        if ($new_password !== $confirm_new_password) {
            $errors['confirm_new_password'] = "New password and confirm password do not match.";
        }
    }

    if (!empty($errors)) {
        show_profile_page($conn, $errors, $old);
        return;
    }

    update_user_profile($conn, $user_id, $old['name'], $old['email'], $profile_picture);

    if ($wants_password_change) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        update_user_password($conn, $user_id, $new_password_hash);
    }

    $_SESSION['name'] = $old['name'];

    set_flash('success', 'Profile updated successfully.');
    redirect('index.php?page=profile');
}

function handle_logout($conn) {
    if (isset($_SESSION['user_id'])) {
        clear_remember_token($conn, (int)$_SESSION['user_id']);
    }

    setcookie('remember_me', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ]);

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    session_destroy();

    session_start();
    set_flash('success', 'Logout successful.');
    redirect('index.php?page=login');
}
