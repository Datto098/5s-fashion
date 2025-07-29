<?php
$statusOptions = [
    'published' => 'Đã xuất bản',
    'draft' => 'Bản nháp',
    'out_of_stock' => 'Hết hàng'
];

$visibilityOptions = [
    'public' => 'Công khai',
    'private' => 'Riêng tư',
    'hidden' => 'Ẩn'
];
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Chỉnh sửa sản phẩm</h1>
                    <p class="text-muted mb-0">Cập nhật thông tin sản phẩm</p>
                </div>
                <div>
                    <a href="/5s-fashion/admin/products" class="btn btn-secondary">
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

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/5s-fashion/admin/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data" id="productForm">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="productName" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="productName" name="name"
                                       value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productSKU" class="form-label">Mã sản phẩm (SKU)</label>
                                <input type="text" class="form-control" id="productSKU" name="sku"
                                       value="<?= htmlspecialchars($product['sku']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productBarcode" class="form-label">Mã vạch</label>
                                <input type="text" class="form-control" id="productBarcode" name="barcode"
                                       value="<?= htmlspecialchars($product['barcode'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shortDescription" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="shortDescription" name="short_description" rows="3"><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Mô tả chi tiết</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="6"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign"></i> Giá cả
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="productPrice" class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="productPrice" name="price"
                                           value="<?= $product['price'] ?>" step="0.01" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="comparePrice" class="form-label">Giá so sánh</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="comparePrice" name="compare_price"
                                           value="<?= $product['compare_price'] ?? '' ?>" step="0.01" min="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="costPrice" class="form-label">Giá vốn</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="costPrice" name="cost_price"
                                           value="<?= $product['cost_price'] ?? '' ?>" step="0.01" min="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-images"></i> Hình ảnh sản phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Featured Image -->
                        <div class="mb-4">
                            <label class="form-label">Ảnh đại diện</label>
                            <?php if (!empty($product['featured_image'])): ?>
                                <div class="current-image mb-3">
                                    <?php
                                    // Create correct image URL using constants
                                    $imagePath = $product['featured_image'];

                                    // Handle different path formats
                                    if (strpos($imagePath, '/uploads/') === 0) {
                                        // Path is /uploads/products/... -> remove /uploads/ part
                                        $cleanPath = substr($imagePath, 9); // Remove '/uploads/'
                                    } elseif (strpos($imagePath, 'uploads/') === 0) {
                                        // Path is uploads/products/... -> remove uploads/ part
                                        $cleanPath = substr($imagePath, 8); // Remove 'uploads/'
                                    } else {
                                        // Path is products/... or already clean
                                        $cleanPath = ltrim($imagePath, '/');
                                    }

                                    // Use file server instead of direct access
                                    $fullImagePath = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);

                                    // Debug output
                                    echo "<!-- DEBUG: Original path: " . htmlspecialchars($imagePath) . " -->";
                                    echo "<!-- DEBUG: Clean path: " . htmlspecialchars($cleanPath) . " -->";
                                    echo "<!-- DEBUG: Full image path: " . htmlspecialchars($fullImagePath) . " -->";
                                    echo "<!-- DEBUG: UPLOAD_URL: " . (defined('UPLOAD_URL') ? UPLOAD_URL : 'NOT DEFINED') . " -->";
                                    ?>
                                    <img src="<?= htmlspecialchars($fullImagePath) ?>"
                                         alt="Current image" class="img-thumbnail" style="max-height: 200px;">
                                    <p class="text-muted small mt-1">Ảnh hiện tại: <?= htmlspecialchars($product['featured_image']) ?></p>
                                    <p class="text-muted small">URL: <?= htmlspecialchars($fullImagePath) ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="upload-area" id="featuredImageUpload">
                                <input type="file" name="featured_image" id="featuredImage" accept="image/*" hidden>
                                <div class="upload-content text-center p-4 border border-dashed rounded">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1">Kéo thả ảnh vào đây hoặc <button type="button" class="btn btn-link p-0">chọn file</button></p>
                                    <small class="text-muted">Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP) tối đa 5MB</small>
                                </div>
                            </div>
                            <div id="featuredImagePreview" class="mt-3" style="display: none;">
                                <img id="featuredImagePreviewImg" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>

                        <!-- Gallery Images -->
                        <div class="mb-3">
                            <label class="form-label">Thư viện ảnh</label>
                            <?php
                            $galleryImages = [];
                            if (!empty($product['gallery'])) {
                                $galleryImages = json_decode($product['gallery'], true) ?: [];
                            }
                            ?>
                            <?php if (!empty($galleryImages)): ?>
                                <div class="current-gallery mb-3">
                                    <div class="row">
                                        <?php foreach ($galleryImages as $image): ?>
                                            <div class="col-md-3 mb-2">
                                                <img src="/5s-fashion/public/<?= htmlspecialchars($image) ?>"
                                                     alt="" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="text-muted small">Thư viện ảnh hiện tại</p>
                                </div>
                            <?php endif; ?>
                            <div class="upload-area" id="galleryUpload">
                                <input type="file" name="product_images[]" id="galleryImages" accept="image/*" multiple hidden>
                                <div class="upload-content text-center p-4 border border-dashed rounded">
                                    <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                    <p class="mb-1">Kéo thả nhiều ảnh vào đây hoặc <button type="button" class="btn btn-link p-0">chọn files</button></p>
                                    <small class="text-muted">Có thể chọn nhiều file ảnh cùng lúc</small>
                                </div>
                            </div>
                            <div id="galleryPreview" class="mt-3 row" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Shipping & Attributes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shipping-fast"></i> Vận chuyển & Thuộc tính
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productWeight" class="form-label">Trọng lượng (g)</label>
                                <input type="number" class="form-control" id="productWeight" name="weight"
                                       value="<?= $product['weight'] ?? '' ?>" min="0" step="0.1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productDimensions" class="form-label">Kích thước</label>
                                <input type="text" class="form-control" id="productDimensions" name="dimensions"
                                       value="<?= htmlspecialchars($product['dimensions'] ?? '') ?>"
                                       placeholder="VD: 20x30x5 cm">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="trackQuantity" name="track_quantity"
                                           <?= (!empty($product['track_quantity'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="trackQuantity">
                                        Theo dõi số lượng tồn kho
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="continueSellingOutOfStock" name="continue_selling_when_out_of_stock"
                                           <?= (!empty($product['continue_selling_when_out_of_stock'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="continueSellingOutOfStock">
                                        Tiếp tục bán khi hết hàng
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="requiresShipping" name="requires_shipping"
                                           <?= (!empty($product['requires_shipping'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="requiresShipping">
                                        Yêu cầu vận chuyển
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="isTaxable" name="is_taxable"
                                           <?= (!empty($product['is_taxable'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isTaxable">
                                        Chịu thuế
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search"></i> Tối ưu hóa công cụ tìm kiếm (SEO)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="metaTitle" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="metaTitle" name="meta_title"
                                   value="<?= htmlspecialchars($product['meta_title'] ?? '') ?>" maxlength="60">
                            <small class="text-muted">Tối đa 60 ký tự</small>
                        </div>
                        <div class="mb-3">
                            <label for="metaDescription" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="metaDescription" name="meta_description" rows="3" maxlength="160"><?= htmlspecialchars($product['meta_description'] ?? '') ?></textarea>
                            <small class="text-muted">Tối đa 160 ký tự</small>
                        </div>
                        <div class="mb-3">
                            <label for="metaKeywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="metaKeywords" name="meta_keywords"
                                   value="<?= htmlspecialchars($product['meta_keywords'] ?? '') ?>"
                                   placeholder="từ khóa 1, từ khóa 2, từ khóa 3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status & Visibility -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Cài đặt
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="productStatus" class="form-label">Trạng thái</label>
                            <select class="form-select" id="productStatus" name="status">
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($product['status'] === $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productVisibility" class="form-label">Hiển thị</label>
                            <select class="form-select" id="productVisibility" name="visibility">
                                <?php foreach ($visibilityOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (($product['visibility'] ?? 'public') === $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="isFeatured" name="is_featured"
                                   <?= (!empty($product['featured'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isFeatured">
                                Sản phẩm nổi bật
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-folder"></i> Danh mục
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Danh mục sản phẩm <span class="text-danger">*</span></label>
                            <select class="form-select" id="productCategory" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($product['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật sản phẩm
                            </button>
                            <a href="/5s-fashion/admin/products/show/<?= $product['id'] ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <a href="/5s-fashion/admin/products" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Featured Image Upload
    const featuredUpload = document.getElementById('featuredImageUpload');
    const featuredInput = document.getElementById('featuredImage');
    const featuredPreview = document.getElementById('featuredImagePreview');
    const featuredPreviewImg = document.getElementById('featuredImagePreviewImg');

    featuredUpload.addEventListener('click', () => featuredInput.click());
    featuredUpload.addEventListener('dragover', handleDragOver);
    featuredUpload.addEventListener('drop', handleFeaturedDrop);

    featuredInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            previewFeaturedImage(this.files[0]);
        }
    });

    function handleFeaturedDrop(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            featuredInput.files = files;
            previewFeaturedImage(files[0]);
        }
    }

    function previewFeaturedImage(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            featuredPreviewImg.src = e.target.result;
            featuredPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Gallery Images Upload
    const galleryUpload = document.getElementById('galleryUpload');
    const galleryInput = document.getElementById('galleryImages');
    const galleryPreview = document.getElementById('galleryPreview');

    galleryUpload.addEventListener('click', () => galleryInput.click());
    galleryUpload.addEventListener('dragover', handleDragOver);
    galleryUpload.addEventListener('drop', handleGalleryDrop);

    galleryInput.addEventListener('change', function() {
        if (this.files) {
            previewGalleryImages(this.files);
        }
    });

    function handleGalleryDrop(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            galleryInput.files = files;
            previewGalleryImages(files);
        }
    }

    function previewGalleryImages(files) {
        galleryPreview.innerHTML = '';
        galleryPreview.style.display = 'flex';

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="height: 100px; object-fit: cover;">`;
                galleryPreview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.style.borderColor = '#007bff';
    }

    // Form validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const name = document.getElementById('productName').value.trim();
        const price = document.getElementById('productPrice').value;
        const category = document.getElementById('productCategory').value;

        if (!name || !price || !category) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return false;
        }
    });

    // Character count for meta fields
    const metaTitle = document.getElementById('metaTitle');
    const metaDescription = document.getElementById('metaDescription');

    metaTitle.addEventListener('input', function() {
        const count = this.value.length;
        const small = this.nextElementSibling;
        small.textContent = `${count}/60 ký tự`;
        small.className = count > 60 ? 'text-danger' : 'text-muted';
    });

    metaDescription.addEventListener('input', function() {
        const count = this.value.length;
        const small = this.nextElementSibling;
        small.textContent = `${count}/160 ký tự`;
        small.className = count > 160 ? 'text-danger' : 'text-muted';
    });
});
</script>

<style>
.upload-area {
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.upload-area:hover {
    border-color: #007bff !important;
}

.upload-content {
    background: #f8f9fa;
}

.current-image img,
.current-gallery img {
    border: 2px solid #dee2e6;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.text-danger {
    font-weight: 500;
}

@media (max-width: 768px) {
    .col-lg-8, .col-lg-4 {
        margin-bottom: 1rem;
    }
}
</style>
