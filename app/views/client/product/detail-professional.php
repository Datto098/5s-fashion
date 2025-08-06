<?php
// Product Detail Page - Using Professional Layout
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

// Set layout variables
$title = htmlspecialchars($product['name']) . ' - 5S Fashion';
$meta_description = htmlspecialchars($product['description'] ?? 'Sản phẩm chất lượng cao tại 5S Fashion');
$meta_keywords = htmlspecialchars($product['name']) . ', thời trang, 5s fashion';

// Custom CSS files for product detail page
$custom_css = ['css/product-detail.css'];

// Custom CSS for professional product detail page
$inline_css = '
/* Professional Product Detail Styles */
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

.product-detail-section {
    padding: 3rem 0;
    background: #fff;
}

.product-images {
    position: sticky;
    top: 20px;
}

.main-image-container {
    position: relative;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.main-product-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.main-product-image:hover {
    transform: scale(1.05);
}

.product-badges {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 10;
}

.product-badges .badge {
    margin-right: 8px;
    padding: 8px 15px;
    font-size: 0.8rem;
    border-radius: 25px;
    font-weight: 600;
}

.product-info {
    padding-left: 2rem;
}

.product-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1rem;
}

.product-rating .stars {
    color: #ffc107;
    font-size: 1.2rem;
}

.rating-text {
    color: #6c757d;
    font-size: 0.9rem;
}

.product-meta {
    margin-bottom: 1.5rem;
}

.product-sku,
.product-availability {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.sku-code {
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 4px;
}

.product-price {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
}

.current-price {
    font-size: 2.5rem;
    font-weight: 700;
    display: block;
    margin-bottom: 0.5rem;
}

.original-price {
    font-size: 1.2rem;
    text-decoration: line-through;
    opacity: 0.8;
    margin-right: 10px;
}

.discount-percent {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.product-description {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    border-left: 4px solid #dc3545;
}

.product-description h5 {
    color: #dc3545;
    margin-bottom: 1rem;
    font-weight: 600;
}

.purchase-actions {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.quantity-selector {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.quantity-btn {
    background: #fff;
    border: none;
    width: 45px;
    height: 45px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #495057;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:hover {
    background: #dc3545;
    color: white;
}

.quantity-input {
    border: none;
    width: 80px;
    text-align: center;
    padding: 12px 8px;
    font-weight: 600;
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
    gap: 15px;
    align-items: center;
}

.action-buttons .btn {
    padding: 15px 25px;
    font-weight: 600;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-add-cart {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.btn-add-cart:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
}

.btn-buy-now {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-buy-now:hover {
    background: linear-gradient(135deg, #20c997, #17a2b8);
}

.wishlist-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 1.5rem;
}

.wishlist-btn:hover {
    background: #ff6b6b;
    border-color: #ff6b6b;
    color: white;
    transform: scale(1.1);
}

.product-features {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem 0;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item i {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 1.2rem;
}

.feature-text strong {
    display: block;
    color: #495057;
    font-weight: 600;
    margin-bottom: 3px;
    font-size: 1.1rem;
}

.feature-text span {
    color: #6c757d;
    font-size: 0.9rem;
}

.variants-section {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem 0;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.variants-section h4 {
    color: #dc3545;
    margin-bottom: 1.5rem;
    font-weight: 700;
}

.variant-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    margin: 1rem 0;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.variant-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #dc3545;
}

.social-share {
    border-top: 2px solid #e9ecef;
    padding-top: 2rem;
    margin-top: 2rem;
}

.share-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    display: block;
    font-size: 1.1rem;
}

.share-buttons {
    display: flex;
    gap: 15px;
}

.share-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.share-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    color: white;
}

.share-btn.facebook { background: linear-gradient(135deg, #1877f2, #0d6efd); }
.share-btn.twitter { background: linear-gradient(135deg, #1da1f2, #0d8ce8); }
.share-btn.pinterest { background: linear-gradient(135deg, #bd081c, #a00717); }
.share-btn.copy { background: linear-gradient(135deg, #6c757d, #5a6268); }

/* Responsive Design */
@media (max-width: 991px) {
    .product-info {
        padding-left: 0;
        margin-top: 2rem;
    }

    .product-title {
        font-size: 2rem;
    }

    .current-price {
        font-size: 2rem;
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

    .action-buttons {
        grid-template-columns: 1fr;
        grid-template-rows: repeat(3, 1fr);
        gap: 12px;
    }

    .wishlist-btn {
        width: 100%;
        height: 50px;
        border-radius: 10px;
        font-size: 1rem;
    }

    .purchase-actions {
        padding: 1rem;
    }

    .main-product-image {
        height: 300px;
    }
}
';

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
                    <!-- Product Title & Rating -->
                    <div class="product-header">
                        <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                        <div class="product-rating">
                            <div class="stars">
                                <?php
                                $rating = $product['rating'] ?? 4.5;
                                for ($i = 1; $i <= 5; $i++):
                                ?>
                                    <i class="<?= $i <= $rating ? 'fas' : 'far' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text">(<?= $product['review_count'] ?? 0 ?> đánh giá)</span>
                        </div>

                        <div class="product-meta">
                            <div class="product-sku">
                                <strong>Mã sản phẩm:</strong>
                                <span class="sku-code"><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span>
                            </div>
                            <div class="product-availability">
                                <?php if (($product['stock_quantity'] ?? 0) > 0): ?>
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span class="text-success">Còn hàng (<?= $product['stock_quantity'] ?> sản phẩm)</span>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger"></i>
                                    <span class="text-danger">Hết hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Product Price -->
                    <div class="product-price">
                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                            <span class="current-price"><?= number_format($product['sale_price'], 0, ',', '.') ?>₫</span>
                            <div>
                                <span class="original-price"><?= number_format($product['price'], 0, ',', '.') ?>₫</span>
                                <span class="discount-percent">
                                    -<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>%
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="current-price"><?= number_format($product['price'], 0, ',', '.') ?>₫</span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Description -->
                    <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <h5><i class="fas fa-info-circle me-2"></i>Mô tả sản phẩm</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Purchase Actions -->
                    <div class="purchase-actions">
                        <div class="quantity-selector">
                            <label class="fw-bold text-dark">Số lượng:</label>
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="changeQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" id="productQuantity" value="1" min="1" max="<?= $product['stock_quantity'] ?? 99 ?>">
                                <button class="quantity-btn" onclick="changeQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="btn btn-add-cart" onclick="addToCart(<?= $product['id'] ?>)">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span class="btn-text">Thêm vào giỏ</span>
                            </button>
                            <button class="btn btn-buy-now" onclick="buyNow(<?= $product['id'] ?>)">
                                <i class="fas fa-bolt me-2"></i>
                                <span class="btn-text">Mua ngay</span>
                            </button>
                            <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" title="Thêm vào yêu thích">
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
                                <span>Đơn hàng từ 500.000₫</span>
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
                        <span class="share-label"><i class="fas fa-share-alt me-2"></i>Chia sẻ:</span>
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

<!-- Product Variants -->
<?php if (!empty($variants) && is_array($variants)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="variants-section">
            <h4><i class="fas fa-palette me-2"></i>Các phiên bản sản phẩm (<?= count($variants) ?>)</h4>
            <p class="text-muted mb-4">Chọn phiên bản phù hợp với sở thích của bạn</p>

            <?php foreach ($variants as $variant): ?>
            <div class="variant-item">
                <div>
                    <strong><?= htmlspecialchars($variant['variant_name'] ?? 'Phiên bản') ?></strong>
                    <div class="mt-2">
                        <?php if (!empty($variant['color'])): ?>
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-palette me-1"></i><?= htmlspecialchars($variant['color']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($variant['size'])): ?>
                            <span class="badge bg-info">
                                <i class="fas fa-ruler me-1"></i><?= htmlspecialchars($variant['size']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-end">
                    <div class="h5 text-success mb-1">
                        <?= number_format($variant['price'] ?? $product['price'], 0, ',', '.') ?>₫
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-box me-1"></i>Còn: <?= $variant['stock_quantity'] ?? 0 ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Capture content and set it for layout
$content = ob_get_clean();

// Custom JavaScript for product detail page
$inline_js = '
// Product functionality
function changeQuantity(change) {
    const input = document.getElementById("productQuantity");
    const currentValue = parseInt(input.value) || 1;
    const maxValue = parseInt(input.max) || 99;
    const newValue = Math.max(1, Math.min(maxValue, currentValue + change));
    input.value = newValue;
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById("productQuantity").value) || 1;

    // Add loading state
    const btn = document.querySelector(".btn-add-cart");
    const btnText = btn.querySelector(".btn-text");
    const originalText = btnText.textContent;

    btn.disabled = true;
    btnText.textContent = "Đang thêm...";
    btn.classList.add("loading");

    // Simulate API call
    setTimeout(() => {
        btn.disabled = false;
        btnText.textContent = originalText;
        btn.classList.remove("loading");

        // Show success notification
        showNotification("Đã thêm " + quantity + " sản phẩm vào giỏ hàng!", "success");
    }, 1000);
}

function buyNow(productId) {
    const quantity = parseInt(document.getElementById("productQuantity").value) || 1;

    // Add loading state
    const btn = document.querySelector(".btn-buy-now");
    const btnText = btn.querySelector(".btn-text");
    const originalText = btnText.textContent;

    btn.disabled = true;
    btnText.textContent = "Đang xử lý...";
    btn.classList.add("loading");

    // Simulate API call
    setTimeout(() => {
        btn.disabled = false;
        btnText.textContent = originalText;
        btn.classList.remove("loading");

        showNotification("Đang chuyển đến trang thanh toán...", "info");

        // Redirect to checkout (simulation)
        setTimeout(() => {
            // window.location.href = "/checkout";
            alert("Chuyển đến trang thanh toán (Demo)");
        }, 1500);
    }, 1000);
}

function toggleWishlist(productId) {
    const btn = document.querySelector(".wishlist-btn");
    const icon = btn.querySelector("i");

    btn.disabled = true;

    setTimeout(() => {
        btn.disabled = false;

        if (icon.classList.contains("far")) {
            icon.className = "fas fa-heart";
            btn.style.background = "#ff6b6b";
            btn.style.borderColor = "#ff6b6b";
            btn.style.color = "white";
            showNotification("Đã thêm vào danh sách yêu thích!", "success");
        } else {
            icon.className = "far fa-heart";
            btn.style.background = "#fff";
            btn.style.borderColor = "#e9ecef";
            btn.style.color = "#6c757d";
            showNotification("Đã xóa khỏi danh sách yêu thích!", "info");
        }
    }, 500);
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, "facebook-share", "width=600,height=400");
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, "twitter-share", "width=600,height=400");
}

function shareOnPinterest() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    const image = encodeURIComponent(document.getElementById("mainProductImage").src);
    window.open(`https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`, "pinterest-share", "width=600,height=400");
}

function copyProductLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showNotification("Đã sao chép link sản phẩm!", "success");
    }).catch(() => {
        showNotification("Không thể sao chép link!", "error");
    });
}

function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `alert alert-${type === "success" ? "success" : type === "error" ? "danger" : "info"} notification-toast`;
    notification.innerHTML = `
        <i class="fas fa-${type === "success" ? "check-circle" : type === "error" ? "exclamation-circle" : "info-circle"} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease-out;
    `;

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
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = "slideOutRight 0.3s ease-in";
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 4000);
}

// Image zoom on click
document.getElementById("mainProductImage").addEventListener("click", function() {
    this.style.transform = this.style.transform === "scale(1.2)" ? "scale(1)" : "scale(1.2)";
    this.style.transition = "transform 0.3s ease";
    this.style.cursor = this.style.transform === "scale(1.2)" ? "zoom-out" : "zoom-in";
});
';

// Include the layout
include VIEW_PATH . '/client/layouts/app.php';
?>
