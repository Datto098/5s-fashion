<?php
// Start output buffering for content
ob_start();
?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-section">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cửa hàng</li>
            </ol>
        </div>
    </nav>

    <!-- Shop Header -->
    <section class="shop-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <?php if (isset($searchQuery) && !empty($searchQuery)): ?>
                        <h1 class="shop-title">Kết quả tìm kiếm</h1>
                        <p class="shop-subtitle">Tìm kiếm cho: "<strong><?= htmlspecialchars($searchQuery) ?></strong>"</p>
                    <?php else: ?>
                        <h1 class="shop-title">Cửa Hàng</h1>
                        <p class="shop-subtitle">Khám phá bộ sưu tập thời trang đa dạng của chúng tôi</p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="shop-stats">
                        <?php if (isset($searchQuery) && !empty($searchQuery)): ?>
                            <span class="result-count">Tìm thấy <strong id="total-count"><?= $totalProducts ?></strong> sản phẩm</span>
                        <?php else: ?>
                            <span class="result-count">Hiển thị <strong id="showing-count">1-12</strong> trong <strong id="total-count"><?= $totalProducts ?></strong> sản phẩm</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (isset($searchQuery) && !empty($searchQuery) && $totalProducts == 0): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="search-no-results alert alert-warning">
                        <i class="fas fa-search me-2"></i>
                        <strong>Không tìm thấy kết quả nào</strong> cho từ khóa "<strong><?= htmlspecialchars($searchQuery) ?></strong>"
                        <br>
                        <small class="mt-2 d-block">Gợi ý: Thử tìm kiếm với từ khóa khác hoặc kiểm tra chính tả</small>
                        <a href="<?= url('shop') ?>" class="ms-2 btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-arrow-left me-1"></i>Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($filters['featured']) && $filters['featured'] == 1): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="featured-banner alert alert-info">
                        <i class="fas fa-star me-2"></i>
                        <strong>Sản Phẩm Nổi Bật:</strong> Đang hiển thị các sản phẩm nổi bật của cửa hàng
                        <a href="<?= url('shop') ?>" class="ms-2 btn btn-sm btn-outline-dark">
                            <i class="fas fa-times me-1"></i>Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Shop Content -->
    <section class="shop-section py-5">
        <div class="container">
            <!-- Hidden category mapping data for JavaScript -->
            <script type="application/json" id="category-data">
                <?php
                    $categoryMapping = [];
                    foreach ($categories as $category) {
                        $categoryMapping[$category['slug']] = $category['id'];
                    }
                    echo json_encode($categoryMapping);
                ?>
            </script>
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="shop-sidebar">
                        <!-- Search Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </h5>
                            <div class="filter-content">
                                <div class="search-box">
                                    <input type="text" class="form-control" id="product-search" placeholder="Tìm kiếm sản phẩm...">
                                    <button class="search-btn" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-list me-2"></i>Danh mục
                            </h5>
                            <div class="filter-content">
                                <div class="category-list">
                                    <label class="filter-checkbox">
                                        <input type="checkbox" name="category" value="all" checked>
                                        <span class="checkmark"></span>
                                        <span class="filter-label">Tất cả</span>
                                        <span class="filter-count">(<?= $totalProducts ?>)</span>
                                    </label>
                                    <?php foreach ($categories as $category): ?>
                                    <label class="filter-checkbox">
                                        <input type="checkbox" name="category" value="<?= $category['id'] ?>">
                                        <span class="checkmark"></span>
                                        <span class="filter-label"><?= htmlspecialchars($category['name']) ?></span>
                                        <span class="filter-count">(<?= $category['product_count'] ?? 0 ?>)</span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-dollar-sign me-2"></i>Khoảng giá
                            </h5>
                            <div class="filter-content">
                                <div class="price-range">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control" id="min-price" placeholder="Từ" min="0">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control" id="max-price" placeholder="Đến" min="0">
                                        </div>
                                    </div>
                                    <div class="price-range-slider mt-3">
                                        <div class="current-price-value mb-2">
                                            Giá tối đa: <span id="current-price-display">2,500,000₫</span>
                                        </div>
                                        <input type="range" class="form-range" id="price-slider" min="0" max="5000000" step="50000" value="2500000">
                                        <div class="price-labels">
                                            <span>0₫</span>
                                            <span>5,000,000₫</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-tags me-2"></i>Thương hiệu
                            </h5>
                            <div class="filter-content">
                                <div class="brand-list">
                                    <?php foreach ($brands as $brand): ?>
                                    <label class="filter-checkbox">
                                        <input type="checkbox" name="brand" value="<?= $brand['id'] ?>">
                                        <span class="checkmark"></span>
                                        <span class="filter-label"><?= htmlspecialchars($brand['name']) ?></span>
                                        <span class="filter-count">(<?= $brand['product_count'] ?? 0 ?>)</span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>




                        <!-- Clear Filters -->
                        <div class="filter-actions">
                            <button type="button" class="btn btn-outline-secondary w-100" id="clear-filters">
                                <i class="fas fa-times me-2"></i>Xóa bộ lọc
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9">
                    <!-- Toolbar -->
                    <div class="shop-toolbar">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="view-options">
                                    <button class="view-btn active" data-view="grid" title="Dạng lưới">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="view-btn" data-view="list" title="Dạng danh sách">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="sort-options">
                                    <select class="form-select" id="sort-select">
                                        <option value="newest">Mới nhất</option>
                                        <option value="oldest">Cũ nhất</option>
                                        <option value="price-asc">Giá thấp đến cao</option>
                                        <option value="price-desc">Giá cao đến thấp</option>
                                        <option value="name-asc">Tên A-Z</option>
                                        <option value="name-desc">Tên Z-A</option>
                                        <option value="rating">Đánh giá cao nhất</option>
                                        <option value="popular">Phổ biến nhất</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Container -->
                    <div class="products-container" id="products-grid">
                        <div class="row g-4" id="products-row">
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                <div class="col-xl-4 col-md-6 product-item"
                                     data-category="<?= $product['category_id'] ?>"
                                     data-category-slug="<?= $product['category_slug'] ?? '' ?>"
                                     data-brand="<?= $product['brand_id'] ?>"
                                     data-price="<?= ($product['sale_price'] > 0) ? $product['sale_price'] : $product['price'] ?>"
                                     data-rating="<?= $product['rating'] ?? 0 ?>"
                                     data-name="<?= strtolower($product['name']) ?>">
                                    <?php include VIEW_PATH . '/client/partials/product-card.php'; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="no-products text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h4>Không tìm thấy sản phẩm nào</h4>
                                        <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác</p>
                                        <button class="btn btn-primary" id="reset-search">
                                            <i class="fas fa-redo me-2"></i>Đặt lại tìm kiếm
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div class="loading-state d-none text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <div class="mt-3">Đang tải sản phẩm...</div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Product pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $queryString ?>" aria-label="Previous">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $queryString ?>" aria-label="Next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php
// Get the buffered content
$content = ob_get_clean();

// Custom CSS and JS for shop page
$custom_css = ['css/shop.css'];
$custom_js = ['js/shop.js'];

// Set additional data
$show_breadcrumb = false;

// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>
