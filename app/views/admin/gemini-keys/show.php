<?php
$title = $title ?? 'Chi tiết Gemini API Key - zone Fashion Admin';
$api_key = $api_key ?? [];
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chi tiết API Key: <?= htmlspecialchars($api_key['name'] ?? 'Unknown') ?></h1>
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
            <button type="button" class="btn btn-outline-info me-2" onclick="testApiKey(<?= $api_key['id'] ?>)">
                <i class="fas fa-heartbeat"></i> Test Key
            </button>
            <a href="/zone-fashion/admin/gemini-keys/<?= $api_key['id'] ?>/edit" class="btn btn-outline-warning me-2">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="/zone-fashion/admin/gemini-keys" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-gray-800 w-25">Tên:</td>
                                    <td><?= htmlspecialchars($api_key['name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-gray-800">Trạng thái:</td>
                                    <td>
                                        <?php 
                                        $statusClass = [
                                            'active' => 'success',
                                            'inactive' => 'warning', 
                                            'error' => 'danger'
                                        ][$api_key['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?> fs-6">
                                            <?= ucfirst($api_key['status'] ?? 'Unknown') ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-gray-800">API Key:</td>
                                    <td>
                                        <div class="input-group">
                                            <code class="api-key-display form-control border-0 bg-light" 
                                                  data-key="<?= htmlspecialchars($api_key['api_key'] ?? '') ?>">
                                                <?= htmlspecialchars(substr($api_key['api_key'] ?? '', 0, 20)) ?>...
                                            </code>
                                            <button class="btn btn-outline-secondary" onclick="toggleApiKey(this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info" onclick="copyApiKey()">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-gray-800">Tổng sử dụng:</td>
                                    <td><?= number_format($api_key['usage_count'] ?? 0) ?> lần</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-gray-800 w-25">Tạo bởi:</td>
                                    <td>
                                        <?= htmlspecialchars($api_key['creator_name'] ?? 'N/A') ?>
                                        <?php if ($api_key['creator_email']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($api_key['creator_email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-gray-800">Tạo lúc:</td>
                                    <td>
                                        <?php if ($api_key['created_at']): ?>
                                            <?= date('d/m/Y H:i:s', strtotime($api_key['created_at'])) ?>
                                            <br><small class="text-muted">
                                                <?php
                                                $created = new DateTime($api_key['created_at']);
                                                $now = new DateTime();
                                                $diff = $now->diff($created);
                                                echo $diff->format('%a ngày trước');
                                                ?>
                                            </small>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-gray-800">Cập nhật cuối:</td>
                                    <td>
                                        <?php if ($api_key['updated_at']): ?>
                                            <?= date('d/m/Y H:i:s', strtotime($api_key['updated_at'])) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-gray-800">Sử dụng cuối:</td>
                                    <td>
                                        <?php if ($api_key['last_used_at']): ?>
                                            <?= date('d/m/Y H:i:s', strtotime($api_key['last_used_at'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa sử dụng</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($api_key['notes']): ?>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold text-gray-800">Ghi chú:</h6>
                                <div class="bg-light p-3 rounded">
                                    <?= nl2br(htmlspecialchars($api_key['notes'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Usage Limits -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Giới hạn sử dụng</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Sử dụng hôm nay
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        <?= number_format($api_key['current_daily_usage'] ?? 0) ?>
                                                        <?php if (($api_key['daily_limit'] ?? 0) > 0): ?>
                                                            / <?= number_format($api_key['daily_limit']) ?>
                                                        <?php else: ?>
                                                            / ∞
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php if (($api_key['daily_limit'] ?? 0) > 0): ?>
                                                    <div class="col">
                                                        <div class="progress progress-sm mr-2">
                                                            <?php 
                                                            $dailyPercent = min(100, (($api_key['current_daily_usage'] ?? 0) / $api_key['daily_limit']) * 100);
                                                            $progressClass = $dailyPercent >= 90 ? 'bg-danger' : ($dailyPercent >= 70 ? 'bg-warning' : 'bg-info');
                                                            ?>
                                                            <div class="progress-bar <?= $progressClass ?>" 
                                                                 style="width: <?= $dailyPercent ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <span class="text-xs font-weight-bold text-gray-500">
                                                            <?= number_format($dailyPercent, 1) ?>%
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Sử dụng tháng này
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        <?= number_format($api_key['current_monthly_usage'] ?? 0) ?>
                                                        <?php if (($api_key['monthly_limit'] ?? 0) > 0): ?>
                                                            / <?= number_format($api_key['monthly_limit']) ?>
                                                        <?php else: ?>
                                                            / ∞
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php if (($api_key['monthly_limit'] ?? 0) > 0): ?>
                                                    <div class="col">
                                                        <div class="progress progress-sm mr-2">
                                                            <?php 
                                                            $monthlyPercent = min(100, (($api_key['current_monthly_usage'] ?? 0) / $api_key['monthly_limit']) * 100);
                                                            $progressClass = $monthlyPercent >= 90 ? 'bg-danger' : ($monthlyPercent >= 70 ? 'bg-warning' : 'bg-primary');
                                                            ?>
                                                            <div class="progress-bar <?= $progressClass ?>" 
                                                                 style="width: <?= $monthlyPercent ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <span class="text-xs font-weight-bold text-gray-500">
                                                            <?= number_format($monthlyPercent, 1) ?>%
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Status & Error Log -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Trạng thái Test & Lịch sử</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-2">
                                    Kết quả test cuối
                                </div>
                                <?php if ($api_key['last_test_at']): ?>
                                    <?php 
                                    $testClass = [
                                        'success' => 'success',
                                        'failed' => 'danger',
                                        'pending' => 'warning'
                                    ][$api_key['last_test_status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $testClass ?> fs-5 px-3 py-2">
                                        <?= ucfirst($api_key['last_test_status']) ?>
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i:s', strtotime($api_key['last_test_at'])) ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary fs-5 px-3 py-2">Chưa test</span>
                                    <div class="mt-2">
                                        <small class="text-muted">Key chưa được test</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-2">
                                    Health Status
                                </div>
                                <?php 
                                $healthStatus = $api_key['health_status'] ?? 'pending';
                                $healthClass = [
                                    'success' => 'success',
                                    'warning' => 'warning',
                                    'pending' => 'secondary'
                                ][$healthStatus] ?? 'secondary';
                                $healthIcon = [
                                    'success' => 'fa-heart',
                                    'warning' => 'fa-heart-broken',
                                    'pending' => 'fa-question-circle'
                                ][$healthStatus] ?? 'fa-question-circle';
                                ?>
                                <div class="text-<?= $healthClass ?> mb-2">
                                    <i class="fas <?= $healthIcon ?> fa-3x"></i>
                                </div>
                                <span class="badge bg-<?= $healthClass ?> fs-6">
                                    <?= ucfirst($healthStatus) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-info btn-lg" onclick="testApiKey(<?= $api_key['id'] ?>)">
                                    <i class="fas fa-heartbeat"></i><br>
                                    Test ngay
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted">Kiểm tra tình trạng key</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($api_key['last_error_message'])): ?>
                        <hr>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Lỗi cuối cùng:</h6>
                            <p class="mb-0"><?= htmlspecialchars($api_key['last_error_message']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info" onclick="testApiKey(<?= $api_key['id'] ?>)">
                            <i class="fas fa-heartbeat"></i> Test API Key
                        </button>
                        
                        <a href="/zone-fashion/admin/gemini-keys/<?= $api_key['id'] ?>/edit" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        
                        <?php if ($api_key['status'] === 'active'): ?>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleStatus('inactive')">
                                <i class="fas fa-pause"></i> Tạm dừng
                            </button>
                        <?php elseif ($api_key['status'] === 'inactive'): ?>
                            <button type="button" class="btn btn-outline-success" onclick="toggleStatus('active')">
                                <i class="fas fa-play"></i> Kích hoạt
                            </button>
                        <?php elseif ($api_key['status'] === 'error'): ?>
                            <button type="button" class="btn btn-outline-success" onclick="toggleStatus('active')">
                                <i class="fas fa-check"></i> Kích hoạt lại
                            </button>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="deleteKey()">
                            <i class="fas fa-trash"></i> Xóa API Key
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Thống kê
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h4 mb-0 font-weight-bold text-primary">
                                <?= number_format($api_key['usage_count'] ?? 0) ?>
                            </div>
                            <div class="text-xs text-gray-500">Tổng sử dụng</div>
                        </div>
                        <div class="col-6">
                            <?php 
                            $avgDaily = 0;
                            if ($api_key['created_at']) {
                                $created = new DateTime($api_key['created_at']);
                                $now = new DateTime();
                                $days = max(1, $now->diff($created)->days);
                                $avgDaily = round(($api_key['usage_count'] ?? 0) / $days, 1);
                            }
                            ?>
                            <div class="h4 mb-0 font-weight-bold text-info">
                                <?= number_format($avgDaily, 1) ?>
                            </div>
                            <div class="text-xs text-gray-500">Trung bình/ngày</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <div class="text-xs font-weight-bold text-gray-500 text-uppercase mb-2">
                                Độ tin cậy
                            </div>
                            <?php 
                            $reliability = 'Chưa xác định';
                            $reliabilityClass = 'secondary';
                            
                            if ($api_key['usage_count'] > 100) {
                                if ($api_key['last_test_status'] === 'success') {
                                    $reliability = 'Cao';
                                    $reliabilityClass = 'success';
                                } elseif ($api_key['last_test_status'] === 'failed') {
                                    $reliability = 'Thấp';
                                    $reliabilityClass = 'danger';
                                }
                            } elseif ($api_key['usage_count'] > 10) {
                                $reliability = 'Trung bình';
                                $reliabilityClass = 'warning';
                            }
                            ?>
                            <span class="badge bg-<?= $reliabilityClass ?> fs-6">
                                <?= $reliability ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-shield-alt"></i> Bảo mật
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý:</h6>
                        <ul class="mb-0">
                            <li>API key được mã hóa trong database</li>
                            <li>Chỉ admin có quyền xem key</li>
                            <li>Monitor usage thường xuyên</li>
                            <li>Báo ngay nếu phát hiện bất thường</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Toggle API key visibility
function toggleApiKey(button) {
    const codeElement = button.parentElement.querySelector('.api-key-display');
    const fullKey = codeElement.getAttribute('data-key');
    const isHidden = codeElement.textContent.includes('...');
    
    if (isHidden) {
        codeElement.textContent = fullKey;
        button.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        codeElement.textContent = fullKey.substr(0, 20) + '...';
        button.innerHTML = '<i class="fas fa-eye"></i>';
    }
}

// Copy API key to clipboard
function copyApiKey() {
    const codeElement = document.querySelector('.api-key-display');
    const fullKey = codeElement.getAttribute('data-key');
    
    navigator.clipboard.writeText(fullKey).then(function() {
        showAlert('success', 'API key đã được copy vào clipboard');
    }, function(err) {
        showAlert('error', 'Không thể copy API key: ' + err);
    });
}

// Test API key
async function testApiKey(keyId) {
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    
    try {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        const response = await fetch('/zone-fashion/admin/gemini-keys/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ key_id: keyId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Có lỗi xảy ra khi test API key: ' + error.message);
    } finally {
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}

// Toggle key status
function toggleStatus(newStatus) {
    const statusText = {
        'active': 'kích hoạt',
        'inactive': 'tạm dừng',
        'error': 'đánh dấu lỗi'
    };
    
    if (!confirm(`Bạn có chắc chắn muốn ${statusText[newStatus]} API key này?`)) return;
    
    // Redirect to edit page with status change
    window.location.href = `/zone-fashion/admin/gemini-keys/<?= $api_key['id'] ?>/edit?status=${newStatus}`;
}

// Delete key
async function deleteKey() {
    if (!confirm('Bạn có chắc chắn muốn xóa API key này? Thao tác này không thể hoàn tác!')) return;
    
    try {
        const response = await fetch('/zone-fashion/admin/gemini-keys/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ key_id: <?= $api_key['id'] ?? 0 ?> })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => {
                window.location.href = '/zone-fashion/admin/gemini-keys';
            }, 1000);
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Có lỗi xảy ra: ' + error.message);
    }
}

// Show alert message
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Add new alert
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Scroll to top
    window.scrollTo(0, 0);
}
</script>