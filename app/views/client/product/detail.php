<?php
// Product Detail Page for Phase 3.3
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product['name'] ?> - 5S Fashion</title>
    <meta name="description" content="<?= htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao tại 5S Fashion') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($product['name']) ?>, thời trang, 5s fashion">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($product['name']) ?> - 5S Fashion">
    <meta property="og:description" content="<?= htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao tại 5S Fashion') ?>">
    <meta property="og:image" content="<?= $product['image'] ?>">
    <meta property="og:type" content="product">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/client.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/product-detail.css">
</head>
<body>
    <!-- Header -->
    <?php include '../layouts/header.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-section">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/shop">Cửa hàng</a></li>
                <li class="breadcrumb-item"><a href="/shop?category=<?= $product['category_id'] ?>"><?= $product['category_name'] ?? 'Danh mục' ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </div>
    </nav>

    <!-- Product Detail Section -->
    <section class="product-detail-section py-5">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images sticky-top">
                        <!-- Main Image -->
                        <div class="main-image-container">
                            <div class="product-badges">
                                <?php if ($product['is_sale']): ?>
                                <span class="badge sale">
                                    -<?= round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) ?>%
                                </span>
                                <?php endif; ?>
                                <?php if ($product['is_new']): ?>
                                <span class="badge new">Mới</span>
                                <?php endif; ?>
                                <?php if ($product['is_featured']): ?>
                                <span class="badge featured">Nổi bật</span>
                                <?php endif; ?>
                            </div>

                            <div class="main-image">
                                <img src="<?= $product['image'] ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     id="mainProductImage"
                                     class="img-fluid">

                                <!-- Image Actions -->
                                <div class="image-actions">
                                    <button class="action-btn zoom-btn" onclick="openImageZoom()" title="Phóng to">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button class="action-btn favorite-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" title="Yêu thích">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        <div class="thumbnail-gallery">
                            <div class="swiper thumbnail-swiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="<?= $product['image'] ?>"
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             class="thumbnail active"
                                             onclick="changeMainImage(this)">
                                    </div>
                                    <?php if (!empty($product['gallery'])): ?>
                                        <?php foreach ($product['gallery'] as $index => $image): ?>
                                        <div class="swiper-slide">
                                            <img src="<?= $image ?>"
                                                 alt="<?= htmlspecialchars($product['name']) ?> - Ảnh <?= $index + 2 ?>"
                                                 class="thumbnail"
                                                 onclick="changeMainImage(this)">
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Placeholder images for demo -->
                                        <?php for ($i = 2; $i <= 4; $i++): ?>
                                        <div class="swiper-slide">
                                            <img src="<?= $product['image'] ?>"
                                                 alt="<?= htmlspecialchars($product['name']) ?> - Ảnh <?= $i ?>"
                                                 class="thumbnail"
                                                 onclick="changeMainImage(this)">
                                        </div>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <!-- Product Title & Rating -->
                        <div class="product-header">
                            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                            <div class="product-meta">
                                <div class="product-rating">
                                    <?php
                                    $rating = $product['rating'] ?? 4.5;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    ?>
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $fullStars): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-text">(<?= number_format($rating, 1) ?> - <?= $product['review_count'] ?? 0 ?> đánh giá)</span>
                                </div>
                                <div class="product-sku">
                                    SKU: <span class="sku-code"><?= $product['sku'] ?? 'SP' . str_pad($product['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                <div class="product-availability">
                                    <?php if ($product['stock'] > 0): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span class="text-success">Còn hàng (<?= $product['stock'] ?> sản phẩm)</span>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                        <span class="text-danger">Hết hàng</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Product Price -->
                        <div class="product-price">
                            <?php if ($product['is_sale'] && $product['original_price'] > $product['price']): ?>
                                <span class="current-price"><?= number_format($product['price']) ?>đ</span>
                                <span class="original-price"><?= number_format($product['original_price']) ?>đ</span>
                                <span class="discount-percent">-<?= round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) ?>%</span>
                            <?php else: ?>
                                <span class="current-price"><?= number_format($product['price']) ?>đ</span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Description -->
                        <div class="product-description">
                            <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao, thiết kế hiện đại và phù hợp với nhiều phong cách khác nhau.')) ?></p>
                        </div>

                        <!-- Product Variants -->
                        <div class="product-variants">
                            <!-- Size Selection -->
                            <div class="variant-group">
                                <label class="variant-label">Kích thước:</label>
                                <div class="size-options">
                                    <?php
                                    $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                    foreach ($sizes as $size):
                                    ?>
                                    <input type="radio" name="size" value="<?= $size ?>" id="size-<?= $size ?>" <?= $size === 'M' ? 'checked' : '' ?>>
                                    <label for="size-<?= $size ?>" class="size-option"><?= $size ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Color Selection -->
                            <div class="variant-group">
                                <label class="variant-label">Màu sắc:</label>
                                <div class="color-options">
                                    <?php
                                    $colors = [
                                        ['name' => 'Đen', 'code' => '#000000'],
                                        ['name' => 'Trắng', 'code' => '#FFFFFF'],
                                        ['name' => 'Xám', 'code' => '#808080'],
                                        ['name' => 'Xanh dương', 'code' => '#0066CC']
                                    ];
                                    foreach ($colors as $index => $color):
                                    ?>
                                    <input type="radio" name="color" value="<?= $color['name'] ?>" id="color-<?= $index ?>" <?= $index === 0 ? 'checked' : '' ?>>
                                    <label for="color-<?= $index ?>" class="color-option" title="<?= $color['name'] ?>" style="background-color: <?= $color['code'] ?>"></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity & Actions -->
                        <div class="product-actions">
                            <div class="quantity-selector">
                                <label class="quantity-label">Số lượng:</label>
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn decrease" onclick="changeQuantity(-1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" id="productQuantity" value="1" min="1" max="<?= $product['stock'] ?>">
                                    <button type="button" class="quantity-btn increase" onclick="changeQuantity(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button class="btn btn-primary btn-lg add-to-cart" onclick="addToCart(<?= $product['id'] ?>)" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    <?= $product['stock'] > 0 ? 'Thêm vào giỏ' : 'Hết hàng' ?>
                                </button>
                                <button class="btn btn-outline-primary buy-now" onclick="buyNow(<?= $product['id'] ?>)" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-bolt me-2"></i>Mua ngay
                                </button>
                                <button class="btn btn-outline-secondary wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Product Features -->
                        <div class="product-features">
                            <div class="feature-item">
                                <i class="fas fa-truck"></i>
                                <div class="feature-text">
                                    <strong>Miễn phí vận chuyển</strong>
                                    <span>Đơn hàng từ 500.000đ</span>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-undo"></i>
                                <div class="feature-text">
                                    <strong>Đổi trả dễ dàng</strong>
                                    <span>Trong vòng 30 ngày</span>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <div class="feature-text">
                                    <strong>Bảo hành chất lượng</strong>
                                    <span>Cam kết chính hãng</span>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-headset"></i>
                                <div class="feature-text">
                                    <strong>Hỗ trợ 24/7</strong>
                                    <span>Tư vấn miễn phí</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social Share -->
                        <div class="social-share">
                            <span class="share-label">Chia sẻ:</span>
                            <div class="share-buttons">
                                <a href="#" class="share-btn facebook" onclick="shareOnFacebook()">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="share-btn twitter" onclick="shareOnTwitter()">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="share-btn pinterest" onclick="shareOnPinterest()">
                                    <i class="fab fa-pinterest"></i>
                                </a>
                                <a href="#" class="share-btn copy" onclick="copyProductLink()">
                                    <i class="fas fa-link"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Details Tabs -->
    <section class="product-tabs-section py-5 bg-light">
        <div class="container">
            <nav class="product-tabs">
                <div class="nav nav-tabs" id="productTabs" role="tablist">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        Mô tả sản phẩm
                    </button>
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                        Thông số kỹ thuật
                    </button>
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                        Đánh giá (<?= $product['review_count'] ?? 0 ?>)
                    </button>
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">
                        Vận chuyển & Đổi trả
                    </button>
                </div>
            </nav>

            <div class="tab-content" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="tab-content-wrapper">
                        <h4>Mô tả chi tiết</h4>
                        <div class="description-content">
                            <?= nl2br(htmlspecialchars($product['long_description'] ?? 'Sản phẩm chất lượng cao với thiết kế hiện đại, phù hợp với nhiều phong cách khác nhau. Được làm từ chất liệu cao cấp, đảm bảo độ bền và thoải mái khi sử dụng.')) ?>
                        </div>
                        <div class="product-highlights">
                            <h5>Điểm nổi bật:</h5>
                            <ul>
                                <li>Chất liệu cao cấp, thoáng khí</li>
                                <li>Thiết kế hiện đại, thời trang</li>
                                <li>Dễ dàng phối đồ với nhiều trang phục</li>
                                <li>Bền đẹp, không phai màu sau nhiều lần giặt</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <div class="tab-content-wrapper">
                        <h4>Thông số kỹ thuật</h4>
                        <div class="specifications-table">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Thương hiệu</strong></td>
                                        <td><?= $product['brand_name'] ?? '5S Fashion' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Danh mục</strong></td>
                                        <td><?= $product['category_name'] ?? 'Thời trang' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Chất liệu</strong></td>
                                        <td>Cotton 100%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Xuất xứ</strong></td>
                                        <td>Việt Nam</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kích thước</strong></td>
                                        <td>S, M, L, XL, XXL</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Màu sắc</strong></td>
                                        <td>Đen, Trắng, Xám, Xanh dương</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="reviews-summary">
                            <div class="rating-overview">
                                <div class="overall-rating">
                                    <div class="rating-number"><?= number_format($rating, 1) ?></div>
                                    <div class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $fullStars): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="rating-text"><?= $product['review_count'] ?? 0 ?> đánh giá</div>
                                </div>
                                <div class="rating-breakdown">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <div class="rating-bar">
                                        <span class="star-label"><?= $i ?> sao</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= rand(10, 80) ?>%"></div>
                                        </div>
                                        <span class="count"><?= rand(1, 20) ?></span>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Sample Reviews -->
                        <div class="reviews-list">
                            <div class="review-item">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="reviewer-details">
                                        <h6>Nguyễn Văn A</h6>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div class="review-date">15/01/2024</div>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <p>Sản phẩm rất đẹp và chất lượng tốt. Vải mềm mại, thoáng khí. Giao hàng nhanh, đóng gói cẩn thận.</p>
                                </div>
                            </div>

                            <div class="review-item">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="reviewer-details">
                                        <h6>Trần Thị B</h6>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <div class="review-date">10/01/2024</div>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <p>Thiết kế đẹp, size chuẩn. Tuy nhiên màu sắc hơi khác so với hình ảnh một chút.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Write Review -->
                        <div class="write-review">
                            <h5>Viết đánh giá</h5>
                            <form class="review-form">
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá của bạn</label>
                                    <div class="star-rating">
                                        <input type="radio" name="rating" value="5" id="star5">
                                        <label for="star5"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="reviewText" class="form-label">Nội dung đánh giá</label>
                                    <textarea class="form-control" id="reviewText" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Shipping Tab -->
                <div class="tab-pane fade" id="shipping" role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Chính sách vận chuyển</h4>
                                <ul class="policy-list">
                                    <li><i class="fas fa-check text-success"></i> Miễn phí vận chuyển cho đơn hàng từ 500.000đ</li>
                                    <li><i class="fas fa-check text-success"></i> Giao hàng toàn quốc trong 2-5 ngày làm việc</li>
                                    <li><i class="fas fa-check text-success"></i> Giao hàng nhanh trong ngày tại Hà Nội & TP.HCM</li>
                                    <li><i class="fas fa-check text-success"></i> Kiểm tra hàng trước khi thanh toán</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4>Chính sách đổi trả</h4>
                                <ul class="policy-list">
                                    <li><i class="fas fa-check text-success"></i> Đổi trả miễn phí trong 30 ngày</li>
                                    <li><i class="fas fa-check text-success"></i> Sản phẩm chưa qua sử dụng, còn nguyên tem mác</li>
                                    <li><i class="fas fa-check text-success"></i> Hỗ trợ đổi size miễn phí 1 lần</li>
                                    <li><i class="fas fa-check text-success"></i> Hoàn tiền 100% nếu lỗi từ nhà sản xuất</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <section class="related-products-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Sản phẩm liên quan</h2>
                <p class="section-subtitle">Khám phá thêm những sản phẩm tương tự</p>
            </div>

            <div class="row">
                <?php foreach ($related_products as $relatedProduct): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <?php
                    $product = $relatedProduct; // For product-card partial
                    include '../partials/product-card.php';
                    ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <?php include '../layouts/footer.php'; ?>

    <!-- Image Zoom Modal -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="" class="img-fluid" id="zoomImage">
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/product-detail.js"></script>

    <!-- Cart Sidebar -->
    <?php include '../partials/cart-sidebar.php'; ?>

    <!-- Product Data for JavaScript -->
    <script>
        window.productData = {
            id: <?= $product['id'] ?>,
            name: <?= json_encode($product['name']) ?>,
            price: <?= $product['price'] ?>,
            image: <?= json_encode($product['image']) ?>,
            stock: <?= $product['stock'] ?>,
            url: <?= json_encode($_SERVER['REQUEST_URI']) ?>
        };
    </script>
</body>
</html>
