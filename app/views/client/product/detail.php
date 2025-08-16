<?php
// Product Detail Page - Clean Version
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
                                <button type="button" class="quantity-btn btn-minus">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="1" min="1" max="99" id="quantity">
                                <button type="button" class="quantity-btn btn-plus">
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
    </div>
</section>
<script>
    // Global quantity tracker
    window.currentQuantity = 1;

    // Setup event listeners when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        // Plus button
        const plusBtn = document.querySelector('.quantity-btn.btn-plus');
        if (plusBtn) {
            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const quantityInput = document.getElementById('quantity');
                console.log('PLUS BUTTON - Before change:');
                console.log('- Current value:', quantityInput ? quantityInput.value : 'N/A');
                console.log('- Element:', quantityInput);

                if (quantityInput) {
                    let currentValue = parseInt(quantityInput.value) || 1;
                    let newValue = currentValue + 1;

                    if (newValue < 1) newValue = 1;
                    if (newValue > 99) newValue = 99;

                    // Update both property and attribute
                    quantityInput.value = newValue;
                    quantityInput.setAttribute('value', newValue);

                    // Update global tracker
                    window.currentQuantity = newValue;

                    console.log('PLUS BUTTON - After change:');
                    console.log('- New value (property):', quantityInput.value);
                    console.log('- New value (attribute):', quantityInput.getAttribute('value'));
                    console.log('- Global quantity:', window.currentQuantity);
                    console.log('- Parsed value:', parseInt(quantityInput.value));

                    // Force trigger change event
                    quantityInput.dispatchEvent(new Event('change'));
                    quantityInput.dispatchEvent(new Event('input'));
                }
            });
        }

        // Minus button
        const minusBtn = document.querySelector('.quantity-btn.btn-minus');
        if (minusBtn) {
            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const quantityInput = document.getElementById('quantity');
                console.log('MINUS BUTTON - Before change:');
                console.log('- Current value:', quantityInput ? quantityInput.value : 'N/A');

                if (quantityInput) {
                    let currentValue = parseInt(quantityInput.value) || 1;
                    let newValue = currentValue - 1;

                    if (newValue < 1) newValue = 1;
                    if (newValue > 99) newValue = 99;

                    // Update both property and attribute
                    quantityInput.value = newValue;
                    quantityInput.setAttribute('value', newValue);

                    // Update global tracker
                    window.currentQuantity = newValue;

                    console.log('MINUS BUTTON - After change:');
                    console.log('- New value (property):', quantityInput.value);
                    console.log('- New value (attribute):', quantityInput.getAttribute('value'));
                    console.log('- Global quantity:', window.currentQuantity);
                    console.log('- Parsed value:', parseInt(quantityInput.value));

                    // Force trigger change event
                    quantityInput.dispatchEvent(new Event('change'));
                    quantityInput.dispatchEvent(new Event('input'));
                }
            });
        }
    });

    // Handle add to cart click
    function handleAddToCart(productId) {
        console.log('=== ADD TO CART DEBUG ===');

        // Check how many quantity inputs exist
        const allQuantityInputs = document.querySelectorAll('#quantity');
        const allQuantityInputsByName = document.querySelectorAll('input[name="quantity"]');
        const allQuantityInputsByClass = document.querySelectorAll('.quantity-input');

        console.log('Elements with ID "quantity":', allQuantityInputs.length);
        console.log('Elements with name "quantity":', allQuantityInputsByName.length);
        console.log('Elements with class "quantity-input":', allQuantityInputsByClass.length);

        // Try multiple ways to get the quantity input
        let quantityInput = document.getElementById('quantity');

        // If not found or multiple, try by class
        if (!quantityInput || allQuantityInputs.length > 1) {
            const inputsByClass = document.querySelectorAll('.quantity-input');
            if (inputsByClass.length > 0) {
                quantityInput = inputsByClass[0]; // Use first one
                console.log('Using quantity input from class selector');
            }
        }

        // If still not found, try by type and context
        if (!quantityInput) {
            const productSection = document.querySelector('.product-detail-section');
            if (productSection) {
                quantityInput = productSection.querySelector('input[type="number"]');
                console.log('Using quantity input from product section');
            }
        }

        // Use global quantity as fallback
        const quantity = window.currentQuantity || parseInt(quantityInput ? quantityInput.value : 1) || 1;

        console.log('Final quantity input:', quantityInput);
        console.log('Input element found:', !!quantityInput);
        console.log('Input element value:', quantityInput ? quantityInput.value : 'N/A');
        console.log('Input element type:', quantityInput ? quantityInput.type : 'N/A');
        console.log('Global quantity:', window.currentQuantity);
        console.log('Final quantity used:', quantity);

        // Check if there are other quantity inputs with different values
        allQuantityInputsByClass.forEach((input, index) => {
            console.log(`Quantity input ${index}:`, input.value, input);
        });

        console.log('===========================');

        console.log('Adding to cart:', {
            productId: productId,
            quantity: quantity,
            inputValue: quantityInput ? quantityInput.value : 'N/A',
            inputElement: quantityInput
        });

        if (window.cartManager && typeof window.cartManager.addToCart === 'function') {
            const selectedVariant = getSelectedVariant();
            console.log('Using cartManager, variant:', selectedVariant);
            window.cartManager.addToCart(productId, quantity, selectedVariant);
        } else {
            console.log('Using fallback method');
            addToCartFallback(productId, quantity);
        }
    }    function getSelectedVariant() {
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
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Add to cart error:', error);
                alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
            });
    }

    function buyNow(productId) {
        const quantity = parseInt(document.getElementById('quantity').value) || 1;

        if (window.cartManager && typeof window.cartManager.addToCart === 'function') {
            const selectedVariant = getSelectedVariant();
            window.cartManager.addToCart(productId, quantity, selectedVariant).then((result) => {
                if (result && result.success) {
                    setTimeout(() => {
                        window.location.href = '/5s-fashion/checkout';
                    }, 1000);
                }
            });
        } else {
            addToCartFallback(productId, quantity);
            setTimeout(() => {
                window.location.href = '/5s-fashion/checkout';
            }, 1000);
        }
    }

    function toggleWishlist(productId) {
        alert('Tính năng yêu thích đang được phát triển');
    }

    function changeMainImage(imageUrl, thumbnailElement) {
        const mainImage = document.getElementById('mainProductImage');
        if (mainImage) {
            mainImage.src = imageUrl;
        }

        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
        });
        thumbnailElement.classList.add('active');
    }

    function selectVariant(element) {
        document.querySelectorAll('.variant-option').forEach(option => {
            option.classList.remove('selected');
        });

        element.classList.add('selected');

        const variantPrice = element.getAttribute('data-price');
        if (variantPrice) {
            const priceElement = document.querySelector('.current-price');
            priceElement.textContent = new Intl.NumberFormat('vi-VN').format(variantPrice) + 'đ';
        }
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
            alert('Đã sao chép link sản phẩm!');
        });
    }
</script>

<?php
// Capture content and set it for layout
$content = ob_get_clean();

// Set layout variables following UI guidelines
$title = htmlspecialchars($product['name']) . ' - 5S Fashion';
$meta_description = htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao tại 5S Fashion');
$meta_keywords = htmlspecialchars($product['name']) . ', thời trang, 5s fashion';

// Custom CSS following UI guidelines
$custom_css = ['css/product-detail.css'];

// Include the layout
include VIEW_PATH . '/client/layouts/app.php';
?>
