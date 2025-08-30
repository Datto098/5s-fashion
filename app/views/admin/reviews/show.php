<?php
$title = $title ?? 'Chi tiết Đánh giá - zone Fashion Admin';
$review = $review ?? null;
?>

<div class="container-fluid py-4">
    <?php if (!$review): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            Không tìm thấy đánh giá
        </div>
        <a href="/zone-fashion/admin/reviews" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    <?php else: ?>
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Chi tiết Đánh giá #<?= $review['id'] ?></h1>
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
                <a href="/zone-fashion/admin/reviews" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Review Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin đánh giá</h5>
                    </div>
                    <div class="card-body">
                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Đánh giá</label>
                            <div class="d-flex align-items-center">
                                <span class="h4 me-3 mb-0"><?= $review['rating'] ?>/5</span>
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i <= $review['rating'] ? ' text-warning' : ' text-muted' ?> fa-lg"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <?php if (!empty($review['title'])): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tiêu đề</label>
                            <p class="mb-0"><?= htmlspecialchars($review['title']) ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Content -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nội dung đánh giá</label>
                            <div class="bg-light p-3 rounded">
                                <?= nl2br(htmlspecialchars($review['content'] ?? '')) ?>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <div>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($review['status']) {
                                    case 'approved':
                                        $statusClass = 'bg-success';
                                        $statusText = 'Đã duyệt';
                                        break;
                                    case 'pending':
                                        $statusClass = 'bg-warning';
                                        $statusText = 'Chờ duyệt';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Từ chối';
                                        break;
                                    default:
                                        $statusClass = 'bg-secondary';
                                        $statusText = 'Không xác định';
                                }
                                ?>
                                <span class="badge <?= $statusClass ?> fs-6"><?= $statusText ?></span>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày tạo</label>
                                <p class="mb-0"><?= date('d/m/Y H:i:s', strtotime($review['created_at'])) ?></p>
                            </div>
                            <?php if (!empty($review['updated_at'])): ?>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cập nhật lần cuối</label>
                                <p class="mb-0"><?= date('d/m/Y H:i:s', strtotime($review['updated_at'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Thông tin sản phẩm</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($review['product_image'])): ?>
                        <div class="text-center mb-3">
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
                            $imageUrl = '/zone-fashion/serve-file.php?file=' . urlencode($cleanPath);
                            ?>
                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                 alt="<?= htmlspecialchars($review['product_name']) ?>"
                                 class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                        <?php endif; ?>

                        <h6 class="mb-2"><?= htmlspecialchars($review['product_name'] ?? 'Sản phẩm không tồn tại') ?></h6>

                        <?php if (!empty($review['product_price'])): ?>
                        <p class="text-primary fw-bold mb-2">
                            <?= number_format($review['product_price']) ?> VNĐ
                        </p>
                        <?php endif; ?>

                        <small class="text-muted">ID sản phẩm: #<?= $review['product_id'] ?></small>

                        <?php if (!empty($review['product_slug'])): ?>
                        <div class="mt-2">
                            <a href="/zone-fashion/products/<?= htmlspecialchars($review['product_slug']) ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Xem sản phẩm
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Thông tin khách hàng</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($review['customer_avatar'])): ?>
                        <div class="text-center mb-3">
                            <img src="/zone-fashion/<?= htmlspecialchars($review['customer_avatar']) ?>"
                                 alt="<?= htmlspecialchars($review['customer_name']) ?>"
                                 class="rounded-circle" width="80" height="80">
                        </div>
                        <?php else: ?>
                        <div class="text-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px; font-size: 24px;">
                                <?= strtoupper(substr($review['customer_name'] ?? 'U', 0, 1)) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <h6 class="text-center mb-2"><?= htmlspecialchars($review['customer_name'] ?? 'Khách hàng đã xóa') ?></h6>

                        <?php if (!empty($review['customer_email'])): ?>
                        <div class="mb-2">
                            <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($review['customer_email']) ?></small>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($review['customer_phone'])): ?>
                        <div class="mb-2">
                            <small class="text-muted d-block"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($review['customer_phone']) ?></small>
                        </div>
                        <?php endif; ?>

                        <small class="text-muted">ID khách hàng: #<?= $review['user_id'] ?></small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Thao tác</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <!-- Status Actions -->
                            <?php if ($review['status'] !== 'approved'): ?>
                            <button class="btn btn-success status-change"
                                    data-review-id="<?= $review['id'] ?>"
                                    data-status="approved">
                                <i class="fas fa-check me-1"></i>Duyệt đánh giá
                            </button>
                            <?php endif; ?>

                            <?php if ($review['status'] !== 'pending'): ?>
                            <button class="btn btn-warning status-change"
                                    data-review-id="<?= $review['id'] ?>"
                                    data-status="pending">
                                <i class="fas fa-clock me-1"></i>Chờ duyệt
                            </button>
                            <?php endif; ?>

                            <?php if ($review['status'] !== 'rejected'): ?>
                            <button class="btn btn-outline-danger status-change"
                                    data-review-id="<?= $review['id'] ?>"
                                    data-status="rejected">
                                <i class="fas fa-times me-1"></i>Từ chối
                            </button>
                            <?php endif; ?>

                            <hr>

                            <!-- Delete Action -->
                            <button class="btn btn-danger delete-review"
                                    data-review-id="<?= $review['id'] ?>"
                                    data-product-name="<?= htmlspecialchars($review['product_name'] ?? '') ?>">
                                <i class="fas fa-trash me-1"></i>Xóa đánh giá
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
                <p>Bạn có chắc chắn muốn xóa đánh giá này?</p>
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
    // Move modal to body to avoid layout conflicts
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal && deleteModal.parentNode !== document.body) {
        document.body.appendChild(deleteModal);
    }

    let deleteReviewId = null;

    // Handle status change
    document.querySelectorAll('.status-change').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            const reviewId = this.getAttribute('data-review-id');
            const status = this.getAttribute('data-status');

            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái đánh giá này?')) {
                updateReviewStatus(reviewId, status);
            }
        });
    });

    // Handle delete review
    document.querySelectorAll('.delete-review').forEach(function(element) {
        element.addEventListener('click', function() {
            deleteReviewId = this.getAttribute('data-review-id');

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });

    // Confirm delete
    document.getElementById('confirm-delete').addEventListener('click', function() {
        if (deleteReviewId) {
            deleteReview(deleteReviewId);
        }
    });

    function updateReviewStatus(reviewId, status) {
        fetch('/zone-fashion/admin/reviews/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                review_id: reviewId,
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

    function deleteReview(reviewId) {
        fetch('/zone-fashion/admin/reviews/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                review_id: reviewId
            })
        })
        .then(response => response.json())
        .then(data => {
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            deleteModal.hide();

            if (data.success) {
                // Redirect to reviews list after successful deletion
                window.location.href = '/zone-fashion/admin/reviews?success=' + encodeURIComponent('Xóa đánh giá thành công');
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa đánh giá'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa đánh giá');
        });
    }
});
</script>
