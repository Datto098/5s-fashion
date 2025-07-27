<?php
// Checkout Page for Phase 3.3
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - 5S Fashion</title>
    <meta name="description" content="Hoàn tất đơn hàng tại 5S Fashion - Thanh toán an toàn và bảo mật">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/client.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/checkout.css">
</head>
<body>
    <!-- Header -->
    <?php include '../layouts/header.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-section">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/cart">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </div>
    </nav>

    <!-- Checkout Progress -->
    <section class="checkout-progress py-3 bg-light">
        <div class="container">
            <div class="progress-steps d-flex justify-content-center">
                <div class="step active" data-step="1">
                    <div class="step-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="step-label">Giỏ hàng</div>
                </div>
                <div class="step-line"></div>
                <div class="step active" data-step="2">
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="step-label">Thanh toán</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="step-label">Hoàn tất</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Section -->
    <section class="checkout-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Checkout Form -->
                    <form id="checkoutForm" class="checkout-form">
                        <!-- Customer Information -->
                        <div class="checkout-section-card mb-4">
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-user me-2"></i>
                                    Thông tin khách hàng
                                </h4>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fullName" class="form-label">Họ và tên *</label>
                                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Số điện thoại *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <div class="form-text">Email để nhận thông báo đơn hàng</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="checkout-section-card mb-4">
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-truck me-2"></i>
                                    Thông tin giao hàng
                                </h4>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="province" class="form-label">Tỉnh/Thành phố *</label>
                                        <select class="form-select" id="province" name="province" required>
                                            <option value="">Chọn tỉnh/thành phố</option>
                                            <option value="ho-chi-minh">TP. Hồ Chí Minh</option>
                                            <option value="ha-noi">Hà Nội</option>
                                            <option value="da-nang">Đà Nẵng</option>
                                            <option value="can-tho">Cần Thơ</option>
                                            <option value="hai-phong">Hải Phòng</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="district" class="form-label">Quận/Huyện *</label>
                                        <select class="form-select" id="district" name="district" required>
                                            <option value="">Chọn quận/huyện</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ward" class="form-label">Phường/Xã *</label>
                                        <select class="form-select" id="ward" name="ward" required>
                                            <option value="">Chọn phường/xã</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="address" class="form-label">Địa chỉ cụ thể *</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                               placeholder="Số nhà, tên đường..." required>
                                    </div>
                                </div>

                                <!-- Shipping Method -->
                                <div class="shipping-methods">
                                    <h6 class="mb-3">Phương thức vận chuyển</h6>
                                    <div class="shipping-option">
                                        <input type="radio" id="standard" name="shippingMethod" value="standard" class="form-check-input" checked>
                                        <label for="standard" class="shipping-label">
                                            <div class="shipping-info">
                                                <div class="shipping-name">Giao hàng tiêu chuẩn</div>
                                                <div class="shipping-desc">Giao trong 3-5 ngày làm việc</div>
                                            </div>
                                            <div class="shipping-price">30.000đ</div>
                                        </label>
                                    </div>
                                    <div class="shipping-option">
                                        <input type="radio" id="express" name="shippingMethod" value="express" class="form-check-input">
                                        <label for="express" class="shipping-label">
                                            <div class="shipping-info">
                                                <div class="shipping-name">Giao hàng nhanh</div>
                                                <div class="shipping-desc">Giao trong 1-2 ngày làm việc</div>
                                            </div>
                                            <div class="shipping-price">50.000đ</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section-card mb-4">
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-credit-card me-2"></i>
                                    Phương thức thanh toán
                                </h4>
                            </div>
                            <div class="section-body">
                                <div class="payment-methods">
                                    <div class="payment-option">
                                        <input type="radio" id="cod" name="paymentMethod" value="cod" class="form-check-input" checked>
                                        <label for="cod" class="payment-label">
                                            <div class="payment-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Thanh toán khi nhận hàng (COD)</div>
                                                <div class="payment-desc">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="vnpay" name="paymentMethod" value="vnpay" class="form-check-input">
                                        <label for="vnpay" class="payment-label">
                                            <div class="payment-icon">
                                                <img src="/5s-fashion/public/assets/images/vnpay.png" alt="VNPay" onerror="this.parentElement.innerHTML='<i class=\'fas fa-credit-card\'></i>'">
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Ví điện tử VNPay</div>
                                                <div class="payment-desc">Thanh toán qua ví điện tử VNPay</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="momo" name="paymentMethod" value="momo" class="form-check-input">
                                        <label for="momo" class="payment-label">
                                            <div class="payment-icon">
                                                <img src="/5s-fashion/public/assets/images/momo.png" alt="MoMo" onerror="this.parentElement.innerHTML='<i class=\'fas fa-mobile-alt\'></i>'">
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Ví MoMo</div>
                                                <div class="payment-desc">Thanh toán qua ví điện tử MoMo</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="bank" name="paymentMethod" value="bank" class="form-check-input">
                                        <label for="bank" class="payment-label">
                                            <div class="payment-icon">
                                                <i class="fas fa-university"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Chuyển khoản ngân hàng</div>
                                                <div class="payment-desc">Chuyển khoản qua Internet Banking</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="checkout-section-card mb-4">
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Ghi chú đơn hàng
                                </h4>
                            </div>
                            <div class="section-body">
                                <textarea class="form-control" id="orderNotes" name="orderNotes" rows="3"
                                          placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="order-summary sticky-top" style="top: 120px;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Tóm tắt đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Order Items -->
                                <div class="order-items mb-3" id="orderItems">
                                    <!-- Will be populated by JavaScript -->
                                </div>

                                <hr>

                                <!-- Order Summary -->
                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="orderSubtotal">0đ</span>
                                </div>

                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span id="orderShipping">30.000đ</span>
                                </div>

                                <div class="summary-item d-flex justify-content-between mb-2" id="orderDiscountRow" style="display: none;">
                                    <span class="text-success">Giảm giá:</span>
                                    <span class="text-success" id="orderDiscount">-0đ</span>
                                </div>

                                <hr>

                                <div class="summary-total d-flex justify-content-between mb-3">
                                    <strong>Tổng cộng:</strong>
                                    <strong class="text-primary" id="orderTotal">0đ</strong>
                                </div>

                                <!-- Promo Code -->
                                <div class="promo-code-section mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Mã giảm giá" id="checkoutPromoCode">
                                        <button class="btn btn-outline-secondary" type="button" onclick="applyCheckoutPromoCode()">
                                            Áp dụng
                                        </button>
                                    </div>
                                </div>

                                <!-- Place Order Button -->
                                <button type="button" class="btn btn-success btn-lg w-100 mb-3" onclick="placeOrder()" id="placeOrderBtn">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Đặt hàng
                                </button>

                                <!-- Security Notice -->
                                <div class="security-notice text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Thông tin của bạn được bảo mật
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Return Policy -->
                        <div class="return-policy mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i class="fas fa-undo me-2"></i>
                                        Chính sách đổi trả
                                    </h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Đổi trả trong 7 ngày
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Miễn phí đổi size
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Hoàn tiền 100% nếu lỗi
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../layouts/footer.php'; ?>

    <!-- Order Item Template -->
    <script type="text/template" id="orderItemTemplate">
        <div class="order-item">
            <div class="item-image">
                <img src="{{image}}" alt="{{name}}">
                <span class="item-quantity">{{quantity}}</span>
            </div>
            <div class="item-details">
                <div class="item-name">{{name}}</div>
                <div class="item-variant">{{variant}}</div>
                <div class="item-price">{{totalPrice}}</div>
            </div>
        </div>
    </script>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/checkout.js"></script>

    <!-- Initialize Checkout -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.checkoutManager = new CheckoutManager();
            checkoutManager.loadOrder();
            checkoutManager.initializeForm();
        });
    </script>
</body>
</html>
