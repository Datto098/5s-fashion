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

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
