function appUrl(path) {
    const base = (window.APP_BASE_URL || '').replace(/\/$/, '');
    const cleanPath = String(path || '').replace(/^\//, '');
    return (base ? base + '/' : '') + cleanPath;
}

function showAlert(message) {
    alert(message);
}

function validateRegisterForm() {
    const name = document.getElementById('reg_name').value.trim();
    const email = document.getElementById('reg_email').value.trim();
    const password = document.getElementById('reg_password').value;
    const confirmPassword = document.getElementById('reg_confirm_password').value;
    const role = document.getElementById('reg_role').value;

    if (name === '') {
        showAlert('Name is required.');
        return false;
    }

    if (!isValidEmail(email)) {
        showAlert('Enter a valid email address.');
        return false;
    }

    if (password.length < 8) {
        showAlert('Password must be at least 8 characters.');
        return false;
    }

    if (password !== confirmPassword) {
        showAlert('Password and confirm password do not match.');
        return false;
    }

    if (role !== 'admin' && role !== 'customer') {
        showAlert('Please select a valid role.');
        return false;
    }

    return true;
}

function validateLoginForm() {
    const email = document.getElementById('login_email').value.trim();
    const password = document.getElementById('login_password').value;

    if (!isValidEmail(email)) {
        showAlert('Enter a valid email address.');
        return false;
    }

    if (password === '') {
        showAlert('Password is required.');
        return false;
    }

    return true;
}

function validateProfileForm() {
    const name = document.getElementById('profile_name').value.trim();
    const email = document.getElementById('profile_email').value.trim();
    const fileInput = document.getElementById('profile_picture');
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmNewPassword = document.getElementById('confirm_new_password').value;

    if (name === '') {
        showAlert('Name is required.');
        return false;
    }

    if (!isValidEmail(email)) {
        showAlert('Enter a valid email address.');
        return false;
    }

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (!allowedTypes.includes(file.type)) {
            showAlert('Only JPEG and PNG profile pictures are allowed.');
            return false;
        }

        if (file.size > 2 * 1024 * 1024) {
            showAlert('Profile picture must be 2MB or smaller.');
            return false;
        }
    }

    const wantsPasswordChange = currentPassword !== '' || newPassword !== '' || confirmNewPassword !== '';

    if (wantsPasswordChange) {
        if (currentPassword === '') {
            showAlert('Current password is required.');
            return false;
        }

        if (newPassword.length < 8) {
            showAlert('New password must be at least 8 characters.');
            return false;
        }

        if (newPassword !== confirmNewPassword) {
            showAlert('New password and confirm password do not match.');
            return false;
        }
    }

    return true;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// AJAX email availability check.
// This satisfies the Task 1 Ajax/JSON requirement.
document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('reg_email');
    const message = document.getElementById('emailCheckMsg');

    if (!emailInput || !message) {
        return;
    }

    emailInput.addEventListener('blur', function () {
        const email = emailInput.value.trim();

        if (!isValidEmail(email)) {
            return;
        }

        fetch(appUrl('api/auth/check-email.php?email=' + encodeURIComponent(email)))
            .then(response => response.json())
            .then(data => {
                message.classList.remove('field-success');

                if (data.exists) {
                    message.textContent = 'This email is already registered.';
                } else {
                    message.textContent = 'Email is available.';
                    message.classList.add('field-success');
                }
            })
            .catch(() => {
                message.textContent = 'Could not check email right now.';
            });
    });
});

// Task 2: Admin validation
function validateCategoryForm() {
    const name = document.getElementById('category_name');
    if (!name) return true;

    if (name.value.trim() === '') {
        alert('Category name is required.');
        name.focus();
        return false;
    }

    return true;
}

function validateBrandForm() {
    const name = document.getElementById('brand_name');
    const category = document.getElementById('brand_category_id');

    if (name && name.value.trim() === '') {
        alert('Brand name is required.');
        name.focus();
        return false;
    }

    if (category && category.value === '') {
        alert('Please select a category.');
        category.focus();
        return false;
    }

    return true;
}

function validateProductForm() {
    const name = document.getElementById('product_name');
    const price = document.getElementById('product_price');
    const category = document.getElementById('product_category_id');
    const brand = document.getElementById('product_brand_id');
    const stock = document.getElementById('product_stock');
    const description = document.getElementById('product_description');
    const review = document.getElementById('product_manufacturer_review');
    const image = document.getElementById('product_image');

    if (name && name.value.trim() === '') {
        alert('Product name is required.');
        name.focus();
        return false;
    }

    if (price && (price.value === '' || Number(price.value) <= 0)) {
        alert('Price must be greater than 0.');
        price.focus();
        return false;
    }

    if (category && category.value === '') {
        alert('Please select a category.');
        category.focus();
        return false;
    }

    if (brand && brand.value === '') {
        alert('Please select a brand.');
        brand.focus();
        return false;
    }

    if (stock && (stock.value === '' || Number(stock.value) < 0 || !Number.isInteger(Number(stock.value)))) {
        alert('Stock must be a non-negative integer.');
        stock.focus();
        return false;
    }

    if (description && description.value.trim() === '') {
        alert('Description is required.');
        description.focus();
        return false;
    }

    if (review && review.value.trim() === '') {
        alert('Manufacturer review is required.');
        review.focus();
        return false;
    }

    if (image && image.files.length > 0) {
        const file = image.files[0];
        const allowed = ['image/jpeg', 'image/png'];
        const maxSize = 2 * 1024 * 1024;

        if (!allowed.includes(file.type)) {
            alert('Only JPEG and PNG images are allowed.');
            image.focus();
            return false;
        }

        if (file.size > maxSize) {
            alert('Product image must be 2MB or smaller.');
            image.focus();
            return false;
        }
    }

    return true;
}

// Task 2: AJAX brand loading for product form
const productCategorySelect = document.getElementById('product_category_id');
const productBrandSelect = document.getElementById('product_brand_id');

if (productCategorySelect && productBrandSelect) {
    productCategorySelect.addEventListener('change', function () {
        const categoryId = this.value;
        productBrandSelect.innerHTML = '<option value="">Loading brands...</option>';

        if (!categoryId) {
            productBrandSelect.innerHTML = '<option value="">Select Brand</option>';
            return;
        }

        fetch(appUrl('api/admin/brands-by-category.php?category_id=' + encodeURIComponent(categoryId)))
            .then(response => response.json())
            .then(data => {
                productBrandSelect.innerHTML = '<option value="">Select Brand</option>';

                if (!data.success || data.brands.length === 0) {
                    productBrandSelect.innerHTML += '<option value="">No brand found</option>';
                    return;
                }

                data.brands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.id;
                    option.textContent = brand.name;
                    productBrandSelect.appendChild(option);
                });
            })
            .catch(() => {
                productBrandSelect.innerHTML = '<option value="">Could not load brands</option>';
            });
    });
}

// Task 3: AJAX product search/filter and cart management
function money(value) {
    return '৳' + value;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text === null || text === undefined ? '' : String(text);
    return div.innerHTML;
}

function validateProductFilterForm() {
    const minPrice = document.getElementById('min_price');
    const maxPrice = document.getElementById('max_price');

    if (!minPrice || !maxPrice) {
        return true;
    }

    const min = minPrice.value.trim();
    const max = maxPrice.value.trim();

    if (min !== '' && (isNaN(Number(min)) || Number(min) < 0)) {
        showAlert('Minimum price must be a positive number.');
        minPrice.focus();
        return false;
    }

    if (max !== '' && (isNaN(Number(max)) || Number(max) < 0)) {
        showAlert('Maximum price must be a positive number.');
        maxPrice.focus();
        return false;
    }

    if (min !== '' && max !== '' && Number(min) > Number(max)) {
        showAlert('Minimum price cannot be greater than maximum price.');
        minPrice.focus();
        return false;
    }

    return true;
}

function productCardHtml(product, loggedIn, role) {
    const image = product.image_path
        ? `<img src="${escapeHtml(product.image_path)}" alt="${escapeHtml(product.name)}">`
        : '<div class="product-placeholder">No Image</div>';

    let action = `<a class="button-secondary" href="index.php?page=product-details&id=${encodeURIComponent(product.id)}">Details</a>`;

    if (loggedIn && role === 'customer') {
        const disabled = Number(product.stock) <= 0 ? 'disabled' : '';
        action += ` <button type="button" class="primary-btn add-to-cart-btn" data-id="${escapeHtml(product.id)}" ${disabled}>Add to Cart</button>`;
    } else if (!loggedIn) {
        action += ' <a class="primary-btn" href="index.php?page=login">Login to Buy</a>';
    }

    return `
        <article class="product-card" data-product-id="${escapeHtml(product.id)}">
            ${image}
            <div class="product-info">
                <h3>${escapeHtml(product.name)}</h3>
                <p class="meta">${escapeHtml(product.category_name)} • ${escapeHtml(product.brand_name)}</p>
                <p>${escapeHtml(product.review_short)}</p>
                <p class="price">৳${escapeHtml(product.price_formatted)}</p>
                <p class="stock ${Number(product.stock) > 0 ? 'in-stock' : 'out-stock'}">
                    ${Number(product.stock) > 0 ? 'In stock: ' + escapeHtml(product.stock) : 'Out of stock'}
                </p>
            </div>
            <div class="product-actions">${action}</div>
        </article>
    `;
}

function loadProductsByAjax() {
    if (!validateProductFilterForm()) {
        return;
    }

    const grid = document.getElementById('productGrid');
    const form = document.getElementById('productSearchForm');

    if (!grid || !form) {
        return;
    }

    const params = new URLSearchParams(new FormData(form));
    grid.innerHTML = '<div class="empty-state">Loading products...</div>';

    fetch(appUrl('api/products/search.php?' + params.toString()))
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                grid.innerHTML = '<div class="empty-state">' + escapeHtml((data.errors || ['Search failed.']).join(' ')) + '</div>';
                return;
            }

            if (data.products.length === 0) {
                grid.innerHTML = '<div class="empty-state">No products found.</div>';
                return;
            }

            grid.innerHTML = data.products.map(product => productCardHtml(product, data.logged_in, data.role)).join('');
        })
        .catch(() => {
            grid.innerHTML = '<div class="empty-state">Could not load products right now.</div>';
        });
}

function addToCart(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', '1');
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/cart/add.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const msg = document.getElementById('cartMessage');
            if (data.success) {
                if (msg) msg.textContent = data.message + ' Cart count: ' + data.cart_count;
                else alert(data.message + ' Cart count: ' + data.cart_count);
            } else {
                alert(data.message || 'Could not add item to cart.');
            }
        })
        .catch(() => alert('Could not add item to cart.'));
}

function updateCartQuantity(productId, quantity) {
    if (!Number.isInteger(Number(quantity)) || Number(quantity) <= 0) {
        alert('Quantity must be a positive integer.');
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/cart/update.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const alertBox = document.getElementById('cartAlert');
            if (!data.success) {
                if (alertBox) alertBox.textContent = data.message || 'Cart update failed.';
                else alert(data.message || 'Cart update failed.');
                return;
            }

            const subtotal = document.getElementById('subtotal-' + productId);
            const total = document.getElementById('cartTotal');

            if (subtotal) subtotal.textContent = money(data.subtotal);
            if (total) total.textContent = money(data.cart_total);
            if (alertBox) alertBox.textContent = data.message;
        })
        .catch(() => alert('Could not update cart.'));
}

function removeCartItem(productId) {
    if (!confirm('Remove this product from cart?')) {
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/cart/remove.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Could not remove item.');
                return;
            }

            const row = document.getElementById('cart-row-' + productId);
            const total = document.getElementById('cartTotal');
            const alertBox = document.getElementById('cartAlert');

            if (row) row.remove();
            if (total) total.textContent = money(data.cart_total);
            if (alertBox) alertBox.textContent = data.message;

            const tbody = document.querySelector('#cartTable tbody');
            if (tbody && tbody.children.length === 0) {
                location.reload();
            }
        })
        .catch(() => alert('Could not remove item.'));
}

document.addEventListener('DOMContentLoaded', function () {
    const searchBtn = document.getElementById('ajaxSearchBtn');
    const searchInput = document.getElementById('search_q');
    const form = document.getElementById('productSearchForm');

    if (searchBtn) {
        searchBtn.addEventListener('click', loadProductsByAjax);
    }

    if (searchInput) {
        let timer = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(loadProductsByAjax, 350);
        });
    }

    if (form) {
        ['filter_category', 'filter_brand', 'min_price', 'max_price'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', loadProductsByAjax);
        });
    }

    document.body.addEventListener('click', function (event) {
        const addBtn = event.target.closest('.add-to-cart-btn');
        if (addBtn) {
            addToCart(addBtn.dataset.id);
            return;
        }

        const updateBtn = event.target.closest('.cart-update-btn');
        if (updateBtn) {
            const productId = updateBtn.dataset.productId;
            const input = document.querySelector('.cart-quantity[data-product-id="' + productId + '"]');
            if (!input) return;

            const nextQuantity = Number(input.value) + Number(updateBtn.dataset.change);
            if (nextQuantity <= 0) {
                alert('Quantity must be at least 1.');
                return;
            }

            input.value = nextQuantity;
            updateCartQuantity(productId, nextQuantity);
            return;
        }

        const removeBtn = event.target.closest('.cart-remove-btn');
        if (removeBtn) {
            removeCartItem(removeBtn.dataset.productId);
        }
    });

    document.body.addEventListener('change', function (event) {
        const qtyInput = event.target.closest('.cart-quantity');
        if (qtyInput) {
            updateCartQuantity(qtyInput.dataset.productId, qtyInput.value);
        }
    });
});

// Task 4: Review, checkout, and admin removal validation/AJAX
function validateCheckoutForm() {
    const payment = document.getElementById('payment_method');
    if (!payment || payment.value === '') {
        alert('Please select a payment method.');
        if (payment) payment.focus();
        return false;
    }

    const allowedPaymentMethods = ['cash_on_delivery', 'bkash', 'nagad', 'dbbl'];
    if (!allowedPaymentMethods.includes(payment.value)) {
        alert('Invalid payment method selected. Please choose Cash on Delivery, bKash, Nagad, or DBBL/Rocket.');
        payment.focus();
        return false;
    }

    return confirm('Place this order now?');
}

function validateReviewComment(comment) {
    const trimmed = comment.trim();

    if (trimmed === '') {
        alert('Review comment cannot be empty.');
        return false;
    }

    if (trimmed.length > 500) {
        alert('Review comment must be within 500 characters.');
        return false;
    }

    return true;
}

function reviewCardHtml(review) {
    return `
        <article class="review-card" id="review-${escapeHtml(review.id)}">
            <div class="review-head">
                <div>
                    <strong>${escapeHtml(review.reviewer_name)}</strong>
                    <p class="meta">${escapeHtml(review.created_at)}</p>
                </div>
                <button type="button" class="danger-btn review-delete-btn" data-review-id="${escapeHtml(review.id)}">Delete</button>
            </div>
            <p>${escapeHtml(review.comment).replace(/\n/g, '<br>')}</p>
        </article>
    `;
}

function addReview() {
    const productInput = document.getElementById('review_product_id');
    const commentInput = document.getElementById('review_comment');
    const message = document.getElementById('reviewMessage');

    if (!productInput || !commentInput) {
        return;
    }

    const comment = commentInput.value;
    if (!validateReviewComment(comment)) {
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productInput.value);
    formData.append('comment', comment.trim());
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/reviews/add.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                if (message) message.textContent = data.message || 'Could not post review.';
                else alert(data.message || 'Could not post review.');
                return;
            }

            const list = document.getElementById('reviewList');
            const empty = document.getElementById('noReviewMsg');
            if (empty) empty.remove();
            if (list) list.insertAdjacentHTML('afterbegin', reviewCardHtml(data.review));
            commentInput.value = '';
            if (message) message.textContent = data.message;
        })
        .catch(() => alert('Could not post review.'));
}

function deleteOwnReview(reviewId) {
    if (!confirm('Delete your review?')) {
        return;
    }

    const formData = new FormData();
    formData.append('review_id', reviewId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/reviews/delete.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Could not delete review.');
                return;
            }

            const row = document.getElementById('review-' + reviewId);
            if (row) row.remove();

            const msg = document.getElementById('myReviewMessage');
            if (msg) msg.textContent = data.message || 'Review deleted successfully.';

            const reviewList = document.getElementById('reviewList');
            if (reviewList && reviewList.querySelectorAll('.review-card').length === 0) {
                reviewList.insertAdjacentHTML('afterend', '<div class="empty-state" id="noMyReviewMsg">You have not posted any review yet.</div>');
                reviewList.remove();
            }
        })
        .catch(() => alert('Could not delete review.'));
}

function adminDeleteReview(reviewId) {
    if (!confirm('Delete this review permanently?')) {
        return;
    }

    const formData = new FormData();
    formData.append('review_id', reviewId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/admin/delete-review.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const msg = document.getElementById('adminReviewMessage');
            if (!data.success) {
                if (msg) msg.textContent = data.message || 'Could not delete review.';
                else alert(data.message || 'Could not delete review.');
                return;
            }

            const row = document.getElementById('admin-review-row-' + reviewId);
            if (row) row.remove();
            if (msg) msg.textContent = data.message;
        })
        .catch(() => alert('Could not delete review.'));
}

function adminDeleteCustomer(customerId) {
    if (!confirm('Delete this customer and related reviews, cart items, and orders?')) {
        return;
    }

    const formData = new FormData();
    formData.append('customer_id', customerId);
    formData.append('csrf_token', window.CSRF_TOKEN || '');

    fetch(appUrl('api/admin/delete-customer.php'), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const msg = document.getElementById('adminCustomerMessage');
            if (!data.success) {
                if (msg) msg.textContent = data.message || 'Could not delete customer.';
                else alert(data.message || 'Could not delete customer.');
                return;
            }

            const row = document.getElementById('customer-row-' + customerId);
            if (row) row.remove();
            if (msg) msg.textContent = data.message;
        })
        .catch(() => alert('Could not delete customer.'));
}

document.addEventListener('DOMContentLoaded', function () {
    const submitReviewBtn = document.getElementById('submitReviewBtn');
    if (submitReviewBtn) {
        submitReviewBtn.addEventListener('click', addReview);
    }

    document.body.addEventListener('click', function (event) {
        const reviewDelete = event.target.closest('.review-delete-btn');
        if (reviewDelete) {
            deleteOwnReview(reviewDelete.dataset.reviewId);
            return;
        }

        const adminReviewDelete = event.target.closest('.admin-delete-review-btn');
        if (adminReviewDelete) {
            adminDeleteReview(adminReviewDelete.dataset.reviewId);
            return;
        }

        const adminCustomerDelete = event.target.closest('.admin-delete-customer-btn');
        if (adminCustomerDelete) {
            adminDeleteCustomer(adminCustomerDelete.dataset.customerId);
        }
    });
});
