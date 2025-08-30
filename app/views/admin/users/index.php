<?php
$title = $title ?? 'Quản lý Admin - zone Fashion Admin';
$users = $users ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$filters = $filters ?? [];
$error = $error ?? '';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Quản lý Admin</h1>
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
            <a href="/zone-fashion/admin/users/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tạo Admin mới
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php if (!empty($stats)): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Tổng Admin</h6>
                            <h3 class="mb-0"><?= number_format($stats['total_admins'] ?? 0) ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users-cog fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Đang hoạt động</h6>
                            <h3 class="mb-0"><?= number_format($stats['active_admins'] ?? 0) ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Không hoạt động</h6>
                            <h3 class="mb-0"><?= number_format($stats['inactive_admins'] ?? 0) ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-times fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Đăng nhập gần đây</h6>
                            <h3 class="mb-0"><?= number_format($stats['recent_logins'] ?? 0) ?></h3>
                            <small>30 ngày qua</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-sign-in-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Alert Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) || $error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($_GET['error'] ?? $error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/zone-fashion/admin/users" class="row g-4">
                <div class="col-lg-4 col-md-6 mb-2">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?= htmlspecialchars($search) ?>"
                           placeholder="Tên, email, username...">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                        <option value="banned" <?= ($filters['status'] ?? '') === 'banned' ? 'selected' : '' ?>>Bị cấm</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                    <label for="sort" class="form-label">Sắp xếp</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="created_at" <?= ($filters['sort'] ?? '') === 'created_at' ? 'selected' : '' ?>>Ngày tạo</option>
                        <option value="full_name" <?= ($filters['sort'] ?? '') === 'full_name' ? 'selected' : '' ?>>Tên</option>
                        <option value="email" <?= ($filters['sort'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                        <option value="status" <?= ($filters['sort'] ?? '') === 'status' ? 'selected' : '' ?>>Trạng thái</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                    <label for="order" class="form-label">Thứ tự</label>
                    <select class="form-select" id="order" name="order">
                        <option value="DESC" <?= ($filters['order'] ?? '') === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                        <option value="ASC" <?= ($filters['order'] ?? '') === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6 d-flex align-items-end mb-2">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                        <a href="/zone-fashion/admin/users" class="btn btn-outline-secondary" title="Đặt lại">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Danh sách Admin (<?= count($users) ?> admin)</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($users)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có admin nào</h5>
                    <p class="text-muted">Hãy tạo tài khoản admin đầu tiên</p>
                    <a href="/zone-fashion/admin/users/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tạo Admin mới
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Avatar</th>
                                <th>Thông tin admin</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Đăng nhập cuối</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="avatar-sm">
                                            <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                                                <img src="/zone-fashion/<?= htmlspecialchars($user['avatar']) ?>"
                                                     alt="<?= htmlspecialchars($user['full_name']) ?>"
                                                     class="rounded-circle" width="40" height="40">
                                            <?php else: ?>
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($user['full_name']) ?></h6>
                                            <small class="text-muted d-block">@<?= htmlspecialchars($user['username']) ?></small>
                                            <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            <?php if (!empty($user['phone'])): ?>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-phone fa-xs me-1"></i><?= htmlspecialchars($user['phone']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($user['status']) {
                                                case 'active':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Hoạt động';
                                                    break;
                                                case 'inactive':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Không hoạt động';
                                                    break;
                                                case 'banned':
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Bị cấm';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Không xác định';
                                            }
                                            ?>
                                            <button class="btn btn-sm <?= $statusClass ?> text-white dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <?= $statusText ?>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item status-change" href="#"
                                                       data-user-id="<?= $user['id'] ?>" data-status="active">
                                                    <i class="fas fa-check text-success me-2"></i>Hoạt động
                                                </a></li>
                                                <li><a class="dropdown-item status-change" href="#"
                                                       data-user-id="<?= $user['id'] ?>" data-status="inactive">
                                                    <i class="fas fa-pause text-warning me-2"></i>Không hoạt động
                                                </a></li>
                                                <li><a class="dropdown-item status-change" href="#"
                                                       data-user-id="<?= $user['id'] ?>" data-status="banned">
                                                    <i class="fas fa-ban text-danger me-2"></i>Cấm
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if (isset($user['last_login']) && $user['last_login']): ?>
                                            <small><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Chưa đăng nhập</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-user"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                                <button class="btn btn-outline-danger delete-user"
                                                        data-user-id="<?= $user['id'] ?>"
                                                        data-user-name="<?= htmlspecialchars($user['full_name']) ?>"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa admin <strong id="delete-user-name"></strong>?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="user_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUsername" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editUsername" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="editPassword" name="password"
                                       placeholder="Để trống nếu không thay đổi">
                                <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFullName" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editFullName" name="full_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="editPhone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="editPhone" name="phone">
                            </div>

                            <div class="mb-3">
                                <label for="editStatus" class="form-label">Trạng thái</label>
                                <select class="form-select" id="editStatus" name="status">
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Không hoạt động</option>
                                    <option value="banned">Bị cấm</option>
                                </select>
                                <div id="selfStatusWarning" class="form-text text-warning d-none">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Không thể thay đổi trạng thái tài khoản của chính mình
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="saveEditUser">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Move modals to body to avoid layout conflicts
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    if (editModal && editModal.parentNode !== document.body) {
        document.body.appendChild(editModal);
    }
    if (deleteModal && deleteModal.parentNode !== document.body) {
        document.body.appendChild(deleteModal);
    }

    let deleteUserId = null;
    let editUserId = null;

    // Handle status change
    document.querySelectorAll('.status-change').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            const userId = this.getAttribute('data-user-id');
            const status = this.getAttribute('data-status');

            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái admin này?')) {
                updateUserStatus(userId, status);
            }
        });
    });

    // Handle edit user
    document.querySelectorAll('.edit-user').forEach(function(element) {
        element.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            loadUserDataForEdit(userId);
        });
    });

    // Handle delete user
    document.querySelectorAll('.delete-user').forEach(function(element) {
        element.addEventListener('click', function() {
            deleteUserId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');

            document.getElementById('delete-user-name').textContent = userName;

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });

    // Confirm delete
    document.getElementById('confirm-delete').addEventListener('click', function() {
        if (deleteUserId) {
            deleteUser(deleteUserId);
        }
    });

    // Save edit user
    document.getElementById('saveEditUser').addEventListener('click', function() {
        if (editUserId) {
            saveUserEdit(editUserId);
        }
    });

    // Handle status change for self in edit modal
    document.getElementById('editStatus').addEventListener('change', function() {
        const currentAdminId = <?= $_SESSION['admin_id'] ?? 0 ?>;
        const selfStatusWarning = document.getElementById('selfStatusWarning');

        if (editUserId == currentAdminId && this.value !== 'active') {
            selfStatusWarning.classList.remove('d-none');
            this.value = 'active';
        } else {
            selfStatusWarning.classList.add('d-none');
        }
    });

    function loadUserDataForEdit(userId) {
        // Find user data from current table
        const userRow = document.querySelector(`[data-user-id="${userId}"]`).closest('tr');
        const cells = userRow.querySelectorAll('td');

        // Extract user data from table cells
        const fullNameElement = cells[1].querySelector('h6');
        const usernameElement = cells[1].querySelector('small:first-of-type');
        const emailElement = cells[1].querySelector('small:nth-of-type(2)');
        const phoneElement = cells[1].querySelector('small:nth-of-type(3)');
        const statusButton = cells[2].querySelector('button');

        const fullName = fullNameElement ? fullNameElement.textContent.trim() : '';
        const username = usernameElement ? usernameElement.textContent.replace('@', '').trim() : '';
        const email = emailElement ? emailElement.textContent.trim() : '';
        const phone = phoneElement ? phoneElement.textContent.replace(/.*?(\d+)/, '$1').trim() : '';

        // Get status from button class
        let status = 'active';
        if (statusButton.classList.contains('bg-warning')) {
            status = 'inactive';
        } else if (statusButton.classList.contains('bg-danger')) {
            status = 'banned';
        }

        // Populate modal
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editEmail').value = email;
        document.getElementById('editFullName').value = fullName;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editStatus').value = status;
        document.getElementById('editPassword').value = '';

        editUserId = userId;

        // Show/hide self status warning
        const currentAdminId = <?= $_SESSION['admin_id'] ?? 0 ?>;
        const selfStatusWarning = document.getElementById('selfStatusWarning');
        if (userId == currentAdminId) {
            selfStatusWarning.classList.remove('d-none');
        } else {
            selfStatusWarning.classList.add('d-none');
        }

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById('editModal'), {
            backdrop: 'static',
            keyboard: true,
            focus: true
        });

        // Force center the modal
        const modalElement = document.getElementById('editModal');
        modalElement.addEventListener('shown.bs.modal', function () {
            // Ensure modal is centered
            this.style.display = 'flex';
            this.style.alignItems = 'center';
            this.style.justifyContent = 'center';
        });

        editModal.show();
    }

    function saveUserEdit(userId) {
        const form = document.getElementById('editUserForm');
        const formData = new FormData(form);

        // Convert to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        fetch(`/zone-fashion/admin/users/${userId}/edit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                editModal.hide();
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể cập nhật thông tin admin'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật thông tin admin');
        });
    }

    function updateUserStatus(userId, status) {
        fetch('/zone-fashion/admin/users/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể cập nhật trạng thái'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
        });
    }

    function deleteUser(userId) {
        fetch('/zone-fashion/admin/users/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            deleteModal.hide();

            if (data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa admin'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa admin');
        });
    }
});
</script>
