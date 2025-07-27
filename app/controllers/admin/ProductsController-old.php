<?php
/**
 * Professional Products Controller with Sidebar
 * Business-grade product management interface
 */

class ProductsController extends BaseController
{
    public function __construct()
    {
        // Session đã được start từ index.php

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin/login');
            exit;
        }
    }    public function index()
    {
        $this->ensureSessionStarted();

        // Sample products data (in real app, get from database)
        $deletedProducts = $_SESSION['deleted_products'] ?? [];

        $products = [
            [
                'id' => 1,
                'name' => 'Áo thun nam cổ tròn',
                'sku' => 'AT001',
                'category' => 'Áo thun nam',
                'current_price' => 299000,
                'old_price' => 399000,
                'stock' => 25,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'image' => 'https://via.placeholder.com/60x60'
            ],
            [
                'id' => 2,
                'name' => 'Áo sơ mi công sở',
                'sku' => 'SM001',
                'category' => 'Áo sơ mi',
                'current_price' => 450000,
                'stock' => 18,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'image' => 'https://via.placeholder.com/60x60'
            ],
            [
                'id' => 3,
                'name' => 'Quần jean slim fit',
                'sku' => 'QJ001',
                'category' => 'Quần jean',
                'current_price' => 699000,
                'old_price' => 899000,
                'stock' => 12,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'image' => 'https://via.placeholder.com/60x60'
            ],
            [
                'id' => 4,
                'name' => 'Đầm dự tiệc sang trọng',
                'sku' => 'DD001',
                'category' => 'Đầm nữ',
                'current_price' => 1200000,
                'stock' => 8,
                'status' => 'active',
                'status_text' => 'Hoạt động',
                'image' => 'https://via.placeholder.com/60x60'
            ]
        ];

        // Filter out deleted products
        $products = array_filter($products, function($product) use ($deletedProducts) {
            return !in_array($product['id'], $deletedProducts);
        });

        $data = [
            'title' => 'Quản lý sản phẩm - 5S Fashion Admin',
            'products' => $products,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                ['title' => 'Sản phẩm']
            ],
            'additionalJS' => $this->getProductsIndexJS($deletedProducts)
        ];

        $this->render('admin/products/index', $data, 'admin/layouts/main-inline');
    }

    private function getProductsIndexJS($deletedProducts)
    {
        return '
        <script>
            // Product management JavaScript
            document.addEventListener("DOMContentLoaded", function() {
                // Delete product functionality
                window.deleteProduct = function(productId, productName) {
                    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm: " + productName + "?")) {
                        fetch("/5s-fashion/admin/products/delete/" + productId, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                                if (row) row.remove();
                                alert("Đã xóa sản phẩm thành công!");
                            } else {
                                alert("Lỗi: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Có lỗi xảy ra khi xóa sản phẩm!");
                        });
                    }
                };

                // View product functionality
                window.viewProduct = function(id) {
                    window.location.href = "/5s-fashion/admin/products/view/" + id;
                };

                // Edit product functionality
                window.editProduct = function(id) {
                    window.location.href = "/5s-fashion/admin/products/edit/" + id;
                };

                // Hide deleted products
                const deletedProducts = ' . json_encode($deletedProducts) . ';
                deletedProducts.forEach(productId => {
                    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                    if (row) {
                        row.style.display = "none";
                    }
                });
            });
        </script>
        ';
    }

    public function create()
    {
        $data = [
            'title' => 'Thêm sản phẩm mới - 5S Fashion Admin',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                ['title' => 'Sản phẩm', 'url' => '/5s-fashion/admin/products'],
                ['title' => 'Thêm sản phẩm']
            ]
        ];

        $this->render('admin/products/create', $data, 'admin/layouts/main-inline');
    }

    public function store()
    {
        $this->ensureSessionStarted();

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $data = [
                'product_name' => $_POST['product_name'] ?? '',
                'product_sku' => $_POST['product_sku'] ?? '',
                'regular_price' => floatval($_POST['regular_price'] ?? 0),
                'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate required fields
            $errors = [];
            if (empty($data['product_name'])) {
                $errors[] = 'Tên sản phẩm không được để trống';
            }
            if (empty($data['product_sku'])) {
                $errors[] = 'Mã SKU không được để trống';
            }
            if ($data['regular_price'] <= 0) {
                $errors[] = 'Giá gốc phải lớn hơn 0';
            }
            if ($data['category_id'] <= 0) {
                $errors[] = 'Vui lòng chọn danh mục';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
                $this->redirect('/5s-fashion/admin/products/create');
            }

            // Save product (simulate)
            $_SESSION['success_message'] = 'Đã thêm sản phẩm thành công!';
            $this->redirect('/5s-fashion/admin/products');
        }

        $this->redirect('/5s-fashion/admin/products/create');
    }

    public function edit($id)
    {
        $this->ensureSessionStarted();

        // Get messages from session
        $successMessage = $_SESSION['success_message'] ?? '';
        $errorMessage = $_SESSION['error_message'] ?? '';
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        // Clear messages from session
        unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['errors'], $_SESSION['old_data']);

        // Default product data (in real app, fetch from database)
        $productData = [
            'product_name' => $oldData['product_name'] ?? 'Áo thun nam cổ tròn premium',
            'product_slug' => $oldData['product_slug'] ?? 'ao-thun-nam-co-tron-premium',
            'product_sku' => $oldData['product_sku'] ?? 'AT001',
            'barcode' => $oldData['barcode'] ?? '1234567890123',
            'short_description' => $oldData['short_description'] ?? 'Áo thun nam cổ tròn từ cotton cao cấp, form regular fit thoải mái',
            'description' => $oldData['description'] ?? 'Áo thun nam cổ tròn được làm từ chất liệu cotton cao cấp, mềm mại và thoáng khí. Thiết kế đơn giản nhưng tinh tế, phù hợp cho mọi hoạt động hàng ngày.',
            'regular_price' => $oldData['regular_price'] ?? 399000,
            'sale_price' => $oldData['sale_price'] ?? 299000,
            'cost_price' => $oldData['cost_price'] ?? 150000,
            'stock_quantity' => $oldData['stock_quantity'] ?? 25,
            'low_stock_threshold' => $oldData['low_stock_threshold'] ?? 10,
            'weight' => $oldData['weight'] ?? 250,
            'status' => $oldData['status'] ?? 'active',
            'is_featured' => $oldData['is_featured'] ?? 1,
            'track_inventory' => $oldData['track_inventory'] ?? 1,
            'category_id' => $oldData['category_id'] ?? 1,
            'brand_id' => $oldData['brand_id'] ?? 5,
            'vendor' => $oldData['vendor'] ?? 'Công ty TNHH 5S Fashion',
            'meta_title' => $oldData['meta_title'] ?? 'Áo thun nam cổ tròn premium - Chất liệu cotton cao cấp',
            'meta_description' => $oldData['meta_description'] ?? 'Áo thun nam cổ tròn được làm từ cotton 100% cao cấp, form regular fit thoải mái. Giá ưu đãi chỉ 299k. Giao hàng miễn phí toàn quốc.',
            'meta_keywords' => $oldData['meta_keywords'] ?? 'áo thun nam, cotton cao cấp, form regular, giá rẻ',
            'has_variants' => $oldData['has_variants'] ?? 1,
            'sizes' => $oldData['sizes'] ?? ['S', 'M', 'L', 'XL'],
            'colors' => $oldData['colors'] ?? ['black', 'white', 'blue']
        ];

        $data = [
            'title' => 'Chỉnh sửa sản phẩm - 5S Fashion Admin',
            'id' => $id,
            'productData' => $productData,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'errors' => $errors,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                ['title' => 'Sản phẩm', 'url' => '/5s-fashion/admin/products'],
                ['title' => 'Chỉnh sửa sản phẩm']
            ]
        ];

        $this->render('admin/products/edit', $data, 'admin/layouts/main-inline');
    }

    public function viewProduct($id)
    {
        $data = [
            'title' => 'Chi tiết sản phẩm - 5S Fashion Admin',
            'id' => $id,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                ['title' => 'Sản phẩm', 'url' => '/5s-fashion/admin/products'],
                ['title' => 'Chi tiết sản phẩm']
            ]
        ];

        $this->render('admin/products/view', $data, 'admin/layouts/main-inline');
    }

    public function update($id)
    {
        $this->ensureSessionStarted();

        error_log("Update method called for product ID: $id");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));

        // Check if request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/5s-fashion/admin/products/edit/' . $id);
        }

        // Get form data
        $data = [
            'product_name' => $_POST['product_name'] ?? '',
            'product_slug' => $_POST['product_slug'] ?? '',
            'product_sku' => $_POST['product_sku'] ?? '',
            'barcode' => $_POST['barcode'] ?? '',
            'short_description' => $_POST['short_description'] ?? '',
            'description' => $_POST['description'] ?? '',
            'regular_price' => floatval($_POST['regular_price'] ?? 0),
            'sale_price' => floatval($_POST['sale_price'] ?? 0),
            'cost_price' => floatval($_POST['cost_price'] ?? 0),
            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
            'low_stock_threshold' => intval($_POST['low_stock_threshold'] ?? 0),
            'weight' => floatval($_POST['weight'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'track_inventory' => isset($_POST['track_inventory']) ? 1 : 0,
            'category_id' => intval($_POST['category_id'] ?? 0),
            'brand_id' => intval($_POST['brand_id'] ?? 0),
            'vendor' => $_POST['vendor'] ?? '',
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'has_variants' => isset($_POST['has_variants']) ? 1 : 0,
            'sizes' => $_POST['sizes'] ?? [],
            'colors' => $_POST['colors'] ?? [],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Validate required fields
        $errors = [];
        if (empty($data['product_name'])) {
            $errors[] = 'Tên sản phẩm không được để trống';
        }
        if (empty($data['product_sku'])) {
            $errors[] = 'Mã SKU không được để trống';
        }
        if ($data['regular_price'] <= 0) {
            $errors[] = 'Giá gốc phải lớn hơn 0';
        }
        if ($data['stock_quantity'] < 0) {
            $errors[] = 'Số lượng tồn kho không được âm';
        }
        if ($data['category_id'] <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        // If there are errors, redirect back with errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('/5s-fashion/admin/products/edit/' . $id);
        }

        // Handle image uploads
        $uploadedImages = [];
        error_log("FILES data: " . print_r($_FILES, true));

        if (!empty($_FILES['new_images']['name'][0])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/5s-fashion/public/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
                error_log("Created upload directory: " . $uploadDir);
            }

            foreach ($_FILES['new_images']['name'] as $key => $name) {
                error_log("Processing file $key: $name, Error: " . $_FILES['new_images']['error'][$key]);

                if ($_FILES['new_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $filename = 'product_' . $id . '_' . uniqid() . '.' . $extension;
                    $uploadPath = $uploadDir . $filename;

                    error_log("Attempting to move file from " . $_FILES['new_images']['tmp_name'][$key] . " to " . $uploadPath);

                    if (move_uploaded_file($_FILES['new_images']['tmp_name'][$key], $uploadPath)) {
                        $uploadedImages[] = '/5s-fashion/public/uploads/products/' . $filename;
                        error_log("Successfully uploaded: " . $filename);
                    } else {
                        error_log("Failed to move uploaded file: " . $filename);
                    }
                } else {
                    error_log("File upload error for $name: " . $_FILES['new_images']['error'][$key]);
                }
            }
        } else {
            error_log("No files uploaded or empty file array");
        }

        // Simulate database update (in real app, use database)
        try {
            // Here you would update the database
            // $db = Database::getInstance();
            // $db->update('products', $data, ['id' => $id]);

            // For demo, we'll just simulate success
            $updateSuccess = true;

            if ($updateSuccess) {
                $message = 'Cập nhật sản phẩm thành công!';
                if (!empty($uploadedImages)) {
                    $message .= ' Đã tải lên ' . count($uploadedImages) . ' hình ảnh mới.';
                }
                $_SESSION['success_message'] = $message;

                // Log the update activity
                error_log("Product updated: ID {$id}, Name: {$data['product_name']}, Uploaded images: " . count($uploadedImages) . ", User: Admin, Time: " . date('Y-m-d H:i:s'));

                $this->redirect('/5s-fashion/admin/products/view/' . $id);
            } else {
                throw new Exception('Không thể cập nhật sản phẩm');
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi cập nhật sản phẩm: ' . $e->getMessage();
            $this->redirect('/5s-fashion/admin/products/edit/' . $id);
        }
    }

    public function delete($id)
    {
        $this->ensureSessionStarted();

        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        try {
            // Validate ID
            if (!$id || !is_numeric($id)) {
                throw new Exception('ID sản phẩm không hợp lệ');
            }

            // Simulate actual deletion (in real app, delete from database)
            // $db = Database::getInstance();
            // $result = $db->delete('products', ['id' => $id]);

            // For demo, we'll create a "deleted products" tracking
            if (!isset($_SESSION['deleted_products'])) {
                $_SESSION['deleted_products'] = [];
            }

            // Mark product as deleted
            $_SESSION['deleted_products'][] = $id;
            $productName = "Sản phẩm #" . $id;
            $deleteSuccess = true;

            if (!$deleteSuccess) {
                throw new Exception('Không thể xóa sản phẩm khỏi database');
            }

            // Log the deletion
            error_log("Product deleted: ID {$id}, User: Admin, Time: " . date('Y-m-d H:i:s'));

            if ($isAjax) {
                // Return JSON response for AJAX
                $this->renderJSON([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm thành công!',
                    'product_id' => $id,
                    'product_name' => $productName
                ]);
            } else {
                // Redirect for normal request
                $_SESSION['success_message'] = 'Đã xóa sản phẩm thành công!';
                $this->redirect('/5s-fashion/admin/products');
            }

        } catch (Exception $e) {
            if ($isAjax) {
                // Return JSON error for AJAX
                http_response_code(400);
                $this->renderJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                // Redirect with error for normal request
                $_SESSION['error_message'] = 'Lỗi xóa sản phẩm: ' . $e->getMessage();
                $this->redirect('/5s-fashion/admin/products');
            }
        }
    }

    public function getDeleted()
    {
        $this->ensureSessionStarted();

        $deletedProducts = $_SESSION['deleted_products'] ?? [];
        $this->renderJSON($deletedProducts);
    }

    public function clearDeleted()
    {
        $this->ensureSessionStarted();

        $_SESSION['deleted_products'] = [];
        $this->renderJSON(['success' => true, 'message' => 'Đã xóa danh sách sản phẩm đã xóa']);
    }
}
