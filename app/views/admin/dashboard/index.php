<?php
// Dashboard content - all HTML moved to view as requested
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">
        <i class="fas fa-tachometer-alt"></i>
        Dashboard
    </h1>
    <p class="dashboard-subtitle">Chào mừng bạn quay trở lại, Admin!</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Tổng sản phẩm</div>
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_products']) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span><?= $stats['published_products'] ?> đang bán</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Đơn hàng hôm nay</div>
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['today_orders']) ?></div>
        <div class="stat-change">
            <span>Tổng <?= number_format($stats['total_orders']) ?> đơn hàng</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Khách hàng</div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_customers']) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span><?= $stats['new_customers'] ?> mới trong tháng</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Doanh thu tháng</div>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['monthly_revenue']) ?>đ</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>+12% so với tháng trước</span>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="dashboard-row">
    <!-- Recent Orders -->
    <div class="content-card" style="flex: 2; margin-right: 20px;">
        <div class="card-header">
            <h3>
                <i class="fas fa-shopping-bag"></i>
                Đơn hàng gần đây
            </h3>
            <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-sm btn-outline">Xem tất cả</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($order['id']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($order['customer']) ?></td>
                        <td><?= number_format($order['total']) ?>đ</td>
                        <td>
                            <span class="status-badge status-<?= $order['status'] ?>">
                                <?php
                                $statuses = [
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'completed' => 'Hoàn thành'
                                ];
                                echo $statuses[$order['status']] ?? $order['status'];
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="content-card" style="flex: 1;">
        <div class="card-header">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Sản phẩm sắp hết
            </h3>
        </div>
        <div class="low-stock-list">
            <?php foreach ($lowStockProducts as $product): ?>
            <div class="low-stock-item">
                <div class="product-info">
                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                    <small>Còn <?= $product['total_stock'] ?? 0 ?> / Tối thiểu <?= $product['min_stock'] ?? 0 ?></small>
                </div>
                <div class="stock-level">
                    <div class="stock-bar">
                        <div class="stock-fill" style="width: <?= (($product['total_stock'] ?? 0) / max(($product['min_stock'] ?? 1), 1)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Additional CSS for new elements -->
<style>
.dashboard-header {
    margin-bottom: 24px;
}

.dashboard-title {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.dashboard-subtitle {
    color: #6b7280;
    margin: 0;
    font-size: 16px;
}

.dashboard-row {
    display: flex;
    gap: 20px;
    margin-top: 24px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-outline {
    background: transparent;
    border: 1px solid #d1d5db;
    color: #6b7280;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.low-stock-list {
    padding: 20px;
}

.low-stock-item {
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.low-stock-item:last-child {
    border-bottom: none;
}

.product-info strong {
    display: block;
    color: #111827;
    margin-bottom: 4px;
}

.product-info small {
    color: #6b7280;
    font-size: 12px;
}

.stock-level {
    margin-top: 8px;
}

.stock-bar {
    width: 100%;
    height: 4px;
    background: #f3f4f6;
    border-radius: 2px;
    overflow: hidden;
}

.stock-fill {
    height: 100%;
    background: #ef4444;
    transition: width 0.3s;
}

@media (max-width: 768px) {
    .dashboard-row {
        flex-direction: column;
    }

    .content-card {
        margin-right: 0 !important;
    }
}
</style>
