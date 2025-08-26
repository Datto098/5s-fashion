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

                    <form id="forgotPasswordForm" action="<?= url('forgot-password') ?>" method="POST" class="auth-form">
                        <input type="hidden" name="ajax" value="1" />
                        <div class="mb-3">
                            <label for="forgotEmail" class="form-label">Email đã đăng ký</label>
                            <input type="email" class="form-control" id="forgotEmail" name="email"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                placeholder="Nhập địa chỉ email của bạn" required>
                        </div>

                        <button id="forgotPasswordBtn" type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Gửi Liên Kết Đặt Lại
                        </button>
                    </form>

                    <div id="successMessage" class="alert alert-success" style="display:none;">
                        Liên kết đặt lại mật khẩu đã được gửi. Vui lòng kiểm tra email của bạn.
                    </div>

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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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



<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
    // Cache-bust local JS by filemtime to ensure clients get the updated script
    $clientJs = PUBLIC_PATH . '/assets/js/client.js';
    $authJs = PUBLIC_PATH . '/assets/js/auth.js';
    $clientVer = file_exists($clientJs) ? filemtime($clientJs) : time();
    $authVer = file_exists($authJs) ? filemtime($authJs) : time();
?>
<script src="/5s-fashion/public/assets/js/client.js?v=<?= $clientVer ?>"></script>
<script src="/5s-fashion/public/assets/js/auth.js?v=<?= $authVer ?>"></script>

<!-- Initialize Forgot Password -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.authManager = new AuthManager();
        authManager.initializeForgotPassword();

        // Auto-focus email input (guarded)
        const _fe = document.getElementById('forgotEmail');
        if (_fe && typeof _fe.focus === 'function') {
            _fe.focus();
        }
    });
</script>
<?php
// Get the buffered content
$content = ob_get_clean();

// Set additional data
$show_breadcrumb = false;

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>

</body>

</html>