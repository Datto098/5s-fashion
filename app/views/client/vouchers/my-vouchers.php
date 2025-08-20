
<div class="container my-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/5s-fashion">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/5s-fashion/vouchers">Voucher</a></li>
                    <li class="breadcrumb-item active">Voucher của tôi</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">Voucher Của Tôi</h1>
                    <p class="text-muted">Quản lý và sử dụng voucher đã lưu</p>
                </div>
                <div>
                    <a href="/5s-fashion/vouchers" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thu thập voucher mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card text-center">
                <div class="stats-icon">
                    <i class="fas fa-bookmark text-primary"></i>
                </div>
                <div class="stats-number"><?= $stats['saved_count'] ?? 0 ?></div>
                <div class="stats-label">Đã lưu</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card text-center">
                <div class="stats-icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stats-number"><?= $stats['used_count'] ?? 0 ?></div>
                <div class="stats-label">Đã sử dụng</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card text-center">
                <div class="stats-icon">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="stats-number"><?= $stats['expired_count'] ?? 0 ?></div>
                <div class="stats-label">Hết hạn</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stats-card text-center">
                <div class="stats-icon">
                    <i class="fas fa-piggy-bank text-info"></i>
                </div>
                <div class="stats-number"><?= number_format($stats['total_savings'] ?? 0) ?>đ</div>
                <div class="stats-label">Tiết kiệm</div>
            </div>
        </div>
    </div>

    <!-- Voucher Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="voucherTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="saved-tab" data-bs-toggle="tab" href="#saved" role="tab">
                        <i class="fas fa-bookmark"></i> Đã lưu (<?= count($savedCoupons) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="used-tab" data-bs-toggle="tab" href="#used" role="tab">
                        <i class="fas fa-check-circle"></i> Đã sử dụng (<?= count($usedCoupons) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="expired-tab" data-bs-toggle="tab" href="#expired" role="tab">
                        <i class="fas fa-clock"></i> Hết hạn (<?= count($expiredCoupons) ?>)
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="voucherTabContent">
                <!-- Saved Vouchers -->
                <div class="tab-pane fade show active" id="saved" role="tabpanel">
                    <?php if (empty($savedCoupons)): ?>
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Chưa có voucher nào</h4>
                            <p class="text-muted mb-4">Bạn chưa lưu voucher nào. Hãy thu thập voucher để được giảm giá!</p>
                            <a href="/5s-fashion/vouchers" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thu thập voucher ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($savedCoupons as $coupon): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="my-voucher-card saved">
                                        <div class="voucher-header">
                                            <div class="voucher-status">Có thể sử dụng</div>
                                            <div class="voucher-code"><?= htmlspecialchars($coupon['code']) ?></div>
                                        </div>

                                        <div class="voucher-body">
                                            <h5 class="voucher-name"><?= htmlspecialchars($coupon['name']) ?></h5>

                                            <div class="voucher-value">
                                                <?php if ($coupon['type'] === 'percentage'): ?>
                                                    <span class="value-text">Giảm <?= $coupon['value'] ?>%</span>
                                                    <?php if ($coupon['maximum_discount']): ?>
                                                        <span class="value-max">Tối đa <?= number_format($coupon['maximum_discount']) ?>đ</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="value-text">Giảm <?= number_format($coupon['value']) ?>đ</span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($coupon['minimum_amount']): ?>
                                                <div class="voucher-condition">
                                                    Cho đơn hàng từ <?= number_format($coupon['minimum_amount']) ?>đ
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($coupon['description']): ?>
                                                <p class="voucher-description"><?= htmlspecialchars($coupon['description']) ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="voucher-footer">
                                            <div class="voucher-dates">
                                                <div class="saved-date">
                                                    <i class="fas fa-bookmark"></i> Lưu: <?= date('d/m/Y', strtotime($coupon['saved_at'])) ?>
                                                </div>
                                                <?php if ($coupon['valid_until']): ?>
                                                    <div class="expiry-date">
                                                        <i class="fas fa-clock"></i> HSD: <?= date('d/m/Y', strtotime($coupon['valid_until'])) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="voucher-actions">
                                                <button class="btn btn-primary btn-sm copy-code"
                                                        data-code="<?= htmlspecialchars($coupon['code']) ?>">
                                                    <i class="fas fa-copy"></i> Copy mã
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm remove-voucher"
                                                        data-coupon-id="<?= $coupon['coupon_id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <a href="/5s-fashion/shop" class="btn btn-success btn-sm">
                                                    <i class="fas fa-shopping-cart"></i> Mua sắm
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Used Vouchers -->
                <div class="tab-pane fade" id="used" role="tabpanel">
                    <?php if (empty($usedCoupons)): ?>
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Chưa sử dụng voucher nào</h4>
                            <p class="text-muted">Voucher đã sử dụng sẽ hiển thị ở đây</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($usedCoupons as $coupon): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="my-voucher-card used">
                                        <div class="voucher-header">
                                            <div class="voucher-status">Đã sử dụng</div>
                                            <div class="voucher-code"><?= htmlspecialchars($coupon['code']) ?></div>
                                        </div>

                                        <div class="voucher-body">
                                            <h5 class="voucher-name"><?= htmlspecialchars($coupon['name']) ?></h5>

                                            <div class="voucher-value">
                                                <?php if ($coupon['type'] === 'percentage'): ?>
                                                    <span class="value-text">Đã giảm <?= $coupon['value'] ?>%</span>
                                                <?php else: ?>
                                                    <span class="value-text">Đã giảm <?= number_format($coupon['value']) ?>đ</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="voucher-footer">
                                            <div class="voucher-dates">
                                                <div class="used-date">
                                                    <i class="fas fa-check-circle"></i> Dùng: <?= date('d/m/Y', strtotime($coupon['used_at'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Expired Vouchers -->
                <div class="tab-pane fade" id="expired" role="tabpanel">
                    <?php if (empty($expiredCoupons)): ?>
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Không có voucher hết hạn</h4>
                            <p class="text-muted">Voucher hết hạn sẽ hiển thị ở đây</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($expiredCoupons as $coupon): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="my-voucher-card expired">
                                        <div class="voucher-header">
                                            <div class="voucher-status">Hết hạn</div>
                                            <div class="voucher-code"><?= htmlspecialchars($coupon['code']) ?></div>
                                        </div>

                                        <div class="voucher-body">
                                            <h5 class="voucher-name"><?= htmlspecialchars($coupon['name']) ?></h5>

                                            <div class="voucher-value">
                                                <?php if ($coupon['type'] === 'percentage'): ?>
                                                    <span class="value-text">Giảm <?= $coupon['value'] ?>%</span>
                                                <?php else: ?>
                                                    <span class="value-text">Giảm <?= number_format($coupon['value']) ?>đ</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="voucher-footer">
                                            <div class="voucher-dates">
                                                <div class="expired-date">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Hết hạn: <?= date('d/m/Y', strtotime($coupon['valid_until'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    transition: all 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stats-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.stats-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.stats-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.my-voucher-card {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.my-voucher-card.saved {
    border-color: #28a745;
}

.my-voucher-card.used {
    border-color: #6c757d;
    opacity: 0.8;
}

.my-voucher-card.expired {
    border-color: #dc3545;
    opacity: 0.7;
}

.voucher-header {
    padding: 15px;
    position: relative;
    color: white;
}

.my-voucher-card.saved .voucher-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.my-voucher-card.used .voucher-header {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.my-voucher-card.expired .voucher-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.voucher-status {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 12px;
    font-weight: bold;
    background: rgba(255,255,255,0.2);
    padding: 4px 8px;
    border-radius: 20px;
}

.voucher-code {
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 2px;
    text-align: center;
    margin-top: 10px;
}

.voucher-body {
    padding: 20px;
    flex-grow: 1;
}

.voucher-name {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.voucher-value {
    margin-bottom: 15px;
}

.value-text {
    font-size: 20px;
    font-weight: bold;
    color: #007bff;
}

.value-max {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.voucher-condition {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 15px;
}

.voucher-description {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 0;
}

.voucher-footer {
    padding: 15px 20px;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
}

.voucher-dates {
    margin-bottom: 15px;
}

.voucher-dates > div {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
}

.voucher-actions {
    display: flex;
    gap: 5px;
}

.voucher-actions .btn-sm {
    min-width: auto;
}

.empty-state {
    background: #f8f9fa;
    border-radius: 10px;
    margin: 20px 0;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background: none;
    border: none;
    border-bottom: 2px solid #007bff;
    color: #007bff;
}

@media (max-width: 768px) {
    .voucher-actions {
        justify-content: center;
    }

    .voucher-actions .btn-sm {
        flex: none;
        margin-bottom: 5px;
    }

    .stats-card {
        margin-bottom: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy code functionality
    document.querySelectorAll('.copy-code').forEach(button => {
        button.addEventListener('click', function() {
            const code = this.dataset.code;
            const originalText = this.innerHTML;

            navigator.clipboard.writeText(code).then(() => {
                this.innerHTML = '<i class="fas fa-check"></i> Đã copy';
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');

                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                }, 2000);

                showToast('success', `Đã copy mã "${code}" vào clipboard`);
            }).catch(() => {
                showToast('error', 'Không thể copy mã voucher');
            });
        });
    });

    // Remove voucher functionality
    document.querySelectorAll('.remove-voucher').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('Bạn có chắc muốn xóa voucher này khỏi danh sách?')) {
                return;
            }

            const couponId = this.dataset.couponId;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('/5s-fashion/vouchers/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `coupon_id=${couponId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    // Remove the voucher card
                    this.closest('.col-md-6').remove();

                    // Update tab counter
                    const savedTab = document.querySelector('#saved-tab');
                    const currentCount = parseInt(savedTab.textContent.match(/\((\d+)\)/)[1]);
                    savedTab.innerHTML = savedTab.innerHTML.replace(/\(\d+\)/, `(${currentCount - 1})`);

                    // Check if this was the last voucher
                    const remainingVouchers = document.querySelectorAll('#saved .my-voucher-card');
                    if (remainingVouchers.length === 0) {
                        document.querySelector('#saved').innerHTML = `
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Chưa có voucher nào</h4>
                                <p class="text-muted mb-4">Bạn chưa lưu voucher nào. Hãy thu thập voucher để được giảm giá!</p>
                                <a href="/5s-fashion/vouchers" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Thu thập voucher ngay
                                </a>
                            </div>
                        `;
                    }
                } else {
                    this.disabled = false;
                    this.innerHTML = originalText;
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.disabled = false;
                this.innerHTML = originalText;
                showToast('error', 'Có lỗi xảy ra khi xóa voucher');
            });
        });
    });
});

// Toast notification function
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} toast-notification`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;

    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations if not already present
if (!document.querySelector('#toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}
</script>