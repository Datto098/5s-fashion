<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><?= htmlspecialchars($category['name']) ?></h1>
                    <p class="text-muted mb-0">Chi tiết danh mục sản phẩm</p>
                </div>
                <div>
                    <a href="/zone-fashion/admin/categories/edit/<?= $category['id'] ?>" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="/zone-fashion/admin/categories" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Category Details -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Thông tin cơ bản
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless product-info-table">
                        <tr>
                            <td width="25%" class="fw-bold">Tên danh mục:</td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Slug:</td>
                            <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Danh mục cha:</td>
                            <td>
                                <?php if (!empty($category['parent_name'])): ?>
                                    <?= htmlspecialchars($category['parent_name']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Danh mục gốc</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Trạng thái:</td>
                            <td>
                                <span class="badge bg-<?= $category['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= $category['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Thứ tự:</td>
                            <td><?= $category['sort_order'] ?? 0 ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Ngày tạo:</td>
                            <td><?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Cập nhật cuối:</td>
                            <td><?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($category['description'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-align-left"></i> Mô tả
                    </h5>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($category['description'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Subcategories -->
            <?php if (!empty($subcategories)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap"></i> Danh mục con (<?= count($subcategories) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Slug</th>
                                    <th>Trạng thái</th>
                                    <th>Thứ tự</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subcategories as $sub): ?>
                                <tr>
                                    <td>
                                        <a href="/zone-fashion/admin/categories/show/<?= $sub['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($sub['name']) ?>
                                        </a>
                                    </td>
                                    <td><code><?= htmlspecialchars($sub['slug']) ?></code></td>
                                    <td>
                                        <span class="badge bg-<?= $sub['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= $sub['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động' ?>
                                        </span>
                                    </td>
                                    <td><?= $sub['sort_order'] ?? 0 ?></td>
                                    <td>
                                        <a href="/zone-fashion/admin/categories/edit/<?= $sub['id'] ?>" class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Products in Category -->
            <?php if (!empty($products)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box"></i> Sản phẩm trong danh mục
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <?php if (!empty($product['featured_image'])): ?>
                                    <?php
                                    // Handle image path for file server
                                    $imagePath = $product['featured_image'];
                                    if (strpos($imagePath, '/uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 9);
                                    } elseif (strpos($imagePath, 'uploads/') === 0) {
                                        $cleanPath = substr($imagePath, 8);
                                    } else {
                                        $cleanPath = ltrim($imagePath, '/');
                                    }
                                    $imageUrl = '/zone-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                    ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>"
                                         class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>"
                                         style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="/zone-fashion/admin/products/show/<?= $product['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h6>
                                    <p class="card-text text-success fw-bold">
                                        <?= number_format($product['price'], 0, ',', '.') ?>đ
                                    </p>
                                    <small class="text-muted">SKU: <?= htmlspecialchars($product['sku']) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/zone-fashion/admin/products?category_id=<?= $category['id'] ?>" class="btn btn-outline-primary">
                            Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SEO Information -->
            <?php if (!empty($category['meta_title']) || !empty($category['meta_description'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-search"></i> Thông tin SEO
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($category['meta_title'])): ?>
                    <div class="mb-3">
                        <strong>Meta Title:</strong>
                        <p class="text-muted"><?= htmlspecialchars($category['meta_title']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($category['meta_description'])): ?>
                    <div class="mb-3">
                        <strong>Meta Description:</strong>
                        <p class="text-muted"><?= htmlspecialchars($category['meta_description']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Category Image -->
            <?php if (!empty($category['image'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image"></i> Hình ảnh danh mục
                    </h5>
                </div>
                <div class="card-body text-center">
                    <img src="/zone-fashion/public<?= htmlspecialchars($category['image']) ?>"
                         alt="<?= htmlspecialchars($category['name']) ?>"
                         class="img-fluid rounded" style="max-height: 300px;">
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Thống kê
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Danh mục con:</span>
                        <span class="fw-bold"><?= count($subcategories) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Sản phẩm:</span>
                        <span class="fw-bold"><?= count($products) ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/zone-fashion/admin/categories/edit/<?= $category['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="/zone-fashion/admin/products?category_id=<?= $category['id'] ?>" class="btn btn-info">
                            <i class="fas fa-box"></i> Xem sản phẩm
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')">
                            <i class="fas fa-trash"></i> Xóa danh mục
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteCategory(id, name) {
    if (confirm(`Bạn có chắc chắn muốn xóa danh mục "${name}"?\n\nTất cả sản phẩm trong danh mục này sẽ chuyển về "Chưa phân loại".\n\nHành động này không thể hoàn tác!`)) {
        fetch(`/zone-fashion/admin/categories/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã xóa danh mục thành công!');
                    window.location.href = '/zone-fashion/admin/categories';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa danh mục!');
            });
    }
}
</script>
