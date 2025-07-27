<?php
// Order Success Page for Phase 3.4
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - 5S Fashion</title>
    <meta name="description" content="Cảm ơn bạn đã đặt hàng tại 5S Fashion">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/client.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/order-success.css">
</head>
<body>
    <!-- Header -->
    <?php include '../layouts/header.php'; ?>

    <!-- Success Section -->
    <section class="success-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Success Animation -->
                    <div class="success-animation text-center mb-5">
                        <div class="success-checkmark">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark-check" fill="none" d="m12.5 23l6.5 6.5L37 16"/>
                            </svg>
                        </div>
                        <h1 class="success-title">Đặt hàng thành công!</h1>
                        <p class="success-subtitle">Cảm ơn bạn đã tin tưởng và mua sắm tại 5S Fashion</p>
                    </div>

                    <!-- Order Details -->
                    <div class="order-details-card mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Thông tin đơn hàng
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="order-info-item">
                                            <span class="label">Mã đơn hàng:</span>
                                            <span class="value" id="orderCode">ORD20240127001</span>
                                        </div>
                                        <div class="order-info-item">
                                            <span class="label">Ngày đặt hàng:</span>
                                            <span class="value" id="orderDate">27/01/2024 15:30</span>
                                        </div>
                                        <div class="order-info-item">
                                            <span class="label">Phương thức thanh toán:</span>
                                            <span class="value" id="paymentMethod">Thanh toán khi nhận hàng</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="order-info-item">
                                            <span class="label">Tổng tiền:</span>
                                            <span class="value total-amount" id="totalAmount">599.000đ</span>
                                        </div>
                                        <div class="order-info-item">
                                            <span class="label">Trạng thái:</span>
                                            <span class="badge bg-warning">Đang xử lý</span>
                                        </div>
                                        <div class="order-info-item">
                                            <span class="label">Dự kiến giao hàng:</span>
                                            <span class="value" id="estimatedDelivery">30/01/2024 - 01/02/2024</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items-card mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-shopping-bag me-2"></i>
                                    Sản phẩm đã đặt
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="order-items-list" id="orderItemsList">
                                    <!-- Items will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Info -->
                    <div class="shipping-info-card mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-truck me-2"></i>
                                    Thông tin giao hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="shipping-detail">
                                            <strong>Người nhận:</strong>
                                            <span id="recipientName">Nguyễn Văn A</span>
                                        </div>
                                        <div class="shipping-detail">
                                            <strong>Số điện thoại:</strong>
                                            <span id="recipientPhone">0901234567</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="shipping-detail">
                                            <strong>Địa chỉ giao hàng:</strong>
                                            <span id="shippingAddress">123 Đường ABC, Phường XYZ, Quận 1, TP.HCM</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="next-steps-card mb-4">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    Bước tiếp theo
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item active">
                                        <div class="timeline-marker">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Đặt hàng thành công</h6>
                                            <p class="text-muted">Đơn hàng của bạn đã được tiếp nhận</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Chuẩn bị hàng</h6>
                                            <p class="text-muted">Chúng tôi đang chuẩn bị sản phẩm</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="fas fa-shipping-fast"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Đang giao hàng</h6>
                                            <p class="text-muted">Đơn hàng đang trên đường đến bạn</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Giao hàng thành công</h6>
                                            <p class="text-muted">Đơn hàng đã được giao thành công</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons text-center">
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <a href="/order-tracking" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>
                                    Theo dõi đơn hàng
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <button class="btn btn-success btn-lg w-100" onclick="downloadInvoice()">
                                    <i class="fas fa-download me-2"></i>
                                    Tải hóa đơn
                                </button>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="/shop" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-shopping-bag me-2"></i>
                                    Tiếp tục mua sắm
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="/" class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-home me-2"></i>
                                    Về trang chủ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Email Notification -->
    <section class="email-notification py-4 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope fa-2x me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Thông báo qua email</h6>
                                <p class="mb-0">
                                    Chúng tôi đã gửi email xác nhận đơn hàng đến địa chỉ của bạn.
                                    Vui lòng kiểm tra hộp thư (bao gồm cả thư mục spam).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="related-products py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h4 class="section-title text-center mb-4">
                        <i class="fas fa-heart me-2"></i>
                        Có thể bạn quan tâm
                    </h4>
                    <div class="row" id="relatedProducts">
                        <!-- Will be populated by JavaScript -->
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
            </div>
            <div class="item-details">
                <h6 class="item-name">{{name}}</h6>
                <div class="item-variant">{{variant}}</div>
                <div class="item-quantity">Số lượng: {{quantity}}</div>
            </div>
            <div class="item-price">
                <div class="price">{{price}}</div>
                <div class="total">{{totalPrice}}</div>
            </div>
        </div>
    </script>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/order-success.js"></script>

    <!-- Initialize Order Success -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.orderSuccessManager = new OrderSuccessManager();
            orderSuccessManager.loadOrderDetails();
            orderSuccessManager.loadRelatedProducts();
        });
    </script>
</body>
</html>
