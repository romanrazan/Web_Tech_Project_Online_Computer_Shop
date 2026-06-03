<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<?php
$formId = (int)($old['id'] ?? ($editCategory['id'] ?? 0));
$formName = $old['name'] ?? ($editCategory['name'] ?? '');
$formParentId = $old['parent_id'] ?? ($editCategory['parent_id'] ?? '');
$isEdit = $formId > 0;
?>

<main class="container">

<section class="section">
    <h1>Category Management</h1>
</section>

<section class="form-card wide">
<h2><?= $isEdit ? 'Edit Category' : 'Add Category' ?></h2>

<form method="POST" action="index.php?page=admin-categories">

    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_category">
    <input type="hidden" name="id" value="<?= e($formId) ?>">

    <label>Name</label>
    <input type="text" name="name" value="<?= e($formName) ?>" required>

    <label>Parent Category</label>
    <select name="parent_id">
        <option value="">Top Level</option>

        <?php foreach ($categories as $cat): ?>
            <?php if ((int)$cat['id'] === $formId) continue; ?>
            <option value="<?= $cat['id'] ?>"
                <?= ($formParentId == $cat['id']) ? 'selected' : '' ?>>
                <?= $cat['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>
    <button><?= $isEdit ? 'Update' : 'Create' ?></button>

    <?php if ($isEdit): ?>
        <a href="index.php?page=admin-categories">Cancel</a>
    <?php endif; ?>

</form>
</section>

<hr>

<section>
<h2>All Categories</h2>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Parent</th>
    <th>Created</th>
    <th>Action</th>
</tr>

<?php if (empty($categories)): ?>
<tr><td colspan="5">No category found</td></tr>
<?php endif; ?>

<?php foreach ($categories as $cat): ?>
<tr>

<td><?= $cat['id'] ?></td>
<td><?= $cat['name'] ?></td>
<td><?= $cat['parent_name'] ?? 'Top' ?></td>
<td><?= $cat['created_at'] ?></td>

<td>
    <a href="index.php?page=admin-categories&edit=<?= $cat['id'] ?>">Edit</a>

    <form method="POST" action="index.php?page=admin-categories" style="display:inline;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete_category">
        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
        <button onclick="return confirm('Delete this category?')">Delete</button>
    </form>
</td>

</tr>
<?php endforeach; ?>

</table>
</section>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>