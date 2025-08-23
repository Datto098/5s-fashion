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
                    <?php if (!empty($product['has_variants'])): ?>
                        <a href="/5s-fashion/admin/products/<?= $product['id'] ?>/variants" class="btn btn-info ms-2">
                            <i class="fas fa-cogs"></i> Quản lý biến thể
                        </a>
                    <?php endif; ?>
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

                <!-- Inventory & Variants -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-boxes"></i> Kho hàng & Biến thể
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="hasVariants" name="has_variants" value="1"
                                           <?= !empty($product['has_variants']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="hasVariants">
                                        Sản phẩm có biến thể (màu sắc, kích thước...)
                                    </label>
                                </div>
                                <small class="text-muted">Bật tùy chọn này nếu sản phẩm có nhiều phiên bản khác nhau</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="manageStock" name="manage_stock" value="1"
                                           <?= !empty($product['manage_stock']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="manageStock">
                                        Quản lý tồn kho
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Inventory (for products without variants) -->
                        <div id="simpleInventory" class="inventory-section" style="<?= !empty($product['has_variants']) ? 'display:none' : '' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="stockQuantity" class="form-label">Số lượng tồn kho</label>
                                    <input type="number" class="form-control" id="stockQuantity" name="stock_quantity"
                                           value="<?= $product['stock_quantity'] ?? '' ?>" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lowStockThreshold" class="form-label">Ngưỡng cảnh báo hết hàng</label>
                                    <input type="number" class="form-control" id="lowStockThreshold" name="low_stock_threshold"
                                           value="<?= $product['low_stock_threshold'] ?? '5' ?>" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Variant Management (for products with variants) -->
                        <div id="variantManagement" class="variant-section" style="<?= empty($product['has_variants']) ? 'display:none' : '' ?>">
                            <?php if (!empty($product['has_variants'])): ?>
                                <!-- Product already has variants enabled -->
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Sản phẩm đã bật chế độ biến thể:</strong>
                                    <a href="/5s-fashion/admin/products/<?= $product['id'] ?>/variants" class="btn btn-sm btn-success ms-2" target="_blank">
                                        <i class="fas fa-cog"></i> Quản lý biến thể & tồn kho
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Product doesn't have variants yet -->
                                <div class="alert alert-warning" id="saveFirstAlert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Lưu ý:</strong> Bạn cần <strong>lưu sản phẩm</strong> trước khi có thể quản lý biến thể.
                                    <br>
                                    <small>Nhấn nút "Cập nhật sản phẩm" bên dưới, sau đó quay lại để thiết lập biến thể.</small>
                                </div>

                                <div class="alert alert-info" id="afterSaveAlert" style="display:none;">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Sản phẩm có biến thể:</strong>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" disabled>
                                        <i class="fas fa-cog"></i> Quản lý biến thể & tồn kho
                                    </button>
                                    <br>
                                    <small class="text-muted">Tính năng này sẽ khả dụng sau khi lưu sản phẩm.</small>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($product['has_variants'])): ?>
                                <!-- Display current variants summary -->
                                <div class="variants-summary">
                                    <h6>Tóm tắt biến thể hiện tại:</h6>
                                    <?php
                                    // Get variants data (you may need to pass this from controller)
                                    $variants = $product['variants'] ?? [];
                                    if (!empty($variants)):
                                    ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>SKU</th>
                                                        <th>Tên biến thể</th>
                                                        <th>Tồn kho</th>
                                                        <th>Trạng thái</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($variants as $variant): ?>
                                                        <tr>
                                                            <td><code><?= htmlspecialchars($variant['sku']) ?></code></td>
                                                            <td><?= htmlspecialchars($variant['variant_name']) ?></td>
                                                            <td>
                                                                <span class="badge <?= $variant['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                                    <?= $variant['stock_quantity'] ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge <?= $variant['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                                                    <?= ucfirst($variant['status']) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Chưa có biến thể nào. <a href="/5s-fashion/admin/products/<?= $product['id'] ?>/variants">Tạo biến thể ngay</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
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
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Thư viện ảnh hiện tại (<?= count($galleryImages) ?> ảnh)</small>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllGalleryImages()" title="Xóa tất cả ảnh gallery">
                                            <i class="fas fa-trash"></i> Xóa tất cả
                                        </button>
                                    </div>
                                    <div class="row" id="currentGalleryContainer">
                                        <?php foreach ($galleryImages as $index => $image): ?>
                                            <div class="col-md-3 col-sm-4 col-6 mb-3" id="gallery-item-<?= $index ?>">
                                                <div class="gallery-item position-relative">
                                                    <?php
                                                    // Convert image path for serve-file.php
                                                    $imagePath = ltrim($image, '/');

                                                    // Remove 'uploads/' prefix if present
                                                    if (strpos($imagePath, 'uploads/') === 0) {
                                                        $imagePath = substr($imagePath, 8); // Remove 'uploads/'
                                                    }

                                                    $encodedPath = urlencode($imagePath);
                                                    ?>
                                                    <img src="/5s-fashion/serve-file.php?file=<?= $encodedPath ?>"
                                                         alt="Gallery Image <?= $index + 1 ?>"
                                                         class="img-thumbnail w-100"
                                                         style="height: 120px; object-fit: cover;">
                                                    <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                         style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s;">
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger me-2"
                                                                onclick="deleteGalleryImage(<?= $product['id'] ?>, <?= $index ?>)"
                                                                title="Xóa ảnh này">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-sm btn-primary"
                                                                onclick="viewGalleryImage('<?= htmlspecialchars($image) ?>')"
                                                                title="Xem ảnh">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    <div class="gallery-info mt-1">
                                                        <small class="text-muted d-block">Ảnh #<?= $index + 1 ?></small>
                                                        <small class="text-muted"><?= basename($image) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Chưa có ảnh gallery nào. Hãy upload ảnh bên dưới.
                                </div>
                            <?php endif; ?>

                            <div class="upload-section">
                                <label class="form-label">Thêm ảnh mới vào thư viện</label>
                                <div class="upload-area" id="galleryUpload">
                                    <input type="file" name="product_images[]" id="galleryImages" accept="image/*" multiple hidden>
                                    <div class="upload-content text-center p-4 border border-dashed rounded">
                                        <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                        <p class="mb-1">Kéo thả nhiều ảnh vào đây hoặc <button type="button" class="btn btn-link p-0">chọn files</button></p>
                                        <small class="text-muted">Có thể chọn nhiều file ảnh cùng lúc (JPG, PNG, WebP - Tối đa 5MB/ảnh)</small>
                                    </div>
                                </div>
                                <div id="galleryPreview" class="mt-3 row" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping & Attributes -->
                <!-- <div class="card mb-4">
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
                </div> -->

                <!-- SEO -->
                <!-- <div class="card mb-4">
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
                </div> -->
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
    // Handle variants toggle
    const hasVariantsCheckbox = document.getElementById('hasVariants');
    const simpleInventory = document.getElementById('simpleInventory');
    const variantManagement = document.getElementById('variantManagement');

    if (hasVariantsCheckbox) {
        hasVariantsCheckbox.addEventListener('change', function() {
            const saveFirstAlert = document.getElementById('saveFirstAlert');
            const afterSaveAlert = document.getElementById('afterSaveAlert');

            if (this.checked) {
                simpleInventory.style.display = 'none';
                variantManagement.style.display = 'block';

                // Show appropriate alert based on product state
                const productHasVariants = <?= !empty($product['has_variants']) ? 'true' : 'false' ?>;
                if (!productHasVariants) {
                    if (saveFirstAlert) saveFirstAlert.style.display = 'block';
                    if (afterSaveAlert) afterSaveAlert.style.display = 'none';
                }

                // Clear simple inventory values when switching to variants
                const stockQuantity = document.getElementById('stockQuantity');
                if (stockQuantity) stockQuantity.value = '';

                // Show save reminder notification
                showNotification('warning', 'Nhớ lưu sản phẩm để kích hoạt tính năng biến thể!');
            } else {
                simpleInventory.style.display = 'block';
                variantManagement.style.display = 'none';
            }
        });

        // Auto-enable manage_stock when has_variants is checked
        hasVariantsCheckbox.addEventListener('change', function() {
            const manageStockCheckbox = document.getElementById('manageStock');
            if (this.checked && manageStockCheckbox) {
                manageStockCheckbox.checked = true;
            }
        });
    }

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

    // Gallery hover effects
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        const overlay = item.querySelector('.gallery-overlay');
        item.addEventListener('mouseenter', () => {
            overlay.style.opacity = '1';
        });
        item.addEventListener('mouseleave', () => {
            overlay.style.opacity = '0';
        });
    });
});

// Gallery Management Functions
function deleteGalleryImage(productId, imageIndex) {
    if (!confirm('Bạn có chắc chắn muốn xóa ảnh này không?')) {
        return;
    }

    // Show loading state
    const galleryItem = document.getElementById(`gallery-item-${imageIndex}`);
    const originalContent = galleryItem.innerHTML;
    galleryItem.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang xóa...</div>';

    fetch('/5s-fashion/admin/products/deletegalleryimage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            productId: productId,
            imageIndex: imageIndex
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the gallery item with animation
            galleryItem.style.transition = 'all 0.3s ease';
            galleryItem.style.opacity = '0';
            galleryItem.style.transform = 'scale(0.8)';

            setTimeout(() => {
                galleryItem.remove();

                // Update gallery count and reindex remaining items
                updateGalleryDisplay(data.remainingImages);

                // Show success message
                showNotification('success', data.message);

                // Reload page after 2 seconds to refresh indexes
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }, 300);
        } else {
            // Restore original content on error
            galleryItem.innerHTML = originalContent;
            showNotification('error', 'Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        galleryItem.innerHTML = originalContent;
        showNotification('error', 'Có lỗi xảy ra khi xóa ảnh');
    });
}

function clearAllGalleryImages() {
    if (!confirm('Bạn có chắc chắn muốn xóa TẤT CẢ ảnh gallery không? Hành động này không thể hoàn tác!')) {
        return;
    }

    const productId = <?= $product['id'] ?>;
    const galleryContainer = document.getElementById('currentGalleryContainer');
    const galleryItems = galleryContainer.querySelectorAll('[id^="gallery-item-"]');

    if (galleryItems.length === 0) {
        showNotification('info', 'Không có ảnh nào để xóa');
        return;
    }

    // Show loading
    galleryContainer.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br><span class="mt-2 d-block">Đang xóa tất cả ảnh...</span></div>';

    // Delete all images by calling the API multiple times
    let deletedCount = 0;
    const totalImages = galleryItems.length;

    // Always delete index 0 since array gets reindexed after each delete
    function deleteNextImage() {
        if (deletedCount >= totalImages) {
            showNotification('success', `Đã xóa thành công ${totalImages} ảnh gallery`);
            setTimeout(() => window.location.reload(), 1500);
            return;
        }

        fetch('/5s-fashion/admin/products/deletegalleryimage', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                productId: productId,
                imageIndex: 0 // Always delete first image
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                deletedCount++;
                deleteNextImage(); // Delete next image
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', `Lỗi khi xóa ảnh thứ ${deletedCount + 1}: ${error.message}`);
            setTimeout(() => window.location.reload(), 2000);
        });
    }

    deleteNextImage();
}

function viewGalleryImage(imagePath) {
    // Convert image path for serve-file.php
    let cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;

    // Remove 'uploads/' prefix if present
    if (cleanPath.startsWith('uploads/')) {
        cleanPath = cleanPath.substring(8); // Remove 'uploads/'
    }

    const encodedPath = encodeURIComponent(cleanPath);
    const serveUrl = `/5s-fashion/serve-file.php?file=${encodedPath}`;

    // Create modal to view image
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xem ảnh gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${serveUrl}" class="img-fluid" alt="Gallery Image">
                    <div class="mt-2">
                        <small class="text-muted">${imagePath}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <a href="${serveUrl}" target="_blank" class="btn btn-primary">Mở trong tab mới</a>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Remove modal from DOM when hidden
    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

function updateGalleryDisplay(remainingCount) {
    const gallerySection = document.querySelector('.current-gallery .d-flex small');
    if (gallerySection) {
        gallerySection.textContent = `Thư viện ảnh hiện tại (${remainingCount} ảnh)`;
    }

    // If no images left, show the info alert
    if (remainingCount === 0) {
        const currentGallery = document.querySelector('.current-gallery');
        currentGallery.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Chưa có ảnh gallery nào. Hãy upload ảnh bên dưới.
            </div>
        `;
    }
}

function showNotification(type = 'info', message = '') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
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

/* Gallery Management Styles */
.gallery-item {
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.gallery-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.gallery-overlay {
    border-radius: 8px;
}

.gallery-overlay .btn {
    opacity: 0.9;
    transition: opacity 0.2s ease;
}

.gallery-overlay .btn:hover {
    opacity: 1;
}

.gallery-info {
    padding: 8px;
    background: #f8f9fa;
    font-size: 11px;
}

.gallery-info small {
    display: block;
    word-break: break-all;
}

.upload-section {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
    margin-top: 1rem;
}

.notification-toast {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
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
