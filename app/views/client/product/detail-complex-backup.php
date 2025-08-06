<?php
// Product Detail Page - Simple Clean Design
// Validate product data
if (!isset($product) || empty($product)) {
    header('HTTP/1.0 404 Not Found');
    echo "Product not found";
    exit;
}

// Prepare image URL
$imageUrl = '/5s-fashion/public/assets/images/default-product.jpg';
if (!empty($product['featured_image'])) {
    $imagePath = $product['featured_image'];
    if (strpos($imagePath, '/uploads/') === 0) {
        $cleanPath = substr($imagePath, 9);
    } elseif (strpos($imagePath, 'uploads/') === 0) {
        $cleanPath = substr($imagePath, 8);
    } else {
        $cleanPath = ltrim($imagePath, '/');
    }
    $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
}

// Custom CSS for product detail page
$inline_css = '
/* Product Detail Page - Consistent with System Design */

/* Breadcrumb consistency */
.breadcrumb-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem 0;
    margin-bottom: 0;
}

.breadcrumb {
    margin-bottom: 0;
    background: transparent;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
}

/* Product Images */
.product-images .main-image-container {
    position: relative;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.product-images .main-image-container:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.main-product-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.main-product-image:hover {
    transform: scale(1.02);
}

.product-badges {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 10;
}

.product-badges .badge {
    margin-right: 8px;
    padding: 6px 12px;
    font-size: 0.75rem;
    border-radius: 20px;
    font-weight: 600;
}

.image-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.image-actions .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.2s ease;
}

.image-actions .btn:hover {
    background: #fff;
    transform: scale(1.1);
}

/* Thumbnail Gallery */
.thumbnail-gallery h6 {
    color: #495057;
    font-weight: 600;
}

.thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
}

.thumbnail:hover,
.thumbnail.active {
    border-color: var(--bs-primary);
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

/* Product Info */
.product-info {
    padding-left: 2rem;
}

.product-title {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.product-meta {
    margin-bottom: 1.5rem;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.5rem;
}

.product-rating .stars {
    color: #ffc107;
}

.rating-text {
    color: #6c757d;
    font-size: 0.9rem;
}

.product-sku,
.product-availability {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.sku-code {
    font-weight: 600;
    color: #495057;
}

/* Price Display */
.product-price {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    text-align: center;
}

.current-price {
    font-size: 2rem;
    font-weight: 700;
    color: #dc3545;
    display: block;
}

.original-price {
    font-size: 1.2rem;
    color: #6c757d;
    text-decoration: line-through;
    margin-left: 10px;
}

.discount-percent {
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 10px;
}

/* Product Description */
.product-description {
    background: #f8f9fa;
    padding: 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--bs-primary);
}

/* Variant Selection */
.attribute-group {
    background: #fff;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}

.attribute-group:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(0,123,255,0.1);
}

.attribute-group label.form-label {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
}

/* Color Options */
.color-options {
    gap: 12px;
}

.color-option-wrapper {
    position: relative;
}

.color-swatch {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    border: 3px solid #e9ecef;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.color-input:checked + .color-option .color-swatch {
    border-color: var(--bs-primary);
    transform: scale(1.1);
    box-shadow: 0 4px 16px rgba(0,123,255,0.3);
}

.color-input:checked + .color-option .color-swatch::after {
    content: "✓";
    position: absolute;
    color: #fff;
    font-weight: bold;
    font-size: 18px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
}

.color-name {
    font-size: 0.75rem;
    color: #6c757d;
    text-align: center;
    margin-top: 8px;
    font-weight: 500;
}

.color-input:checked + .color-option .color-name {
    color: var(--bs-primary);
    font-weight: 600;
}

/* Size Options */
.size-options {
    gap: 10px;
}

.size-option {
    min-width: 65px;
    height: 50px;
    border: 2px solid #dee2e6;
    background: #fff;
    color: #495057;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.size-option:hover {
    border-color: var(--bs-primary);
    color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.size-input:checked + .size-option {
    background: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,123,255,0.3);
}

.size-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f8f9fa;
    color: #6c757d;
}

/* Variant Summary */
.variant-summary .alert {
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-left: 4px solid var(--bs-info);
}

/* Purchase Actions */
.purchase-actions {
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.quantity-selector {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.quantity-label {
    font-weight: 600;
    color: #495057;
    font-size: 1rem;
    margin: 0;
}

.quantity-controls {
    display: flex;
    align-items: center;

    overflow: hidden;
    background: #fff;

}

.quantity-btn {
    background: #fff;
    border: none;
    width: 45px;
    height: 45px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #495057;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:hover {
    background: var(--bs-primary);
    color: white;
}

.quantity-btn:active {
    transform: scale(0.95);
}

.quantity-input {
    border: none;
    width: 80px;
    text-align: center;
    padding: 12px 8px;
    font-weight: 700;
    background: #fff;
    font-size: 1rem;
    color: #495057;
}

.quantity-input:focus {
    outline: none;
    background: #f8f9fa;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 12px;
    align-items: center;
}

.action-buttons .btn {
    padding: 16px 24px;
    font-weight: 600;
    border-radius: 25px;
    font-size: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.action-buttons .btn:active {
    transform: translateY(0);
}

.add-to-cart {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border-color: #dc3545;
    color: white;
    font-size: 0.95rem;
}

.add-to-cart:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    border-color: #bd2130;
    color: white;
}

.buy-now {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-color: #28a745;
    color: white;
    font-size: 0.95rem;
}

.buy-now:hover {
    background: linear-gradient(135deg, #20c997, #17a2b8);
    border-color: #20c997;
    color: white;
}

.wishlist-btn {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 1.2rem;
}

.wishlist-btn:hover {
    background: #ff6b6b;
    border-color: #ff6b6b;
    color: white;
    transform: translateY(-2px) scale(1.05);
}

.wishlist-btn.active {
    background: #ff6b6b;
    border-color: #ff6b6b;
    color: white;
}

.action-buttons .btn .fas,
.action-buttons .btn .far {
    margin-right: 8px;
    font-size: 1rem;
}

/* Button Loading State */
.btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.btn.loading .btn-text {
    opacity: 0;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Product Features */
.product-features {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1.5rem 0;
    border: 1px solid #e9ecef;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item i {
    width: 40px;
    height: 40px;
    background: var(--bs-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1rem;
}

.feature-text strong {
    display: block;
    color: #495057;
    font-weight: 600;
    margin-bottom: 2px;
}

.feature-text span {
    color: #6c757d;
    font-size: 0.9rem;
}

/* Social Share */
.social-share {
    border-top: 1px solid #e9ecef;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
}

.share-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    display: block;
}

.share-buttons {
    display: flex;
    gap: 12px;
}

.share-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    color: white;
}

.share-btn.facebook { background: linear-gradient(135deg, #1877f2, #0d6efd); }
.share-btn.twitter { background: linear-gradient(135deg, #1da1f2, #0d8ce8); }
.share-btn.pinterest { background: linear-gradient(135deg, #bd081c, #a00717); }
.share-btn.copy { background: linear-gradient(135deg, #6c757d, #5a6268); }

/* Tabs */
.product-tabs-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.product-tabs .nav-tabs {
    border: none;
    justify-content: center;
}

.product-tabs .nav-link {
    border: none;
    border-radius: 25px;
    padding: 12px 24px;
    margin: 0 8px;
    color: #6c757d;
    font-weight: 600;
    background: rgba(255,255,255,0.7);
    transition: all 0.3s ease;
}

.product-tabs .nav-link:hover {
    background: rgba(255,255,255,0.9);
    color: var(--bs-primary);
    transform: translateY(-2px);
}

.product-tabs .nav-link.active {
    background: var(--bs-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,123,255,0.3);
}

.tab-content-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    margin-top: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

/* Reviews */
.review-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.2s ease;
}

.review-item:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 4px 16px rgba(0,123,255,0.1);
}

.reviewer-info {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.reviewer-avatar {
    width: 50px;
    height: 50px;
    background: var(--bs-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 15px;
}

.reviewer-details h6 {
    margin: 0;
    font-weight: 600;
    color: #495057;
}

.review-rating {
    margin: 4px 0;
    color: #ffc107;
}

.review-date {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Rating breakdown */
.rating-overview {
    display: flex;
    gap: 3rem;
    align-items: center;
    margin-bottom: 2rem;
}

.overall-rating {
    text-align: center;
}

.rating-number {
    font-size: 3rem;
    font-weight: 700;
    color: var(--bs-primary);
    line-height: 1;
}

.rating-stars {
    margin: 0.5rem 0;
    color: #ffc107;
    font-size: 1.5rem;
}

.rating-text {
    color: #6c757d;
}

.rating-breakdown {
    flex: 1;
}

.rating-bar {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    gap: 1rem;
}

.star-label {
    width: 60px;
    font-size: 0.9rem;
    color: #6c757d;
}

.rating-bar .progress {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
}

.rating-bar .progress-bar {
    background: linear-gradient(90deg, #ffc107, #ffb300);
    border-radius: 4px;
}

.rating-bar .count {
    width: 40px;
    text-align: right;
    font-size: 0.9rem;
    color: #6c757d;
}

/* Review form */
.review-form {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 2rem;
    margin-top: 2rem;
}

.star-rating {
    display: flex;
    gap: 8px;
    margin-bottom: 1rem;
}

.star-rating label {
    font-size: 2rem;
    color: #e9ecef;
    cursor: pointer;
    transition: all 0.2s ease;
}

.star-rating label:hover,
.star-rating input:checked ~ label {
    color: #ffc107;
    transform: scale(1.1);
}

/* Related products */
.related-products-section {
    background: #fff;
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 991px) {
    .product-info {
        padding-left: 0;
        margin-top: 2rem;
    }

    .product-title {
        font-size: 1.75rem;
    }

    .current-price {
        font-size: 1.75rem;
    }

    .rating-overview {
        flex-direction: column;
        gap: 1.5rem;
    }

    .purchase-actions {
        padding: 1.5rem;
    }
}

@media (max-width: 767px) {
    .quantity-selector {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
        text-align: center;
    }

    .quantity-controls {
        justify-content: center;
        width: fit-content;
        margin: 0 auto;
    }

    .action-buttons {
        grid-template-columns: 1fr;
        grid-template-rows: repeat(3, 1fr);
        gap: 12px;
    }

    .wishlist-btn {
        width: 100%;
        height: 50px;
        border-radius: 25px;
        font-size: 1rem;
    }

    .wishlist-btn .far,
    .wishlist-btn .fas {
        margin-right: 8px;
    }

    .wishlist-btn::after {
        content: " Yêu thích";
    }

    .feature-item {
        padding: 8px 0;
    }

    .share-buttons {
        justify-content: center;
    }

    .purchase-actions {
        padding: 1rem;
    }
}
';// Start content capture
ob_start();
?>

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
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <span class="badge bg-danger">Sale</span>
                            <?php endif; ?>
                            <?php if ($product['is_new'] ?? false): ?>
                                <span class="badge bg-info">New</span>
                            <?php endif; ?>
                        </div>

                        <div class="main-image">
                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 class="img-fluid main-product-image"
                                 id="mainProductImage">

                            <!-- Image Actions -->
                            <div class="image-actions">
                                <button class="btn btn-light" onclick="zoomImage('<?= htmlspecialchars($imageUrl) ?>')" title="Phóng to">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Gallery -->
                    <?php
                    // Get additional images from database
                    $additionalImages = [];
                    if (!empty($product['id'])) {
                        $db = Database::getInstance();
                        $imageQuery = "SELECT image_path, alt_text FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC";
                        $dbImages = $db->fetchAll($imageQuery, [$product['id']]);

                        foreach ($dbImages as $img) {
                            if (!empty($img['image_path'])) {
                                $imagePath = $img['image_path'];
                                if (strpos($imagePath, '/uploads/') === 0) {
                                    $cleanPath = substr($imagePath, 9);
                                } elseif (strpos($imagePath, 'uploads/') === 0) {
                                    $cleanPath = substr($imagePath, 8);
                                } else {
                                    $cleanPath = ltrim($imagePath, '/');
                                }
                                $additionalImages[] = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                            }
                        }
                    }

                    // Parse gallery JSON if exists
                    if (!empty($product['gallery'])) {
                        $galleryData = json_decode($product['gallery'], true);
                        if (is_array($galleryData)) {
                            foreach ($galleryData as $img) {
                                if (!empty($img)) {
                                    $imagePath = $img;
                                    if (strpos($imagePath, '/uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 9);
                                    } elseif (strpos($imagePath, 'uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 8);
                                    } else {
                                        $cleanPath = ltrim($imagePath, '/');
                                    }
                                    $additionalImages[] = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                }
                            }
                        }
                    }

                    // Only show thumbnails if we have images
                    $allImages = array_merge([$imageUrl], $additionalImages);
                    $allImages = array_unique($allImages);
                    ?>

                    <?php if (count($allImages) > 1): ?>
                    <div class="thumbnail-gallery">
                        <h6 class="mb-3 text-muted">Hình ảnh sản phẩm (<?= count($allImages) ?> ảnh):</h6>
                        <div class="swiper thumbnail-swiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($allImages as $index => $thumbImage): ?>
                                <div class="swiper-slide">
                                    <img src="<?= htmlspecialchars($thumbImage) ?>"
                                         alt="<?= htmlspecialchars($product['name']) ?> - Ảnh <?= $index + 1 ?>"
                                         class="thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                         onclick="changeMainImage(this)">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($allImages) > 3): ?>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
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
                                <?php if ($product['status'] !== 'out_of_stock'): ?>
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span class="text-success">Còn hàng</span>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger"></i>
                                    <span class="text-danger">Hết hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Product Price -->
                    <div class="product-price" id="product-price">
                        <?php
                        // If product has variants, show price range
                        if (!empty($variants)):
                            $prices = array_map(function($v) use ($product) {
                                return $v['sale_price'] ?: $v['price'] ?: $product['price'];
                            }, $variants);
                            $filteredPrices = array_filter($prices, function($price) { return $price > 0; });

                            if (!empty($filteredPrices)):
                                $minPrice = min($filteredPrices);
                                $maxPrice = max($filteredPrices);

                                if ($minPrice === $maxPrice):
                        ?>
                            <span class="current-price"><?= number_format($minPrice) ?>đ</span>
                        <?php else: ?>
                            <span class="current-price"><?= number_format($minPrice) ?>đ - <?= number_format($maxPrice) ?>đ</span>
                        <?php endif; ?>
                        <?php else: ?>
                            <span class="current-price"><?= number_format($product['price']) ?>đ</span>
                        <?php endif; ?>
                        <?php elseif (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                            <span class="current-price"><?= number_format($product['sale_price']) ?>đ</span>
                            <span class="original-price"><?= number_format($product['price']) ?>đ</span>
                            <span class="discount-percent">-<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>%</span>
                        <?php else: ?>
                            <span class="current-price"><?= number_format($product['current_price'] ?? $product['price']) ?>đ</span>
                        <?php endif; ?>
                    </div>

                    <!-- Stock Information -->
                    <div class="product-stock mb-3" id="product-stock">
                        <?php if (!empty($variants)): ?>
                            <span class="text-muted">Chọn biến thể để xem tình trạng kho</span>
                        <?php else: ?>
                            <?php if ($product['status'] !== 'out_of_stock'): ?>
                                <span class="text-success">Còn hàng</span>
                            <?php else: ?>
                                <span class="text-danger">Hết hàng</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Product Description -->
                    <div class="product-description">
                        <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao, thiết kế hiện đại và phù hợp với nhiều phong cách khác nhau.')) ?></p>
                    </div>

                    <!-- Product Variants -->
                    <?php if (!empty($variants)): ?>
                    <div class="product-variants" id="product-attributes">
                        <?php
                        // Debug: Log variant data
                        error_log("Processing variants: " . json_encode($variants));

                        // Group variants by attributes to get unique values with stock
                        $uniqueColors = [];
                        $uniqueSizes = [];
                        $colorSizeCombos = [];

                        // First, collect all available combinations with stock info from variants
                        foreach ($variants as $variant) {
                            // Each variant should have color and size info from variant_name or attributes
                            if (!empty($variant['variant_name'])) {
                                // Parse variant name like "Áo Sơ Mi Nữ Trắng Công Sở - Đỏ - XS"
                                $parts = explode(' - ', $variant['variant_name']);
                                if (count($parts) >= 3) {
                                    $colorName = trim($parts[count($parts) - 2]);
                                    $sizeName = trim($parts[count($parts) - 1]);
                                    $stock = $variant['stock_quantity'] ?? 0;

                                    // Create color entry
                                    if (!isset($uniqueColors[$colorName])) {
                                        $uniqueColors[$colorName] = [
                                            'id' => $colorName,
                                            'value' => $colorName,
                                            'color_code' => getDefaultColorCode($colorName) // Use helper function
                                        ];
                                    }

                                    // Create size entry
                                    if (!isset($uniqueSizes[$sizeName])) {
                                        $uniqueSizes[$sizeName] = [
                                            'id' => $sizeName,
                                            'value' => $sizeName
                                        ];
                                    }

                                    // Store combination
                                    $colorSizeCombos[] = [
                                        'color_id' => $colorName,
                                        'size_id' => $sizeName,
                                        'color_name' => $colorName,
                                        'size_name' => $sizeName,
                                        'stock' => $stock,
                                        'variant_id' => $variant['id']
                                    ];
                                }
                            }
                        }

                        // Debug log
                        error_log("Unique colors: " . json_encode($uniqueColors));
                        error_log("Unique sizes: " . json_encode($uniqueSizes));
                        error_log("Color-Size combos: " . json_encode($colorSizeCombos));
                        ?>

                        <?php if (!empty($uniqueColors)): ?>
                        <!-- Color Selection -->
                        <div class="attribute-group mb-4">
                            <label class="form-label fw-bold">Màu sắc:</label>
                            <div class="color-options d-flex flex-wrap gap-2">
                                <?php $colorIndex = 0; foreach ($uniqueColors as $colorKey => $colorValue): ?>
                                <div class="color-option-wrapper">
                                    <input type="radio"
                                           name="selected_color"
                                           value="<?= htmlspecialchars($colorKey) ?>"
                                           id="color_<?= htmlspecialchars($colorKey) ?>"
                                           class="color-input d-none"
                                           data-color-name="<?= htmlspecialchars($colorValue['value']) ?>"
                                           <?= $colorIndex === 0 ? 'checked' : '' ?>>
                                    <label for="color_<?= htmlspecialchars($colorKey) ?>"
                                           class="color-option position-relative d-inline-block"
                                           title="<?= htmlspecialchars($colorValue['value']) ?>">
                                        <div class="color-swatch"
                                             style="width: 50px; height: 50px; border-radius: 8px; border: 2px solid #e9ecef;
                                                    background-color: <?= !empty($colorValue['color_code']) ? htmlspecialchars($colorValue['color_code']) : getDefaultColorCode($colorValue['value']) ?>;
                                                    cursor: pointer; transition: all 0.2s ease;
                                                    display: flex; align-items: center; justify-content: center;">
                                            <?php if (empty($colorValue['color_code']) && getDefaultColorCode($colorValue['value']) === '#f8f9fa'): ?>
                                                <span style="font-size: 10px; font-weight: bold; color: #333;"><?= htmlspecialchars(mb_substr($colorValue['value'], 0, 2)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="color-name text-center d-block mt-1" style="font-size: 12px;"><?= htmlspecialchars($colorValue['value']) ?></span>
                                    </label>
                                </div>
                                <?php $colorIndex++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($uniqueSizes)): ?>
                        <!-- Size Selection -->
                        <div class="attribute-group mb-4">
                            <label class="form-label fw-bold">Kích thước:</label>
                            <div class="size-options d-flex flex-wrap gap-2">
                                <?php $sizeIndex = 0; foreach ($uniqueSizes as $sizeKey => $sizeValue): ?>
                                <div class="size-option-wrapper">
                                    <input type="radio"
                                           name="selected_size"
                                           value="<?= htmlspecialchars($sizeKey) ?>"
                                           id="size_<?= htmlspecialchars($sizeKey) ?>"
                                           class="size-input d-none"
                                           data-size-name="<?= htmlspecialchars($sizeValue['value']) ?>"
                                           <?= $sizeIndex === 0 ? 'checked' : '' ?>>
                                    <label for="size_<?= htmlspecialchars($sizeKey) ?>"
                                           class="size-option btn btn-outline-secondary"
                                           style="min-width: 60px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                        <?= htmlspecialchars($sizeValue['value']) ?>
                                    </label>
                                </div>
                                <?php $sizeIndex++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Variant Info -->
                    <div class="variant-summary mb-3" id="variant-summary">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="selected-variant-info">Chọn màu sắc và kích thước</span>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Default variant selection for products without variants -->
                    <div class="product-variants">
                        <!-- Color Selection -->
                        <div class="attribute-group mb-4">
                            <label class="form-label fw-bold">Màu sắc:</label>
                            <div class="color-options d-flex flex-wrap gap-2">
                                <?php
                                $colors = [
                                    ['name' => 'Đen', 'code' => '#000000'],
                                    ['name' => 'Trắng', 'code' => '#FFFFFF'],
                                    ['name' => 'Xám', 'code' => '#808080'],
                                    ['name' => 'Xanh dương', 'code' => '#0066CC'],
                                    ['name' => 'Đỏ', 'code' => '#DC3545']
                                ];
                                foreach ($colors as $index => $color):
                                ?>
                                <div class="color-option-wrapper">
                                    <input type="radio"
                                           name="default_color"
                                           value="<?= $color['name'] ?>"
                                           id="default_color_<?= $index ?>"
                                           class="color-input d-none"
                                           <?= $index === 0 ? 'checked' : '' ?>>
                                    <label for="default_color_<?= $index ?>"
                                           class="color-option d-inline-block position-relative"
                                           title="<?= $color['name'] ?>">
                                        <div class="color-swatch"
                                             style="width: 50px; height: 50px; border-radius: 8px; border: 2px solid #e9ecef;
                                                    background-color: <?= $color['code'] ?>;
                                                    cursor: pointer; transition: all 0.2s ease;
                                                    display: flex; align-items: center; justify-content: center;">
                                        </div>
                                        <span class="color-name text-center d-block mt-1" style="font-size: 12px;"><?= $color['name'] ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Size Selection -->
                        <div class="attribute-group mb-4">
                            <label class="form-label fw-bold">Kích thước:</label>
                            <div class="size-options d-flex flex-wrap gap-2">
                                <?php
                                $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                foreach ($sizes as $index => $size):
                                ?>
                                <div class="size-option-wrapper">
                                    <input type="radio"
                                           name="default_size"
                                           value="<?= $size ?>"
                                           id="default_size_<?= $size ?>"
                                           class="size-input d-none"
                                           <?= $size === 'M' ? 'checked' : '' ?>>
                                    <label for="default_size_<?= $size ?>"
                                           class="size-option btn btn-outline-secondary">
                                        <?= $size ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Purchase Actions -->
                    <div class="purchase-actions">
                        <div class="quantity-selector">
                            <label class="quantity-label">Số lượng:</label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn decrease" onclick="changeQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" id="productQuantity" value="1" min="1" max="99">
                                <button type="button" class="quantity-btn increase" onclick="changeQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="btn add-to-cart" id="add-to-cart-btn" onclick="addToCartWithVariant(<?= $product['id'] ?>)">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="btn-text">Thêm vào giỏ</span>
                            </button>
                            <button class="btn buy-now" onclick="buyNowWithVariant(<?= $product['id'] ?>)">
                                <i class="fas fa-bolt"></i>
                                <span class="btn-text">Mua ngay</span>
                            </button>
                            <button class="btn wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" title="Thêm vào yêu thích">
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
                $product = $relatedProduct;
                include __DIR__ . '/../partials/product-card.php';
                ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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

<?php
// Capture content and set it for layout
$content = ob_get_clean();

// Custom JavaScript for product detail page
$inline_js = '
// Product variants data
const productVariants = ' . json_encode($variants ?? []) . ';
const colorSizeCombos = ' . json_encode($colorSizeCombos ?? []) . ';
const baseProductPrice = ' . ($product['sale_price'] ?: $product['price'] ?: 0) . ';

document.addEventListener("DOMContentLoaded", function() {
    console.log("Variants:", productVariants);
    console.log("Color-Size Combos:", colorSizeCombos);
    console.log("Base Price:", baseProductPrice);

    // Initialize thumbnail swiper
    if (document.querySelector(".thumbnail-swiper")) {
        new Swiper(".thumbnail-swiper", {
            slidesPerView: 3,
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: { slidesPerView: 4 },
                768: { slidesPerView: 5 }
            }
        });
    }

    // Handle color and size selection
    function updateVariantAvailability() {
        const selectedColorId = document.querySelector("input[name=\"selected_color\"]:checked")?.value;
        const selectedSizeId = document.querySelector("input[name=\"selected_size\"]:checked")?.value;

        console.log("Selected Color:", selectedColorId);
        console.log("Selected Size:", selectedSizeId);

        // Update size availability based on selected color
        if (selectedColorId) {
            document.querySelectorAll(".size-option-wrapper").forEach(wrapper => {
                const sizeInput = wrapper.querySelector(".size-input");
                const sizeLabel = wrapper.querySelector(".size-option");
                const sizeId = sizeInput.value;

                const hasStock = colorSizeCombos.some(combo =>
                    combo.color_id == selectedColorId &&
                    combo.size_id == sizeId &&
                    combo.stock > 0
                );

                console.log(`Size ${sizeId} with color ${selectedColorId}: hasStock = ${hasStock}`);

                if (hasStock) {
                    sizeLabel.classList.remove("disabled");
                    sizeInput.disabled = false;
                } else {
                    sizeLabel.classList.add("disabled");
                    sizeInput.disabled = true;
                    if (sizeInput.checked) sizeInput.checked = false;
                }
            });
        }

        // Update color availability based on selected size
        if (selectedSizeId) {
            document.querySelectorAll(".color-option-wrapper").forEach(wrapper => {
                const colorInput = wrapper.querySelector(".color-input");
                const colorLabel = wrapper.querySelector(".color-option");
                const colorId = colorInput.value;

                const hasStock = colorSizeCombos.some(combo =>
                    combo.color_id == colorId &&
                    combo.size_id == selectedSizeId &&
                    combo.stock > 0
                );

                console.log(`Color ${colorId} with size ${selectedSizeId}: hasStock = ${hasStock}`);

                if (hasStock) {
                    colorLabel.classList.remove("disabled");
                    colorInput.disabled = false;
                } else {
                    colorLabel.classList.add("disabled");
                    colorInput.disabled = true;
                    if (colorInput.checked) colorInput.checked = false;
                }
            });
        }

        // Update product info if both are selected
        if (selectedColorId && selectedSizeId) {
            const combo = colorSizeCombos.find(combo =>
                combo.color_id == selectedColorId &&
                combo.size_id == selectedSizeId
            );

            console.log("Found combo:", combo);

            if (combo) {
                const stockInfo = document.getElementById("product-stock");
                const variantInfo = document.getElementById("selected-variant-info");
                const addToCartBtn = document.getElementById("add-to-cart-btn");

                if (combo.stock > 0) {
                    if (stockInfo) stockInfo.innerHTML = `<span class="text-success">Còn ${combo.stock} sản phẩm</span>`;
                    if (variantInfo) variantInfo.textContent = `Đã chọn: ${combo.color_name || selectedColorId} - ${combo.size_name || selectedSizeId}`;
                    if (addToCartBtn) {
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove("btn-secondary");
                        addToCartBtn.classList.add("btn-primary");
                        addToCartBtn.querySelector(".btn-text").textContent = "Thêm vào giỏ";
                    }
                } else {
                    if (stockInfo) stockInfo.innerHTML = `<span class="text-danger">Hết hàng</span>`;
                    if (variantInfo) variantInfo.textContent = "Tổ hợp này đã hết hàng";
                    if (addToCartBtn) {
                        addToCartBtn.disabled = true;
                        addToCartBtn.classList.remove("btn-primary");
                        addToCartBtn.classList.add("btn-secondary");
                        addToCartBtn.querySelector(".btn-text").textContent = "Hết hàng";
                    }
                }
            }

            // Update price display
            updatePriceDisplay();
        } else {
            const stockInfo = document.getElementById("product-stock");
            const variantInfo = document.getElementById("selected-variant-info");
            const addToCartBtn = document.getElementById("add-to-cart-btn");

            if (stockInfo) stockInfo.innerHTML = `<span class="text-muted">Chọn biến thể để xem tình trạng kho</span>`;
            if (variantInfo) variantInfo.textContent = "Chọn màu sắc và kích thước";
            if (addToCartBtn) {
                addToCartBtn.disabled = false;
                addToCartBtn.classList.remove("btn-secondary");
                addToCartBtn.classList.add("btn-primary");
                addToCartBtn.querySelector(".btn-text").textContent = "Thêm vào giỏ";
            }
        }
    }

    // Add event listeners
    document.querySelectorAll("input[name=\"selected_color\"]").forEach(input => {
        input.addEventListener("change", function() {
            console.log("Color changed to:", this.value);
            updateVariantAvailability();
        });
    });

    document.querySelectorAll("input[name=\"selected_size\"]").forEach(input => {
        input.addEventListener("change", function() {
            console.log("Size changed to:", this.value);
            updateVariantAvailability();
        });
    });

    // Initialize on page load
    updateVariantAvailability();
});

// Utility functions
function changeMainImage(thumbnail) {
    const mainImage = document.getElementById("mainProductImage");
    if (mainImage && thumbnail) {
        mainImage.src = thumbnail.src;
        document.querySelectorAll(".thumbnail").forEach(thumb => thumb.classList.remove("active"));
        thumbnail.classList.add("active");
    }
}

function zoomImage(imageUrl) {
    const modal = new bootstrap.Modal(document.getElementById("imageZoomModal"));
    document.getElementById("zoomImage").src = imageUrl;
    modal.show();
}

function changeQuantity(delta) {
    const input = document.getElementById("productQuantity");
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.max(1, Math.min(99, currentValue + delta));
    input.value = newValue;
}

// Function to get current variant price
function getCurrentVariantPrice() {
    const selectedColorEl = document.querySelector("input[name=\'selected_color\']:checked") ||
                            document.querySelector("input[name=\'default_color\']:checked");
    const selectedSizeEl = document.querySelector("input[name=\'selected_size\']:checked") ||
                           document.querySelector("input[name=\'default_size\']:checked");

    const selectedColor = selectedColorEl ? selectedColorEl.value : null;
    const selectedSize = selectedSizeEl ? selectedSizeEl.value : null;

    // Find matching variant
    if (productVariants && productVariants.length > 0) {
        const matchingVariant = productVariants.find(variant => {
            const variantColor = variant.color || null;
            const variantSize = variant.size || null;
            return variantColor === selectedColor && variantSize === selectedSize;
        });

        if (matchingVariant) {
            return parseFloat(matchingVariant.sale_price || matchingVariant.price || 0);
        }
    }

    // Fallback to base product price
    return parseFloat(baseProductPrice || 0);
}

// Function to update price display when variant changes
function updatePriceDisplay() {
    const priceElement = document.querySelector(\'#product-price .current-price\');
    if (priceElement) {
        const currentPrice = getCurrentVariantPrice();
        if (currentPrice > 0) {
            priceElement.textContent = new Intl.NumberFormat(\'vi-VN\').format(currentPrice) + \'đ\';
        }
    }
}

function addToCartWithVariant(productId) {
    console.log("Add to cart:", productId);

    // Get selected variant information
    const selectedColorEl = document.querySelector("input[name=\'selected_color\']:checked") ||
                            document.querySelector("input[name=\'default_color\']:checked");
    const selectedSizeEl = document.querySelector("input[name=\'selected_size\']:checked") ||
                           document.querySelector("input[name=\'default_size\']:checked");

    const selectedColor = selectedColorEl ? selectedColorEl.value : null;
    const selectedSize = selectedSizeEl ? selectedSizeEl.value : null;
    const quantity = parseInt(document.getElementById("productQuantity").value) || 1;

    // Get current variant price
    const currentPrice = getCurrentVariantPrice();

    // Create variant string for AjaxController format
    let variant = null;
    if (selectedColor || selectedSize) {
        variant = JSON.stringify({
            color: selectedColor,
            size: selectedSize
        });
    }

    // Prepare data for AJAX endpoint
    const requestData = {
        product_id: productId,
        quantity: quantity,
        price: currentPrice // Send current price
    };

    if (variant) {
        requestData.variant = variant;
    }

    console.log("Request data:", requestData);

    // Add loading state
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const btnTextEl = addToCartBtn.querySelector(".btn-text");
    const originalText = btnTextEl.textContent;
    addToCartBtn.disabled = true;
    addToCartBtn.classList.add("loading");
    btnTextEl.textContent = "Đang thêm...";

    // Call AJAX endpoint
    fetch("http://localhost/5s-fashion/public/ajax/cart/add", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response:", data);
        if (data.success) {
            // Show success message
            showNotification("Đã thêm sản phẩm vào giỏ hàng!", "success");

            // Update cart counter if exists
            updateCartCounter(data.cart_count);
        } else {
            showNotification(data.message || "Có lỗi xảy ra khi thêm vào giỏ hàng", "error");
        }
    })
    .catch(error => {
        console.error("Error adding to cart:", error);
        showNotification("Có lỗi xảy ra khi thêm vào giỏ hàng", "error");
    })
    .finally(() => {
        // Remove loading state
        addToCartBtn.disabled = false;
        addToCartBtn.classList.remove("loading");
        btnTextEl.textContent = originalText;
    });
}

function buyNowWithVariant(productId) {
    console.log("Buy now:", productId);

    // Get selected variant information
    const selectedColorEl = document.querySelector("input[name=\'selected_color\']:checked") ||
                            document.querySelector("input[name=\'default_color\']:checked");
    const selectedSizeEl = document.querySelector("input[name=\'selected_size\']:checked") ||
                           document.querySelector("input[name=\'default_size\']:checked");

    const selectedColor = selectedColorEl ? selectedColorEl.value : null;
    const selectedSize = selectedSizeEl ? selectedSizeEl.value : null;
    const quantity = parseInt(document.getElementById("productQuantity").value) || 1;

    // Create variant string for AjaxController format
    let variant = null;
    if (selectedColor || selectedSize) {
        variant = JSON.stringify({
            color: selectedColor,
            size: selectedSize
        });
    }

    // Prepare data for AJAX endpoint
    const requestData = {
        product_id: productId,
        quantity: quantity
    };

    if (variant) {
        requestData.variant = variant;
    }

    // Add loading state
    const buyNowBtn = document.querySelector(".buy-now");
    const btnTextEl = buyNowBtn.querySelector(".btn-text");
    const originalText = btnTextEl.textContent;
    buyNowBtn.disabled = true;
    buyNowBtn.classList.add("loading");
    btnTextEl.textContent = "Đang xử lý...";

    // First add to cart, then redirect to checkout
    fetch("http://localhost/5s-fashion/public/ajax/cart/add", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to checkout page
            window.location.href = "http://localhost/5s-fashion/public/checkout";
        } else {
            showNotification(data.message || "Có lỗi xảy ra khi mua hàng", "error");
        }
    })
    .catch(error => {
        console.error("Error buying now:", error);
        showNotification("Có lỗi xảy ra khi mua hàng", "error");
    })
    .finally(() => {
        // Remove loading state
        buyNowBtn.disabled = false;
        buyNowBtn.classList.remove("loading");
        btnTextEl.textContent = originalText;
    });
}

function toggleWishlist(productId) {
    console.log("Toggle wishlist:", productId);

    const wishlistBtn = document.querySelector(".wishlist-btn");
    const icon = wishlistBtn.querySelector("i");

    // Add loading state
    wishlistBtn.disabled = true;

    // Call wishlist AJAX endpoint
    fetch("http://localhost/5s-fashion/public/ajax/wishlist/toggle", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Toggle heart icon and button state
            if (data.in_wishlist) {
                icon.className = "fas fa-heart";
                wishlistBtn.classList.add("active");
                showNotification("Đã thêm vào danh sách yêu thích!", "success");
            } else {
                icon.className = "far fa-heart";
                wishlistBtn.classList.remove("active");
                showNotification("Đã xóa khỏi danh sách yêu thích!", "info");
            }
        } else {
            showNotification(data.message || "Có lỗi xảy ra", "error");
        }
    })
    .catch(error => {
        console.error("Error toggling wishlist:", error);
        showNotification("Có lỗi xảy ra", "error");
    })
    .finally(() => {
        wishlistBtn.disabled = false;
    });
}

// Utility function to show notifications
function showNotification(message, type = "info") {
    // Create notification element
    const notification = document.createElement("div");
    notification.className = `alert alert-${type === "success" ? "success" : type === "error" ? "danger" : "info"} notification-toast`;
    notification.innerHTML = `
        <i class="fas fa-${type === "success" ? "check-circle" : type === "error" ? "exclamation-circle" : "info-circle"} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease-out;
    `;

    // Add CSS for animation if not exists
    if (!document.querySelector("#notification-styles")) {
        const style = document.createElement("style");
        style.id = "notification-styles";
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            .notification-toast {
                transition: all 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }

    // Append to body
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = "slideOutRight 0.3s ease-in";
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Utility function to update cart counter
function updateCartCounter(cartCount = null) {
    if (cartCount !== null) {
        // Use provided count from addToCart response
        updateCartDisplay(cartCount);
        return;
    }

    // Fetch cart data from AJAX endpoint
    fetch("http://localhost/5s-fashion/public/ajax/cart/items", {
        method: "GET",
        headers: {
            "Accept": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.cart_count !== undefined) {
            updateCartDisplay(data.cart_count);
        }
    })
    .catch(error => {
        console.error("Error updating cart counter:", error);
    });
}

function updateCartDisplay(totalItems) {
    // Update cart counter in header if exists
    const cartCounters = document.querySelectorAll(".cart-counter, .cart-count, .badge-cart");
    cartCounters.forEach(counter => {
        counter.textContent = totalItems;
        counter.style.display = totalItems > 0 ? "inline" : "none";
    });

    // Update cart button text if exists
    const cartButtons = document.querySelectorAll(".btn-cart");
    cartButtons.forEach(btn => {
        const text = btn.querySelector(".cart-text");
        if (text) {
            text.textContent = `Giỏ hàng (${totalItems})`;
        }
    });
}

function shareOnFacebook() {
    console.log("Share on Facebook");
}

function shareOnTwitter() {
    console.log("Share on Twitter");
}

function shareOnPinterest() {
    console.log("Share on Pinterest");
}

function copyProductLink() {
    navigator.clipboard.writeText(window.location.href);
    alert("Link đã được sao chép!");
}
';

// Include the layout
include VIEW_PATH . '/client/layouts/app.php';
?>
