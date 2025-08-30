<?php
$title = $title ?? 'Analytics Dashboard';
$overview = $overview ?? [];
$reviews = $reviews ?? [];
$products = $products ?? [];
$customers = $customers ?? [];
$charts = $charts ?? [];
$trending = $trending ?? [];$period = $period ?? '30';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-analytics me-2"></i>Analytics Dashboard
        </h1>

        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="periodFilter" onchange="changePeriod(this.value)">
                <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 ngày qua</option>
                <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 ngày qua</option>
                <option value="90" <?= $period == '90' ? 'selected' : '' ?>>3 tháng qua</option>
                <option value="365" <?= $period == '365' ? 'selected' : '' ?>>1 năm qua</option>
            </select>
            <a href="/zone-fashion/admin/analytics/reports" class="btn btn-primary btn-sm">
                <i class="fas fa-chart-line me-1"></i>Chi tiết
            </a>
            <button class="btn btn-success btn-sm" onclick="exportData()">
                <i class="fas fa-download me-1"></i>Xuất dữ liệu
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng doanh thu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['total_revenue'] ?? 0) ?> ₫
                            </div>
                            <div class="small text-success">
                                <i class="fas fa-arrow-<?= ($overview['revenue_growth'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i>
                                <?= number_format(abs($overview['revenue_growth'] ?? 0), 1) ?>% hôm nay
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Sản phẩm
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['total_products'] ?? 0) ?>
                            </div>
                            <div class="small text-muted">
                                Đang bán
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Khách hàng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['total_customers'] ?? 0) ?>
                            </div>
                            <div class="small text-info">
                                <i class="fas fa-arrow-<?= ($overview['customer_growth'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i>
                                <?= number_format(abs($overview['customer_growth'] ?? 0), 1) ?>% tuần này
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đánh giá TB
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($overview['average_rating'] ?? 0, 1) ?>/5.0
                            </div>
                            <div class="small text-warning">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa<?= $i <= round($overview['average_rating'] ?? 0) ? 's' : 'r' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-1"><?= number_format($sales['total_orders'] ?? 0) ?></div>
                        <div class="small">Tổng đơn hàng</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-1"><?= number_format($sales['completed_orders'] ?? 0) ?></div>
                        <div class="small">Đã hoàn thành</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-1"><?= number_format($sales['avg_order_value'] ?? 0) ?> ₫</div>
                        <div class="small">Giá trị TB/đơn</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-1"><?= number_format($sales['conversion_rate'] ?? 0, 2) ?>%</div>
                        <div class="small">Tỷ lệ chuyển đổi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Revenue Trend -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu theo ngày</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="switchChart('revenue')">Doanh thu</a>
                            <a class="dropdown-item" href="#" onclick="switchChart('orders')">Đơn hàng</a>
                            <a class="dropdown-item" href="#" onclick="switchChart('rating')">Rating trung bình</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="mainChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sản phẩm bán chạy</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($sales['best_sellers'])): ?>
                        <?php foreach ($sales['best_sellers'] as $index => $product): ?>
                            <div class="d-flex align-items-center border-bottom pb-2 mb-2">
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-pill"><?= $index + 1 ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= htmlspecialchars($product['name']) ?></h6>
                                    <small class="text-muted">Đã bán: <?= number_format($product['total_sold']) ?> sản phẩm</small>
                                </div>
                                <div class="text-end">
                                    <strong><?= number_format($product['revenue'] ?? 0) ?> ₫</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Chưa có dữ liệu sản phẩm bán chạy</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sentiment Analysis & Top Reviewers Row -->
    <div class="row mb-4">
        <!-- Sentiment Analysis -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân tích cảm xúc khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-3">
                            <i class="fas fa-circle text-success"></i> Tích cực (<?= $reviews['sentiment']['positive'] ?? 0 ?>)
                        </span>
                        <span class="mr-3">
                            <i class="fas fa-circle text-warning"></i> Trung tính (<?= $reviews['sentiment']['neutral'] ?? 0 ?>)
                        </span>
                        <span class="mr-3">
                            <i class="fas fa-circle text-danger"></i> Tiêu cực (<?= $reviews['sentiment']['negative'] ?? 0 ?>)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Reviewers -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-medal me-2"></i>Top Reviewers
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($customers['top_reviewers'])): ?>
                        <?php foreach ($customers['top_reviewers'] as $index => $reviewer): ?>
                            <div class="d-flex align-items-center mb-3 <?= $index < 4 ? 'border-bottom pb-3' : '' ?>">
                                <div class="me-3">
                                    <div class="rounded-circle bg-<?= $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'primary') ?> d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <span class="text-white fw-bold"><?= $index + 1 ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?= htmlspecialchars($reviewer['full_name']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($reviewer['email']) ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary"><?= $reviewer['review_count'] ?></div>
                                    <div class="small text-muted">
                                        <?= number_format($reviewer['avg_rating'], 1) ?> <i class="fas fa-star text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-medal fa-2x mb-2"></i>
                            <p>Chưa có reviewer nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Hoạt động gần đây
                    </h6>
                    <a href="/zone-fashion/admin/reviews" class="btn btn-outline-primary btn-sm">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($trending['recent_activities'])): ?>
                        <div class="timeline">
                            <?php foreach (array_slice($trending['recent_activities'], 0, 10) as $activity): ?>
                                <div class="d-flex mb-3">
                                    <div class="me-3 pt-1">
                                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-star text-white small"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small">
                                            <strong><?= htmlspecialchars($activity['user_name']) ?></strong>
                                            đã đánh giá
                                            <strong><?= htmlspecialchars($activity['product_name']) ?></strong>
                                            <span class="ms-2">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fa<?= $i <= $activity['rating'] ? 's' : 'r' ?> fa-star text-warning" style="font-size: 12px;"></i>
                                                <?php endfor; ?>
                                            </span>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-3x mb-3"></i>
                            <p>Chưa có hoạt động gần đây</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart data from PHP
const reviewsData = <?= json_encode($charts['reviews_trend'] ?? []) ?>;
const ratingData = <?= json_encode($charts['rating_trend'] ?? []) ?>;
const revenueData = <?= json_encode($sales['revenue_by_day'] ?? []) ?>;
const ordersData = <?= json_encode($sales['orders_by_day'] ?? []) ?>;

// Main Chart (Revenue/Orders/Rating Trend)
const mainCtx = document.getElementById('mainChart').getContext('2d');
let currentChart = 'revenue';

const mainChart = new Chart(mainCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(item => item.date),
        datasets: [{
            label: 'Doanh thu (₫)',
            data: revenueData.map(item => parseFloat(item.revenue)),
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                },
                ticks: {
                    callback: function(value, index, values) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (currentChart === 'revenue') {
                            label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                        } else {
                            label += context.parsed.y;
                        }
                        return label;
                    }
                }
            }
        }
    }
});

// Sentiment Chart
const sentimentCtx = document.getElementById('sentimentChart').getContext('2d');
const sentimentChart = new Chart(sentimentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Tích cực', 'Trung tính', 'Tiêu cực'],
        datasets: [{
            data: [
                <?= $reviews['sentiment']['positive'] ?? 0 ?>,
                <?= $reviews['sentiment']['neutral'] ?? 0 ?>,
                <?= $reviews['sentiment']['negative'] ?? 0 ?>
            ],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            borderWidth: 0,
            cutout: '60%'
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

// Functions
function changePeriod(period) {
    window.location.href = '?period=' + period;
}

function switchChart(type) {
    if (type === 'revenue' && currentChart !== 'revenue') {
        mainChart.data.labels = revenueData.map(item => item.date);
        mainChart.data.datasets[0].label = 'Doanh thu (₫)';
        mainChart.data.datasets[0].data = revenueData.map(item => parseFloat(item.revenue));
        mainChart.data.datasets[0].borderColor = '#1cc88a';
        mainChart.data.datasets[0].backgroundColor = 'rgba(28, 200, 138, 0.1)';
        currentChart = 'revenue';
    } else if (type === 'orders' && currentChart !== 'orders') {
        mainChart.data.labels = ordersData.map(item => item.date);
        mainChart.data.datasets[0].label = 'Số đơn hàng';
        mainChart.data.datasets[0].data = ordersData.map(item => parseInt(item.orders));
        mainChart.data.datasets[0].borderColor = '#36b9cc';
        mainChart.data.datasets[0].backgroundColor = 'rgba(54, 185, 204, 0.1)';
        currentChart = 'orders';
    } else if (type === 'rating' && currentChart !== 'rating') {
        mainChart.data.labels = ratingData.map(item => item.date);
        mainChart.data.datasets[0].label = 'Rating trung bình';
        mainChart.data.datasets[0].data = ratingData.map(item => parseFloat(item.avg_rating));
        mainChart.data.datasets[0].borderColor = '#f6c23e';
        mainChart.data.datasets[0].backgroundColor = 'rgba(246, 194, 62, 0.1)';
        currentChart = 'rating';
    }
    mainChart.update();
}

function exportData() {
    // Implement export functionality
    const data = {
        period: <?= json_encode($period) ?>,
        overview: <?= json_encode($overview) ?>,
        sales: <?= json_encode($sales) ?>,
        reviews: <?= json_encode($reviews) ?>,
        timestamp: new Date().toISOString()
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `zone-fashion-analytics-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Auto refresh every 5 minutes
setInterval(() => {
    location.reload();
}, 300000);
</script>

<style>
.timeline {
    max-height: 400px;
    overflow-y: auto;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
