<?php
// Order Success Page - Using standard layout
// Start output buffering for content
ob_start();
?>

<!-- Success Section -->
<section class="success-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Animation -->
                <div class="success-animation text-center mb-5">
                    <div class="success-checkmark animate">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="var(--success-color, #28a745)" stroke-width="3"/>
                            <path class="checkmark-check" fill="none" stroke="var(--success-color, #28a745)" stroke-width="3" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    </div>
                    <h1 class="success-title">Đặt hàng thành công!</h1>
                    <p class="success-subtitle">Cảm ơn bạn đã tin tưởng và mua sắm tại 5S Fashion</p>
                </div>

                <!-- Order Details -->
                <div class="order-details-card mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 text-white">
                                <i class="fas fa-file-invoice me-2"></i>
                                Thông tin đơn hàng
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="order-info-item">
                                        <span class="label">Mã đơn hàng:</span>
                                        <span class="value text-primary fw-bold" id="orderCode">
                                            <?php echo isset($data['order']['order_code']) ? $data['order']['order_code'] : 'ORD' . ($data['orderId'] ?? '000001'); ?>
                                        </span>
                                    </div>
                                    <div class="order-info-item">
                                        <span class="label">Ngày đặt hàng:</span>
                                        <span class="value" id="orderDate">
                                            <?php echo isset($data['order']['created_at']) ? date('d/m/Y H:i', strtotime($data['order']['created_at'])) : date('d/m/Y H:i'); ?>
                                        </span>
                                    </div>
                                    <div class="order-info-item">
                                        <span class="label">Phương thức thanh toán:</span>
                                        <span class="value" id="paymentMethod">
                                            <?php
                                            $paymentMethod = $data['order']['payment_method'] ?? 'cod';
                                            switch($paymentMethod) {
                                                case 'cod': echo 'Thanh toán khi nhận hàng'; break;
                                                case 'vnpay': echo 'VNPay'; break;
                                                case 'momo': echo 'MoMo'; break;
                                                case 'bank_transfer': echo 'Chuyển khoản ngân hàng'; break;
                                                default: echo 'Thanh toán khi nhận hàng';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="order-info-item">
                                        <span class="label">Tổng tiền:</span>
                                        <span class="value total-amount text-danger fw-bold" id="totalAmount">
                                            <?php echo isset($data['order']['total_amount']) ? number_format($data['order']['total_amount']) . 'đ' : '0đ'; ?>
                                        </span>
                                    </div>
                                    <div class="order-info-item">
                                        <span class="label">Trạng thái:</span>
                                        <span class="badge bg-warning text-dark">Đang xử lý</span>
                                    </div>
                                    <div class="order-info-item">
                                        <span class="label">Dự kiến giao hàng:</span>
                                        <span class="value" id="estimatedDelivery">
                                            <?php
                                            $deliveryStart = date('d/m/Y', strtotime('+3 days'));
                                            $deliveryEnd = date('d/m/Y', strtotime('+5 days'));
                                            echo $deliveryStart . ' - ' . $deliveryEnd;
                                            ?>
                                        </span>
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
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Sản phẩm đã đặt
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="order-items-list" id="orderItemsList">
                                <?php if (isset($data['order']['items']) && !empty($data['order']['items'])): ?>
                                    <?php foreach ($data['order']['items'] as $item): ?>
                                        <div class="order-item d-flex align-items-center mb-3 p-3 border rounded">
                                            <div class="item-image me-3">
                                                <?php if (!empty($item['featured_image'])): ?>
                                                    <img src="/5s-fashion/serve-file.php?file=<?php echo rawurlencode($item['featured_image']); ?>"
                                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                         class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="placeholder-image d-flex align-items-center justify-content-center rounded bg-light"
                                                         style="width: 80px; height: 80px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-details flex-grow-1">
                                                <h6 class="item-name mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                <?php if (!empty($item['variant_info'])): ?>
                                                    <?php $variant = is_array($item['variant_info']) ? $item['variant_info'] : json_decode($item['variant_info'], true); ?>
                                                    <?php if ($variant): ?>
                                                        <small class="text-muted">
                                                            <?php if (isset($variant['size'])): ?>Kích thước: <?php echo htmlspecialchars($variant['size']); ?><?php endif; ?>
                                                            <?php if (isset($variant['color'])): ?> - Màu: <?php echo htmlspecialchars($variant['color']); ?><?php endif; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <div class="item-quantity-price mt-1">
                                                    <span class="quantity">Số lượng: <?php echo (int)$item['quantity']; ?></span>
                                                    <span class="price text-primary fw-bold ms-3"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                                                </div>
                                            </div>
                                            <div class="item-total text-end">
                                                <strong class="text-primary"><?php echo number_format($item['total'], 0, ',', '.'); ?>đ</strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-bag text-muted fs-1 mb-3"></i>
                                        <p class="text-muted mb-0">Không có thông tin sản phẩm</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="shipping-info-card mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-truck me-2"></i>
                                Thông tin giao hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="shipping-detail">
                                        <strong>Người nhận:</strong>
                                        <span id="recipientName">
                                            <?php echo isset($data['order']['customer_name']) ? $data['order']['customer_name'] : 'Khách hàng'; ?>
                                        </span>
                                    </div>
                                    <div class="shipping-detail">
                                        <strong>Số điện thoại:</strong>
                                        <span id="recipientPhone">
                                            <?php echo isset($data['order']['customer_phone']) ? $data['order']['customer_phone'] : 'Chưa có thông tin'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="shipping-detail">
                                        <strong>Địa chỉ giao hàng:</strong>
                                        <span id="shippingAddress">
                                            <?php
                                            if (isset($data['order']['shipping_address'])) {
                                                $address = $data['order']['shipping_address'];

                                                // Kiểm tra nếu đã là array (đã decode từ getFullDetails)
                                                if (is_array($address)) {
                                                    // Hiển thị theo format: Tên - SĐT - Địa chỉ
                                                    $display_parts = [];
                                                    if (isset($address['name'])) $display_parts[] = $address['name'];
                                                    if (isset($address['phone'])) $display_parts[] = $address['phone'];
                                                    if (isset($address['address'])) $display_parts[] = $address['address'];
                                                    echo htmlspecialchars(implode(' - ', $display_parts));
                                                } elseif (is_string($address) && json_decode($address, true)) {
                                                    // Nếu là JSON string, decode nó
                                                    $address_data = json_decode($address, true);
                                                    $display_parts = [];
                                                    if (isset($address_data['name'])) $display_parts[] = $address_data['name'];
                                                    if (isset($address_data['phone'])) $display_parts[] = $address_data['phone'];
                                                    if (isset($address_data['address'])) $display_parts[] = $address_data['address'];
                                                    echo htmlspecialchars(implode(' - ', $display_parts));
                                                } elseif (is_string($address)) {
                                                    // Nếu là string thường
                                                    echo htmlspecialchars($address);
                                                } else {
                                                    echo 'Định dạng địa chỉ không hợp lệ';
                                                }
                                            } else {
                                                echo 'Chưa có thông tin địa chỉ';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps-card mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-white">
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
                                <div class="timeline-item" id="paymentStep" style="display: none;">
                                    <div class="timeline-marker">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Thanh toán</h6>
                                        <p class="text-muted">Hoàn tất thanh toán để xử lý đơn hàng</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Đang chuẩn bị hàng</h6>
                                        <p class="text-muted">Chúng tôi sẽ chuẩn bị và đóng gói đơn hàng</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Giao hàng</h6>
                                        <p class="text-muted">Đơn hàng sẽ được giao đến địa chỉ của bạn</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Info (if applicable) -->
                <div class="bank-transfer-card mb-4" id="bankTransferInfo" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-university me-2"></i>
                                Thông tin chuyển khoản
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Vui lòng chuyển khoản theo thông tin bên dưới để hoàn tất đơn hàng
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bank-info">
                                        <div class="info-item">
                                            <span class="label">Ngân hàng:</span>
                                            <span class="value">Vietcombank</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">Số tài khoản:</span>
                                            <span class="value">1234567890</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">Chủ tài khoản:</span>
                                            <span class="value">CONG TY 5S FASHION</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bank-info">
                                        <div class="info-item">
                                            <span class="label">Số tiền:</span>
                                            <span class="value total-amount text-danger fw-bold" id="bankAmount">
                                                <?php echo isset($data['order']['total_amount']) ? number_format($data['order']['total_amount']) . 'đ' : '0đ'; ?>
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">Nội dung:</span>
                                            <span class="value" id="bankContent">
                                                5SFASHION <?php echo isset($data['order']['order_code']) ? $data['order']['order_code'] : 'ORD' . ($data['orderId'] ?? '000001'); ?>
                                            </span>
                                        </div>
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
                            <a href="/5s-fashion/order/tracking" class="btn-primary-action w-100">
                                <i class="fas fa-search me-2"></i>
                                Theo dõi đơn hàng
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <button class="btn-success-action w-100" onclick="downloadInvoice()">
                                <i class="fas fa-download me-2"></i>
                                Tải hóa đơn
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/5s-fashion" class="btn-outline-primary-action w-100">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Tiếp tục mua sắm
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/5s-fashion" class="btn-outline-secondary-action w-100">
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


<?php
// Get content from buffer
$content = ob_get_clean();

// Set page variables for layout
$title = 'Đặt hàng thành công - 5S Fashion';
$meta_description = 'Cảm ơn bạn đã đặt hàng tại 5S Fashion';

// Custom CSS for this page
$custom_css = [
    'css/order-success.css'
];

// Custom JavaScript for this page
$custom_js = [
    'js/order-success.js',
    'js/checkmark-trigger.js'
];

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
