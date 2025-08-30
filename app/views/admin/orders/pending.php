<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Đơn hàng chờ xử lý
                    </h1>
                    <p class="text-muted mb-0">Quản lý và xử lý các đơn hàng đang chờ</p>
                </div>
                <div>
                    <a href="/zone-fashion/admin/orders" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Tất cả đơn hàng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count($orders) ?></h4>
                            <p class="mb-0">Chờ xử lý</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format(array_sum(array_column($orders, 'total_amount')), 0, ',', '.') ?>đ</h4>
                            <p class="mb-0">Tổng giá trị</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <?php
                            $urgentOrders = array_filter($orders, function($order) {
                                return (time() - strtotime($order['created_at'])) > 86400; // > 24 hours
                            });
                            ?>
                            <h4 class="mb-0"><?= count($urgentOrders) ?></h4>
                            <p class="mb-0">Cần xử lý gấp</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <?php
                            $todayOrders = array_filter($orders, function($order) {
                                return date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d');
                            });
                            ?>
                            <h4 class="mb-0"><?= count($todayOrders) ?></h4>
                            <p class="mb-0">Hôm nay</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Danh sách đơn hàng chờ xử lý
            </h5>
        </div>
        <div class="card-body">
            <?php if (count($orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Thời gian đặt</th>
                                <th>Tổng tiền</th>
                                <th>Thanh toán</th>
                                <th>Ưu tiên</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $hoursSinceCreated = (time() - strtotime($order['created_at'])) / 3600;
                                $priorityClass = $hoursSinceCreated > 24 ? 'table-danger' : ($hoursSinceCreated > 12 ? 'table-warning' : '');
                                ?>
                                <tr class="<?= $priorityClass ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($order['order_code']) ?></strong>
                                        <?php if ($hoursSinceCreated > 24): ?>
                                            <span class="badge bg-danger ms-1">Cấp bách</span>
                                        <?php elseif ($hoursSinceCreated > 12): ?>
                                            <span class="badge bg-warning ms-1">Khẩn cấp</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($order['customer_name']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i>
                                                <?= htmlspecialchars($order['customer_email']) ?>
                                            </small>
                                            <?php if (!empty($order['customer_phone'])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i>
                                                    <?= htmlspecialchars($order['customer_phone']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php
                                                if ($hoursSinceCreated < 1) {
                                                    echo floor($hoursSinceCreated * 60) . ' phút trước';
                                                } elseif ($hoursSinceCreated < 24) {
                                                    echo floor($hoursSinceCreated) . ' giờ trước';
                                                } else {
                                                    echo floor($hoursSinceCreated / 24) . ' ngày trước';
                                                }
                                                ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-primary">
                                            <?= number_format($order['total_amount'], 0, ',', '.') ?>đ
                                        </strong>
                                    </td>
                                    <td>
                                        <?php
                                        $paymentStatusMap = [
                                            'pending' => ['class' => 'warning', 'text' => 'Chờ'],
                                            'paid' => ['class' => 'success', 'text' => 'Đã thanh toán'],
                                            'failed' => ['class' => 'danger', 'text' => 'Thất bại'],
                                            'refunded' => ['class' => 'info', 'text' => 'Đã hoàn tiền']
                                        ];
                                        $paymentStatus = $paymentStatusMap[$order['payment_status']] ?? ['class' => 'secondary', 'text' => 'Không xác định'];
                                        ?>
                                        <span class="badge bg-<?= $paymentStatus['class'] ?>">
                                            <?= $paymentStatus['text'] ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            <?php
                                            $paymentMethodMap = [
                                                'cod' => 'Tiền mặt',
                                                'bank_transfer' => 'Chuyển khoản',
                                                'vnpay' => 'VNPay',
                                                'momo' => 'MoMo',
                                                'zalopay' => 'ZaloPay',
                                                'credit_card' => 'Thẻ tín dụng'
                                            ];
                                            echo $paymentMethodMap[$order['payment_method']] ?? $order['payment_method'];
                                            ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($hoursSinceCreated > 24): ?>
                                            <i class="fas fa-exclamation-triangle text-danger" title="Cần xử lý gấp - Quá 24 giờ"></i>
                                            <span class="text-danger">Cao</span>
                                        <?php elseif ($hoursSinceCreated > 12): ?>
                                            <i class="fas fa-exclamation-circle text-warning" title="Khẩn cấp - Quá 12 giờ"></i>
                                            <span class="text-warning">Trung bình</span>
                                        <?php else: ?>
                                            <i class="fas fa-check-circle text-success" title="Bình thường"></i>
                                            <span class="text-success">Thấp</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/zone-fashion/admin/orders/show/<?= $order['id'] ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-success"
                                                    onclick="processOrder(<?= $order['id'] ?>)"
                                                    title="Xử lý đơn hàng">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h4>Tuyệt vời!</h4>
                    <p class="text-muted">Hiện tại không có đơn hàng nào chờ xử lý.</p>
                    <a href="/zone-fashion/admin/orders" class="btn btn-primary">
                        <i class="fas fa-list"></i> Xem tất cả đơn hàng
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function processOrder(orderId) {
    if (confirm('Bạn có muốn chuyển đơn hàng này sang trạng thái "Đang xử lý"?')) {
        updateOrderStatus(orderId, 'processing');
    }
}

function updateOrderStatus(orderId, status) {
    fetch(`/zone-fashion/admin/orders/update-status/${orderId}`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh page to update the list
        } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Không thể cập nhật trạng thái'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái');
    });
}
</script>

<style>
.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.btn-group .btn {
    margin-right: 0;
}

.badge {
    font-size: 0.75em;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
