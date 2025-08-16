<!-- Product Card -->
<div class="product-card" data-product-id="<?= $product['id'] ?>">
    <div class="product-image-wrapper position-relative">
        <!-- Product Image -->
        <a href="<?= url('product/' . ($product['slug'] ?? 'product-' . $product['id'])) ?>" class="product-image-link">
            <?php if (!empty($product['featured_image'])): ?>
                <?php
                // Handle image path for file server
                $imagePath = $product['featured_image'];
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
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="product-image img-fluid">
            <?php else: ?>
                <img src="<?= asset('images/no-image.jpg') ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="product-image img-fluid">
            <?php endif; ?>
        </a>

        <!-- Product Badges -->
        <div class="product-badges">
            <?php if (isset($product['sale_price']) && $product['sale_price'] > 0 && $product['sale_price'] < $product['price']): ?>
                <?php $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                <span class="badge bg-danger">-<?= $discount ?>%</span>
            <?php endif; ?>

            <?php if (isset($product['featured']) && $product['featured']): ?>
                <span class="badge bg-warning text-dark">Hot</span>
            <?php endif; ?>

            <?php if (isset($product['is_new']) && $product['is_new']): ?>
                <span class="badge bg-success">Mới</span>
            <?php endif; ?>
        </div>

        <!-- Product Actions -->
        <div class="product-actions">
            <button class="btn btn-sm btn-light rounded-circle"
                    onclick="toggleWishlist(<?= $product['id'] ?>)"
                    title="Thêm vào yêu thích">
                <i class="far fa-heart"></i>
            </button>
            <button class="btn btn-sm btn-light rounded-circle"
                    onclick="quickView(<?= $product['id'] ?>)"
                    title="Xem nhanh">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <!-- Product Info -->
    <div class="product-info p-3">
        <!-- Product Category -->
        <?php if (isset($product['category_name'])): ?>
            <div class="product-category mb-1">
                <a href="/shop?category=<?= urlencode($product['category_slug']) ?>"
                   class="text-muted text-decoration-none small">
                    <?= htmlspecialchars($product['category_name']) ?>
                </a>
            </div>
        <?php endif; ?>

        <!-- Product Name -->
        <h5 class="product-name mb-2">
            <a href="<?= url('product/' . ($product['slug'] ?? 'product-' . $product['id'])) ?>"
               class="text-dark text-decoration-none">
                <?= htmlspecialchars($product['name']) ?>
            </a>
        </h5>

        <!-- Product Price -->
        <div class="product-price mb-2">
            <?php if (isset($product['sale_price']) && $product['sale_price'] > 0): ?>
                <span class="current-price fw-bold text-danger"><?= formatCurrency($product['sale_price']) ?></span>
                <span class="original-price text-muted text-decoration-line-through ms-2"><?= formatCurrency($product['price']) ?></span>
            <?php else: ?>
                <span class="current-price fw-bold text-primary"><?= formatCurrency($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Product Rating -->
        <div class="product-rating mb-2">
            <?php
            $rating = $product['rating'] ?? 4.5; // Default rating
            $fullStars = floor($rating);
            $halfStar = ($rating - $fullStars) >= 0.5;
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
            ?>
            <div class="stars">
                <?php for($i = 0; $i < $fullStars; $i++): ?>
                    <i class="fas fa-star text-warning"></i>
                <?php endfor; ?>

                <?php if($halfStar): ?>
                    <i class="fas fa-star-half-alt text-warning"></i>
                <?php endif; ?>

                <?php for($i = 0; $i < $emptyStars; $i++): ?>
                    <i class="far fa-star text-warning"></i>
                <?php endfor; ?>

                <span class="rating-count text-muted ms-1 small">(<?= $product['review_count'] ?? 0 ?>)</span>
            </div>
        </div>

        <!-- Product Colors (if available) -->
        <?php if (isset($product['colors']) && !empty($product['colors'])): ?>
            <div class="product-colors mb-2">
                <?php foreach (array_slice($product['colors'], 0, 5) as $color): ?>
                    <span class="color-swatch"
                          style="background-color: <?= htmlspecialchars($color['code']) ?>"
                          title="<?= htmlspecialchars($color['name']) ?>">
                    </span>
                <?php endforeach; ?>
                <?php if (count($product['colors']) > 5): ?>
                    <span class="text-muted small">+<?= count($product['colors']) - 5 ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Stock Status -->
        <div class="stock-status">
            <?php
            // Check if product has variants
            $hasStock = false;
            $totalStock = 0;
            $lowStock = false;

            // If product has variants, check variant stock
            if (isset($product['variants']) && !empty($product['variants'])) {
                foreach ($product['variants'] as $variant) {
                    if (isset($variant['stock_quantity']) && $variant['stock_quantity'] > 0) {
                        $hasStock = true;
                        $totalStock += $variant['stock_quantity'];
                    }
                }
                $lowStock = $totalStock > 0 && $totalStock <= 5;
            } else {
                // No variants, check main product stock
                if (isset($product['stock']) && $product['stock'] > 0) {
                    $hasStock = true;
                    $totalStock = $product['stock'];
                    $lowStock = $totalStock <= 5;
                }
            }
            ?>

            <?php if ($hasStock): ?>
                <?php if ($lowStock): ?>
                    <small class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Chỉ còn <?= $totalStock ?> sản phẩm
                    </small>
                <?php else: ?>
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Còn hàng
                    </small>
                <?php endif; ?>
            <?php else: ?>
                <small class="text-danger">
                    <i class="fas fa-times-circle me-1"></i>
                    Hết hàng
                </small>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product-image-wrapper {
    overflow: hidden;
    aspect-ratio: 1;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
}

.product-badges .badge {
    display: block;
    margin-bottom: 5px;
    font-size: 0.7rem;
}

.product-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 3;
}

.product-card:hover .product-actions {
    opacity: 1;
}

.product-actions .btn {
    width: 38px;
    height: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.95);
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.product-actions .btn:hover {
    background: rgba(255,255,255,1);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.product-actions .btn i {
    font-size: 16px;
    line-height: 1;
    color: #495057;
    transition: color 0.3s ease;
}

.product-actions .btn:hover i {
    color: #dc3545;
}

.product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 1rem;
    line-height: 1.3;
    min-height: 2.6rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-name a:hover {
    color: #007bff !important;
}

.product-price {
    margin-top: auto;
}

.current-price {
    font-size: 1.1rem;
}

.original-price {
    font-size: 0.9rem;
}

.stars {
    font-size: 0.8rem;
}

.color-swatch {
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 3px rgba(0,0,0,0.3);
    margin-right: 5px;
    cursor: pointer;
}

.stock-status {
    margin-top: auto;
}

@media (max-width: 768px) {
    .product-actions {
        opacity: 1;
        position: static;
        flex-direction: row;
        justify-content: center;
        padding: 10px;
        background: rgba(0,0,0,0.05);
    }
}
</style>
