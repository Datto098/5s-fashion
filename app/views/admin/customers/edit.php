<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Chỉnh sửa khách hàng</h1>
                    <p class="text-muted mb-0">Cập nhật thông tin: <?= htmlspecialchars($customer['full_name']) ?></p>
                </div>
                <div>
                    <a href="/5s-fashion/admin/customers/show/<?= $customer['id'] ?>" class="btn btn-info">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                    <a href="/5s-fashion/admin/customers" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Error/Success Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/5s-fashion/admin/customers/update/<?= $customer['id'] ?>" method="POST" enctype="multipart/form-data" id="customerForm">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullName" name="full_name"
                                           value="<?= htmlspecialchars($customer['full_name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                           value="<?= htmlspecialchars($customer['username']) ?>">
                                    <small class="text-muted">Có thể để trống nếu chỉ sử dụng email để đăng nhập</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= htmlspecialchars($customer['email']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3"
                                      placeholder="Nhập địa chỉ đầy đủ của khách hàng"><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Password Change -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key"></i> Thay đổi mật khẩu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Chỉ điền thông tin bên dưới nếu muốn thay đổi mật khẩu. Để trống nếu giữ nguyên mật khẩu hiện tại.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Tối thiểu 6 ký tự</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" id="generatePassword">
                                <i class="fas fa-random"></i> Tạo mật khẩu ngẫu nhiên
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Avatar Upload -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image"></i> Ảnh đại diện
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($customer['avatar'])): ?>
                            <div class="current-avatar mb-3">
                                <img src="/5s-fashion/public<?= htmlspecialchars($customer['avatar']) ?>"
                                     alt="Current avatar" class="img-thumbnail rounded-circle"
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <p class="text-muted small mt-1">Ảnh hiện tại</p>
                            </div>
                        <?php endif; ?>

                        <div class="upload-area" id="avatarUpload">
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" hidden>
                            <div class="upload-content text-center p-4 border border-dashed rounded">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1">Kéo thả ảnh vào đây hoặc <button type="button" class="btn btn-link p-0">chọn file mới</button></p>
                                <small class="text-muted">Chỉ chấp nhận file ảnh (JPG, PNG, GIF) tối đa 5MB</small>
                            </div>
                        </div>
                        <div id="avatarPreview" class="mt-3" style="display: none;">
                            <img id="avatarPreviewImg" class="img-thumbnail rounded-circle"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Thông tin bổ sung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dateOfBirth" class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="date_of_birth"
                                           value="<?= htmlspecialchars($customer['date_of_birth'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Giới tính</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Chọn giới tính</option>
                                        <option value="male" <?= ($customer['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Nam</option>
                                        <option value="female" <?= ($customer['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nữ</option>
                                        <option value="other" <?= ($customer['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Ghi chú về khách hàng (tùy chọn)"><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Account Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info"></i> Thông tin tài khoản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>ID khách hàng:</strong> #<?= $customer['id'] ?>
                        </div>
                        <div class="mb-2">
                            <strong>Ngày tham gia:</strong> <?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?>
                        </div>
                        <?php if (!empty($customer['last_login'])): ?>
                        <div class="mb-2">
                            <strong>Đăng nhập lần cuối:</strong> <?= date('d/m/Y H:i', strtotime($customer['last_login'])) ?>
                        </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <strong>Cập nhật lần cuối:</strong> <?= date('d/m/Y H:i', strtotime($customer['updated_at'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Cài đặt tài khoản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái tài khoản</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $customer['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="inactive" <?= $customer['status'] === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="emailVerified" name="email_verified" value="1"
                                       <?= !empty($customer['email_verified_at']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="emailVerified">
                                    Email đã được xác thực
                                </label>
                            </div>
                        </div>

                        <?php if (empty($customer['email_verified_at'])): ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendVerificationEmail" name="send_verification_email" value="1">
                                <label class="form-check-label" for="sendVerificationEmail">
                                    Gửi email xác thực
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Group -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users"></i> Nhóm khách hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customerGroup" class="form-label">Nhóm</label>
                            <select class="form-select" id="customerGroup" name="customer_group">
                                <option value="regular" <?= ($customer['customer_group'] ?? 'regular') === 'regular' ? 'selected' : '' ?>>Khách hàng thường</option>
                                <option value="vip" <?= ($customer['customer_group'] ?? '') === 'vip' ? 'selected' : '' ?>>Khách hàng VIP</option>
                                <option value="wholesale" <?= ($customer['customer_group'] ?? '') === 'wholesale' ? 'selected' : '' ?>>Khách hàng sỉ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="discountPercent" class="form-label">Chiết khấu (%)</label>
                            <input type="number" class="form-control" id="discountPercent" name="discount_percent"
                                   min="0" max="100" step="0.1" value="<?= $customer['discount_percent'] ?? 0 ?>">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật khách hàng
                            </button>
                            <a href="/5s-fashion/admin/customers/show/<?= $customer['id'] ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <a href="/5s-fashion/admin/customers" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePassword');

    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    // Generate random password
    const generatePasswordBtn = document.getElementById('generatePassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    generatePasswordBtn.addEventListener('click', function() {
        const password = generateRandomPassword();
        passwordInput.value = password;
        confirmPasswordInput.value = password;

        // Show password temporarily
        passwordInput.setAttribute('type', 'text');
        const icon = togglePasswordBtn.querySelector('i');
        icon.className = 'fas fa-eye-slash';

        // Copy to clipboard
        navigator.clipboard.writeText(password).then(() => {
            alert('Mật khẩu đã được tạo và sao chép vào clipboard: ' + password);
        });
    });

    function generateRandomPassword() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    // Avatar upload
    const avatarUpload = document.getElementById('avatarUpload');
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPreviewImg = document.getElementById('avatarPreviewImg');

    avatarUpload.addEventListener('click', () => avatarInput.click());
    avatarUpload.addEventListener('dragover', handleDragOver);
    avatarUpload.addEventListener('drop', handleDrop);

    avatarInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            previewAvatar(this.files[0]);
        }
    });

    function handleDrop(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            avatarInput.files = files;
            previewAvatar(files[0]);
        }
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.style.borderColor = '#007bff';
    }

    function previewAvatar(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarPreviewImg.src = e.target.result;
            avatarPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Form validation
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        const fullName = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!fullName || !email) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return false;
        }

        // Only validate password if it's being changed
        if (password) {
            if (password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Định dạng email không hợp lệ!');
            return false;
        }
    });

    // Real-time password confirmation check
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;

        if (password && confirmPassword && password !== confirmPassword) {
            this.setCustomValidity('Mật khẩu xác nhận không khớp');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
});
</script>

<style>
.upload-area {
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.upload-area:hover {
    border-color: #007bff !important;
}

.upload-content {
    background: #f8f9fa;
}

.current-avatar img {
    border: 2px solid #dee2e6;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.text-danger {
    font-weight: 500;
}

.input-group .btn {
    border-color: #ced4da;
}

.is-invalid {
    border-color: #dc3545;
}

#avatarPreview img {
    border: 2px solid #dee2e6;
}
</style>
