<?php
$title = $title ?? 'Tạo tài khoản Admin - zone Fashion Admin';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tạo tài khoản Admin</h1>
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
            <a href="/zone-fashion/admin/users" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin tài khoản</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/zone-fashion/admin/users/create" enctype="multipart/form-data" id="createUserForm">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required
                                           placeholder="Nhập tên đăng nhập">
                                    <div class="form-text">Chỉ được chứa chữ cái, số và dấu gạch dưới. Tối thiểu 3 ký tự.</div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           placeholder="Nhập địa chỉ email">
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required
                                               placeholder="Nhập mật khẩu">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Tối thiểu 8 ký tự</div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                           placeholder="Nhập lại mật khẩu">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Full Name -->
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required
                                           placeholder="Nhập họ và tên">
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           placeholder="Nhập số điện thoại">
                                    <div class="form-text">10-11 chữ số</div>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" selected>Hoạt động</option>
                                        <option value="inactive">Không hoạt động</option>
                                    </select>
                                </div>

                                <!-- Avatar -->
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Avatar</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar"
                                           accept="image/jpeg,image/png,image/gif">
                                    <div class="form-text">Định dạng: JPG, PNG, GIF. Tối đa 2MB.</div>
                                </div>

                                <!-- Avatar Preview -->
                                <div class="mb-3" id="avatarPreview" style="display: none;">
                                    <label class="form-label">Xem trước</label>
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
                                <i class="fas fa-save me-2"></i>Tạo tài khoản
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Hướng dẫn
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Tên đăng nhập:</strong> Duy nhất, không thay đổi sau khi tạo
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Email:</strong> Dùng để đăng nhập và nhận thông báo
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Mật khẩu:</strong> Tối thiểu 8 ký tự, nên có chữ hoa, chữ thường, số
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Trạng thái:</strong> Admin không hoạt động không thể đăng nhập
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Avatar:</strong> Không bắt buộc, sẽ hiển thị chữ cái đầu nếu không có
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Bảo mật
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý bảo mật:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Sử dụng mật khẩu mạnh</li>
                                <li>Không chia sẻ thông tin đăng nhập</li>
                                <li>Thay đổi mật khẩu định kỳ</li>
                                <li>Vô hiệu hóa tài khoản khi không sử dụng</li>
                            </ul>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const previewImage = document.getElementById('previewImage');

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

        // Check password match
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp');
            confirmPasswordInput.focus();
            return;
        }

        // Check password length
        if (password.length < 8) {
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
