<?php
$title = $title ?? 'Chi tiết Admin - 5S Fashion Admin';
$user = $user ?? [];
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chi tiết Admin: <?= htmlspecialchars($user['full_name'] ?? '') ?></h1>
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
            <a href="/5s-fashion/admin/users/<?= $user['id'] ?>/edit" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Chỉnh sửa
            </a>
            <a href="/5s-fashion/admin/users" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="mb-3">
                        <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                            <img src="/5s-fashion/<?= htmlspecialchars($user['avatar']) ?>"
                                 alt="<?= htmlspecialchars($user['full_name']) ?>"
                                 class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 120px; height: 120px; font-size: 3rem;">
                                <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Name and Status -->
                    <h4 class="card-title"><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="text-muted mb-2">@<?= htmlspecialchars($user['username']) ?></p>

                    <!-- Status Badge -->
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
                    <span class="badge <?= $statusClass ?> mb-3"><?= $statusText ?></span>

                    <!-- Contact Info -->
                    <div class="text-start">
                        <p class="mb-2">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a>
                        </p>
                        <?php if (!empty($user['phone'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <a href="tel:<?= htmlspecialchars($user['phone']) ?>"><?= htmlspecialchars($user['phone']) ?></a>
                        </p>
                        <?php endif; ?>
                        <p class="mb-2">
                            <i class="fas fa-user-shield text-muted me-2"></i>
                            <?= ucfirst($user['role']) ?>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mt-3">
                        <a href="/5s-fashion/admin/users/<?= $user['id'] ?>/edit" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
                        </a>

                        <!-- Status Change Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-2"></i>Thay đổi trạng thái
                            </button>
                            <ul class="dropdown-menu w-100">
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

                        <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                        <button class="btn btn-outline-danger delete-user"
                                data-user-id="<?= $user['id'] ?>"
                                data-user-name="<?= htmlspecialchars($user['full_name']) ?>">
                            <i class="fas fa-trash me-2"></i>Xóa tài khoản
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="col-lg-8">
            <!-- Account Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông tin tài khoản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">ID:</td>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tên đăng nhập:</td>
                                    <td>@<?= htmlspecialchars($user['username']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Họ và tên:</td>
                                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Số điện thoại:</td>
                                    <td>
                                        <?php if (!empty($user['phone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($user['phone']) ?>">
                                                <?= htmlspecialchars($user['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa cập nhật</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Vai trò:</td>
                                    <td>
                                        <span class="badge bg-info"><?= ucfirst($user['role']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Trạng thái:</td>
                                    <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Ngày tạo:</td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Cập nhật cuối:</td>
                                    <td>
                                        <?= isset($user['updated_at']) ? date('d/m/Y H:i', strtotime($user['updated_at'])) : 'N/A' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Đăng nhập cuối:</td>
                                    <td>
                                        <?php if (isset($user['last_login']) && $user['last_login']): ?>
                                            <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                            <?php if (isset($user['days_since_last_login']) && $user['days_since_last_login'] !== null): ?>
                                                <small class="text-muted">(<?= $user['days_since_last_login'] ?> ngày trước)</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa đăng nhập</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Thống kê hoạt động
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1"><?= number_format($user['login_count'] ?? 0) ?></h4>
                                <small class="text-muted">Lần đăng nhập</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1"><?= number_format($user['created_orders'] ?? 0) ?></h4>
                                <small class="text-muted">Đơn hàng tạo</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1">
                                    <?php
                                    if (isset($user['days_since_last_login']) && $user['days_since_last_login'] !== null) {
                                        echo $user['days_since_last_login'];
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </h4>
                                <small class="text-muted">Ngày từ lần đăng nhập cuối</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Bảo mật tài khoản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Mật khẩu</h6>
                            <p class="text-muted mb-3">
                                <i class="fas fa-lock me-2"></i>
                                Mật khẩu được mã hóa an toàn
                            </p>
                            <a href="/5s-fashion/admin/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h6>Quyền truy cập</h6>
                            <p class="text-muted mb-3">
                                <i class="fas fa-user-shield me-2"></i>
                                Quyền quản trị viên (Admin)
                            </p>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Quyền truy cập đầy đủ vào hệ thống</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteUserId = null;

    // Handle status change
    document.querySelectorAll('.status-change').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            const userId = this.getAttribute('data-user-id');
            const status = this.getAttribute('data-status');

            // Prevent self-deactivation
            <?php if ($user['id'] == $_SESSION['admin_id']): ?>
            if (status !== 'active') {
                alert('Không thể thay đổi trạng thái tài khoản của chính mình');
                return;
            }
            <?php endif; ?>

            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái admin này?')) {
                updateUserStatus(userId, status);
            }
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

    function updateUserStatus(userId, status) {
        fetch('/5s-fashion/admin/users/update-status', {
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
        fetch('/5s-fashion/admin/users/delete', {
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
                window.location.href = '/5s-fashion/admin/users?success=' + encodeURIComponent(data.message);
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
