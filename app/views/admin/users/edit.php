<?php
$title = $title ?? 'Chỉnh sửa Admin - zone Fashion Admin';
$user = $user ?? [];
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chỉnh sửa Admin: <?= htmlspecialchars($user['full_name'] ?? '') ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <?php if (isset($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <?php if ($crumb['url']): ?>
                                <li class="breadcrumb-item"><a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['name']) ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active"><?= htmlspecialchars($crumb['name']) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div>
            <a href="/zone-fashion/admin/users/<?= $user['id'] ?>" class="btn btn-outline-info me-2">
                <i class="fas fa-eye me-2"></i>Xem chi tiết
            </a>
            <a href="/zone-fashion/admin/users" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin tài khoản</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/zone-fashion/admin/users/<?= $user['id'] ?>/edit" enctype="multipart/form-data" id="editUserForm">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required
                                           value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                                           placeholder="Nhập tên đăng nhập">
                                    <div class="form-text">Chỉ được chứa chữ cái, số và dấu gạch dưới. Tối thiểu 3 ký tự.</div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                           placeholder="Nhập địa chỉ email">
                                </div>

                                <!-- Password (Optional for edit) -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                               placeholder="Để trống nếu không thay đổi">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu. Tối thiểu 8 ký tự nếu thay đổi.</div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3" id="confirmPasswordGroup" style="display: none;">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                           placeholder="Nhập lại mật khẩu mới">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Full Name -->
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required
                                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>"
                                           placeholder="Nhập họ và tên">
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                           placeholder="Nhập số điện thoại">
                                    <div class="form-text">10-11 chữ số</div>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= ($user['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                                        <option value="banned" <?= ($user['status'] ?? '') === 'banned' ? 'selected' : '' ?>>Bị cấm</option>
                                    </select>
                                    <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                        <div class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Không thể thay đổi trạng thái tài khoản của chính mình
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Current Avatar -->
                                <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Avatar hiện tại</label>
                                        <div>
                                            <img src="/zone-fashion/<?= htmlspecialchars($user['avatar']) ?>"
                                                 alt="Current Avatar"
                                                 class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Avatar Upload -->
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Avatar mới</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar"
                                           accept="image/jpeg,image/png,image/gif">
                                    <div class="form-text">Định dạng: JPG, PNG, GIF. Tối đa 2MB. Để trống nếu không thay đổi.</div>
                                </div>

                                <!-- Avatar Preview -->
                                <div class="mb-3" id="avatarPreview" style="display: none;">
                                    <label class="form-label">Xem trước avatar mới</label>
                                    <div>
                                        <img id="previewImage" src="" alt="Avatar Preview"
                                             class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="/zone-fashion/admin/users" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="col-lg-4">
            <!-- Account Info -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông tin tài khoản
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ngày tạo:</strong></td>
                            <td><?= isset($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Cập nhật cuối:</strong></td>
                            <td><?= isset($user['updated_at']) ? date('d/m/Y H:i', strtotime($user['updated_at'])) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Đăng nhập cuối:</strong></td>
                            <td><?= isset($user['last_login']) && $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Chưa đăng nhập' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Statistics (if available) -->
            <?php if (isset($user['login_count']) || isset($user['created_orders'])): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Thống kê hoạt động
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <?php if (isset($user['login_count'])): ?>
                        <tr>
                            <td><strong>Số lần đăng nhập:</strong></td>
                            <td><?= number_format($user['login_count']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (isset($user['days_since_last_login'])): ?>
                        <tr>
                            <td><strong>Ngày từ lần đăng nhập cuối:</strong></td>
                            <td><?= $user['days_since_last_login'] !== null ? $user['days_since_last_login'] . ' ngày' : 'N/A' ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (isset($user['created_orders'])): ?>
                        <tr>
                            <td><strong>Đơn hàng tạo:</strong></td>
                            <td><?= number_format($user['created_orders']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Help Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>Hướng dẫn
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Mật khẩu:</strong> Để trống nếu không muốn thay đổi
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Avatar:</strong> Tải lên ảnh mới hoặc giữ nguyên
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Trạng thái:</strong> Admin không hoạt động không thể đăng nhập
                        </li>
                        <li>
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            <strong>Lưu ý:</strong> Không thể tự thay đổi trạng thái của chính mình
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editUserForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const confirmPasswordGroup = document.getElementById('confirmPasswordGroup');
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const previewImage = document.getElementById('previewImage');
    const statusSelect = document.getElementById('status');

    // Disable status change for own account
    <?php if ($user['id'] == $_SESSION['admin_id']): ?>
    statusSelect.addEventListener('change', function() {
        if (this.value !== 'active') {
            alert('Không thể thay đổi trạng thái tài khoản của chính mình');
            this.value = 'active';
        }
    });
    <?php endif; ?>

    // Show/hide confirm password field based on password input
    passwordInput.addEventListener('input', function() {
        if (this.value.trim()) {
            confirmPasswordGroup.style.display = 'block';
            confirmPasswordInput.required = true;
        } else {
            confirmPasswordGroup.style.display = 'none';
            confirmPasswordInput.required = false;
            confirmPasswordInput.value = '';
            confirmPasswordInput.classList.remove('is-invalid');
        }
    });

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        if (type === 'password') {
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        } else {
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        }
    });

    // Avatar preview
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('File quá lớn. Vui lòng chọn file nhỏ hơn 2MB.');
                avatarInput.value = '';
                avatarPreview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                avatarPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            avatarPreview.style.display = 'none';
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Check password match if password is being changed
        if (password.trim() && password !== confirmPassword) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp');
            confirmPasswordInput.focus();
            return;
        }

        // Check password length if password is being changed
        if (password.trim() && password.length < 8) {
            e.preventDefault();
            alert('Mật khẩu phải có ít nhất 8 ký tự');
            passwordInput.focus();
            return;
        }

        // Check username format
        const username = document.getElementById('username').value;
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            e.preventDefault();
            alert('Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới');
            document.getElementById('username').focus();
            return;
        }

        // Check phone format if provided
        const phone = document.getElementById('phone').value;
        if (phone && !/^[0-9]{10,11}$/.test(phone)) {
            e.preventDefault();
            alert('Số điện thoại không hợp lệ (10-11 chữ số)');
            document.getElementById('phone').focus();
            return;
        }
    });

    // Real-time password confirmation validation
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;

        if (confirmPassword && password !== confirmPassword) {
            this.setCustomValidity('Mật khẩu xác nhận không khớp');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Username format validation
    document.getElementById('username').addEventListener('input', function() {
        const username = this.value;
        if (username && !/^[a-zA-Z0-9_]+$/.test(username)) {
            this.setCustomValidity('Chỉ được chứa chữ cái, số và dấu gạch dưới');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
});
</script>
