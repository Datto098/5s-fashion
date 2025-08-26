<?php
// Start output buffering for content
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-slider swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?= asset('images/hero/banner1.jpg') ?>') center/cover;">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="hero-content text-white">
                                    <h1 class="hero-title mb-3">Bộ Sưu Tập Thu Đông 2024</h1>
                                    <p class="hero-subtitle mb-4">Khám phá những xu hướng thời trang mới nhất với chất lượng cao cấp và thiết kế hiện đại.</p>
                                    <div class="hero-actions">
                                        <a href="<?= url('shop') ?>" class="btn btn-primary btn-lg me-3">Mua Ngay</a>
                                        <a href="<?= url('shop?featured=1') ?>" class="btn btn-outline-light btn-lg">Sản Phẩm Nổi Bật</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?= asset('images/hero/banner2.jpg') ?>') center/cover;">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-6">
                                <div class="hero-content text-white text-end">
                                    <h1 class="hero-title mb-3">Sale Up To 50%</h1>
                                    <p class="hero-subtitle mb-4">Cơ hội vàng để sở hữu những item yêu thích với giá ưu đãi không thể bỏ qua.</p>
                                    <div class="hero-actions">
                                        <a href="<?= url('shop?sale=1') ?>" class="btn btn-danger btn-lg me-3">Mua Ngay</a>
                                        <a href="<?= url('shop') ?>" class="btn btn-outline-light btn-lg">Xem Tất Cả</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?= asset('images/hero/banner3.jpg') ?>') center/cover;">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="hero-content text-white">
                                    <h1 class="hero-title mb-3">Thời Trang Công Sở</h1>
                                    <p class="hero-subtitle mb-4">Tự tin và chuyên nghiệp với bộ sưu tập thời trang công sở cao cấp.</p>
                                    <div class="hero-actions">
                                        <a href="<?= url('shop?category=cong-so') ?>" class="btn btn-primary btn-lg me-3">Khám Phá</a>
                                        <a href="<?= url('contact') ?>" class="btn btn-outline-light btn-lg">Tư Vấn</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slider Navigation -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shipping-fast fa-3x text-primary"></i>
                    </div>
                    <h5 class="feature-title">Giao Hàng Nhanh</h5>
                    <p class="feature-desc">Miễn phí giao hàng toàn quốc cho đơn hàng từ 500k</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-undo-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="feature-title">Đổi Trả Dễ Dàng</h5>
                    <p class="feature-desc">Đổi trả miễn phí trong vòng 30 ngày</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="feature-title">Bảo Hành Chất Lượng</h5>
                    <p class="feature-desc">Cam kết chất lượng với chế độ bảo hành tốt nhất</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset fa-3x text-primary"></i>
                    </div>
                    <h5 class="feature-title">Hỗ Trợ 24/7</h5>
                    <p class="feature-desc">Đội ngũ tư vấn chuyên nghiệp luôn sẵn sàng hỗ trợ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Vouchers Section -->
<?php if (!empty($featured_vouchers)): ?>
<section class="voucher-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-fire text-danger me-2"></i>Voucher Hot Giảm Giá</h2>
                <p style="text-align: center; color: #666; font-size: 16px; margin-bottom: 40px;">
                    Thu thập ngay các voucher ưu đãi để tiết kiệm cho đơn hàng tiếp theo!
                </p>
            </div>
        </div>
        <div class="row justify-content-center">
            <?php
            // Đảm bảo mảng voucher IDs đã được khởi tạo
            if (!isset($saved_voucher_ids)) $saved_voucher_ids = [];
            if (!isset($used_voucher_ids)) $used_voucher_ids = [];
            
            foreach ($featured_vouchers as $index => $voucher):
                $is_saved = in_array($voucher['id'], $saved_voucher_ids);
                $is_used = in_array($voucher['id'], $used_voucher_ids);
                $has_saved = $is_saved || $is_used; // Voucher is either saved or used
            ?>
                <div class="col-lg-5 col-md-6 mb-4">
                    <div class="voucher-card <?= $voucher['type'] === 'fixed' ? 'fixed' : '' ?><?= $has_saved ? ' saved' : '' ?><?= $is_used ? ' used' : '' ?>" data-voucher-id="<?= $voucher['id'] ?>">
                        <!-- Decorative elements -->
                        <div class="decoration-1"></div>
                        <div class="decoration-2"></div>

                        <!-- Voucher Header -->
                        <div class="voucher-header">
                            <div class="voucher-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="voucher-content">
                                <div class="voucher-discount">
                                    <?php if ($voucher['type'] === 'percentage'): ?>
                                        <?= number_format($voucher['value']) ?>% OFF
                                    <?php else: ?>
                                        -<?= number_format($voucher['value']) ?>đ
                                    <?php endif; ?>
                                </div>
                                <div class="voucher-title"><?= htmlspecialchars($voucher['name']) ?></div>
                            </div>
                        </div>

                        <!-- Voucher Description -->
                        <div class="voucher-description">
                            <?php if ($voucher['minimum_amount']): ?>
                                Áp dụng cho đơn hàng từ <?= number_format($voucher['minimum_amount']) ?>đ
                            <?php else: ?>
                                Áp dụng cho mọi đơn hàng
                            <?php endif; ?>
                        </div>

                        <!-- Voucher Code -->
                        <div class="voucher-code">
                            <?= $voucher['code'] ?>
                        </div>

                        <!-- Voucher Footer -->
                        <div class="voucher-footer">
                            <div class="voucher-expiry">
                                <i class="far fa-clock"></i>
                                HSD: <?= date('d.m.Y', strtotime($voucher['valid_until'])) ?>
                            </div>
                            <div class="voucher-actions">
                                <button class="btn-save-voucher<?= $has_saved ? ' disabled' : '' ?>" data-coupon-id="<?= $voucher['id'] ?>"<?= $has_saved ? ' disabled' : '' ?>>
                                    <i class="fas fa-bookmark"></i>
                                    <?php if ($is_used): ?>
                                        <span title="Bạn đã sử dụng voucher này">Đã dùng</span>
                                    <?php elseif ($is_saved): ?>
                                        <span>Đã lưu</span>
                                    <?php else: ?>
                                        <span>Lưu mã</span>
                                    <?php endif; ?>
                                </button>
                                <button class="btn-copy-code" data-code="<?= $voucher['code'] ?>">
                                    <i class="fas fa-copy"></i>
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/5s-fashion/vouchers" class="btn btn-outline-primary">
                <i class="fas fa-ticket-alt me-2"></i>
                Xem tất cả voucher
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Categories -->
<?php if (!empty($featured_categories)): ?>
<section class="categories-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Danh Mục Nổi Bật</h2>
                    <p class="section-subtitle">Khám phá những danh mục thời trang hot nhất</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($featured_categories as $category): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="category-card">
                        <a href="<?= url('shop?category=' . urlencode($category['slug'])) ?>" class="category-link">
                            <div class="category-image">
                                <?php if (!empty($category['image'])): ?>
                                    <?php
                                    // Handle image path for file server
                                    $imagePath = $category['image'];
                                    if (strpos($imagePath, '/uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 9);
                                    } elseif (strpos($imagePath, 'uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 8);
                                    } else {
                                        $cleanPath = ltrim($imagePath, '/');
                                    }
                                    $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                    ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>"
                                         alt="<?= htmlspecialchars($category['name']) ?>" class="img-fluid">
                                <?php else: ?>
                                    <img src="<?= asset('images/no-image.jpg') ?>"
                                         alt="<?= htmlspecialchars($category['name']) ?>" class="img-fluid">
                                <?php endif; ?>
                                <div class="category-overlay">
                                    <div class="category-content">
                                        <h4 class="category-name"><?= htmlspecialchars($category['name']) ?></h4>
                                        <p class="category-desc"><?= htmlspecialchars(truncate($category['description'], 80)) ?></p>
                                        <span class="btn btn-light btn-sm">Xem Sản Phẩm</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products -->
<?php if (!empty($featured_products)): ?>
<section class="products-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
                    <p class="section-subtitle">Những sản phẩm được yêu thích nhất</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <?php include VIEW_PATH . '/client/partials/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="<?= url('shop?featured=1') ?>" class="btn btn-primary btn-lg">Xem Tất Cả Sản Phẩm Nổi Bật</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- New Arrivals -->
<?php if (!empty($new_arrivals)): ?>
<section class="products-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Hàng Mới Về</h2>
                    <p class="section-subtitle">Cập nhật xu hướng thời trang mới nhất</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($new_arrivals as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <?php include VIEW_PATH . '/client/partials/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="<?= url('shop?sort=latest') ?>" class="btn btn-outline-primary btn-lg">Xem Thêm Hàng Mới</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Sale Products -->
<?php if (!empty($sale_products)): ?>
<section class="products-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Flash Sale</h2>
                    <p class="section-subtitle">Giảm giá sốc - Số lượng có hạn</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($sale_products as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <?php include VIEW_PATH . '/client/partials/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="<?= url('shop?sale=1') ?>" class="btn btn-primary btn-lg">Xem Tất Cả Sale</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Get the buffered content
$content = ob_get_clean();

// Custom CSS and JS for homepage
$custom_css = ['css/homepage.css'];
$custom_js = ['js/homepage.js'];

// Set additional data
$show_breadcrumb = false;

// Inline CSS for voucher section
$inline_css = "
.voucher-section {
    margin: 50px 0;
    padding: 0 15px;
}

.voucher-section h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
    font-weight: 600;
    position: relative;
}

.voucher-section h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #ff6b6b, #ffa726);
    border-radius: 2px;
}

.voucher-card {
    position: relative;
    background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 50%, #ffa726 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(255, 107, 107, 0.3);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 3px solid transparent;
    background-clip: padding-box;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Saved voucher styling */
.voucher-card.saved {
    border: 3px solid #28a745;
    box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
    position: relative;
}

// .voucher-card.saved::before {
//     content: '✓';
//     position: absolute;
//     top: -15px;
//     right: -15px;
//     width: 40px;
//     height: 40px;
//     background-color: #28a745;
//     color: white;
//     border-radius: 50%;
//     display: flex;
//     align-items: center;
//     justify-content: center;
//     font-weight: bold;
//     font-size: 20px;
//     box-shadow: 0 3px 10px rgba(0,0,0,0.2);
//     transform: scale(0);
//     animation: popIn 0.5s forwards;
//     z-index: 2;
// }

/* Used voucher styling */
.voucher-card.used {
    border: 3px solid #6c757d;
    box-shadow: 0 10px 25px rgba(108, 117, 125, 0.4);
    position: relative;
}

// .voucher-card.used::before {
//     content: '✓';
//     position: absolute;
//     top: -15px;
//     right: -15px;
//     width: 40px;
//     height: 40px;
//     background-color: #6c757d;
//     color: white;
//     border-radius: 50%;
//     display: flex;
//     align-items: center;
//     justify-content: center;
//     font-weight: bold;
//     font-size: 20px;
//     box-shadow: 0 3px 10px rgba(0,0,0,0.2);
//     transform: scale(0);
//     animation: popIn 0.5s forwards;
//     z-index: 2;
// }

.voucher-card.used {
    position: relative;
}

.voucher-card.used::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.1);
    z-index: 1;
    border-radius: 17px;
    pointer-events: none;
}

.voucher-card.used .btn-save-voucher {
    background-color: #6c757d !important;
    cursor: default;
}

/* Badge for used vouchers */

@keyframes popIn {
    0% { transform: scale(0); }
    70% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.voucher-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 0% 50%, transparent 12px, white 13px, white 15px, transparent 16px),
                radial-gradient(circle at 100% 50%, transparent 12px, white 13px, white 15px, transparent 16px);
    background-size: 25px 25px;
    background-position: 0% 50%, 100% 50%;
    background-repeat: repeat-y;
    pointer-events: none;
    border-radius: 20px;
}

.voucher-card::after {
    content: '';
    position: absolute;
    top: 50%;
    left: -8px;
    right: -8px;
    height: 16px;
    background: radial-gradient(circle at 50% 50%, transparent 8px, rgba(255,255,255,0.1) 9px, rgba(255,255,255,0.1) 10px, transparent 11px);
    background-size: 16px 16px;
    background-repeat: repeat-x;
    transform: translateY(-50%);
    pointer-events: none;
}

.voucher-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px rgba(255, 107, 107, 0.4);
}

.voucher-card.fixed {
    background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 50%, #81C784 100%);
    box-shadow: 0 15px 35px rgba(76, 175, 80, 0.3);
}

.voucher-card.fixed:hover {
    box-shadow: 0 25px 50px rgba(76, 175, 80, 0.4);
}

.voucher-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.voucher-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
    backdrop-filter: blur(10px);
}

.voucher-content {
    flex: 1;
}

.voucher-discount {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 8px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    line-height: 1;
}

.voucher-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
    opacity: 0.95;
}

.voucher-description {
    font-size: 13px;
    opacity: 0.85;
    margin-bottom: 15px;
    line-height: 1.4;
}

.voucher-code {
    background: rgba(255, 255, 255, 0.25);
    padding: 8px 15px;
    border-radius: 25px;
    display: inline-block;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 15px;
    border: 2px dashed rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
}

.voucher-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.voucher-expiry {
    font-size: 12px;
    opacity: 0.8;
    font-weight: 500;
}

.voucher-actions {
    display: flex;
    gap: 12px;
}

.btn-save-voucher, .btn-copy-code {
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 100px;
    justify-content: center;
}

.btn-save-voucher {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
}

.btn-save-voucher:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-copy-code {
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-copy-code:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.btn-save-voucher:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

/* Decorative elements */
.voucher-card .decoration-1 {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 60px;
    height: 60px;
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 50%;
    transform: rotate(45deg);
}

.voucher-card .decoration-2 {
    position: absolute;
    bottom: 15px;
    left: 15px;
    width: 30px;
    height: 30px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

@media (max-width: 768px) {
    .voucher-card {
        margin-bottom: 25px;
        min-height: 160px;
        padding: 20px;
    }

    .voucher-discount {
        font-size: 28px;
    }

    .voucher-actions {
        flex-direction: column;
        gap: 8px;
    }

    .btn-save-voucher, .btn-copy-code {
        width: 100%;
        min-width: auto;
    }

    .voucher-footer {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
}

@media (max-width: 576px) {
    .voucher-section {
        margin: 30px 0;
        padding: 0 10px;
    }

    .voucher-card {
        padding: 18px;
        min-height: 140px;
    }

    .voucher-discount {
        font-size: 24px;
    }

    .voucher-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
}
";

$inline_js = <<<'JS'
document.querySelectorAll('.btn-save-voucher').forEach(button => {
    const couponId = parseInt(button.getAttribute('data-coupon-id'), 10);
    const voucherCard = button.closest('.voucher-card');
    if (voucherCard.classList.contains('used')) {
        button.disabled = true;
        button.classList.add('disabled');
    } else if (voucherCard.classList.contains('saved')) {
        button.disabled = true;
        button.classList.add('disabled');
    } else if (!button.disabled) {
        button.addEventListener('click', function() {
            const btn = this;
            if (!couponId || isNaN(couponId)) {
                showToast('Mã voucher không hợp lệ', 'error');
                return;
            }
            fetch('/5s-fashion/api/auth/check')
                .then(response => response.json())
                .then(data => {
                    if (!data.data.authenticated) {
                        showToast('Vui lòng đăng nhập để lưu voucher', 'warning');
                        return Promise.reject('not-auth');
                    }
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
                    btn.disabled = true;
                    return fetch('/5s-fashion/api/voucher/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ coupon_id: couponId })
                    });
                })
                .then(response => {
                    // remove any existing alerts
                    const existingAlerts = document.querySelectorAll('.alert');
                    existingAlerts.forEach(alert => alert.remove());

                    if (!response) return null;
                    // try to parse JSON; if parse fails, treat as error
                    return response.json().catch(err => {
                        console.error('Invalid JSON response for voucher save', err);
                        return null;
                    });

                })
                .then(result => {
                    if (!result) return;
                    if (result.success) {
                        btn.innerHTML = '<i class="fas fa-bookmark"></i> Đã lưu';
                        btn.disabled = true;
                        btn.classList.add('disabled');
                        voucherCard.classList.add('saved');
                        showToast(result.message || 'Voucher đã được lưu vào tài khoản của bạn!', 'success');
                    } else {
                        if (result.code === 'ALREADY_SAVED' || (result.message && result.message.includes('đã lưu'))) {
                            btn.innerHTML = '<i class="fas fa-bookmark"></i> Đã lưu';
                            btn.disabled = true;
                            btn.classList.add('disabled');
                            voucherCard.classList.add('saved');
                            showToast(result.message || 'Bạn đã lưu voucher này rồi', 'info');
                        }
                        else if (result.code === 'NOT_LOGGED_IN') {
                            showToast('Vui lòng đăng nhập để lưu voucher', 'warning');
                        } 
                        else if (result.code === 'ALREADY_USED'|| (result.message && result.message.includes('đã dùng'))) {
                            btn.innerHTML = '<i class="fas fa-check"></i> Đã dùng';
                            btn.disabled = true;
                            btn.classList.add('disabled');
                            voucherCard.classList.add('used');
                            showToast(result.message || 'Voucher này đã được sử dụng', 'info');
                        }
                        else {
                            btn.innerHTML = '<i class="far fa-bookmark"></i> Lưu mã';
                            btn.disabled = false;
                            showToast(result.message || 'Không thể lưu voucher', 'error');
                        }
                    }
                })
                .catch(error => {
                    if (error !== 'not-auth') {
                        console.error('Error:', error);
                        showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
                        // Reset button
                        btn.innerHTML = '<i class="far fa-bookmark"></i> Lưu mã';
                        btn.disabled = false;
                    }
                });
        });
    }
});

// Copy voucher code functionality
document.querySelectorAll('.btn-copy-code').forEach(button => {
    button.addEventListener('click', function() {
        const code = this.getAttribute('data-code');
        const btn = this;
        
        // Change button appearance immediately for better feedback
        const originalText = btn.innerHTML;
        const originalBackground = btn.style.background;
        const originalColor = btn.style.color;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang sao chép...';
        
        const copyCode = () => {
            // Show successful copy effect
            btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
            btn.style.background = '#28a745';
            btn.style.color = 'white';
            
            // Show toast notification
            showToast('<strong>' + code + '</strong> đã được sao chép vào clipboard', 'success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = originalBackground;
                btn.style.color = originalColor;
            }, 2000);
        };

        if (navigator.clipboard) {
            navigator.clipboard.writeText(code)
                .then(copyCode)
                .catch(err => {
                    console.error('Could not copy text: ', err);
                    showToast('Không thể sao chép, vui lòng thử lại!', 'error');
                    btn.innerHTML = originalText;
                });
        } else {
            try {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = code;
                textArea.style.position = 'fixed';  // Prevent scrolling to the element
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                
                if (successful) {
                    copyCode();
                } else {
                    throw new Error('Copy command was unsuccessful');
                }
            } catch (err) {
                console.error('Could not copy text: ', err);
                showToast('Không thể sao chép, vui lòng thử lại!', 'error');
                btn.innerHTML = originalText;
            }
        }
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;

    let iconClass = 'info-circle';
    if (type === 'success') iconClass = 'check-circle';
    else if (type === 'warning') iconClass = 'exclamation-triangle';
    else if (type === 'error') iconClass = 'times-circle';

    // Add close button to toast
    toast.innerHTML = '<div class="toast-content">' +
        '<i class="fas fa-' + iconClass + '"></i>' +
        '<span>' + message + '</span>' +
        '</div>' +
        '<button class="toast-close"><i class="fas fa-times"></i></button>';

    // Add toast styles
    let bgColor = '#17a2b8';
    let textColor = 'white';
    let borderColor = '#0f8599';
    
    if (type === 'success') {
        bgColor = '#d4edda';
        textColor = '#155724';
        borderColor = '#c3e6cb';
    } else if (type === 'warning') {
        bgColor = '#fff3cd';
        textColor = '#856404';
        borderColor = '#ffeeba';
    } else if (type === 'error') {
        bgColor = '#f8d7da';
        textColor = '#721c24';
        borderColor = '#f5c6cb';
    } else { // info
        bgColor = '#d1ecf1';
        textColor = '#0c5460';
        borderColor = '#bee5eb';
    }

    toast.style.cssText = `
        position: fixed; 
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        padding: 15px 20px; 
        background: ${bgColor}; 
        color: ${textColor}; 
        border-left: 4px solid ${borderColor};
        border-radius: 8px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
        transform: translateY(-20px); 
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease; 
        font-size: 16px; 
        font-weight: 500;
        max-width: 350px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    `;
    
    // Style for toast content
    const toastContent = toast.querySelector('.toast-content');
    toastContent.style.cssText = `
        display: flex;
        align-items: center;
        gap: 12px;
    `;
    
    // Style for close button
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.style.cssText = `
        background: transparent;
        border: none;
        color: ${textColor};
        cursor: pointer;
        padding: 0;
        margin-left: 15px;
        opacity: 0.7;
    `;
    
    // Add click handler to close button
    closeBtn.addEventListener('click', () => {
        hideToast(toast);
    });

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 100);

    // Remove after 5 seconds (longer display time for better visibility)
    const toastTimeout = setTimeout(() => {
        hideToast(toast);
    }, 5000);
    
    // Function to hide toast with animation
    function hideToast(toastElement) {
        toastElement.style.transform = 'translateY(-20px)';
        toastElement.style.opacity = '0';
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.parentNode.removeChild(toastElement);
            }
        }, 300);
        clearTimeout(toastTimeout);
    }
}
JS;

// Add chatbot CSS and JS
$custom_css[] = 'css/chatbot.css';
$custom_css[] = 'css/chatbot-fix.css';
$custom_js[] = 'js/chatbot.js';

// Add chatbot HTML after content
$content .= '
<!-- Chatbot -->
<div id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comments"></i>
</div>

<div id="chatbot-container" class="chatbot-container">
    <div class="chatbot-header">
        <div class="chatbot-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div class="chatbot-info">
            <h4>5S Fashion Assistant</h4>
            <span class="status online">Trực tuyến</span>
        </div>
        <button class="chatbot-close" id="chatbot-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="chatbot-messages" id="chatbot-messages">
        <div class="message bot-message">
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="message-text">
                    Xin chào! Tôi là trợ lý ảo của 5S Fashion. Tôi có thể giúp bạn tìm sản phẩm, tư vấn thời trang, hoặc hỗ trợ mua hàng. Bạn cần hỗ trợ gì?
                </div>
                <div class="message-time">' . date('H:i') . '</div>
            </div>
        </div>
    </div>

    <div class="chatbot-quick-actions">
        <button class="quick-action" data-message="Sản phẩm bán chạy">
            <i class="fas fa-fire"></i>
            Sản phẩm hot
        </button>
        <button class="quick-action" data-message="Sản phẩm giảm giá">
            <i class="fas fa-tags"></i>
            Khuyến mãi
        </button>
        <button class="quick-action" data-message="Sản phẩm mới">
            <i class="fas fa-star"></i>
            Mới về
        </button>
        <button class="quick-action" data-message="Tư vấn thời trang">
            <i class="fas fa-magic"></i>
            Tư vấn
        </button>
        <button class="quick-action" data-message="Hướng dẫn chọn size">
            <i class="fas fa-ruler"></i>
            Size
        </button>
        <button class="quick-action" data-message="Thông tin cửa hàng">
            <i class="fas fa-store"></i>
            Cửa hàng
        </button>
    </div>

    <div class="chatbot-input">
        <input type="text" id="chatbot-input" placeholder="Nhập tin nhắn..." style="max-width: 260px">
        <button id="chatbot-send">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>';

// Add chatbot initialization script
$inline_js .= "
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo chatbot
    window.chatbot = new FSFashionChatbot();
    console.log('Chatbot initialized on page load');
});
";

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
