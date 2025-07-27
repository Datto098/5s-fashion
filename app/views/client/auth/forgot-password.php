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
                        <h2 class="auth-title">Quên Mật Khẩu</h2>
                        <p class="auth-subtitle">Nhập email để nhận liên kết đặt lại mật khẩu</p>
                    </div>

                    <form action="<?= url('forgot-password') ?>" method="POST" class="auth-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email đã đăng ký</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="Nhập địa chỉ email của bạn" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Gửi Liên Kết Đặt Lại
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">Nhớ lại mật khẩu?
                            <a href="<?= url('login') ?>" class="text-primary">Đăng nhập</a>
                        </p>
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
                                <i class="fas fa-key fa-4x text-warning"></i>
                            </div>
                            <h3 class="auth-title">Quên mật khẩu</h3>
                            <p class="auth-subtitle text-muted">Nhập email để nhận link khôi phục mật khẩu</p>
                        </div>

                        <!-- Forgot Password Form -->
                        <form id="forgotPasswordForm" class="auth-form" novalidate>
                            <div class="mb-3">
                                <label for="forgotEmail" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email đã đăng ký
                                </label>
                                <input type="email" class="form-control" id="forgotEmail" name="email" required
                                       placeholder="Nhập địa chỉ email">
                                <div class="form-text">
                                    Chúng tôi sẽ gửi link khôi phục mật khẩu đến email này
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg w-100 mb-3" id="forgotPasswordBtn">
                                <i class="fas fa-paper-plane me-2"></i>Gửi link khôi phục
                            </button>
                        </form>

                        <!-- Success Message (Hidden by default) -->
                        <div class="success-message" id="successMessage" style="display: none;">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Đã gửi thành công!</strong><br>
                                Vui lòng kiểm tra email và làm theo hướng dẫn để khôi phục mật khẩu.
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Không nhận được email? Kiểm tra thư mục spam hoặc
                                        <a href="#" onclick="resendEmail()" class="text-decoration-none">gửi lại</a>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Auth Footer -->
                        <div class="auth-footer text-center mt-4">
                            <p class="mb-2">
                                <a href="/login" class="text-decoration-none">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                                </a>
                            </p>
                            <p class="mb-0">
                                Chưa có tài khoản?
                                <a href="/register" class="register-link">Đăng ký ngay</a>
                            </p>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="help-section mt-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Cần hỗ trợ?
                                </h6>
                                <p class="text-muted mb-3">
                                    Nếu bạn gặp khó khăn trong việc khôi phục mật khẩu,
                                    hãy liên hệ với chúng tôi
                                </p>
                                <div class="contact-options">
                                    <a href="tel:19001900" class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-phone me-1"></i>1900 1900
                                    </a>
                                    <a href="mailto:support@5sfashion.com" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-envelope me-1"></i>Email hỗ trợ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../layouts/footer.php'; ?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/auth.js"></script>

    <!-- Initialize Forgot Password -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.authManager = new AuthManager();
            authManager.initializeForgotPassword();

            // Auto-focus email input
            document.getElementById('forgotEmail').focus();
        });
    </script>
</body>
</html>
