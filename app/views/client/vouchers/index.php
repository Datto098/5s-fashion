

<div class="container my-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/5s-fashion">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Voucher khuyến mãi</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">Voucher Khuyến Mãi</h1>
                    <p class="text-muted">Thu thập voucher để được giảm giá khi mua sắm</p>
                </div>
                <?php if ($userId): ?>
                    <div>
                        <a href="/5s-fashion/vouchers/my-vouchers" class="btn btn-outline-primary">
                            <i class="fas fa-wallet"></i> Voucher của tôi
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$userId): ?>
        <!-- Login Prompt -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Đăng nhập để lưu voucher!</strong>
            <a href="/5s-fashion/login" class="alert-link">Đăng nhập ngay</a> để có thể lưu và sử dụng voucher.
        </div>
    <?php endif; ?>

    <!-- Trending Vouchers -->
    <?php if (!empty($trendingCoupons)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-fire text-danger me-2"></i>
                    <h3 class="h4 mb-0">Voucher Hot Nhất</h3>
                </div>

                <div class="row">
                    <?php foreach ($trendingCoupons as $coupon): ?>
                        <?php
                        $isSaved = false;
                        if (!empty($savedCoupons)) {
                            foreach ($savedCoupons as $saved) {
                                if ($saved['coupon_id'] == $coupon['id']) {
                                    $isSaved = true;
                                    break;
                                }
                            }
                        }
                        if ($isSaved) continue; // Ẩn voucher đã lưu khỏi phần Hot
                        ?>
                        <div class="col-md-4 mb-3">
                            <div class="voucher-card trending">
                                <div class="voucher-header">
                                    <div class="voucher-badge">
                                        <i class="fas fa-fire"></i> HOT
                                    </div>
                                    <div class="voucher-code"><?= htmlspecialchars($coupon['code']) ?></div>
                                </div>

                                <div class="voucher-body">
                                    <h5 class="voucher-title"><?= htmlspecialchars($coupon['name']) ?></h5>
                                    <div class="voucher-value">
                                        <?php if ($coupon['type'] === 'percentage'): ?>
                                            <span class="value-text">Giảm <?= $coupon['value'] ?>%</span>
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
                                    <?php if ($coupon['valid_until']): ?>
                                        <div class="voucher-expiry">
                                            <i class="fas fa-clock"></i> HSD: <?= date('d/m/Y', strtotime($coupon['valid_until'])) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($userId): ?>
                                        <button class="btn btn-sm save-voucher btn-primary" data-coupon-id="<?= $coupon['id'] ?>">
                                            <i class="fas fa-plus"></i> Lưu
                                        </button>
                                    <?php else: ?>
                                        <a href="/5s-fashion/login" class="btn btn-outline-primary btn-sm">
                                            Đăng nhập để lưu
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Available Vouchers -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h4 mb-0">Tất Cả Voucher</h3>
                <div class="voucher-filters">
                    <button class="btn btn-sm btn-outline-secondary active" data-filter="all">Tất cả</button>
                    <button class="btn btn-sm btn-outline-secondary" data-filter="percentage">Giảm %</button>
                    <button class="btn btn-sm btn-outline-secondary" data-filter="fixed_amount">Giảm tiền</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="vouchersContainer">
        <?php if (empty($availableCoupons)): ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Không có voucher nào</h4>
                    <p class="text-muted">Hiện tại không có voucher khả dụng</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($availableCoupons as $coupon): ?>
                <div class="col-md-6 col-lg-4 mb-4 voucher-item" data-type="<?= $coupon['type'] ?>">
                    <div class="voucher-card">
                        <div class="voucher-header">
                            <div class="voucher-code"><?= htmlspecialchars($coupon['code']) ?></div>
                            <?php if ($coupon['usage_limit'] && $coupon['used_count'] > 0): ?>
                                <div class="voucher-usage">
                                    <?= $coupon['used_count'] ?>/<?= $coupon['usage_limit'] ?> đã dùng
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="voucher-body">
                            <h5 class="voucher-title"><?= htmlspecialchars($coupon['name']) ?></h5>
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
                                    <i class="fas fa-shopping-cart"></i> Cho đơn hàng từ <?= number_format($coupon['minimum_amount']) ?>đ
                                </div>
                            <?php endif; ?>

                            <?php if ($coupon['description']): ?>
                                <p class="voucher-description"><?= htmlspecialchars($coupon['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="voucher-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($coupon['valid_until']): ?>
                                        <div class="voucher-expiry">
                                            <i class="fas fa-clock"></i> HSD: <?= date('d/m/Y', strtotime($coupon['valid_until'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="voucher-expiry">
                                            <i class="fas fa-infinity"></i> Không hết hạn
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="voucher-actions">
                                    <?php if ($userId): ?>
                                        <button class="btn btn-primary btn-sm save-voucher" data-coupon-id="<?= $coupon['id'] ?>">
                                            <i class="fas fa-plus"></i> Lưu
                                        </button>
                                    <?php else: ?>
                                        <a href="/5s-fashion/login" class="btn btn-outline-primary btn-sm">
                                            Đăng nhập để lưu
                                        </a>
                                    <?php endif; ?>

                                    <button class="btn btn-outline-secondary btn-sm share-voucher"
                                            data-coupon-id="<?= $coupon['id'] ?>"
                                            data-coupon-code="<?= htmlspecialchars($coupon['code']) ?>"
                                            data-coupon-name="<?= htmlspecialchars($coupon['name']) ?>">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- My Saved Vouchers (if logged in) -->
    <?php if ($userId && !empty($savedCoupons)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h4 mb-0">Voucher Đã Lưu</h3>
                    <a href="/5s-fashion/vouchers/my-vouchers" class="btn btn-outline-primary btn-sm">
                        Xem tất cả <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="row">
                    <?php foreach (array_slice($savedCoupons, 0, 3) as $coupon): ?>
                        <div class="col-md-4 mb-3">
                            <div class="voucher-card saved">
                                <div class="voucher-header">
                                    <div class="voucher-badge saved-badge">
                                        <i class="fas fa-bookmark"></i> ĐÃ LƯU
                                    </div>
                                    <div class="voucher-code"><?= htmlspecialchars($coupon['code']) ?></div>
                                </div>

                                <div class="voucher-body">
                                    <h5 class="voucher-title"><?= htmlspecialchars($coupon['name']) ?></h5>
                                    <div class="voucher-value">
                                        <?php if ($coupon['type'] === 'percentage'): ?>
                                            <span class="value-text">Giảm <?= $coupon['value'] ?>%</span>
                                        <?php else: ?>
                                            <span class="value-text">Giảm <?= number_format($coupon['value']) ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="voucher-footer">
                                    <div class="d-flex justify-content-between">
                                        <?php if ($coupon['valid_until']): ?>
                                            <div class="voucher-expiry">
                                                <i class="fas fa-clock"></i> HSD: <?= date('d/m/Y', strtotime($coupon['valid_until'])) ?>
                                            </div>
                                        <?php endif; ?>

                                        <button class="btn btn-outline-danger btn-sm remove-voucher" data-coupon-id="<?= $coupon['coupon_id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.voucher-card {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.voucher-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.voucher-card.trending {
    border-color: #dc3545;
    background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
}

.voucher-card.saved {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
}

.voucher-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 15px;
    position: relative;
}

.voucher-card.trending .voucher-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.voucher-card.saved .voucher-header {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.voucher-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,255,255,0.2);
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.voucher-code {
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 2px;
    text-align: center;
}

.voucher-usage {
    text-align: right;
    font-size: 12px;
    opacity: 0.9;
    margin-top: 5px;
}

.voucher-body {
    padding: 20px;
    flex-grow: 1;
}

.voucher-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.voucher-value {
    margin-bottom: 10px;
}

.value-text {
    font-size: 24px;
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
    margin-bottom: 10px;
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

.voucher-expiry {
    font-size: 12px;
    color: #6c757d;
}

.voucher-actions {
    display: flex;
    gap: 5px;
}

.voucher-filters {
    display: flex;
    gap: 5px;
}

.voucher-filters .btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

@media (max-width: 768px) {
    .voucher-card {
        margin-bottom: 15px;
    }

    .voucher-filters {
        margin-top: 10px;
        width: 100%;
        justify-content: center;
    }

    .voucher-filters .btn {
        flex: 1;
    }
}
/* Nút đã lưu màu xanh đẹp */
.btn-saved {
    background: #6fc49b !important;
    color: #fff !important;
    border: none !important;
    box-shadow: none;
    font-weight: 600;
    transition: background 0.2s;
}
.btn-saved:disabled,
.btn-saved[disabled] {
    background: #6fc49b !important;
    color: #fff !important;
    opacity: 1 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('.voucher-filters .btn');
    const voucherItems = document.querySelectorAll('.voucher-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;

            voucherItems.forEach(item => {
                if (filter === 'all' || item.dataset.type === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Save voucher functionality
    document.querySelectorAll('.save-voucher').forEach(button => {
        button.addEventListener('click', function() {
            const couponId = this.dataset.couponId;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

            fetch('/5s-fashion/api/voucher/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `coupon_id=${couponId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Đổi tất cả nút cùng couponId thành Đã lưu (disable, màu xanh)
                    document.querySelectorAll('.save-voucher[data-coupon-id="' + couponId + '"]').forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-check"></i> Đã lưu';
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-saved');
                    });
                    showToast('success', data.message);
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
                showToast('error', 'Có lỗi xảy ra khi lưu voucher');
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
                    this.closest('.voucher-item, .col-md-4').remove();
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

    // Share voucher functionality
    document.querySelectorAll('.share-voucher').forEach(button => {
        button.addEventListener('click', function() {
            const couponCode = this.dataset.couponCode;
            const couponName = this.dataset.couponName;

            const shareText = `Nhận ngay voucher ${couponName} - Mã: ${couponCode} tại 5S Fashion!`;
            const shareUrl = window.location.origin + '/5s-fashion/vouchers';

            if (navigator.share) {
                navigator.share({
                    title: '5S Fashion - Voucher Khuyến Mãi',
                    text: shareText,
                    url: shareUrl,
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(`${shareText} ${shareUrl}`).then(() => {
                    showToast('success', 'Đã copy link chia sẻ vào clipboard');
                });
            }
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

    // Add toast styles
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;

    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
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
</script>
