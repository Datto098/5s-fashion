<?php
// Order Tracking Page for Phase 3.4
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo dõi đơn hàng - 5S Fashion</title>
    <meta name="description" content="Theo dõi tình trạng đơn hàng tại 5S Fashion">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/client.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/order-tracking.css">
</head>
<body>
    <!-- Header -->
    <?php include '../layouts/header.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-section">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Theo dõi đơn hàng</li>
            </ol>
        </div>
    </nav>

    <!-- Tracking Section -->
    <section class="tracking-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- Search Form -->
                    <div class="tracking-search-card mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fas fa-search me-2"></i>
                                    Tra cứu đơn hàng
                                </h4>
                            </div>
                            <div class="card-body">
                                <form id="trackingSearchForm" class="tracking-search-form">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label for="orderCode" class="form-label">Mã đơn hàng *</label>
                                            <input type="text" class="form-control" id="orderCode" name="orderCode"
                                                   placeholder="Nhập mã đơn hàng (ví dụ: ORD20240127001)" required>
                                            <div class="form-text">
                                                Mã đơn hàng được gửi qua email sau khi đặt hàng thành công
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="phone" class="form-label">Số điện thoại</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                   placeholder="Số điện thoại đặt hàng">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-lg" id="searchBtn">
                                                <i class="fas fa-search me-2"></i>Tra cứu đơn hàng
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Search -->
                    <div class="quick-search mb-4">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Tra cứu nhanh
                            </h6>
                            <p class="mb-2">Bạn có thể thử các mã đơn hàng mẫu:</p>
                            <div class="quick-codes">
                                <button class="btn btn-outline-primary btn-sm me-2 mb-2" onclick="quickSearch('ORD20240127001')">
                                    ORD20240127001
                                </button>
                                <button class="btn btn-outline-primary btn-sm me-2 mb-2" onclick="quickSearch('ORD20240126002')">
                                    ORD20240126002
                                </button>
                                <button class="btn btn-outline-primary btn-sm mb-2" onclick="quickSearch('ORD20240125003')">
                                    ORD20240125003
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Results -->
                    <div class="tracking-results" id="trackingResults" style="display: none;">
                        <!-- Order Info -->
                        <div class="order-info-card mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Đơn hàng <span id="foundOrderCode"></span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <span class="label">Ngày đặt hàng:</span>
                                                <span class="value" id="orderDate"></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Khách hàng:</span>
                                                <span class="value" id="customerName"></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Số điện thoại:</span>
                                                <span class="value" id="customerPhone"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <span class="label">Tổng tiền:</span>
                                                <span class="value total-amount" id="orderTotal"></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Trạng thái hiện tại:</span>
                                                <span class="badge" id="currentStatus"></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Dự kiến giao hàng:</span>
                                                <span class="value" id="estimatedDelivery"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tracking Timeline -->
                        <div class="tracking-timeline-card mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-route me-2"></i>
                                        Lộ trình đơn hàng
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="tracking-timeline" id="trackingTimeline">
                                        <!-- Timeline will be populated by JavaScript -->
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
                                        Sản phẩm trong đơn hàng
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="order-items" id="orderItems">
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
                                                <strong>Phương thức giao hàng:</strong>
                                                <span id="shippingMethod"></span>
                                            </div>
                                            <div class="shipping-detail">
                                                <strong>Đơn vị vận chuyển:</strong>
                                                <span id="shippingProvider"></span>
                                            </div>
                                            <div class="shipping-detail">
                                                <strong>Mã vận đơn:</strong>
                                                <span id="trackingNumber"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="shipping-detail">
                                                <strong>Địa chỉ giao hàng:</strong>
                                                <span id="shippingAddress"></span>
                                            </div>
                                            <div class="shipping-detail">
                                                <strong>Ghi chú:</strong>
                                                <span id="orderNotes"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="order-actions text-center">
                            <div class="row g-3">
                                <div class="col-md-4 col-6">
                                    <button class="btn btn-success btn-lg w-100" onclick="downloadInvoice()">
                                        <i class="fas fa-download me-2"></i>
                                        Tải hóa đơn
                                    </button>
                                </div>
                                <div class="col-md-4 col-6">
                                    <button class="btn btn-info btn-lg w-100" onclick="contactSupport()">
                                        <i class="fas fa-phone me-2"></i>
                                        Liên hệ hỗ trợ
                                    </button>
                                </div>
                                <div class="col-md-4 col-12">
                                    <button class="btn btn-primary btn-lg w-100" onclick="trackAnother()">
                                        <i class="fas fa-search me-2"></i>
                                        Tra cứu đơn khác
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div class="no-results" id="noResults" style="display: none;">
                        <div class="alert alert-warning text-center">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                            </div>
                            <h5>Không tìm thấy đơn hàng</h5>
                            <p class="mb-3">
                                Vui lòng kiểm tra lại mã đơn hàng hoặc thông tin bạn đã nhập.
                            </p>
                            <div class="help-actions">
                                <button class="btn btn-outline-primary me-2" onclick="clearSearch()">
                                    <i class="fas fa-redo me-2"></i>Thử lại
                                </button>
                                <button class="btn btn-outline-info" onclick="contactSupport()">
                                    <i class="fas fa-phone me-2"></i>Liên hệ hỗ trợ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Help Section -->
    <section class="help-section py-4 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="help-item">
                                <div class="help-icon mb-2">
                                    <i class="fas fa-phone-alt fa-2x text-primary"></i>
                                </div>
                                <h6>Hotline hỗ trợ</h6>
                                <p class="text-muted">1900 1900</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="help-item">
                                <div class="help-icon mb-2">
                                    <i class="fas fa-envelope fa-2x text-success"></i>
                                </div>
                                <h6>Email hỗ trợ</h6>
                                <p class="text-muted">support@5sfashion.com</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="help-item">
                                <div class="help-icon mb-2">
                                    <i class="fas fa-clock fa-2x text-info"></i>
                                </div>
                                <h6>Giờ làm việc</h6>
                                <p class="text-muted">8:00 - 22:00 hàng ngày</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../layouts/footer.php'; ?>

    <!-- Timeline Item Template -->
    <script type="text/template" id="timelineItemTemplate">
        <div class="timeline-item {{status}}">
            <div class="timeline-marker">
                <i class="{{icon}}"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-time">{{time}}</div>
                <h6 class="timeline-title">{{title}}</h6>
                <p class="timeline-description">{{description}}</p>
                <div class="timeline-location" style="{{locationStyle}}">
                    <i class="fas fa-map-marker-alt me-1"></i>{{location}}
                </div>
            </div>
        </div>
    </script>

    <!-- Order Item Template -->
    <script type="text/template" id="orderItemTemplate">
        <div class="tracking-order-item">
            <div class="item-image">
                <img src="{{image}}" alt="{{name}}">
            </div>
            <div class="item-details">
                <h6 class="item-name">{{name}}</h6>
                <div class="item-variant">{{variant}}</div>
                <div class="item-price">
                    <span class="price">{{price}}</span>
                    <span class="quantity">x{{quantity}}</span>
                </div>
            </div>
            <div class="item-total">{{totalPrice}}</div>
        </div>
    </script>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/order-tracking.js"></script>

    <!-- Initialize Order Tracking -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.orderTrackingManager = new OrderTrackingManager();
            orderTrackingManager.initialize();

            // Auto-search if order code is in URL
            const urlParams = new URLSearchParams(window.location.search);
            const orderCode = urlParams.get('orderCode');
            if (orderCode) {
                document.getElementById('orderCode').value = orderCode;
                orderTrackingManager.searchOrder();
            }
        });
    </script>
</body>
</html>
