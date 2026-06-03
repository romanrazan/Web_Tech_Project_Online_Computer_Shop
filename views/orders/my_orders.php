<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
    <section class="page-head">
        <div>
            <h1>My Orders</h1>
            <p>Track your order status here. Pending means waiting for admin acceptance.</p>
        </div>
        <a class="button-secondary" href="index.php?page=products">Continue Shopping</a>
    </section>

    <section class="section">
        <?php if (empty($orders)): ?>
            <p>You have not placed any order yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= e($order['id']) ?></td>
                                <td><?= e($order['item_count']) ?></td>
                                <td>৳<?= number_format((float)$order['total_amount'], 2) ?></td>
                                <td><?= e(payment_method_label($order['payment_method'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= e($order['status']) ?>">
                                        <?= e(ucfirst($order['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($order['status'] === 'accepted'): ?>
                                        Your order has been accepted by admin.
                                    <?php else: ?>
                                        Waiting for admin acceptance.
                                    <?php endif; ?>
                                </td>
                                <td><?= e(date('d M Y, h:i A', strtotime($order['order_date']))) ?></td>
                                <td><a class="mini-btn" href="index.php?page=order-confirmation&id=<?= e($order['id']) ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
