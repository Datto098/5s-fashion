<?php
$title = $title ?? 'Báo cáo chi tiết';
$type = $type ?? 'overview';
$period = $period ?? '30';
$data = $data ?? [];
$error = $error ?? null;
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line me-2"></i>Báo cáo chi tiết
        </h1>

        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="periodFilter" onchange="changePeriod(this.value)">
                <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 ngày qua</option>
                <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 ngày qua</option>
                <option value="90" <?= $period == '90' ? 'selected' : '' ?>>3 tháng qua</option>
                <option value="365" <?= $period == '365' ? 'selected' : '' ?>>1 năm qua</option>
            </select>
            <a href="/zone-fashion/admin/analytics" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
            <button class="btn btn-success btn-sm" onclick="exportReport()">
                <i class="fas fa-download me-1"></i>Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $type == 'overview' ? 'active' : '' ?>" 
                            onclick="changeReportType('overview')"
                            type="button" role="tab">
                        <i class="fas fa-chart-bar me-1"></i>Tổng quan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $type == 'reviews' ? 'active' : '' ?>" 
                            onclick="changeReportType('reviews')"
                            type="button" role="tab">
                        <i class="fas fa-star me-1"></i>Đánh giá
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $type == 'products' ? 'active' : '' ?>" 
                            onclick="changeReportType('products')"
                            type="button" role="tab">
                        <i class="fas fa-box me-1"></i>Sản phẩm
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $type == 'customers' ? 'active' : '' ?>" 
                            onclick="changeReportType('customers')"
                            type="button" role="tab">
                        <i class="fas fa-users me-1"></i>Khách hàng
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <?php if ($error): ?>
                <!-- Error Message -->
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php else: ?>
                <!-- Report Content -->
                <div class="tab-content" id="reportTabContent">
                    <?php switch($type): 
                        case 'overview': ?>
                            <!-- Overview Report -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-4">Báo cáo tổng quan - <?= $period ?> ngày qua</h5>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <div class="card border-left-primary">
                                                <div class="card-body py-3">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Tổng đơn hàng
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?= number_format($data['total_orders'] ?? 0) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-left-success">
                                                <div class="card-body py-3">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Doanh thu
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?= number_format($data['total_revenue'] ?? 0) ?> ₫
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-left-info">
                                                <div class="card-body py-3">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        Khách hàng mới
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?= number_format($data['new_customers'] ?? 0) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-left-warning">
                                                <div class="card-body py-3">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                        Đánh giá trung bình
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?= number_format($data['avg_rating'] ?? 0, 1) ?>/5
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>Tóm tắt báo cáo</h6>
                                        <p class="mb-0">
                                            Trong <?= $period ?> ngày qua, hệ thống đã ghi nhận được các dữ liệu tổng quan về hoạt động kinh doanh. 
                                            Để xem chi tiết hơn, vui lòng chọn tab báo cáo cụ thể bên trên.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php break; 
                        
                        case 'reviews': ?>
                            <!-- Reviews Report -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-4">Báo cáo đánh giá - <?= $period ?> ngày qua</h5>
                                    
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-star me-2"></i>Báo cáo đánh giá chi tiết</h6>
                                        <p class="mb-0">
                                            Báo cáo này sẽ hiển thị thông tin chi tiết về các đánh giá từ khách hàng, 
                                            bao gồm điểm số trung bình, phân tích xu hướng và feedback quan trọng.
                                        </p>
                                        <small class="text-muted">Tính năng đang được phát triển...</small>
                                    </div>
                                </div>
                            </div>
                            <?php break; 
                        
                        case 'products': ?>
                            <!-- Products Report -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-4">Báo cáo sản phẩm - <?= $period ?> ngày qua</h5>
                                    
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-box me-2"></i>Báo cáo sản phẩm chi tiết</h6>
                                        <p class="mb-0">
                                            Báo cáo này bao gồm thông tin về sản phẩm bán chạy nhất, tồn kho, 
                                            và hiệu suất bán hàng của từng danh mục sản phẩm.
                                        </p>
                                        <small class="text-muted">Tính năng đang được phát triển...</small>
                                    </div>
                                </div>
                            </div>
                            <?php break; 
                        
                        case 'customers': ?>
                            <!-- Customers Report -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-4">Báo cáo khách hàng - <?= $period ?> ngày qua</h5>
                                    
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-users me-2"></i>Báo cáo khách hàng chi tiết</h6>
                                        <p class="mb-0">
                                            Báo cáo này phân tích hành vi khách hàng, tỷ lệ retention, 
                                            customer lifetime value và segmentation khách hàng.
                                        </p>
                                        <small class="text-muted">Tính năng đang được phát triển...</small>
                                    </div>
                                </div>
                            </div>
                            <?php break; 
                        
                        default: ?>
                            <!-- Default View -->
                            <div class="alert alert-secondary">
                                <h6><i class="fas fa-chart-line me-2"></i>Chọn loại báo cáo</h6>
                                <p class="mb-0">Vui lòng chọn một tab báo cáo ở trên để xem thông tin chi tiết.</p>
                            </div>
                            <?php break; 
                    endswitch; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Additional Reports Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Báo cáo nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="?type=overview&period=<?= $period ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar text-primary me-2"></i>
                            Tổng quan kinh doanh
                        </a>
                        <a href="?type=products&period=<?= $period ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-box text-info me-2"></i>
                            Top sản phẩm bán chạy
                        </a>
                        <a href="?type=customers&period=<?= $period ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-users text-success me-2"></i>
                            Phân tích khách hàng
                        </a>
                        <a href="?type=reviews&period=<?= $period ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-star text-warning me-2"></i>
                            Đánh giá và feedback
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Xuất dữ liệu</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Xuất báo cáo dưới định dạng phù hợp:</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>Xuất PDF
                        </button>
                        <button class="btn btn-outline-success" onclick="exportReport('excel')">
                            <i class="fas fa-file-excel me-2"></i>Xuất Excel
                        </button>
                        <button class="btn btn-outline-info" onclick="exportReport('csv')">
                            <i class="fas fa-file-csv me-2"></i>Xuất CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changePeriod(period) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('period', period);
    window.location.href = currentUrl.toString();
}

function changeReportType(type) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('type', type);
    window.location.href = currentUrl.toString();
}

function exportReport(format = 'pdf') {
    // Show loading
    const originalText = event.target.innerHTML;
    event.target.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xuất...';
    event.target.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        // Reset button
        event.target.innerHTML = originalText;
        event.target.disabled = false;
        
        // Show success message
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                Báo cáo đã được xuất thành công dưới định dạng ${format.toUpperCase()}!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) alert.remove();
        }, 3000);
        
    }, 2000);
}

// Auto-refresh functionality
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        location.reload();
    }, 300000); // Refresh every 5 minutes
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Initialize auto-refresh
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Stop auto-refresh when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        startAutoRefresh();
    }
});
</script>

<style>
.border-left-primary {
    border-left: .25rem solid #4e73df!important;
}

.border-left-success {
    border-left: .25rem solid #1cc88a!important;
}

.border-left-info {
    border-left: .25rem solid #36b9cc!important;
}

.border-left-warning {
    border-left: .25rem solid #f6c23e!important;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
    background-color: transparent;
    border-bottom-color: #4e73df;
    color: #4e73df;
    font-weight: 600;
}

.card {
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1)!important;
}

.list-group-item-action:hover {
    background-color: #f8f9fc;
}

.alert {
    border: none;
    border-radius: 0.375rem;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}
</style>