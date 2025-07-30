<?php
$pageTitle = $title ?? 'Quản lý biến thể sản phẩm';
$currentPage = 'products';

// Helper function để tạo URL ảnh
function getImageUrl($imagePath)
{
    if (empty($imagePath) || $imagePath === '/assets/images/no-image.jpg') {
        return '/5s-fashion/assets/images/no-image.jpg';
    }

    // Nếu path bắt đầu với /uploads/, chuyển thành serve-file.php format
    if (strpos($imagePath, '/uploads/') === 0) {
        // Loại bỏ /uploads/ và chỉ giữ phần sau (ví dụ: products/filename.webp)
        $fileParam = ltrim(str_replace('/uploads/', '', $imagePath), '/');
        return '/5s-fashion/serve-file.php?file=' . urlencode($fileParam);
    }

    return '/5s-fashion' . $imagePath;
}
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><?= htmlspecialchars($product['name']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/products">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">Biến thể</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#generateVariantsModal">
                <i class="fas fa-magic me-2"></i>Tạo tự động
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                <i class="fas fa-plus me-2"></i>Thêm biến thể
            </button>
        </div>
    </div>

    <!-- Product Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <img src="<?= getImageUrl($product['featured_image'] ?? '') ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        class="img-fluid rounded">
                </div>
                <div class="col-md-10">
                    <h5><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="text-muted mb-1">SKU: <?= htmlspecialchars($product['sku']) ?></p>
                    <p class="text-muted mb-1">Giá gốc: <?= number_format($product['price']) ?>₫</p>
                    <p class="text-muted mb-0">
                        Trạng thái biến thể:
                        <?php if ($product['has_variants']): ?>
                            <span class="badge bg-success">Đã bật</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Chưa bật</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Variants Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Danh sách biến thể (<?= count($variants) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($variants)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có biến thể nào</h5>
                    <p class="text-muted">Bạn có thể tạo biến thể thủ công hoặc tự động từ các thuộc tính</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateVariantsModal">
                        <i class="fas fa-magic me-2"></i>Tạo tự động
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên biến thể</th>
                                <th>SKU</th>
                                <th>Thuộc tính</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variants as $variant): ?>
                                <tr>
                                    <td>
                                        <img src="<?= getImageUrl($variant['image'] ?: $product['featured_image'] ?: '') ?>"
                                            alt="<?= htmlspecialchars($variant['variant_name']) ?>"
                                            class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($variant['variant_name']) ?></div>
                                        <small class="text-muted">Thứ tự: <?= $variant['sort_order'] ?></small>
                                    </td>
                                    <td>
                                        <code><?= htmlspecialchars($variant['sku']) ?></code>
                                    </td>
                                    <td>
                                        <?php if (!empty($variant['attributes'])): ?>
                                            <?php foreach ($variant['attributes'] as $attr): ?>
                                                <span class="badge bg-light text-dark me-1">
                                                    <?= htmlspecialchars($attr['attribute_name']) ?>: <?= htmlspecialchars($attr['value']) ?>
                                                    <?php if ($attr['color_code']): ?>
                                                        <span class="color-preview ms-1" style="background-color: <?= htmlspecialchars($attr['color_code']) ?>; width: 12px; height: 12px; display: inline-block; border-radius: 2px; border: 1px solid #dee2e6;"></span>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($variant['sale_price']): ?>
                                            <div class="text-danger fw-bold"><?= number_format($variant['sale_price']) ?>₫</div>
                                            <small class="text-muted text-decoration-line-through"><?= number_format($variant['price'] ?: $product['price']) ?>₫</small>
                                        <?php else: ?>
                                            <div class="fw-bold"><?= number_format($variant['price'] ?: $product['price']) ?>₫</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold <?= $variant['stock_quantity'] <= 5 ? 'text-danger' : 'text-success' ?>">
                                            <?= $variant['stock_quantity'] ?>
                                        </div>
                                        <?php if ($variant['reserved_quantity'] > 0): ?>
                                            <small class="text-warning">Đặt trước: <?= $variant['reserved_quantity'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'active' => 'bg-success',
                                            'inactive' => 'bg-secondary',
                                            'out_of_stock' => 'bg-danger'
                                        ];
                                        $statusTexts = [
                                            'active' => 'Hoạt động',
                                            'inactive' => 'Tạm dừng',
                                            'out_of_stock' => 'Hết hàng'
                                        ];
                                        ?>
                                        <span class="badge <?= $statusClasses[$variant['status']] ?? 'bg-secondary' ?>">
                                            <?= $statusTexts[$variant['status']] ?? $variant['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="editVariant(<?= $variant['id'] ?>)"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="deleteVariant(<?= $variant['id'] ?>)"
                                                title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Variant Modal -->
<div class="modal" id="addVariantModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="/5s-fashion/admin/products/<?= $product['id'] ?>/variants/create">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm biến thể mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên biến thể <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="variant_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Màu sắc</label>
                                <select class="form-select" name="color_id">
                                    <option value="">Chọn màu sắc</option>
                                    <?php if (!empty($colors)): ?>
                                        <?php foreach ($colors as $color): ?>
                                            <?php $values = json_decode($color['values'], true) ?? []; ?>
                                            <?php foreach ($values as $value): ?>
                                                <option value="<?= $value['id'] ?>">
                                                    <?= htmlspecialchars($value['value']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kích thước</label>
                                <select class="form-select" name="size_id">
                                    <option value="">Chọn kích thước</option>
                                    <?php if (!empty($sizes)): ?>
                                        <?php foreach ($sizes as $size): ?>
                                            <?php $values = json_decode($size['values'], true) ?? []; ?>
                                            <?php foreach ($values as $value): ?>
                                                <option value="<?= $value['id'] ?>">
                                                    <?= htmlspecialchars($value['value']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Chất liệu</label>
                                <select class="form-select" name="material_id">
                                    <option value="">Chọn chất liệu</option>
                                    <?php foreach ($materials as $material): ?>
                                        <option value="<?= $material['id'] ?>">
                                            <?= htmlspecialchars($material['value']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá riêng</label>
                                <input type="number" class="form-control" name="price" step="0.01" min="0" placeholder="Để trống = dùng giá gốc">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá khuyến mãi</label>
                                <input type="number" class="form-control" name="sale_price" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tồn kho</label>
                                <input type="number" class="form-control" name="stock_quantity" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="status">
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Tạm dừng</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thứ tự</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Tạo biến thể</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Variants Modal -->
<div class="modal" id="generateVariantsModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="/5s-fashion/admin/products/<?= $product['id'] ?>/variants/generate">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo biến thể tự động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Chọn các thuộc tính để tạo tự động tất cả các kết hợp biến thể có thể.
                    </div>

                    <div class="mb-4">
                        <h6>Cài đặt chung</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">SKU cơ sở</label>
                                    <input type="text" class="form-control" name="base_sku" value="<?= htmlspecialchars($product['sku']) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Giá mặc định</label>
                                    <input type="number" class="form-control" name="base_price" step="0.01" min="0" placeholder="Để trống = dùng giá gốc">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tồn kho mặc định</label>
                                    <input type="number" class="form-control" name="base_stock" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <?php if (!empty($colors)): ?>
                        <div class="mb-4">
                            <h6>Màu sắc</h6>
                            <div class="row">
                                <?php foreach ($colors as $color): ?>
                                    <?php $values = json_decode($color['values'], true) ?? []; ?>
                                    <?php foreach ($values as $value): ?>
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_colors[]" value="<?= $value['id'] ?>" id="color_<?= $value['id'] ?>">
                                                <label class="form-check-label d-flex align-items-center" for="color_<?= $value['id'] ?>">
                                                    <?php if ($value['color_code']): ?>
                                                        <span class="color-preview me-2" style="background-color: <?= htmlspecialchars($value['color_code']) ?>; width: 20px; height: 20px; display: inline-block; border-radius: 3px; border: 1px solid #dee2e6;"></span>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($value['value']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Sizes -->
                    <?php if (!empty($sizes)): ?>
                        <div class="mb-4">
                            <h6>Kích thước</h6>
                            <div class="row">
                                <?php foreach ($sizes as $size): ?>
                                    <?php $values = json_decode($size['values'], true) ?? []; ?>
                                    <?php foreach ($values as $value): ?>
                                        <div class="col-md-2 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_sizes[]" value="<?= $value['id'] ?>" id="size_<?= $value['id'] ?>">
                                                <label class="form-check-label" for="size_<?= $value['id'] ?>">
                                                    <?= htmlspecialchars($value['value']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Materials -->
                    <?php if (!empty($materials)): ?>
                        <div class="mb-4">
                            <h6>Chất liệu</h6>
                            <div class="row">
                                <?php foreach ($materials as $material): ?>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="selected_materials[]" value="<?= $material['id'] ?>" id="material_<?= $material['id'] ?>">
                                            <label class="form-check-label" for="material_<?= $material['id'] ?>">
                                                <?= htmlspecialchars($material['value']) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo biến thể</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Variant Modal -->
<div class="modal" id="editVariantModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="" id="editVariantForm">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="variant_id" id="edit_variant_id">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa biến thể</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên biến thể <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="variant_name" id="edit_variant_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" id="edit_sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá riêng</label>
                                <input type="number" class="form-control" name="price" id="edit_price" step="0.01" min="0" placeholder="Để trống = dùng giá gốc">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá khuyến mãi</label>
                                <input type="number" class="form-control" name="sale_price" id="edit_sale_price" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tồn kho</label>
                                <input type="number" class="form-control" name="stock_quantity" id="edit_stock_quantity" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="status" id="edit_status">
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Tạm dừng</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thứ tự</label>
                                <input type="number" class="form-control" name="sort_order" id="edit_sort_order" value="0" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thuộc tính biến thể</label>
                        <div id="variant_attributes_display" class="p-3 bg-light rounded">
                            <!-- Attributes will be displayed here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật biến thể</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editVariant(variantId) {
        // Load variant data and show edit modal
        fetch(`/5s-fashion/admin/products/<?= $product['id'] ?>/variants/${variantId}/data`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate edit form with variant data
                    showEditVariantModal(data.variant);
                } else {
                    alert('Không thể tải dữ liệu biến thể');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
    }

    function deleteVariant(variantId) {
        if (confirm('Bạn có chắc chắn muốn xóa biến thể này?')) {
            window.location.href = `/5s-fashion/admin/products/<?= $product['id'] ?>/variants/${variantId}/delete`;
        }
    }

    function showEditVariantModal(variant) {
        // Implementation for edit modal will be added
        console.log('Edit variant:', variant);
    }
</script>



<script>
// Simple modal system
document.addEventListener('DOMContentLoaded', function() {
    let currentModal = null;

    // Show modal function
    function showModal(modalId) {
        // Hide current modal if any
        if (currentModal) {
            hideModal(currentModal.id);
        }

        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Show modal
        modal.classList.add('show');
        document.body.classList.add('modal-open');

        currentModal = modal;

        // Close on modal background click (not modal content)
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal(modalId);
            }
        });
    }

    // Hide modal function
    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }

        document.body.classList.remove('modal-open');
        currentModal = null;
    }

    // Handle show modal triggers
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const targetModal = this.getAttribute('data-bs-target').substring(1);
            showModal(targetModal);
        });
    });

    // Handle close buttons
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeBtn => {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = this.closest('.modal');
            if (modal) {
                hideModal(modal.id);
            }
        });
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && currentModal) {
            hideModal(currentModal.id);
        }
    });
});    // Other functions
    function editVariant(variantId) {
        fetch(`/5s-fashion/admin/products/<?= $product['id'] ?>/variants/${variantId}/data`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showEditVariantModal(data.variant);
                } else {
                    alert('Không thể tải dữ liệu biến thể');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            });
    }

    function deleteVariant(variantId) {
        if (confirm('Bạn có chắc chắn muốn xóa biến thể này?')) {
            window.location.href = `/5s-fashion/admin/products/<?= $product['id'] ?>/variants/${variantId}/delete`;
        }
    }

    function showEditVariantModal(variant) {
        console.log('Edit variant:', variant);

        // Populate form fields
        document.getElementById('edit_variant_id').value = variant.id;
        document.getElementById('edit_variant_name').value = variant.variant_name || '';
        document.getElementById('edit_sku').value = variant.sku || '';
        document.getElementById('edit_price').value = variant.price || '';
        document.getElementById('edit_sale_price').value = variant.sale_price || '';
        document.getElementById('edit_stock_quantity').value = variant.stock_quantity || 0;
        document.getElementById('edit_status').value = variant.status || 'active';
        document.getElementById('edit_sort_order').value = variant.sort_order || 0;

        // Set form action
        document.getElementById('editVariantForm').action = `/5s-fashion/admin/products/<?= $product['id'] ?>/variants/${variant.id}/update`;

        // Display variant attributes
        const attributesDisplay = document.getElementById('variant_attributes_display');
        if (variant.attributes && variant.attributes.length > 0) {
            let attributesHtml = '<div class="row">';
            variant.attributes.forEach(attr => {
                attributesHtml += `
                    <div class="col-md-4 mb-2">
                        <strong>${attr.attribute_name}:</strong>
                        <span class="badge bg-secondary">${attr.value}</span>
                    </div>
                `;
            });
            attributesHtml += '</div>';
            attributesDisplay.innerHTML = attributesHtml;
        } else {
            attributesDisplay.innerHTML = '<p class="text-muted mb-0">Không có thuộc tính được định nghĩa</p>';
        }

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('editVariantModal'));
        modal.show();
    }
</script>
