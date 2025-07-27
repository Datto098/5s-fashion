<?php
$title = $title ?? 'Thống kê tổng quan';
$overview = $overview ?? [];
$reviews = $reviews ?? [];
$products = $products ?? [];
$customers = $customers ?? [];
$charts = $charts ?? [];
$period = $period ?? '30';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-area me-2"></i>Thống kê tổng quan
        </h1>

        <!-- Time Period Filter -->
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="periodFilter" onchange="changePeriod(this.value)">
                <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 ngày qua</option>
                <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 ngày qua</option>
                <option value="90" <?= $period == '90' ? 'selected' : '' ?>>3 tháng qua</option>
                <option value="365" <?= $period == '365' ? 'selected' : '' ?>>1 năm qua</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="exportStats()">
                <i class="fas fa-download me-1"></i>Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- Overview Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng sản phẩm
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['total_products'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng khách hàng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['total_customers'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng đánh giá
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['total_reviews'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Điểm trung bình
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['average_rating'] ?? 0, 1) ?>/5
                                <i class="fas fa-star text-warning"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Reviews Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ đánh giá theo ngày</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="reviewsChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Status Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Trạng thái đánh giá</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="reviewStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Đã duyệt (<?= $reviews['approved'] ?? 0 ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Chờ duyệt (<?= $reviews['pending'] ?? 0 ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Từ chối (<?= $reviews['rejected'] ?? 0 ?>)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Row -->
    <div class="row">
        <!-- Products Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4 text-center">
                            <div class="mb-2">
                                <div class="h4 text-success mb-0"><?= $products['published'] ?? 0 ?></div>
                                <div class="small text-muted">Đã xuất bản</div>
                            </div>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="mb-2">
                                <div class="h4 text-warning mb-0"><?= $products['draft'] ?? 0 ?></div>
                                <div class="small text-muted">Bản nháp</div>
                            </div>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="mb-2">
                                <div class="h4 text-danger mb-0"><?= $products['out_of_stock'] ?? 0 ?></div>
                                <div class="small text-muted">Hết hàng</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/5s-fashion/admin/products" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bố đánh giá</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($reviews['rating_distribution'])): ?>
                        <?php foreach ($reviews['rating_distribution'] as $rating): ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2 fw-bold"><?= $rating['rating'] ?> <i class="fas fa-star text-warning"></i></div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 20px;">
                                        <?php
                                        $percentage = $reviews['total'] > 0 ? ($rating['count'] / $reviews['total']) * 100 : 0;
                                        ?>
                                        <div class="progress-bar bg-warning" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                                <div class="ms-2 small text-muted"><?= $rating['count'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Chưa có đánh giá nào</p>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="/5s-fashion/admin/reviews" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem đánh giá
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hoạt động hôm nay</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-star fa-2x text-info"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0"><?= $overview['today_reviews'] ?? 0 ?></div>
                                    <div class="small text-muted">Đánh giá mới</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-user-plus fa-2x text-success"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0"><?= $overview['today_customers'] ?? 0 ?></div>
                                    <div class="small text-muted">Khách hàng mới</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Reviews Line Chart
const reviewsCtx = document.getElementById('reviewsChart').getContext('2d');
const reviewsChart = new Chart(reviewsCtx, {
    type: 'line',
    data: {
        labels: [<?php
            if (!empty($charts['reviews_by_day'])) {
                echo "'" . implode("','", array_column($charts['reviews_by_day'], 'date')) . "'";
            }
        ?>],
        datasets: [{
            label: 'Đánh giá',
            data: [<?php
                if (!empty($charts['reviews_by_day'])) {
                    echo implode(',', array_column($charts['reviews_by_day'], 'count'));
                }
            ?>],
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Review Status Pie Chart
const statusCtx = document.getElementById('reviewStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
        datasets: [{
            data: [
                <?= $reviews['approved'] ?? 0 ?>,
                <?= $reviews['pending'] ?? 0 ?>,
                <?= $reviews['rejected'] ?? 0 ?>
            ],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function changePeriod(period) {
    window.location.href = '?period=' + period;
}

function exportStats() {
    // Implement export functionality
    alert('Chức năng xuất báo cáo sẽ được triển khai sau!');
}
</script>
