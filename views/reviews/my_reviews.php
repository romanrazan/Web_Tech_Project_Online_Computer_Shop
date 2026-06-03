<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
    <section class="page-head">
        <div>
            <p class="meta">Customer Dashboard</p>
            <h1>My Reviews</h1>
            <p class="muted">All reviews you have posted are shown here. You can delete your own reviews from this page.</p>
        </div>
        <a class="button-secondary" href="index.php?page=products">Browse Products</a>
    </section>

    <p id="myReviewMessage" class="small-note"></p>

    <?php if (empty($reviews)): ?>
        <div class="empty-state" id="noMyReviewMsg">You have not posted any review yet.</div>
    <?php else: ?>
        <section class="review-list dashboard-review-list" id="reviewList">
            <?php foreach ($reviews as $review): ?>
                <article class="review-card dashboard-review-card" id="review-<?= e($review['id']) ?>">
                    <div class="review-head">
                        <div>
                            <strong><?= e($review['product_name']) ?></strong>
                            <p class="meta">Posted on <?= e(date('d M Y, h:i A', strtotime($review['created_at']))) ?></p>
                        </div>
                        <button type="button" class="danger-btn review-delete-btn" data-review-id="<?= e($review['id']) ?>">Delete</button>
                    </div>

                    <div class="my-review-product-row">
                        <?php if (!empty($review['image_path'])): ?>
                            <img class="mini-product-img" src="<?= e($review['image_path']) ?>" alt="<?= e($review['product_name']) ?>">
                        <?php else: ?>
                            <div class="mini-product-placeholder">No Image</div>
                        <?php endif; ?>
                        <div>
                            <p class="price">৳<?= number_format((float)$review['product_price'], 2) ?></p>
                            <a class="small-link" href="index.php?page=product-details&id=<?= e($review['product_id']) ?>">View Product</a>
                        </div>
                    </div>

                    <p><?= nl2br(e($review['comment'])) ?></p>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <script>
        window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';
    </script>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
