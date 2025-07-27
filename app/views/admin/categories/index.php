<?php
// Categories index view
?>

<!-- Page Header -->
<div class="admin-header" style="">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-folder-open"></i>
            Quản lý danh mục
        </h1>
        <div class="admin-breadcrumb">
            Quản lý và tổ chức danh mục sản phẩm
        </div>
    </div>
    <div class="admin-header-actions">
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus"></i>
            Thêm danh mục
        </button>
    </div>
</div>

<!-- Statistics -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count($categories) ?></div>
        <div class="admin-stat-label">Tổng danh mục</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($categories, fn($c) => $c['status'] === 'active')) ?></div>
        <div class="admin-stat-label">Đang hoạt động</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= array_sum(array_column($categories, 'products_count')) ?></div>
        <div class="admin-stat-label">Tổng sản phẩm</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-number"><?= count(array_filter($categories, fn($c) => $c['parent_id'] === null)) ?></div>
        <div class="admin-stat-label">Danh mục cha</div>
    </div>
</div>

<!-- Filters and Search -->
<div class="admin-filters">
    <div class="filter-group">
        <input type="text" class="form-input" placeholder="Tìm kiếm danh mục..." id="searchInput">
    </div>
    <div class="filter-group">
        <select class="form-select" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="inactive">Tạm ẩn</option>
        </select>
    </div>
    <div class="filter-group">
        <select class="form-select" id="parentFilter">
            <option value="">Tất cả cấp độ</option>
            <option value="parent">Danh mục cha</option>
            <option value="child">Danh mục con</option>
        </select>
    </div>
</div>

<!-- Categories Table -->
<div class="admin-table-container">
    <table class="admin-table" id="categoriesTable">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" class="form-checkbox" id="selectAll">
                </th>
                <th>Danh mục</th>
                <th>Slug</th>
                <th>Sản phẩm</th>
                <th>Danh mục cha</th>
                <th>Thứ tự</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr data-category-id="<?= $category['id'] ?>">
                    <td>
                        <input type="checkbox" class="form-checkbox row-select" value="<?= $category['id'] ?>">
                    </td>
                    <td>
                        <div class="category-info">
                            <?php if (!empty($category['image'])): ?>
                                <?php
                                // Handle image path for file server
                                $imagePath = $category['image'];
                                if (strpos($imagePath, '/uploads/') === 0) {
                                    $cleanPath = substr($imagePath, 9);
                                } elseif (strpos($imagePath, 'uploads/') === 0) {
                                    $cleanPath = substr($imagePath, 8);
                                } else {
                                    $cleanPath = ltrim($imagePath, '/');
                                }
                                $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                ?>
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($category['name']) ?>" class="category-thumb">
                            <?php else: ?>
                                <div class="category-thumb-placeholder">
                                    <i class="fas fa-folder"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                                <?php if (!empty($category['description'])): ?>
                                    <div class="category-description"><?= htmlspecialchars(substr($category['description'], 0, 100)) ?><?= strlen($category['description']) > 100 ? '...' : '' ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="slug-code"><?= htmlspecialchars($category['slug']) ?></code>
                    </td>
                    <td class="text-center">
                        <span class="products-count <?= $category['products_count'] > 0 ? 'has-products' : 'no-products' ?>">
                            <?= number_format($category['products_count']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($category['parent_id']): ?>
                            <span class="parent-category"><?= htmlspecialchars($category['parent_name'] ?? 'Không xác định') ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="sort-order"><?= $category['sort_order'] ?></span>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $category['status'] ?>">
                            <?php
                            $statusText = [
                                'active' => 'Hoạt động',
                                'inactive' => 'Tạm ẩn'
                            ];
                            echo $statusText[$category['status']] ?? $category['status'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-main"><?= date('d/m/Y', strtotime($category['created_at'])) ?></div>
                            <div class="date-time"><?= date('H:i', strtotime($category['created_at'])) ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="editCategory(<?= $category['id'] ?>)" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="viewCategory(<?= $category['id'] ?>)" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($category['products_count'] == 0): ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')" title="Xóa">
                                    <i class="fas fa-trash"></i>
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
            <button class="btn btn-sm btn-warning" onclick="bulkUpdateStatus('active')">Kích hoạt</button>
            <button class="btn btn-sm btn-secondary" onclick="bulkUpdateStatus('inactive')">Vô hiệu hóa</button>
            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">Xóa</button>
        </div>
    </div>
</div>

<!-- Create/Edit Category Modal -->
<div class="modal" id="categoryModal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Thêm danh mục mới</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="categoryForm" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Tên danh mục</label>
                        <input type="text" name="name" class="form-input" required id="categoryName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" class="form-input" id="categorySlug">
                        <small class="form-help">Tự động tạo từ tên danh mục nếu để trống</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-textarea" rows="3" id="categoryDescription"></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Danh mục cha</label>
                        <select name="parent_id" class="form-select" id="categoryParent">
                            <option value="">Danh mục gốc</option>
                            <?php foreach ($categories as $cat): ?>
                                <?php if ($cat['parent_id'] === null): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Thứ tự sắp xếp</label>
                        <input type="number" name="sort_order" class="form-input" value="0" id="categorySortOrder">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Hình ảnh danh mục</label>
                    <div class="image-upload-area" id="imageUploadArea">
                        <input type="file" name="image" class="form-file" accept="image/*" id="categoryImage">
                        <div class="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>Kéo thả hình ảnh hoặc click để chọn</div>
                        </div>
                        <div class="image-preview" id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeImage()">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <div class="form-radio-group">
                        <label class="form-radio">
                            <input type="radio" name="status" value="active" checked>
                            <span class="radio-indicator"></span>
                            <span class="radio-label">Hoạt động</span>
                        </label>
                        <label class="form-radio">
                            <input type="radio" name="status" value="inactive">
                            <span class="radio-indicator"></span>
                            <span class="radio-label">Tạm ẩn</span>
                        </label>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="form-section">
                    <h4 class="form-section-title">Cài đặt SEO</h4>
                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-input" id="categoryMetaTitle">
                        <small class="form-help">Tiêu đề trang cho SEO (tối đa 60 ký tự)</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-textarea" rows="2" id="categoryMetaDescription"></textarea>
                        <small class="form-help">Mô tả trang cho SEO (tối đa 160 ký tự)</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Lưu danh mục</button>
            </div>
        </form>
    </div>
</div>

<style>
    .category-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .category-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .category-thumb-placeholder {
        width: 40px;
        height: 40px;
        background: #f3f4f6;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        border: 1px solid #e5e7eb;
    }

    .category-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }

    .category-description {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.3;
    }

    .slug-code {
        background: #f3f4f6;
        padding: 4px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.875rem;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .products-count {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .products-count.has-products {
        background: #dbeafe;
        color: #1e40af;
    }

    .products-count.no-products {
        background: #f3f4f6;
        color: #6b7280;
    }

    .parent-category {
        background: #ede9fe;
        color: #6d28d9;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .sort-order {
        font-weight: 600;
        color: #374151;
    }

    .date-info {
        text-align: left;
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

    .image-upload-area.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
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
        max-width: 200px;
        max-height: 150px;
        border-radius: 6px;
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

    .form-section {
        border-top: 1px solid #e5e7eb;
        padding-top: 20px;
        margin-top: 20px;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 16px;
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
</style>

<script>
    // Auto-generate slug from name
    document.getElementById('categoryName').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '-')
            .trim();
        document.getElementById('categorySlug').value = slug;
    });

    // Image upload handling
    document.getElementById('categoryImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
                document.querySelector('.upload-placeholder').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    // Click on upload area to trigger file input
    document.getElementById('imageUploadArea').addEventListener('click', function() {
        document.getElementById('categoryImage').click();
    });

    // Remove image
    function removeImage() {
        document.getElementById('categoryImage').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.querySelector('.upload-placeholder').style.display = 'block';
    }

    // Modal functions
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Thêm danh mục mới';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryForm').action = '/5s-fashion/admin/categories/store';
        document.getElementById('submitBtn').textContent = 'Lưu danh mục';
        removeImage();

        // Hide current image container if exists
        const currentImageContainer = document.getElementById('currentImageContainer');
        if (currentImageContainer) {
            currentImageContainer.style.display = 'none';
        }

        // Remove hidden input for image removal
        const removeImageInput = document.getElementById('removeCurrentImage');
        if (removeImageInput) {
            removeImageInput.remove();
        }

        document.getElementById('categoryModal').style.display = 'block';
    }

    function removeCurrentImage() {
        const currentImageContainer = document.getElementById('currentImageContainer');
        if (currentImageContainer) {
            currentImageContainer.style.display = 'none';
        }
        // Add hidden input to indicate image removal
        let removeImageInput = document.getElementById('removeCurrentImage');
        if (!removeImageInput) {
            removeImageInput = document.createElement('input');
            removeImageInput.type = 'hidden';
            removeImageInput.name = 'remove_current_image';
            removeImageInput.id = 'removeCurrentImage';
            document.getElementById('categoryForm').appendChild(removeImageInput);
        }
        removeImageInput.value = '1';
    }

    function editCategory(id) {
        // Set modal title and form action
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa danh mục';
        document.getElementById('categoryForm').action = '/5s-fashion/admin/categories/update/' + id;
        document.getElementById('submitBtn').textContent = 'Cập nhật';

        // Load category data via AJAX
        fetch(`/5s-fashion/admin/categories/api/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const category = data.data;

                    // Populate form fields
                    document.getElementById('categoryName').value = category.name || '';
                    document.getElementById('categorySlug').value = category.slug || '';
                    document.getElementById('categoryDescription').value = category.description || '';
                    document.getElementById('categoryParent').value = category.parent_id || '';
                    document.getElementById('categorySortOrder').value = category.sort_order || 0;
                    document.getElementById('categoryMetaTitle').value = category.meta_title || '';
                    document.getElementById('categoryMetaDescription').value = category.meta_description || '';

                    // Set status radio button
                    const statusRadios = document.querySelectorAll('input[name="status"]');
                    statusRadios.forEach(radio => {
                        radio.checked = radio.value === (category.status || 'active');
                    });

                    // Show current image if exists
                    if (category.image) {
                        // Create current image container if it doesn't exist
                        let currentImageContainer = document.getElementById('currentImageContainer');
                        if (!currentImageContainer) {
                            currentImageContainer = document.createElement('div');
                            currentImageContainer.id = 'currentImageContainer';
                            currentImageContainer.innerHTML = `
                                <div style="margin-bottom: 10px;">
                                    <label class="form-label">Hình ảnh hiện tại:</label>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img id="currentImage" src="" alt="Current image" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCurrentImage()">Xóa ảnh</button>
                                    </div>
                                </div>
                            `;
                            document.getElementById('imageUploadArea').parentNode.insertBefore(currentImageContainer, document.getElementById('imageUploadArea'));
                        }

                        // Process image path for file server
                        let imageUrl = category.image;
                        if (category.image.indexOf('/uploads/') === 0) {
                            const cleanPath = category.image.substring(9);
                            imageUrl = '/5s-fashion/serve-file.php?file=' + encodeURIComponent(cleanPath);
                        } else if (category.image.indexOf('uploads/') === 0) {
                            const cleanPath = category.image.substring(8);
                            imageUrl = '/5s-fashion/serve-file.php?file=' + encodeURIComponent(cleanPath);
                        } else {
                            const cleanPath = category.image.replace(/^\/+/, '');
                            imageUrl = '/5s-fashion/serve-file.php?file=' + encodeURIComponent(cleanPath);
                        }

                        document.getElementById('currentImage').src = imageUrl;
                        currentImageContainer.style.display = 'block';
                    } else {
                        const currentImageContainer = document.getElementById('currentImageContainer');
                        if (currentImageContainer) {
                            currentImageContainer.style.display = 'none';
                        }
                    }

                    // Show modal
                    document.getElementById('categoryModal').style.display = 'block';
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra khi tải dữ liệu danh mục!', 'error');
            });
    }

    function viewCategory(id) {
        window.location.href = '/5s-fashion/admin/categories/show/' + id;
    }

    function deleteCategory(id, name) {
        if (confirm(`Bạn có chắc chắn muốn xóa danh mục "${name}"?\n\nLưu ý: Chỉ có thể xóa danh mục không có sản phẩm.`)) {
            fetch(`/5s-fashion/admin/categories/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`tr[data-category-id="${id}"]`).remove();
                        showNotification('Đã xóa danh mục thành công!', 'success');
                    } else {
                        showNotification('Lỗi: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra khi xóa danh mục!', 'error');
                });
        }
    }

    function closeModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('parentFilter').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const parentFilter = document.getElementById('parentFilter').value;

        const rows = document.querySelectorAll('#categoriesTable tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.category-name').textContent.toLowerCase();
            const status = row.querySelector('.status-badge').textContent.trim();
            const hasParent = row.querySelector('.parent-category') !== null;

            let showRow = true;

            // Search filter
            if (searchTerm && !name.includes(searchTerm)) {
                showRow = false;
            }

            // Status filter
            if (statusFilter && !status.toLowerCase().includes(statusFilter.toLowerCase())) {
                showRow = false;
            }

            // Parent filter
            if (parentFilter === 'parent' && hasParent) {
                showRow = false;
            } else if (parentFilter === 'child' && !hasParent) {
                showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
        });
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

        const statusText = status === 'active' ? 'kích hoạt' : 'vô hiệu hóa';

        if (confirm(`Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} danh mục đã chọn?`)) {
            // TODO: Implement bulk status update
            showNotification(`Đã ${statusText} ${selectedIds.length} danh mục!`, 'success');
        }
    }

    function bulkDelete() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        if (confirm(`Bạn có chắc chắn muốn xóa ${selectedIds.length} danh mục đã chọn?\n\nLưu ý: Chỉ có thể xóa các danh mục không có sản phẩm.`)) {
            // TODO: Implement bulk delete
            showNotification(`Đã xóa ${selectedIds.length} danh mục!`, 'success');
        }
    }

    // Notification helper
    function showNotification(message, type = 'info') {
        // TODO: Implement notification system
        alert(message);
    }

    // Drag and drop for image upload
    const uploadArea = document.getElementById('imageUploadArea');

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('categoryImage').files = files;
            document.getElementById('categoryImage').dispatchEvent(new Event('change'));
        }
    });

    // Close modal on outside click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('categoryModal');
        if (e.target === modal) {
            closeModal();
        }
    });
</script>

<style>
    /* Fix admin header spacing */
    .admin-header {
        /* margin-top: 20px !important; */
        /* padding-top: 10px; */
    }

    .admin-content {
        padding: 24px;
        padding-top: 88px;
        flex: 1;
        min-height: 100vh;
    }
</style>
