<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<?php
$formId = (int)($old['id'] ?? ($editProduct['id'] ?? 0));
$formName = $old['name'] ?? ($editProduct['name'] ?? '');
$formDescription = $old['description'] ?? ($editProduct['description'] ?? '');
$formManufacturerReview = $old['manufacturer_review'] ?? ($editProduct['manufacturer_review'] ?? '');
$formPrice = $old['price'] ?? ($editProduct['price'] ?? '');
$formCategoryId = $old['category_id'] ?? ($editProduct['category_id'] ?? '');
$formBrandId = $old['brand_id'] ?? ($editProduct['brand_id'] ?? '');
$formStock = $old['stock'] ?? ($editProduct['stock'] ?? '');
$isEdit = $formId > 0;
?>

<main class="container">
    <section class="section">
        <h1>Product Management</h1>
        <p class="muted">Create, edit, and delete products with image upload and stock quantity.</p>
    </section>

    <section class="form-card wide">
        <h2><?= $isEdit ? 'Edit Product' : 'Add New Product' ?></h2>

        <form method="POST" action="index.php?page=admin-products" enctype="multipart/form-data" onsubmit="return validateProductForm()" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_product">
            <input type="hidden" name="id" value="<?= e($formId) ?>">

            <div class="form-grid">
                <div>
                    <label>Product Name</label>
                    <input type="text" name="name" id="product_name" value="<?= e($formName) ?>" placeholder="Example: ASUS 24 Inch Monitor">
                    <small class="field-error"><?= e($errors['name'] ?? '') ?></small>
                </div>

                <div>
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" id="product_price" value="<?= e($formPrice) ?>" placeholder="Example: 18500">
                    <small class="field-error"><?= e($errors['price'] ?? '') ?></small>
                </div>

                <div>
                    <label>Category</label>
                    <select name="category_id" id="product_category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['id']) ?>" <?= ((string)$formCategoryId === (string)$category['id']) ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="field-error"><?= e($errors['category_id'] ?? '') ?></small>
                </div>

                <div>
                    <label>Brand</label>
                    <select name="brand_id" id="product_brand_id" data-selected-brand="<?= e($formBrandId) ?>">
                        <option value="">Select Brand</option>
                        <?php foreach ($brands as $brand): ?>
                            <?php if ((string)$formCategoryId === '' || (string)$brand['category_id'] === (string)$formCategoryId): ?>
                                <option value="<?= e($brand['id']) ?>" <?= ((string)$formBrandId === (string)$brand['id']) ? 'selected' : '' ?>>
                                    <?= e($brand['name']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <small class="field-error"><?= e($errors['brand_id'] ?? '') ?></small>
                </div>

                <div>
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" id="product_stock" value="<?= e($formStock) ?>" placeholder="Example: 10">
                    <small class="field-error"><?= e($errors['stock'] ?? '') ?></small>
                </div>

                <div>
                    <label>Product Image</label>
                    <input type="file" name="image" id="product_image" accept="image/jpg,image/png">
                    <small class="field-error"><?= e($errors['image'] ?? '') ?></small>
                    <small class="help">Allowed: JPG/PNG, maximum size: 2MB.</small>
                </div>
            </div>

            <label>Description</label>
            <textarea name="description" id="product_description" rows="4" placeholder="Write full product description."><?= e($formDescription) ?></textarea>
            <small class="field-error"><?= e($errors['description'] ?? '') ?></small>

            <label>Manufacturer Review</label>
            <textarea name="manufacturer_review" id="product_manufacturer_review" rows="3" placeholder="Short manufacturer information or review."><?= e($formManufacturerReview) ?></textarea>
            <small class="field-error"><?= e($errors['manufacturer_review'] ?? '') ?></small>

            <?php if ($isEdit && !empty($editProduct['image_path'])): ?>
                <p class="muted">Current Image:</p>
                <img class="thumb" src="public/uploads/products/<?= e($product['image_path']) ?>" alt="Current Product Image">
            <?php endif; ?>

            <button type="submit"><?= $isEdit ? 'Update Product' : 'Create Product' ?></button>
            <?php if ($isEdit): ?>
                <a class="button-secondary" href="index.php?page=admin-products">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="section">
        <h2>All Products</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8">No product found.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= e($product['id']) ?></td>
                            <td>
                                <?php if (!empty($product['image_path'])): ?>
                                    <img class="table-img" src="public/uploads/products/<?= e($product['image_path']) ?>" alt="<?= e($product['name']) ?>">
                                <?php else: ?>
                                    <span class="muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($product['name']) ?></td>
                            <td><?= e($product['category_name']) ?></td>
                            <td><?= e($product['brand_name']) ?></td>
                            <td>৳<?= number_format((float)$product['price'], 2) ?></td>
                            <td><?= e($product['stock']) ?></td>
                            <td class="actions">
                                <a class="mini-btn" href="index.php?page=admin-products&edit=<?= e($product['id']) ?>">Edit</a>

                                <form method="POST" action="index.php?page=admin-products" class="inline-form" onsubmit="return confirm('Delete this product?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete_product">
                                    <input type="hidden" name="id" value="<?= e($product['id']) ?>">
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
