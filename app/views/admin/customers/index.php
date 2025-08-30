<?php
// Customers index view
?>

<!-- Page Header -->
<div class="admin-header">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-users"></i>
            Quản lý khách hàng
        </h1>
        <div class="admin-breadcrumb">
            Theo dõi và quản lý thông tin khách hàng
        </div>
    </div>
    <div class="admin-header-actions">
        <button class="btn btn-outline" onclick="exportCustomers()">
            <i class="fas fa-download"></i>
            Xuất Excel
        </button>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus"></i>
            Thêm khách hàng
        </button>
    </div>
</div>

<!-- Statistics -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count($customers) ?></div>
        <div class="admin-stat-label">Tổng khách hàng</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($customers, fn($c) => $c['status'] === 'active')) ?></div>
        <div class="admin-stat-label">Đang hoạt động</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($customers, fn($c) => ($c['total_orders'] ?? 0) > 0)) ?></div>
        <div class="admin-stat-label">Đã mua hàng</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number">₫<?= number_format(array_sum(array_column($customers, 'total_spent'))) ?></div>
        <div class="admin-stat-label">Tổng chi tiêu</div>
    </div>
</div>

<!-- Filters and Search -->
<div class="admin-filters">
    <div class="filter-group">
        <input type="text" class="form-input" placeholder="Tìm kiếm khách hàng..." id="searchInput">
    </div>
    <div class="filter-group">
        <select class="form-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="inactive">Tạm khóa</option>
            <option value="banned">Bị cấm</option>
        </select>
    </div>
    <div class="filter-group">
        <select class="form-select" id="customerTypeFilter">
            <option value="">Tất cả loại</option>
            <option value="new">Khách hàng mới</option>
            <option value="returning">Khách hàng cũ</option>
            <option value="vip">Khách VIP</option>
        </select>
    </div>
    <div class="filter-group">
        <select class="form-select" id="orderCountFilter">
            <option value="">Tất cả đơn hàng</option>
            <option value="0">Chưa mua</option>
            <option value="1-5">1-5 đơn</option>
            <option value="6-10">6-10 đơn</option>
            <option value="10+">Trên 10 đơn</option>
        </select>
    </div>
    <div class="filter-group">
        <button class="btn btn-outline" onclick="resetFilters()">
            <i class="fas fa-undo"></i>
            Đặt lại
        </button>
    </div>
</div>

<!-- Customers Table -->
<div class="admin-table-container">
    <table class="admin-table" id="customersTable">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" class="form-checkbox" id="selectAll">
                </th>
                <th>Khách hàng</th>
                <th>Thông tin liên hệ</th>
                <th>Đơn hàng</th>
                <th>Tổng chi tiêu</th>
                <th>Loại khách hàng</th>
                <th>Trạng thái</th>
                <th>Ngày đăng ký</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr data-customer-id="<?= $customer['id'] ?>">
                    <td>
                        <input type="checkbox" class="form-checkbox row-select" value="<?= $customer['id'] ?>">
                    </td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-avatar">
                                <?php if (!empty($customer['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($customer['avatar']) ?>" alt="<?= htmlspecialchars($customer['full_name']) ?>">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <?= strtoupper(substr($customer['full_name'], 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="customer-details">
                                <div class="customer-name">
                                    <?= htmlspecialchars($customer['full_name']) ?>
                                    <?php if (isset($customer['email_verified_at']) && $customer['email_verified_at']): ?>
                                        <span class="verified-badge" title="Email đã xác thực">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="customer-username">@<?= htmlspecialchars($customer['username']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= htmlspecialchars($customer['email']) ?>"><?= htmlspecialchars($customer['email']) ?></a>
                            </div>
                            <?php if (!empty($customer['phone'])): ?>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:<?= htmlspecialchars($customer['phone']) ?>"><?= htmlspecialchars($customer['phone']) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="order-stats">
                            <div class="order-count">
                                <span class="count-number"><?= number_format($customer['total_orders'] ?? 0) ?></span>
                                <span class="count-label">đơn hàng</span>
                            </div>
                            <?php if (($customer['total_orders'] ?? 0) > 0): ?>
                                <div class="last-order">
                                    Lần cuối: <?= isset($customer['last_order_date']) ? date('d/m/Y', strtotime($customer['last_order_date'])) : 'N/A' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="spending-info">
                            <div class="total-spent">₫<?= number_format($customer['total_spent'] ?? 0) ?></div>
                            <?php if (($customer['total_orders'] ?? 0) > 0): ?>
                                <div class="avg-order">
                                    Trung bình: ₫<?= number_format(($customer['total_spent'] ?? 0) / ($customer['total_orders'] ?? 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="customer-type-badge customer-type-<?= $customer['customer_type'] ?? 'regular' ?>">
                            <?php
                            $customerTypes = [
                                'new' => 'Khách mới',
                                'returning' => 'Khách cũ',
                                'vip' => 'VIP',
                                'regular' => 'Thường'
                            ];
                            echo $customerTypes[$customer['customer_type'] ?? 'regular'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $customer['status'] ?>">
                            <?php
                            $statusText = [
                                'active' => 'Hoạt động',
                                'inactive' => 'Tạm khóa',
                                'banned' => 'Bị cấm'
                            ];
                            echo $statusText[$customer['status']] ?? $customer['status'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-main"><?= date('d/m/Y', strtotime($customer['created_at'])) ?></div>
                            <div class="date-time"><?= date('H:i', strtotime($customer['created_at'])) ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-info" onclick="editCustomer(<?= $customer['id'] ?>)" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-success" onclick="sendEmail(<?= $customer['id'] ?>)" title="Gửi email">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <?php if ($customer['status'] === 'active'): ?>
                                <button class="btn btn-sm btn-warning" onclick="toggleStatus(<?= $customer['id'] ?>, 'inactive')" title="Tạm khóa">
                                    <i class="fas fa-lock"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-success" onclick="toggleStatus(<?= $customer['id'] ?>, 'active')" title="Kích hoạt">
                                    <i class="fas fa-unlock"></i>
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
            <button class="btn btn-sm btn-primary" onclick="bulkUpdateStatus('active')">Kích hoạt</button>
            <button class="btn btn-sm btn-warning" onclick="bulkUpdateStatus('inactive')">Tạm khóa</button>
            <button class="btn btn-sm btn-info" onclick="bulkSendEmail()">Gửi email</button>
            <button class="btn btn-sm btn-success" onclick="bulkExport()">Xuất Excel</button>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="admin-pagination">
    <div class="pagination-info">
        Hiển thị 1-<?= count($customers) ?> của <?= count($customers) ?> khách hàng
    </div>
    <div class="pagination-controls">
        <!-- Add pagination controls here -->
    </div>
</div>

<!-- Create/Edit Customer Modal -->
<div class="modal" id="customerModal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Thêm khách hàng mới</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="customerForm" method="POST">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Họ và tên</label>
                        <input type="text" name="full_name" class="form-input" required id="customerFullName">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-input" required id="customerUsername">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-input" required id="customerEmail">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-input" id="customerPhone">
                    </div>
                </div>

                <div class="form-group" id="passwordGroup">
                    <label class="form-label required">Mật khẩu</label>
                    <input type="password" name="password" class="form-input" id="customerPassword">
                    <small class="form-help">Để trống nếu không muốn thay đổi mật khẩu (chỉ khi chỉnh sửa)</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Avatar</label>
                    <div class="image-upload-area" id="avatarUploadArea">
                        <input type="file" name="avatar" class="form-file" accept="image/*" id="customerAvatar">
                        <div class="upload-placeholder">
                            <i class="fas fa-user-circle"></i>
                            <div>Tải lên ảnh đại diện</div>
                        </div>
                        <div class="image-preview" id="avatarPreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeAvatar()">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Loại khách hàng</label>
                        <select name="customer_type" class="form-select" id="customerType">
                            <option value="regular">Thường</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" id="customerStatus">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Tạm khóa</option>
                            <option value="banned">Bị cấm</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="email_verified" id="customerEmailVerified">
                        <span class="checkbox-indicator"></span>
                        <span class="checkbox-label">Email đã được xác thực</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Lưu khách hàng</button>
            </div>
        </form>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal" id="emailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Gửi email cho khách hàng</h3>
            <button class="modal-close" onclick="closeEmailModal()">&times;</button>
        </div>
        <form id="emailForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Người nhận</label>
                    <input type="text" class="form-input" id="emailRecipient" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label required">Tiêu đề</label>
                    <input type="text" name="subject" class="form-input" required id="emailSubject">
                </div>
                <div class="form-group">
                    <label class="form-label required">Nội dung</label>
                    <textarea name="message" class="form-textarea" rows="8" required id="emailMessage"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEmailModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Gửi email</button>
            </div>
        </form>
    </div>
</div>

<style>
    .customer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .customer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        flex-shrink: 0;
    }

    .customer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: #3b82f6;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.125rem;
    }

    .customer-details {
        flex: 1;
        min-width: 0;
    }

    .customer-name {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .customer-name a {
        font-weight: 600;
        color: #111827;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .customer-name a:hover {
        color: #3b82f6;
    }

    .verified-badge {
        color: #10b981;
        font-size: 0.875rem;
    }

    .customer-username {
        color: #6b7280;
        font-size: 0.875rem;
        font-family: monospace;
    }

    .contact-info {
        min-width: 180px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        font-size: 0.875rem;
    }

    .contact-item i {
        width: 16px;
        color: #6b7280;
    }

    .contact-item a {
        color: #3b82f6;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .contact-item a:hover {
        text-decoration: underline;
    }

    .order-stats {
        text-align: center;
    }

    .order-count {
        margin-bottom: 4px;
    }

    .count-number {
        font-weight: 700;
        font-size: 1.25rem;
        color: #111827;
    }

    .count-label {
        color: #6b7280;
        font-size: 0.875rem;
        margin-left: 4px;
    }

    .last-order {
        color: #6b7280;
        font-size: 0.75rem;
    }

    .spending-info {
        text-align: right;
    }

    .total-spent {
        font-weight: 700;
        font-size: 1.125rem;
        color: #111827;
        margin-bottom: 4px;
    }

    .avg-order {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .customer-type-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .customer-type-new {
        background: #dbeafe;
        color: #1e40af;
    }

    .customer-type-returning {
        background: #d1fae5;
        color: #065f46;
    }

    .customer-type-vip {
        background: #fef3c7;
        color: #92400e;
    }

    .customer-type-regular {
        background: #f3f4f6;
        color: #374151;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fef3c7;
        color: #92400e;
    }

    .status-banned {
        background: #fee2e2;
        color: #dc2626;
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

    .image-upload-area {
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        position: relative;
        transition: border-color 0.3s;
    }

    .image-upload-area:hover {
        border-color: #d1d5db;
    }

    .upload-placeholder {
        color: #6b7280;
    }

    .upload-placeholder i {
        font-size: 2rem;
        margin-bottom: 8px;
        display: block;
        color: #9ca3af;
    }

    .image-preview {
        position: relative;
        display: inline-block;
    }

    .image-preview img {
        max-width: 120px;
        max-height: 120px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ef4444;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    document.getElementById('customerTypeFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('orderCountFilter').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const typeFilter = document.getElementById('customerTypeFilter').value;
        const orderCountFilter = document.getElementById('orderCountFilter').value;

        const rows = document.querySelectorAll('#customersTable tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.customer-name a').textContent.toLowerCase();
            const email = row.querySelector('.contact-item a[href^="mailto"]').textContent.toLowerCase();
            const status = row.querySelector('.status-badge').className.split('-').pop();
            const type = row.querySelector('.customer-type-badge').className.split('-').pop();
            const orderCount = parseInt(row.querySelector('.count-number').textContent);

            let showRow = true;

            // Search filter
            if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
                showRow = false;
            }

            // Status filter
            if (statusFilter && status !== statusFilter) {
                showRow = false;
            }

            // Type filter
            if (typeFilter && type !== typeFilter) {
                showRow = false;
            }

            // Order count filter
            if (orderCountFilter) {
                switch (orderCountFilter) {
                    case '0':
                        if (orderCount !== 0) showRow = false;
                        break;
                    case '1-5':
                        if (orderCount < 1 || orderCount > 5) showRow = false;
                        break;
                    case '6-10':
                        if (orderCount < 6 || orderCount > 10) showRow = false;
                        break;
                    case '10+':
                        if (orderCount <= 10) showRow = false;
                        break;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('customerTypeFilter').value = '';
        document.getElementById('orderCountFilter').value = '';
        filterTable();
    }

    // Auto-generate username from full name
    document.getElementById('customerFullName').addEventListener('input', function() {
        const fullName = this.value;
        const username = fullName.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '')
            .trim();

        if (document.getElementById('customerUsername').value === '') {
            document.getElementById('customerUsername').value = username;
        }
    });

    // Avatar upload handling
    document.getElementById('customerAvatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('avatarPreview').style.display = 'block';
                document.querySelector('.upload-placeholder').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    function removeAvatar() {
        document.getElementById('customerAvatar').value = '';
        document.getElementById('avatarPreview').style.display = 'none';
        document.querySelector('.upload-placeholder').style.display = 'block';
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

    // Modal functions
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Thêm khách hàng mới';
        document.getElementById('customerForm').reset();
        document.getElementById('customerForm').action = '/zone-fashion/admin/customers/store';
        document.getElementById('submitBtn').textContent = 'Lưu khách hàng';
        document.getElementById('passwordGroup').style.display = 'block';
        document.getElementById('customerPassword').required = true;
        removeAvatar();
        document.getElementById('customerModal').style.display = 'block';
    }

    function editCustomer(id) {
        // Set modal title and form action
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa khách hàng';
        document.getElementById('customerForm').action = '/zone-fashion/admin/customers/update/' + id;
        document.getElementById('submitBtn').textContent = 'Cập nhật';
        document.getElementById('customerPassword').required = false;
        document.querySelector('#passwordGroup .form-help').style.display = 'block';

        // Load customer data via AJAX
        fetch(`/zone-fashion/admin/customers/api/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const customer = data.data;

                    // Populate form fields
                    document.getElementById('customerFullName').value = customer.full_name || '';
                    document.getElementById('customerUsername').value = customer.username || '';
                    document.getElementById('customerEmail').value = customer.email || '';
                    document.getElementById('customerPhone').value = customer.phone || '';
                    document.getElementById('customerPassword').value = ''; // Don't populate password for security

                    // Set status radio button
                    const statusRadios = document.querySelectorAll('input[name="status"]');
                    statusRadios.forEach(radio => {
                        radio.checked = radio.value === (customer.status || 'active');
                    });

                    // Show modal
                    document.getElementById('customerModal').style.display = 'block';
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra khi tải dữ liệu khách hàng!', 'error');
                // Still show modal even if AJAX fails
                document.getElementById('customerModal').style.display = 'block';
            });
    }

    function closeModal() {
        document.getElementById('customerModal').style.display = 'none';
    }

    // Customer actions
    function toggleStatus(customerId, newStatus) {
        const statusText = {
            'active': 'kích hoạt',
            'inactive': 'tạm khóa',
            'banned': 'cấm'
        } [newStatus];

        if (confirm(`Bạn có chắc chắn muốn ${statusText} khách hàng này?`)) {
            fetch(`/zone-fashion/admin/customers/update-status/${customerId}`, {
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
                        // Update the status badge and button
                        const row = document.querySelector(`tr[data-customer-id="${customerId}"]`);
                        const statusBadge = row.querySelector('.status-badge');
                        const actionButton = row.querySelector('.btn-warning, .btn-success');

                        statusBadge.className = `status-badge status-${newStatus}`;
                        statusBadge.textContent = {
                            'active': 'Hoạt động',
                            'inactive': 'Tạm khóa',
                            'banned': 'Bị cấm'
                        } [newStatus];

                        // Update button
                        if (newStatus === 'active') {
                            actionButton.className = 'btn btn-sm btn-warning';
                            actionButton.innerHTML = '<i class="fas fa-lock"></i>';
                            actionButton.title = 'Tạm khóa';
                            actionButton.onclick = () => toggleStatus(customerId, 'inactive');
                        } else {
                            actionButton.className = 'btn btn-sm btn-success';
                            actionButton.innerHTML = '<i class="fas fa-unlock"></i>';
                            actionButton.title = 'Kích hoạt';
                            actionButton.onclick = () => toggleStatus(customerId, 'active');
                        }

                        showNotification(`Đã ${statusText} khách hàng thành công!`, 'success');
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

    // Email functions
    let currentCustomerId = null;

    function sendEmail(customerId) {
        const row = document.querySelector(`tr[data-customer-id="${customerId}"]`);
        const customerName = row.querySelector('.customer-name a').textContent;
        const customerEmail = row.querySelector('.contact-item a[href^="mailto"]').textContent;

        currentCustomerId = customerId;
        document.getElementById('emailRecipient').value = `${customerName} (${customerEmail})`;
        document.getElementById('emailModal').style.display = 'block';
    }

    function closeEmailModal() {
        document.getElementById('emailModal').style.display = 'none';
        document.getElementById('emailForm').reset();
        currentCustomerId = null;
    }

    document.getElementById('emailForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!currentCustomerId) return;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch(`/zone-fashion/admin/customers/send-email/${currentCustomerId}`, {
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
                    showNotification('Đã gửi email thành công!', 'success');
                    closeEmailModal();
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra!', 'error');
            });
    });

    // Bulk actions
    function bulkUpdateStatus(status) {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        const statusText = {
            'active': 'kích hoạt',
            'inactive': 'tạm khóa',
            'banned': 'cấm'
        } [status];

        if (confirm(`Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} khách hàng đã chọn?`)) {
            // TODO: Implement bulk status update
            showNotification(`Đã ${statusText} ${selectedIds.length} khách hàng!`, 'success');
        }
    }

    function bulkSendEmail() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        // TODO: Implement bulk email sending
        showNotification(`Đã gửi email cho ${selectedIds.length} khách hàng!`, 'success');
    }

    function bulkExport() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        let url = '/zone-fashion/admin/customers/export';
        if (selectedIds.length > 0) {
            url += '?ids=' + selectedIds.join(',');
        }

        window.open(url, '_blank');
    }

    function exportCustomers() {
        bulkExport();
    }

    // Notification helper
    function showNotification(message, type = 'info') {
        // TODO: Implement notification system
        alert(message);
    }

    // Close modals on outside click
    window.addEventListener('click', function(e) {
        const customerModal = document.getElementById('customerModal');
        const emailModal = document.getElementById('emailModal');

        if (e.target === customerModal) {
            closeModal();
        }

        if (e.target === emailModal) {
            closeEmailModal();
        }
    });
</script>
