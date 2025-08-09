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
                    <div class="main-image-container">
                        <div class="product-badges">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <span class="badge bg-danger">Sale</span>
                            <?php endif; ?>
                            <?php if ($product['featured'] ?? false): ?>
                                <span class="badge bg-warning">Hot</span>
                            <?php endif; ?>
                        </div>

                        <img src="<?= htmlspecialchars($imageUrl) ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             class="main-product-image"
                             id="mainProductImage">
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text">(4.5 - 123 đánh giá)</span>
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

                    <!-- Product Description -->
                    <div class="product-description">
                        <h5><i class="fas fa-info-circle me-2"></i>Mô tả sản phẩm</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả')) ?></p>
                    </div>

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
                                        <div class="variant-name"><?= htmlspecialchars($variant['name']) ?></div>
                                        <div class="price-display"><?= number_format($variant['price'], 0, ',', '.') ?>đ</div>
                                        <div class="stock-status <?= $variant['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                                            <?= $variant['stock'] > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                                        </div>
                                    </div>
                                    <div class="variant-selector"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

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
    const quantity = document.getElementById('quantity').value;
    addToCart(productId, quantity);
}

function addToCart(productId, quantity = 1) {
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
            showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            // Update cart counter if exists
            if (typeof updateCartCounter === 'function') {
                updateCartCounter(data.cart_count);
            }
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
    })
    .finally(() => {
        // Restore button
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function buyNow(productId) {
    const quantity = document.getElementById('quantity').value;
    // Add to cart then redirect to checkout
    addToCart(productId, quantity);
    setTimeout(() => {
        window.location.href = '/5s-fashion/checkout';
    }, 1000);
}

function toggleWishlist(productId) {
    showNotification('Tính năng yêu thích đang được phát triển', 'info');
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
        showNotification('Đã sao chép link sản phẩm!', 'success');
    });
}

function showNotification(message, type = "info") {
    // Create toast notification following UI guidelines
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;

    document.body.appendChild(toast);

    // Show toast
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);

    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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
    // Any initialization code here
});
JS;

// Include the layout
include VIEW_PATH . '/client/layouts/app.php';
?>
