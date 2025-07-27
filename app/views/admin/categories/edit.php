<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Chỉnh sửa danh mục</h1>
                    <p class="text-muted mb-0">Cập nhật thông tin danh mục: <?= htmlspecialchars($category['name']) ?></p>
                </div>
                <div>
                    <a href="/5s-fashion/admin/categories" class="btn btn-secondary">
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

    <form action="/5s-fashion/admin/categories/update/<?= $category['id'] ?>" method="POST" enctype="multipart/form-data" id="categoryForm">
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
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="categoryName" name="name"
                                   value="<?= htmlspecialchars($category['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="categorySlug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="categorySlug" name="slug"
                                   value="<?= htmlspecialchars($category['slug']) ?>" readonly>
                            <small class="text-muted">Slug được tạo tự động từ tên danh mục</small>
                        </div>

                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image"></i> Hình ảnh danh mục
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($category['image'])): ?>
                            <div class="current-image mb-3">
                                <img src="/5s-fashion/public<?= htmlspecialchars($category['image']) ?>"
                                     alt="Current image" class="img-thumbnail" style="max-height: 200px;">
                                <p class="text-muted small mt-1">Ảnh hiện tại</p>
                            </div>
                        <?php endif; ?>

                        <div class="upload-area" id="imageUpload">
                            <input type="file" name="image" id="categoryImage" accept="image/*" hidden>
                            <div class="upload-content text-center p-4 border border-dashed rounded">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1">Kéo thả ảnh vào đây hoặc <button type="button" class="btn btn-link p-0">chọn file mới</button></p>
                                <small class="text-muted">Chỉ chấp nhận file ảnh (JPG, PNG, GIF) tối đa 5MB</small>
                            </div>
                        </div>
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="imagePreviewImg" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search"></i> Tối ưu hóa SEO
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="metaTitle" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="metaTitle" name="meta_title"
                                   value="<?= htmlspecialchars($category['meta_title'] ?? '') ?>" maxlength="60">
                            <small class="text-muted">Tối đa 60 ký tự</small>
                        </div>
                        <div class="mb-3">
                            <label for="metaDescription" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="metaDescription" name="meta_description" rows="3" maxlength="160"><?= htmlspecialchars($category['meta_description'] ?? '') ?></textarea>
                            <small class="text-muted">Tối đa 160 ký tự</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status & Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Cài đặt
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="parentCategory" class="form-label">Danh mục cha</label>
                            <select class="form-select" id="parentCategory" name="parent_id">
                                <option value="">Danh mục gốc</option>
                                <?php foreach ($parentCategories as $parent): ?>
                                    <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] == $parent['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($parent['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sortOrder" class="form-label">Thứ tự sắp xếp</label>
                            <input type="number" class="form-control" id="sortOrder" name="sort_order"
                                   value="<?= $category['sort_order'] ?? 0 ?>" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="categoryStatus" class="form-label">Trạng thái</label>
                            <select class="form-select" id="categoryStatus" name="status">
                                <option value="active" <?= ($category['status'] === 'active') ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="inactive" <?= ($category['status'] === 'inactive') ? 'selected' : '' ?>>Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật danh mục
                            </button>
                            <a href="/5s-fashion/admin/categories/show/<?= $category['id'] ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <a href="/5s-fashion/admin/categories" class="btn btn-secondary">
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
    // Auto-generate slug from name
    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');

    nameInput.addEventListener('input', function() {
        const slug = createSlug(this.value);
        slugInput.value = slug;
    });

    function createSlug(text) {
        return text
            .toLowerCase()
            .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
            .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
            .replace(/[ìíịỉĩ]/g, 'i')
            .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
            .replace(/[ùúụủũưừứựửữ]/g, 'u')
            .replace(/[ỳýỵỷỹ]/g, 'y')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
    }

    // Image Upload
    const imageUpload = document.getElementById('imageUpload');
    const imageInput = document.getElementById('categoryImage');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewImg = document.getElementById('imagePreviewImg');

    imageUpload.addEventListener('click', () => imageInput.click());
    imageUpload.addEventListener('dragover', handleDragOver);
    imageUpload.addEventListener('drop', handleDrop);

    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            previewImage(this.files[0]);
        }
    });

    function handleDrop(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            previewImage(files[0]);
        }
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.style.borderColor = '#007bff';
    }

    function previewImage(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreviewImg.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Form validation
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        const name = document.getElementById('categoryName').value.trim();

        if (!name) {
            e.preventDefault();
            alert('Vui lòng nhập tên danh mục!');
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

.current-image img {
    border: 2px solid #dee2e6;
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
</style>
