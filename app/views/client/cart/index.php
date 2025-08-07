<?php $title = 'Giỏ hàng - 5S Fashion'; ?>

<style>
/* Professional Cart Styles */
.cart-section {
    padding: 2rem 0;
    background: #ecf0f1;
    min-height: 60vh;
}

.cart-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(44, 62, 80, 0.1);
    border-radius: 0.5rem;
}

.cart-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 1.5rem;
}

.cart-item {
    border-bottom: 1px solid #ecf0f1;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #ecf0f1;
}

.cart-item:last-child {
    border-bottom: none;
}

.product-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 2px solid #bdc3c7;
}

.product-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    text-decoration: none;
}

.product-title:hover {
    color: #f39c12;
    text-decoration: none;
}

.product-variant {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.product-price {
    color: #e74c3c;
    font-weight: 700;
    font-size: 1.2rem;
}

.original-price {
    color: #95a5a6;
    text-decoration: line-through;
    font-size: 0.9rem;
}

.quantity-controls {
    border: 1px solid #bdc3c7;
    border-radius: 0.375rem;
    overflow: hidden;
    display: inline-flex;
    background: white;
}

.quantity-btn {
    background: white;
    border: none;
    padding: 0.5rem 0.75rem;
    color: #7f8c8d;
    cursor: pointer;
    transition: all 0.2s;
}

.quantity-btn:hover {
    background: #ecf0f1;
    color: #2c3e50;
}

.quantity-btn:active {
    background: #d5dbdb;
}

.quantity-input {
    border: none;
    width: 60px;
    text-align: center;
    padding: 0.5rem 0.25rem;
    font-weight: 600;
}

.quantity-input:focus {
    outline: none;
    box-shadow: none;
}

.remove-btn {
    color: #e74c3c;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.remove-btn:hover {
    color: #fff;
    background: #e74c3c;
    transform: scale(1.1);
}

.cart-summary {
    background: white;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(44, 62, 80, 0.1);
    position: sticky;
    top: 2rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
}

.summary-row:not(:last-child) {
    border-bottom: 1px solid #ecf0f1;
}

.summary-label {
    color: #7f8c8d;
    font-weight: 500;
}

.summary-value {
    font-weight: 600;
    color: #2c3e50;
}

.summary-total {
    font-size: 1.5rem;
    font-weight: 700;
    color: #e74c3c;
    border-top: 2px solid #ecf0f1;
    padding-top: 1rem;
    margin-top: 1rem;
}

.btn-checkout {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border: none;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(44, 62, 80, 0.3);
}

.btn-continue {
    background: transparent;
    color: #2c3e50;
    border: 2px solid #2c3e50;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.btn-continue:hover {
    background: #2c3e50;
    color: white;
    transform: translateY(-2px);
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    color: #7f8c8d;
}

.empty-cart i {
    color: #bdc3c7;
}

.promo-section {
    background: #ecf0f1;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.promo-input {
    border: 1px solid #bdc3c7;
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.promo-btn {
    background: #27ae60;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 600;
}

.promo-btn:hover {
    background: #219a52;
    color: white;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

@media (max-width: 768px) {
    .cart-section {
        padding: 1rem 0;
    }

    .cart-item {
        padding: 1rem;
    }

    .product-image {
        width: 80px;
        height: 80px;
    }

    .cart-summary {
        margin-top: 2rem;
        position: relative;
        top: auto;
    }

    .btn-checkout {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}

/* Toast Styles */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    background: white;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(44, 62, 80, 0.15);
    border-radius: 0.5rem;
    overflow: hidden;
}

.toast-header {
    background: #27ae60;
    color: white;
    border-bottom: none;
}

.toast-body {
    padding: 1rem;
}

.toast.error .toast-header {
    background: #e74c3c;
}

.toast.warning .toast-header {
    background: #f39c12;
    color: #2c3e50;
}
</style>

<!-- Cart Section -->
<div class="cart-section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= url('') ?>" class="text-decoration-none">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card cart-card">
                    <div class="cart-header">
                        <h4 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Giỏ hàng của bạn
                            <span class="badge bg-light text-dark ms-2" id="cart-items-count"><?= $cartCount ?></span>
                        </h4>
                    </div>

                    <div id="cart-items-container">
                        <?php if (!empty($cartItems)): ?>
                            <!-- Cart has items -->
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item" data-item-id="<?= $item['id'] ?>">
                                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <?php
                            $imagePath = $item['product_image'] ?? 'placeholder.jpg';
                            // Remove leading slash if present
                            $imagePath = ltrim($imagePath, '/');
                            // Remove uploads/products/ prefix if present
                            $imagePath = preg_replace('#^uploads/products/#', '', $imagePath);
                            ?>
                            <img src="<?= url('serve-file.php?file=' . urlencode('products/' . $imagePath)) ?>"
                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                 class="product-image img-fluid">
                        </div>                                        <div class="col-md-4">
                                            <a href="<?= url('product/' . ($item['product_slug'] ?? '')) ?>"
                                               class="product-title"><?= htmlspecialchars($item['product_name']) ?></a>
                                            <div class="product-variant">
                                                <?php if (!empty($item['variant_attributes'])): ?>
                                                    <?= htmlspecialchars($item['variant_attributes']) ?>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">Mã SP: <span class="product-sku"><?= $item['product_sku'] ?? 'N/A' ?></span></small>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="product-price"><?= number_format($item['price'], 0, ',', '.') ?> ₫</div>
                                            <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                                                <div class="original-price"><?= number_format($item['original_price'], 0, ',', '.') ?> ₫</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="quantity-controls">
                                                <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'decrease')">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="quantity-input cart-quantity-input"
                                                       min="1" max="99" value="<?= $item['quantity'] ?>"
                                                       data-cart-id="<?= $item['id'] ?>">
                                                <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'increase')">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-2 text-end">
                                            <div class="item-total fw-bold text-danger">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫
                                            </div>
                                            <button type="button" class="remove-btn remove-cart-item mt-2"
                                                    data-cart-id="<?= $item['id'] ?>"
                                                    title="Xóa sản phẩm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Empty cart -->
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h5>Giỏ hàng trống</h5>
                                <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng</p>
                                <a href="<?= url('') ?>" class="btn btn-primary mt-3">
                                    <i class="fas fa-shopping-bag me-2"></i>
                                    Mua sắm ngay
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-4">
                    <a href="<?= url('') ?>" class="btn btn-continue">
                        <i class="fas fa-arrow-left me-2"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <!-- Promo Code Section -->
                <div class="promo-section">
                    <h6 class="mb-3">
                        <i class="fas fa-tag me-2"></i>
                        Mã giảm giá
                    </h6>
                    <div class="input-group">
                        <input type="text" class="form-control promo-input" id="promo-code" placeholder="Nhập mã giảm giá">
                        <button class="btn promo-btn" type="button" onclick="applyPromoCode()">
                            <i class="fas fa-check"></i>
                            Áp dụng
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="cart-summary">
                    <h5 class="mb-4">
                        <i class="fas fa-receipt me-2"></i>
                        Tổng đơn hàng
                    </h5>

                    <div id="cart-summary-content">
                        <div class="summary-row">
                            <span class="summary-label">Tạm tính:</span>
                            <span class="summary-value" id="subtotal"><?= number_format($cartTotal, 0, ',', '.') ?> ₫</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Phí vận chuyển:</span>
                            <span class="summary-value text-success">Miễn phí</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Giảm giá:</span>
                            <span class="summary-value text-success" id="discount">0 ₫</span>
                        </div>

                        <div class="summary-row summary-total">
                            <span class="summary-label">Tổng cộng:</span>
                            <span class="summary-value" id="total"><?= number_format($cartTotal, 0, ',', '.') ?> ₫</span>
                        </div>
                    </div>

                    <button class="btn btn-checkout mt-4" id="checkout-btn" <?= empty($cartItems) ? 'disabled' : '' ?>>
                        <i class="fas fa-credit-card me-2"></i>
                        Thanh toán
                    </button>

                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i>
                            Thanh toán an toàn & bảo mật
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container"></div>

<!-- Templates -->
<template id="cart-item-template">
    <div class="cart-item" data-item-id="">
        <div class="row align-items-center">
            <div class="col-md-2">
                <img src="" alt="" class="product-image img-fluid">
            </div>

            <div class="col-md-4">
                <a href="" class="product-title"></a>
                <div class="product-variant"></div>
                <small class="text-muted">Mã SP: <span class="product-sku"></span></small>
            </div>

            <div class="col-md-2">
                <div class="product-price"></div>
                <div class="original-price"></div>
            </div>

            <div class="col-md-2">
                <div class="quantity-controls">
                    <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'decrease')">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="quantity-input cart-quantity-input" min="1" max="99" value="1">
                    <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'increase')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-2 text-end">
                <div class="item-total fw-bold text-danger"></div>
                <button type="button" class="remove-btn remove-cart-item mt-2" title="Xóa sản phẩm">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<template id="empty-cart-template">
    <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h5>Giỏ hàng trống</h5>
        <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng</p>
        <a href="<?= url('') ?>" class="btn btn-primary mt-3">
            <i class="fas fa-shopping-bag me-2"></i>
            Mua sắm ngay
        </a>
    </div>
</template>

<script>
// Initialize cart when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Global cartManager is now initialized in app.php
    // No need to check or init here anymore
    console.log('Cart page loaded, global cartManager:', window.cartManager);
});

function updateCartQuantity(element, action) {
    const cartItem = element.closest('.cart-item');
    const quantityInput = cartItem.querySelector('.cart-quantity-input');
    const cartId = quantityInput.dataset.cartId;
    let quantity = parseInt(quantityInput.value);

    if (action === 'increase') {
        quantity++;
    } else if (action === 'decrease') {
        quantity--;
    } else if (action === 'change') {
        quantity = parseInt(element.value);
    }

    if (quantity < 1) {
        quantity = 1;
    }

    quantityInput.value = quantity;

    // Call CartManager to update quantity
    if (window.cartManager) {
        window.cartManager.updateCartQuantity(quantityInput);
    }
}

// removeCartItem function removed - handled by CartManager auto-binding

function applyPromoCode() {
    const promoCode = document.getElementById('promo-code').value.trim();

    if (!promoCode) {
        if (window.showWarning) {
            window.showWarning('Vui lòng nhập mã giảm giá');
        } else {
            alert('Vui lòng nhập mã giảm giá');
        }
        return;
    }

    // TODO: Implement promo code API
    if (window.showInfo) {
        window.showInfo('Tính năng mã giảm giá đang được phát triển');
    } else {
        alert('Tính năng mã giảm giá đang được phát triển');
    }
}
</script>
