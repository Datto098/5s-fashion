<script>
// Đảm bảo disable nút tăng khi đạt max, disable input khi hết hàng
document.addEventListener('DOMContentLoaded', function() {
    function syncQuantityInputState() {
        const qtyInput = document.getElementById('quantity');
        const btnInc = qtyInput?.parentElement.querySelector('.btn-plus');
        const btnDec = qtyInput?.parentElement.querySelector('.btn-minus');
        const maxQty = window.selectedVariantDetail?.maxQty || 99;
        const inStock = maxQty > 0;
        if (qtyInput) {
            qtyInput.readOnly = true;
            qtyInput.disabled = !inStock;
            if (+qtyInput.value > maxQty) qtyInput.value = maxQty;
            if (+qtyInput.value < 1) qtyInput.value = 1;
            qtyInput.min = 1;
            qtyInput.max = maxQty;
        }
        if (btnInc) btnInc.disabled = !inStock || (+qtyInput.value >= maxQty);
        if (btnDec) btnDec.disabled = !inStock || (+qtyInput.value <= 1);
    }
    // Gọi lại khi chọn màu/size
    window._oldUpdateSelectedVariantDetail = window.updateSelectedVariantDetail;
    window.updateSelectedVariantDetail = function() {
        window._oldUpdateSelectedVariantDetail();
        syncQuantityInputState();
    };
    // Gọi khi thay đổi số lượng
    const qtyInput = document.getElementById('quantity');
    if (qtyInput) {
        qtyInput.addEventListener('input', syncQuantityInputState);
    }
    // Gọi lần đầu
    setTimeout(syncQuantityInputState, 100);
});
</script>
<script>
// Lấy variants từ API và render lại chọn màu/size bằng JS giống modal
document.addEventListener('DOMContentLoaded', function() {
    const productId = <?= (int)$product['id'] ?>;
    // store the initial product price text so we can revert when no variant selected
    try { window._initialProductPrice = document.querySelector('.current-price')?.textContent?.trim() || null; } catch(e) {}
    fetch(`/zone-fashion/ajax/product/quickview?id=${productId}`)
        .then(res => res.json())
        .then(data => {
            try {
                // support multiple response shapes
                const variants = (data && data.product && data.product.variants) || data.variants || (data.data && data.data.variants) || [];
                if (!Array.isArray(variants) || variants.length === 0) {
                    window.sizesByColor = window.sizesByColor || {};
                    return;
                }
            // Group variants by color
            const colorMap = {};
            const sizesByColor = {};
            variants.forEach(variant => {
                const color = (variant.color || '').trim();
                const color_code = variant.color_code || '#ccc';
                if (!colorMap[color]) colorMap[color] = color_code;
                if (!sizesByColor[color]) sizesByColor[color] = [];
                sizesByColor[color].push(variant);
            });
            // Render color buttons
            const colorOptionsDiv = document.querySelector('.color-options');
            if (colorOptionsDiv) {
                let idx = 0;
                colorOptionsDiv.innerHTML = Object.entries(colorMap).map(([color, color_code]) => `
                    <button type="button" class="btn btn-outline-secondary color-option${idx++ === 0 ? ' active' : ''}"
                        data-color="${color}"
                        style="border-radius:999px;padding:4px 6px;min-width:32px;min-height:32px;display:inline-flex;align-items:center;justify-content:center;border-width:2px;${idx === 1 ? 'border:2.5px solid #007bff;box-shadow:0 0 0 2px #b3d7ff;' : ''}">
                        <span class="color-swatch" style="display:inline-block;width:18px;height:18px;border-radius:50%;background:${color_code};border:1px solid #ccc;vertical-align:middle;"></span>
                    </button>
                `).join('');
            }
            // Render size buttons for first color
            const sizeOptionsDiv = document.getElementById('sizeOptionsDetail');
            function renderSizeOptions(selectedColor) {
                const variants = sizesByColor[selectedColor] || [];
                let html = '';
                let firstAvailableIdx = -1;
                let sizeSet = [];
                variants.forEach((variant, idx) => {
                    const size = variant.size || 'One Size';
                    if (sizeSet.includes(size)) return;
                    sizeSet.push(size);
                    const outOfStock = !((variant.stock_quantity || variant.stock) > 0);
                    if (!outOfStock && firstAvailableIdx === -1) firstAvailableIdx = idx;
                    html += `<button type="button" class="btn btn-outline-secondary size-option${(!outOfStock && firstAvailableIdx === idx) ? ' active' : ''}"
                        data-variant-id="${variant.id}"
                        data-size="${size}"
                        data-price="${variant.price}"
                        data-color="${variant.color}"
                        data-stock="${variant.stock_quantity || variant.stock || 0}"
                        ${outOfStock ? 'disabled style=\'opacity:0.5;cursor:not-allowed;\'' : ''}>
                        ${size}
                    </button> `;
                });
                if (sizeOptionsDiv) sizeOptionsDiv.innerHTML = html;
                // Ensure a visible, in-stock size is selected programmatically so state is consistent
                // Use a slightly longer delay to let the browser parse the inserted HTML and avoid race conditions
                setTimeout(function(){
                    try {
                        const firstAvailableBtn = sizeOptionsDiv.querySelector('.size-option:not([disabled])');
                        if (firstAvailableBtn) {
                            sizeOptionsDiv.querySelectorAll('.size-option').forEach(b=>b.classList.remove('active'));
                            firstAvailableBtn.classList.add('active');
                        }
                    } catch(e) {}
                    try{ updateSelectedVariantDetail(); } catch(e) {}
                    try{ updateDetailAddToCartState(); } catch(e) {}
                }, 60);
            }
            // Lần đầu render size cho màu đầu tiên
            const firstColor = Object.keys(colorMap)[0];
            renderSizeOptions(firstColor);
            // Sự kiện chọn màu
            if (colorOptionsDiv) {
                colorOptionsDiv.addEventListener('click', function(e) {
                    const colorButton = e.target.closest('.color-option');
                    if (!colorButton) return;
                    const selectedColor = colorButton.getAttribute('data-color');
                    colorOptionsDiv.querySelectorAll('.color-option').forEach(btn => {
                        btn.classList.remove('active');
                        btn.style.border = '';
                        btn.style.boxShadow = '';
                    });
                    colorButton.classList.add('active');
                    colorButton.style.border = '2.5px solid #007bff';
                    colorButton.style.boxShadow = '0 0 0 2px #b3d7ff';
                    renderSizeOptions(selectedColor);
                    // renderSizeOptions will call updateSelectedVariantDetail/updateDetailAddToCartState after it finishes,
                    // so no extra immediate timeouts are necessary here.
                });
            }
            // Sự kiện chọn size
            if (sizeOptionsDiv) {
                sizeOptionsDiv.addEventListener('click', function(e) {
                    const sizeButton = e.target.closest('.size-option');
                    if (sizeButton && !sizeButton.disabled) {
                        sizeOptionsDiv.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('active'));
                        sizeButton.classList.add('active');
                            setTimeout(updateSelectedVariantDetail, 10);
                            // Update add-to-cart button after size selection
                            setTimeout(function(){ try{ updateDetailAddToCartState(); }catch(e){} }, 12);
                    }
                });
            }
            // Lưu lại để các hàm khác dùng
            window.sizesByColor = sizesByColor;
            setTimeout(updateSelectedVariantDetail, 50);
            } catch(e) {
                console.error('Error processing quickview response', e, data);
                window.sizesByColor = window.sizesByColor || {};
                setTimeout(updateSelectedVariantDetail, 50);
            }
        });
});
// Hàm đồng bộ variant đang chọn giống modal
function updateSelectedVariantDetail() {
    const colorBtn = document.querySelector('.color-option.active');
    const color = colorBtn ? colorBtn.getAttribute('data-color') : null;
    const sizeBtn = document.querySelector('.size-option.active');
    const size = sizeBtn ? sizeBtn.getAttribute('data-size') : null;
    const variantId = sizeBtn ? sizeBtn.getAttribute('data-variant-id') : null;
    const price = sizeBtn ? sizeBtn.getAttribute('data-price') : null;
    let maxQty = 99;
    if (sizeBtn) {
        const vColor = sizeBtn.getAttribute('data-color') || color;
        const variants = (window.sizesByColor && window.sizesByColor[vColor]) || [];
        const found = variants.find(v => v.id == variantId);
    if (found && (found.stock_quantity || found.stock)) maxQty = found.stock_quantity || found.stock;
    }
    window.selectedVariantDetail = variantId ? {id: variantId, color, size, price, maxQty: maxQty} : null;
    // Cập nhật max cho input số lượng
    const qtyInput = document.getElementById('quantity');
    if (qtyInput && window.selectedVariantDetail) {
        qtyInput.max = window.selectedVariantDetail.maxQty;
        if (+qtyInput.value > window.selectedVariantDetail.maxQty) qtyInput.value = window.selectedVariantDetail.maxQty;
    }
    // Update displayed price when variant selected
    try {
        const priceElement = document.querySelector('.current-price');
        if (priceElement) {
            if (window.selectedVariantDetail && window.selectedVariantDetail.price) {
                const num = parseFloat(window.selectedVariantDetail.price) || 0;
                priceElement.textContent = new Intl.NumberFormat('vi-VN').format(num) + 'đ';
            } else if (window._initialProductPrice) {
                priceElement.textContent = window._initialProductPrice;
            }
        }
    } catch(e) {}
    // Update add-to-cart state on detail page
    try { updateDetailAddToCartState(); } catch(e) {}
}
// Ghi đè hàm handleAddToCart để truyền đúng variant id và số lượng
function handleAddToCart(productId) {
    const qtyInput = document.getElementById('quantity');
    const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
    const selected = window.selectedVariantDetail;
    if (!selected || !selected.id) {
        alert('Vui lòng chọn màu sắc và kích thước!');
        return;
    }
    if (quantity > selected.maxQty) {
        alert('Số lượng vượt quá tồn kho!');
        return;
    }
    // Lấy object variant đầy đủ từ sizesByColor
    let fullVariant = null;
    if (window.sizesByColor && selected.color && window.sizesByColor[selected.color]) {
        fullVariant = window.sizesByColor[selected.color].find(v => v.id == selected.id);
    }
    if (!fullVariant) {
        alert('Không tìm thấy biến thể sản phẩm!');
        return;
    }
    // Sử dụng hàm addToCart của client.js nếu có
    if (window.addToCart) {
        window.addToCart(productId, quantity, fullVariant);
    } else {
        // Fallback ajax, truyền đủ thông tin variant
        fetch('/zone-fashion/ajax/cart/add', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                variant_id: fullVariant.id,
                variant: fullVariant
            })
        })
        .then(r=>r.json()).then(data=>{
            if(data.success) alert('Đã thêm sản phẩm vào giỏ hàng!');
            else alert(data.message||'Có lỗi xảy ra');
        }).catch(()=>alert('Có lỗi xảy ra khi thêm vào giỏ hàng'));
    }
}
// Hàm đồng bộ variant đang chọn giống modal
function updateSelectedVariantDetail() {
    const colorBtn = document.querySelector('.color-option.active');
    const color = colorBtn ? colorBtn.getAttribute('data-color') : null;
    const sizeBtn = document.querySelector('.size-option.active');
    const size = sizeBtn ? sizeBtn.getAttribute('data-size') : null;
    const variantId = sizeBtn ? sizeBtn.getAttribute('data-variant-id') : null;
    const price = sizeBtn ? sizeBtn.getAttribute('data-price') : null;
    let maxQty = 99;
    if (sizeBtn) {
        const vColor = sizeBtn.getAttribute('data-color') || color;
        const variants = (window.sizesByColor && window.sizesByColor[vColor]) || [];
        const found = variants.find(v => v.id == variantId);
    if (found && (found.stock_quantity || found.stock)) maxQty = found.stock_quantity || found.stock;
    }
    window.selectedVariantDetail = variantId ? {id: variantId, color, size, price, maxQty: maxQty} : null;
    // Cập nhật max cho input số lượng
    const qtyInput = document.getElementById('quantity');
    if (qtyInput && window.selectedVariantDetail) {
        qtyInput.max = window.selectedVariantDetail.maxQty;
        if (+qtyInput.value > window.selectedVariantDetail.maxQty) qtyInput.value = window.selectedVariantDetail.maxQty;
    }
    // Update displayed price when variant selected (duplicate function guard)
    try {
        const priceElement = document.querySelector('.current-price');
        if (priceElement) {
            if (window.selectedVariantDetail && window.selectedVariantDetail.price) {
                const num = parseFloat(window.selectedVariantDetail.price) || 0;
                priceElement.textContent = new Intl.NumberFormat('vi-VN').format(num) + 'đ';
            } else if (window._initialProductPrice) {
                priceElement.textContent = window._initialProductPrice;
            }
        }
    } catch(e) {}
}
// Update add-to-cart button on detail page based on selected variant availability
function updateDetailAddToCartState() {
    const btn = document.querySelector('.action-buttons .btn-add-cart') ||
                document.querySelector('.action-buttons button[onclick^="handleAddToCart("]') ||
                document.querySelector('.action-buttons button');
    if (!btn) return;

    // Prefer DOM data-stock values when available (more authoritative for rendered buttons)
    let available = false;

    try {
        // 1) Check active size button first
        const activeSize = document.querySelector('.size-option.active');
        if (activeSize) {
            const stockAttr = activeSize.getAttribute('data-stock');
            const stock = parseInt(stockAttr || '0', 10);
            if (stock > 0) available = true;
        }
        // 2) If still unknown, check any size-option with data-stock > 0
        if (!available) {
            const any = Array.from(document.querySelectorAll('.size-option')).some(el => parseInt(el.getAttribute('data-stock') || '0', 10) > 0);
            if (any) available = true;
        }
    } catch(e) {
        // swallow and fallback
    }

    // 3) Fallback to selectedVariantDetail if DOM didn't indicate availability
    if (!available && window.selectedVariantDetail) {
        available = (window.selectedVariantDetail.maxQty || 0) > 0;
    }

    if (!available) {
        // keep .btn-add-cart but add out-of-stock modifier so layout is preserved
        btn.classList.add('out-of-stock');
        btn.disabled = true;
        btn.setAttribute('aria-disabled', 'true');
        btn.innerHTML = '<i class="fas fa-times me-2"></i>Hết Hàng';
    } else {
        btn.classList.remove('out-of-stock');
        btn.disabled = false;
        btn.removeAttribute('aria-disabled');
        btn.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ';
    }
}

// Ensure detail add-to-cart state syncs after variant updates
setTimeout(updateDetailAddToCartState, 100);
</script>
<?php

// Product Detail Page - Clean Version
// Validate product data
if (!isset($product) || empty($product)) {
    header('HTTP/1.0 404 Not Found');
    echo "Product not found";
    exit;
}

// Prepare image URL
$imageUrl = '/zone-fashion/public/assets/images/default-product.jpg';
if (!empty($product['featured_image'])) {
    $imagePath = $product['featured_image'];
    if (strpos($imagePath, '/uploads/') === 0) {
        $cleanPath = substr($imagePath, 9);
    } elseif (strpos($imagePath, 'uploads/') === 0) {
        $cleanPath = substr($imagePath, 8);
    } else {
        $cleanPath = ltrim($imagePath, '/');
    }
    $imageUrl = '/zone-fashion/serve-file.php?file=' . urlencode($cleanPath);
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
                                        'url' => '/zone-fashion/serve-file.php?file=' . urlencode($cleanPath),
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
                                    'url' => '/zone-fashion/serve-file.php?file=' . urlencode($cleanPath),
                                    'alt' => htmlspecialchars($variant['variant_name']),
                                    'type' => 'variant'
                                ];
                            }
                        }
                    }

                    // Fallback to default image if no images
                    if (empty($allImages)) {
                        $allImages[] = [
                            'url' => '/zone-fashion/public/assets/images/default-product.jpg',
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
                            // Lấy điểm trung bình động từ controller
                            $avgRating = isset($ratingStats['average']) ? $ratingStats['average'] : 0;
                            // $reviewCount đã được truyền động từ controller
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
                        <span class="rating-text">(<?= $reviewCount ?> đánh giá)</span>
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
                        <?php
                        // Group variants by color using attribute_details for reliability
                        $colors = [];
                        $sizesByColor = [];
                        foreach ($variants as $variant) {
                            $color = null;
                            $color_code = null;
                            // Parse attribute_details if available
                            if (!empty($variant['attribute_details'])) {
                                $attrs = explode('|', $variant['attribute_details']);
                                foreach ($attrs as $attr) {
                                    $parts = explode(':', $attr);
                                    if (count($parts) >= 4 && $parts[0] === 'color') {
                                        $color = $parts[2];
                                        $color_code = $parts[3] !== '' ? $parts[3] : '#ccc';
                                    }
                                }
                            }
                            // Fallback if not found
                            if ($color === null) {
                                $color = isset($variant['color']) && $variant['color'] !== null && $variant['color'] !== '' ? $variant['color'] : 'Không xác định';
                                $color_code = isset($variant['color_code']) && $variant['color_code'] !== null && $variant['color_code'] !== '' ? $variant['color_code'] : '#ccc';
                            }
                            if (!isset($colors[$color])) {
                                $colors[$color] = $color_code;
                            }
                            $sizesByColor[$color][] = $variant;
                        }
                        $firstColor = array_key_first($colors);

                        // Helper: extract size from attribute_details
                        function get_variant_size($variant) {
                            if (!empty($variant['attribute_details'])) {
                                $attrs = explode('|', $variant['attribute_details']);
                                foreach ($attrs as $attr) {
                                    $parts = explode(':', $attr);
                                    if (count($parts) >= 4 && $parts[0] === 'size') {
                                        return $parts[2];
                                    }
                                }
                            }
                            return isset($variant['size']) && $variant['size'] ? $variant['size'] : 'One Size';
                        }
                        ?>
                        <div class="variants-section">
                            <div class="mb-2"><strong>Chọn Màu Sắc:</strong></div>
                            <div class="color-options mb-3">
                                <?php $colorIdx = 0; foreach ($colors as $color => $color_code): ?>
                                    <button type="button"
                                        class="btn btn-outline-secondary color-option<?= $colorIdx === 0 ? ' active' : '' ?>"
                                        data-color="<?= htmlspecialchars($color) ?>"
                                        style="border-radius:999px;padding:4px 6px;min-width:32px;min-height:32px;display:inline-flex;align-items:center;justify-content:center;border-width:2px;<?= $colorIdx === 0 ? 'border:2.5px solid #007bff;box-shadow:0 0 0 2px #b3d7ff;' : '' ?>">
                                        <span class="color-swatch" style="display:inline-block;width:18px;height:18px;border-radius:50%;background:<?= htmlspecialchars($color_code) ?>;border:1px solid #ccc;vertical-align:middle;"></span>
                                    </button>
                                <?php $colorIdx++; endforeach; ?>
                            </div>
                            <div class="mb-2"><strong>Chọn Kích Thước:</strong></div>
                            <div class="size-options mb-3" id="sizeOptionsDetail">
                                <?php
                                // Render size buttons cho màu đầu tiên để user thấy ngay khi load trang
                                $sizeSet = [];
                                $firstAvailableIdx = -1;
                                if (!empty($sizesByColor[$firstColor])) {
                                    foreach ($sizesByColor[$firstColor] as $idx => $variant) {
                                        $size = get_variant_size($variant);
                                        if (in_array($size, $sizeSet)) continue;
                                        $sizeSet[] = $size;
                                        $stockVal = isset($variant['stock_quantity']) && $variant['stock_quantity'] !== '' ? (int)$variant['stock_quantity'] : (isset($variant['stock']) ? (int)$variant['stock'] : 0);
                                        $outOfStock = $stockVal <= 0;
                                        if (!$outOfStock && $firstAvailableIdx === -1) $firstAvailableIdx = $idx;
                                        $colorSafe = isset($variant['color']) && $variant['color'] !== null ? $variant['color'] : '';
                                        echo '<button type="button" class="btn btn-outline-secondary size-option'.((!$outOfStock && $firstAvailableIdx === $idx)?' active':'').'" data-variant-id="'.$variant['id'].'" data-size="'.htmlspecialchars($size).'" data-price="'.$variant['price'].'" data-color="'.htmlspecialchars($colorSafe).'" data-stock="'.$stockVal.'" '.($outOfStock?'disabled style=\'opacity:0.5;cursor:not-allowed;\'':'').'>' . $size . '</button> ';
                                    }
                                }
                                ?>
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

<!-- Product Details Tabs -->
<section class="product-tabs-section">
    <div class="container">
        <div class="product-tabs">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">
                        <i class="fas fa-info-circle me-2"></i>Chi tiết sản phẩm
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                        <i class="fas fa-star me-2"></i>Đánh giá sản phẩm
                        <span class="badge bg-primary ms-1"><?= $reviewCount ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">
                        <i class="fas fa-shipping-fast me-2"></i>Vận chuyển & Đổi trả
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <div class="description-content">
                        <h4>Mô tả sản phẩm</h4>
                        <div class="product-description">
                            <?php if (!empty($product['description'])): ?>
                                <?= nl2br(htmlspecialchars($product['description'])) ?>
                            <?php else: ?>
                                <p>Thông tin mô tả sản phẩm đang được cập nhật...</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($product['specifications'])): ?>
                            <h5 class="mt-4">Thông số kỹ thuật</h5>
                            <div class="specifications">
                                <?= nl2br(htmlspecialchars($product['specifications'])) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Product Attributes -->
                        <div class="product-attributes mt-4">
                            <h5>Thông tin chi tiết</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><strong>Mã sản phẩm:</strong> <?= $product['sku'] ?? 'N/A' ?></li>
                                        <li><strong>Danh mục:</strong> <?= $product['category_name'] ?? 'N/A' ?></li>
                                        <li><strong>Trạng thái:</strong>
                                            <span class="badge bg-success">Còn hàng</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><strong>Thương hiệu:</strong> zone Fashion</li>
                                        <li><strong>Xuất xứ:</strong> Việt Nam</li>
                                        <li><strong>Bảo hành:</strong> 12 tháng</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="reviews-content">
                        <div class="reviews-summary">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="rating-overview">
                                        <div class="average-rating">
                                            <span class="rating-number"><?= number_format($avgRating ?? 0, 1) ?></span>
                                            <div class="rating-stars">
                                                <?php
                                                $avgRating = $avgRating ?? 0;
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
                                            <div class="rating-count">(<?= $reviewCount ?? 0 ?> đánh giá)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="rating-breakdown">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <?php
                                            $starCount = isset($ratingStats['counts'][$i]) ? $ratingStats['counts'][$i] : 0;
                                            $percent = $reviewCount > 0 ? round($starCount / $reviewCount * 100) : 0;
                                            ?>
                                            <div class="rating-bar">
                                                <span class="rating-label"><?= $i ?> sao</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="rating-percentage"><?= $percent ?>% (<?= $starCount ?>)</span>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Write Review Button -->
                        <div class="write-review-section">
                            <?php if (isLoggedIn()): ?>
                                <?php if ($canReview): ?>
                                    <button id="btn-write-review" class="btn btn-primary" onclick="showReviewForm()">
                                        <i class="fas fa-edit me-2"></i>Viết đánh giá
                                    </button>
                                <?php elseif ($hasReviewed): ?>
                                    <button id="btn-reviewed" class="btn btn-secondary" disabled>
                                        <i class="fas fa-check me-2"></i>Bạn đã đánh giá sản phẩm này
                                    </button>
                                <?php elseif (!$hasCompletedOrders): ?>
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-shopping-cart me-2"></i>Bạn cần mua và nhận sản phẩm để đánh giá
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="/auth/login" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để đánh giá
                                </a>
                            <?php endif; ?>
                            <script>
                                // Sau khi xóa review bằng AJAX, nếu không còn review nào thì cập nhật lại nút "Viết đánh giá"
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Lắng nghe sự kiện xóa review từ JS ngoài (review.js)
                                    document.addEventListener('review:deleted', function(e) {
                                        // Ẩn nút "Bạn đã đánh giá sản phẩm này"
                                        var reviewedBtn = document.getElementById('btn-reviewed');
                                        if (reviewedBtn) reviewedBtn.style.display = 'none';
                                        // Hiện nút "Viết đánh giá"
                                        var writeBtn = document.getElementById('btn-write-review');
                                        if (writeBtn) writeBtn.style.display = '';
                                    });
                                });
                            </script>
                        </div>

                        <!-- Review Form (Hidden by default) -->
                        <div id="reviewForm" class="review-form-container" style="display: none;">
                            <form id="submitReviewForm" class="review-form">
                                <h5>Đánh giá sản phẩm</h5>
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá của bạn</label>
                                    <div class="rating-input">
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
                                    <label for="reviewTitle" class="form-label">Tiêu đề đánh giá</label>
                                    <input type="text" class="form-control" id="reviewTitle" name="title" placeholder="Tóm tắt đánh giá của bạn" required>
                                </div>
                                <div class="mb-3">
                                    <label for="reviewContent" class="form-label">Nội dung đánh giá</label>
                                    <textarea class="form-control" id="reviewContent" name="content" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..." required></textarea>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="hideReviewForm()">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Reviews List -->
                        <div class="reviews-list">
                            <?php if (!empty($reviews)): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review-item position-relative">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <div class="reviewer-avatar">
                                                    <?php if (!empty($review['customer_avatar'])): ?>
                                                        <img src="<?= $review['customer_avatar'] ?>" alt="<?= htmlspecialchars($review['customer_name'] ?? '') ?>">
                                                    <?php else: ?>
                                                        <div class="avatar-placeholder">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="reviewer-details">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <h6 class="reviewer-name mb-0"><?= htmlspecialchars($review['customer_name'] ?? '') ?></h6>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="review-rating mb-0">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <div class="review-date mb-0"><?= date('d/m/Y', strtotime($review['created_at'])) ?></div>
                                                    </div>
                                                    <div class="review-action-buttons position-absolute end-0 d-flex flex-column align-items-end gap-2" style="top: 40px; margin-right: 19px; z-index: 2;">
                                                        <button type="button" class="btn btn-sm btn-outline-success like-review-btn<?= !empty($review['liked_by_user']) ? ' liked' : '' ?>" data-review-id="<?= $review['id'] ?>">
                                                            <i class="fas fa-thumbs-up"></i> Hữu ích <span class="helpful-count"><?= $review['helpful_count'] ?? 0 ?></span>
                                                        </button>
                                                        <?php if (isset($userId) && !empty($review['user_id']) && $review['user_id'] == $userId): ?>
                                                            <!-- Nút sửa đã bị ẩn theo yêu cầu -->
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-review-btn" data-review-id="<?= $review['id'] ?>">
                                                                <i class="fas fa-trash"></i> Xóa
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="review-content">
                                            <?php if (!empty($review['title'])): ?>
                                                <div class="review-title fw-bold mb-1"><?= htmlspecialchars($review['title']) ?></div>
                                            <?php endif; ?>
                                            <p><?= nl2br(htmlspecialchars($review['content'])) ?></p>
                                           



                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-reviews">
                                    <div class="text-center py-5">
                                        <i class="fas fa-star-o fa-3x text-muted mb-3"></i>
                                        <h5>Chưa có đánh giá nào</h5>
                                        <p class="text-muted">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Shipping Tab -->
                <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                    <div class="shipping-content">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-shipping-fast me-2"></i>Chính sách vận chuyển</h5>
                                <ul class="shipping-policy">
                                    <li><i class="fas fa-check text-success me-2"></i>Miễn phí vận chuyển cho đơn hàng từ 500.000đ</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Giao hàng nhanh trong 1-2 ngày</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Hỗ trợ giao hàng toàn quốc</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Đóng gói cẩn thận, chống ẩm</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-undo-alt me-2"></i>Chính sách đổi trả</h5>
                                <ul class="return-policy">
                                    <li><i class="fas fa-check text-success me-2"></i>Đổi trả miễn phí trong 7 ngày</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Sản phẩm còn nguyên tem, mác</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Hoàn tiền 100% nếu lỗi nhà sản xuất</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Hỗ trợ đổi size miễn phí</li>
                                </ul>
                            </div>
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
                    const max = parseInt(quantityInput.getAttribute('max')) || 99;
                    if (newValue > max) newValue = max;

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
                    const max2 = parseInt(quantityInput.getAttribute('max')) || 99;
                    if (newValue > max2) newValue = max2;

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
    }

    function getSelectedVariant() {
        // Prefer the unified state set by updateSelectedVariantDetail()
        if (window.selectedVariantDetail) {
            return window.selectedVariantDetail;
        }

        // Fallback: try to read from DOM (.size-option.active)
        const sizeBtn = document.querySelector('.size-option.active') || document.querySelector('.size-option');
        if (sizeBtn) {
            const vid = sizeBtn.getAttribute('data-variant-id');
            const price = sizeBtn.getAttribute('data-price');
            const size = sizeBtn.getAttribute('data-size');
            const color = sizeBtn.getAttribute('data-color');

            // Try to find full variant object from window.sizesByColor
            let full = null;
            try {
                if (window.sizesByColor && color && window.sizesByColor[color]) {
                    full = window.sizesByColor[color].find(v => String(v.id) === String(vid));
                }
            } catch(e) {}

            return {
                id: vid,
                price: price,
                size: size,
                color: color,
                full: full || null
            };
        }

        return null;
    }

    function addToCartFallback(productId, quantity = 1) {
        // include variant info when possible
        const selected = getSelectedVariant();
        const payload = {
            product_id: productId,
            quantity: quantity
        };
        if (selected) {
            payload.variant_id = selected.id || null;
            // include the full variant object if available
            payload.variant = selected.full ? selected.full : { id: selected.id, price: selected.price, size: selected.size, color: selected.color };
        }

        fetch('/zone-fashion/ajax/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.assign({}, payload, {
                    variant_id: payload.variant_id || (payload.variant && (payload.variant.id || payload.variant.variant_id)) || null,
                    price: payload.variant && (payload.variant.price || payload.variant.sale_price) ? (payload.variant.price || payload.variant.sale_price) : null
                }))
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
                        window.location.href = '/zone-fashion/checkout';
                    }, 1000);
                }
            });
        } else {
            addToCartFallback(productId, quantity);
            setTimeout(() => {
                window.location.href = '/zone-fashion/checkout';
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

    function showReviewForm() {
        document.getElementById('reviewForm').style.display = 'block';
        document.getElementById('reviewForm').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function hideReviewForm() {
        document.getElementById('reviewForm').style.display = 'none';
    }

    // Handle review form submission
    document.addEventListener('DOMContentLoaded', function() {
        const reviewForm = document.getElementById('submitReviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('product_id', <?= $product['id'] ?>);

                // Check if rating is selected
                const rating = formData.get('rating');
                if (!rating) {
                    alert('Vui lòng chọn số sao đánh giá');
                    return;
                }

                // Submit review
                fetch('http://localhost/zone-fashion/api/reviews', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Cảm ơn bạn đã đánh giá sản phẩm!');
                            hideReviewForm();
                            // Reload page to show new review
                            window.location.reload();
                        } else {
                            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra, vui lòng thử lại');
                    });
            });
        }
    });

    document.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-edit-review');
        if (button) {
            const reviewId = button.getAttribute('data-review-id');
            const reviewItem = button.closest('.review-item');

            // Fetch review data and populate the form
            fetch(`http://localhost/zone-fashion/api/reviews/${reviewId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.review) {
                        const review = data.review;
                        document.getElementById('reviewTitle').value = review.title;
                        document.getElementById('reviewContent').value = review.content;

                        // Set the rating
                        const ratingInputs = document.querySelectorAll('input[name="rating"]');
                        ratingInputs.forEach(input => {
                            input.checked = false;
                        });
                        const reviewRating = Math.round(review.rating);
                        if (reviewRating > 0 && reviewRating <= 5) {
                            document.getElementById(`star${reviewRating}`).checked = true;
                        }

                        // Show the review form
                        showReviewForm();

                        // Scroll to the review form
                        document.getElementById('reviewForm').scrollIntoView({
                            behavior: 'smooth'
                        });
                    } else {
                        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra, vui lòng thử lại');
                });
        }
    });
</script>

<?php
// Capture content and set it for layout
$content = ob_get_clean();

// Set layout variables following UI guidelines
$title = htmlspecialchars($product['name']) . ' - zone Fashion';
$meta_description = htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao tại zone Fashion');
$meta_keywords = htmlspecialchars($product['name']) . ', thời trang, zone fashion';

// Custom CSS following UI guidelines
$custom_css = ['css/product-detail.css'];

// Include the layout
include VIEW_PATH . '/client/layouts/app.php';
?>