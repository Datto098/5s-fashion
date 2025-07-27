<?php
// Create product view
?>

<!-- Page Header -->
<div class="admin-header">
    <div class="admin-header-content">
        <h1 class="admin-title">
            <i class="fas fa-plus-circle"></i>
            Thêm sản phẩm mới
        </h1>
        <div class="admin-breadcrumb">
            Tạo sản phẩm mới trong hệ thống
        </div>
    </div>
    <div class="admin-header-actions">
        <a href="/5s-fashion/admin/products" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Quay lại
        </a>
    </div>
</div>

<!-- Product Form -->
<form method="POST" action="/5s-fashion/admin/products/store" enctype="multipart/form-data" id="productForm">
    <div class="form-container">
        <div class="form-grid-main">
            <!-- Left Column - Main Information -->
            <div class="form-column">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Thông tin cơ bản</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Tên sản phẩm <span class="required">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-input" required
                                   placeholder="Nhập tên sản phẩm..." value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="slug">
                                Slug (URL thân thiện)
                            </label>
                            <input type="text" name="slug" id="slug" class="form-input"
                                   placeholder="Tự động tạo từ tên sản phẩm..." value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                            <small class="form-help">Để trống để tự động tạo từ tên sản phẩm</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="short_description">
                                Mô tả ngắn
                            </label>
                            <textarea name="short_description" id="short_description" class="form-textarea" rows="3"
                                      placeholder="Mô tả ngắn gọn về sản phẩm..."><?= htmlspecialchars($_POST['short_description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">
                                Mô tả chi tiết
                            </label>
                            <textarea name="description" id="description" class="form-textarea" rows="8"
                                      placeholder="Mô tả chi tiết về sản phẩm, tính năng, ưu điểm..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Giá cả</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="price">
                                    Giá gốc <span class="required">*</span>
                                </label>
                                <input type="number" name="price" id="price" class="form-input" required min="0" step="1000"
                                       placeholder="0" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="sale_price">
                                    Giá khuyến mãi
                                </label>
                                <input type="number" name="sale_price" id="sale_price" class="form-input" min="0" step="1000"
                                       placeholder="0" value="<?= htmlspecialchars($_POST['sale_price'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cost_price">
                                Giá vốn (để tính lợi nhuận)
                            </label>
                            <input type="number" name="cost_price" id="cost_price" class="form-input" min="0" step="1000"
                                   placeholder="0" value="<?= htmlspecialchars($_POST['cost_price'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Hình ảnh</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label class="form-label" for="featured_image">
                                Hình ảnh đại diện
                            </label>
                            <div class="image-upload-area" id="featuredImageArea">
                                <input type="file" name="featured_image" id="featured_image" class="form-file" accept="image/*">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Kéo thả hoặc click để chọn hình ảnh</p>
                                    <small>JPG, PNG, WebP. Tối đa 5MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="gallery_images">
                                Thư viện ảnh
                            </label>
                            <div class="image-upload-area" id="galleryImageArea">
                                <input type="file" name="gallery_images[]" id="gallery_images" class="form-file" accept="image/*" multiple>
                                <div class="upload-placeholder">
                                    <i class="fas fa-images"></i>
                                    <p>Chọn nhiều hình ảnh</p>
                                    <small>Có thể chọn nhiều file cùng lúc</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Meta & Settings -->
            <div class="form-sidebar">
                <!-- Status & Visibility -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Xuất bản</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label class="form-label" for="status">
                                Trạng thái
                            </label>
                            <select name="status" id="status" class="form-select">
                                <option value="draft" <?= ($_POST['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                                <option value="published" <?= ($_POST['status'] ?? '') === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-checkbox">
                                <input type="checkbox" name="featured" value="1" <?= !empty($_POST['featured']) ? 'checked' : '' ?>>
                                <span class="checkbox-indicator"></span>
                                <span class="checkbox-label">Sản phẩm nổi bật</span>
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="action" value="save_draft" class="btn btn-secondary btn-block">
                                <i class="fas fa-save"></i>
                                Lưu nháp
                            </button>
                            <button type="submit" name="action" value="publish" class="btn btn-primary btn-block">
                                <i class="fas fa-check"></i>
                                Xuất bản ngay
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Danh mục</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label class="form-label" for="category_id">
                                Danh mục chính <span class="required">*</span>
                            </label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Chọn danh mục...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tags">
                                Tags
                            </label>
                            <input type="text" name="tags" id="tags" class="form-input"
                                   placeholder="Nhập tags, cách nhau bởi dấu phẩy..." value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>">
                            <small class="form-help">Ví dụ: áo thun, cotton, nam</small>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Chi tiết sản phẩm</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label class="form-label" for="sku">
                                Mã SKU
                            </label>
                            <input type="text" name="sku" id="sku" class="form-input"
                                   placeholder="Tự động tạo nếu để trống..." value="<?= htmlspecialchars($_POST['sku'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="brand">
                                Thương hiệu
                            </label>
                            <input type="text" name="brand" id="brand" class="form-input"
                                   placeholder="Tên thương hiệu..." value="<?= htmlspecialchars($_POST['brand'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="weight">
                                Trọng lượng (g)
                            </label>
                            <input type="number" name="weight" id="weight" class="form-input" min="0" step="1"
                                   placeholder="0" value="<?= htmlspecialchars($_POST['weight'] ?? '') ?>">
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="length">Dài (cm)</label>
                                <input type="number" name="length" id="length" class="form-input" min="0" step="0.1"
                                       placeholder="0" value="<?= htmlspecialchars($_POST['length'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="width">Rộng (cm)</label>
                                <input type="number" name="width" id="width" class="form-input" min="0" step="0.1"
                                       placeholder="0" value="<?= htmlspecialchars($_POST['width'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="height">Cao (cm)</label>
                                <input type="number" name="height" id="height" class="form-input" min="0" step="0.1"
                                       placeholder="0" value="<?= htmlspecialchars($_POST['height'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">SEO</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label class="form-label" for="meta_title">
                                Meta Title
                            </label>
                            <input type="text" name="meta_title" id="meta_title" class="form-input" maxlength="60"
                                   placeholder="Tiêu đề SEO..." value="<?= htmlspecialchars($_POST['meta_title'] ?? '') ?>">
                            <small class="form-help">Tối đa 60 ký tự</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="meta_description">
                                Meta Description
                            </label>
                            <textarea name="meta_description" id="meta_description" class="form-textarea" rows="3" maxlength="160"
                                      placeholder="Mô tả SEO..."><?= htmlspecialchars($_POST['meta_description'] ?? '') ?></textarea>
                            <small class="form-help">Tối đa 160 ký tự</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.auto !== 'false') {
            slugInput.value = generateSlug(this.value);
            slugInput.dataset.auto = 'true';
        }
    });

    slugInput.addEventListener('input', function() {
        this.dataset.auto = 'false';
    });

    function generateSlug(text) {
        return text
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
    }

    // Auto generate SKU
    const categorySelect = document.getElementById('category_id');
    const skuInput = document.getElementById('sku');

    function generateSKU() {
        if (!skuInput.value || skuInput.dataset.auto !== 'false') {
            const categoryCode = categorySelect.options[categorySelect.selectedIndex].text.substring(0, 3).toUpperCase();
            const timestamp = Date.now().toString().slice(-6);
            skuInput.value = categoryCode + timestamp;
            skuInput.dataset.auto = 'true';
        }
    }

    categorySelect.addEventListener('change', generateSKU);
    nameInput.addEventListener('input', generateSKU);

    skuInput.addEventListener('input', function() {
        this.dataset.auto = 'false';
    });

    // Image upload preview
    function setupImagePreview(input, area) {
        // Add click handler for the upload area
        area.addEventListener('click', function(e) {
            // Don't trigger if clicking on an existing image
            if (!e.target.classList.contains('preview-image')) {
                input.click();
            }
        });

        // Add drag and drop functionality
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                // Set files to input element
                input.files = files;
                // Trigger change event
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
        });

        // Handle file selection
        input.addEventListener('change', function() {
            const files = this.files;
            if (files.length > 0) {
                // Clear previous preview
                const existingPreview = area.querySelector('.image-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }

                const preview = document.createElement('div');
                preview.className = 'image-preview';

                for (let file of files) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-image';
                            img.title = file.name;
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                }

                const placeholder = area.querySelector('.upload-placeholder');
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
                area.appendChild(preview);
            }
        });
    }

    setupImagePreview(document.getElementById('featured_image'), document.getElementById('featuredImageArea'));
    setupImagePreview(document.getElementById('gallery_images'), document.getElementById('galleryImageArea'));

    // Custom checkbox functionality
    document.querySelectorAll('.form-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('click', function(e) {
            // Prevent double-click on label
            if (e.target.className === 'checkbox-label' || e.target.className === 'checkbox-indicator') {
                e.preventDefault();
                const input = this.querySelector('input[type="checkbox"]');
                input.checked = !input.checked;

                // Trigger change event
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
        });
    });

    // Form validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const requiredFields = ['name', 'category_id', 'price'];
        let isValid = true;

        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ các trường bắt buộc!');
        }
    });

    // Price validation
    const priceInput = document.getElementById('price');
    const salePriceInput = document.getElementById('sale_price');

    salePriceInput.addEventListener('input', function() {
        const price = parseFloat(priceInput.value) || 0;
        const salePrice = parseFloat(this.value) || 0;

        if (salePrice > price && price > 0) {
            this.setCustomValidity('Giá khuyến mãi không thể lớn hơn giá gốc');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
