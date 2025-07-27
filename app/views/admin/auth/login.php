<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Đăng nhập Admin' ?> - 5S Fashion</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSET_URL ?>/images/favicon.ico">

    <!-- CSS -->
    <link href="<?= ASSET_URL ?>/css/admin.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="auth-logo">
                <h1><span class="text-primary">5S</span> Fashion</h1>
                <p>Quản trị hệ thống</p>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
                <?php foreach ($flashMessages as $type => $message): ?>
                    <div class="alert alert-<?= $type ?> alert-dismissible">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="alert-close">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?= BASE_URL ?>/admin/login" class="auth-form" data-validate">

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        placeholder="Nhập email của bạn"
                        required
                    >
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        Mật khẩu
                    </label>
                    <div class="password-input">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            placeholder="Nhập mật khẩu"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" value="1" class="form-check-input">
                        <label for="remember" class="form-check-label">Ghi nhớ đăng nhập</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>

                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/admin/forgot-password" class="forgot-link">
                        <i class="fas fa-question-circle"></i>
                        Quên mật khẩu?
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <p>&copy; <?= date('Y') ?> 5S Fashion. All rights reserved.</p>
        </div>
    </div>

    <!-- CSS cho auth pages -->
    <style>
        .auth-body {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
        }

        .auth-card {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: var(--gray-800);
        }

        .auth-logo p {
            color: var(--gray-600);
            margin: 0;
            font-size: 1rem;
        }

        .auth-form {
            margin: 0;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .password-input {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 0.875rem;
        }

        .password-toggle:hover {
            color: var(--primary-red);
        }

        .btn-block {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            margin-top: 1rem;
        }

        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-link {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-fast);
        }

        .forgot-link:hover {
            color: var(--primary-red);
        }

        .auth-footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }

        .auth-footer p {
            margin: 0;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }

            .auth-logo h1 {
                font-size: 2rem;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }

            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const closeBtn = alert.querySelector('.alert-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 300);
                    });
                }
            });
        });

        // Form submission
        document.querySelector('.auth-form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
        });
    </script>
</body>
</html>
