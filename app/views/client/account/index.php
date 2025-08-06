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
                         <h5 class="mt-2"><?= htmlspecialchars(getUser()['name'] ?? getUser()['full_name'] ?? 'User') ?></h5>
                        <p class="text-muted"><?= htmlspecialchars(getUser()['email'] ?? '') ?></p>
                    </div>

                    <nav class="account-nav">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="<?= url('account') ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('account/profile') ?>">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('orders') ?>">
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
                        <h2 class="content-title">Dashboard</h2>
                        <p class="content-subtitle">Chào mừng bạn trở lại, <?= htmlspecialchars($user['name'] ?? 'User') ?>!</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?= count($recent_orders) ?></h3>
                                    <p>Đơn hàng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>0</h3>
                                    <p>Yêu thích</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>0</h3>
                                    <p>Địa chỉ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?= isset($user['created_at']) ? date('Y', strtotime($user['created_at'])) : date('Y') ?></h3>
                                    <p>Thành viên từ</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Đơn hàng gần đây</h5>
                            <a href="<?= url('orders') ?>" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_orders)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                    <h5>Chưa có đơn hàng nào</h5>
                                    <p class="text-muted">Bạn chưa thực hiện đơn hàng nào. Hãy khám phá các sản phẩm của chúng tôi!</p>
                                    <a href="<?= url('shop') ?>" class="btn btn-primary">Mua sắm ngay</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>#<?= $order['id'] ?></td>
                                                    <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                                    <td><?= number_format($order['total']) ?>đ</td>
                                                    <td>
                                                        <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                                            <?= $order['status'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= url('orders/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                            Xem chi tiết
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-user fa-2x text-primary mb-3"></i>
                                    <h5>Cập nhật thông tin</h5>
                                    <p class="text-muted">Cập nhật thông tin cá nhân của bạn</p>
                                    <a href="<?= url('account/profile') ?>" class="btn btn-primary">Cập nhật</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-lock fa-2x text-warning mb-3"></i>
                                    <h5>Đổi mật khẩu</h5>
                                    <p class="text-muted">Thay đổi mật khẩu để bảo mật tài khoản</p>
                                    <a href="<?= url('account/password') ?>" class="btn btn-warning">Đổi mật khẩu</a>
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

.stat-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    color: #dc3545;
    font-size: 2rem;
    margin-bottom: 10px;
}

.stat-info h3 {
    color: #333;
    font-weight: 700;
    margin: 0;
}

.stat-info p {
    color: #6c757d;
    margin: 0;
    font-size: 0.9rem;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}

@media (max-width: 768px) {
    .account-container {
        padding: 20px 0;
    }

    .account-content {
        padding: 20px;
    }

    .stat-card {
        margin-bottom: 15px;
    }
}
</style>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>
