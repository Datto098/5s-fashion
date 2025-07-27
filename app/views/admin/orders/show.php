<?php
// Helper function to convert status to Vietnamese
function getStatusLabel($status) {
    $statusLabels = [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đã gửi hàng',
        'delivered' => 'Đã giao hàng',
        'cancelled' => 'Đã hủy'
    ];
    return $statusLabels[$status] ?? ucfirst($status);
}
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Chi tiết đơn hàng #<?= $order['id'] ?></h1>
                    <p class="text-muted mb-0">Xem thông tin chi tiết đơn hàng</p>
                </div>
                <div>
                    <a href="/5s-fashion/admin/orders" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="mb-1">
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'Chờ xử lý',
                                        'confirmed' => 'Đã xác nhận',
                                        'processing' => 'Đang xử lý',
                                        'shipped' => 'Đã gửi hàng',
                                        'delivered' => 'Đã giao hàng',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'processing' => 'primary',
                                        'shipped' => 'success',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusColors[$order['status']] ?? 'secondary' ?> fs-6">
                                        <?= $statusLabels[$order['status']] ?? ucfirst($order['status']) ?>
                                    </span>
                                </h4>
                                <small class="text-muted">Trạng thái đơn hàng</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="mb-1 text-primary"><?= number_format($order['total_amount']) ?> đ</h4>
                                <small class="text-muted">Tổng giá trị</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="mb-1">
                                    <?php
                                    $paymentLabels = [
                                        'pending' => 'Chờ thanh toán',
                                        'paid' => 'Đã thanh toán',
                                        'confirmed' => 'Đã xác nhận',
                                        'failed' => 'Thất bại',
                                        'refunded' => 'Đã hoàn tiền'
                                    ];
                                    $paymentColors = [
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'confirmed' => 'primary',
                                        'failed' => 'danger',
                                        'refunded' => 'info'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $paymentColors[$order['payment_status']] ?? 'secondary' ?> fs-6">
                                        <?= $paymentLabels[$order['payment_status']] ?? ucfirst($order['payment_status']) ?>
                                    </span>
                                </h4>
                                <small class="text-muted">Thanh toán</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="mb-1"><?= date('d/m/Y', strtotime($order['created_at'])) ?></h4>
                                <small class="text-muted">Ngày đặt hàng</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart"></i> Sản phẩm đã đặt
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($orderItems)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($item['product_image'])): ?>
                                                        <?php
                                                        // Handle image path for file server
                                                        $imagePath = $item['product_image'];
                                                        if (strpos($imagePath, '/uploads/') === 0) {
                                                            $cleanPath = substr($imagePath, 9);
                                                        } elseif (strpos($imagePath, 'uploads/') === 0) {
                                                            $cleanPath = substr($imagePath, 8);
                                                        } else {
                                                            $cleanPath = ltrim($imagePath, '/');
                                                        }
                                                        $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                                        ?>
                                                        <img src="<?= htmlspecialchars($imageUrl) ?>"
                                                             class="me-2 rounded" width="50" height="50" alt="Product">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                        <?php if (!empty($item['product_slug'])): ?>
                                                            <small class="text-muted">SKU: <?= htmlspecialchars($item['product_slug']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark"><?= $item['quantity'] ?></span>
                                            </td>
                                            <td class="text-end"><?= number_format($item['price']) ?> đ</td>
                                            <td class="text-end fw-bold"><?= number_format($item['quantity'] * $item['price']) ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Tạm tính:</td>
                                        <td class="text-end fw-bold"><?= number_format($order['subtotal'] ?? array_sum(array_map(function($item) { return $item['quantity'] * $item['price']; }, $orderItems))) ?> đ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end">Phí vận chuyển:</td>
                                        <td class="text-end"><?= number_format($order['shipping_fee'] ?? 0) ?> đ</td>
                                    </tr>
                                    <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end">Giảm giá:</td>
                                        <td class="text-end text-success">-<?= number_format($order['discount_amount']) ?> đ</td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end fw-bold fs-5">Tổng cộng:</td>
                                        <td class="text-end fw-bold fs-5 text-primary"><?= number_format($order['total_amount']) ?> đ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Không có sản phẩm nào trong đơn hàng này</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Thông tin khách hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thông tin liên hệ</h6>
                            <p class="mb-1"><strong>Tên:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                            <p class="mb-3"><strong>Điện thoại:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>

                            <?php if ($customer): ?>
                                <h6>Thông tin tài khoản</h6>
                                <p class="mb-1"><strong>ID:</strong> #<?= $customer['id'] ?></p>
                                <p class="mb-1"><strong>Ngày tham gia:</strong> <?= date('d/m/Y', strtotime($customer['created_at'])) ?></p>
                                <p class="mb-1">
                                    <strong>Trạng thái:</strong>
                                    <span class="badge bg-<?= $customer['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= $customer['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động' ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6>Địa chỉ giao hàng</h6>
                            <address class="mb-0">
                                <?php if (is_array($order['shipping_address'])): ?>
                                    <?= htmlspecialchars($order['shipping_address']['name'] ?? '') ?><br>
                                    <?= htmlspecialchars($order['shipping_address']['phone'] ?? '') ?><br>
                                    <?= htmlspecialchars($order['shipping_address']['address'] ?? '') ?><br>
                                    <?= htmlspecialchars($order['shipping_address']['ward'] ?? '') ?>,
                                    <?= htmlspecialchars($order['shipping_address']['district'] ?? '') ?>,
                                    <?= htmlspecialchars($order['shipping_address']['city'] ?? '') ?>
                                <?php else: ?>
                                    <?= nl2br(htmlspecialchars($order['shipping_address'] ?? '')) ?>
                                <?php endif; ?>
                            </address>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            <?php if (!empty($order['notes'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note"></i> Ghi chú đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Chi tiết đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>ID đơn hàng:</strong> #<?= $order['id'] ?>
                    </div>
                    <div class="mb-2">
                        <strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Cập nhật lần cuối:</strong> <?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?>
                    </div>
                    <div class="mb-2">
                        <?php
                        $paymentMethodLabels = [
                            'cod' => 'Thanh toán khi nhận hàng',
                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                            'credit_card' => 'Thẻ tín dụng',
                            'e_wallet' => 'Ví điện tử'
                        ];
                        ?>
                        <strong>Phương thức thanh toán:</strong> <?= $paymentMethodLabels[$order['payment_method']] ?? ucfirst($order['payment_method']) ?>
                    </div>
                    <?php if (!empty($order['tracking_number'])): ?>
                    <div class="mb-2">
                        <strong>Mã vận đơn:</strong>
                        <code><?= htmlspecialchars($order['tracking_number']) ?></code>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order History -->
            <?php if (!empty($orderLogs)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Lịch sử đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($orderLogs as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">
                                    <?php if ($log['status_from']): ?>
                                        Chuyển từ "<strong><?= getStatusLabel($log['status_from']) ?></strong>" sang "<strong><?= getStatusLabel($log['status_to']) ?></strong>"
                                    <?php else: ?>
                                        Trạng thái: <strong><?= getStatusLabel($log['status_to']) ?></strong>
                                    <?php endif; ?>
                                </h6>
                                <p class="timeline-description">
                                    <?= htmlspecialchars($log['notes'] ?? 'Không có ghi chú') ?>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                    <?php if (isset($log['created_by_name']) && $log['created_by_name']): ?>
                                        bởi <?= htmlspecialchars($log['created_by_name']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Check if Bootstrap is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? bootstrap : 'Bootstrap not loaded');

    // Initialize dropdowns manually if needed
    var dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log('Found dropdowns:', dropdowns.length);

    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function(e) {
            console.log('Dropdown clicked:', e.target);
        });
    });
});

function updateOrderStatus(status) {
    console.log('updateOrderStatus called with:', status);
    if (confirm(`Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng thành "${status}"?`)) {
        console.log('Sending order status update request:', status);

        fetch(`/5s-fashion/admin/orders/update-status/<?= $order['id'] ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                order_id: '<?= $order['id'] ?>',
                status: status,
                admin_notes: 'Cập nhật từ admin interface'
            })
        })
        .then(response => {
            console.log('Order status response status:', response.status);
            return response.text(); // Get raw text first
        })
        .then(rawText => {
            console.log('Order status raw response:', rawText);
            try {
                const data = JSON.parse(rawText);
                console.log('Order status parsed JSON:', data);

                if (data.success) {
                    showNotification('Cập nhật trạng thái đơn hàng thành công!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.error || data.message || 'Có lỗi xảy ra!', 'error');
                }
            } catch (e) {
                console.error('Order status JSON parse error:', e);
                console.log('Response is not valid JSON, likely HTML redirect');
                showNotification('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!', 'error');
            }
        })
        .catch(error => {
            console.error('Order status fetch error:', error);
            showNotification('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!', 'error');
        });
    }
}

function updatePaymentStatus(status) {
    console.log('updatePaymentStatus called with:', status);
    if (confirm(`Bạn có chắc chắn muốn cập nhật trạng thái thanh toán thành "${status}"?`)) {
        console.log('Sending payment status update request:', status);

        fetch(`/5s-fashion/admin/orders/update-payment-status/<?= $order['id'] ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ payment_status: status })
        })
        .then(response => {
            console.log('Payment status response status:', response.status);
            return response.text(); // Get raw text first
        })
        .then(rawText => {
            console.log('Payment status raw response:', rawText);
            try {
                const data = JSON.parse(rawText);
                console.log('Payment status parsed JSON:', data);

                if (data.success) {
                    showNotification('Cập nhật trạng thái thanh toán thành công!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.error || data.message || 'Có lỗi xảy ra!', 'error');
                }
            } catch (e) {
                console.error('Payment status JSON parse error:', e);
                console.log('Response is not valid JSON, likely HTML redirect');
                showNotification('Có lỗi xảy ra khi cập nhật trạng thái thanh toán!', 'error');
            }
        })
        .catch(error => {
            console.error('Payment status fetch error:', error);
            showNotification('Có lỗi xảy ra khi cập nhật trạng thái thanh toán!', 'error');
        });
    }
}

// Notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to body
    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-description {
    margin-bottom: 5px;
    color: #6c757d;
}

.badge {
    font-size: 0.75em;
}

address {
    font-style: normal;
    line-height: 1.6;
}
</style>

</div>
<!-- End container-fluid -->
