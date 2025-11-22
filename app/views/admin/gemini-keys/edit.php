<?php
$title = $title ?? 'Chỉnh sửa Gemini API Key - zone Fashion Admin';
$api_key = $api_key ?? [];
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chỉnh sửa API Key: <?= htmlspecialchars($api_key['name'] ?? 'Unknown') ?></h1>
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
            <a href="/zone-fashion/admin/gemini-keys/<?= $api_key['id'] ?>" class="btn btn-outline-info me-2">
                <i class="fas fa-eye"></i> Xem chi tiết
            </a>
            <a href="/zone-fashion/admin/gemini-keys" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin API Key</h6>
                </div>
                <div class="card-body">
                    <form method="POST" id="editKeyForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Tên API Key <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           required placeholder="VD: Gemini Production Key"
                                           value="<?= htmlspecialchars($_POST['name'] ?? $api_key['name'] ?? '') ?>">
                                    <div class="form-text">Tên gợi nhớ để phân biệt các key</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <?php $currentStatus = $_POST['status'] ?? $api_key['status'] ?? 'active'; ?>
                                        <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Tạm dừng</option>
                                        <option value="error" <?= $currentStatus === 'error' ? 'selected' : '' ?>>Lỗi</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="api_key" class="form-label">
                                API Key <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="api_key" name="api_key" 
                                       required placeholder="AIzaSy..."
                                       value="<?= htmlspecialchars($_POST['api_key'] ?? $api_key['api_key'] ?? '') ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleApiKeyVisibility()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="testApiKeyInput()">
                                    <i class="fas fa-heartbeat"></i> Test
                                </button>
                            </div>
                            <div class="form-text">
                                API key từ Google AI Studio. Thay đổi key sẽ trigger test tự động.
                            </div>
                            <div id="testResult" class="mt-2"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="daily_limit" class="form-label">Giới hạn ngày</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="daily_limit" name="daily_limit" 
                                               min="0" value="<?= htmlspecialchars($_POST['daily_limit'] ?? $api_key['daily_limit'] ?? '1000') ?>">
                                        <span class="input-group-text">requests</span>
                                    </div>
                                    <div class="form-text">Để 0 nếu không giới hạn</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monthly_limit" class="form-label">Giới hạn tháng</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="monthly_limit" name="monthly_limit" 
                                               min="0" value="<?= htmlspecialchars($_POST['monthly_limit'] ?? $api_key['monthly_limit'] ?? '30000') ?>">
                                        <span class="input-group-text">requests</span>
                                    </div>
                                    <div class="form-text">Để 0 nếu không giới hạn</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú về key này, mục đích sử dụng..."><?= htmlspecialchars($_POST['notes'] ?? $api_key['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/zone-fashion/admin/gemini-keys" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="fas fa-save"></i> Cập nhật API Key
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Key Status -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Trạng thái hiện tại
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Trạng thái
                            </div>
                            <?php 
                            $statusClass = [
                                'active' => 'success',
                                'inactive' => 'warning', 
                                'error' => 'danger'
                            ][$api_key['status']] ?? 'secondary';
                            ?>
                            <div class="h6 mb-0">
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst($api_key['status'] ?? 'Unknown') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Test cuối
                            </div>
                            <?php if ($api_key['last_test_at']): ?>
                                <?php 
                                $testClass = [
                                    'success' => 'success',
                                    'failed' => 'danger',
                                    'pending' => 'warning'
                                ][$api_key['last_test_status']] ?? 'secondary';
                                ?>
                                <div class="h6 mb-0">
                                    <span class="badge bg-<?= $testClass ?>">
                                        <?= ucfirst($api_key['last_test_status']) ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($api_key['last_test_at'])) ?>
                                </small>
                            <?php else: ?>
                                <div class="h6 mb-0">
                                    <span class="badge bg-secondary">Chưa test</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Dùng hôm nay
                            </div>
                            <div class="h6 mb-0">
                                <?= number_format($api_key['current_daily_usage'] ?? 0) ?>
                                <?php if (($api_key['daily_limit'] ?? 0) > 0): ?>
                                    / <?= number_format($api_key['daily_limit']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Dùng tháng này
                            </div>
                            <div class="h6 mb-0">
                                <?= number_format($api_key['current_monthly_usage'] ?? 0) ?>
                                <?php if (($api_key['monthly_limit'] ?? 0) > 0): ?>
                                    / <?= number_format($api_key['monthly_limit']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($api_key['last_used_at']): ?>
                        <hr>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Sử dụng cuối
                        </div>
                        <div class="h6 mb-0">
                            <?= date('d/m/Y H:i:s', strtotime($api_key['last_used_at'])) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($api_key['creator_name']): ?>
                        <hr>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Người tạo
                        </div>
                        <div class="h6 mb-0"><?= htmlspecialchars($api_key['creator_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($api_key['creator_email']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info" onclick="testCurrentKey()">
                            <i class="fas fa-heartbeat"></i> Test API Key hiện tại
                        </button>
                        
                        <button type="button" class="btn btn-outline-warning" onclick="resetUsageForKey()">
                            <i class="fas fa-redo"></i> Reset Usage Counters
                        </button>
                        
                        <?php if ($api_key['status'] === 'error'): ?>
                            <button type="button" class="btn btn-outline-success" onclick="reactivateKey()">
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

            <!-- Error Log (if any) -->
            <?php if (!empty($api_key['last_error_message'])): ?>
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Lỗi cuối
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-0">
                            <?= htmlspecialchars($api_key['last_error_message']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
const originalApiKey = '<?= htmlspecialchars($api_key['api_key'] ?? '') ?>';

// Toggle API key visibility
function toggleApiKeyVisibility() {
    const input = document.getElementById('api_key');
    const icon = document.getElementById('toggleIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Test API key input
async function testApiKeyInput() {
    const apiKey = document.getElementById('api_key').value.trim();
    const resultDiv = document.getElementById('testResult');
    const testBtn = event.target;
    const originalHtml = testBtn.innerHTML;
    
    if (!apiKey) {
        showTestResult('error', 'Vui lòng nhập API key để test');
        return;
    }
    
    try {
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
        
        const response = await fetch('/zone-fashion/admin/gemini-keys/test-input', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ api_key: apiKey })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showTestResult('success', result.message);
        } else {
            showTestResult('error', result.message);
        }
    } catch (error) {
        showTestResult('error', 'Có lỗi xảy ra khi test API key: ' + error.message);
    } finally {
        testBtn.disabled = false;
        testBtn.innerHTML = originalHtml;
    }
}

// Test current API key
async function testCurrentKey() {
    const button = event.target;
    const originalHtml = button.innerHTML;
    
    try {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
        
        const response = await fetch('/zone-fashion/admin/gemini-keys/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ key_id: <?= $api_key['id'] ?? 0 ?> })
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
    } finally {
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}

// Reset usage for this key
async function resetUsageForKey() {
    if (!confirm('Bạn có chắc chắn muốn reset usage counters cho key này?')) return;
    
    // This would require a new endpoint for resetting single key usage
    showAlert('info', 'Tính năng này sẽ được thêm vào phiên bản tiếp theo');
}

// Reactivate error key
function reactivateKey() {
    if (!confirm('Bạn có chắc chắn muốn kích hoạt lại key này?')) return;
    
    document.getElementById('status').value = 'active';
    showAlert('info', 'Đã thay đổi trạng thái thành "Hoạt động". Hãy nhấn "Cập nhật" để lưu.');
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

// Show test result
function showTestResult(type, message) {
    const resultDiv = document.getElementById('testResult');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    resultDiv.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}

// Show alert message
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : type === 'info' ? 'alert-info' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : type === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle';
    
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

// Form submission
document.getElementById('editKeyForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
});

// Check if API key changed
document.getElementById('api_key').addEventListener('input', function(e) {
    const value = e.target.value.trim();
    const resultDiv = document.getElementById('testResult');
    
    if (value && value !== originalApiKey) {
        resultDiv.innerHTML = `
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i> 
                API key đã thay đổi. Hệ thống sẽ test key mới trước khi lưu.
            </div>
        `;
    } else if (value && value === originalApiKey) {
        resultDiv.innerHTML = `
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> 
                API key không thay đổi.
            </div>
        `;
    } else {
        resultDiv.innerHTML = '';
    }
});
</script>