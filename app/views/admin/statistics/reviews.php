<?php
$title = $title ?? 'Thống kê đánh giá';
$stats = $stats ?? [];
$charts = $charts ?? [];
$topProducts = $topProducts ?? [];
$recentReviews = $recentReviews ?? [];
$period = $period ?? '30';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-star me-2"></i>Thống kê đánh giá chi tiết
        </h1>

        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="periodFilter" onchange="changePeriod(this.value)">
                <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 ngày qua</option>
                <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 ngày qua</option>
                <option value="90" <?= $period == '90' ? 'selected' : '' ?>>3 tháng qua</option>
                <option value="365" <?= $period == '365' ? 'selected' : '' ?>>1 năm qua</option>
            </select>
            <a href="/zone-fashion/admin/statistics" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Top Products by Reviews -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Sản phẩm được đánh giá cao nhất
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($topProducts)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hạng</th>
                                        <th>Sản phẩm</th>
                                        <th>Số đánh giá</th>
                                        <th>Điểm trung bình</th>
                                        <th>Xếp hạng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $index => $product): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : ($index == 2 ? 'dark' : 'primary'));
                                                ?>">
                                                    #<?= $index + 1 ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($product['featured_image']): ?>
                                                        <?php
                                                        // Handle image path for file server
                                                        $imagePath = $product['featured_image'];
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
                                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                                             class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="me-2 bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $product['review_count'] ?></span>
                                            </td>
                                            <td>
                                                <strong><?= number_format($product['avg_rating'], 1) ?></strong>/5
                                            </td>
                                            <td>
                                                <?php
                                                $rating = $product['avg_rating'];
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $rating) {
                                                        echo '<i class="fas fa-star text-warning"></i>';
                                                    } elseif ($i - 0.5 <= $rating) {
                                                        echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star text-muted"></i>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-star fa-3x mb-3"></i>
                            <p>Chưa có đánh giá nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Đánh giá gần đây
                    </h6>
                    <a href="/zone-fashion/admin/reviews" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentReviews)): ?>
                        <?php foreach ($recentReviews as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start">
                                            <?php if ($review['product_image']): ?>
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
                                                     class="me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="me-3 bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= htmlspecialchars($review['title'] ?? 'Không có tiêu đề') ?></h6>
                                                <p class="mb-2 text-muted small">
                                                    <?= htmlspecialchars(substr($review['content'] ?? '', 0, 100)) ?><?= strlen($review['content'] ?? '') > 100 ? '...' : '' ?>
                                                </p>
                                                <div class="small text-muted">
                                                    <i class="fas fa-box me-1"></i><?= htmlspecialchars($review['product_name']) ?>
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-user me-1"></i><?= htmlspecialchars($review['customer_name']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa<?= $i <= $review['rating'] ? 's' : 'r' ?> fa-star text-warning"></i>
                                            <?php endfor; ?>
                                            <span class="ms-1 small text-muted"><?= $review['rating'] ?>/5</span>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <?= date('d/m/Y H:i', strtotime($review['created_at'])) ?>
                                        </div>
                                        <span class="badge bg-<?php
                                            echo $review['status'] == 'approved' ? 'success' :
                                                ($review['status'] == 'pending' ? 'warning' : 'danger');
                                        ?>">
                                            <?php
                                            echo $review['status'] == 'approved' ? 'Đã duyệt' :
                                                ($review['status'] == 'pending' ? 'Chờ duyệt' : 'Từ chối');
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p>Chưa có đánh giá nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Review Statistics -->
    <?php if (!empty($stats['product_stats'])): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Thống kê đánh giá theo sản phẩm (<?= $period ?> ngày qua)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số đánh giá</th>
                                    <th>Điểm trung bình</th>
                                    <th>Xu hướng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['product_stats'] as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= $product['review_count'] ?></span>
                                        </td>
                                        <td>
                                            <strong><?= number_format($product['avg_rating'], 1) ?></strong>/5
                                        </td>
                                        <td>
                                            <?php
                                            $rating = $product['avg_rating'];
                                            if ($rating >= 4.5) {
                                                echo '<i class="fas fa-arrow-up text-success"></i> Rất tốt';
                                            } elseif ($rating >= 3.5) {
                                                echo '<i class="fas fa-arrow-right text-warning"></i> Ổn';
                                            } else {
                                                echo '<i class="fas fa-arrow-down text-danger"></i> Cần cải thiện';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function changePeriod(period) {
    window.location.href = '?period=' + period;
}
</script>
