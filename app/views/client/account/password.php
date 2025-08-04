<script>
// Ẩn flash message sau 2 giây với hiệu ứng fade out
document.addEventListener('DOMContentLoaded', function() {
    var flash = document.querySelector('.alert-success, .alert-danger, .alert-warning, .alert-info');
    if (flash) {
        setTimeout(function() {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(function() {
                flash.style.display = 'none';
            }, 500);
        }, 2000);
    }
});
</script>
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
                                <a class="nav-link" href="<?= url('wishlist') ?>">
                                    <i class="fas fa-heart me-2"></i>Sản phẩm yêu thích
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="<?= url('account/password') ?>">
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
                        <h2 class="content-title">Đổi mật khẩu</h2>
                        <p class="content-subtitle">Thay đổi mật khẩu để bảo mật tài khoản của bạn</p>
                    </div>

                    <!-- Password Form -->
                    <div class="row">
                        <div class="col-md-8">
                            <form action="<?= url('account/updatePassword') ?>" method="POST" class="password-form">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">
                                        Mật khẩu hiện tại <span class="text-danger">*</span>
                                    </label>
                                    <div class="password-input">
                                        <input type="password" class="form-control" id="current_password"
                                               name="current_password" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">
                                        Mật khẩu mới <span class="text-danger">*</span>
                                    </label>
                                    <div class="password-input">
                                        <input type="password" class="form-control" id="new_password"
                                               name="new_password" minlength="6" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">
                                        Xác nhận mật khẩu mới <span class="text-danger">*</span>
                                    </label>
                                    <div class="password-input">
                                        <input type="password" class="form-control" id="confirm_password"
                                               name="confirm_password" minlength="6" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                                    </button>
                                    <a href="<?= url('account') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tips -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-shield-alt me-2"></i>Bảo mật tài khoản
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="security-tip">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Sử dụng ít nhất 8 ký tự</span>
                                    </div>
                                    <div class="security-tip">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Kết hợp chữ hoa và chữ thường</span>
                                    </div>
                                    <div class="security-tip">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Bao gồm số và ký tự đặc biệt</span>
                                    </div>
                                    <div class="security-tip">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Không sử dụng thông tin cá nhân</span>
                                    </div>
                                    <div class="security-tip">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Không chia sẻ mật khẩu với ai</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-clock me-2"></i>Lịch sử đăng nhập
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="login-history">
                                        <div class="login-item">
                                            <div class="login-info">
                                                <strong>Lần cuối đăng nhập:</strong><br>
                                                <small class="text-muted">
                                                    <?= isset(getUser()['last_login']) ? date('d/m/Y H:i', strtotime(getUser()['last_login'])) : 'Chưa có thông tin' ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
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

.password-form .form-label {
    font-weight: 600;
    color: #333;
}

.password-input {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 5px;
}

.password-toggle:hover {
    color: #dc3545;
}

.form-actions {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
    margin-top: 20px;
}

.security-tip {
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.login-history .login-item {
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.login-history .login-item:last-child {
    border-bottom: none;
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
}
</style>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = field.nextElementSibling.querySelector('i');

    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;

    if (confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Mật khẩu xác nhận không khớp');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php
// Get the content and assign to layout
$content = ob_get_clean();
include VIEW_PATH . '/client/layouts/app.php';
?>
