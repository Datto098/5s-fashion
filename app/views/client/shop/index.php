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
                    <h1 class="shop-title">Cửa Hàng</h1>
                    <p class="shop-subtitle">Khám phá bộ sưu tập thời trang đa dạng của chúng tôi</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="shop-stats">
                        <span class="result-count">Hiển thị <strong id="showing-count">1-12</strong> trong <strong id="total-count"><?= $totalProducts ?></strong> sản phẩm</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Content -->
    <section class="shop-section py-5">
        <div class="container">
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
                                        <input type="range" class="form-range" id="price-slider" min="0" max="5000000" step="50000">
                                        <div class="price-labels">
                                            <span>0đ</span>
                                            <span>5,000,000đ</span>
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

                        <!-- Size Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-ruler me-2"></i>Kích thước
                            </h5>
                            <div class="filter-content">
                                <div class="size-options">
                                    <?php
                                    $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                    foreach ($sizes as $size):
                                    ?>
                                    <label class="size-option">
                                        <input type="checkbox" name="size" value="<?= $size ?>">
                                        <span class="size-label"><?= $size ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Color Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-palette me-2"></i>Màu sắc
                            </h5>
                            <div class="filter-content">
                                <div class="color-options">
                                    <?php
                                    $colors = [
                                        ['name' => 'Đen', 'code' => '#000000'],
                                        ['name' => 'Trắng', 'code' => '#FFFFFF'],
                                        ['name' => 'Xám', 'code' => '#808080'],
                                        ['name' => 'Đỏ', 'code' => '#FF0000'],
                                        ['name' => 'Xanh dương', 'code' => '#0000FF'],
                                        ['name' => 'Xanh lá', 'code' => '#008000'],
                                        ['name' => 'Vàng', 'code' => '#FFFF00'],
                                        ['name' => 'Hồng', 'code' => '#FFC0CB']
                                    ];
                                    foreach ($colors as $color):
                                    ?>
                                    <label class="color-option" title="<?= $color['name'] ?>">
                                        <input type="checkbox" name="color" value="<?= $color['name'] ?>">
                                        <span class="color-swatch" style="background-color: <?= $color['code'] ?>"></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-group">
                            <h5 class="filter-title">
                                <i class="fas fa-star me-2"></i>Đánh giá
                            </h5>
                            <div class="filter-content">
                                <div class="rating-filter">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <label class="rating-option">
                                        <input type="radio" name="rating" value="<?= $i ?>">
                                        <div class="rating-stars">
                                            <?php for ($j = 1; $j <= 5; $j++): ?>
                                            <i class="fas fa-star <?= $j <= $i ? 'active' : '' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="rating-text">từ <?= $i ?> sao</span>
                                        </div>
                                    </label>
                                    <?php endfor; ?>
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
                                     data-brand="<?= $product['brand_id'] ?>"
                                     data-price="<?= $product['price'] ?>"
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
