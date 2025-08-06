<?php
// Product Detail Page - Simple Clean Design
// Validate product data
if (!isset($product) || empty($product)) {
    header('HTTP/1.0 404 Not Found');
    echo "Product not found";
    exit;
}

// Prepare image URL
$imageUrl = '/5s-fashion/public/assets/images/default-product.jpg';
if (!empty($product['featured_image'])) {
    $imagePath = $product['featured_image'];
    if (strpos($imagePath, '/uploads/') === 0) {
        $cleanPath = substr($imagePath, 9);
    } elseif (strpos($imagePath, 'uploads/') === 0) {
        $cleanPath = substr($imagePath, 8);
    } else {
        $cleanPath = ltrim($imagePath, '/');
    }
    $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - 5S Fashion</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .header h1 {
            color: #dc3545;
            margin-bottom: 10px;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-weight: bold;
            color: #6c757d;
        }
        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 30px 0;
        }
        .product-image {
            text-align: center;
        }
        .product-image img {
            max-width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .product-info {
            padding: 20px 0;
        }
        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 15px;
        }
        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .original-price {
            font-size: 1.2rem;
            color: #6c757d;
            text-decoration: line-through;
            margin-left: 10px;
        }
        .product-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .product-meta h4 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .meta-item:last-child {
            border-bottom: none;
        }
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 30px 0;
        }
        .btn-custom {
            padding: 15px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-add-cart {
            background: #dc3545;
            color: white;
        }
        .btn-add-cart:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        .btn-buy-now {
            background: #28a745;
            color: white;
        }
        .btn-buy-now:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        .product-description {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
        }
        .product-description h4 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .variants-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .variant-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navigation {
            text-align: center;
            margin: 30px 0;
        }
        .nav-link {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            margin: 0 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }
        .nav-link.primary {
            background: #dc3545;
        }
        .nav-link.primary:hover {
            background: #c82333;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            text-align: center;
        }
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .action-buttons {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 20px;
            }
            .product-title {
                font-size: 1.5rem;
            }
            .product-price {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ 5S Fashion</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/shop">Cửa hàng</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
                </ol>
            </nav>
        </div>

        <div class="product-grid">
            <!-- Product Image -->
            <div class="product-image">
                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="productImage">
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <h2 class="product-title"><?= htmlspecialchars($product['name']) ?></h2>

                <div class="product-price">
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <?= number_format($product['sale_price'], 0, ',', '.') ?>₫
                        <span class="original-price"><?= number_format($product['price'], 0, ',', '.') ?>₫</span>
                        <span class="badge bg-danger ms-2">
                            -<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>%
                        </span>
                    <?php else: ?>
                        <?= number_format($product['price'], 0, ',', '.') ?>₫
                    <?php endif; ?>
                </div>

                <div class="product-meta">
                    <h4>📋 Thông tin sản phẩm</h4>
                    <div class="meta-item">
                        <strong>Mã sản phẩm:</strong>
                        <span><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span>
                    </div>
                    <div class="meta-item">
                        <strong>Danh mục:</strong>
                        <span><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></span>
                    </div>
                    <div class="meta-item">
                        <strong>Tình trạng:</strong>
                        <span class="<?= ($product['stock_quantity'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($product['stock_quantity'] ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <strong>Số lượng:</strong>
                        <span><?= $product['stock_quantity'] ?? 0 ?> sản phẩm</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-custom btn-add-cart" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                    </button>
                    <button class="btn-custom btn-buy-now" onclick="buyNow(<?= $product['id'] ?>)">
                        <i class="fas fa-bolt me-2"></i>Mua ngay
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <?php if (!empty($product['description'])): ?>
        <div class="product-description">
            <h4>📝 Mô tả sản phẩm</h4>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Product Variants -->
        <?php if (!empty($variants) && is_array($variants)): ?>
        <div class="variants-section">
            <h4>🎨 Các phiên bản sản phẩm</h4>
            <p><strong>Sản phẩm này có <?= count($variants) ?> phiên bản khác nhau:</strong></p>
            <?php foreach ($variants as $variant): ?>
            <div class="variant-item">
                <div>
                    <strong><?= htmlspecialchars($variant['variant_name'] ?? 'Phiên bản') ?></strong>
                    <?php if (!empty($variant['color'])): ?>
                        <span class="badge bg-secondary ms-2"><?= htmlspecialchars($variant['color']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($variant['size'])): ?>
                        <span class="badge bg-info ms-1"><?= htmlspecialchars($variant['size']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong class="text-success"><?= number_format($variant['price'] ?? $product['price'], 0, ',', '.') ?>₫</strong>
                    <small class="text-muted ms-2">Còn: <?= $variant['stock_quantity'] ?? 0 ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Navigation -->
        <div class="navigation">
            <a href="/" class="nav-link">🏠 Trang chủ</a>
            <a href="/shop" class="nav-link">🛒 Cửa hàng</a>
            <a href="/cart" class="nav-link primary">🛍️ Giỏ hàng</a>
        </div>

        <div class="footer">
            <p>&copy; 2025 5S Fashion. Phát triển bởi AI Assistant</p>
            <p>Framework: PHP MVC | Database: MySQL | Theme: Red-White-Gray</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Simple cart functionality
        function addToCart(productId) {
            console.log('Adding product', productId, 'to cart');

            // Simple alert for now
            alert('Đã thêm sản phẩm vào giỏ hàng!\n\n' +
                  'Sản phẩm: <?= htmlspecialchars($product['name']) ?>\n' +
                  'Giá: <?= number_format($product['sale_price'] ?? $product['price'], 0, ',', '.') ?>₫');

            // TODO: Implement actual cart functionality
        }

        function buyNow(productId) {
            console.log('Buy now product', productId);

            // Simple alert for now
            alert('Chức năng mua ngay!\n\n' +
                  'Sản phẩm: <?= htmlspecialchars($product['name']) ?>\n' +
                  'Giá: <?= number_format($product['sale_price'] ?? $product['price'], 0, ',', '.') ?>₫\n\n' +
                  'Đang chuyển đến trang thanh toán...');

            // TODO: Implement actual checkout functionality
        }

        // Image click to zoom
        document.getElementById('productImage').addEventListener('click', function() {
            this.style.transform = this.style.transform === 'scale(1.5)' ? 'scale(1)' : 'scale(1.5)';
            this.style.transition = 'transform 0.3s ease';
        });
    </script>
</body>
</html>
