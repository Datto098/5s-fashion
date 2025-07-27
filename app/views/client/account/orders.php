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
                            <i class="fas fa-user-circle fa-4x text-danger"></i>
                        </div>
                        <h5 class="mt-2"><?= htmlspecialchars(getUser()['name'] ?? 'User') ?></h5>
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
                                <a class="nav-link text-danger" href="<?= url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                <div class="account-content">
                    <div class="content-header mb-4">
                        <h2 class="content-title">Đơn hàng của tôi</h2>
                        <p class="content-subtitle">Theo dõi tình trạng đơn hàng của bạn</p>
                    </div>

                    <!-- Order Filters -->
                    <div class="order-filters mb-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active">Tất cả</button>
                            <button type="button" class="btn btn-outline-primary">Chờ xác nhận</button>
                            <button type="button" class="btn btn-outline-primary">Đang xử lý</button>
                            <button type="button" class="btn btn-outline-primary">Đang giao</button>
                            <button type="button" class="btn btn-outline-primary">Hoàn thành</button>
                            <button type="button" class="btn btn-outline-primary">Đã hủy</button>
                        </div>
                    </div>

                    <!-- Orders List -->
                    <?php if (empty($orders)): ?>
                        <div class="empty-orders text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            <h4>Chưa có đơn hàng nào</h4>
                            <p class="text-muted mb-4">Bạn chưa thực hiện đơn hàng nào. Hãy khám phá các sản phẩm tuyệt vời của chúng tôi!</p>
                            <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div class="order-info">
                                            <h6 class="order-id">Đơn hàng #<?= $order['id'] ?></h6>
                                            <p class="order-date">Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                                        </div>
                                        <div class="order-status">
                                            <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="order-body">
                                        <div class="order-items">
                                            <!-- Order items would be listed here -->
                                            <p class="text-muted">Chi tiết sản phẩm sẽ được hiển thị khi Order model được tạo</p>
                                        </div>
                                    </div>

                                    <div class="order-footer">
                                        <div class="order-total">
                                            <strong>Tổng: <?= number_format($order['total']) ?>đ</strong>
                                        </div>
                                        <div class="order-actions">
                                            <a href="<?= url('orders/' . $order['id']) ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Xem chi tiết
                                            </a>
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-times me-1"></i>Hủy đơn
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <button class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-redo me-1"></i>Mua lại
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Orders pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Trước</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Sau</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.account-container {
    background: #f8f9fa;
    min-height: 100vh;
}

.account-sidebar {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.user-avatar {
    margin-bottom: 15px;
}

.account-nav .nav-link {
    color: #6c757d;
    border: none;
    text-align: left;
    margin-bottom: 5px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.account-nav .nav-link:hover,
.account-nav .nav-link.active {
    background: #dc3545;
    color: white;
}

.account-nav .nav-link.text-danger:hover {
    background: #dc3545;
    color: white;
}

.account-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.content-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 5px;
}

.content-subtitle {
    color: #6c757d;
    margin: 0;
}

.order-filters .btn-group .btn {
    border: 1px solid #dc3545;
    color: #dc3545;
}

.order-filters .btn-group .btn.active {
    background: #dc3545;
    color: white;
}

.order-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 20px;
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}

.order-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.order-header {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}

.order-id {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.order-date {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

.order-body {
    padding: 20px;
}

.order-footer {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #e9ecef;
}

.order-total {
    font-size: 1.1rem;
    color: #333;
}

.order-actions .btn {
    margin-left: 5px;
}

.empty-orders {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 40px;
    margin: 20px 0;
}

@media (max-width: 768px) {
    .account-container {
        padding: 20px 0;
    }

    .account-content {
        padding: 20px;
    }

    .order-header,
    .order-footer {
        flex-direction: column;
        text-align: center;
    }

    .order-actions {
        margin-top: 10px;
    }

    .order-filters .btn-group {
        flex-wrap: wrap;
    }

    .order-filters .btn-group .btn {
        margin-bottom: 5px;
    }
}
</style>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>
