<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container narrow">
    <div class="form-card">
        <h1>Profile</h1>
        <p class="muted">Update your name, email, profile picture, or password.</p>

        <?php if (!empty($user['profile_picture'])): ?>
            <img class="profile-img" src="<?= e($user['profile_picture']) ?>" alt="Profile Picture">
        <?php else: ?>
            <div class="profile-placeholder">No Profile Picture</div>
        <?php endif; ?>

        <?php if (!empty($errors['csrf'])): ?>
            <div class="error-box"><?= e($errors['csrf']) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=profile" enctype="multipart/form-data" onsubmit="return validateProfileForm()" novalidate>
            <?= csrf_field() ?>

            <label>Name</label>
            <input type="text" name="name" id="profile_name" value="<?= e($old['name'] ?? '') ?>">
            <small class="field-error"><?= e($errors['name'] ?? '') ?></small>

            <label>Email</label>
            <input type="email" name="email" id="profile_email" value="<?= e($old['email'] ?? '') ?>">
            <small class="field-error"><?= e($errors['email'] ?? '') ?></small>

            <label>Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/png">
            <small class="field-error"><?= e($errors['profile_picture'] ?? '') ?></small>
            <small class="help">Allowed: JPEG/PNG, maximum size: 2MB.</small>

            <hr>

            <h2>Change Password</h2>
            <p class="muted">Leave these fields empty if you do not want to change password.</p>

            <label>Current Password</label>
            <input type="password" name="current_password" id="current_password">
            <small class="field-error"><?= e($errors['current_password'] ?? '') ?></small>

            <label>New Password</label>
            <input type="password" name="new_password" id="new_password">
            <small class="field-error"><?= e($errors['new_password'] ?? '') ?></small>

            <label>Confirm New Password</label>
            <input type="password" name="confirm_new_password" id="confirm_new_password">
            <small class="field-error"><?= e($errors['confirm_new_password'] ?? '') ?></small>

            <button type="submit">Save Profile</button>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
