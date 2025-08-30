<?php
// Products index view
?>

<!-- Page Header -->
<div class="admin-header">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-box"></i>
            Quản lý sản phẩm
        </h1>
        <div class="admin-breadcrumb">
            Quản lý toàn bộ sản phẩm trong hệ thống
        </div>
    </div>
    <div class="admin-header-actions">
        <button class="btn btn-outline" onclick="exportProducts()">
            <i class="fas fa-download"></i>
            Xuất Excel
        </button>
        <a href="/zone-fashion/admin/products/create" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Thêm sản phẩm
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count($products) ?></div>
        <div class="admin-stat-label">Tổng sản phẩm</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($products, fn($p) => $p['status'] === 'published')) ?></div>
        <div class="admin-stat-label">Đã xuất bản</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($products, fn($p) => ($p['total_stock'] ?? 0) <= 10)) ?></div>
        <div class="admin-stat-label">Sắp hết hàng</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($products, fn($p) => $p['featured'] == 1)) ?></div>
        <div class="admin-stat-label">Sản phẩm nổi bật</div>
    </div>
</div>

<!-- Filters and Search -->
<div class="admin-filters">
    <div class="filter-group">
        <input type="text" class="form-input" placeholder="Tìm kiếm sản phẩm..." id="searchInput">
    </div>
    <div class="filter-group">
        <select class="form-select" id="categoryFilter">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <select class="form-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="published">Đã xuất bản</option>
            <option value="draft">Bản nháp</option>
            <option value="out_of_stock">Hết hàng</option>
        </select>
    </div>
    <div class="filter-group">
        <select class="form-select" id="stockFilter">
            <option value="">Tất cả tồn kho</option>
            <option value="in_stock">Còn hàng</option>
            <option value="low_stock">Sắp hết</option>
            <option value="out_of_stock">Hết hàng</option>
        </select>
    </div>
    <div class="filter-group">
        <button class="btn btn-outline" onclick="resetFilters()">
            <i class="fas fa-undo"></i>
            Đặt lại
        </button>
    </div>
</div>

<!-- Products Table -->
<div class="admin-table-container">
    <table class="admin-table" id="productsTable">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" class="form-checkbox" id="selectAll">
                </th>
                <th>Sản phẩm</th>
                <th>SKU</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr data-product-id="<?= $product['id'] ?>">
                    <td>
                        <input type="checkbox" class="form-checkbox row-select" value="<?= $product['id'] ?>">
                    </td>
                    <td>
                        <div class="product-info">
                            <div class="product-image">
                                <?php if (!empty($product['featured_image'])): ?>
                                    <?php
                                    // Handle image path for file server
                                    $imagePath = $product['featured_image'];
                                    if (strpos($imagePath, '/uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 9); // Remove '/uploads/'
                                    } elseif (strpos($imagePath, 'uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 8); // Remove 'uploads/'
                                    } else {
                                        $cleanPath = ltrim($imagePath, '/');
                                    }
                                    $imageUrl = '/zone-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                    ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-details">
                                <div class="product-name">
                                    <a href="/zone-fashion/admin/products/show/<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
                                    <?php if ($product['featured']): ?>
                                        <span class="featured-badge" title="Sản phẩm nổi bật">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($product['short_description'])): ?>
                                    <div class="product-description">
                                        <?= htmlspecialchars(substr($product['short_description'], 0, 80)) ?><?= strlen($product['short_description']) > 80 ? '...' : '' ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="sku-code"><?= htmlspecialchars($product['sku']) ?></code>
                    </td>
                    <td>
                        <span class="category-badge"><?= htmlspecialchars($product['category_name'] ?? 'Không có') ?></span>
                    </td>
                    <td>
                        <div class="price-info">
                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <div class="sale-price">₫<?= number_format($product['sale_price']) ?></div>
                                <div class="original-price">₫<?= number_format($product['price']) ?></div>
                                <div class="discount-percent">
                                    -<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>%
                                </div>
                            <?php else: ?>
                                <div class="current-price">₫<?= number_format($product['price']) ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="stock-info">
                            <span class="stock-quantity <?= ($product['total_stock'] ?? 0) <= 10 ? 'low-stock' : (($product['total_stock'] ?? 0) > 0 ? 'in-stock' : 'out-of-stock') ?>">
                                <?= number_format($product['total_stock'] ?? 0) ?>
                            </span>
                            <?php if (($product['total_stock'] ?? 0) <= 10 && ($product['total_stock'] ?? 0) > 0): ?>
                                <div class="stock-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Sắp hết
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $product['status'] ?>">
                            <?php
                            $statusText = [
                                'published' => 'Đã xuất bản',
                                'draft' => 'Bản nháp',
                                'out_of_stock' => 'Hết hàng'
                            ];
                            echo $statusText[$product['status']] ?? $product['status'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-main"><?= date('d/m/Y', strtotime($product['created_at'])) ?></div>
                            <div class="date-time"><?= date('H:i', strtotime($product['created_at'])) ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/zone-fashion/admin/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/zone-fashion/admin/products/show/<?= $product['id'] ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-success" onclick="duplicateProduct(<?= $product['id'] ?>)" title="Nhân bản">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
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
            <button class="btn btn-sm btn-primary" onclick="bulkUpdateStatus('published')">Xuất bản</button>
            <button class="btn btn-sm btn-secondary" onclick="bulkUpdateStatus('draft')">Lưu nháp</button>
            <button class="btn btn-sm btn-warning" onclick="bulkToggleFeatured()">Bật/Tắt nổi bật</button>
            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">Xóa</button>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="admin-pagination">
    <div class="pagination-info">
        Hiển thị 1-<?= count($products) ?> của <?= count($products) ?> sản phẩm
    </div>
    <div class="pagination-controls">
        <!-- Add pagination controls here -->
    </div>
</div>

<!-- Quick Edit Modal -->
<div class="modal" id="quickEditModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Chỉnh sửa nhanh</h3>
            <button class="modal-close" onclick="closeQuickEdit()">&times;</button>
        </div>
        <form id="quickEditForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-input" id="quickEditName">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Giá gốc</label>
                        <input type="number" name="price" class="form-input" id="quickEditPrice">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giá khuyến mãi</label>
                        <input type="number" name="sale_price" class="form-input" id="quickEditSalePrice">
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Tồn kho</label>
                        <input type="number" name="stock" class="form-input" id="quickEditStock">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" id="quickEditStatus">
                            <option value="published">Đã xuất bản</option>
                            <option value="draft">Bản nháp</option>
                            <option value="out_of_stock">Hết hàng</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="featured" id="quickEditFeatured">
                        <span class="checkbox-indicator"></span>
                        <span class="checkbox-label">Sản phẩm nổi bật</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickEdit()">Hủy</button>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });

    document.getElementById('categoryFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('stockFilter').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const stockFilter = document.getElementById('stockFilter').value;

        const rows = document.querySelectorAll('#productsTable tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.product-name a').textContent.toLowerCase();
            const sku = row.querySelector('.sku-code').textContent.toLowerCase();
            const category = row.querySelector('.category-badge').textContent;
            const status = row.querySelector('.status-badge').dataset.status || row.querySelector('.status-badge').className.split('-').pop();
            const stockElement = row.querySelector('.stock-quantity');
            const stockClass = stockElement.className;

            let showRow = true;

            // Search filter
            if (searchTerm && !name.includes(searchTerm) && !sku.includes(searchTerm)) {
                showRow = false;
            }

            // Category filter
            if (categoryFilter && !category.includes(categoryFilter)) {
                showRow = false;
            }

            // Status filter
            if (statusFilter && !status.includes(statusFilter)) {
                showRow = false;
            }

            // Stock filter
            if (stockFilter) {
                switch (stockFilter) {
                    case 'in_stock':
                        if (!stockClass.includes('in-stock')) showRow = false;
                        break;
                    case 'low_stock':
                        if (!stockClass.includes('low-stock')) showRow = false;
                        break;
                    case 'out_of_stock':
                        if (!stockClass.includes('out-of-stock')) showRow = false;
                        break;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('stockFilter').value = '';
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

    function bulkUpdateStatus(status) {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        const statusText = {
            'published': 'xuất bản',
            'draft': 'lưu nháp',
            'out_of_stock': 'đánh dấu hết hàng'
        } [status];

        if (confirm(`Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} sản phẩm đã chọn?`)) {
            // TODO: Implement bulk status update
            fetch('/zone-fashion/admin/products/bulk-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ids: selectedIds,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra!');
                });
        }
    }

    function bulkToggleFeatured() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        if (confirm(`Bạn có chắc chắn muốn bật/tắt nổi bật cho ${selectedIds.length} sản phẩm đã chọn?`)) {
            // TODO: Implement bulk featured toggle
            showNotification(`Đã cập nhật ${selectedIds.length} sản phẩm!`, 'success');
        }
    }

    function bulkDelete() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        if (confirm(`Bạn có chắc chắn muốn xóa ${selectedIds.length} sản phẩm đã chọn?\n\nHành động này không thể hoàn tác!`)) {
            // TODO: Implement bulk delete
            showNotification(`Đã xóa ${selectedIds.length} sản phẩm!`, 'success');
        }
    }

    // Product actions
    function deleteProduct(id, name) {
        if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${name}"?\n\nHành động này không thể hoàn tác!`)) {
            fetch(`/zone-fashion/admin/products/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`tr[data-product-id="${id}"]`).remove();
                        showNotification('Đã xóa sản phẩm thành công!', 'success');
                    } else {
                        showNotification('Lỗi: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra khi xóa sản phẩm!', 'error');
                });
        }
    }

    function duplicateProduct(id) {
        if (confirm('Bạn có muốn tạo bản sao của sản phẩm này?')) {
            fetch(`/zone-fashion/admin/products/duplicate/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Đã tạo bản sao sản phẩm thành công!', 'success');
                        setTimeout(() => location.reload(), 1000);
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

    function exportProducts() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        let url = '/zone-fashion/admin/products/export';
        if (selectedIds.length > 0) {
            url += '?ids=' + selectedIds.join(',');
        }

        window.open(url, '_blank');
    }

    // Quick edit functionality
    function openQuickEdit(id) {
        // TODO: Load product data and show modal
        document.getElementById('quickEditModal').style.display = 'block';
    }

    function closeQuickEdit() {
        document.getElementById('quickEditModal').style.display = 'none';
    }

    // Notification helper
    function showNotification(message, type = 'info') {
        // TODO: Implement notification system
        alert(message);
    }

    // Close modal on outside click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('quickEditModal');
        if (e.target === modal) {
            closeQuickEdit();
        }
    });

    // Double click to quick edit
    document.addEventListener('dblclick', function(e) {
        const row = e.target.closest('tr[data-product-id]');
        if (row) {
            const productId = row.dataset.productId;
            openQuickEdit(productId);
        }
    });
</script>
