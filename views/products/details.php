<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
<section class="detail-layout">
    <div class="detail-image">
        <?php if (!empty($product['image_path'])): ?>
             <img src="<?= e($product['image_path']) ?>" 
        <?php else: ?>
            <div class="detail-placeholder">No Image</div>
        <?php endif; ?>
    </div>

    <div class="detail-info">
        <p class="meta"><?= e($product['category_name']) ?> • <?= e($product['brand_name']) ?></p>
        <h1><?= e($product['name']) ?></h1>
        <p class="price big">৳<?= number_format((float)$product['price'], 2) ?></p>
        <p class="stock <?= (int)$product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
            <?= (int)$product['stock'] > 0 ? 'In stock: ' . e($product['stock']) : 'Out of stock' ?>
        </p>

        <h3>Description</h3>
        <p><?= nl2br(e($product['description'])) ?></p>

        <h3>Manufacturer Review</h3>
        <p><?= nl2br(e($product['manufacturer_review'])) ?></p>

        <div class="action-row">
            <a class="button-secondary" href="index.php?page=products">Back to Products</a>

            <?php if (is_logged_in() && ($_SESSION['role'] ?? '') === 'customer'): ?>
                <button type="button" class="primary-btn add-to-cart-btn" data-id="<?= e($product['id']) ?>" <?= (int)$product['stock'] <= 0 ? 'disabled' : '' ?>>Add to Cart</button>
            <?php elseif (!is_logged_in()): ?>
                <a class="primary-btn" href="index.php?page=login">Login to Add Cart</a>
            <?php endif; ?>
        </div>

        <p id="cartMessage" class="small-note"></p>
    </div>
</section>

<section class="section review-section">
    <div class="page-head compact">
        <div>
            <h2>Customer Reviews</h2>
            <p class="muted">Existing product reviews from customers.</p>
        </div>
    </div>

    <?php if (is_logged_in() && ($_SESSION['role'] ?? '') === 'customer'): ?>
        <form id="reviewForm" class="review-form" onsubmit="return false;">
            <input type="hidden" id="review_product_id" value="<?= e($product['id']) ?>">
            <label>Your Name</label>
            <input type="text" value="<?= e($_SESSION['name'] ?? '') ?>" disabled>

            <label for="review_comment">Comment</label>
            <textarea id="review_comment" maxlength="500" placeholder="Write your review within 500 characters"></textarea>
            <small class="small-note">Comment cannot be empty. Maximum 500 characters.</small>

            <button type="button" id="submitReviewBtn" class="primary-btn">Post Review</button>
            <span id="reviewMessage" class="small-note"></span>
        </form>
    <?php elseif (!is_logged_in()): ?>
        <p class="empty-state">Please <a href="index.php?page=login">login</a> as customer to post a review.</p>
    <?php endif; ?>

    <div id="reviewList" class="review-list">
        <?php if (empty($reviews)): ?>
            <div class="empty-state" id="noReviewMsg">No review found for this product.</div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <article class="review-card" id="review-<?= e($review['id']) ?>">
                    <div class="review-head">
                        <div>
                            <strong><?= e($review['reviewer_name']) ?></strong>
                            <p class="meta"><?= e(date('d M Y, h:i A', strtotime($review['created_at']))) ?></p>
                        </div>
                        <?php if (is_logged_in() && ($_SESSION['role'] ?? '') === 'customer' && (int)$review['user_id'] === (int)$_SESSION['user_id']): ?>
                            <button type="button" class="danger-btn review-delete-btn" data-review-id="<?= e($review['id']) ?>">Delete</button>
                        <?php endif; ?>
                    </div>
                    <p><?= nl2br(e($review['comment'])) ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
    window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';
    window.CURRENT_USER_ID = '<?= e($_SESSION['user_id'] ?? '') ?>';
    window.CURRENT_USER_NAME = '<?= e($_SESSION['name'] ?? '') ?>';
</script>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
