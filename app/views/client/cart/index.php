<?php
// Shopping Cart Page for Phase 3.3
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - 5S Fashion</title>
    <meta name="description" content="Giỏ hàng của bạn tại 5S Fashion - Xem lại các sản phẩm đã chọn và tiến hành thanh toán">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/client.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/cart.css">
</head>
<body>
    <!-- Header -->
    <?php include '../layouts/header.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-section">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </div>
    </nav>

    <!-- Cart Section -->
    <section class="cart-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Cart Header -->
                    <div class="cart-header d-flex justify-content-between align-items-center mb-4">
                        <h2 class="cart-title">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Giỏ hàng của bạn
                        </h2>
                        <div class="cart-actions">
                            <button class="btn btn-outline-danger btn-sm" onclick="clearCart()" id="clearCartBtn">
                                <i class="fas fa-trash me-2"></i>Xóa tất cả
                            </button>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div class="cart-items" id="cartItemsContainer">
                        <!-- Empty cart message (will be hidden when items exist) -->
                        <div class="empty-cart text-center py-5" id="emptyCartMessage">
                            <div class="empty-cart-icon mb-4">
                                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">Giỏ hàng trống</h4>
                            <p class="text-muted mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                            <a href="/shop" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>

                        <!-- Cart items will be dynamically loaded here -->
                        <div class="cart-items-list" id="cartItemsList" style="display: none;">
                            <!-- Items will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Continue Shopping -->
                    <div class="continue-shopping mt-4" id="continueShoppingSection" style="display: none;">
                        <a href="/shop" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Cart Summary -->
                    <div class="cart-summary sticky-top" id="cartSummary" style="display: none; top: 120px;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>
                                    Tổng đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Summary items -->
                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="subtotal">0đ</span>
                                </div>

                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span id="shippingFee">30.000đ</span>
                                </div>

                                <div class="summary-item d-flex justify-content-between mb-2" id="discountRow" style="display: none;">
                                    <span class="text-success">Giảm giá:</span>
                                    <span class="text-success" id="discountAmount">-0đ</span>
                                </div>

                                <hr>

                                <div class="summary-total d-flex justify-content-between mb-3">
                                    <strong>Tổng cộng:</strong>
                                    <strong class="text-primary" id="totalAmount">0đ</strong>
                                </div>

                                <!-- Promo Code -->
                                <div class="promo-code-section mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Mã giảm giá" id="promoCodeInput">
                                        <button class="btn btn-outline-secondary" type="button" onclick="applyPromoCode()">
                                            Áp dụng
                                        </button>
                                    </div>
                                </div>

                                <!-- Free shipping notice -->
                                <div class="free-shipping-notice mb-3" id="freeShippingNotice">
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" id="freeShippingProgress" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="freeShippingText">
                                        Mua thêm <strong id="remainingAmount">500.000đ</strong> để được <strong>miễn phí vận chuyển</strong>
                                    </small>
                                </div>

                                <!-- Checkout Button -->
                                <button class="btn btn-primary btn-lg w-100 mb-2" onclick="proceedToCheckout()" id="checkoutBtn">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Tiến hành thanh toán
                                </button>

                                <!-- Payment Methods -->
                                <div class="payment-methods text-center">
                                    <small class="text-muted d-block mb-2">Phương thức thanh toán:</small>
                                    <div class="payment-icons">
                                        <img src="/5s-fashion/public/assets/images/visa.png" alt="Visa" class="payment-icon" onerror="this.style.display='none'">
                                        <img src="/5s-fashion/public/assets/images/mastercard.png" alt="Mastercard" class="payment-icon" onerror="this.style.display='none'">
                                        <img src="/5s-fashion/public/assets/images/momo.png" alt="MoMo" class="payment-icon" onerror="this.style.display='none'">
                                        <img src="/5s-fashion/public/assets/images/vnpay.png" alt="VNPay" class="payment-icon" onerror="this.style.display='none'">
                                    </div>
                                </div>

                                <!-- Security Notice -->
                                <div class="security-notice text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Thanh toán an toàn & bảo mật
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Recommended Products -->
                        <div class="recommended-products mt-4">
                            <h6 class="mb-3">
                                <i class="fas fa-heart me-2"></i>
                                Có thể bạn quan tâm
                            </h6>
                            <div class="recommended-items" id="recommendedProducts">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recently Viewed Products -->
    <section class="recently-viewed py-5 bg-light" id="recentlyViewedSection" style="display: none;">
        <div class="container">
            <h4 class="section-title mb-4">
                <i class="fas fa-history me-2"></i>
                Sản phẩm đã xem
            </h4>
            <div class="row" id="recentlyViewedProducts">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../layouts/footer.php'; ?>

    <!-- Cart Item Template -->
    <script type="text/template" id="cartItemTemplate">
        <div class="cart-item" data-id="{{id}}">
            <div class="row align-items-center">
                <div class="col-md-2 col-3">
                    <div class="item-image">
                        <img src="{{image}}" alt="{{name}}" class="img-fluid rounded">
                    </div>
                </div>
                <div class="col-md-4 col-9">
                    <div class="item-details">
                        <h6 class="item-name">
                            <a href="/product/{{slug}}" class="text-dark text-decoration-none">{{name}}</a>
                        </h6>
                        <div class="item-variant text-muted small">{{variant}}</div>
                        <div class="item-price">
                            <span class="current-price fw-bold text-primary">{{price}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="quantity-controls">
                        <button class="quantity-btn decrease" onclick="updateQuantity({{id}}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="quantity-input" value="{{quantity}}" min="1" max="99"
                               onchange="setQuantity({{id}}, this.value)">
                        <button class="quantity-btn increase" onclick="updateQuantity({{id}}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="item-total text-end">
                        <div class="total-price fw-bold">{{totalPrice}}</div>
                    </div>
                </div>
                <div class="col-md-1 col-2">
                    <button class="btn btn-outline-danger btn-sm remove-item"
                            onclick="removeItem({{id}})" title="Xóa sản phẩm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </script>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/cart.js"></script>

    <!-- Initialize Cart -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartManager = new CartManager();
            cartManager.loadCart();
            cartManager.loadRecommendedProducts();
            cartManager.loadRecentlyViewed();
        });
    </script>
</body>
</html>
