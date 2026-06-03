<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
    <section class="page-head">
        <div>
            <h1>Order Management</h1>
            <p>Accept pending customer orders or delete invalid orders.</p>
        </div>
        <a class="button-secondary" href="index.php?page=admin-dashboard">Back to Dashboard</a>
    </section>

    <section class="section">
        <?php if (empty($orders)): ?>
            <p>No customer order found.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= e($order['id']) ?></td>
                                <td>
                                    <?= e($order['customer_name']) ?><br>
                                    <span class="meta"><?= e($order['customer_email']) ?></span>
                                </td>
                                <td><?= e($order['item_count']) ?></td>
                                <td>৳<?= number_format((float)$order['total_amount'], 2) ?></td>
                                <td><?= e(payment_method_label($order['payment_method'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= e($order['status']) ?>">
                                        <?= e(ucfirst($order['status'])) ?>
                                    </span>
                                </td>
                                <td><?= e(date('d M Y, h:i A', strtotime($order['order_date']))) ?></td>
                                <td>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <form action="index.php?page=admin-orders" method="POST" class="inline-form" onsubmit="return confirm('Accept this order?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="order_id" value="<?= e($order['id']) ?>">
                                            <input type="hidden" name="action" value="accept_order">
                                            <button type="submit" class="mini-btn">Accept</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="meta">Already accepted</span>
                                    <?php endif; ?>

                                    <form action="index.php?page=admin-orders" method="POST" class="inline-form" onsubmit="return confirm('Delete this order?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="order_id" value="<?= e($order['id']) ?>">
                                        <input type="hidden" name="action" value="delete_order">
                                        <button type="submit" class="danger-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
