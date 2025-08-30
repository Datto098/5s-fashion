<?php
// Admin Coupons Edit View
?>

<!-- Page Header -->
<div class="admin-header">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-edit"></i>
            Sửa Voucher
        </h1>
        <div class="admin-breadcrumb">
            <a href="/zone-fashion/admin/coupons" class="breadcrumb-link">Quản lý Voucher</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">Sửa</span>
        </div>
    </div>
    <div class="admin-header-actions">
        <a href="/zone-fashion/admin/coupons" class="btn btn-outline">
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

                <form method="POST" action="/zone-fashion/admin/coupons/<?= $coupon['id'] ?>/update">
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
                                               value="<?= htmlspecialchars($old_data['code'] ?? $coupon['code'] ?? '') ?>"
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
                                        <option value="active" <?= (($old_data['status'] ?? $coupon['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="inactive" <?= (($old_data['status'] ?? $coupon['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Tạm dừng</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Tên voucher <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= htmlspecialchars($old_data['name'] ?? $coupon['name'] ?? '') ?>"
                                   placeholder="VD: Chào mừng khách hàng mới" required maxlength="100">
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Mô tả về voucher..."><?= htmlspecialchars($old_data['description'] ?? $coupon['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Loại giảm giá <span class="text-danger">*</span></label>
                                    <select class="form-control" id="type" name="type" required onchange="toggleValueInput()">
                                        <option value="percentage" <?= (($old_data['type'] ?? $coupon['type'] ?? '') === 'percentage') ? 'selected' : '' ?>>Phần trăm (%)</option>
                                        <option value="fixed_amount" <?= (($old_data['type'] ?? $coupon['type'] ?? '') === 'fixed_amount') ? 'selected' : '' ?>>Số tiền cố định (đ)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Giá trị <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="value" name="value"
                                               value="<?= htmlspecialchars($old_data['value'] ?? $coupon['value'] ?? '') ?>"
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
                                               value="<?= htmlspecialchars($old_data['minimum_amount'] ?? $coupon['minimum_amount'] ?? '') ?>"
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
                                               value="<?= htmlspecialchars($old_data['maximum_discount'] ?? $coupon['maximum_discount'] ?? '') ?>"
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
                                           value="<?= htmlspecialchars($old_data['usage_limit'] ?? $coupon['usage_limit'] ?? '') ?>"
                                           placeholder="Để trống = không giới hạn" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_limit">Giới hạn mỗi người dùng</label>
                                    <input type="number" class="form-control" id="user_limit" name="user_limit"
                                           value="<?= htmlspecialchars($old_data['user_limit'] ?? $coupon['user_limit'] ?? '') ?>"
                                           placeholder="Để trống = không giới hạn" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_from">Hiệu lực từ</label>
                                    <input type="datetime-local" class="form-control" id="valid_from" name="valid_from"
                                           value="<?= htmlspecialchars($old_data['valid_from'] ?? $coupon['valid_from'] ?? '') ?>">
                                    <small class="form-text text-muted">Để trống = có hiệu lực ngay</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_until">Hiệu lực đến</label>
                                    <input type="datetime-local" class="form-control" id="valid_until" name="valid_until"
                                           value="<?= htmlspecialchars($old_data['valid_until'] ?? $coupon['valid_until'] ?? '') ?>">
                                    <small class="form-text text-muted">Để trống = không hết hạn</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="/zone-fashion/admin/coupons" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <a href="/zone-fashion/admin/coupons/<?= $coupon['id'] ?>/delete" class="btn btn-danger float-end" onclick="return confirm('Bạn có chắc muốn xóa voucher này không?')">
                            <i class="fas fa-trash"></i> Xóa
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
                            <span id="previewCode"><?= htmlspecialchars($coupon['code'] ?? 'Mã voucher') ?></span>
                        </div>
                        <div class="voucher-name">
                            <span id="previewName"><?= htmlspecialchars($coupon['name'] ?? 'Tên voucher') ?></span>
                        </div>
                        <div class="voucher-value">
                            <span id="previewValue"><?= htmlspecialchars($coupon['value'] ?? 'Giá trị') ?></span>
                        </div>
                        <div class="voucher-condition">
                            <small id="previewCondition"><?= htmlspecialchars($coupon['minimum_amount'] ? 'Cho đơn hàng từ ' . number_format($coupon['minimum_amount']) . 'đ' : 'Không điều kiện tối thiểu') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Minimal styles for voucher preview */
.voucher-preview{border:1px solid #eee;padding:12px;border-radius:6px;background:#fff}
.voucher-code{font-weight:700;font-size:18px;color:#333}
.voucher-name{color:#666;margin-top:6px}
.voucher-value{margin-top:8px;font-size:16px;font-weight:600;color:#007bff}
.voucher-condition{margin-top:6px;color:#999}
.card .card-body .form-text{display:block}
</style>

<script>
// Minimal JS helpers for the edit view
function generateCode(){
    var code = 'V' + Math.random().toString(36).substr(2,8).toUpperCase();
    var el = document.getElementById('code');
    if(el) el.value = code;
    updatePreview();
}

function toggleValueInput(){
    var typeEl = document.getElementById('type');
    var unitEl = document.getElementById('valueUnit');
    if(!typeEl || !unitEl) return;
    unitEl.textContent = (typeEl.value === 'percentage') ? '%' : 'đ';
    updatePreview();
}

function updatePreview(){
    var code = document.getElementById('code');
    var name = document.getElementById('name');
    var value = document.getElementById('value');
    var min = document.getElementById('minimum_amount');
    var type = document.getElementById('type');

    var previewCode = document.getElementById('previewCode');
    var previewName = document.getElementById('previewName');
    var previewValue = document.getElementById('previewValue');
    var previewCondition = document.getElementById('previewCondition');

    if(previewCode) previewCode.textContent = (code && code.value) ? code.value : 'Mã voucher';
    if(previewName) previewName.textContent = (name && name.value) ? name.value : 'Tên voucher';
    if(previewValue) previewValue.textContent = (value && value.value) ? (value.value + (type && type.value === 'percentage' ? '%' : ' đ')) : 'Giá trị';
    if(previewCondition) previewCondition.textContent = (min && min.value) ? ('Cho đơn hàng từ ' + Number(min.value).toLocaleString() + 'đ') : 'Không điều kiện tối thiểu';
}

document.addEventListener('DOMContentLoaded', function(){
    // initialize preview and bindings
    toggleValueInput();
    updatePreview();
    ['code','name','value','minimum_amount'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.addEventListener('input', updatePreview);
    });
    var typeEl = document.getElementById('type');
    if(typeEl) typeEl.addEventListener('change', toggleValueInput);
});
</script>
