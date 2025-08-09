<?php
// Create cart sidebar partial for client website
?>
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-overlay" id="cartSidebarOverlay" onclick="closeCartSidebar()"></div>
    <div class="cart-content">
        <!-- Cart Header -->
        <div class="cart-header">
            <h5 class="cart-title">
                <i class="fas fa-shopping-cart me-2"></i>
                Giỏ hàng của bạn
            </h5>
            <button class="cart-close" onclick="closeCartSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cart Items -->
        <div class="cart-body">
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be loaded here -->
                <div class="empty-cart text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Giỏ hàng trống</h5>
                    <p class="text-muted mb-3">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                    <button class="btn btn-primary" onclick="closeCartSidebar()">
                        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                    </button>
                </div>
            </div>
        </div>

        <!-- Cart Footer -->
        <div class="cart-footer">
            <div class="cart-total">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="total-label">Tổng cộng:</span>
                    <span class="total-amount" id="cartTotal">0đ</span>
                </div>
                <div class="cart-actions">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="viewCart()">
                        <i class="fas fa-eye me-2"></i>Xem giỏ hàng
                    </button>
                    <button class="btn btn-primary w-100" onclick="checkout()">
                        <i class="fas fa-credit-card me-2"></i>Thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cart Item Template -->
<script type="text/template" id="cartItemTemplate">
    <div class="cart-item" data-id="{{id}}">
        <div class="item-image">
            <img src="{{image}}" alt="{{name}}" class="img-fluid">
        </div>
        <div class="item-info">
            <h6 class="item-name">{{name}}</h6>
            <div class="item-variant text-muted">{{variant}}</div>
            <div class="item-price">{{price}}</div>
        </div>
        <div class="item-controls">
            <div class="quantity-controls">
                <button class="quantity-btn decrease" onclick="updateCartQuantity({{id}}, -1)">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="quantity">{{quantity}}</span>
                <button class="quantity-btn increase" onclick="updateCartQuantity({{id}}, 1)">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <button class="remove-item" onclick="removeFromCart({{id}})" title="Xóa sản phẩm">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</script>

<style>
/* Cart Sidebar Styles */
.cart-sidebar {
    position: fixed;
    top: 0;
    right: -400px;
    width: 400px;
    height: 100vh;
    z-index: 1060;
    transition: right 0.3s ease;
}

.cart-sidebar.active,
.cart-sidebar.show {
    right: 0;
}

.cart-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.cart-sidebar.active .cart-overlay,
.cart-sidebar.show .cart-overlay,
.cart-overlay.show {
    opacity: 1;
    visibility: visible;
}

.cart-content {
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 100vh;
    background: white;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.cart-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: between;
    align-items: center;
}

.cart-title {
    margin: 0;
    font-weight: 600;
    color: var(--dark-color);
}

.cart-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--dark-color);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.cart-close:hover {
    background: #f8f9fa;
    color: var(--primary-color);
}

.cart-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.cart-item:hover {
    background: #e9ecef;
}

.item-image {
    flex: 0 0 60px;
}

.item-image img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
}

.item-info {
    flex: 1;
}

.item-name {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--dark-color);
}

.item-variant {
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.item-price {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--primary-color);
}

.item-controls {
    flex: 0 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border-radius: 6px;
    padding: 0.25rem;
}

.quantity-btn {
    width: 24px;
    height: 24px;
    border: none;
    background: var(--primary-light);
    color: var(--primary-color);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background: var(--primary-color);
    color: white;
}

.quantity {
    font-size: 0.9rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.remove-item {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.remove-item:hover {
    background: #dc3545;
    color: white;
}

.cart-footer {
    padding: 1.5rem;
    border-top: 1px solid #eee;
    background: #f8f9fa;
}

.cart-total {
    background: white;
    padding: 1rem;
    border-radius: 8px;
}

.total-label {
    font-size: 1rem;
    font-weight: 500;
}

.total-amount {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.empty-cart i {
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 480px) {
    .cart-sidebar {
        width: 100vw;
        right: -100vw;
    }

    .cart-content {
        width: 100vw;
    }

    .cart-item {
        flex-direction: column;
        text-align: center;
    }

    .item-controls {
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
    }
}

/* Animation */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(100%);
    }
}

.cart-sidebar.active .cart-content {
    animation: slideInRight 0.3s ease;
}

.cart-sidebar.closing .cart-content {
    animation: slideOutRight 0.3s ease;
}
</style>

<script>
// Cart sidebar functions
function openCartSidebar() {
    const sidebar = document.getElementById('cartSidebar');
    sidebar.classList.add('active');
    document.body.style.overflow = 'hidden';
    loadCartItems();
}

function closeCartSidebar() {
    const sidebar = document.getElementById('cartSidebar');
    sidebar.classList.add('closing');

    setTimeout(() => {
        sidebar.classList.remove('active', 'closing');
        document.body.style.overflow = '';
    }, 300);
}

function loadCartItems() {
    // Get cart items from localStorage or API
    const cartItems = getCartItems();
    const cartItemsContainer = document.getElementById('cartItems');

    if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Giỏ hàng trống</h5>
                <p class="text-muted mb-3">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                <button class="btn btn-primary" onclick="closeCartSidebar()">
                    <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                </button>
            </div>
        `;
        document.getElementById('cartTotal').textContent = '0đ';
        return;
    }

    // Render cart items
    const template = document.getElementById('cartItemTemplate').innerHTML;
    let itemsHtml = '';
    let total = 0;

    cartItems.forEach(item => {
        const itemHtml = template
            .replace(/{{id}}/g, item.id)
            .replace(/{{image}}/g, item.image)
            .replace(/{{name}}/g, item.name)
            .replace(/{{variant}}/g, item.variant || '')
            .replace(/{{price}}/g, formatPrice(item.price))
            .replace(/{{quantity}}/g, item.quantity);

        itemsHtml += itemHtml;
        total += item.price * item.quantity;
    });

    cartItemsContainer.innerHTML = itemsHtml;
    document.getElementById('cartTotal').textContent = formatPrice(total);
}

function updateCartQuantity(productId, change) {
    const cartItems = getCartItems();
    const itemIndex = cartItems.findIndex(item => item.id == productId);

    if (itemIndex !== -1) {
        cartItems[itemIndex].quantity += change;

        if (cartItems[itemIndex].quantity <= 0) {
            cartItems.splice(itemIndex, 1);
        }

        saveCartItems(cartItems);
        loadCartItems();
        updateCartCounter();

        showToast(change > 0 ? 'Đã tăng số lượng' : 'Đã giảm số lượng', 'info');
    }
}

function removeFromCart(productId) {
    const cartItems = getCartItems();
    const filteredItems = cartItems.filter(item => item.id != productId);

    saveCartItems(filteredItems);
    loadCartItems();
    updateCartCounter();

    showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'info');
}

function viewCart() {
    window.location.href = '/?route=cart';
}

function checkout() {
    const cartItems = getCartItems();
    if (cartItems.length === 0) {
        showToast('Giỏ hàng trống! Hãy thêm sản phẩm trước khi thanh toán.', 'warning');
        return;
    }

    window.location.href = '/5s-fashion/checkout';
}

function getCartItems() {
    const items = localStorage.getItem('cart');
    return items ? JSON.parse(items) : [];
}

function saveCartItems(items) {
    localStorage.setItem('cart', JSON.stringify(items));
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Initialize cart sidebar
document.addEventListener('DOMContentLoaded', function() {
    // Update cart counter on page load
    updateCartCounter();

    // Handle escape key to close sidebar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCartSidebar();
        }
    });
});
</script>
