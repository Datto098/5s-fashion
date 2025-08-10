<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Cập nhật trạng thái đơn hàng</h1>
            <p class="text-muted">Đơn hàng #<?= htmlspecialchars($order['order_code'] ?? $order['id']) ?></p>
        </div>
        <a href="/5s-fashion/admin/orders" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Order Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Thông tin đơn hàng</h6>
                    <p><strong>Mã đơn:</strong> <?= htmlspecialchars($order['order_code'] ?? $order['id']) ?></p>
                    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></p>
                    <p><strong>Tổng tiền:</strong> <?= number_format($order['total_amount']) ?>đ</p>
                </div>
                <div class="col-md-6">
                    <h6>Trạng thái hiện tại</h6>
                    <p>
                        <span class="badge bg-<?=
                            $order['status'] === 'pending' ? 'warning' :
                            ($order['status'] === 'processing' ? 'info' :
                            ($order['status'] === 'shipping' ? 'primary' :
                            ($order['status'] === 'delivered' ? 'success' : 'danger')))
                        ?>">
                            <?php
                            $statusLabels = [
                                'pending' => 'Chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'shipping' => 'Đang giao',
                                'delivered' => 'Đã giao',
                                'cancelled' => 'Đã hủy'
                            ];
                            echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                            ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Form -->
    <div class="card">
        <div class="card-body">
            <h6 class="mb-3">Cập nhật trạng thái</h6>
            <form action="/5s-fashion/admin/orders/update-status/<?= $order['id'] ?>" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái mới</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="">-- Chọn trạng thái --</option>
                        <?php foreach ($validStatuses as $status): ?>
                            <?php if ($status !== $order['status']): ?>
                                <option value="<?= $status ?>">
                                    <?= $statusLabels[$status] ?? ucfirst($status) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="admin_notes" class="form-label">Ghi chú (tùy chọn)</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3"
                              placeholder="Nhập ghi chú về việc cập nhật trạng thái..."></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Cập nhật trạng thái
                    </button>
                    <a href="/5s-fashion/admin/orders" class="btn btn-secondary">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
