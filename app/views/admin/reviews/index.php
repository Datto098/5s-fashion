<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        Quản lý đánh giá
                    </h1>
                    <p class="text-muted mb-0">Quản lý và kiểm duyệt đánh giá sản phẩm</p>
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
                            <h4 class="mb-0"><?= $stats['total_reviews'] ?? 0 ?></h4>
                            <p class="mb-0">Tổng đánh giá</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x"></i>
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
                            <h4 class="mb-0"><?= number_format($stats['average_rating'] ?? 0, 1) ?></h4>
                            <p class="mb-0">Điểm trung bình</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="mb-0"><?= $stats['pending_reviews'] ?? 0 ?></h4>
                            <p class="mb-0">Chờ duyệt</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="mb-0"><?= $stats['approved_reviews'] ?? 0 ?></h4>
                            <p class="mb-0">Đã duyệt</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check fa-2x"></i>
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
                           placeholder="Tìm theo nội dung, sản phẩm..."
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đánh giá</label>
                    <select name="rating" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="5" <?= ($filters['rating'] ?? '') === '5' ? 'selected' : '' ?>>5 sao</option>
                        <option value="4" <?= ($filters['rating'] ?? '') === '4' ? 'selected' : '' ?>>4 sao</option>
                        <option value="3" <?= ($filters['rating'] ?? '') === '3' ? 'selected' : '' ?>>3 sao</option>
                        <option value="2" <?= ($filters['rating'] ?? '') === '2' ? 'selected' : '' ?>>2 sao</option>
                        <option value="1" <?= ($filters['rating'] ?? '') === '1' ? 'selected' : '' ?>>1 sao</option>
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
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Danh sách đánh giá
            </h5>
            <div>
                <button class="btn btn-success btn-sm" onclick="bulkAction('approve')" id="bulkApproveBtn" disabled>
                    <i class="fas fa-check"></i> Duyệt
                </button>
                <button class="btn btn-warning btn-sm" onclick="bulkAction('reject')" id="bulkRejectBtn" disabled>
                    <i class="fas fa-times"></i> Từ chối
                </button>
                <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')" id="bulkDeleteBtn" disabled>
                    <i class="fas fa-trash"></i> Xóa
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($reviews)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Sản phẩm</th>
                                <th>Khách hàng</th>
                                <th>Đánh giá</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="review-checkbox" value="<?= $review['id'] ?>" onchange="updateBulkButtons()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($review['product_image'])): ?>
                                                <?php
                                                // Handle image path for file server
                                                $imagePath = $review['product_image'];
                                                if (strpos($imagePath, '/uploads/') === 0) {
                                                    $cleanPath = substr($imagePath, 9);
                                                } elseif (strpos($imagePath, 'uploads/') === 0) {
                                                    $cleanPath = substr($imagePath, 8);
                                                } else {
                                                    $cleanPath = ltrim($imagePath, '/');
                                                }
                                                $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                                ?>
                                                <img src="<?= htmlspecialchars($imageUrl) ?>"
                                                     alt="Product" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($review['product_name'] ?? 'N/A') ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($review['customer_name'] ?? 'N/A') ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($review['customer_email'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                            <br>
                                            <small class="text-muted"><?= $review['rating'] ?>/5</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px;">
                                            <?php if (!empty($review['title'])): ?>
                                                <strong><?= htmlspecialchars($review['title']) ?></strong><br>
                                            <?php endif; ?>
                                            <span class="text-muted">
                                                <?= htmlspecialchars(substr($review['content'] ?? '', 0, 100)) ?>
                                                <?= strlen($review['content'] ?? '') > 100 ? '...' : '' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'pending' => ['class' => 'warning', 'text' => 'Chờ duyệt'],
                                            'approved' => ['class' => 'success', 'text' => 'Đã duyệt'],
                                            'rejected' => ['class' => 'danger', 'text' => 'Từ chối']
                                        ];
                                        $status = $statusMap[$review['status']] ?? ['class' => 'secondary', 'text' => 'Không xác định'];
                                        ?>
                                        <span class="badge bg-<?= $status['class'] ?>">
                                            <?= $status['text'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($review['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/5s-fashion/admin/reviews/<?= $review['id'] ?>"
                                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($review['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-success"
                                                        onclick="updateStatus(<?= $review['id'] ?>, 'approved')"
                                                        title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning"
                                                        onclick="updateStatus(<?= $review['id'] ?>, 'rejected')"
                                                        title="Từ chối">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="deleteReview(<?= $review['id'] ?>)"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h4>Chưa có đánh giá nào</h4>
                    <p class="text-muted">Hiện tại chưa có đánh giá nào trong hệ thống.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateStatus(reviewId, status) {
    const statusText = status === 'approved' ? 'duyệt' : 'từ chối';
    if (confirm(`Bạn có chắc muốn ${statusText} đánh giá này?`)) {
        fetch('/5s-fashion/admin/reviews/update-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ review_id: reviewId, status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể cập nhật'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
        });
    }
}

function deleteReview(reviewId) {
    if (confirm('Bạn có chắc muốn xóa đánh giá này? Hành động này không thể hoàn tác.')) {
        fetch('/5s-fashion/admin/reviews/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ review_id: reviewId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể xóa'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa đánh giá');
        });
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    updateBulkButtons();
}

function updateBulkButtons() {
    const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
    const bulkButtons = ['bulkApproveBtn', 'bulkRejectBtn', 'bulkDeleteBtn'];

    bulkButtons.forEach(btnId => {
        document.getElementById(btnId).disabled = checkedBoxes.length === 0;
    });
}

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.review-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một đánh giá');
        return;
    }

    const reviewIds = Array.from(checkedBoxes).map(cb => cb.value);
    const actionText = {
        'approve': 'duyệt',
        'reject': 'từ chối',
        'delete': 'xóa'
    };

    if (confirm(`Bạn có chắc muốn ${actionText[action]} ${reviewIds.length} đánh giá đã chọn?`)) {
        fetch('/5s-fashion/admin/reviews/bulk-action', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, review_ids: reviewIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể thực hiện'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thực hiện hành động');
        });
    }
}
</script>

<style>
.rating .fa-star {
    font-size: 14px;
}

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
</style>
