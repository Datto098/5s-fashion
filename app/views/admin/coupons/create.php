<?php
// Admin Coupons Create View
?>

<!-- Page Header -->
<div class="admin-header">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-plus-circle"></i>
            Tạo Voucher Mới
        </h1>
        <div class="admin-breadcrumb">
            <a href="/5s-fashion/admin/coupons" class="breadcrumb-link">Quản lý Voucher</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">Tạo mới</span>
        </div>
    </div>
    <div class="admin-header-actions">
        <a href="/5s-fashion/admin/coupons" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Quay lại
        </a>
    </div>
</div>

    <section class="content">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin voucher</h3>
                        </div>

                        <form method="POST" action="/5s-fashion/admin/coupons/store">
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Mã voucher <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="code" name="code"
                                                       value="<?= htmlspecialchars($old_data['code'] ?? '') ?>"
                                                       placeholder="VD: WELCOME10" required maxlength="50">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">
                                                        <i class="fas fa-magic"></i> Tạo tự động
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Mã voucher phải duy nhất và không chứa khoảng trắng</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active" <?= ($old_data['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                                <option value="inactive" <?= ($old_data['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm dừng</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Tên voucher <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?= htmlspecialchars($old_data['name'] ?? '') ?>"
                                           placeholder="VD: Chào mừng khách hàng mới" required maxlength="100">
                                </div>

                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                              placeholder="Mô tả về voucher..."><?= htmlspecialchars($old_data['description'] ?? '') ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Loại giảm giá <span class="text-danger">*</span></label>
                                            <select class="form-control" id="type" name="type" required onchange="toggleValueInput()">
                                                <option value="percentage" <?= ($old_data['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Phần trăm (%)</option>
                                                <option value="fixed_amount" <?= ($old_data['type'] ?? '') === 'fixed_amount' ? 'selected' : '' ?>>Số tiền cố định (đ)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="value">Giá trị <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="value" name="value"
                                                       value="<?= $old_data['value'] ?? '' ?>"
                                                       placeholder="0" required min="0" step="0.01">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="valueUnit">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="minimum_amount">Đơn hàng tối thiểu</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="minimum_amount" name="minimum_amount"
                                                       value="<?= $old_data['minimum_amount'] ?? '' ?>"
                                                       placeholder="0" min="0" step="1000">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">đ</span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Để trống nếu không có giới hạn</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="maximum_discount">Giảm tối đa</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="maximum_discount" name="maximum_discount"
                                                       value="<?= $old_data['maximum_discount'] ?? '' ?>"
                                                       placeholder="0" min="0" step="1000">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">đ</span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Chỉ áp dụng cho voucher phần trăm</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="usage_limit">Giới hạn tổng sử dụng</label>
                                            <input type="number" class="form-control" id="usage_limit" name="usage_limit"
                                                   value="<?= $old_data['usage_limit'] ?? '' ?>"
                                                   placeholder="Để trống = không giới hạn" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_limit">Giới hạn mỗi người dùng</label>
                                            <input type="number" class="form-control" id="user_limit" name="user_limit"
                                                   value="<?= $old_data['user_limit'] ?? '' ?>"
                                                   placeholder="Để trống = không giới hạn" min="1">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="valid_from">Hiệu lực từ</label>
                                            <input type="datetime-local" class="form-control" id="valid_from" name="valid_from"
                                                   value="<?= $old_data['valid_from'] ?? '' ?>">
                                            <small class="form-text text-muted">Để trống = có hiệu lực ngay</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="valid_until">Hiệu lực đến</label>
                                            <input type="datetime-local" class="form-control" id="valid_until" name="valid_until"
                                                   value="<?= $old_data['valid_until'] ?? '' ?>">
                                            <small class="form-text text-muted">Để trống = không hết hạn</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Tạo Voucher
                                </button>
                                <a href="/5s-fashion/admin/coupons" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Hướng dẫn</h3>
                        </div>
                        <div class="card-body">
                            <h6><i class="fas fa-lightbulb text-warning"></i> Mẹo tạo voucher hiệu quả:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Mã voucher ngắn gọn, dễ nhớ</li>
                                <li><i class="fas fa-check text-success"></i> Tên rõ ràng, mô tả chi tiết</li>
                                <li><i class="fas fa-check text-success"></i> Đặt điều kiện phù hợp</li>
                                <li><i class="fas fa-check text-success"></i> Thời hạn hợp lý</li>
                            </ul>

                            <hr>

                            <h6><i class="fas fa-info-circle text-info"></i> Loại voucher:</h6>
                            <div class="mb-3">
                                <strong>Phần trăm:</strong> Giảm % giá trị đơn hàng<br>
                                <small class="text-muted">VD: Giảm 10% (tối đa 50k)</small>
                            </div>
                            <div>
                                <strong>Số tiền cố định:</strong> Giảm số tiền nhất định<br>
                                <small class="text-muted">VD: Giảm 30k cho đơn từ 200k</small>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Xem trước</h3>
                        </div>
                        <div class="card-body">
                            <div class="voucher-preview" id="voucherPreview">
                                <div class="voucher-code">
                                    <span id="previewCode">Mã voucher</span>
                                </div>
                                <div class="voucher-name">
                                    <span id="previewName">Tên voucher</span>
                                </div>
                                <div class="voucher-value">
                                    <span id="previewValue">Giá trị</span>
                                </div>
                                <div class="voucher-condition">
                                    <small id="previewCondition">Điều kiện</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<style>
.voucher-preview {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.voucher-preview::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="%23ffffff" opacity="0.1"/></svg>') repeat;
    animation: float 20s linear infinite;
}

@keyframes float {
    0% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
    100% { transform: translateX(-50%) translateY(-50%) rotate(360deg); }
}

.voucher-code {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
    letter-spacing: 2px;
}

.voucher-name {
    font-size: 14px;
    margin-bottom: 10px;
    opacity: 0.9;
}

.voucher-value {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
}

.voucher-condition {
    font-size: 12px;
    opacity: 0.8;
}
</style>

<script>
// Toggle value unit based on type
function toggleValueInput() {
    const type = document.getElementById('type').value;
    const valueUnit = document.getElementById('valueUnit');
    const valueInput = document.getElementById('value');

    if (type === 'percentage') {
        valueUnit.textContent = '%';
        valueInput.setAttribute('max', '100');
        document.getElementById('maximum_discount').closest('.form-group').style.display = 'block';
    } else {
        valueUnit.textContent = 'đ';
        valueInput.removeAttribute('max');
        document.getElementById('maximum_discount').closest('.form-group').style.display = 'none';
    }

    updatePreview();
}

// Generate coupon code
function generateCode() {
    const prefix = prompt('Nhập tiền tố (để trống nếu không cần):') || '';

    fetch(`/5s-fashion/admin/coupons/generate-code?prefix=${prefix}&length=8`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('code').value = data.code;
            updatePreview();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo mã');
        });
}

// Update preview
function updatePreview() {
    const code = document.getElementById('code').value || 'Mã voucher';
    const name = document.getElementById('name').value || 'Tên voucher';
    const type = document.getElementById('type').value;
    const value = document.getElementById('value').value || '0';
    const minAmount = document.getElementById('minimum_amount').value;

    document.getElementById('previewCode').textContent = code;
    document.getElementById('previewName').textContent = name;

    let valueText = '';
    if (type === 'percentage') {
        valueText = `Giảm ${value}%`;
    } else {
        valueText = `Giảm ${new Intl.NumberFormat('vi-VN').format(value)}đ`;
    }
    document.getElementById('previewValue').textContent = valueText;

    let conditionText = '';
    if (minAmount) {
        conditionText = `Cho đơn hàng từ ${new Intl.NumberFormat('vi-VN').format(minAmount)}đ`;
    } else {
        conditionText = 'Không điều kiện tối thiểu';
    }
    document.getElementById('previewCondition').textContent = conditionText;
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    toggleValueInput();
    updatePreview();

    // Update preview when inputs change
    ['code', 'name', 'type', 'value', 'minimum_amount'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });
});
</script>
