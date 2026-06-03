<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<?php
$formId = (int)($old['id'] ?? ($editBrand['id'] ?? 0));
$formName = $old['name'] ?? ($editBrand['name'] ?? '');
$formCategoryId = $old['category_id'] ?? ($editBrand['category_id'] ?? '');
$isEdit = $formId > 0;
?>

<main class="container">
    <section class="section">
        <h1>Brand Management</h1>
        <p class="muted">Create, edit, and delete brands under a specific category.</p>
    </section>

    <section class="form-card wide">
        <h2><?= $isEdit ? 'Edit Brand' : 'Add New Brand' ?></h2>

        <form method="POST" action="index.php?page=admin-brands" onsubmit="return validateBrandForm()" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_brand">
            <input type="hidden" name="id" value="<?= e($formId) ?>">

            <label>Brand Name</label>
            <input type="text" name="name" id="brand_name" value="<?= e($formName) ?>" placeholder="Example: ASUS">
            <small class="field-error"><?= e($errors['name'] ?? '') ?></small>

            <label>Category</label>
            <select name="category_id" id="brand_category_id">
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['id']) ?>" <?= ((string)$formCategoryId === (string)$category['id']) ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="field-error"><?= e($errors['category_id'] ?? '') ?></small>

            <button type="submit"><?= $isEdit ? 'Update Brand' : 'Create Brand' ?></button>
            <?php if ($isEdit): ?>
                <a class="button-secondary" href="index.php?page=admin-brands">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="section">
        <h2>All Brands</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($brands)): ?>
                        <tr><td colspan="5">No brand found.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td><?= e($brand['id']) ?></td>
                            <td><?= e($brand['name']) ?></td>
                            <td><?= e($brand['category_name']) ?></td>
                            <td><?= e($brand['created_at']) ?></td>
                            <td class="actions">
                                <a class="mini-btn" href="index.php?page=admin-brands&edit=<?= e($brand['id']) ?>">Edit</a>

                                <form method="POST" action="index.php?page=admin-brands" class="inline-form" onsubmit="return confirm('Delete this brand?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete_brand">
                                    <input type="hidden" name="id" value="<?= e($brand['id']) ?>">
                                    <button type="submit" class="danger-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
