<?php
// Order Tracking Page - Using standard layout
// Start output buffering for content
ob_start();
?>

<!-- Page Header -->
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Theo dõi đơn hàng</li>
        </ol>
    </nav>
    <h1 class="h2 mb-0">Theo dõi đơn hàng</h1>
    <p class="text-muted">Nhập mã đơn hàng để xem tình trạng đơn hàng của bạn</p>
</div>

<!-- Search Form -->
<section class="tracking-search py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="orderTrackingForm" class="d-flex gap-3">
                            <div class="flex-grow-1">
                                <input type="text" class="form-control form-control-lg"
                                       id="orderCode" placeholder="Nhập mã đơn hàng (VD: ORD-2024-001)"
                                       required pattern="^ORD-\d{4}-\d{3}$">
                                <div class="invalid-feedback">
                                    Vui lòng nhập mã đơn hàng hợp lệ (VD: ORD-2024-001)
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Search -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
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
        </div>
    </div>
</section>

<!-- Tracking Results -->
<section class="tracking-results py-4" id="trackingResults" style="display: none;">
    <div class="container">
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
                        </div>
                        <div class="col-md-6">
                            <div class="shipping-detail">
                                <strong>Mã vận đơn:</strong>
                                <span id="trackingNumber"></span>
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
</section>

<!-- No Results -->
<section class="no-results py-4" id="noResults" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
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

<?php
// Get content from buffer
$content = ob_get_clean();

// Set page variables for layout
$title = 'Theo dõi đơn hàng - 5S Fashion';
$meta_description = 'Theo dõi tình trạng đơn hàng tại 5S Fashion';

// Custom CSS for this page
$custom_css = [
    'css/order-tracking.css'
];

// Custom JavaScript for this page
$custom_js = [
    'js/order-tracking.js'
];

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
