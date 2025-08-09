<?php
// Checkout Page for Phase 3.3
$title = 'Thanh toán - 5S Fashion';
$meta_description = 'Hoàn tất đơn hàng tại 5S Fashion - Thanh toán an toàn và bảo mật';

// Custom CSS for checkout page
$custom_css = [
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'css/checkout.css'
];

// Custom JavaScript for checkout page
$custom_js = [
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'js/checkout.js'
];

// Start output buffering for content
ob_start();
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="breadcrumb-section">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">
                <i class="fas fa-home"></i> Trang chủ
            </a></li>
            <li class="breadcrumb-item"><a href="/?route=cart" class="text-decoration-none">Giỏ hàng</a></li>
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
                    <!-- Form sẽ được load bởi JavaScript -->
                </form>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="order-summary sticky-top" style="top: 120px;">
                    <!-- Summary sẽ được load bởi JavaScript -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Order Item Template -->
<template id="orderItemTemplate">
    <div class="order-item">
        <div class="item-image">
            <img src="{image}" alt="{name}">
            <div class="item-quantity">{quantity}</div>
        </div>
        <div class="item-details">
            <div class="item-name">{name}</div>
            <div class="text-muted small">{variant_info}</div>
            <div class="item-price">{price}</div>
        </div>
    </div>
</template>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Lỗi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage">Có lỗi xảy ra. Vui lòng thử lại sau.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Address Form Modal -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ giao hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addressForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tỉnh/Thành phố *</label>
                            <select class="form-select" name="province" required>
                                <option value="">Chọn tỉnh/thành</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quận/Huyện *</label>
                            <select class="form-select" name="district" required>
                                <option value="">Chọn quận/huyện</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phường/Xã *</label>
                            <select class="form-select" name="ward" required>
                                <option value="">Chọn phường/xã</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ cụ thể *</label>
                        <input type="text" class="form-control" name="address_line"
                               placeholder="Số nhà, tên đường..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Loại địa chỉ</label>
                        <div class="d-flex gap-3">
                            <label class="form-check">
                                <input type="radio" class="form-check-input" name="address_type" value="home" checked>
                                <span class="form-check-label">Nhà riêng</span>
                            </label>
                            <label class="form-check">
                                <input type="radio" class="form-check-input" name="address_type" value="office">
                                <span class="form-check-label">Văn phòng</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_default" id="setDefault">
                        <label class="form-check-label" for="setDefault">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="addressManager.saveAddress()">
                    Lưu địa chỉ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn vị trí trên bản đồ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="leafletMap" style="height: 400px;"></div>
                <div class="mt-3">
                    <small class="text-muted">Nhấn vào bản đồ để chọn vị trí chính xác</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="addressManager.confirmLocation()">
                    Xác nhận vị trí
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Store content in variable
$content = ob_get_clean();

// Include the shared layout
include VIEW_PATH . '/client/layouts/app.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.checkoutManager = new CheckoutManager();
        window.addressManager = new CheckoutAddressManager();

        checkoutManager.loadOrder();
        checkoutManager.initializeForm();
    });
</script>
