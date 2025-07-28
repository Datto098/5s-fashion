<?php
// Admin Coupons Index View
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-ticket-alt text-primary me-2"></i>
                        Quản lý Voucher
                    </h1>
                    <p class="text-muted mb-0">Quản lý toàn bộ voucher trong hệ thống</p>
                </div>
                <div>
                    <a href="/5s-fashion/admin/coupons/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Thêm Voucher
                    </a>
                    <button class="btn btn-outline-secondary" onclick="exportCoupons()">
                        <i class="fas fa-download me-2"></i>
                        Xuất Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Statistics Cards -->
    <?php if (isset($stats) && $stats): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $stats['total_coupons'] ?? 0 ?></h4>
                            <p class="mb-0">Tổng voucher</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-ticket-alt fa-2x"></i>
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
                            <h4 class="mb-0"><?= $stats['active_coupons'] ?? 0 ?></h4>
                            <p class="mb-0">Đang hoạt động</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="mb-0"><?= number_format($stats['total_usage'] ?? 0) ?></h4>
                            <p class="mb-0">Lượt sử dụng</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                            <h4 class="mb-0"><?= number_format($stats['total_savings'] ?? 0) ?>đ</h4>
                            <p class="mb-0">Tổng tiết kiệm</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-piggy-bank fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Tìm theo mã, tên voucher..."
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm dừng</option>
                        <option value="expired" <?= ($filters['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Loại</label>
                    <select name="type" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="percentage" <?= ($filters['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Phần trăm</option>
                        <option value="fixed_amount" <?= ($filters['type'] ?? '') === 'fixed_amount' ? 'selected' : '' ?>>Số tiền cố định</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control"
                           value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control"
                           value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

                <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($_GET['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Bulk Actions -->
                    <form id="bulkForm" method="POST" action="/5s-fashion/admin/coupons/bulk-action">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <select name="bulk_action" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Chọn hành động</option>
                                    <option value="activate">Kích hoạt</option>
                                    <option value="deactivate">Tạm dừng</option>
                                    <option value="delete">Xóa</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirmBulkAction()">Áp dụng</button>
                            </div>
                            <div class="text-muted">
                                Tổng: <?= $totalCoupons ?> voucher
                            </div>
                        </div>

                        <!-- Coupons Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="30"><input type="checkbox" id="selectAll"></th>
                                        <th>Code</th>
                                        <th>Tên</th>
                                        <th>Loại</th>
                                        <th>Giá trị</th>
                                        <th>Đã sử dụng</th>
                                        <th>Hạn sử dụng</th>
                                        <th>Trạng thái</th>
                                        <th width="120">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($coupons)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Chưa có voucher nào</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($coupons as $coupon): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="coupon_ids[]" value="<?= $coupon['id'] ?>" class="coupon-checkbox">
                                                </td>
                                                <td>
                                                    <strong class="text-primary"><?= htmlspecialchars($coupon['code']) ?></strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($coupon['name']) ?></strong>
                                                        <?php if ($coupon['description']): ?>
                                                            <br><small class="text-muted"><?= htmlspecialchars($coupon['description']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $coupon['type'] === 'percentage' ? 'primary' : 'info' ?>">
                                                        <?= $coupon['type'] === 'percentage' ? 'Phần trăm' : 'Cố định' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?php if ($coupon['type'] === 'percentage'): ?>
                                                            <?= $coupon['value'] ?>%
                                                        <?php else: ?>
                                                            <?= number_format($coupon['value']) ?>đ
                                                        <?php endif; ?>
                                                    </strong>
                                                    <?php if ($coupon['minimum_amount']): ?>
                                                        <br><small class="text-muted">Tối thiểu: <?= number_format($coupon['minimum_amount']) ?>đ</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-secondary"><?= $coupon['used_count'] ?></span>
                                                    <?php if ($coupon['usage_limit']): ?>
                                                        / <?= $coupon['usage_limit'] ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($coupon['valid_until']): ?>
                                                        <?= date('d/m/Y', strtotime($coupon['valid_until'])) ?>
                                                        <?php if (strtotime($coupon['valid_until']) < time()): ?>
                                                            <br><small class="text-danger">Đã hết hạn</small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Không giới hạn</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'active' => 'success',
                                                        'inactive' => 'warning',
                                                        'expired' => 'danger'
                                                    ];
                                                    $statusText = [
                                                        'active' => 'Hoạt động',
                                                        'inactive' => 'Tạm dừng',
                                                        'expired' => 'Hết hạn'
                                                    ];
                                                    ?>
                                                    <span class="badge badge-<?= $statusClass[$coupon['status']] ?>">
                                                        <?= $statusText[$coupon['status']] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="/5s-fashion/admin/coupons/<?= $coupon['id'] ?>"
                                                           class="btn btn-info btn-sm" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="/5s-fashion/admin/coupons/<?= $coupon['id'] ?>/edit"
                                                           class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($coupon['used_count'] == 0): ?>
                                                            <button onclick="deleteCoupon(<?= $coupon['id'] ?>)"
                                                                    class="btn btn-danger btn-sm" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                    <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page ?>&<?= http_build_query($filters) ?>">
                                            <?= $page ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<script>
// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.coupon-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Confirm bulk action
function confirmBulkAction() {
    const selectedCoupons = document.querySelectorAll('.coupon-checkbox:checked');
    const action = document.querySelector('select[name="bulk_action"]').value;

    if (selectedCoupons.length === 0) {
        alert('Vui lòng chọn ít nhất một voucher');
        return false;
    }

    if (!action) {
        alert('Vui lòng chọn hành động');
        return false;
    }

    const actionText = {
        'activate': 'kích hoạt',
        'deactivate': 'tạm dừng',
        'delete': 'xóa'
    };

    return confirm(`Bạn có chắc muốn ${actionText[action]} ${selectedCoupons.length} voucher đã chọn?`);
}

// Delete single coupon
function deleteCoupon(id) {
    if (confirm('Bạn có chắc muốn xóa voucher này?')) {
        window.location.href = `/5s-fashion/admin/coupons/${id}/delete`;
    }
}

// Export coupons
function exportCoupons() {
    const params = new URLSearchParams(window.location.search);
    params.set('format', 'csv');
    window.location.href = `/5s-fashion/admin/coupons/export?${params.toString()}`;
}

// Mark expired coupons
function markExpiredCoupons() {
    if (confirm('Bạn có chắc muốn đánh dấu tất cả voucher hết hạn?')) {
        window.location.href = '/5s-fashion/admin/coupons/mark-expired';
    }
}
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-group .btn {
    margin-right: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75em;
}

.text-muted {
    color: #6c757d !important;
}
</style>
