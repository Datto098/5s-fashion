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
                        <div class="btn-group gap-2" role="group">
                            <button type="button" class="btn btn-primary order-filter-btn" data-status="all">Tất cả</button>
                            <button type="button" class="btn btn-outline-primary order-filter-btn" data-status="confirmed">Đã xác nhận</button>
                            <button type="button" class="btn btn-outline-primary order-filter-btn" data-status="pending">Chờ xác nhận</button>
                            <button type="button" class="btn btn-outline-primary order-filter-btn" data-status="processing">Đang xử lý</button>
                            <button type="button" class="btn btn-outline-primary order-filter-btn" data-status="shipped">Đang giao</button>
                            <button type="button" class="btn btn-outline-primary order-filter-btn" data-status="delivered,completed">Hoàn thành</button>
                            <button type="button" class="btn btn-outline-primary order-filter-btn" data-status="cancelled">Hủy</button>
                        </div>
                    </div>

                    <!-- Orders List -->
                    <?php if (empty($orders)): ?>
                        <div class="empty-orders text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            <h4>Chưa có đơn hàng nào</h4>
                            <p class="text-muted mb-4">Bạn chưa thực hiện đơn hàng nào. Hãy khám phá các sản phẩm tuyệt vời của chúng tôi!</p>
                            <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg">
                               Mua sắm ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card" data-status="<?= $order['status'] ?? 'pending' ?>">
                                    <div class="order-header">
                                        <div class="order-info">
                                            <h6 class="order-id">Đơn hàng #<?= $order['order_code'] ?></h6>
                                            <p class="order-date">Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                                        </div>
                                        <div class="order-status">
                                            <?php
                                            $status = $order['status'] ?? 'pending';
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Đang xử lý';

                                            switch($status) {
                                                case 'pending':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Chờ xác nhận';
                                                    break;
                                                case 'processing':
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Đang xử lý';
                                                    break;
                                                case 'confirmed':
                                                    $statusClass = 'bg-primary';
                                                    $statusText = 'Đã xác nhận';
                                                    break;
                                                case 'shipped':
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Đang giao hàng';
                                                    break;
                                                case 'delivered':
                                                case 'completed':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Hoàn thành';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Đã hủy';
                                                    break;
                                                case 'refunded':
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Đã hoàn tiền';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Chờ xác nhận';
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="order-body">
                                        <div class="order-items">
                                            <?php if (!empty($order['items'])): ?>
                                                <?php foreach ($order['items'] as $item): ?>
                                                    <div class="order-item d-flex align-items-center mb-2 p-2 border-bottom">
                                                        <div class="item-image me-3">
                                                            <?php if (!empty($item['featured_image'])): ?>
                                                                <img src="/5s-fashion/serve-file.php?file=<?php echo rawurlencode($item['featured_image']); ?>"
                                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                            <?php else: ?>
                                                                <div class="placeholder-image d-flex align-items-center justify-content-center rounded bg-light"
                                                                     style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="item-details flex-grow-1">
                                                            <h6 class="item-name mb-0 small"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                            <small class="text-muted">
                                                                Số lượng: <?php echo (int)$item['quantity']; ?> -
                                                                Giá: <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                                            </small>
                                                        </div>
                                                        <div class="item-total">
                                                            <small class="text-primary fw-bold"><?php echo number_format($item['total'], 0, ',', '.'); ?>đ</small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted mb-0">Không có thông tin sản phẩm</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="order-footer">
                                        <div class="order-total">
                                            <strong>Tổng: <?= number_format($order['total_amount']) ?>đ</strong>
                                        </div>
                                        <div class="order-actions">
                                            <a href="<?= url('orders/' . $order['id']) ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Xem chi tiết
                                            </a>
                                            <?php
                                            // Show cancel button only for COD orders that are not yet shipping/delivered/cancelled
                                            $canCancel = $order['payment_method'] === 'cod' &&
                                                        !in_array($order['status'], ['shipping', 'delivered', 'cancelled']);
                                            if ($canCancel):
                                            ?>
                                                <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder('<?= $order['id'] ?>')">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order filter functionality
    const filterBtns = document.querySelectorAll('.order-filter-btn');
    const orderCards = document.querySelectorAll('.order-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterStatus = this.getAttribute('data-status');

            // Update button states
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            // Filter orders
            if (filterStatus === 'all') {
                // Show all orders
                orderCards.forEach(card => {
                    card.style.display = 'block';
                });
            } else {
                // Show/hide orders based on status
                const statusArray = filterStatus.split(',');
                orderCards.forEach(card => {
                    const cardStatus = card.getAttribute('data-status');
                    if (statusArray.includes(cardStatus)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }            // Update counter if needed
            updateOrderCounter();
        });
    });

    function updateOrderCounter() {
        const visibleOrders = document.querySelectorAll('.order-card:not([style*="display: none"])').length;
        const totalOrders = orderCards.length;

        // Update the subtitle if it exists
        const subtitle = document.querySelector('.content-subtitle');
        if (subtitle) {
            if (visibleOrders === totalOrders) {
                subtitle.textContent = 'Theo dõi tình trạng đơn hàng của bạn';
            } else {
                subtitle.textContent = `Hiển thị ${visibleOrders} trên ${totalOrders} đơn hàng`;
            }
        }
    }
});
</script>

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

.order-filters .btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.order-filter-btn {
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 13px;
    min-width: auto;
    flex: 0 0 auto;
    border-radius: 20px;
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

<script>
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
        fetch('<?= url('api/orders') ?>/' + orderId + '/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đơn hàng đã được hủy thành công');
                location.reload(); // Reload page to show updated status
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể hủy đơn hàng'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy đơn hàng');
        });
    }
}
</script>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>
