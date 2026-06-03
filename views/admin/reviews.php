<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
<section class="page-head">
    <div>
        <h1>Review Management</h1>
        <p class="muted">Admin can remove any customer review.</p>
    </div>
    <a class="button-secondary" href="index.php?page=admin-dashboard">Back to Dashboard</a>
</section>

<section class="section">
    <div id="adminReviewMessage" class="small-note"></div>

    <?php if (empty($reviews)): ?>
        <div class="empty-state">No review found.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table id="adminReviewTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Reviewer</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr id="admin-review-row-<?= e($review['id']) ?>">
                            <td><?= e($review['id']) ?></td>
                            <td><?= e($review['product_name']) ?></td>
                            <td><?= e($review['reviewer_name']) ?><br><span class="meta"><?= e($review['user_email']) ?></span></td>
                            <td><?= nl2br(e($review['comment'])) ?></td>
                            <td><?= e(date('d M Y, h:i A', strtotime($review['created_at']))) ?></td>
                            <td>
                                <button type="button" class="danger-btn admin-delete-review-btn" data-review-id="<?= e($review['id']) ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
    window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';
</script>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
