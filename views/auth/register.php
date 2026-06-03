<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container narrow">
    <div class="form-card">
        <h1>Create Account</h1>
        <p class="muted">Register as admin or customer.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="error-box"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors['csrf'])): ?>
            <div class="error-box"><?= e($errors['csrf']) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register" onsubmit="return validateRegisterForm()" novalidate>
            <?= csrf_field() ?>

            <label>Name</label>
            <input type="text" name="name" id="reg_name" value="<?= e($old['name'] ?? '') ?>" placeholder="Enter your name">
            <small class="field-error"><?= e($errors['name'] ?? '') ?></small>

            <label>Email</label>
            <input type="email" name="email" id="reg_email" value="<?= e($old['email'] ?? '') ?>" placeholder="Enter your email">
            <small class="field-error" id="emailCheckMsg"><?= e($errors['email'] ?? '') ?></small>

            <label>Password</label>
            <input type="password" name="password" id="reg_password" placeholder="Minimum 8 characters">
            <small class="field-error"><?= e($errors['password'] ?? '') ?></small>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="reg_confirm_password" placeholder="Re-enter password">
            <small class="field-error"><?= e($errors['confirm_password'] ?? '') ?></small>

            <label>Role</label>
            <select name="role" id="reg_role">
                <option value="">Select Role</option>
                <option value="customer" <?= (($old['role'] ?? '') === 'customer') ? 'selected' : '' ?>>Customer</option>
                <option value="admin" <?= (($old['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
            <small class="field-error"><?= e($errors['role'] ?? '') ?></small>

            <button type="submit">Register</button>
        </form>

        <p class="form-note">Already have an account? <a href="index.php?page=login">Login here</a></p>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
