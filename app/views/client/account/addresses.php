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
                                <a class="nav-link active" href="<?= url('addresses') ?>">
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
                    <div class="content-header mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="content-title">Địa chỉ của tôi</h2>
                            <p class="content-subtitle">Quản lý địa chỉ giao hàng</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                        </button>
                    </div>

                    <!-- Address List -->
                    <?php if (empty($addresses)): ?>
                        <div class="empty-addresses text-center py-5">
                            <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                            <h4>Chưa có địa chỉ nào</h4>
                            <p class="text-muted mb-4">Thêm địa chỉ giao hàng để mua sắm thuận tiện hơn!</p>
                            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>Thêm địa chỉ đầu tiên
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="addresses-list">
                            <?php foreach ($addresses as $address): ?>
                                <div class="address-card">
                                    <div class="address-header">
                                        <div class="address-info">
                                            <h6 class="address-name">
                                                <?= htmlspecialchars($address['name']) ?>
                                                <?php if ($address['is_default']): ?>
                                                    <span class="badge bg-primary ms-2">Mặc định</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="address-phone"><?= htmlspecialchars($address['phone']) ?></p>
                                        </div>
                                        <div class="address-actions">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editAddress(<?= $address['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteAddress(<?= $address['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="address-body">
                                        <p class="address-full">
                                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                            <?= htmlspecialchars($address['address']) ?>
                                        </p>
                                    </div>

                                    <?php if (!$address['is_default']): ?>
                                        <div class="address-footer">
                                            <button class="btn btn-outline-secondary btn-sm" onclick="setDefaultAddress(<?= $address['id'] ?>)">
                                                <i class="fas fa-star me-1"></i>Đặt làm mặc định
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('addresses') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="province" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <select class="form-select" id="province" name="province" required>
                                    <option value="">Chọn tỉnh/thành phố</option>
                                    <option value="hanoi">Hà Nội</option>
                                    <option value="hcm">TP. Hồ Chí Minh</option>
                                    <option value="danang">Đà Nẵng</option>
                                    <!-- Add more provinces -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <select class="form-select" id="district" name="district" required>
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="ward" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                <select class="form-select" id="ward" name="ward" required>
                                    <option value="">Chọn phường/xã</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3"
                                  placeholder="Số nhà, tên đường..." required></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                        <label class="form-check-label" for="is_default">
                            Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu địa chỉ</button>
                </div>
            </form>
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

.address-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 20px;
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}

.address-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.address-header {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}

.address-name {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.address-phone {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

.address-body {
    padding: 15px 20px;
}

.address-full {
    margin: 0;
    color: #333;
}

.address-footer {
    background: #f8f9fa;
    padding: 10px 20px;
    border-top: 1px solid #e9ecef;
}

.address-actions .btn {
    margin-left: 5px;
}

.empty-addresses {
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

    .content-header {
        flex-direction: column;
        text-align: center;
    }

    .content-header .btn {
        margin-top: 15px;
    }

    .address-header {
        flex-direction: column;
        text-align: center;
    }

    .address-actions {
        margin-top: 10px;
    }
}
</style>

<script>
function editAddress(addressId) {
    // Implementation for edit address
    alert('Chức năng sửa địa chỉ đang được phát triển. ID: ' + addressId);
}

function deleteAddress(addressId) {
    if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
        // Implementation for delete address
        alert('Chức năng xóa địa chỉ đang được phát triển. ID: ' + addressId);
    }
}

function setDefaultAddress(addressId) {
    // Implementation for set default address
    alert('Chức năng đặt địa chỉ mặc định đang được phát triển. ID: ' + addressId);
}
</script>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>
