<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container narrow">
    <div class="form-card">
        <h1>Login</h1>
        <p class="muted">Login to access your profile, cart, reviews, and orders.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="error-box"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors['csrf'])): ?>
            <div class="error-box"><?= e($errors['csrf']) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login" onsubmit="return validateLoginForm()" novalidate>
            <?= csrf_field() ?>

            <label>Email</label>
            <input type="email" name="email" id="login_email" value="<?= e($old['email'] ?? '') ?>" placeholder="Enter your email">
            <small class="field-error"><?= e($errors['email'] ?? '') ?></small>

            <label>Password</label>
            <input type="password" name="password" id="login_password" placeholder="Enter your password">
            <small class="field-error"><?= e($errors['password'] ?? '') ?></small>

            <label class="checkbox-row">
                <input type="checkbox" name="remember_me" value="1">
                <span>Remember Me for 30 days</span>
            </label>

            <button type="submit">Login</button>
        </form>

        <p class="form-note">No account? <a href="index.php?page=register">Register here</a></p>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
