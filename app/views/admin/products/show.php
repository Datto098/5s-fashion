<?php
$statusLabels = [
    'published' => 'Đã xuất bản',
    'draft' => 'Bản nháp',
    'out_of_stock' => 'Hết hàng',
    'active' => 'Hoạt động',
    'inactive' => 'Không hoạt động'
];

$statusClass = [
    'published' => 'success',
    'draft' => 'warning',
    'out_of_stock' => 'danger',
    'active' => 'success',
    'inactive' => 'secondary'
];
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><?= htmlspecialchars($product['name']) ?></h1>
                    <p class="text-muted mb-0">Mã sản phẩm: <?= htmlspecialchars($product['sku']) ?></p>
                </div>
                <div>
                    <a href="/zone-fashion/admin/products/edit/<?= $product['id'] ?>" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="/zone-fashion/admin/products" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images"></i> Hình ảnh sản phẩm
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['featured_image'])): ?>
                        <div class="product-featured-image mb-3">
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
                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 class="img-fluid rounded product-main-image">
                        </div>
                    <?php else: ?>
                        <div class="no-image-placeholder mb-3">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($galleryImages)): ?>
                        <div class="product-gallery">
                            <h6>Gallery</h6>
                            <div class="row">
                                <?php foreach ($galleryImages as $image): ?>
                                    <div class="col-4 mb-2">
                                        <?php
                                        // Handle gallery image path
                                        $imagePath = $image;
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
                                             alt=""
                                             class="img-fluid rounded gallery-thumb">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Thông tin cơ bản
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless product-info-table">
                        <tr>
                            <td width="30%" class="fw-bold">Tên sản phẩm:</td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Danh mục:</td>
                            <td><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Giá bán:</td>
                            <td class="text-success fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php if (!empty($product['compare_price'])): ?>
                        <tr>
                            <td class="fw-bold">Giá so sánh:</td>
                            <td class="text-muted"><del><?= number_format($product['compare_price'], 0, ',', '.') ?>đ</del></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($product['cost_price'])): ?>
                        <tr>
                            <td class="fw-bold">Giá vốn:</td>
                            <td><?= number_format($product['cost_price'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="fw-bold">Trạng thái:</td>
                            <td>
                                <span class="badge bg-<?= $statusClass[$product['status']] ?? 'secondary' ?>">
                                    <?= $statusLabels[$product['status']] ?? $product['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tồn kho:</td>
                            <td>
                                <?php
                                $baseStock = $product['stock_quantity'] ?? 0;
                                $variantStock = $product['total_variant_stock'] ?? 0;
                                $totalStock = $product['has_variants'] ? $variantStock : $baseStock;

                                if ($totalStock > 0): ?>
                                    <span class="text-success"><?= $totalStock ?> sản phẩm</span>
                                <?php else: ?>
                                    <span class="text-danger">Hết hàng</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!empty($product['weight'])): ?>
                        <tr>
                            <td class="fw-bold">Trọng lượng:</td>
                            <td><?= $product['weight'] ?>g</td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($product['dimensions'])): ?>
                        <tr>
                            <td class="fw-bold">Kích thước:</td>
                            <td><?= htmlspecialchars($product['dimensions']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="fw-bold">Ngày tạo:</td>
                            <td><?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Cập nhật cuối:</td>
                            <td><?= date('d/m/Y H:i', strtotime($product['updated_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <?php if (!empty($product['description']) || !empty($product['short_description'])): ?>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-align-left"></i> Mô tả sản phẩm
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['short_description'])): ?>
                    <div class="mb-3">
                        <h6>Mô tả ngắn:</h6>
                        <div class="text-muted"><?= nl2br(htmlspecialchars($product['short_description'])) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($product['description'])): ?>
                    <div>
                        <h6>Mô tả chi tiết:</h6>
                        <div><?= nl2br(htmlspecialchars($product['description'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Product Variants -->
    <?php if (!empty($variants)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Biến thể sản phẩm
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped variants-table">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Màu sắc</th>
                                    <th>Tồn kho</th>
                                    <th>Giá phụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($variants as $variant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($variant['size'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($variant['color']) && !empty($variant['color_code'])): ?>
                                            <span class="d-inline-flex align-items-center">
                                                <span class="color-circle me-2" style="background-color: <?= htmlspecialchars($variant['color_code']) ?>; width: 20px; height: 20px; border-radius: 50%; border: 1px solid #ddd;"></span>
                                                <?= htmlspecialchars($variant['color']) ?>
                                            </span>
                                        <?php elseif (!empty($variant['color'])): ?>
                                            <?= htmlspecialchars($variant['color']) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($variant['stock_quantity'] > 0): ?>
                                            <span class="text-success"><?= $variant['stock_quantity'] ?></span>
                                        <?php else: ?>
                                            <span class="text-danger">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($variant['additional_price'])): ?>
                                            <?php if ($variant['additional_price'] > 0): ?>
                                                +<?= number_format($variant['additional_price'], 0, ',', '.') ?>đ
                                            <?php elseif ($variant['additional_price'] < 0): ?>
                                                <?= number_format($variant['additional_price'], 0, ',', '.') ?>đ
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- SEO Information -->
    <?php if (!empty($product['meta_title']) || !empty($product['meta_description']) || !empty($product['meta_keywords'])): ?>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-search"></i> Thông tin SEO
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['meta_title'])): ?>
                    <div class="mb-3">
                        <strong>Meta Title:</strong>
                        <p class="text-muted"><?= htmlspecialchars($product['meta_title']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($product['meta_description'])): ?>
                    <div class="mb-3">
                        <strong>Meta Description:</strong>
                        <p class="text-muted"><?= htmlspecialchars($product['meta_description']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($product['meta_keywords'])): ?>
                    <div class="mb-3">
                        <strong>Meta Keywords:</strong>
                        <p class="text-muted"><?= htmlspecialchars($product['meta_keywords']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Image lightbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.querySelector('.product-main-image');
    const galleryThumbs = document.querySelectorAll('.gallery-thumb');

    // Main image click
    if (mainImage) {
        mainImage.addEventListener('click', function() {
            openImageModal(this.src);
        });
    }

    // Gallery thumbs click
    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            if (mainImage) {
                mainImage.src = this.src;
            }
            openImageModal(this.src);
        });
    });

    function openImageModal(imageSrc) {
        // Simple modal implementation
        const modal = document.createElement('div');
        modal.className = 'image-modal';

        const img = document.createElement('img');
        img.src = imageSrc;

        modal.appendChild(img);
        document.body.appendChild(modal);

        modal.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
    }
});
</script>
