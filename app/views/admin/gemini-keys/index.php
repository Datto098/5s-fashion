<?php
$title = $title ?? 'Quản lý Gemini API Keys - zone Fashion Admin';
$api_keys = $api_keys ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$filters = $filters ?? [];
$pagination = $pagination ?? [];
$error = $error ?? '';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Quản lý Gemini API Keys</h1>
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
            <button type="button" class="btn btn-outline-info me-2" onclick="testAllKeys()">
                <i class="fas fa-heartbeat"></i> Test All Keys
            </button>
            <a href="/zone-fashion/admin/gemini-keys/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm API Key
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

    <?php if (isset($_GET['error']) || $error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error'] ?? $error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số Keys
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_keys'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
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
                                Keys Hoạt Động
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['active_keys'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Keys Lỗi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['error_keys'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Sử dụng hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_usage_today'] ?? 0) ?>
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

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc và Tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Tên hoặc ghi chú...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Tạm dừng</option>
                        <option value="error" <?= $filters['status'] === 'error' ? 'selected' : '' ?>>Lỗi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="test_status" class="form-label">Kết quả test</label>
                    <select class="form-select" id="test_status" name="test_status">
                        <option value="">Tất cả</option>
                        <option value="success" <?= $filters['test_status'] === 'success' ? 'selected' : '' ?>>Thành công</option>
                        <option value="failed" <?= $filters['test_status'] === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                        <option value="pending" <?= $filters['test_status'] === 'pending' ? 'selected' : '' ?>>Chưa test</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="/zone-fashion/admin/gemini-keys" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- API Keys Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách API Keys</h6>
            <div>
                <button class="btn btn-outline-warning btn-sm me-2" onclick="resetUsage('daily')">
                    <i class="fas fa-redo"></i> Reset Daily
                </button>
                <button class="btn btn-outline-info btn-sm" onclick="resetUsage('monthly')">
                    <i class="fas fa-calendar"></i> Reset Monthly
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($api_keys)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-key fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Chưa có API key nào. <a href="/zone-fashion/admin/gemini-keys/create">Thêm API key đầu tiên</a></p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>API Key</th>
                                <th>Trạng thái</th>
                                <th>Sử dụng hôm nay</th>
                                <th>Sử dụng tháng này</th>
                                <th>Test cuối</th>
                                <th>Lần dùng cuối</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($api_keys as $key): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($key['name']) ?></strong>
                                        <?php if ($key['notes']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($key['notes'], 0, 50)) ?><?= strlen($key['notes']) > 50 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="api-key-display" data-key="<?= htmlspecialchars($key['api_key']) ?>">
                                            <?= htmlspecialchars(substr($key['api_key'], 0, 20)) ?>...
                                        </code>
                                        <button class="btn btn-sm btn-outline-secondary ms-1" onclick="toggleApiKey(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = [
                                            'active' => 'success',
                                            'inactive' => 'warning', 
                                            'error' => 'danger'
                                        ][$key['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>">
                                            <?= ucfirst($key['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span><?= number_format($key['current_daily_usage']) ?></span>
                                            <?php if ($key['daily_limit'] > 0): ?>
                                                <span class="text-muted">/ <?= number_format($key['daily_limit']) ?></span>
                                                <div class="progress ms-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar" style="width: <?= min($key['daily_usage_percent'], 100) ?>%"></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span><?= number_format($key['current_monthly_usage']) ?></span>
                                            <?php if ($key['monthly_limit'] > 0): ?>
                                                <span class="text-muted">/ <?= number_format($key['monthly_limit']) ?></span>
                                                <div class="progress ms-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar" style="width: <?= min($key['monthly_usage_percent'], 100) ?>%"></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($key['last_test_at']): ?>
                                            <?php 
                                            $testClass = [
                                                'success' => 'success',
                                                'failed' => 'danger',
                                                'pending' => 'warning'
                                            ][$key['last_test_status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $testClass ?> mb-1">
                                                <?= ucfirst($key['last_test_status']) ?>
                                            </span>
                                            <br><small class="text-muted">
                                                <?= date('d/m H:i', strtotime($key['last_test_at'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Chưa test</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($key['last_used_at']): ?>
                                            <small><?= date('d/m/Y H:i', strtotime($key['last_used_at'])) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Chưa sử dụng</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="testApiKey(<?= $key['id'] ?>)" title="Test API Key">
                                                <i class="fas fa-heartbeat"></i>
                                            </button>
                                            <a href="/zone-fashion/admin/gemini-keys/<?= $key['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/zone-fashion/admin/gemini-keys/<?= $key['id'] ?>/edit" 
                                               class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteApiKey(<?= $key['id'] ?>, '<?= htmlspecialchars($key['name'], ENT_QUOTES) ?>')" 
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

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filters['status']) ?>&test_status=<?= urlencode($filters['test_status']) ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filters['status']) ?>&test_status=<?= urlencode($filters['test_status']) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filters['status']) ?>&test_status=<?= urlencode($filters['test_status']) ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Toggle API key visibility
function toggleApiKey(button) {
    const codeElement = button.previousElementSibling;
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

// Test single API key
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
            setTimeout(() => location.reload(), 1000);
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

// Test all API keys
async function testAllKeys() {
    if (!confirm('Bạn có chắc chắn muốn test tất cả API keys?')) return;
    
    const button = event.target;
    const originalHtml = button.innerHTML;
    
    try {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
        
        const response = await fetch('/zone-fashion/admin/gemini-keys/test-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Có lỗi xảy ra: ' + error.message);
    } finally {
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}

// Reset usage counters
async function resetUsage(type) {
    if (!confirm(`Bạn có chắc chắn muốn reset ${type} usage counters?`)) return;
    
    try {
        const response = await fetch('/zone-fashion/admin/gemini-keys/reset-usage', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ type: type })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('error', result.message);
        }
    } catch (error) {
        showAlert('error', 'Có lỗi xảy ra: ' + error.message);
    }
}

// Delete API key
async function deleteApiKey(keyId, keyName) {
    if (!confirm(`Bạn có chắc chắn muốn xóa API key "${keyName}"?`)) return;
    
    try {
        const response = await fetch('/zone-fashion/admin/gemini-keys/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ key_id: keyId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => location.reload(), 1000);
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