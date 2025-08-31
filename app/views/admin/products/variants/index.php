<?php
$pageTitle = $title ?? 'Quản lý biến thể sản phẩm';
$currentPage = 'products';

// Helper function để tạo URL ảnh
function getImageUrl($imagePath)
{
    if (empty($imagePath) || $imagePath === '/assets/images/no-image.jpg') {
        return '/zone-fashion/assets/images/no-image.jpg';
    }

    // Nếu path bắt đầu với /uploads/, chuyển thành URL đầy đủ
    if (strpos($imagePath, '/uploads/') === 0) {
        // Loại bỏ /uploads/ và chỉ giữ phần sau (ví dụ: products/filename.webp)
        $fileParam = ltrim(str_replace('/uploads/', '', $imagePath), '/');
        return '/zone-fashion/serve-file.php?file=' . urlencode($fileParam);
    }

    return '/zone-fashion' . $imagePath;
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

    <!-- Thông báo về biến thể lỗi -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div>
                <h5 class="mb-1">Lưu ý quan trọng về biến thể</h5>
                <p class="mb-0">Mỗi biến thể chỉ nên có một giá trị cho mỗi loại thuộc tính (màu sắc, kích thước, chất liệu). Các biến thể có nhiều giá trị cùng loại thuộc tính sẽ được đánh dấu bằng cảnh báo màu vàng. Để sửa, hãy xóa biến thể đó và tạo lại.</p>
            </div>
        </div>
        <div class="mt-2 text-center">
            <a href="/zone-fashion/admin/products/<?= $product['id'] ?>/variants/fix-duplicates" class="btn btn-sm btn-warning" onclick="return confirm('Hành động này sẽ sửa các biến thể có nhiều thuộc tính trùng loại bằng cách chỉ giữ lại giá trị đầu tiên cho mỗi loại. Bạn có chắc chắn muốn tiếp tục?')">
                <i class="fas fa-wrench me-1"></i> Sửa tự động các biến thể lỗi
            </a>
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
        <div class="card-body variants-list">
            <?php include __DIR__ . '/partials/variants_list.php'; ?>
        </div>
    </div>
</div>

<!-- Add Variant Modal -->
<div class="modal" id="addVariantModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addVariantForm" method="POST" action="/zone-fashion/admin/products/<?= $product['id'] ?>/variants/create">
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
                                <input type="text" class="form-control" name="sku" required data-product-sku="<?= htmlspecialchars($product['sku'] ?? '') ?>">
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
                    <button type="submit" class="btn btn-success" id="createVariantBtn">Tạo biến thể</button>
                </div>
            </form>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('addVariantForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        var formData = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Có lỗi xảy ra khi tạo biến thể');
                            }
                        })
                        .catch(() => {
                            alert('Có lỗi xảy ra khi gửi request');
                        });
                    });
                }
            });
            </script>
        </div>
    </div>
</div>

<!-- Generate Variants Modal -->
<div class="modal" id="generateVariantsModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="/zone-fashion/admin/products/<?= $product['id'] ?>/variants/generate">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo biến thể tự động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Chọn các thuộc tính để tạo tự động biến thể. Bạn chỉ được chọn một giá trị cho mỗi loại thuộc tính (màu sắc, kích thước, chất liệu).
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
                                                <input class="form-check-input" type="radio" name="selected_colors" value="<?= $value['id'] ?>" id="color_<?= $value['id'] ?>">
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
                                                <input class="form-check-input" type="radio" name="selected_sizes" value="<?= $value['id'] ?>" id="size_<?= $value['id'] ?>">
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
                                            <input class="form-check-input" type="radio" name="selected_materials" value="<?= $material['id'] ?>" id="material_<?= $material['id'] ?>">
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
                    <button type="submit" class="btn btn-primary" id="generateVariantsBtn">Tạo biến thể</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script để xử lý form submission và modal reset -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lấy reference đến modal và form
    const generateModal = document.getElementById('generateVariantsModal');
    const generateForm = generateModal.querySelector('form');

    // Reset form khi modal đóng
    generateModal.addEventListener('hidden.bs.modal', function() {
        generateForm.reset();
        // Bỏ chọn tất cả radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.checked = false;
        });
    });

    // Xử lý form submission bằng AJAX thay vì submit thông thường
    generateForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Hiển thị loading state
        const submitBtn = document.getElementById('generateVariantsBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';

        // Gửi form data bằng Fetch API
        fetch(generateForm.action, {
            method: 'POST',
            body: new FormData(generateForm),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị thông báo thành công
                alert(data.message || 'Tạo biến thể thành công');

                // Đóng modal
                const bsModal = bootstrap.Modal.getInstance(generateModal);
                bsModal.hide();

                // Tải lại danh sách biến thể
                loadVariants();
            } else {
                // Hiển thị lỗi
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo biến thể');
        })
        .finally(() => {
            // Khôi phục trạng thái nút
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});

// Hàm để tải lại danh sách biến thể mà không cần tải lại trang
const loadVariants = function() {
    const variantsList = document.querySelector('.variants-list');
    if (!variantsList) return;

    // Lấy product ID từ URL
    const pathParts = window.location.pathname.split('/');
    const productIdIndex = pathParts.indexOf('products') + 1;
    if (productIdIndex > 0 && productIdIndex < pathParts.length) {
        const productId = pathParts[productIdIndex];

        // Tải lại danh sách biến thể
        fetch(`/zone-fashion/admin/products/${productId}/variants?format=json`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    variantsList.innerHTML = data.html;
                }
            })
            .catch(error => {
                console.error('Error loading variants:', error);
            });
    }
};
</script>

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
                                <label class="form-label">Thuộc tính</label>
                                <div id="edit_attributes_display" class="mt-2">
                                    <p class="text-muted">Đang tải thuộc tính...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script để xử lý form chỉnh sửa biến thể -->
<script>
function editVariant(variantId, variantName, sku, price, salePrice, stockQuantity, status) {
    // Điền thông tin biến thể vào form
    document.getElementById('edit_variant_id').value = variantId;
    document.getElementById('edit_variant_name').value = variantName;
    document.getElementById('edit_sku').value = sku;
    document.getElementById('edit_price').value = price || '';
    document.getElementById('edit_sale_price').value = salePrice || '';
    document.getElementById('edit_stock_quantity').value = stockQuantity;
    document.getElementById('edit_status').value = status;

    // Lấy thuộc tính của biến thể
    const attributesDisplay = document.getElementById('edit_attributes_display');
    attributesDisplay.innerHTML = '<p class="text-muted mb-0">Đang tải thông tin thuộc tính...</p>';

    // Set action cho form
    const form = document.getElementById('editVariantForm');
    const productId = window.location.pathname.split('/')[4]; // /admin/products/:productId/variants
    form.action = `/zone-fashion/admin/products/${productId}/variants/${variantId}/update`;

    // Lấy thông tin thuộc tính
    fetch(`/zone-fashion/admin/products/${productId}/variants/${variantId}/attributes`)
        .then(response => response.json())
        .then(data => {
            if (data.attributes && data.attributes.length > 0) {
                let attributesHtml = '<div class="d-flex flex-wrap gap-2 mt-1">';

                data.attributes.forEach(attr => {
                    const badgeClass = attr.isDuplicate ? 'bg-warning text-dark' : 'bg-light text-dark';

                    attributesHtml += `
                        <span class="badge ${badgeClass}">
                            ${attr.attribute_name}: ${attr.value}
                            ${attr.color_code ? `<span class="color-preview ms-1" style="background-color: ${attr.color_code}; width: 12px; height: 12px; display: inline-block; border-radius: 2px; border: 1px solid #dee2e6;"></span>` : ''}
                        </span>
                    `;
                });

                attributesHtml += '</div>';
                attributesDisplay.innerHTML = attributesHtml;
            } else {
                attributesDisplay.innerHTML = '<p class="text-muted mb-0">Không có thuộc tính được định nghĩa</p>';
            }
        });

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('editVariantModal'));
    modal.show();
}
</script>

<!-- Thêm script xử lý form tạo biến thể đơn -->
<script src="/assets/js/variant-form.js"></script>
