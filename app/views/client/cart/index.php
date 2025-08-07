<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Xem và quản lý giỏ hàng của bạn tại 5S Fashion">
    <title>Giỏ hàng - 5S Fashion</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/client.css') ?>" rel="stylesheet">

    <style>
/* Professional Cart Styles */
.cart-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 80vh;
}

.cart-hero {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.cart-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 200"><polygon fill="rgba(255,255,255,0.1)" points="0,0 1000,0 1000,120 0,200"/></svg>');
    pointer-events: none;
}

.cart-hero .container {
    position: relative;
    z-index: 2;
}

.cart-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.cart-hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    font-weight: 300;
}

.cart-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    overflow: hidden;
    border: 1px solid rgba(220, 53, 69, 0.1);
}

.cart-content {
    padding: 2rem;
}

.empty-cart {
    text-align: center;
    padding: 5rem 2rem;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
}

.empty-cart-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #dc3545, #c82333);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.empty-cart-icon i {
    font-size: 3rem;
    color: white;
}

.empty-cart h4 {
    font-size: 2rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 1rem;
}

.empty-cart p {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.btn-shopping {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    padding: 12px 24px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}

.btn-shopping:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(220, 53, 69, 0.5);
    color: white;
}

.cart-stats {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.cart-stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.cart-stat-item:last-child {
    border-bottom: none;
    font-weight: 700;
    font-size: 1.1rem;
    color: #dc3545;
}

.cart-stat-label {
    color: #6c757d;
}

.cart-stat-value {
    color: #495057;
    font-weight: 600;
}

.continue-shopping-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    margin-top: 2rem;
}

.continue-shopping-section h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.breadcrumb-section {
    background: rgba(255,255,255,0.9);
    padding: 1rem 0;
    margin-bottom: 0;
    border-bottom: 1px solid #e9ecef;
}

.breadcrumb {
    margin-bottom: 0;
    background: transparent;
}

.breadcrumb-item a {
    color: #dc3545;
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-item a:hover {
    color: #c82333;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #6c757d;
    font-weight: 600;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-weight: bold;
    color: #6c757d;
}

.empty-cart i {
    color: #dee2e6;
}

.empty-cart h3 {
    color: #6c757d;
    margin-bottom: 1rem;
}

.empty-cart p {
    color: #adb5bd;
    margin-bottom: 2rem;
}

.cart-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.cart-item:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.cart-item-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.cart-item-info {
    flex: 1;
    padding-left: 1rem;
}

.cart-item-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.5rem;
    text-decoration: none;
}

.cart-item-name:hover {
    color: #dc3545;
}

.cart-item-details {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-badge {
    background: #f8f9fa;
    color: #495057;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
}

.detail-badge.color {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-color: #2196f3;
    color: #1976d2;
}

.detail-badge.size {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    border-color: #9c27b0;
    color: #7b1fa2;
}

.cart-item-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #28a745;
    margin-bottom: 1rem;
}

.cart-item-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.quantity-control {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.quantity-btn {
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #495057;
    font-weight: 600;
}

.quantity-btn:hover {
    background: #dc3545;
    color: white;
}

.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.quantity-input {
    border: none;
    width: 60px;
    text-align: center;
    padding: 8px;
    font-weight: 600;
    background: white;
    color: #495057;
}

.quantity-input:focus {
    outline: none;
    background: #f8f9fa;
}

.remove-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remove-btn:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.cart-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 2rem;
    margin-top: 2rem;
    position: sticky;
    top: 20px;
}

.cart-summary h3 {
    color: #495057;
    margin-bottom: 1.5rem;
    font-weight: 700;
    text-align: center;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #dee2e6;
}

.summary-row:last-child {
    border-bottom: none;
    font-size: 1.2rem;
    font-weight: 700;
    color: #dc3545;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #dc3545;
}

.checkout-actions {
    margin-top: 2rem;
}

.checkout-btn {
    width: 100%;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.checkout-btn:hover {
    background: linear-gradient(135deg, #20c997, #17a2b8);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.continue-shopping {
    width: 100%;
    background: white;
    color: #6c757d;
    border: 2px solid #dee2e6;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    text-align: center;
}

.continue-shopping:hover {
    border-color: #dc3545;
    color: #dc3545;
    background: #fff5f5;
}

.clear-cart-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    margin-bottom: 1rem;
}

.clear-cart-btn:hover {
    background: #5a6268;
}

/* Loading States */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-top: 2px solid #dc3545;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .cart-content {
        padding: 1rem;
    }

    .cart-item {
        padding: 1rem;
    }

    .cart-item-controls {
        flex-direction: column;
        align-items: stretch;
    }

    .quantity-control {
        justify-content: center;
        margin-bottom: 1rem;
    }

    .cart-summary {
        position: static;
        margin-top: 1rem;
    }

    .cart-hero h1 {
        font-size: 2rem;
    }

    .cart-hero-subtitle {
        font-size: 1rem;
    }

    .btn-shopping {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        font-size: 0.9rem;
    }

    .continue-shopping-section .d-flex {
        flex-direction: column;
        align-items: center;
    }

    .continue-shopping-section .btn-shopping {
        width: 100%;
        max-width: 280px;
        margin-bottom: 10px;
    }
}

/* Additional Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.cart-container {
    animation: fadeInUp 0.6s ease-out;
}

.continue-shopping-section {
    animation: fadeInUp 0.8s ease-out;
}

/* Enhanced Button Styles */
.btn-shopping:not(:last-child) {
    margin-right: 15px;
}

@media (max-width: 768px) {
    .btn-shopping:not(:last-child) {
        margin-right: 0;
        margin-bottom: 10px;
    }
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Benefit Cards */
.benefit-card {
    background: white;
    border-radius: 15px;
    padding: 2rem 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    height: 100%;
}

.benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    border-color: #dc3545;
}

.benefit-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, #dc3545, #c82333);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.benefit-card:hover .benefit-icon {
    transform: scale(1.1);
    box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
}

.benefit-icon i {
    font-size: 1.8rem;
    color: white;
}

.benefit-card h6 {
    font-weight: 700;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.benefit-card p {
    margin-bottom: 0;
    font-size: 0.9rem;
}

/* Enhanced empty cart */
.empty-cart {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,249,250,0.95) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(220, 53, 69, 0.1);
}

/* Cart Statistics */
.cart-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.cart-stat-card {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    border: 1px solid #e9ecef;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.cart-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.cart-stat-icon {
    width: 50px;
    height: 50px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, #dc3545, #c82333);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-stat-icon i {
    color: white;
    font-size: 1.2rem;
}

.cart-stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #dc3545;
    line-height: 1;
}

.cart-stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include VIEW_PATH . '/client/layouts/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="breadcrumb-section">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/shop">Cửa hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
                </ol>
            </div>
        </nav>

        <!-- Cart Hero Section -->
        <section class="cart-hero">
            <div class="container">
                <div class="text-center">
                    <h1><i class="fas fa-shopping-cart me-3"></i>Giỏ hàng của bạn</h1>
                    <p class="cart-hero-subtitle">Quản lý và xem lại các sản phẩm bạn đã chọn</p>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark px-4 py-2 rounded-pill fs-6">
                            <span id="cartItemCount"><?= $cartCount ?? 0 ?></span> sản phẩm
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cart Section -->
        <section class="cart-section">
            <div class="container">
                <div class="cart-container">
                    <!-- Cart Content -->
                    <div class="cart-content">
                    <div class="cart-items" id="cartItemsContainer">
                        <!-- Empty cart message (will be hidden when items exist) -->
                        <div class="empty-cart" id="emptyCartMessage">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4>Giỏ hàng đang trống</h4>
                            <p>Hãy khám phá bộ sưu tập thời trang đa dạng của chúng tôi và thêm những món đồ yêu thích vào giỏ hàng</p>
                            <a href="/shop" class="btn-shopping">
                                <i class="fas fa-shopping-bag"></i>
                                Khám phá sản phẩm
                            </a>
                        </div>

                        <!-- Cart items will be dynamically loaded here -->
                        <div class="cart-items-list" id="cartItemsList" style="display: none;">
                            <!-- Items will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Continue Shopping -->
                    <div class="continue-shopping mt-4" id="continueShoppingSection" style="display: none;">
                        <a href="/shop" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Cart Summary -->
                    <div class="cart-summary sticky-top" id="cartSummary" style="display: none; top: 120px;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>
                                    Tổng đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Summary items -->
                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="subtotal">0đ</span>
                                </div>


                                <!-- Checkout Button -->
                                <a href="<?= url('checkout') ?>" class="btn btn-primary btn-lg w-100 mb-2" id="checkoutBtn">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Tiến hành thanh toán
                                </a>

                               
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recently Viewed Products -->
    <section class="recently-viewed py-5 bg-light" id="recentlyViewedSection" style="display: none;">
        <div class="container">
            <h4 class="section-title mb-4">
                <i class="fas fa-history me-2"></i>
                Sản phẩm đã xem
            </h4>
            <div class="row" id="recentlyViewedProducts">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Footer -->
        <!-- Footer -->
    <?php include VIEW_PATH . '/client/layouts/footer.php'; ?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/client.js') ?>"></script>

    <script>
    // Cart management functions
    let cartItems = [];

    // Load cart items from server
    function loadCartItems() {
        console.log('Loading cart items...');
        console.log('Current cookies:', document.cookie);

        fetch('/5s-fashion/ajax/cart/items')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Cart API response:', data);
                if (data.success) {
                    cartItems = data.items;
                    updateCartDisplay();
                    // Chỉ update cart summary nếu có items
                    if (cartItems.length > 0) {
                        updateCartSummary();
                    }
                } else {
                    console.error('Error loading cart:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading cart:', error);
            });
    }

    // Update cart display
    function updateCartDisplay() {
        const cartContainer = document.getElementById('cartItemsContainer');
        const emptyMessage = document.getElementById('emptyCartMessage');
        const cartItemsList = document.getElementById('cartItemsList');
        const cartSummary = document.getElementById('cartSummary');

        if (cartItems.length === 0) {
            emptyMessage.style.display = 'block';
            cartItemsList.style.display = 'none';
            cartSummary.style.display = 'none';
        } else {
            emptyMessage.style.display = 'none';
            cartItemsList.style.display = 'block';
            cartSummary.style.display = 'block';

            renderCartItems();
        }

        // Update cart count
        const cartCountElement = document.getElementById('cartItemCount');
        if (cartCountElement) {
            cartCountElement.textContent = cartItems.length;
        }
    }

    // Render cart items
    function renderCartItems() {
        const cartItemsList = document.getElementById('cartItemsList');

        cartItemsList.innerHTML = cartItems.map(item => `
            <div class="cart-item" data-cart-key="${item.cart_key}">
                <div class="row align-items-center">
                    <div class="col-md-2 col-4">
                        <img src="${item.product_image || '/assets/images/no-image.jpg'}"
                             alt="${item.product_name}"
                             class="cart-item-image">
                    </div>
                    <div class="col-md-6 col-8">
                        <div class="cart-item-info">
                            <h6 class="cart-item-name">${item.product_name}</h6>
                            <div class="cart-item-details">
                                ${item.variant && item.variant.color ? `<span class="detail-badge color">${item.variant.color}</span>` : ''}
                                ${item.variant && item.variant.size ? `<span class="detail-badge size">${item.variant.size}</span>` : ''}
                            </div>
                            <div class="cart-item-price">${formatPrice(item.price)}</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="cart-item-controls">
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="updateQuantity('${item.cart_key}', ${item.quantity - 1})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="${item.quantity}"
                                       onchange="updateQuantity('${item.cart_key}', this.value)" min="1">
                                <button class="quantity-btn" onclick="updateQuantity('${item.cart_key}', ${item.quantity + 1})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button class="remove-btn" onclick="removeCartItem('${item.cart_key}')">
                                <i class="fas fa-trash"></i>
                                Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Update cart summary
    function updateCartSummary() {
        const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        // Kiểm tra elements tồn tại trước khi cập nhật
        const subtotalElement = document.getElementById('subtotal');
        const shippingElement = document.getElementById('shippingFee');
        const totalElement = document.getElementById('totalAmount');

        if (subtotalElement) subtotalElement.textContent = formatPrice(subtotal);
        if (shippingElement) shippingElement.textContent = formatPrice(shippingFee);
        if (totalElement) totalElement.textContent = formatPrice(total);

        // Update free shipping progress
        updateFreeShippingProgress(subtotal);
    }

    // Update quantity
    function updateQuantity(cartKey, newQuantity) {
        if (newQuantity < 1) return;

        fetch('/5s-fashion/ajax/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_key: cartKey,
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartItems(); // Reload cart
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            alert('Có lỗi xảy ra khi cập nhật số lượng');
        });
    }

    // Remove cart item
    function removeCartItem(cartKey) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

        fetch('/5s-fashion/ajax/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_key: cartKey
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartItems(); // Reload cart
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm');
        });
    }

    // Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }

    // Proceed to checkout
    function proceedToCheckout() {
        if (cartItems.length === 0) {
            alert('Giỏ hàng trống');
            return;
        }
        window.location.href = '/checkout';
    }

    // Apply promo code
    function applyPromoCode() {
        const promoCode = document.getElementById('promoCodeInput').value.trim();
        if (!promoCode) {
            alert('Vui lòng nhập mã giảm giá');
            return;
        }

        // TODO: Implement promo code logic
        alert('Tính năng mã giảm giá đang được phát triển');
    }

    // Load cart items when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadCartItems();
    });
    </script>

</body>
</html>
