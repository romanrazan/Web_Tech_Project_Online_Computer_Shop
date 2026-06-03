<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<main class="container">
<section class="page-head">
    <div>
        <h1>My Cart</h1>
        <p>Update quantity, remove items, select payment method, and place your order.</p>
    </div>
    <a class="button-secondary" href="index.php?page=products">Continue Shopping</a>
</section>

<div id="cartAlert" class="small-note"></div>

<?php if (empty($cartItems)): ?>
    <div class="empty-state">
        Your cart is empty. <a href="index.php?page=products">Browse products</a>.
    </div>
<?php else: ?>
    <section class="cart-card">
        <div class="table-wrap">
            <table id="cartTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr id="cart-row-<?= e($item['product_id']) ?>">
                            <td>
                                <div class="cart-product">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= e($product['image_path']) ?>">
                                    <?php else: ?>
                                        <div class="cart-img-placeholder">No Image</div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= e($item['name']) ?></strong><br>
                                        <span class="meta"><?= e($item['category_name']) ?> • <?= e($item['brand_name']) ?></span><br>
                                        <span class="small-note">Available stock: <?= e($item['stock']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>৳<?= number_format((float)$item['price'], 2) ?></td>
                            <td>
                                <div class="qty-control">
                                    <button type="button" class="qty-btn cart-update-btn" data-product-id="<?= e($item['product_id']) ?>" data-change="-1">−</button>
                                    <input type="number" min="1" max="<?= e($item['stock']) ?>" value="<?= e($item['quantity']) ?>" class="cart-quantity" data-product-id="<?= e($item['product_id']) ?>">
                                    <button type="button" class="qty-btn cart-update-btn" data-product-id="<?= e($item['product_id']) ?>" data-change="1">+</button>
                                </div>
                            </td>
                            <td id="subtotal-<?= e($item['product_id']) ?>">৳<?= number_format((float)$item['subtotal'], 2) ?></td>
                            <td>
                                <button type="button" class="danger-btn cart-remove-btn" data-product-id="<?= e($item['product_id']) ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="checkout-panel">
            <div>
                <h2>Total: <span id="cartTotal">৳<?= number_format((float)$cartTotal, 2) ?></span></h2>
                <p class="muted">Select Cash on Delivery, bKash, Nagad, or DBBL/Rocket before checkout.</p>
            </div>

            <form action="index.php?page=checkout" method="POST" onsubmit="return validateCheckoutForm();">
                <?= csrf_field() ?>
                <label for="payment_method">Payment Method</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="dbbl">DBBL / Rocket</option>
                </select>
                <button type="submit" class="primary-btn">Place Order</button>
            </form>
        </div>
    </section>
<?php endif; ?>

<script>
    window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';
</script>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
