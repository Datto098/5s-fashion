<?php
// Checkout Page for Phase 3.3
$title = 'Thanh toán - 5S Fashion';
$meta_description = 'Hoàn tất đơn hàng tại 5S Fashion - Thanh toán an toàn và bảo mật';

// Custom CSS for checkout page
$custom_css = [
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'css/checkout.css',
    'css/vnpay.css'
];

// Custom JavaScript for checkout page
$custom_js = [
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'js/checkout.js'
];

// Lấy thông tin mã giảm giá từ session nếu có
$applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;

// Start output buffering for content
ob_start();
?>

<script>
// Truyền dữ liệu mã giảm giá vào JavaScript nếu có
window.appliedCoupon = <?= $applied_coupon ? json_encode($applied_coupon) : 'null' ?>;
</script>


<style>
    .payment-icons{
        display: flex;
    }
    /* Keep variant and SKU tidy: show a separator before SKU only when variant exists */
   
    /* If variant is empty, remove the separator (uses :has - supported in modern browsers) */
    .item-meta:has(.variant-text:empty) .sku-text::before {
        content: "";
        margin: 0;
    }
</style>

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
                    <?php if (!empty($applied_coupon)) : ?>
                    <div id="applied-coupon-info" style="margin-bottom:10px;">
                        <div class="alert alert-success p-2 mb-2" style="font-size: 15px;">
                            <span>Mã giảm giá: <b><?= htmlspecialchars($applied_coupon['code']) ?></b></span>
                            <span class="ms-2">- <?= number_format($applied_coupon['discount_amount']) ?>đ</span>
                        </div>
                    </div>
                    <?php endif; ?>
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
                <div class="item-meta text-muted small">
                    <span class="sku-text">SKU: {sku}</span>
                    <br>
                    <span class="variant-text">{variant_info}</span>
                </div>
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


<!-- Address Form Modal (chuẩn style như trang account/addresses) -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalTitle">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addressForm">
                <input type="hidden" id="address_id" name="address_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ, ví dụ: 123 Lê Lợi, Quận 1, TP.HCM" autocomplete="off" required>
                                    <button class="btn btn-outline-secondary" type="button" id="searchAddressBtn" title="Tìm trên bản đồ">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div id="map" style="height: 300px; border-radius: 8px;"></div>
                                <input type="hidden" id="lat" name="lat">
                                <input type="hidden" id="lng" name="lng">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Ghi chú địa chỉ (nếu có)</label>
                        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Ghi chú thêm về địa chỉ, ví dụ: Gần trường học, tầng 2..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                        <label class="form-check-label" for="is_default">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="saveAddressBtn">Lưu địa chỉ</button>
                </div>
            </form>
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

<!-- Payment Processing Overlay -->
<div class="payment-processing" id="paymentProcessing">
    <div class="processing-content">
        <div class="processing-spinner"></div>
        <h5>Đang xử lý thanh toán...</h5>
        <p class="text-muted">Vui lòng đợi, đừng tắt trình duyệt</p>
    </div>
</div>

<?php
$applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
if ($applied_coupon) {
    echo '<script>window.appliedCoupon = ' . json_encode($applied_coupon) . ';</script>';
}

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

<script>
// Khởi tạo lại map khi mở modal địa chỉ checkout
let checkoutMap, checkoutMarker;
const checkoutModal = document.getElementById('addressModal');
checkoutModal.addEventListener('shown.bs.modal', function () {
    // Xóa map cũ nếu có
    if (checkoutMap) {
        checkoutMap.remove();
        checkoutMap = null;
    }
    setTimeout(function() {
        checkoutMap = L.map('map').setView([21.0285, 105.8542], 13); 
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(checkoutMap);
        checkoutMap.on('click', function(e) {
            setCheckoutMarker(e.latlng.lat, e.latlng.lng);
        });
        // Nếu đã có lat/lng thì set lại marker
        const lat = document.getElementById('lat').value;
        const lng = document.getElementById('lng').value;
        if (lat && lng) setCheckoutMarker(lat, lng);
    }, 200);
});

function setCheckoutMarker(lat, lng) {
    if (checkoutMarker) checkoutMarker.remove();
    checkoutMarker = L.marker([lat, lng]).addTo(checkoutMap);
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    checkoutMap.setView([lat, lng], 17);
    // Lấy địa chỉ từ lat/lng và điền vào input address
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('address').value = data.display_name;
            }
        });
}

// Tìm kiếm địa chỉ với Nominatim
function searchCheckoutAddressOnMap() {
    let query = document.getElementById('address').value;
    if (!query) return;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                let lat = parseFloat(data[0].lat);
                let lon = parseFloat(data[0].lon);
                setCheckoutMarker(lat, lon);
            } else {
                alert('Không tìm thấy địa chỉ phù hợp!');
            }
        });
}
document.getElementById('address').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchCheckoutAddressOnMap();
    }
});
document.getElementById('searchAddressBtn').addEventListener('click', function() {
    searchCheckoutAddressOnMap();
});
</script>


