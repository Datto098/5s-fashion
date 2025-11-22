<?php
$title = $title ?? 'Tạo Gemini API Key - zone Fashion Admin';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tạo Gemini API Key</h1>
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
        <a href="/zone-fashion/admin/gemini-keys" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin API Key</h6>
                </div>
                <div class="card-body">
                    <form method="POST" id="createKeyForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Tên API Key <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           required placeholder="VD: Gemini Production Key"
                                           value="<?= htmlspecialchars($old_data['name'] ?? '') ?>">
                                    <div class="form-text">Tên gợi nhớ để phân biệt các key</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= ($_POST['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm dừng</option>
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
                                       value="<?= htmlspecialchars($old_data['api_key'] ?? '') ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleApiKeyVisibility()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="testApiKeyInput()">
                                    <i class="fas fa-heartbeat"></i> Test
                                </button>
                            </div>
                            <div class="form-text">
                                API key từ Google AI Studio. 
                                <a href="https://makersuite.google.com/app/apikey" target="_blank">Lấy API key tại đây</a>
                            </div>
                            <div id="testResult" class="mt-2"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="daily_limit" class="form-label">Giới hạn ngày</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="daily_limit" name="daily_limit" 
                                               min="0" value="<?= htmlspecialchars($_POST['daily_limit'] ?? '1000') ?>">
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
                                               min="0" value="<?= htmlspecialchars($_POST['monthly_limit'] ?? '30000') ?>">
                                        <span class="input-group-text">requests</span>
                                    </div>
                                    <div class="form-text">Để 0 nếu không giới hạn</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú về key này, mục đích sử dụng..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/zone-fashion/admin/gemini-keys" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Lưu API Key
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Information -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-question-circle"></i> Hướng dẫn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Cách lấy API Key</h6>
                        <ol class="mb-0">
                            <li>Truy cập <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a></li>
                            <li>Đăng nhập với tài khoản Google</li>
                            <li>Nhấn "Create API Key"</li>
                            <li>Chọn project hoặc tạo mới</li>
                            <li>Copy API key và paste vào form</li>
                        </ol>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý</h6>
                        <ul class="mb-0">
                            <li>API key sẽ được test trước khi lưu</li>
                            <li>Key phải hợp lệ mới được tạo</li>
                            <li>Nên đặt giới hạn để tránh vượt quota</li>
                            <li>Key sẽ được auto-test định kỳ</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-lightbulb"></i> Gợi ý</h6>
                        <ul class="mb-0">
                            <li>Đặt tên key dễ nhớ</li>
                            <li>Ghi chú mục đích sử dụng</li>
                            <li>Thiết lập limit phù hợp</li>
                            <li>Monitor usage thường xuyên</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- API Key Format Reference -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-code"></i> Format API Key
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">API key từ Google AI Studio có format như sau:</p>
                    <div class="bg-light p-3 rounded">
                        <code>AIzaSy[33-39 ký tự ngẫu nhiên]</code>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <strong>Ví dụ:</strong><br>
                        <code>AIzaSyA1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q</code>
                    </p>
                    <div class="mt-3">
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Lấy API Key từ Google AI Studio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
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
        
        const response = await fetch('/zone-fashion/admin/gemini-keys/test', {
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

// Form submission
document.getElementById('createKeyForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
});

// Validate API key format on input
document.getElementById('api_key').addEventListener('input', function(e) {
    const value = e.target.value.trim();
    const resultDiv = document.getElementById('testResult');
    
    // Check for valid Google AI API key formats
    const isValidFormat = value.match(/^AIzaSy[A-Za-z0-9_-]{33,39}$/) || value.match(/^[A-Za-z0-9_-]{39}$/);
    
    if (value && !isValidFormat) {
        resultDiv.innerHTML = `
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i> 
                Format API key không đúng. API key phải bắt đầu bằng "AIzaSy" hoặc là chuỗi 39 ký tự.
            </div>
        `;
    } else if (value) {
        resultDiv.innerHTML = `
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> 
                Format API key hợp lệ. Hãy nhấn "Test" để kiểm tra.
            </div>
        `;
    } else {
        resultDiv.innerHTML = '';
    }
});
</script>