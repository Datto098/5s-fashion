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
                                <?php
                                // Group attributes by type
                                $attributesByType = [];
                                $duplicateTypes = [];

                                foreach ($variant['attributes'] as $attr) {
                                    $type = $attr['attribute_type'] ?? 'other';

                                    // Check for duplicates
                                    if (isset($attributesByType[$type])) {
                                        $duplicateTypes[$type] = true;
                                    }

                                    $attributesByType[$type] = true;
                                }

                                $hasDuplicates = !empty($duplicateTypes);
                                ?>

                                <?php if ($hasDuplicates): ?>
                                    <div class="alert alert-warning mb-2 p-1" style="font-size: 0.8rem;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Lỗi: Nhiều giá trị cùng loại thuộc tính
                                    </div>
                                <?php endif; ?>

                                <?php foreach ($variant['attributes'] as $attr): ?>
                                    <?php
                                        $type = $attr['attribute_type'] ?? 'other';
                                        $badgeClass = 'bg-light text-dark';

                                        // Add warning if duplicate
                                        if (isset($duplicateTypes[$type])) {
                                            $badgeClass = 'bg-warning text-dark';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> me-1">
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
                            $statusClass = '';
                            $statusText = '';

                            switch ($variant['status']) {
                                case 'active':
                                    $statusClass = 'success';
                                    $statusText = 'Hoạt động';
                                    break;
                                case 'inactive':
                                    $statusClass = 'warning';
                                    $statusText = 'Tạm dừng';
                                    break;
                                case 'out_of_stock':
                                    $statusClass = 'danger';
                                    $statusText = 'Hết hàng';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <div class="d-flex">
                                <button type="button"
                                   class="btn btn-sm btn-primary me-1"
                                   onclick="editVariant(<?= $variant['id'] ?>, '<?= htmlspecialchars(addslashes($variant['variant_name'])) ?>', '<?= htmlspecialchars($variant['sku']) ?>', <?= $variant['price'] ?: 'null' ?>, <?= $variant['sale_price'] ?: 'null' ?>, <?= $variant['stock_quantity'] ?>, '<?= $variant['status'] ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="/5s-fashion/admin/products/<?= $product['id'] ?>/variants/<?= $variant['id'] ?>/delete"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa biến thể này?');" class="d-inline">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($hasDuplicates ?? false): ?>
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>
        Phát hiện biến thể có nhiều giá trị cùng loại thuộc tính (được đánh dấu màu vàng). <a href="/5s-fashion/admin/products/<?= $product['id'] ?>/variants/fix-duplicates" class="alert-link">Bấm vào đây</a> để sửa chữa.
    </div>
    <?php endif; ?>
<?php endif; ?>
