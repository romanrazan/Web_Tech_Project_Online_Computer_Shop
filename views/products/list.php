<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
<section class="page-head">
    <div>
        <h1><?= e($pageTitle ?? 'Products') ?></h1>
        <p>Browse computer components by category, sub-category, brand, price, or keyword.</p>
    </div>
    <a class="button-secondary" href="index.php?page=cart">View Cart</a>
</section>

<?php if (!empty($errors)): ?>
    <div class="flash error">
        <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section class="filter-card">
    <form id="productSearchForm" onsubmit="return false;">
        <div class="form-grid">
            <div class="form-group">
                <label for="search_q">Search</label>
                <input type="text" id="search_q" name="q" placeholder="Search RAM, SSD, ASUS..." value="<?= e($filters['q'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="filter_category">Category</label>
                <select id="filter_category" name="category_id">
                    <option value="">All Categories</option>
                    <?php foreach ($filterData['categories'] as $category): ?>
                        <option value="<?= e($category['id']) ?>" <?= (int)($filters['category_id'] ?? 0) === (int)$category['id'] ? 'selected' : '' ?>>
                            <?= $category['parent_name'] ? e($category['parent_name'] . ' → ' . $category['name']) : e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="filter_brand">Brand</label>
                <select id="filter_brand" name="brand_id">
                    <option value="">All Brands</option>
                    <?php foreach ($filterData['brands'] as $brand): ?>
                        <option value="<?= e($brand['id']) ?>" <?= (int)($filters['brand_id'] ?? 0) === (int)$brand['id'] ? 'selected' : '' ?>>
                            <?= e($brand['name']) ?><?= isset($brand['category_name']) ? ' (' . e($brand['category_name']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="min_price">Min Price</label>
                <input type="number" id="min_price" name="min_price" min="0" step="0.01" value="<?= e($filters['min_price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="max_price">Max Price</label>
                <input type="number" id="max_price" name="max_price" min="0" step="0.01" value="<?= e($filters['max_price'] ?? '') ?>">
            </div>
        </div>

        <div class="action-row">
            <button type="button" class="primary-btn" id="ajaxSearchBtn">Search / Filter</button>
            <a class="button-secondary" href="index.php?page=products">Reset</a>
        </div>
        <p class="small-note">AJAX search updates the product grid without reloading the page.</p>
    </form>
</section>

<section>
    <div id="productGrid" class="product-grid">
        <?php if (empty($products)): ?>
            <div class="empty-state">No products found.</div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <article class="product-card" data-product-id="<?= e($product['id']) ?>">
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="<?= e($product['image_path']) ?>" alt="<?= e($product['name']) ?>">
                    <?php else: ?>
                        <div class="product-placeholder">No Image</div>
                    <?php endif; ?>

                    <div class="product-info">
                        <h3><?= e($product['name']) ?></h3>
                        <p class="meta"><?= e($product['category_name']) ?> • <?= e($product['brand_name']) ?></p>
                        <p><?= e(strlen($product['manufacturer_review']) > 95 ? substr($product['manufacturer_review'], 0, 95) . '...' : $product['manufacturer_review']) ?></p>
                        <p class="price">৳<?= number_format((float)$product['price'], 2) ?></p>
                        <p class="stock <?= (int)$product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                            <?= (int)$product['stock'] > 0 ? 'In stock: ' . e($product['stock']) : 'Out of stock' ?>
                        </p>
                    </div>

                    <div class="product-actions">
                        <a class="button-secondary" href="index.php?page=product-details&id=<?= e($product['id']) ?>">Details</a>
                        <?php if (is_logged_in() && ($_SESSION['role'] ?? '') === 'customer'): ?>
                            <button type="button" class="primary-btn add-to-cart-btn" data-id="<?= e($product['id']) ?>" <?= (int)$product['stock'] <= 0 ? 'disabled' : '' ?>>Add to Cart</button>
                        <?php elseif (!is_logged_in()): ?>
                            <a class="primary-btn" href="index.php?page=login">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
    window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';
</script>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
