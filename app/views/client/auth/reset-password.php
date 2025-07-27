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
                        <h2 class="auth-title">Đặt Lại Mật Khẩu</h2>
                        <p class="auth-subtitle">Nhập mật khẩu mới cho tài khoản của bạn</p>
                    </div>

                    <form action="<?= url('reset-password') ?>" method="POST" class="auth-form">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">

                        <div class="mb-3">
                            <label for="email_display" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_display"
                                   value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   minlength="6" placeholder="Nhập mật khẩu mới" required>
                            <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="Nhập lại mật khẩu mới" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-key me-2"></i>Đặt Lại Mật Khẩu
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Nhớ lại mật khẩu?
                            <a href="<?= url('login') ?>" class="text-primary">Đăng nhập</a>
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

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
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
