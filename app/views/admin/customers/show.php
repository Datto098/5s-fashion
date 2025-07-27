<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Chi tiết khách hàng</h1>
                    <p class="text-muted mb-0"><?= htmlspecialchars($customer['full_name']) ?></p>
                </div>
                <div>
                    <a href="/5s-fashion/admin/customers/edit/<?= $customer['id'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="/5s-fashion/admin/customers" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?= $stats['total_orders'] ?? 0 ?></h3>
                    <p class="card-text text-muted">Tổng đơn hàng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?= number_format($stats['total_spent'] ?? 0) ?> đ</h3>
                    <p class="card-text text-muted">Tổng chi tiêu</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info"><?= number_format($stats['avg_order_value'] ?? 0) ?> đ</h3>
                    <p class="card-text text-muted">Giá trị đơn hàng TB</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">
                        <?= !empty($stats['last_order_date']) ? date('d/m/Y', strtotime($stats['last_order_date'])) : 'Chưa có' ?>
                    </h3>
                    <p class="card-text text-muted">Đơn hàng cuối</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Recent Orders -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart"></i> Đơn hàng gần đây
                    </h5>
                    <a href="/5s-fashion/admin/orders?customer_id=<?= $customer['id'] ?>" class="btn btn-sm btn-outline-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th class="text-end">Giá trị</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Thanh toán</th>
                                        <th class="text-center">Ngày đặt</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="/5s-fashion/admin/orders/show/<?= $order['id'] ?>" class="text-decoration-none">
                                                    #<?= $order['id'] ?>
                                                </a>
                                            </td>
                                            <td class="text-end fw-bold"><?= number_format($order['total_amount']) ?> đ</td>
                                            <td class="text-center">
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
                                                <span class="badge bg-<?= $statusColors[$order['status']] ?? 'secondary' ?>">
                                                    <?= $statusLabels[$order['status']] ?? ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $paymentLabels = [
                                                    'pending' => 'Chờ thanh toán',
                                                    'paid' => 'Đã thanh toán',
                                                    'failed' => 'Thất bại',
                                                    'refunded' => 'Đã hoàn tiền'
                                                ];
                                                $paymentColors = [
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'failed' => 'danger',
                                                    'refunded' => 'info'
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $paymentColors[$order['payment_status']] ?? 'secondary' ?>">
                                                    <?= $paymentLabels[$order['payment_status']] ?? ucfirst($order['payment_status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="/5s-fashion/admin/orders/show/<?= $order['id'] ?>"
                                                       class="btn btn-outline-primary" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/5s-fashion/admin/orders/edit/<?= $order['id'] ?>"
                                                       class="btn btn-outline-warning" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Khách hàng chưa có đơn hàng nào</p>
                            <a href="/5s-fashion/admin/orders/create?customer_id=<?= $customer['id'] ?>"
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tạo đơn hàng mới
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Customer Activity Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Hoạt động gần đây
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Tham gia hệ thống</h6>
                                <p class="timeline-description">Khách hàng đã đăng ký tài khoản</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?>
                                </small>
                            </div>
                        </div>

                        <?php if (!empty($customer['email_verified_at'])): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Xác thực email</h6>
                                <p class="timeline-description">Địa chỉ email đã được xác thực</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($customer['email_verified_at'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($customer['last_login'])): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Đăng nhập lần cuối</h6>
                                <p class="timeline-description">Khách hàng đã đăng nhập vào hệ thống</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($customer['last_login'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Thông tin khách hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?php if (!empty($customer['avatar'])): ?>
                            <img src="/5s-fashion/public<?= htmlspecialchars($customer['avatar']) ?>"
                                 class="rounded-circle mb-2" width="80" height="80" alt="Avatar">
                        <?php else: ?>
                            <div class="bg-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center text-white"
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                <?= strtoupper(substr($customer['full_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <h5 class="mb-1"><?= htmlspecialchars($customer['full_name']) ?></h5>
                        <span class="badge bg-<?= $customer['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= $customer['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động' ?>
                        </span>
                    </div>

                    <div class="mb-2">
                        <strong>ID khách hàng:</strong> #<?= $customer['id'] ?>
                    </div>
                    <div class="mb-2">
                        <strong>Email:</strong>
                        <a href="mailto:<?= htmlspecialchars($customer['email']) ?>" class="text-decoration-none">
                            <?= htmlspecialchars($customer['email']) ?>
                        </a>
                        <?php if (!empty($customer['email_verified_at'])): ?>
                            <i class="fas fa-check-circle text-success ms-1" title="Email đã xác thực"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle text-warning ms-1" title="Email chưa xác thực"></i>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <strong>Điện thoại:</strong>
                        <?php if (!empty($customer['phone'])): ?>
                            <a href="tel:<?= htmlspecialchars($customer['phone']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($customer['phone']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Chưa cập nhật</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2">
                        <strong>Ngày tham gia:</strong> <?= date('d/m/Y', strtotime($customer['created_at'])) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Cập nhật lần cuối:</strong> <?= date('d/m/Y H:i', strtotime($customer['updated_at'])) ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/5s-fashion/admin/orders/create?customer_id=<?= $customer['id'] ?>"
                           class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tạo đơn hàng mới
                        </a>
                        <a href="mailto:<?= htmlspecialchars($customer['email']) ?>" class="btn btn-info">
                            <i class="fas fa-envelope"></i> Gửi email
                        </a>
                        <?php if (!empty($customer['phone'])): ?>
                        <a href="tel:<?= htmlspecialchars($customer['phone']) ?>" class="btn btn-success">
                            <i class="fas fa-phone"></i> Gọi điện
                        </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-warning" onclick="toggleCustomerStatus(<?= $customer['id'] ?>, '<?= $customer['status'] ?>')">
                            <i class="fas fa-<?= $customer['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                            <?= $customer['status'] === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteCustomer(<?= $customer['id'] ?>)">
                            <i class="fas fa-trash"></i> Xóa khách hàng
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Address -->
            <?php if (!empty($customer['address'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt"></i> Địa chỉ
                    </h5>
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        <?= nl2br(htmlspecialchars($customer['address'])) ?>
                    </address>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleCustomerStatus(customerId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa';

    if (confirm(`Bạn có chắc chắn muốn ${action} khách hàng này?`)) {
        fetch(`/5s-fashion/admin/customers/toggle-status/${customerId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái khách hàng');
        });
    }
}

function deleteCustomer(customerId) {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng này? Hành động này không thể hoàn tác.')) {
        if (confirm('Việc xóa khách hàng sẽ ảnh hưởng đến các đơn hàng liên quan. Bạn có chắc chắn muốn tiếp tục?')) {
            fetch(`/5s-fashion/admin/customers/delete/${customerId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/5s-fashion/admin/customers';
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa khách hàng');
            });
        }
    }
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

address {
    font-style: normal;
    line-height: 1.6;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.775rem;
}
</style>
