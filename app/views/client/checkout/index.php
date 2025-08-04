<?php
// Checkout Page for Phase 3.3
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - 5S Fashion</title>
    <meta name="description" content="Hoàn tất đơn hàng tại 5S Fashion - Thanh toán an toàn và bảo mật">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/client.css">
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/checkout.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-section">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/cart">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </div>
    </nav>

    <!-- Checkout Progress -->
    <section class="checkout-progress py-3 bg-light">
        <div class="container">
            <div class="progress-steps d-flex justify-content-center">
                <div class="step active" data-step="1">
                    <div class="step-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="step-label">Giỏ hàng</div>
                </div>
                <div class="step-line"></div>
                <div class="step active" data-step="2">
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="step-label">Thanh toán</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="step-label">Hoàn tất</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Section -->
    <section class="checkout-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Checkout Form -->
                    <form id="checkoutForm" class="checkout-form">
                        <!-- Customer Information -->
                    
                        <!-- Shipping Information -->
                        <div class="checkout-section-card mb-4">
                            <script>
                                // Store addresses for edit function
                                window.addressesList = [];
                            </script>
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-truck me-2"></i>
                                    Thông tin giao hàng
                                </h4>
                            </div>
                            <div class="section-body">
                                <!-- Address List -->
                                <div class="address-selection mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Địa chỉ giao hàng</h6>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                            <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                                        </button>
                                    </div>
                                    
                                    <!-- Saved Addresses -->
                                    <div id="savedAddresses" class="saved-addresses">
                                        <?php if (empty($addresses)): ?>
                                            <div class="empty-addresses text-center py-5">
                                                <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                                                <h4>Chưa có địa chỉ nào</h4>
                                                <p class="text-muted mb-4">Thêm địa chỉ giao hàng để mua sắm thuận tiện hơn!</p>
                                                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                                    <i class="fas fa-plus me-2"></i>Thêm địa chỉ đầu tiên
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="addresses-list">
                                                <?php foreach ($addresses as $address): ?>
                                                    <div class="address-card <?= $address['is_default'] ? 'selected' : '' ?>" data-address-id="<?= $address['id'] ?>">
                                                        <div class="card-body">
                                                            <input type="radio" name="addressOption" value="<?= $address['id'] ?>" 
                                                                   class="form-check-input" <?= $address['is_default'] ? 'checked' : '' ?>>
                                                            <div class="address-info">
                                                                <div class="address-name">
                                                                    <strong><?= htmlspecialchars($address['name']) ?></strong>
                                                                    <?php if ($address['is_default']): ?>
                                                                        <span class="address-default-badge">Mặc định</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="address-phone">
                                                                    <i class="fas fa-phone me-1"></i><?= htmlspecialchars($address['phone']) ?>
                                                                </div>
                                                                <div class="address-details">
                                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                                    <?= htmlspecialchars($address['address']) ?>
                                                                </div>
                                                                <div class="address-actions">
                                                                    <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="editAddress(<?= $address['id'] ?>)">
                                                                        <i class="fas fa-edit me-1"></i>Sửa
                                                                    </button>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteAddress(<?= $address['id'] ?>)">
                                                                        <i class="fas fa-trash me-1"></i>Xóa
                                                                    </button>
                                                                    <?php if (!$address['is_default']): ?>
                                                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="setDefaultAddress(<?= $address['id'] ?>)">
                                                                            <i class="fas fa-star me-1"></i>Đặt mặc định
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                
                                              
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Shipping Method -->
                                <div class="shipping-methods">
                                    <h6 class="mb-3">Phương thức vận chuyển</h6>
                                    <div class="shipping-option">
                                        <input type="radio" id="standard" name="shippingMethod" value="standard" class="form-check-input" checked>
                                        <label for="standard" class="shipping-label">
                                            <div class="shipping-info">
                                                <div class="shipping-name">Giao hàng tiêu chuẩn</div>
                                                <div class="shipping-desc">Giao trong 3-5 ngày làm việc</div>
                                            </div>
                                            <div class="shipping-price">30.000đ</div>
                                        </label>
                                    </div>
                                    <div class="shipping-option">
                                        <input type="radio" id="express" name="shippingMethod" value="express" class="form-check-input">
                                        <label for="express" class="shipping-label">
                                            <div class="shipping-info">
                                                <div class="shipping-name">Giao hàng nhanh</div>
                                                <div class="shipping-desc">Giao trong 1-2 ngày làm việc</div>
                                            </div>
                                            <div class="shipping-price">50.000đ</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section-card mb-4">
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-credit-card me-2"></i>
                                    Phương thức thanh toán
                                </h4>
                            </div>
                            <div class="section-body">
                                <div class="payment-methods">
                                    <div class="payment-option">
                                        <input type="radio" id="cod" name="paymentMethod" value="cod" class="form-check-input" checked>
                                        <label for="cod" class="payment-label">
                                            <div class="payment-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Thanh toán khi nhận hàng (COD)</div>
                                                <div class="payment-desc">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="vnpay" name="paymentMethod" value="vnpay" class="form-check-input">
                                        <label for="vnpay" class="payment-label">
                                            <div class="payment-icon">
                                                <img src="/5s-fashion/public/assets/images/vnpay.png" alt="VNPay" onerror="this.parentElement.innerHTML='<i class=\'fas fa-credit-card\'></i>'">
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Ví điện tử VNPay</div>
                                                <div class="payment-desc">Thanh toán qua ví điện tử VNPay</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="momo" name="paymentMethod" value="momo" class="form-check-input">
                                        <label for="momo" class="payment-label">
                                            <div class="payment-icon">
                                                <img src="/5s-fashion/public/assets/images/momo.png" alt="MoMo" onerror="this.parentElement.innerHTML='<i class=\'fas fa-mobile-alt\'></i>'">
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Ví MoMo</div>
                                                <div class="payment-desc">Thanh toán qua ví điện tử MoMo</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="bank" name="paymentMethod" value="bank" class="form-check-input">
                                        <label for="bank" class="payment-label">
                                            <div class="payment-icon">
                                                <i class="fas fa-university"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-name">Chuyển khoản ngân hàng</div>
                                                <div class="payment-desc">Chuyển khoản qua Internet Banking</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="checkout-section-card mb-4">
                            <div class="section-header">
                                <h4>
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Ghi chú đơn hàng
                                </h4>
                            </div>
                            <div class="section-body">
                                <textarea class="form-control" id="orderNotes" name="orderNotes" rows="3"
                                          placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="order-summary sticky-top" style="top: 120px;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Tóm tắt đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Order Items -->
                                <div class="order-items mb-3" id="orderItems">
                                    <!-- Will be populated by JavaScript -->
                                </div>

                                <hr>

                                <!-- Order Summary -->
                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="orderSubtotal">0đ</span>
                                </div>

                                <div class="summary-item d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span id="orderShipping">30.000đ</span>
                                </div>

                                <div class="summary-item d-flex justify-content-between mb-2" id="orderDiscountRow" style="display: none;">
                                    <span class="text-success">Giảm giá:</span>
                                    <span class="text-success" id="orderDiscount">-0đ</span>
                                </div>

                                <hr>

                                <div class="summary-total d-flex justify-content-between mb-3">
                                    <strong>Tổng cộng:</strong>
                                    <strong class="text-primary" id="orderTotal">0đ</strong>
                                </div>

                                <!-- Promo Code -->
                                <div class="promo-code-section mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Mã giảm giá" id="checkoutPromoCode">
                                        <button class="btn btn-outline-secondary" type="button" onclick="applyCheckoutPromoCode()">
                                            Áp dụng
                                        </button>
                                    </div>
                                   <!-- Danh sách voucher đã lưu còn hạn -->
                                   <?php if (!empty($savedVouchers)): ?>
                                   <div class="saved-vouchers-list mt-2">
                                       <div class="fw-bold mb-1" style="font-size: 0.95em;">Voucher đã lưu của bạn:</div>
                                       <?php foreach ($savedVouchers as $voucher): ?>
                                           <div class="voucher-item mb-1 p-2 border rounded d-flex align-items-center justify-content-between" style="background: #f8f9fa; cursor:pointer;" onclick="document.getElementById('checkoutPromoCode').value='<?= htmlspecialchars($voucher['code']) ?>'">
                                               <div>
                                                   <span class="badge bg-success me-2"><?= htmlspecialchars($voucher['code']) ?></span>
                                                   <span><?= htmlspecialchars($voucher['name']) ?></span>
                                                   <span class="text-muted ms-2" style="font-size:0.9em;">HSD: <?= $voucher['valid_until'] ? date('d/m/Y', strtotime($voucher['valid_until'])) : 'Không giới hạn' ?></span>
                                               </div>
                                               <span class="text-primary fw-bold">
                                                   <?= $voucher['type']==='percent' ? $voucher['value'].'%' : number_format($voucher['value'],0,',','.').'đ' ?>
                                               </span>
                                           </div>
                                       <?php endforeach; ?>
                                   </div>
                                   <?php endif; ?>
                                </div>

                                <!-- Place Order Button -->
                                <button type="button" class="btn btn-success btn-lg w-100 mb-3" onclick="placeOrder()" id="placeOrderBtn">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Đặt hàng
                                </button>

                                <!-- Security Notice -->
                                <div class="security-notice text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Thông tin của bạn được bảo mật
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Return Policy -->
                        <div class="return-policy mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i class="fas fa-undo me-2"></i>
                                        Chính sách đổi trả
                                    </h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Đổi trả trong 7 ngày
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Miễn phí đổi size
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Hoàn tiền 100% nếu lỗi
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">
                        <i class="fas fa-map-marker-alt me-2"></i>Thêm địa chỉ mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAddressForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modalCustomerName" class="form-label">Họ và tên *</label>
                                <input type="text" class="form-control" id="modalCustomerName" name="customerName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modalCustomerPhone" class="form-label">Số điện thoại *</label>
                                <input type="tel" class="form-control" id="modalCustomerPhone" name="customerPhone" required>
                            </div>
                        </div>

                        <!-- Address Search with Map -->
                        <div class="mb-3">
                            <label for="addressSearch" class="form-label">Địa chỉ giao hàng *</label>
                            <div class="address-search-container">
                                <input type="text" class="form-control" id="addressSearch" 
                                       placeholder="Nhập địa chỉ, ví dụ: 123 Lê Lợi, Quận 1, TP.HCM"
                                       autocomplete="off">
                                <input type="hidden" id="modalAddress" name="address">
                                <div class="address-suggestions" id="addressSuggestions">
                                    <!-- Suggestions will be populated here -->
                                </div>
                            </div>
                        </div>

     
                        <!-- Map Container -->
                        <div class="mb-3">
                            <label class="form-label">Chọn vị trí trên bản đồ</label>
                            <div id="mapContainer" style="height: 300px; border-radius: 8px;">
                                <!-- Leaflet map will be initialized here -->
                            </div>
                            <input type="hidden" id="modalLat" name="lat">
                            <input type="hidden" id="modalLng" name="lng">
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-3">
                            <label for="modalNotes" class="form-label">Ghi chú địa chỉ (nếu có)</label>
                            <textarea class="form-control" id="modalNotes" name="notes" rows="2"
                                      placeholder="Ghi chú thêm về địa chỉ, ví dụ: Gần trường học, tầng 2..."></textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modalSetDefault" name="setDefault">
                            <label class="form-check-label" for="modalSetDefault">
                                Đặt làm địa chỉ mặc định
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveNewAddressModal()">
                        <i class="fas fa-save me-2"></i>Lưu địa chỉ
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .address-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .address-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.15);
        }
        
        .address-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .address-card .card-body {
            position: relative;
            padding: 15px;
        }
        
        .address-card input[type="radio"] {
            position: absolute;
            top: 15px;
            right: 15px;
            transform: scale(1.2);
        }
        
        .address-info .address-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .address-info .address-phone {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        
        .address-info .address-details {
            color: #777;
            font-size: 0.9em;
            line-height: 1.4;
        }
        
        .address-default-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            font-size: 0.75em;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }
        
        .address-actions {
            margin-top: 10px;
        }
        
        .address-actions .btn {
            font-size: 0.85em;
            padding: 4px 12px;
        }
        
        .add-address-card {
            border: 2px dashed #007bff;
            border-radius: 8px;
            text-align: center;
            padding: 30px;
            color: #007bff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-address-card:hover {
            background-color: #f8f9ff;
            border-color: #0056b3;
        }
        
        .add-address-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        #mapContainer {
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
        }
        
        .leaflet-container {
            border-radius: 8px;
        }
        
        .address-search-container {
            position: relative;
        }
        
        .address-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .suggestion-item {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        
        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .saved-addresses:empty::after {
            content: "Chưa có địa chỉ đã lưu. Vui lòng thêm địa chỉ mới.";
            display: block;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    
    .payment-icons {
        display: flex;
        gap: 10px;
    }
    
    /* Address Selection Styles */
    .address-option {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .address-option:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0,123,255,0.15);
    }
    
    .address-option.selected {
        border-color: #007bff;
        background-color: #f8f9ff;
    }
    
    .address-option input[type="radio"] {
        position: absolute;
        top: 15px;
        right: 15px;
        transform: scale(1.2);
    }
    
    .address-label {
        display: block;
        padding: 15px 50px 15px 15px;
        cursor: pointer;
        margin: 0;
        width: 100%;
    }
    
    .address-info .address-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .address-info .address-phone {
        color: #666;
        font-size: 0.9em;
        margin-bottom: 8px;
    }
    
    .address-info .address-details {
        color: #777;
        font-size: 0.9em;
        line-height: 1.4;
    }
    
    .address-default-badge {
        display: inline-block;
        background: #28a745;
        color: white;
        font-size: 0.75em;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: 8px;
    }
    
    .new-address-option .address-name {
        color: #007bff;
        font-weight: 500;
    }
    
    .new-address-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
        border: 1px solid #dee2e6;
    }
    
    .saved-addresses:empty::after {
        content: "Chưa có địa chỉ đã lưu. Vui lòng thêm địa chỉ mới.";
        display: block;
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px;
        border: 1px dashed #dee2e6;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    </style>
    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">
                        <i class="fas fa-edit me-2"></i>Sửa địa chỉ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAddressForm">
                        <input type="hidden" id="editAddressId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editCustomerName" class="form-label">Họ và tên *</label>
                                <input type="text" class="form-control" id="editCustomerName" name="customerName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editCustomerPhone" class="form-label">Số điện thoại *</label>
                                <input type="tel" class="form-control" id="editCustomerPhone" name="customerPhone" required>
                            </div>
                        </div>
                        <!-- Address Search with Map -->
                        <div class="mb-3">
                            <label for="editAddressSearch" class="form-label">Địa chỉ giao hàng *</label>
                            <div class="edit-address-search-container">
                                <input type="text" class="form-control" id="editAddressSearch" placeholder="Nhập địa chỉ, ví dụ: 123 Lê Lợi, Quận 1, TP.HCM" autocomplete="off">
                                <input type="hidden" id="editModalAddress" name="address">
                                <div class="address-suggestions" id="editAddressSuggestions"></div>
                            </div>
                        </div>
                        <!-- Map Container -->
                        <div class="mb-3">
                            <label class="form-label">Chọn vị trí trên bản đồ</label>
                            <div id="editMapContainer" style="height: 300px; border-radius: 8px;"></div>
                            <input type="hidden" id="editModalLat" name="lat">
                            <input type="hidden" id="editModalLng" name="lng">
                        </div>
                        <!-- Additional Notes -->
                        <div class="mb-3">
                            <label for="editModalNotes" class="form-label">Ghi chú địa chỉ (nếu có)</label>
                            <textarea class="form-control" id="editModalNotes" name="notes" rows="2" placeholder="Ghi chú thêm về địa chỉ, ví dụ: Gần trường học, tầng 2..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editModalSetDefault" name="setDefault">
                            <label class="form-check-label" for="editModalSetDefault">Đặt làm địa chỉ mặc định</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveEditAddressModal()">
                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <!-- Order Item Template -->
    <script type="text/template" id="orderItemTemplate">
        <div class="order-item">
            <div class="item-image">
                <img src="{{image}}" alt="{{name}}">
                <span class="item-quantity">{{quantity}}</span>
            </div>
            <div class="item-details">
                <div class="item-name">{{name}}</div>
                <div class="item-variant">{{variant}}</div>
                <div class="item-price">{{totalPrice}}</div>
            </div>
        </div>
    </script>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/5s-fashion/public/assets/js/client.js"></script>
    <script src="/5s-fashion/public/assets/js/checkout.js"></script>

    <!-- Initialize Checkout -->
    <script>
        // Address Management for Checkout
        class CheckoutAddressManager {
            constructor() {
                this.selectedAddressId = null;
                this.map = null;
                this.marker = null;
                this.init();
            }

            init() {
                this.bindEvents();
                this.initAddressSearch();
                this.initializeAddressSelection();
            }

            initializeAddressSelection() {
                // Find pre-selected address (default or first)
                const selectedRadio = document.querySelector('input[name="addressOption"]:checked');
                if (selectedRadio) {
                    this.selectedAddressId = selectedRadio.value;
                }
            }

            bindEvents() {
                // Handle address selection
                document.addEventListener('change', (e) => {
                    if (e.target.name === 'addressOption') {
                        const value = e.target.value;
                        
                        // Update UI
                        document.querySelectorAll('.address-card').forEach(card => {
                            card.classList.remove('selected');
                        });
                        e.target.closest('.address-card').classList.add('selected');

                        this.selectedAddressId = value;
                    }
                });

                // Handle modal events
                document.getElementById('addAddressModal').addEventListener('shown.bs.modal', () => {
                    setTimeout(() => {
                        if (window.addressManager.map) {
                            window.addressManager.map.remove();
                            window.addressManager.map = null;
                        }
                        window.addressManager.initMap();
                    }, 400); // tăng timeout lên 400ms để chắc chắn modal đã render xong
                });
            }

            initMap() {
                if (this.map) {
                    this.map.remove();
                    this.map = null;
                }
                // Đảm bảo container đã render xong
                setTimeout(() => {
                    this.map = L.map('mapContainer').setView([21.0285, 105.8542], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.map.on('click', (e) => {
                        this.setMarker(e.latlng.lat, e.latlng.lng);
                    });
                }, 0);
            }

            setMarker(lat, lng) {
                if (!this.map) return;
                
                if (this.marker) {
                    this.marker.remove();
                }
                
                this.marker = L.marker([lat, lng]).addTo(this.map);
                document.getElementById('modalLat').value = lat;
                document.getElementById('modalLng').value = lng;
                this.map.setView([lat, lng], 17);
                
                // Reverse geocoding để lấy địa chỉ
                this.reverseGeocode({ lat, lng });
            }

            async reverseGeocode(latlng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&addressdetails=1`);
                    const data = await response.json();
                    
                    if (data && data.display_name) {
                        document.getElementById('addressSearch').value = data.display_name;
                        document.getElementById('modalAddress').value = data.display_name;
                        this.parseAddress(data);
                    }
                } catch (error) {
                    console.error('Reverse geocoding error:', error);
                }
            }

            parseAddress(data) {
                // Sử dụng địa chỉ đầy đủ thay vì chỉ lấy house_number và road
                if (data && data.display_name) {
                    document.getElementById('modalAddress').value = data.display_name;
                } else {
                    const address = data.address || {};
                    
                    if (address.house_number && address.road) {
                        document.getElementById('modalAddress').value = `${address.house_number} ${address.road}`;
                    } else if (address.road) {
                        document.getElementById('modalAddress').value = address.road;
                    }
                }
            }

            initAddressSearch() {
                const searchInput = document.getElementById('addressSearch');
                const hiddenInput = document.getElementById('modalAddress');
                const suggestions = document.getElementById('addressSuggestions');
                let debounceTimer;

                if (!searchInput) return;

                // Sync visible input with hidden input
                searchInput.addEventListener('input', (e) => {
                    if (hiddenInput) {
                        hiddenInput.value = e.target.value;
                    }
                    
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        this.searchAddresses(e.target.value);
                    }, 500);
                });

                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.searchAddressOnMap();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.address-search-container')) {
                        if (suggestions) suggestions.style.display = 'none';
                    }
                });
            }

            searchAddressOnMap() {
                const query = document.getElementById('addressSearch').value;
                if (!query) return;
                
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Vietnam')}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            this.setMarker(lat, lng);
                        } else {
                            alert('Không tìm thấy địa chỉ phù hợp!');
                        }
                    });
            }

            async searchAddresses(query) {
                if (query.length < 3) return;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Vietnam')}&limit=5&addressdetails=1`);
                    const results = await response.json();
                    
                    this.displaySuggestions(results);
                } catch (error) {
                    console.error('Address search error:', error);
                }
            }

            displaySuggestions(results) {
                const suggestions = document.getElementById('addressSuggestions');
                
                if (results.length === 0) {
                    suggestions.style.display = 'none';
                    return;
                }

                suggestions.innerHTML = results.map(result => `
                    <div class="suggestion-item" onclick="selectSuggestion('${result.lat}', '${result.lon}', '${result.display_name.replace(/'/g, "\\'")}')">
                        <div><strong>${result.display_name}</strong></div>
                    </div>
                `).join('');

                suggestions.style.display = 'block';
            }

            getSelectedAddress() {
                const selectedRadio = document.querySelector('input[name="addressOption"]:checked');
                if (selectedRadio) {
                    const addressCard = selectedRadio.closest('.address-card');
                    const nameElement = addressCard.querySelector('.address-name strong');
                    const phoneElement = addressCard.querySelector('.address-phone');
                    const addressElement = addressCard.querySelector('.address-details');
                    
                    return {
                        id: selectedRadio.value,
                        name: nameElement ? nameElement.textContent.trim() : '',
                        phone: phoneElement ? phoneElement.textContent.replace(/.*?(\d+).*/, '$1') : '',
                        address: addressElement ? addressElement.textContent.trim().replace(/^.*?\s+/, '') : ''
                    };
                }
                return null;
            }

            async saveNewAddress(addressData) {
                try {
                    const response = await fetch('/5s-fashion/public/order/addAddress', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(addressData)
                    });

                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            // Reload page to show new address
                            window.location.reload();
                            return result;
                        }
                    }
                    return false;
                } catch (error) {
                    console.error('Error saving address:', error);
                    return false;
                }
            }
        }

        // Helper Functions
        function selectSuggestion(lat, lon, address) {
            document.getElementById('addressSearch').value = address;
            document.getElementById('modalAddress').value = address;
            document.getElementById('addressSuggestions').style.display = 'none';
            
            const latlng = { lat: parseFloat(lat), lng: parseFloat(lon) };
            window.addressManager.setMarker(latlng.lat, latlng.lng);
        }

        // Store addresses for edit function
        window.addressesList = <?php echo isset($addresses) ? json_encode($addresses) : '[]'; ?>;

        // Sửa địa chỉ: Hiển thị modal, load dữ liệu lên form và cho phép chỉnh sửa
        async function editAddress(id) {
            console.log("Editing address with ID:", id);
            const address = window.addressesList.find(addr => addr.id == id);
            if (!address) {
                alert('Không tìm thấy địa chỉ!');
                return;
            }
            
            // Điền dữ liệu vào form sửa
            document.getElementById('editAddressId').value = address.id;
            document.getElementById('editCustomerName').value = address.name;
            document.getElementById('editCustomerPhone').value = address.phone;
            document.getElementById('editAddressSearch').value = address.address;
            document.getElementById('editModalAddress').value = address.address;
            document.getElementById('editModalNotes').value = address.note || '';
            document.getElementById('editModalSetDefault').checked = address.is_default == 1;
            document.getElementById('editModalLat').value = address.lat || '';
            document.getElementById('editModalLng').value = address.lng || '';

            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
            modal.show();

            // Khởi tạo lại map sau khi modal hiển thị hoàn tất
            document.getElementById('editAddressModal').addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    console.log("Initializing map for edit address modal");
                    if (window.editMap) {
                        window.editMap.remove();
                        window.editMap = null;
                    }
                    window.editMap = L.map('editMapContainer').setView([
                        address.lat ? parseFloat(address.lat) : 21.0285,
                        address.lng ? parseFloat(address.lng) : 105.8542
                    ], address.lat && address.lng ? 17 : 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(window.editMap);
                    if (address.lat && address.lng) {
                        window.editMarker = L.marker([parseFloat(address.lat), parseFloat(address.lng)]).addTo(window.editMap);
                    } else {
                        window.editMarker = null;
                    }
                    window.editMap.on('click', function(e) {
                        if (window.editMarker) window.editMarker.remove();
                        window.editMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(window.editMap);
                        document.getElementById('editModalLat').value = e.latlng.lat;
                        document.getElementById('editModalLng').value = e.latlng.lng;
                        // Reverse geocode
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}&addressdetails=1`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    // Sử dụng địa chỉ đầy đủ
                                    document.getElementById('editAddressSearch').value = data.display_name;
                                    document.getElementById('editModalAddress').value = data.display_name;
                                }
                            });
                    });
                }, 400);
            }, { once: true }); // Đảm bảo chỉ chạy một lần
        }
        
        // Lưu địa chỉ đã sửa
        async function saveEditAddressModal() {
            const id = document.getElementById('editAddressId').value;
            const customerName = document.getElementById('editCustomerName').value.trim();
            const customerPhone = document.getElementById('editCustomerPhone').value.trim();
            const address = document.getElementById('editModalAddress').value.trim();
            const notes = document.getElementById('editModalNotes').value.trim();
            const setDefault = document.getElementById('editModalSetDefault').checked;
            const lat = document.getElementById('editModalLat').value;
            const lng = document.getElementById('editModalLng').value;
            
            if (!customerName || !customerPhone || !address) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
                return;
            }
            
            const addressData = {
                name: customerName,
                phone: customerPhone,
                address: address,
                note: notes,
                lat: lat,
                lng: lng,
                is_default: setDefault ? 1 : 0
            };
            
            try {
                const response = await fetch(`/5s-fashion/public/order/editAddress/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(addressData)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        alert('Đã cập nhật địa chỉ thành công!');
                        window.location.reload();
                    } else {
                        alert(result.message || 'Không thể cập nhật địa chỉ');
                    }
                } else {
                    alert('Có lỗi xảy ra khi cập nhật địa chỉ');
                }
            } catch (error) {
                console.error('Error updating address:', error);
                alert('Có lỗi xảy ra khi cập nhật địa chỉ');
            }
        }
        
        // Chọn gợi ý địa chỉ trong modal sửa
        function editSelectSuggestion(lat, lon, address) {
            document.getElementById('editAddressSearch').value = address;
            document.getElementById('editModalAddress').value = address;
            document.getElementById('editAddressSuggestions').style.display = 'none';
            if (window.editMap) {
                if (window.editMarker) window.editMarker.remove();
                window.editMarker = L.marker([parseFloat(lat), parseFloat(lon)]).addTo(window.editMap);
                window.editMap.setView([parseFloat(lat), parseFloat(lon)], 17);
                document.getElementById('editModalLat').value = lat;
                document.getElementById('editModalLng').value = lon;
            }
        }

        // Bổ sung autocomplete cho modal sửa địa chỉ
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('editAddressSearch');
            const hiddenInput = document.getElementById('editModalAddress');
            const suggestions = document.getElementById('editAddressSuggestions');
            let debounceTimer;
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    if (hiddenInput) hiddenInput.value = e.target.value;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const query = e.target.value;
                        if (query.length < 3) {
                            suggestions.style.display = 'none';
                            return;
                        }
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Vietnam')}&limit=5&addressdetails=1`)
                            .then(res => res.json())
                            .then(results => {
                                if (results.length === 0) {
                                    suggestions.style.display = 'none';
                                    return;
                                }
                                suggestions.innerHTML = results.map(result => `
                                    <div class="suggestion-item" onclick="editSelectSuggestion('${result.lat}', '${result.lon}', '${result.display_name.replace(/'/g, "\\'") }')">
                                        <div><strong>${result.display_name}</strong></div>
                                    </div>
                                `).join('');
                                suggestions.style.display = 'block';
                            });
                    }, 500);
                });
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        // Tìm kiếm vị trí trên bản đồ
                        const query = searchInput.value;
                        if (!query) return;
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Vietnam')}&limit=1`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    const lat = parseFloat(data[0].lat);
                                    const lng = parseFloat(data[0].lon);
                                    if (window.editMap) {
                                        if (window.editMarker) window.editMarker.remove();
                                        window.editMarker = L.marker([lat, lng]).addTo(window.editMap);
                                        window.editMap.setView([lat, lng], 17);
                                        document.getElementById('editModalLat').value = lat;
                                        document.getElementById('editModalLng').value = lng;
                                    }
                                } else {
                                    alert('Không tìm thấy địa chỉ phù hợp!');
                                }
                            });
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.edit-address-search-container')) {
                        if (suggestions) suggestions.style.display = 'none';
                    }
                });
            }
        });

        async function saveNewAddressModal() {
            const form = document.getElementById('addAddressForm');
            
            // Get form values
            const customerName = document.getElementById('modalCustomerName').value.trim();
            const customerPhone = document.getElementById('modalCustomerPhone').value.trim();
            const address = document.getElementById('modalAddress').value.trim();
            const notes = document.getElementById('modalNotes').value.trim();
            const setDefault = document.getElementById('modalSetDefault').checked;
            const lat = document.getElementById('modalLat').value;
            const lng = document.getElementById('modalLng').value;
            
            if (!customerName || !customerPhone || !address) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
                return;
            }

            const addressData = {
                name: customerName,
                phone: customerPhone,
                address: address,
                note: notes,
                lat: lat,
                lng: lng,
                is_default: setDefault ? 1 : 0
            };

            const result = await window.addressManager.saveNewAddress(addressData);
            if (result) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addAddressModal'));
                modal.hide();
                form.reset();
                
                // Clear map
                if (window.addressManager.marker) {
                    window.addressManager.marker.remove();
                }
                document.getElementById('modalLat').value = '';
                document.getElementById('modalLng').value = '';
                
                alert('Địa chỉ đã được thêm thành công!');
            } else {
                alert('Có lỗi xảy ra khi thêm địa chỉ');
            }
        }

    
        async function deleteAddress(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) return;

            try {
                const response = await fetch(`/5s-fashion/public/order/deleteAddress/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message || 'Không thể xóa địa chỉ');
                    }
                } else {
                    alert('Có lỗi xảy ra khi xóa địa chỉ');
                }
            } catch (error) {
                alert('Có lỗi xảy ra khi xóa địa chỉ');
            }
        }

        async function setDefaultAddress(id) {
            try {
                const response = await fetch(`/5s-fashion/public/order/setDefaultAddress/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ is_default: true })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message || 'Không thể đặt địa chỉ mặc định');
                    }
                } else {
                    alert('Có lỗi xảy ra khi đặt địa chỉ mặc định');
                }
            } catch (error) {
                alert('Có lỗi xảy ra khi đặt địa chỉ mặc định');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.checkoutManager = new CheckoutManager();
            window.addressManager = new CheckoutAddressManager();
            
            checkoutManager.loadOrder();
            checkoutManager.initializeForm();
        });
    </script>
</body>
</html>
