<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
<section class="section order-confirmation">
    <h1>Order Confirmed</h1>
    <p class="muted">Your order has been placed successfully.</p>

    <?php if (($order['status'] ?? '') === 'accepted'): ?>
        <div class="flash success">Your order has been accepted by admin.</div>
    <?php else: ?>
        <div class="flash error">Your order is pending. Please wait for admin acceptance.</div>
    <?php endif; ?>

    <div class="summary-grid">
        <div class="stat-card">
            <h2>#<?= e($order['id']) ?></h2>
            <p>Order ID</p>
        </div>
        <div class="stat-card">
            <h2>৳<?= number_format((float)$order['total_amount'], 2) ?></h2>
            <p>Total Amount</p>
        </div>
        <div class="stat-card">
            <h2><?= e(payment_method_label($order['payment_method'])) ?></h2>
            <p>Payment Method</p>
        </div>
        <div class="stat-card">
            <h2><?= e(ucfirst($order['status'])) ?></h2>
            <p>Status</p>
        </div>
    </div>
</section>

<section class="section">
    <h2>Order Summary</h2>
    <p class="muted">Order date: <?= e(date('d M Y, h:i A', strtotime($order['order_date']))) ?></p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?= e($item['product_name']) ?></td>
                        <td><?= e($item['quantity']) ?></td>
                        <td>৳<?= number_format((float)$item['unit_price'], 2) ?></td>
                        <td>৳<?= number_format((float)$item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="action-row">
        <a class="button-secondary" href="index.php?page=products">Continue Shopping</a>
    </div>
</section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
