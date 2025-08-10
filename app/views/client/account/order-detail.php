<?php
// Start output buffering for content
ob_start();
?>

<div class="account-container py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="account-sidebar">
                    <div class="user-info text-center mb-4">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle fa-4x text-primary"></i>
                        </div>
                        <h5 class="mt-2"><?= htmlspecialchars(getUser()['name'] ?? getUser()['full_name'] ?? 'User') ?></h5>
                        <p class="text-muted"><?= htmlspecialchars(getUser()['email'] ?? '') ?></p>
                    </div>

                    <nav class="account-nav">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account') ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account/profile') ?>">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="<?= url('orders') ?>">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('addresses') ?>">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('wishlist') ?>">
                                    <i class="fas fa-heart me-2"></i>Sản phẩm yêu thích
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account/password') ?>">
                                    <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-primary" href="<?= url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                <div class="account-main">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-1">Chi Tiết Đơn Hàng</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= url('account') ?>">Tài khoản</a></li>
                                    <li class="breadcrumb-item"><a href="<?= url('orders') ?>">Đơn hàng</a></li>
                                    <li class="breadcrumb-item active">#<?= htmlspecialchars($order['order_code']) ?></li>
                                </ol>
                            </nav>
                        </div>
                        <a href="<?= url('orders') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>

                    <!-- Order Info Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <i class="fas fa-receipt text-danger me-2"></i>
                                        Đơn hàng #<?= htmlspecialchars($order['order_code']) ?>
                                    </h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($order['status']) {
                                        case 'pending':
                                            $statusClass = 'bg-warning text-dark';
                                            $statusText = 'Chờ xử lý';
                                            break;
                                        case 'processing':
                                            $statusClass = 'bg-info text-white';
                                            $statusText = 'Đang xử lý';
                                            break;
                                        case 'shipped':
                                            $statusClass = 'bg-primary text-white';
                                            $statusText = 'Đã giao vận';
                                            break;
                                        case 'delivered':
                                            $statusClass = 'bg-success text-white';
                                            $statusText = 'Đã giao hàng';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'bg-danger text-white';
                                            $statusText = 'Đã hủy';
                                            break;
                                        case 'refunded':
                                            $statusClass = 'bg-secondary text-white';
                                            $statusText = 'Đã hoàn tiền';
                                            break;
                                        default:
                                            $statusClass = 'bg-light text-dark';
                                            $statusText = 'Chưa xác định';
                                    }

                                    $paymentStatusClass = '';
                                    $paymentStatusText = '';
                                    switch ($order['payment_status']) {
                                        case 'pending':
                                            $paymentStatusClass = 'bg-warning text-dark';
                                            $paymentStatusText = 'Chờ thanh toán';
                                            break;
                                        case 'paid':
                                            $paymentStatusClass = 'bg-success text-white';
                                            $paymentStatusText = 'Đã thanh toán';
                                            break;
                                        case 'failed':
                                            $paymentStatusClass = 'bg-danger text-white';
                                            $paymentStatusText = 'Thất bại';
                                            break;
                                        case 'refunded':
                                            $paymentStatusClass = 'bg-info text-white';
                                            $paymentStatusText = 'Đã hoàn tiền';
                                            break;
                                        default:
                                            $paymentStatusClass = 'bg-light text-dark';
                                            $paymentStatusText = 'Chưa xác định';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?> me-2"><?= $statusText ?></span>
                                    <span class="badge <?= $paymentStatusClass ?>"><?= $paymentStatusText ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-calendar text-muted me-2"></i>Thông tin đơn hàng</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Ngày đặt:</strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phương thức thanh toán:</strong></td>
                                            <td>
                                                <?php
                                                switch ($order['payment_method']) {
                                                    case 'cod':
                                                        echo '<i class="fas fa-money-bill text-success me-1"></i>Thanh toán khi nhận hàng';
                                                        break;
                                                    case 'vnpay':
                                                        echo '<i class="fas fa-credit-card text-primary me-1"></i>VNPay';
                                                        break;
                                                    case 'bank_transfer':
                                                        echo '<i class="fas fa-university text-info me-1"></i>Chuyển khoản';
                                                        break;
                                                    default:
                                                        echo $order['payment_method'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($order['notes'])): ?>
                                        <tr>
                                            <td><strong>Ghi chú:</strong></td>
                                            <td><?= htmlspecialchars($order['notes']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-shipping-fast text-muted me-2"></i>Thông tin giao hàng</h6>
                                    <?php
                                    // Handle both string and array cases
                                    if (is_string($order['shipping_address'])) {
                                        $shippingAddress = json_decode($order['shipping_address'], true);
                                    } else {
                                        $shippingAddress = $order['shipping_address'];
                                    }
                                    ?>
                                    <address class="mb-0">
                                        <strong><?= htmlspecialchars($order['customer_name']) ?></strong><br>
                                        <i class="fas fa-phone text-muted me-1"></i><?= htmlspecialchars($order['customer_phone']) ?><br>
                                        <?php if (!empty($order['customer_email'])): ?>
                                        <i class="fas fa-envelope text-muted me-1"></i><?= htmlspecialchars($order['customer_email']) ?><br>
                                        <?php endif; ?>
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <?= htmlspecialchars($shippingAddress['address'] ?? 'N/A') ?>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart text-danger me-2"></i>
                                Sản phẩm đã đặt
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($order['items'])): ?>
                                            <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($item['product_image'])): ?>
                                                        <img src="<?= htmlspecialchars($item['product_image']) ?>"
                                                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                             class="img-thumbnail me-3"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php else: ?>
                                                        <div class="bg-light me-3 d-flex align-items-center justify-content-center"
                                                             style="width: 60px; height: 60px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                            <?php if (!empty($item['variant_info'])): ?>
                                                            <small class="text-muted">
                                                                <?php
                                                                // Handle both string and array cases
                                                                if (is_string($item['variant_info'])) {
                                                                    $variantInfo = json_decode($item['variant_info'], true);
                                                                } else {
                                                                    $variantInfo = $item['variant_info'];
                                                                }

                                                                if (is_array($variantInfo)) {
                                                                    foreach ($variantInfo as $key => $value) {
                                                                        if ($key !== 'info') {
                                                                            echo htmlspecialchars($key) . ': ' . htmlspecialchars($value) . ' ';
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo htmlspecialchars($item['variant_info']);
                                                                }
                                                                ?>
                                                            </small>
                                                            <?php endif; ?>
                                                            <?php if (!empty($item['product_sku'])): ?>
                                                            <small class="text-muted d-block">SKU: <?= htmlspecialchars($item['product_sku']) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-light text-dark"><?= number_format($item['quantity']) ?></span>
                                                </td>
                                                <td class="text-end align-middle">
                                                    <strong><?= number_format($item['price'], 0, ',', '.') ?>₫</strong>
                                                </td>
                                                <td class="text-end align-middle">
                                                    <strong class="text-danger"><?= number_format($item['total'], 0, ',', '.') ?>₫</strong>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <i class="fas fa-inbox text-muted fa-2x mb-2"></i>
                                                    <p class="text-muted mb-0">Không có sản phẩm nào trong đơn hàng</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-calculator text-danger me-2"></i>
                                Tổng kết đơn hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php if (!empty($order['admin_notes'])): ?>
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-sticky-note me-2"></i>Ghi chú từ Shop</h6>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($order['admin_notes'])) ?></p>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($order['shipped_at']): ?>
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-truck me-2"></i>Thông tin vận chuyển</h6>
                                        <p class="mb-0">Đã giao vận: <?= date('d/m/Y H:i', strtotime($order['shipped_at'])) ?></p>
                                        <?php if ($order['delivered_at']): ?>
                                        <p class="mb-0">Đã giao hàng: <?= date('d/m/Y H:i', strtotime($order['delivered_at'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td><strong>Tạm tính:</strong></td>
                                                <td class="text-end"><?= number_format($order['subtotal'], 0, ',', '.') ?>₫</td>
                                            </tr>
                                            <?php if ($order['tax_amount'] > 0): ?>
                                            <tr>
                                                <td><strong>Thuế:</strong></td>
                                                <td class="text-end"><?= number_format($order['tax_amount'], 0, ',', '.') ?>₫</td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td><strong>Phí vận chuyển:</strong></td>
                                                <td class="text-end"><?= number_format($order['shipping_amount'], 0, ',', '.') ?>₫</td>
                                            </tr>
                                            <?php if ($order['discount_amount'] > 0): ?>
                                            <tr>
                                                <td><strong>Giảm giá:</strong></td>
                                                <td class="text-end text-success">-<?= number_format($order['discount_amount'], 0, ',', '.') ?>₫</td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr class="border-top">
                                                <td><strong class="fs-5">Tổng cộng:</strong></td>
                                                <td class="text-end"><strong class="fs-5 text-danger"><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</strong></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-3 d-grid gap-2">
                                        <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?= $order['id'] ?>)">
                                            <i class="fas fa-times me-2"></i>Hủy đơn hàng
                                        </button>
                                        <?php endif; ?>

                                        <?php if (in_array($order['status'], ['delivered', 'cancelled'])): ?>
                                        <a href="<?= url('orders') ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-redo me-2"></i>Đặt lại
                                        </a>
                                        <?php endif; ?>

                                        <a href="#" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>In đơn hàng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Account Sidebar Styling with Shadow */
.account-sidebar {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.account-sidebar:hover {
    box-shadow: 0 6px 30px rgba(0, 0, 0, 0.15);
}

/* Account Sidebar Primary Styling */
.account-sidebar .account-nav .nav-link {
    color: #6c757d;
    border: none;
    text-align: left;
    margin-bottom: 5px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.account-sidebar .account-nav .nav-link:hover,
.account-sidebar .account-nav .nav-link.active {
    background: var(--primary-color);
    color: white;
}

.account-sidebar .account-nav .nav-link.text-primary:hover {
    background: var(--primary-color);
    color: white;
}

/* Card headers with primary color */
.card-header h5 i {
    color: var(--primary-color) !important;
}

/* Disable all transform hover effects for this page */
.card, .card:hover,
.shadow-hover, .shadow-hover:hover,
.feature-item, .feature-item:hover,
.category-card, .category-card:hover {
    transform: none !important;
    transition: box-shadow 0.3s ease !important;
}
</style>

<script>
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
        fetch('<?= url('api/orders/cancel') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đơn hàng đã được hủy thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy đơn hàng');
        });
    }
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.querySelector('.account-sidebar').style.display = 'none';
    document.querySelector('.account-main').style.width = '100%';
});

window.addEventListener('afterprint', function() {
    document.querySelector('.account-sidebar').style.display = 'block';
    document.querySelector('.account-main').style.width = '';
});
</script>

<?php
// Get buffered content
$content = ob_get_clean();

// Include layout
include VIEW_PATH . '/client/layouts/app.php';
?>
