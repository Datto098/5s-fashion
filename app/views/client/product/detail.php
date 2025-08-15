<?php
// Product Detail Page - Using UI Guidelines
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

// Start content capture
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
<section class="product-detail-section">
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="product-images">
                    <?php
                    // Collect all images: featured image + gallery + variant images
                    $allImages = [];

                    // Add featured image first
                    if (!empty($product['featured_image'])) {
                        $allImages[] = [
                            'url' => $imageUrl,
                            'alt' => htmlspecialchars($product['name']),
                            'type' => 'featured'
                        ];
                    }

                    // Add gallery images
                    if (!empty($product['gallery'])) {
                        $galleryImages = is_string($product['gallery']) ? json_decode($product['gallery'], true) : $product['gallery'];
                        if (is_array($galleryImages)) {
                            foreach ($galleryImages as $galleryImg) {
                                if (!empty($galleryImg)) {
                                    $cleanPath = ltrim($galleryImg, '/');
                                    $allImages[] = [
                                        'url' => '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath),
                                        'alt' => htmlspecialchars($product['name']),
                                        'type' => 'gallery'
                                    ];
                                }
                            }
                        }
                    }

                    // Add variant images (if any)
                    if (!empty($variants)) {
                        foreach ($variants as $variant) {
                            if (!empty($variant['image'])) {
                                $cleanPath = ltrim($variant['image'], '/');
                                $allImages[] = [
                                    'url' => '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath),
                                    'alt' => htmlspecialchars($variant['variant_name']),
                                    'type' => 'variant'
                                ];
                            }
                        }
                    }

                    // Fallback to default image if no images
                    if (empty($allImages)) {
                        $allImages[] = [
                            'url' => '/5s-fashion/public/assets/images/default-product.jpg',
                            'alt' => htmlspecialchars($product['name']),
                            'type' => 'default'
                        ];
                    }
                    ?>

                    <div class="product-image-slider">
                        <div class="product-badges">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <span class="badge bg-danger">Sale</span>
                            <?php endif; ?>
                            <?php if ($product['featured'] ?? false): ?>
                                <span class="badge bg-warning">Hot</span>
                            <?php endif; ?>
                        </div>

                        <!-- Main image display -->
                        <div class="main-image-wrapper">
                            <img src="<?= $allImages[0]['url'] ?>"
                                 alt="<?= $allImages[0]['alt'] ?>"
                                 class="main-product-image"
                                 id="mainProductImage">
                        </div>

                        <!-- Thumbnail slider -->
                        <?php if (count($allImages) > 1): ?>
                        <div class="image-thumbnails">
                            <div class="thumbnail-slider">
                                <?php foreach ($allImages as $index => $image): ?>
                                <div class="thumbnail-item <?= $index === 0 ? 'active' : '' ?>"
                                     onclick="changeMainImage('<?= htmlspecialchars($image['url']) ?>', this)">
                                    <img src="<?= $image['url'] ?>"
                                         alt="<?= $image['alt'] ?>"
                                         class="thumbnail-image">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                    <!-- Đánh giá sản phẩm -->
                    <div class="product-rating mb-3">
                        <div class="stars">
                            <?php
                            $avgRating = isset($product['rating']) ? round($product['rating'], 1) : 0;
                            $reviewCount = isset($product['review_count']) ? (int)$product['review_count'] : 0;
                            for ($i = 1; $i <= 5; $i++):
                                if ($i <= floor($avgRating)) {
                                    echo '<i class="fas fa-star text-warning"></i>';
                                } elseif ($i - $avgRating < 1 && $i - $avgRating > 0) {
                                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                } else {
                                    echo '<i class="far fa-star text-warning"></i>';
                                }
                            endfor;
                            ?>
                        </div>
                        <span class="rating-text">(<?= $avgRating ?> / 5 - <?= $reviewCount ?> đánh giá)</span>
                    </div>


                    <div class="product-meta">
                        <div class="product-sku">
                            Mã sản phẩm: <span class="sku-code"><?= $product['sku'] ?? 'N/A' ?></span>
                        </div>
                        <div class="product-availability">
                            Trạng thái: <span class="text-success">Còn hàng</span>
                        </div>
                    </div>

                    <!-- Product Price -->
                    <div class="product-price">
                        <?php
                        $currentPrice = !empty($product['sale_price']) ? $product['sale_price'] : $product['price'];
                        $originalPrice = !empty($product['sale_price']) ? $product['price'] : null;
                        ?>
                        <span class="current-price"><?= number_format($currentPrice, 0, ',', '.') ?>đ</span>
                        <?php if ($originalPrice && $originalPrice > $currentPrice): ?>
                            <span class="original-price"><?= number_format($originalPrice, 0, ',', '.') ?>đ</span>
                            <span class="discount-percent">
                                -<?= round((($originalPrice - $currentPrice) / $originalPrice) * 100) ?>%
                            </span>
                        <?php endif; ?>
                    </div>

                  

                         <!-- Product Variants (if available) -->
                    <?php if (!empty($variants)): ?>
                    <div class="variants-section">
                        <h4><i class="fas fa-palette"></i> Các lựa chọn khác</h4>
                        <div class="variant-grid">
                            <?php foreach ($variants as $variant): ?>
                                <div class="variant-option"
                                     data-variant-id="<?= $variant['id'] ?>"
                                     data-price="<?= $variant['price'] ?>"
                                     onclick="selectVariant(this)">
                                    <div class="variant-content">
                                        <div class="variant-name"><?= htmlspecialchars($variant['variant_name']) ?></div>
                                        <div class="price-display">
                                            <?php
                                            $variantPrice = !empty($variant['price']) ? $variant['price'] : $product['price'];
                                            echo number_format($variantPrice, 0, ',', '.') . 'đ';
                                            ?>
                                        </div>
                                        <div class="stock-status <?= $variant['stock_quantity'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                                            <?= $variant['stock_quantity'] > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                                        </div>
                                    </div>
                                    <div class="variant-selector"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Purchase Actions -->
                    <div class="purchase-actions">
                        <div class="quantity-selector">
                            <label><strong>Số lượng:</strong></label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="1" min="1" max="99" id="quantity">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        

                        <div class="action-buttons">
                            <button class="btn btn-add-cart" onclick="handleAddToCart(<?= $product['id'] ?>)">
                                <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                            </button>
                            <button class="btn btn-buy-now" onclick="buyNow(<?= $product['id'] ?>)">
                                <i class="fas fa-bolt me-2"></i>Mua ngay
                            </button>
                            <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" title="Thêm vào yêu thích">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Product Features -->
                    <div class="product-features">
                        <div class="feature-item">
                            <i class="fas fa-shipping-fast"></i>
                            <div class="feature-text">
                                <strong>Giao hàng miễn phí</strong>
                                <span>Đơn hàng từ 500.000đ</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-undo-alt"></i>
                            <div class="feature-text">
                                <strong>Đổi trả trong 7 ngày</strong>
                                <span>Miễn phí đổi trả</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <div class="feature-text">
                                <strong>Cam kết chính hãng</strong>
                                <span>100% sản phẩm chính hãng</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-headset"></i>
                            <div class="feature-text">
                                <strong>Hỗ trợ 24/7</strong>
                                <span>Hotline: 1900 1900</span>
                            </div>
                        </div>
                    </div>

               

                    <!-- Social Share -->
                    <div class="social-share">
                        <p class="share-label">Chia sẻ sản phẩm:</p>
                        <div class="share-buttons">
                            <button class="share-btn facebook" onclick="shareOnFacebook()" title="Chia sẻ Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="share-btn twitter" onclick="shareOnTwitter()" title="Chia sẻ Twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="share-btn pinterest" onclick="shareOnPinterest()" title="Chia sẻ Pinterest">
                                <i class="fab fa-pinterest"></i>
                            </button>
                            <button class="share-btn copy" onclick="copyProductLink()" title="Sao chép link">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Reviews & Comments Section -->
        <div class="row mt-5">
            <div class="col-12">
                <!-- Tabs for product information and reviews -->
                <div class="product-tabs">
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">
                                <i class="fas fa-info-circle me-2"></i>Mô tả
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                                <i class="fas fa-star me-2"></i>Đánh giá (<?= $reviewCount ?? 0 ?>)
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                            <h5 class="mb-3">Thông tin chi tiết sản phẩm</h5>
                            <?php if (!empty($product['long_description'])): ?>
                                <?= $product['long_description'] ?>
                            <?php else: ?>
                                <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả chi tiết cho sản phẩm này.')) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['specifications'])): ?>
                            <div class="product-specs mt-4">
                                <h5 class="mb-3">Thông số kỹ thuật</h5>
                                <table class="table table-striped">
                                    <tbody>
                                        <?php 
                                        $specs = is_string($product['specifications']) ? 
                                            json_decode($product['specifications'], true) : 
                                            $product['specifications'];
                                        
                                        if (is_array($specs)): 
                                            foreach ($specs as $key => $value): 
                                        ?>
                                            <tr>
                                                <td width="30%"><?= htmlspecialchars($key) ?></td>
                                                <td><?= htmlspecialchars($value) ?></td>
                                            </tr>
                                        <?php 
                                            endforeach; 
                                        endif; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <div class="reviews-section">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5><i class="fas fa-star text-warning me-2"></i>Đánh giá từ khách hàng</h5>
                                    <div class="rating-summary">
                                        <div class="overall-rating">
                                            <span class="rating-number"><?= $avgRating ?? 0 ?></span>/5
                                            <div class="stars">
                                                <?php
                                                $avgRating = isset($product['rating']) ? round($product['rating'], 1) : 0;
                                                for ($i = 1; $i <= 5; $i++):
                                                    if ($i <= floor($avgRating)) {
                                                        echo '<i class="fas fa-star text-warning"></i>';
                                                    } elseif ($i - $avgRating < 1 && $i - $avgRating > 0) {
                                                        echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star text-warning"></i>';
                                                    }
                                                endfor;
                                                ?>
                                            </div>
                                        </div>
                                        <span class="total-reviews">Dựa trên <?= $reviewCount ?? 0 ?> đánh giá</span>
                                    </div>
                                </div>

                                <!-- Đánh giá của khách hàng -->
                                <?php if (!empty($reviews)): ?>
                                    <div class="review-list">
                                        <?php foreach ($reviews as $review): ?>
                                            <div class="review-item mb-4 p-3 border rounded">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="reviewer-info d-flex align-items-center">
                                                        <?php if (!empty($review['customer_avatar'])): ?>
                                                            <img src="<?= $review['customer_avatar'] ?>" alt="<?= htmlspecialchars($review['customer_name']) ?>" class="rounded-circle me-2" width="40" height="40">
                                                        <?php else: ?>
                                                            <div class="avatar-placeholder rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width:40px;height:40px;">
                                                                <?= strtoupper(substr($review['customer_name'] ?? 'K', 0, 1)) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= htmlspecialchars($review['customer_name'] ?? 'Khách') ?></strong>
                                                            <?php if (isset($review['is_verified_purchase']) && $review['is_verified_purchase']): ?>
                                                                <span class="badge bg-success ms-1"><i class="fas fa-check-circle"></i> Đã mua hàng</span>
                                                            <?php endif; ?>
                                                            <div class="text-muted small">
                                                                <?= date('d/m/Y', strtotime($review['created_at'])) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="rating text-warning">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                
                                                <?php if (!empty($review['title'])): ?>
                                                    <h6 class="review-title fw-bold"><?= htmlspecialchars($review['title']) ?></h6>
                                                <?php endif; ?>
                                                
                                                <div class="review-content">
                                                    <?= nl2br(htmlspecialchars($review['content'])) ?>
                                                </div>
                                                
                                                <div class="review-actions mt-3 d-flex align-items-center">
                                                    <button class="btn btn-sm btn-outline-secondary me-2 like-review-btn <?= isset($review['user_has_liked']) && $review['user_has_liked'] ? 'liked' : '' ?>" data-review-id="<?= $review['id'] ?>">
                                                        <i class="far fa-thumbs-up me-1"></i>
                                                        <span class="helpful-count"><?= $review['helpful_count'] ?? 0 ?></span> Hữu ích
                                                    </button>
                                                    
                                                    <?php if (isLoggedIn() && $_SESSION['user']['id'] == $review['user_id']): ?>
                                                        <button class="btn btn-sm btn-outline-danger delete-review-btn" data-review-id="<?= $review['id'] ?>">
                                                            <i class="far fa-trash-alt me-1"></i>Xóa
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4 border rounded bg-light">
                                        <i class="fas fa-star fs-1 text-muted mb-3"></i>
                                        <p>Sản phẩm này chưa có đánh giá nào.</p>
                                        <?php if (isLoggedIn()): ?>
                                            <button class="btn btn-outline-primary" onclick="scrollToReviewForm()">
                                                <i class="fas fa-edit me-2"></i>Viết đánh giá đầu tiên
                                            </button>
                                        <?php else: ?>
                                            <a href="/5s-fashion/login" class="btn btn-outline-primary">
                                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để đánh giá
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Form đánh giá sản phẩm -->
                                <?php if (isLoggedIn()): ?>
                                    <div class="review-form mt-5" id="review-form">
                                        <h5 class="mb-3"><i class="fas fa-edit me-2"></i>Viết đánh giá của bạn</h5>
                                        
                                        <?php if (!empty($canReview)): ?>
                                            <form action="/5s-fashion/ajax/review/add" method="post" class="border rounded p-4 bg-light" id="review-add-form" onsubmit="return handleFormSubmit(event)">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                
                                                <div class="mb-3">
                                                    <label for="rating" class="form-label">Đánh giá của bạn <span class="text-danger">*</span></label>
                                                    <div class="star-rating">
                                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required onclick="console.log('Star clicked:', <?= $i ?>)">
                                                            <label for="star<?= $i ?>" onclick="console.log('Label clicked:', <?= $i ?>)"><i class="fas fa-star"></i></label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Tiêu đề</label>
                                                    <input type="text" class="form-control" id="title" name="title" placeholder="Tiêu đề ngắn gọn cho đánh giá của bạn">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="content" class="form-label">Nội dung đánh giá <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="content" name="content" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <?php if (isLoggedIn()): ?>
                                                    <?php 
                                                    // Lấy thông tin chi tiết về lý do không thể đánh giá
                                                    $userId = $_SESSION['user']['id'];
                                                    $hasPurchasedAny = isset($hasOrderedProduct) ? $hasOrderedProduct : false;
                                                    $hasDelivered = isset($hasCompletedOrders) ? $hasCompletedOrders : false;
                                                    $hasReviewed = isset($hasReviewed) ? $hasReviewed : false;
                                                    ?>
                                                    
                                                    <?php if ($hasReviewed): ?>
                                                        <strong>Bạn đã đánh giá sản phẩm này rồi.</strong> Bạn có thể tìm đánh giá của mình phía trên.
                                                    <?php elseif (!$hasPurchasedAny): ?>
                                                        <strong>Bạn chưa mua sản phẩm này.</strong> Vui lòng mua sản phẩm trước khi đánh giá.
                                                    <?php elseif (!$hasDelivered): ?>
                                                        <strong>Đơn hàng của bạn đang được xử lý.</strong> Bạn có thể đánh giá sau khi nhận được hàng.
                                                    <?php else: ?>
                                                        <strong>Không thể đánh giá.</strong> Vui lòng liên hệ bộ phận hỗ trợ khách hàng nếu bạn tin rằng đây là lỗi.
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <strong>Bạn cần đăng nhập và mua sản phẩm</strong> để có thể đánh giá.
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Capture content and set it for layout
$content = ob_get_clean();

// Set layout variables following UI guidelines
$title = htmlspecialchars($product['name']) . ' - 5S Fashion';
$meta_description = htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao tại 5S Fashion');
$meta_keywords = htmlspecialchars($product['name']) . ', thời trang, 5s fashion';

// Custom CSS following UI guidelines
$custom_css = ['css/product-detail.css'];

// Inline CSS for reviews and comments
$inline_css = <<<'CSS'
/* Product Tabs Styling */
.product-tabs .nav-tabs {
    border-bottom: 1px solid #dee2e6;
}

.product-tabs .nav-link {
    color: #495057;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    padding: 0.8rem 1.2rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.product-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

.product-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #007bff;
}

/* Star Rating System */
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.star-rating input {
    display: none;
}

.star-rating label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    margin-right: 5px;
}

.star-rating label:hover,
.star-rating label.hover,
.star-rating label.selected {
    color: #ffb700;
}

.star-rating input:checked ~ label {
    color: #ffb700;
}

/* Reviews and Comments Styling */
.review-item, .comment-item {
    transition: all 0.2s ease;
    background-color: #fff;
}

.review-item:hover, .comment-item:hover {
    border-color: #007bff !important;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
}

.avatar-placeholder {
    font-weight: bold;
}

.rating-summary {
    text-align: right;
}

.overall-rating {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-bottom: 5px;
}

.rating-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-right: 8px;
    color: #ffb700;
}

.total-reviews {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-tabs .nav-link {
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
    }
    
    .rating-summary {
        text-align: left;
        margin-top: 10px;
    }
    
    .overall-rating {
        justify-content: flex-start;
    }
}
CSS;

// Custom JavaScript for product functionality
$custom_js = ['js/product-detail.js'];

// Inline JavaScript for product-specific functionality
$inline_js = <<<'JS'
// Product functionality
function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let newValue = parseInt(quantityInput.value) + change;
    if (newValue < 1) newValue = 1;
    if (newValue > 99) newValue = 99;
    quantityInput.value = newValue;
}

// Handle add to cart click
function handleAddToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;

    // Check if unified cart manager is available
    if (window.unifiedCart && typeof window.unifiedCart.addToCart === 'function') {
        // Get selected variant if any
        const selectedVariant = getSelectedVariant();
        window.unifiedCart.addToCart(productId, quantity, selectedVariant);
    } else {
        // Fallback to local implementation
        addToCartFallback(productId, quantity);
    }
}

function getSelectedVariant() {
    const selectedVariantElement = document.querySelector('.variant-option.selected');
    if (selectedVariantElement) {
        return {
            id: selectedVariantElement.getAttribute('data-variant-id'),
            price: selectedVariantElement.getAttribute('data-price'),
            name: selectedVariantElement.querySelector('.variant-name')?.textContent
        };
    }
    return null;
}

function addToCartFallback(productId, quantity = 1) {
    // Show loading state
    const btn = document.querySelector('.btn-add-cart');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';
    btn.disabled = true;

    // Add to cart API call
    fetch('/5s-fashion/ajax/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showProductNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            // Update cart counter if exists
            // DISABLED - preventing counter jumping
            // if (typeof updateCartCounter === 'function') {
            //     updateCartCounter(data.cart_count);
            // }
        } else {
            showProductNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Add to cart error:', error);
        showProductNotification('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
    })
    .finally(() => {
        // Restore button
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function buyNow(productId) {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;

    // Use unified cart if available
    if (window.unifiedCart && typeof window.unifiedCart.addToCart === 'function') {
        const selectedVariant = getSelectedVariant();
        window.unifiedCart.addToCart(productId, quantity, selectedVariant).then((result) => {
            if (result && result.success) {
                setTimeout(() => {
                    window.location.href = '/5s-fashion/checkout';
                }, 1000);
            }
        });
    } else {
        // Fallback: Add to cart then redirect to checkout
        addToCartFallback(productId, quantity);
        setTimeout(() => {
            window.location.href = '/5s-fashion/checkout';
        }, 1000);
    }
}

function toggleWishlist(productId) {
    showProductNotification('Tính năng yêu thích đang được phát triển', 'info');
}

function shareOnFacebook() {
    const url = window.location.href;
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
}

function shareOnTwitter() {
    const url = window.location.href;
    const text = document.querySelector('.product-title').textContent;
    window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
}

function shareOnPinterest() {
    const url = window.location.href;
    const media = document.getElementById('mainProductImage').src;
    const description = document.querySelector('.product-title').textContent;
    window.open(`https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&media=${encodeURIComponent(media)}&description=${encodeURIComponent(description)}`, '_blank');
}

function copyProductLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showProductNotification('Đã sao chép link sản phẩm!', 'success');
    });
}

function showProductNotification(message, type = "info") {
    // Use unified notification system if available (avoid self-reference)
    if (window.notifications && typeof window.notifications.show === 'function') {
        window.notifications.show(message, type);
        return;
    }

    // Try global notification functions
    if (window.showSuccess && type === 'success') {
        window.showSuccess(message);
        return;
    } else if (window.showError && type === 'error') {
        window.showError(message);
        return;
    } else if (window.showWarning && type === 'warning') {
        window.showWarning(message);
        return;
    } else if (window.showInfo && (type === 'info' || !type)) {
        window.showInfo(message);
        return;
    }

    // Fallback notification system
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type} position-fixed`;
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 12px 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#17a2b8'};
        color: ${type === 'warning' ? '#000' : '#fff'};
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        min-width: 300px;
    `;

    // Add icon
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info-circle';
    toast.innerHTML = `<i class="fas fa-${icon} me-2"></i>${message}`;

    document.body.appendChild(toast);    // Show toast
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);

    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Change main product image when clicking thumbnail
function changeMainImage(imageUrl, thumbnailElement) {
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) {
        // Add fade effect
        mainImage.style.opacity = '0.5';

        setTimeout(() => {
            mainImage.src = imageUrl;
            mainImage.style.opacity = '1';
        }, 150);
    }

    // Update thumbnail active state
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    thumbnailElement.classList.add('active');
}

// Select variant function
function selectVariant(element) {
    // Remove selected class from all variants
    document.querySelectorAll('.variant-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Add selected class to clicked variant
    element.classList.add('selected');

    // Update price if variant has different price
    const variantPrice = element.getAttribute('data-price');
    if (variantPrice) {
        const priceElement = document.querySelector('.current-price');
        priceElement.textContent = new Intl.NumberFormat('vi-VN').format(variantPrice) + 'đ';
    }
}

// Initialize page
document.addEventListener("DOMContentLoaded", function() {
    // Initialize star rating functionality
    initStarRating();

    // Set up tab functionality if not using Bootstrap's built-in tab handling
    setupProductTabs();
});

// Function to scroll to review form
function scrollToReviewForm() {
    const reviewsTab = document.getElementById('reviews-tab');
    if (reviewsTab) {
        // Activate the reviews tab
        const tabTrigger = new bootstrap.Tab(reviewsTab);
        tabTrigger.show();
        
        // Scroll to review form
        const reviewForm = document.getElementById('review-form');
        if (reviewForm) {
            setTimeout(() => {
                reviewForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    }
}

// Initialize star rating functionality
function initStarRating() {
    const starLabels = document.querySelectorAll('.star-rating label');
    if (starLabels.length === 0) return;
    
    // Handle hover effect
    starLabels.forEach(label => {
        label.addEventListener('mouseover', function() {
            const currentStar = parseInt(this.getAttribute('for').replace('star', ''));
            
            // Update stars on hover
            starLabels.forEach(innerLabel => {
                const innerStar = parseInt(innerLabel.getAttribute('for').replace('star', ''));
                if (innerStar <= currentStar) {
                    innerLabel.classList.add('hover');
                } else {
                    innerLabel.classList.remove('hover');
                }
            });
        });
        
        // Handle click event
        label.addEventListener('click', function() {
            const currentStar = parseInt(this.getAttribute('for').replace('star', ''));
            
            // Set all appropriate stars as selected
            starLabels.forEach(innerLabel => {
                const innerStar = parseInt(innerLabel.getAttribute('for').replace('star', ''));
                if (innerStar <= currentStar) {
                    innerLabel.classList.add('selected');
                } else {
                    innerLabel.classList.remove('selected');
                }
            });
            
            // Set the radio button as checked
            document.getElementById('star' + currentStar).checked = true;
        });
    });
    
    // Remove hover effect when mouse leaves rating area
    const starRatingContainer = document.querySelector('.star-rating');
    if (starRatingContainer) {
        starRatingContainer.addEventListener('mouseleave', function() {
            starLabels.forEach(label => {
                label.classList.remove('hover');
            });
            
            // Re-apply selected class to appropriate stars based on the checked radio
            const checkedRadio = document.querySelector('.star-rating input:checked');
            if (checkedRadio) {
                const checkedValue = parseInt(checkedRadio.value);
                starLabels.forEach(label => {
                    const star = parseInt(label.getAttribute('for').replace('star', ''));
                    if (star <= checkedValue) {
                        label.classList.add('selected');
                    } else {
                        label.classList.remove('selected');
                    }
                });
            }
        });
    }
}

// Set up tab functionality (only needed if not using Bootstrap's built-in tab handling)
function setupProductTabs() {
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    
    // Activate tab from URL parameter if available
    if (activeTab) {
        const tabToActivate = document.getElementById(activeTab + '-tab');
        if (tabToActivate) {
            const tab = new bootstrap.Tab(tabToActivate);
            tab.show();
        }
    }
    
    // Update URL when tab changes
    tabLinks.forEach(tabLink => {
        tabLink.addEventListener('shown.bs.tab', function (e) {
            const tabId = e.target.id.replace('-tab', '');
            
            // Update URL without reloading the page
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        });
    });
}

// Debug: Add star rating debugging and styles
document.addEventListener('DOMContentLoaded', function() {
    console.log('Detail page loaded');
    
    // Check if rating inputs exist
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    console.log('Rating inputs found:', ratingInputs.length);
    
    // Add change listeners to rating inputs
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            console.log('Rating selected:', this.value);
        });
    });
    
    // Check form
    const form = document.getElementById('review-add-form');
    console.log('Review form found:', !!form);
    
    if (form) {
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
    }
});

// Simple form handler
function handleFormSubmit(event) {
    event.preventDefault();
    console.log('Form submit triggered');
    
    const form = event.target;
    const formData = new FormData(form);
    
    console.log('Form data:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    const rating = formData.get('rating');
    const content = formData.get('content');
    
    console.log('Rating:', rating);
    console.log('Content:', content);
    
    if (!rating) {
        alert('Vui lòng chọn số sao đánh giá');
        return false;
    }
    
    if (!content || content.trim().length < 10) {
        alert('Vui lòng nhập nội dung đánh giá ít nhất 10 ký tự');
        return false;
    }
    
    console.log('Submitting to server...');
    
    // Submit via AJAX
    fetch('/5s-fashion/ajax/review/add', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text(); // Get as text first to debug
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            
            if (data.success) {
                alert('Cảm ơn bạn đã đánh giá sản phẩm!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert(data.message || 'Có lỗi xảy ra khi gửi đánh giá');
            }
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            console.log('Response was not JSON:', text);
            alert('Lỗi server: ' + text);
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        alert('Đã xảy ra lỗi khi gửi đánh giá: ' + error.message);
    });
    
    return false;
}
JS;

$inline_css = '
/* Star Rating Styles */
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
    margin: 10px 0;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    cursor: pointer;
    font-size: 24px;
    color: #ddd;
    transition: color 0.2s ease;
}

.star-rating label:hover,
.star-rating label.hover {
    color: #ffc107;
}

.star-rating input[type="radio"]:checked ~ label,
.star-rating label.selected {
    color: #ffc107;
}

/* Fix for the reverse order display */
.star-rating label:hover ~ label,
.star-rating label.hover ~ label {
    color: #ffc107;
}

.review-form button[type="submit"] {
    min-width: 150px;
}

.review-form button[type="submit"]:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
';

// Include the layout
include VIEW_PATH . '/client/layouts/app.php';
?>
