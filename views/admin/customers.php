<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
<section class="page-head">
    <div>
        <h1>Customer Management</h1>
        <p class="muted">Admin can remove customer accounts and related data.</p>
    </div>
    <a class="button-secondary" href="index.php?page=admin-dashboard">Back to Dashboard</a>
</section>

<section class="section">
    <div id="adminCustomerMessage" class="small-note"></div>

    <?php if (empty($customers)): ?>
        <div class="empty-state">No customer account found.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table id="customerTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Reviews</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr id="customer-row-<?= e($customer['id']) ?>">
                            <td><?= e($customer['id']) ?></td>
                            <td><?= e($customer['name']) ?></td>
                            <td><?= e($customer['email']) ?></td>
                            <td><?= e($customer['order_count']) ?></td>
                            <td><?= e($customer['review_count']) ?></td>
                            <td><?= e(date('d M Y', strtotime($customer['created_at']))) ?></td>
                            <td>
                                <button type="button" class="danger-btn admin-delete-customer-btn" data-customer-id="<?= e($customer['id']) ?>">Delete</button>
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
