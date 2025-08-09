<?php
$title = 'Giỏ hàng - 5S Fashion';
// Start output buffering for content
ob_start();
?>

<!-- Cart Section -->
<section class="cart-section py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= url('') ?>" class="text-decoration-none">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card cart-card">
                    <div class="cart-header">
                        <h4 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Giỏ hàng của bạn
                            <span class="badge bg-light text-dark ms-2" id="cart-items-count"><?= $cartCount ?></span>
                        </h4>
                    </div>

                    <div id="cart-items-container">
                        <?php if (!empty($cartItems)): ?>
                            <!-- Cart has items -->
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item" data-item-id="<?= $item['id'] ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <?php
                                            $imagePath = $item['product_image'] ?? 'placeholder.jpg';
                                            // Remove leading slash if present
                                            $imagePath = ltrim($imagePath, '/');
                                            // Remove uploads/products/ prefix if present
                                            $imagePath = preg_replace('#^uploads/products/#', '', $imagePath);
                                            ?>
                                            <img src="/5s-fashion/serve-file.php?file=<?= urlencode('products/' . $imagePath) ?>"
                                                alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                class="product-image img-fluid">
                                        </div>
                                        <div class="col-md-4">
                                            <a href="<?= url('product/' . ($item['product_slug'] ?? '')) ?>"
                                                class="product-title"><?= htmlspecialchars($item['product_name']) ?></a>
                                            <div class="product-variant">
                                                <?php if (!empty($item['variant_attributes'])): ?>
                                                    <?= htmlspecialchars($item['variant_attributes']) ?>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">Mã SP: <span class="product-sku"><?= $item['product_sku'] ?? 'N/A' ?></span></small>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="product-price"><?= number_format($item['price'], 0, ',', '.') ?> ₫</div>
                                            <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                                                <div class="original-price"><?= number_format($item['original_price'], 0, ',', '.') ?> ₫</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="quantity-controls">
                                                <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'decrease')">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="quantity-input cart-quantity-input"
                                                    min="1" max="99" value="<?= $item['quantity'] ?>"
                                                    data-cart-id="<?= $item['id'] ?>">
                                                <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'increase')">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-2 text-end">
                                            <div class="item-total fw-bold text-danger">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫
                                            </div>
                                            <button type="button" class="remove-btn remove-cart-item mt-2"
                                                data-cart-id="<?= $item['id'] ?>"
                                                title="Xóa sản phẩm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Empty cart -->
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h5>Giỏ hàng trống</h5>
                                <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng</p>
                                <a href="<?= url('') ?>" class="btn btn-primary mt-3">
                                    <i class="fas fa-shopping-bag me-2"></i>
                                    Mua sắm ngay
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="mt-4">
                    <a href="<?= url('') ?>" class="btn btn-continue">
                        <i class="fas fa-arrow-left me-2"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <!-- Promo Code Section -->
                <div class="promo-section">
                    <h6 class="mb-3">
                        <i class="fas fa-tag me-2"></i>
                        Mã giảm giá
                    </h6>
                    <div class="input-group">
                        <input type="text" class="form-control promo-input" id="promo-code" placeholder="Nhập mã giảm giá">
                        <button class="btn promo-btn btn-primary" type="button" onclick="applyPromoCode()">
                            <i class="fas fa-check"></i>
                            Áp dụng
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="cart-summary">
                    <h5 class="mb-4">
                        <i class="fas fa-receipt me-2"></i>
                        Tổng đơn hàng
                    </h5>

                    <div id="cart-summary-content">
                        <div class="summary-row">
                            <span class="summary-label">Tạm tính:</span>
                            <span class="summary-value" id="subtotal"><?= number_format($cartTotal, 0, ',', '.') ?> ₫</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Phí vận chuyển:</span>
                            <span class="summary-value text-success">Miễn phí</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Giảm giá:</span>
                            <span class="summary-value text-success" id="discount">0 ₫</span>
                        </div>

                        <div class="summary-row summary-total">
                            <span class="summary-label">Tổng cộng:</span>
                            <span class="summary-value" id="total"><?= number_format($cartTotal, 0, ',', '.') ?> ₫</span>
                        </div>
                    </div>

                    <button class="btn btn-checkout mt-4" id="checkout-btn" <?= empty($cartItems) ? 'disabled' : '' ?>>
                        <i class="fas fa-credit-card me-2"></i>
                        Thanh toán
                    </button>

                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i>
                            Thanh toán an toàn & bảo mật
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Templates -->
    <template id="cart-item-template">
        <div class="cart-item" data-item-id="">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="" alt="" class="product-image img-fluid">
                </div>

                <div class="col-md-4">
                    <a href="" class="product-title"></a>
                    <div class="product-variant"></div>
                    <small class="text-muted">Mã SP: <span class="product-sku"></span></small>
                </div>

                <div class="col-md-2">
                    <div class="product-price"></div>
                    <div class="original-price"></div>
                </div>

                <div class="col-md-2">
                    <div class="quantity-controls">
                        <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'decrease')">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="quantity-input cart-quantity-input" min="1" max="99" value="1">
                        <button type="button" class="quantity-btn" onclick="updateCartQuantity(this, 'increase')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-2 text-end">
                    <div class="item-total fw-bold text-danger"></div>
                    <button type="button" class="remove-btn remove-cart-item mt-2" title="Xóa sản phẩm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template id="empty-cart-template">
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h5>Giỏ hàng trống</h5>
            <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng</p>
            <a href="<?= url('') ?>" class="btn btn-primary mt-3">
                <i class="fas fa-shopping-bag me-2"></i>
                Mua sắm ngay
            </a>
        </div>
    </template>



    <?php
    // Get content from buffer
    $content = ob_get_clean();

    // Set page variables for layout
    $title = 'Giỏ hàng - 5S Fashion';
    $meta_description = 'Giỏ hàng mua sắm tại 5S Fashion';

    // Custom CSS following UI guidelines
    $custom_css = ['css/cart.css'];

    // Custom JS for cart functionality
    $custom_js = ['js/cart-page.js'];

    // Include main layout
    include VIEW_PATH . '/client/layouts/app.php';
    ?>
