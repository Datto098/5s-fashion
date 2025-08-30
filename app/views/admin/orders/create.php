<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Tạo đơn hàng mới</h1>
                    <p class="text-muted mb-0">Thêm đơn hàng mới vào hệ thống</p>
                </div>
                <div>
                    <a href="/zone-fashion/admin/orders" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Error/Success Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/zone-fashion/admin/orders/store" method="POST" id="orderForm">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> Thông tin khách hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customerSelect" class="form-label">Khách hàng</label>
                                    <select class="form-select" id="customerSelect" name="customer_id">
                                        <option value="">Chọn khách hàng hoặc tạo mới</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>" data-email="<?= htmlspecialchars($customer['email']) ?>"
                                                    data-phone="<?= htmlspecialchars($customer['phone']) ?>">
                                                <?= htmlspecialchars($customer['full_name']) ?> - <?= htmlspecialchars($customer['email']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customerEmail" class="form-label">Email khách hàng</label>
                                    <input type="email" class="form-control" id="customerEmail" name="customer_email" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customerName" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customerName" name="customer_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customerPhone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="customerPhone" name="customer_phone" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shippingAddress" class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="shippingAddress" name="shipping_address" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shippingCity" class="form-label">Thành phố <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shippingCity" name="shipping_city" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shippingDistrict" class="form-label">Quận/Huyện</label>
                                    <input type="text" class="form-control" id="shippingDistrict" name="shipping_district">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shippingWard" class="form-label">Phường/Xã</label>
                                    <input type="text" class="form-control" id="shippingWard" name="shipping_ward">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart"></i> Sản phẩm đặt hàng
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                            <i class="fas fa-plus"></i> Thêm sản phẩm
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="orderItems">
                            <!-- Order items will be added here -->
                        </div>

                        <div class="text-center mt-3" id="noItemsMessage">
                            <p class="text-muted">Chưa có sản phẩm nào được thêm vào đơn hàng</p>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note"></i> Ghi chú đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="orderNotes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="orderNotes" name="notes" rows="4"
                                      placeholder="Nhập ghi chú cho đơn hàng..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card mb-4" id="orderSummary">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calculator"></i> Tổng kết đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span id="subtotal">0 đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span id="shippingFee">0 đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Giảm giá:</span>
                            <span id="discount">0 đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Tổng cộng:</span>
                            <span id="totalAmount" class="text-primary">0 đ</span>
                        </div>

                        <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                        <input type="hidden" name="shipping_fee" id="shippingFeeInput" value="0">
                        <input type="hidden" name="discount_amount" id="discountInput" value="0">
                        <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                    </div>
                </div>

                <!-- Order Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Cài đặt đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="orderStatus" class="form-label">Trạng thái đơn hàng</label>
                            <select class="form-select" id="orderStatus" name="status">
                                <option value="pending">Chờ xử lý</option>
                                <option value="confirmed">Đã xác nhận</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipped">Đã gửi hàng</option>
                                <option value="delivered">Đã giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Phương thức thanh toán</label>
                            <select class="form-select" id="paymentMethod" name="payment_method">
                                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                <option value="credit_card">Thẻ tín dụng</option>
                                <option value="e_wallet">Ví điện tử</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="paymentStatus" class="form-label">Trạng thái thanh toán</label>
                            <select class="form-select" id="paymentStatus" name="payment_status">
                                <option value="pending">Chờ thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                                <option value="failed">Thanh toán thất bại</option>
                                <option value="refunded">Đã hoàn tiền</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Tạo đơn hàng
                            </button>
                            <a href="/zone-fashion/admin/orders" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="productSearch" placeholder="Tìm kiếm sản phẩm...">
                </div>
                <div class="row" id="productList">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 mb-3 product-item" data-name="<?= strtolower(htmlspecialchars($product['name'])) ?>">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
                                    <p class="card-text">
                                        Giá: <strong><?= number_format($product['price']) ?> đ</strong><br>
                                        Tồn kho: <span class="badge bg-info"><?= $product['stock_quantity'] ?></span>
                                    </p>
                                    <button type="button" class="btn btn-primary btn-sm select-product"
                                            data-id="<?= $product['id'] ?>"
                                            data-name="<?= htmlspecialchars($product['name']) ?>"
                                            data-price="<?= $product['price'] ?>"
                                            data-stock="<?= $product['stock_quantity'] ?>">
                                        Chọn
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let orderItemCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Customer selection
    const customerSelect = document.getElementById('customerSelect');
    const customerEmail = document.getElementById('customerEmail');
    const customerName = document.getElementById('customerName');
    const customerPhone = document.getElementById('customerPhone');

    customerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            customerEmail.value = selectedOption.dataset.email || '';
            customerName.value = selectedOption.text.split(' - ')[0] || '';
            customerPhone.value = selectedOption.dataset.phone || '';
        } else {
            customerEmail.value = '';
            customerName.value = '';
            customerPhone.value = '';
        }
    });

    // Product selection
    const addProductBtn = document.getElementById('addProductBtn');
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    const productSearch = document.getElementById('productSearch');

    addProductBtn.addEventListener('click', function() {
        productModal.show();
    });

    // Product search
    productSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const productName = item.dataset.name;
            item.style.display = productName.includes(searchTerm) ? 'block' : 'none';
        });
    });

    // Select product
    document.querySelectorAll('.select-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const productData = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                stock: parseInt(this.dataset.stock)
            };
            addOrderItem(productData);
            productModal.hide();
        });
    });

    // Form validation
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        const customerName = document.getElementById('customerName').value.trim();
        const customerPhone = document.getElementById('customerPhone').value.trim();
        const shippingAddress = document.getElementById('shippingAddress').value.trim();
        const orderItems = document.querySelectorAll('.order-item');

        if (!customerName || !customerPhone || !shippingAddress) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin khách hàng và địa chỉ giao hàng!');
            return false;
        }

        if (orderItems.length === 0) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất một sản phẩm vào đơn hàng!');
            return false;
        }
    });
});

function addOrderItem(product) {
    orderItemCount++;
    const orderItems = document.getElementById('orderItems');
    const noItemsMessage = document.getElementById('noItemsMessage');

    noItemsMessage.style.display = 'none';

    const itemHtml = `
        <div class="order-item border rounded p-3 mb-3" data-product-id="${product.id}">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="mb-1">${product.name}</h6>
                    <small class="text-muted">Tồn kho: ${product.stock}</small>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Số lượng</label>
                    <input type="number" class="form-control quantity-input" name="items[${orderItemCount}][quantity]"
                           value="1" min="1" max="${product.stock}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đơn giá</label>
                    <input type="number" class="form-control price-input" name="items[${orderItemCount}][price]"
                           value="${product.price}" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Thành tiền</label>
                    <div class="form-control-plaintext fw-bold item-total">${formatMoney(product.price)}</div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="items[${orderItemCount}][product_id]" value="${product.id}">
        </div>
    `;

    orderItems.insertAdjacentHTML('beforeend', itemHtml);

    // Add event listeners for the new item
    const newItem = orderItems.lastElementChild;
    const quantityInput = newItem.querySelector('.quantity-input');
    const priceInput = newItem.querySelector('.price-input');
    const removeBtn = newItem.querySelector('.remove-item');

    quantityInput.addEventListener('input', updateItemTotal);
    priceInput.addEventListener('input', updateItemTotal);
    removeBtn.addEventListener('click', removeOrderItem);

    updateOrderSummary();
}

function removeOrderItem(e) {
    const item = e.target.closest('.order-item');
    item.remove();

    const orderItems = document.getElementById('orderItems');
    const noItemsMessage = document.getElementById('noItemsMessage');

    if (orderItems.children.length === 0) {
        noItemsMessage.style.display = 'block';
    }

    updateOrderSummary();
}

function updateItemTotal(e) {
    const item = e.target.closest('.order-item');
    const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(item.querySelector('.price-input').value) || 0;
    const total = quantity * price;

    item.querySelector('.item-total').textContent = formatMoney(total);
    updateOrderSummary();
}

function updateOrderSummary() {
    let subtotal = 0;

    document.querySelectorAll('.order-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(item.querySelector('.price-input').value) || 0;
        subtotal += quantity * price;
    });

    const shippingFee = 30000; // Fixed shipping fee
    const discount = 0;
    const total = subtotal + shippingFee - discount;

    document.getElementById('subtotal').textContent = formatMoney(subtotal);
    document.getElementById('shippingFee').textContent = formatMoney(shippingFee);
    document.getElementById('discount').textContent = formatMoney(discount);
    document.getElementById('totalAmount').textContent = formatMoney(total);

    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('shippingFeeInput').value = shippingFee;
    document.getElementById('discountInput').value = discount;
    document.getElementById('totalAmountInput').value = total;
}

function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
}
</script>

<style>
.order-item {
    background: #f8f9fa;
}

.order-item:hover {
    background: #e9ecef;
}

.product-item {
    cursor: pointer;
}

.product-item .card:hover {
    border-color: #007bff;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.075);
}

.form-control-plaintext {
    padding-top: 0.375rem;
    padding-bottom: 0.375rem;
}

#orderSummary {
    position: sticky;
    top: 20px;
}
</style>
