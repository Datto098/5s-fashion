<?php
// Orders index view
?>

<!-- Page Header -->
<div class="admin-header">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-shopping-cart"></i>
            Quản lý đơn hàng
        </h1>
        <div class="admin-breadcrumb">
            Theo dõi và xử lý đơn hàng từ khách hàng
        </div>
    </div>
    <div class="admin-header-actions">
        <button class="btn btn-outline" onclick="exportOrders()">
            <i class="fas fa-download"></i>
            Xuất Excel
        </button>
        <button class="btn btn-primary" onclick="refreshOrders()">
            <i class="fas fa-sync-alt"></i>
            Làm mới
        </button>
    </div>
</div>

<!-- Statistics -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count($orders) ?></div>
        <div class="admin-stat-label">Tổng đơn hàng</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></div>
        <div class="admin-stat-label">Chờ xử lý</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'processing')) ?></div>
        <div class="admin-stat-label">Đang xử lý</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">₫<?= number_format(array_sum(array_column($orders, 'total_amount'))) ?></div>
        <div class="admin-stat-label">Tổng doanh thu</div>
    </div>
</div>

<!-- Filters and Search -->
<div class="admin-filters">
    <div class="filter-group">
        <input type="text" class="form-input" placeholder="Tìm kiếm đơn hàng..." id="searchInput">
    </div>
    <div class="filter-group">
        <select class="form-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="pending">Chờ xử lý</option>
            <option value="processing">Đang xử lý</option>
            <option value="shipped">Đã gửi</option>
            <option value="delivered">Đã giao</option>
            <option value="cancelled">Đã hủy</option>
            <!-- <option value="refunded">Đã hoàn tiền</option> -->
        </select>
    </div>
    <div class="filter-group">
        <select class="form-select" id="paymentFilter">
            <option value="">Tất cả thanh toán</option>
            <option value="cod">COD</option>
            <option value="bank_transfer">Chuyển khoản</option>
            <option value="vnpay">VNPay</option>
            <option value="momo">Momo</option>
            <option value="credit_card">Thẻ tín dụng</option>
        </select>
    </div>
    <div class="filter-group">
        <input type="date" class="form-input" id="dateFrom" placeholder="Từ ngày">
    </div>
    <div class="filter-group">
        <input type="date" class="form-input" id="dateTo" placeholder="Đến ngày">
    </div>
    <div class="filter-group">
        <button class="btn btn-outline" onclick="resetFilters()">
            <i class="fas fa-undo"></i>
            Đặt lại
        </button>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-table-container">
    <table class="admin-table" id="ordersTable">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" class="form-checkbox" id="selectAll">
                </th>
                <th>Mã đơn hàng</th>
                <th>Khách hàng</th>
                <th>Sản phẩm</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái đơn</th>
                <th>TT Thanh toán</th>
                <th>Ngày đặt</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr data-order-id="<?= $order['id'] ?>" class="order-row">
                    <td>
                        <input type="checkbox" class="form-checkbox row-select" value="<?= $order['id'] ?>">
                    </td>
                    <td>
                        <div class="order-code">
                            <a href="/5s-fashion/admin/orders/show/<?= $order['id'] ?>" class="order-link">
                                #<?= htmlspecialchars($order['order_code']) ?>
                            </a>
                            <?php if (date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d')): ?>
                                <span class="new-badge">Mới</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-avatar">
                                <?= strtoupper(substr($order['customer_name'], 0, 2)) ?>
                            </div>
                            <div class="customer-details">
                                <div class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></div>
                                <div class="customer-email"><?= htmlspecialchars($order['customer_email']) ?></div>
                                <?php if (!empty($order['customer_phone'])): ?>
                                    <div class="customer-phone"><?= htmlspecialchars($order['customer_phone']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="order-products">
                            <?php if (isset($order['items_count'])): ?>
                                <div class="items-count"><?= $order['items_count'] ?> sản phẩm</div>
                            <?php endif; ?>
                            <?php if (isset($order['items_preview'])): ?>
                                <div class="items-preview">
                                    <?php foreach (array_slice($order['items_preview'], 0, 2) as $item): ?>
                                        <div class="item-preview">
                                            <?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>)
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($order['items_preview']) > 2): ?>
                                        <div class="items-more">+<?= count($order['items_preview']) - 2 ?> sản phẩm khác</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="order-total">
                            <div class="total-amount">₫<?= number_format($order['total_amount']) ?></div>
                            <?php if (isset($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                <div class="discount-info">
                                    <span class="discount-badge">-₫<?= number_format($order['discount_amount']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="payment-info">
                            <span class="payment-method payment-<?= $order['payment_method'] ?>">
                                <?php
                                $paymentMethods = [
                                    'cod' => 'COD',
                                    'bank_transfer' => 'Chuyển khoản',
                                    'vnpay' => 'VNPay',
                                    'momo' => 'Momo',
                                    'credit_card' => 'Thẻ tín dụng'
                                ];
                                echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                                ?>
                            </span>
                            <span class="payment-status payment-status-<?= $order['payment_status'] ?>">
                                <?php
                                $paymentStatuses = [
                                    'pending' => 'Chờ thanh toán',
                                    'paid' => 'Đã thanh toán',
                                    'confirmed' => 'Đã xác nhận',
                                    'failed' => 'Thanh toán thất bại',
                                    'refunded' => 'Đã hoàn tiền'
                                ];
                                echo $paymentStatuses[$order['payment_status']] ?? $order['payment_status'];
                                ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <?php if ($order['status'] === 'delivered'): ?>
                            <b style="color: green;">Hoàn thành</b>
                            <?php else: ?>
                        <select class="status-select" data-order-id="<?= $order['id'] ?>" onchange="updateOrderStatus(this)">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Đã gửi</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                        <?php endif; ?>
                    </td>
                    <td>
                        <select class="payment-status-select" data-order-id="<?= $order['id'] ?>" onchange="updatePaymentStatus(this)">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Chờ thanh toán</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                            <option value="confirmed" <?= $order['payment_status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
                        </select>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-main"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                            <div class="date-time"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/5s-fashion/admin/orders/show/<?= $order['id'] ?>" class="btn btn-sm btn-primary" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-info" onclick="printOrder(<?= $order['id'] ?>)" title="In đơn hàng">
                                <i class="fas fa-print"></i>
                            </button>
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-success" onclick="quickProcess(<?= $order['id'] ?>)" title="Xử lý nhanh">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-danger" onclick="cancelOrder(<?= $order['id'] ?>)" title="Hủy đơn">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                           
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bulk Actions -->
<div class="bulk-actions" id="bulkActions" style="display: none;">
    <div class="bulk-actions-content">
        <span class="selected-count">0 mục được chọn</span>
        <div class="bulk-action-buttons">
            <button class="btn btn-sm btn-primary" onclick="bulkUpdateStatus('processing')">Xử lý</button>
            <button class="btn btn-sm btn-success" onclick="bulkUpdateStatus('shipped')">Gửi hàng</button>
            <button class="btn btn-sm btn-info" onclick="bulkPrint()">In đơn hàng</button>
            <button class="btn btn-sm btn-warning" onclick="bulkExport()">Xuất Excel</button>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="admin-pagination">
    <div class="pagination-info">
        Hiển thị 1-<?= count($orders) ?> của <?= count($orders) ?> đơn hàng
    </div>
    <div class="pagination-controls">
        <!-- Add pagination controls here -->
    </div>
</div>

<!-- Quick Process Modal -->
<div class="modal" id="quickProcessModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Xử lý đơn hàng nhanh</h3>
            <button class="modal-close" onclick="closeQuickProcess()">&times;</button>
        </div>
        <form id="quickProcessForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Trạng thái mới</label>
                    <select name="status" class="form-select" id="quickProcessStatus">
                        <option value="processing">Đang xử lý</option>
                        <option value="shipped">Đã gửi hàng</option>
                        <option value="delivered">Đã giao hàng</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ghi chú admin</label>
                    <textarea name="admin_notes" class="form-textarea" rows="3" placeholder="Ghi chú về việc xử lý đơn hàng..."></textarea>
                </div>
                <div class="form-group" id="shippingGroup" style="display: none;">
                    <label class="form-label">Mã vận đơn</label>
                    <input type="text" name="tracking_code" class="form-input" placeholder="Nhập mã vận đơn">
                </div>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="send_notification" checked>
                        <span class="checkbox-indicator"></span>
                        <span class="checkbox-label">Gửi thông báo cho khách hàng</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickProcess()">Hủy</button>
                <button type="submit" class="btn btn-primary">Cập nhật đơn hàng</button>
            </div>
        </form>
    </div>
</div>

<style>
    .order-row {
        transition: background-color 0.2s;
    }

    .order-row:hover {
        background-color: #f9fafb;
    }

    .order-code {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .order-link {
        font-weight: 600;
        color: #3b82f6;
        text-decoration: none;
        font-family: monospace;
    }

    .order-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .new-badge {
        background: #ef4444;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .customer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #3b82f6;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .customer-details {
        flex: 1;
        min-width: 0;
    }

    .customer-name {
        font-weight: 600;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .customer-email {
        color: #6b7280;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .customer-phone {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .order-products {
        min-width: 200px;
    }

    .items-count {
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }

    .items-preview {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .item-preview {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .items-more {
        color: #9ca3af;
        font-style: italic;
    }

    .order-total {
        text-align: right;
    }

    .total-amount {
        font-weight: 700;
        color: #111827;
        font-size: 1.1rem;
    }

    .discount-info {
        margin-top: 4px;
    }

    .discount-badge {
        background: #fee2e2;
        color: #dc2626;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .payment-info {
        text-align: center;
    }

    .payment-method {
        display: block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .payment-cod {
        background: #fef3c7;
        color: #92400e;
    }

    .payment-bank_transfer {
        background: #dbeafe;
        color: #1e40af;
    }

    .payment-vnpay {
        background: #fee2e2;
        color: #dc2626;
    }

    .payment-momo {
        background: #fce7f3;
        color: #be185d;
    }

    .payment-credit_card {
        background: #e0e7ff;
        color: #3730a3;
    }

    .payment-status {
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 3px;
        display: inline-block;
    }

    .payment-status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .payment-status-paid {
        background: #d1fae5;
        color: #065f46;
    }

    .payment-status-confirmed {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .payment-status-failed {
        background: #fee2e2;
        color: #dc2626;
    }

    .payment-status-refunded {
        background: #f3f4f6;
        color: #374151;
    }

    .status-select,
    .payment-status-select {
        padding: 6px 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 0.875rem;
        background: white;
        cursor: pointer;
        min-width: 120px;
    }

    .status-select:focus,
    .payment-status-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }

    .date-info {
        text-align: left;
        min-width: 80px;
    }

    .date-main {
        font-weight: 500;
        color: #111827;
        font-size: 0.875rem;
    }

    .date-time {
        color: #6b7280;
        font-size: 0.75rem;
    }

    .action-buttons {
        display: flex;
        gap: 4px;
        white-space: nowrap;
    }

    .bulk-actions {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid #e5e7eb;
        z-index: 1000;
    }

    .bulk-actions-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .selected-count {
        font-weight: 600;
        color: #374151;
    }

    .bulk-action-buttons {
        display: flex;
        gap: 8px;
    }

    .admin-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 0.875rem;
    }

    #shippingGroup {
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
        margin-top: 16px;
    }

    @media (max-width: 768px) {
        .admin-filters {
            flex-direction: column;
            gap: 12px;
        }

        .filter-group {
            width: 100%;
        }

        .admin-table-container {
            overflow-x: auto;
        }

        .customer-info {
            min-width: 200px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 2px;
        }
    }
</style>

<script>
    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('paymentFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('dateFrom').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('dateTo').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const paymentFilter = document.getElementById('paymentFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;

        const rows = document.querySelectorAll('#ordersTable tbody tr');

        rows.forEach(row => {
            const orderCode = row.querySelector('.order-link').textContent.toLowerCase();
            const customerName = row.querySelector('.customer-name').textContent.toLowerCase();
            const customerEmail = row.querySelector('.customer-email').textContent.toLowerCase();
            const status = row.querySelector('.status-select').value;
            const payment = row.querySelector('.payment-method').className.split('-').pop();
            const orderDate = row.querySelector('.date-main').textContent;

            let showRow = true;

            // Search filter
            if (searchTerm && !orderCode.includes(searchTerm) && !customerName.includes(searchTerm) && !customerEmail.includes(searchTerm)) {
                showRow = false;
            }

            // Status filter
            if (statusFilter && status !== statusFilter) {
                showRow = false;
            }

            // Payment filter
            if (paymentFilter && payment !== paymentFilter) {
                showRow = false;
            }

            // Date filters
            if (dateFrom || dateTo) {
                const orderDateFormatted = orderDate.split('/').reverse().join('-'); // Convert dd/mm/yyyy to yyyy-mm-dd

                if (dateFrom && orderDateFormatted < dateFrom) {
                    showRow = false;
                }

                if (dateTo && orderDateFormatted > dateTo) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('paymentFilter').value = '';
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        filterTable();
    }

    // Bulk actions
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-select');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-select')) {
            updateBulkActions();
        }
    });

    function updateBulkActions() {
        const selectedBoxes = document.querySelectorAll('.row-select:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.querySelector('.selected-count');

        if (selectedBoxes.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = `${selectedBoxes.length} mục được chọn`;
        } else {
            bulkActions.style.display = 'none';
        }
    }

    // Order status update
    function updateOrderStatus(selectElement) {
        const orderId = selectElement.dataset.orderId;
        const newStatus = selectElement.value;
        const originalValue = selectElement.dataset.originalValue || selectElement.querySelector('option[selected]')?.value;

        if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng này?`)) {
            fetch(`/5s-fashion/admin/orders/update-status/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectElement.dataset.originalValue = newStatus;
                        showNotification('Đã cập nhật trạng thái đơn hàng!', 'success');

                        // Update row styling based on status
                        updateRowStatus(selectElement.closest('tr'), newStatus);
                    } else {
                        selectElement.value = originalValue;
                        showNotification('Lỗi: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    selectElement.value = originalValue;
                    showNotification('Có lỗi xảy ra!', 'error');
                });
        } else {
            selectElement.value = originalValue;
        }
    }

    // Payment status update
    function updatePaymentStatus(selectElement) {
        const orderId = selectElement.dataset.orderId;
        const newPaymentStatus = selectElement.value;
        const originalValue = selectElement.dataset.originalValue || selectElement.querySelector('option[selected]')?.value;

        if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thanh toán?`)) {
            fetch(`/5s-fashion/admin/orders/update-payment-status/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        payment_status: newPaymentStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectElement.dataset.originalValue = newPaymentStatus;
                        showNotification('Đã cập nhật trạng thái thanh toán!', 'success');
                    } else {
                        selectElement.value = originalValue;
                        showNotification('Lỗi: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    selectElement.value = originalValue;
                    showNotification('Có lỗi xảy ra!', 'error');
                });
        } else {
            selectElement.value = originalValue;
        }
    }

    // Shipper confirms delivery (button)
    function markAsDelivered(orderId) {
        if (!confirm('Xác nhận: đơn hàng đã được khách nhận?')) return;

        fetch(`/5s-fashion/admin/orders/update-status/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ status: 'delivered' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update select UI and row styling
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (row) {
                        const select = row.querySelector('.status-select');
                        if (select) select.value = 'delivered';
                        updateRowStatus(row, 'delivered');
                    }
                    showNotification('Đã chuyển trạng thái sang Đã giao', 'success');
                } else {
                    showNotification('Lỗi: ' + (data.message || 'Không thể cập nhật'), 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('Có lỗi xảy ra!', 'error');
            });
    }

    function updateRowStatus(row, status) {
        // Update action buttons based on status
        const actionButtons = row.querySelector('.action-buttons');
        const quickProcessBtn = actionButtons.querySelector('.btn-success');
        const cancelBtn = actionButtons.querySelector('.btn-danger');

        if (quickProcessBtn) {
            quickProcessBtn.style.display = status === 'pending' ? '' : 'none';
        }

        if (cancelBtn) {
            cancelBtn.style.display = ['pending', 'processing'].includes(status) ? '' : 'none';
        }
    }

    // Quick process
    let currentOrderId = null;

    function quickProcess(orderId) {
        currentOrderId = orderId;
        document.getElementById('quickProcessModal').style.display = 'block';
    }

    function closeQuickProcess() {
        document.getElementById('quickProcessModal').style.display = 'none';
        currentOrderId = null;
    }

    // Show/hide shipping fields based on status
    document.getElementById('quickProcessStatus').addEventListener('change', function() {
        const shippingGroup = document.getElementById('shippingGroup');
        if (this.value === 'shipped') {
            shippingGroup.style.display = 'block';
        } else {
            shippingGroup.style.display = 'none';
        }
    });

    document.getElementById('quickProcessForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!currentOrderId) return;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        data.send_notification = formData.has('send_notification');

        fetch(`/5s-fashion/admin/orders/quick-process/${currentOrderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Đã xử lý đơn hàng thành công!', 'success');
                    closeQuickProcess();

                    // Update the status select
                    const statusSelect = document.querySelector(`select[data-order-id="${currentOrderId}"]`);
                    if (statusSelect) {
                        statusSelect.value = data.status;
                        updateRowStatus(statusSelect.closest('tr'), data.status);
                    }
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra!', 'error');
            });
    });

    // Other actions
    function printOrder(orderId) {
        window.open(`/5s-fashion/admin/orders/print/${orderId}`, '_blank', 'width=800,height=600');
    }

    function cancelOrder(orderId) {
        if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
            fetch(`/5s-fashion/admin/orders/cancel/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusSelect = document.querySelector(`select[data-order-id="${orderId}"]`);
                        if (statusSelect) {
                            statusSelect.value = 'cancelled';
                            updateRowStatus(statusSelect.closest('tr'), 'cancelled');
                        }
                        showNotification('Đã hủy đơn hàng!', 'success');
                    } else {
                        showNotification('Lỗi: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra!', 'error');
                });
        }
    }

    function bulkUpdateStatus(status) {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        const statusText = {
            'processing': 'đang xử lý',
            'shipped': 'đã gửi hàng'
        } [status];

        if (confirm(`Bạn có chắc chắn muốn cập nhật ${selectedIds.length} đơn hàng thành "${statusText}"?`)) {
            // TODO: Implement bulk status update
            showNotification(`Đã cập nhật ${selectedIds.length} đơn hàng!`, 'success');
        }
    }

    function bulkPrint() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        window.open(`/5s-fashion/admin/orders/bulk-print?ids=${selectedIds.join(',')}`, '_blank');
    }

    function bulkExport() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        let url = '/5s-fashion/admin/orders/export';
        if (selectedIds.length > 0) {
            url += '?ids=' + selectedIds.join(',');
        }

        window.open(url, '_blank');
    }

    function exportOrders() {
        bulkExport();
    }

    function refreshOrders() {
        location.reload();
    }

    // Notification helper
    function showNotification(message, type = 'info') {
        // TODO: Implement notification system
        alert(message);
    }

    // Extract a user-friendly message from various AJAX response shapes
    function getAjaxMessage(data) {
        if (!data) return 'Có lỗi xảy ra!';
        // If server returned string directly
        if (typeof data === 'string') return data;
        // Prefer message, then error, then any other known keys
        return data.message || data.error || data.msg || data.message_text || 'Có lỗi xảy ra!';
    }

    // Close modal on outside click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('quickProcessModal');
        if (e.target === modal) {
            closeQuickProcess();
        }
    });

    // Auto-refresh orders every 30 seconds
    setInterval(() => {
        // Only refresh if no modals are open
        if (!document.querySelector('.modal[style*="display: block"]')) {
            // TODO: Implement AJAX refresh of orders table
            console.log('Auto-refreshing orders...');
        }
    }, 30000);
</script>
