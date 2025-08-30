<!-- Voucher Selection Component for Checkout -->
<div class="voucher-section mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-ticket-alt text-primary me-2"></i>
                Voucher Giảm Giá
            </h5>
        </div>
        <div class="card-body">
            <!-- Manual Voucher Input -->
            <div class="voucher-input-section">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="voucherCode" placeholder="Nhập mã voucher"
                           value="<?= $_SESSION['applied_coupon']['code'] ?? '' ?>">
                    <button class="btn btn-outline-primary" type="button" onclick="applyVoucher()">
                        <i class="fas fa-check"></i> Áp dụng
                    </button>
                    <?php if (isset($_SESSION['applied_coupon'])): ?>
                        <button class="btn btn-outline-danger" type="button" onclick="removeAppliedVoucher()">
                            <i class="fas fa-times"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <div id="voucherMessage" class="alert" style="display: none;"></div>
            </div>

            <!-- Applied Voucher Display -->
            <?php if (isset($_SESSION['applied_coupon'])): ?>
                <div class="applied-voucher" id="appliedVoucher">
                    <div class="alert alert-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($_SESSION['applied_coupon']['name']) ?></strong>
                                <br>
                                <small>Mã: <?= htmlspecialchars($_SESSION['applied_coupon']['code']) ?></small>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">
                                    -<?= number_format($_SESSION['applied_coupon']['discount_amount']) ?>đ
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- User's Available Vouchers (if logged in) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-vouchers-section">
                    <h6 class="mb-3">
                        <i class="fas fa-bookmark text-info me-1"></i>
                        Voucher của bạn
                    </h6>

                    <div id="userVouchers" class="row">
                        <!-- Will be loaded via AJAX -->
                        <div class="col-12 text-center">
                            <button class="btn btn-outline-secondary btn-sm" onclick="loadUserVouchers()">
                                <i class="fas fa-sync-alt"></i> Tải voucher khả dụng
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p class="text-muted mb-2">
                        <i class="fas fa-user-plus"></i> Đăng nhập để sử dụng voucher đã lưu
                    </p>
                    <a href="/zone-fashion/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                       class="btn btn-outline-primary btn-sm">
                        Đăng nhập
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Global variables for voucher functionality
let currentOrderAmount = <?= $orderAmount ?? 0 ?>;

// Apply voucher manually
function applyVoucher() {
    const code = document.getElementById('voucherCode').value.trim();

    if (!code) {
        showVoucherMessage('error', 'Vui lòng nhập mã voucher');
        return;
    }

    if (!currentOrderAmount || currentOrderAmount <= 0) {
        showVoucherMessage('error', 'Không thể áp dụng voucher cho đơn hàng trống');
        return;
    }

    // Show loading
    const applyBtn = event.target;
    const originalText = applyBtn.innerHTML;
    applyBtn.disabled = true;
    applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang áp dụng...';

    fetch('/zone-fashion/vouchers/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `code=${encodeURIComponent(code)}&amount=${currentOrderAmount}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showVoucherMessage('success', data.message);
            updateOrderSummary(data.discount, data.final_amount);
            showAppliedVoucher(data.coupon, data.discount);
        } else {
            showVoucherMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showVoucherMessage('error', 'Có lỗi xảy ra khi áp dụng voucher');
    })
    .finally(() => {
        applyBtn.disabled = false;
        applyBtn.innerHTML = originalText;
    });
}

// Remove applied voucher
function removeAppliedVoucher() {
    fetch('/zone-fashion/vouchers/remove-applied', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showVoucherMessage('success', data.message);
            hideAppliedVoucher();
            updateOrderSummary(0, currentOrderAmount);
            document.getElementById('voucherCode').value = '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showVoucherMessage('error', 'Có lỗi xảy ra');
    });
}

// Apply voucher from user's list
function applyUserVoucher(couponCode) {
    document.getElementById('voucherCode').value = couponCode;
    applyVoucher();
}

// Load user's valid vouchers
function loadUserVouchers() {
    const container = document.getElementById('userVouchers');

    container.innerHTML = '<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

    fetch(`/zone-fashion/vouchers/get-valid?amount=${currentOrderAmount}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.coupons.length > 0) {
            let html = '';
            data.coupons.forEach(coupon => {
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="voucher-mini-card" onclick="applyUserVoucher('${coupon.code}')">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary">${coupon.code}</strong>
                                    <br>
                                    <small class="text-muted">${coupon.name}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">${coupon.formatted_discount}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="col-12 text-center text-muted">Không có voucher khả dụng</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = '<div class="col-12 text-center text-danger">Có lỗi xảy ra khi tải voucher</div>';
    });
}

// Show voucher message
function showVoucherMessage(type, message) {
    const messageDiv = document.getElementById('voucherMessage');
    messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
    messageDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    messageDiv.style.display = 'block';

    // Auto hide after 5 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Show applied voucher
function showAppliedVoucher(coupon, discount) {
    const appliedDiv = document.getElementById('appliedVoucher');
    if (!appliedDiv) {
        // Create applied voucher element if it doesn't exist
        const newDiv = document.createElement('div');
        newDiv.id = 'appliedVoucher';
        newDiv.className = 'applied-voucher';
        document.querySelector('.voucher-input-section').after(newDiv);
    }

    document.getElementById('appliedVoucher').innerHTML = `
        <div class="alert alert-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${coupon.name}</strong>
                    <br>
                    <small>Mã: ${coupon.code}</small>
                </div>
                <div class="text-end">
                    <strong class="text-success">-${new Intl.NumberFormat('vi-VN').format(discount)}đ</strong>
                </div>
            </div>
        </div>
    `;

    // Update apply button to remove button
    const applyBtn = document.querySelector('.voucher-input-section .btn-outline-primary');
    if (applyBtn) {
        applyBtn.outerHTML = `
            <button class="btn btn-outline-danger" type="button" onclick="removeAppliedVoucher()">
                <i class="fas fa-times"></i>
            </button>
        `;
    }
}

// Hide applied voucher
function hideAppliedVoucher() {
    const appliedDiv = document.getElementById('appliedVoucher');
    if (appliedDiv) {
        appliedDiv.innerHTML = '';
    }

    // Update remove button back to apply button
    const removeBtn = document.querySelector('.voucher-input-section .btn-outline-danger');
    if (removeBtn) {
        removeBtn.outerHTML = `
            <button class="btn btn-outline-primary" type="button" onclick="applyVoucher()">
                <i class="fas fa-check"></i> Áp dụng
            </button>
        `;
    }
}

// Update order summary (to be called from parent page)
function updateOrderSummary(discount, finalAmount) {
    // This function should be implemented in the checkout page
    if (typeof updateCheckoutSummary === 'function') {
        updateCheckoutSummary(discount, finalAmount);
    }

    // Update current order amount
    currentOrderAmount = finalAmount;
}

// Auto-load user vouchers on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-load if user is logged in and no voucher is applied
    <?php if (isset($_SESSION['user_id']) && !isset($_SESSION['applied_coupon'])): ?>
        loadUserVouchers();
    <?php endif; ?>

    // Add Enter key support for voucher input
    document.getElementById('voucherCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyVoucher();
        }
    });
});
</script>

<style>
.voucher-mini-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.voucher-mini-card:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

.applied-voucher .alert {
    margin-bottom: 0;
}

.voucher-section .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
}

.voucher-section .card-header h5 {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .voucher-mini-card {
        margin-bottom: 10px;
    }

    .input-group {
        flex-direction: column;
    }

    .input-group .form-control {
        margin-bottom: 10px;
        border-radius: 0.375rem !important;
    }

    .input-group .btn {
        border-radius: 0.375rem !important;
    }
}
</style>
