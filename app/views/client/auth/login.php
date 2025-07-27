<?php
// Start output buffering for content
ob_start();
?>

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header text-center mb-4">
                        <h2 class="auth-title">Đăng Nhập</h2>
                        <p class="auth-subtitle">Chào mừng bạn quay lại 5S Fashion</p>
                    </div>

                    <form action="<?= url('login') ?>" method="POST" class="auth-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                        </button>

                        <div class="text-center">
                            <a href="<?= url('forgot-password') ?>" class="text-muted">Quên mật khẩu?</a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Chưa có tài khoản?
                            <a href="<?= url('register') ?>" class="text-primary">Đăng ký ngay</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.auth-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid #eee;
}

.auth-title {
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.auth-subtitle {
    color: var(--secondary-color);
    font-size: 0.95rem;
}

.auth-form .form-control {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
}

.auth-form .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.auth-form .btn {
    border-radius: 8px;
    padding: 0.75rem;
    font-weight: 600;
}
</style>

<?php
// Get the buffered content
$content = ob_get_clean();

// Set additional data
$show_breadcrumb = false;

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
