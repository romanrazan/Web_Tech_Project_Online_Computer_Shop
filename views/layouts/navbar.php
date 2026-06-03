<nav class="navbar">
    <div class="brand">
        <a class="brand-link" href="index.php?page=home">
            <span class="brand-mark">OCS</span>
            <span>Online Computer Shop</span>
        </a>
    </div>

    <div class="nav-links">
        <a href="index.php?page=home">Home</a>
        <a href="index.php?page=products">Products</a>

        <?php if (!is_logged_in()): ?>
            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register">Register</a>
        <?php else: ?>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="index.php?page=admin-dashboard">Admin Dashboard</a>
                <a href="index.php?page=admin-categories">Categories</a>
                <a href="index.php?page=admin-brands">Brands</a>
                <a href="index.php?page=admin-products">Inventory</a>
                <a href="index.php?page=admin-orders">Orders</a>
                <a href="index.php?page=admin-customers">Customers</a>
                <a href="index.php?page=admin-reviews">Reviews</a>
                <a href="index.php?page=profile">Profile</a>
            <?php elseif (($_SESSION['role'] ?? '') === 'customer'): ?>
                <a href="index.php?page=cart">Cart</a>
                <a href="index.php?page=my-orders">My Orders</a>
                <a href="index.php?page=my-reviews">Reviews</a>
                <a href="index.php?page=profile">Profile</a>
            <?php endif; ?>

            <a href="index.php?page=logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
        <?php endif; ?>
    </div>
</nav>

<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
    <div class="flash <?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
