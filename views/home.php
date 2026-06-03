<?php require __DIR__ . '/layouts/header.php'; ?>
<?php require __DIR__ . '/layouts/navbar.php'; ?>

<main class="container">
    <section class="hero">
        <div>
            <h1>Online Computer Shop</h1>
            <p>Browse PC components, peripherals, manufacturer information, and prices.</p>
        </div>

        <div class="hero-card">
            <?php if (is_logged_in()): ?>
                <p>Welcome, <strong><?= e($_SESSION['name']) ?></strong></p>
                <p>Your role: <strong><?= e($_SESSION['role']) ?></strong></p>
            <?php else: ?>
                <p>You are browsing as a guest.</p>
                <p>Login or register to review products and place orders.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section">
        <h2>Category Bar</h2>

        <?php if (empty($categories)): ?>
            <p class="muted">No category found. Task 2 admin will add categories later.</p>
        <?php else: ?>
            <div class="category-bar">
                <?php foreach ($categories as $category): ?>
                    <a href="index.php?page=category&id=<?= e($category['id']) ?>">
                        <?= e($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="section">
        <h2>Featured Components</h2>

        <?php if (empty($featuredProducts)): ?>
            <p class="muted">No featured products found. Task 2 admin will add products later.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <article class="product-card">
                        <?php if (!empty($product['image_path'])): ?>
                             <img src="public/uploads/products/<?= e($product['image_path']) ?>"
                        <?php else: ?>
                            <div class="image-placeholder">No Image</div>
                        <?php endif; ?>

                        <h3><?= e($product['name']) ?></h3>

                        <p class="muted">
                            <?= e($product['brand_name'] ?? 'No Brand') ?> |
                            <?= e($product['category_name'] ?? 'No Category') ?>
                        </p>

                        <p><?= e($product['manufacturer_review']) ?></p>

                        <p class="price">৳<?= number_format((float)$product['price'], 2) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/layouts/footer.php'; ?>
