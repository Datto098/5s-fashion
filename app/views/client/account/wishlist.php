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
                                <a class="nav-link active" href="<?= url('wishlist') ?>">
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
                        <h2 class="content-title">Sản phẩm yêu thích</h2>
                        <p class="content-subtitle">Danh sách những sản phẩm bạn đã lưu</p>
                    </div>

                    <!-- Wishlist Controls -->
                    <div class="wishlist-controls mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="wishlist-count mb-0">
                                    <span class="fw-bold"><?= count($wishlist ?? []) ?></span> sản phẩm
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="listView">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wishlist Items -->
                    <?php if (empty($wishlist)): ?>
                        <div class="empty-wishlist text-center py-5">
                            <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                            <h4>Chưa có sản phẩm yêu thích</h4>
                            <p class="text-muted mb-4">Khám phá và lưu những sản phẩm bạn yêu thích!</p>
                            <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-bag me-2"></i>Khám phá sản phẩm
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="wishlist-items" id="wishlistGrid">
                            <div class="row">
                                <?php foreach ($wishlist as $item): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="wishlist-item-card">
                                            <div class="product-image">
                                                <?php if (!empty($item['image'])): ?>
                                                    <?php
                                                    // Handle image path for file server
                                                    $imagePath = $item['image'];
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
                                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                                         class="product-image img-fluid">
                                                <?php else: ?>
                                                    <img src="<?= asset('images/no-image.jpg') ?>"
                                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                                         class="product-image img-fluid">
                                                <?php endif; ?>
                                                <div class="product-overlay">
                                                    <button class="btn btn-white btn-sm" onclick="quickView(<?= $item['product_id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <button class="btn btn-danger btn-sm remove-wishlist"
                                                        onclick="removeFromWishlist(<?= $item['product_id'] ?>)"
                                                        title="Xóa khỏi yêu thích">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>                                            <div class="product-info p-3">
                                                <h6 class="product-name">
                                                    <a href="<?= url('product/' . $item['slug']) ?>">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                </h6>

                                                <div class="product-price mb-2">
                                                    <?php if (!empty($item['sale_price'])): ?>
                                                        <span class="current-price fw-bold text-danger">
                                                            <?= number_format($item['sale_price']) ?>đ
                                                        </span>
                                                        <span class="original-price text-muted text-decoration-line-through ms-2">
                                                            <?= number_format($item['price']) ?>đ
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="current-price fw-bold">
                                                            <?= number_format($item['price']) ?>đ
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="product-rating mb-3">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?= $i <= ($item['rating'] ?? 0) ? 'text-warning' : 'text-muted' ?>"></i>
                                                    <?php endfor; ?>
                                                    <small class="text-muted ms-1">(<?= $item['reviews_count'] ?? 0 ?>)</small>
                                                </div>

                                                <div class="product-actions">
                                                    <button class="btn btn-primary btn-sm w-100"
                                                            onclick="addToCart(<?= $item['product_id'] ?>)">
                                                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- List View (Hidden by default) -->
                        <div class="wishlist-items d-none" id="wishlistList">
                            <?php foreach ($wishlist as $item): ?>
                                <div class="wishlist-item-row">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="product-image-small">
                                                <?php if (!empty($item['image'])): ?>
                                                    <?php
                                                    // Handle image path for file server
                                                    $imagePath = $item['image'];
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
                                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                                         class="img-fluid">
                                                <?php else: ?>
                                                    <img src="<?= asset('images/no-image.jpg') ?>"
                                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                                         class="img-fluid">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="product-name mb-1">
                                                <a href="<?= url('product/' . $item['slug']) ?>">
                                                    <?= htmlspecialchars($item['name']) ?>
                                                </a>
                                            </h6>
                                            <div class="product-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= ($item['rating'] ?? 0) ? 'text-warning' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                                <small class="text-muted ms-1">(<?= $item['reviews_count'] ?? 0 ?>)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="product-price">
                                                <?php if (!empty($item['sale_price'])): ?>
                                                    <span class="current-price fw-bold text-danger">
                                                        <?= number_format($item['sale_price']) ?>đ
                                                    </span>
                                                    <br>
                                                    <span class="original-price text-muted text-decoration-line-through">
                                                        <?= number_format($item['price']) ?>đ
                                                    </span>
                                                <?php else: ?>
                                                    <span class="current-price fw-bold">
                                                        <?= number_format($item['price']) ?>đ
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="product-actions d-flex gap-2">
                                                <button class="btn btn-primary btn-sm" onclick="addToCart(<?= $item['product_id'] ?>)">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm" onclick="quickView(<?= $item['product_id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="removeFromWishlist(<?= $item['product_id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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

.wishlist-count {
    font-size: 1.1rem;
    color: #333;
}

.wishlist-item-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.wishlist-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    overflow: hidden;
    height: 250px;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image:hover img {
    transform: scale(1.1);
}

.product-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-image:hover .product-overlay {
    opacity: 1;
}

.remove-wishlist {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.product-name a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
}

.product-name a:hover {
    color: #dc3545;
}

.wishlist-item-row {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: box-shadow 0.3s ease;
}

.wishlist-item-row:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.product-image-small {
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 8px;
}

.product-image-small img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.empty-wishlist {
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

    .wishlist-controls {
        text-align: center;
    }

    .wishlist-controls .col-md-6 {
        margin-bottom: 15px;
    }

    .product-image {
        height: 200px;
    }

    .wishlist-item-row .product-actions {
        margin-top: 15px;
        justify-content: center;
    }
}
</style>

<script>
// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    // Add to page
    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

// View toggle functionality
document.getElementById('gridView').addEventListener('click', function() {
    document.getElementById('wishlistGrid').classList.remove('d-none');
    document.getElementById('wishlistList').classList.add('d-none');
    this.classList.add('active');
    document.getElementById('listView').classList.remove('active');
});

document.getElementById('listView').addEventListener('click', function() {
    document.getElementById('wishlistList').classList.remove('d-none');
    document.getElementById('wishlistGrid').classList.add('d-none');
    this.classList.add('active');
    document.getElementById('gridView').classList.remove('active');
});

function removeFromWishlist(productId) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch('<?= url('account/wishlist/remove') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Xóa item ngay khỏi DOM
                const gridCard = button.closest('.col-lg-4');
                const listRow = button.closest('.wishlist-item-row');
                
                if (gridCard) {
                    gridCard.style.transition = 'all 0.3s ease';
                    gridCard.style.opacity = '0';
                    gridCard.style.transform = 'scale(0.8)';
                    setTimeout(() => gridCard.remove(), 300);
                }
                if (listRow) {
                    listRow.style.transition = 'all 0.3s ease';
                    listRow.style.opacity = '0';
                    listRow.style.transform = 'scale(0.98)';
                    setTimeout(() => listRow.remove(), 300);
                }
                
                // Update wishlist count
                const countElement = document.querySelector('.wishlist-count .fw-bold');
                if (countElement) {
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = Math.max(currentCount - 1, 0);
                }
                
                showToast('Đã xóa sản phẩm khỏi danh sách yêu thích', 'success');
                
                // Check if wishlist is empty after animation and show empty state
                setTimeout(() => {
                    const remainingGrid = document.querySelectorAll('#wishlistGrid .wishlist-item-card');
                    const remainingList = document.querySelectorAll('#wishlistList .wishlist-item-row');
                    if (remainingGrid.length === 0 && remainingList.length === 0) {
                        // Hide both views and show empty state
                        document.getElementById('wishlistGrid').classList.add('d-none');
                        document.getElementById('wishlistList').classList.add('d-none');
                        
                        // Show empty state
                        const emptyHtml = `
                            <div class="empty-wishlist text-center py-5">
                                <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                                <h4>Chưa có sản phẩm yêu thích</h4>
                                <p class="text-muted mb-4">Khám phá và lưu những sản phẩm bạn yêu thích!</p>
                                <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-bag me-2"></i>Khám phá sản phẩm
                                </a>
                            </div>
                        `;
                        document.querySelector('.account-content').insertAdjacentHTML('beforeend', emptyHtml);
                    }
                }, 400);
            } else {
                button.innerHTML = originalContent;
                button.disabled = false;
                showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.innerHTML = originalContent;
            button.disabled = false;
            showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
        });
    }
    // Helper: check if wishlist is empty and reload page if so
    function checkWishlistEmpty() {
        const remainingGrid = document.querySelectorAll('#wishlistGrid .wishlist-item-card');
        const remainingList = document.querySelectorAll('#wishlistList .wishlist-item-row');
        if (remainingGrid.length === 0 && remainingList.length === 0) {
            location.reload();
        }
    }
}

function quickView(productId) {
    // Implementation for quick view
    alert('Chức năng xem nhanh đang được phát triển. Product ID: ' + productId);
}

function addToCart(productId) {
    // Use global addToCart function if available
    if (typeof window.addToCart === 'function') {
        window.addToCart(productId);
    } else {
        alert('Chức năng thêm vào giỏ hàng đang được phát triển. Product ID: ' + productId);
    }
}
</script>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>
