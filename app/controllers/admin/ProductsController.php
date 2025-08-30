<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../core/Database.php';

class ProductsController extends BaseController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /zone-fashion/admin/login');
            exit;
        }

        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index($productId = null, $action = null)
    {
        // Handle variants routing: /admin/products/{id}/variants
        if ($productId && $action === 'variants') {
            return $this->variants($productId);
        }

        try {
            // Get search and filter parameters
            $search = $_GET['search'] ?? '';
            $filters = [
                'category_id' => $_GET['category_id'] ?? '',
                'status' => $_GET['status'] ?? '',
                'stock_status' => $_GET['stock_status'] ?? '',
                'price_min' => $_GET['price_min'] ?? '',
                'price_max' => $_GET['price_max'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC',
                'limit' => $_GET['limit'] ?? 20
            ];

            // Get products with search and filters
            $products = $this->productModel->search($search, $filters);

            // Get categories for filter dropdown
            $categories = $this->categoryModel->getActive();

            // Get product statistics
            $stats = $this->productModel->getProductStatistics();

            $data = [
                'title' => 'Quản lý sản phẩm - zone Fashion Admin',
                'products' => $products,
                'categories' => $categories,
                'stats' => $stats,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Sản phẩm']
                ]
            ];

            $this->render('admin/products/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            $data = [
                'title' => 'Quản lý sản phẩm - zone Fashion Admin',
                'error' => 'Lỗi khi tải danh sách sản phẩm: ' . $e->getMessage(),
                'products' => [],
                'categories' => [],
                'stats' => [],
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Sản phẩm']
                ]
            ];

            $this->render('admin/products/index', $data, 'admin/layouts/main-inline');
        }
    }

    public function create()
    {
        try {
            // Get categories for dropdown
            $categories = $this->categoryModel->getActive();

            $data = [
                'title' => 'Thêm sản phẩm mới - zone Fashion Admin',
                'categories' => $categories,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Sản phẩm', 'url' => '/zone-fashion/admin/products'],
                    ['title' => 'Thêm mới']
                ]
            ];

            $this->render('admin/products/create', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products?error=' . urlencode('Lỗi khi tải form tạo sản phẩm'));
            exit;
        }
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/products');
                exit;
            }

            // Validate required fields
            $requiredFields = ['name', 'category_id', 'price'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Trường {$field} là bắt buộc");
                }
            }

            // Prepare product data
            $productData = [
                'name' => trim($_POST['name']),
                'slug' => $this->generateSlug($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'short_description' => $_POST['short_description'] ?? '',
                'category_id' => (int)$_POST['category_id'],
                'price' => (float)$_POST['price'],
                'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
                'compare_price' => !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : null,
                'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                'sku' => $_POST['sku'] ?? $this->generateSKU(),
                'barcode' => $_POST['barcode'] ?? null,
                'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                'dimensions' => $_POST['dimensions'] ?? null,
                'track_quantity' => isset($_POST['track_quantity']) ? 1 : 0,
                'continue_selling_when_out_of_stock' => isset($_POST['continue_selling_when_out_of_stock']) ? 1 : 0,
                'requires_shipping' => isset($_POST['requires_shipping']) ? 1 : 0,
                'is_taxable' => isset($_POST['is_taxable']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'active',
                'visibility' => $_POST['visibility'] ?? 'public',
                'featured' => isset($_POST['is_featured']) ? 1 : 0,
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'meta_keywords' => $_POST['meta_keywords'] ?? '',
                // New variant-related fields
                'has_variants' => isset($_POST['has_variants']) ? 1 : 0,
                'manage_stock' => isset($_POST['manage_stock']) ? 1 : 0,
                'stock_quantity' => !empty($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0,
                'low_stock_threshold' => !empty($_POST['low_stock_threshold']) ? (int)$_POST['low_stock_threshold'] : 5
            ];

            // Handle image upload
            if (!empty($_FILES['featured_image']['name'])) {
                $uploadResult = $this->uploadProductImage($_FILES['featured_image']);
                if ($uploadResult['success']) {
                    $productData['featured_image'] = $uploadResult['path'];
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Create product
            $productId = $this->productModel->create($productData);

            if (!$productId) {
                throw new Exception('Không thể tạo sản phẩm');
            }

            // Handle product variants if provided
            if (!empty($_POST['variants'])) {
                $this->createProductVariants($productId, $_POST['variants']);
            }

            // Handle product images
            if (!empty($_FILES['product_images'])) {
                $this->uploadProductImages($productId, $_FILES['product_images']);
            }

            // Handle tags - temporarily disabled until tags column is added
            /*
            if (!empty($_POST['tags'])) {
                $this->attachProductTags($productId, $_POST['tags']);
            }
            */

            header('Location: /zone-fashion/admin/products?success=' . urlencode('Tạo sản phẩm thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products/create?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function show($id)
    {
        try {
            // Get product details
            $product = $this->productModel->getFullDetails($id);

            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Get product variants if exist
            $variants = $this->getProductVariants($id);

            // Calculate total stock from variants
            $totalStock = 0;
            foreach ($variants as $variant) {
                $totalStock += (int)($variant['stock_quantity'] ?? 0);
            }
            $product['total_variant_stock'] = $totalStock;

            // Get product images gallery
            $galleryImages = [];
            if (!empty($product['gallery'])) {
                $galleryImages = json_decode($product['gallery'], true) ?: [];
            }

            $data = [
                'title' => 'Chi tiết sản phẩm: ' . $product['name'] . ' - zone Fashion Admin',
                'product' => $product,
                'variants' => $variants,
                'galleryImages' => $galleryImages,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Sản phẩm', 'url' => '/zone-fashion/admin/products'],
                    ['title' => 'Chi tiết']
                ]
            ];

            $this->render('admin/products/show', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function edit($id)
    {
        try {
            // Get product details
            $product = $this->productModel->getFullDetails($id);

            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Get categories for dropdown
            $categories = $this->categoryModel->getActive();

            $data = [
                'title' => 'Chỉnh sửa sản phẩm - zone Fashion Admin',
                'product' => $product,
                'categories' => $categories,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Sản phẩm', 'url' => '/zone-fashion/admin/products'],
                    ['title' => 'Chỉnh sửa']
                ]
            ];

            $this->render('admin/products/edit', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function update($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/products');
                exit;
            }

            // Debug $_FILES and $_POST
            // error_log("DEBUG update() - POST data: " . json_encode($_POST));
            // error_log("DEBUG update() - FILES data: " . json_encode($_FILES));

            // Check if product exists
            $existingProduct = $this->productModel->find($id);
            if (!$existingProduct) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Prepare update data
            $updateData = [
                'name' => trim($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'short_description' => $_POST['short_description'] ?? '',
                'category_id' => (int)$_POST['category_id'],
                'price' => (float)$_POST['price'],
                'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
                'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                'sku' => $_POST['sku'] ?? $existingProduct['sku'],
                'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                'dimensions' => $_POST['dimensions'] ?? null,
                'status' => $_POST['status'] ?? 'published',
                'featured' => isset($_POST['is_featured']) ? 1 : 0,
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                // Add variant-related fields
                'has_variants' => isset($_POST['has_variants']) ? 1 : 0,
                'manage_stock' => isset($_POST['manage_stock']) ? 1 : 0,
                'stock_quantity' => isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0,
                'low_stock_threshold' => isset($_POST['low_stock_threshold']) ? (int)$_POST['low_stock_threshold'] : 5
            ];

            // Update slug if name changed
            if ($updateData['name'] !== $existingProduct['name']) {
                $updateData['slug'] = $this->generateSlug($updateData['name']);
            }

            // TEMPORARILY DISABLE IMAGE UPLOAD FOR DEBUGGING
            /*
            // Handle new featured image upload
            if (!empty($_FILES['featured_image']['name'])) {
                error_log("DEBUG: Uploading new featured image: " . $_FILES['featured_image']['name']);
                $uploadResult = $this->uploadProductImage($_FILES['featured_image']);
                error_log("DEBUG: Upload result: " . json_encode($uploadResult));

                if ($uploadResult['success']) {
                    // Delete old image
                    if ($existingProduct['featured_image']) {
                        error_log("DEBUG: Deleting old image: " . $existingProduct['featured_image']);
                        $this->deleteProductImage($existingProduct['featured_image']);
                    }
                    $updateData['featured_image'] = $uploadResult['path'];
                    error_log("DEBUG: New image path: " . $uploadResult['path']);
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Handle gallery images upload
            if (!empty($_FILES['product_images']['name'][0])) {
                error_log("DEBUG: Uploading gallery images");
                error_log("DEBUG: Gallery files: " . json_encode($_FILES['product_images']));

                $galleryUploadResult = $this->uploadMultipleProductImages($_FILES['product_images']);
                error_log("DEBUG: Gallery upload result: " . json_encode($galleryUploadResult));

                if ($galleryUploadResult['success']) {
                    // Get existing gallery images
                    $existingGallery = [];
                    if (!empty($existingProduct['gallery'])) {
                        $existingGallery = json_decode($existingProduct['gallery'], true) ?: [];
                    }

                    // Merge with new images
                    $allGalleryImages = array_merge($existingGallery, $galleryUploadResult['paths']);
                    $updateData['gallery'] = json_encode($allGalleryImages);
                    error_log("DEBUG: Updated gallery: " . $updateData['gallery']);
                } else {
                    throw new Exception($galleryUploadResult['error']);
                }
            }
            */

            // Update product
            $result = $this->productModel->update($id, $updateData);

            if (!$result) {
                throw new Exception('Không thể cập nhật sản phẩm');
            }

            header('Location: /zone-fashion/admin/products?success=' . urlencode('Cập nhật sản phẩm thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products/edit/' . $id . '?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function delete($id)
    {
        try {
            // Accept both POST and DELETE methods
            $allowedMethods = ['POST', 'DELETE'];
            if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                    exit;
                } else {
                    header('Location: /zone-fashion/admin/products');
                    exit;
                }
            }

            // Check if product exists
            $product = $this->productModel->find($id);
            if (!$product) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
                    exit;
                } else {
                    throw new Exception('Không tìm thấy sản phẩm');
                }
            }

            // Delete related data first
            $this->deleteProductRelatedData($id);

            // Delete product
            $result = $this->productModel->delete($id);

            if (!$result) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm']);
                    exit;
                } else {
                    throw new Exception('Không thể xóa sản phẩm');
                }
            }

            // Return success response
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
                exit;
            } else {
                header('Location: /zone-fashion/admin/products?success=' . urlencode('Xóa sản phẩm thành công'));
                exit;
            }

        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            } else {
                header('Location: /zone-fashion/admin/products?error=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    /**
     * Delete individual gallery image - URL: /admin/products/deletegalleryimage
     */
    public function deletegalleryimage()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $productId = $input['productId'] ?? $_POST['product_id'] ?? null;
            $imageIndex = $input['imageIndex'] ?? $_POST['image_index'] ?? null;

            if (!$productId || $imageIndex === null) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                exit;
            }

            // Check if product exists
            $product = $this->productModel->find($productId);
            if (!$product) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
                exit;
            }

            // Get current gallery
            $galleryImages = [];
            if (!empty($product['gallery'])) {
                $galleryImages = json_decode($product['gallery'], true) ?: [];
            }

            // Check if image index exists
            if (!isset($galleryImages[$imageIndex])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy ảnh']);
                exit;
            }

            // Get image path to delete
            $imageToDelete = $galleryImages[$imageIndex];

            // Remove image from array
            unset($galleryImages[$imageIndex]);
            $galleryImages = array_values($galleryImages); // Re-index array

            // Update product gallery
            $newGalleryJson = empty($galleryImages) ? null : json_encode($galleryImages);
            $updateData = ['gallery' => $newGalleryJson];

            $result = $this->productModel->update($productId, $updateData);

            if ($result) {
                // Delete physical file
                $this->deleteProductImage($imageToDelete);

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa ảnh thành công',
                    'remainingImages' => count($galleryImages)
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật database']);
            }
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    private function deleteProductRelatedData($productId)
    {
        try {
            // Delete related data (if tables exist)
            $db = Database::getInstance();

            // Check if tables exist before deleting
            $tables = [
                'product_images',
                'product_variants',
                'product_tags',
                'cart_items',
                'order_items'
            ];

            foreach ($tables as $table) {
                // Check if table exists
                $checkTable = $db->query("SHOW TABLES LIKE '$table'");
                if ($checkTable && $checkTable->rowCount() > 0) {
                    $db->query("DELETE FROM $table WHERE product_id = ?", [$productId]);
                }
            }

        } catch (Exception $e) {
            error_log("Error deleting product related data: " . $e->getMessage());
        }
    }

    public function exportToExcel($products, $filename = 'products_export.xlsx')
    {
        // Tạm thời export CSV thay vì Excel
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . str_replace('.xlsx', '.csv', $filename) . '"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['ID', 'Tên sản phẩm', 'Giá', 'Số lượng', 'Danh mục', 'Trạng thái']);

        // Data
        foreach ($products as $product) {
            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['price'],
                $product['stock_quantity'],
                $product['category_name'] ?? '',
                $product['status'] == 1 ? 'Hoạt động' : 'Không hoạt động'
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Bulk actions
     */
    public function bulkAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/products');
                exit;
            }

            $action = $_POST['bulk_action'] ?? '';
            $productIds = $_POST['product_ids'] ?? [];

            if (empty($action) || empty($productIds)) {
                throw new Exception('Vui lòng chọn thao tác và sản phẩm');
            }

            $count = 0;
            foreach ($productIds as $productId) {
                switch ($action) {
                    case 'activate':
                        if ($this->productModel->update($productId, ['status' => 'active'])) {
                            $count++;
                        }
                        break;

                    case 'deactivate':
                        if ($this->productModel->update($productId, ['status' => 'inactive'])) {
                            $count++;
                        }
                        break;

                    case 'feature':
                        if ($this->productModel->update($productId, ['featured' => 1])) {
                            $count++;
                        }
                        break;

                    case 'unfeature':
                        if ($this->productModel->update($productId, ['featured' => 0])) {
                            $count++;
                        }
                        break;

                    case 'delete':
                        if ($this->productModel->softDelete($productId)) {
                            $count++;
                        }
                        break;
                }
            }

            header('Location: /zone-fashion/admin/products?success=' . urlencode("Đã thực hiện thao tác cho {$count} sản phẩm"));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Export products
     */
    public function export()
    {
        try {
            $format = $_GET['format'] ?? 'csv';
            $search = $_GET['search'] ?? '';
            $filters = [
                'category_id' => $_GET['category_id'] ?? '',
                'status' => $_GET['status'] ?? '',
                'stock_status' => $_GET['stock_status'] ?? ''
            ];

            $products = $this->productModel->exportProducts($filters);

            if ($format === 'csv') {
                $this->exportToCsv($products, 'products_' . date('Y-m-d_H-i-s') . '.csv');
            } else {
                $this->exportToExcel($products, 'products_' . date('Y-m-d_H-i-s') . '.xlsx');
            }

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products?error=' . urlencode('Lỗi khi xuất dữ liệu: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Get product details via AJAX
     */
    public function getDetails($id)
    {
        try {
            $product = $this->productModel->getFullDetails($id);

            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $product
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate product slug
     */
    private function generateSlug($name)
    {
        // Convert Vietnamese characters
        $slug = $this->removeVietnameseAccents($name);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;

        while ($this->productModel->findBy('slug', $slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate product SKU
     */
    private function generateSKU()
    {
        do {
            $sku = 'PRD' . date('ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $exists = $this->productModel->findBy('sku', $sku);
        } while ($exists);

        return $sku;
    }

    /**
     * Upload product image
     */
    private function uploadProductImage($file)
    {
        error_log("DEBUG uploadProductImage - File info: " . json_encode($file));

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            error_log("DEBUG uploadProductImage - Invalid file type: " . $file['type']);
            return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)'];
        }

        if ($file['size'] > $maxSize) {
            error_log("DEBUG uploadProductImage - File too large: " . $file['size']);
            return ['success' => false, 'error' => 'File ảnh không được vượt quá 5MB'];
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/products/';
        error_log("DEBUG uploadProductImage - Upload dir: " . $uploadDir);

        if (!is_dir($uploadDir)) {
            error_log("DEBUG uploadProductImage - Creating directory: " . $uploadDir);
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;

        error_log("DEBUG uploadProductImage - Target file path: " . $filePath);

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            error_log("DEBUG uploadProductImage - Upload SUCCESS: " . $fileName);
            return ['success' => true, 'path' => '/uploads/products/' . $fileName];
        } else {
            error_log("DEBUG uploadProductImage - Upload FAILED. Source: " . $file['tmp_name'] . ", Target: " . $filePath);
            return ['success' => false, 'error' => 'Không thể upload file'];
        }
    }

    /**
     * Delete product image
     */
    private function deleteProductImage($imagePath)
    {
        $fullPath = __DIR__ . '/../../../public' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Upload multiple product images for gallery
     */
    private function uploadMultipleProductImages($files)
    {
        error_log("DEBUG uploadMultipleProductImages - Files info: " . json_encode($files));

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadedPaths = [];
        $errors = [];

        if (!is_array($files['tmp_name'])) {
            return ['success' => false, 'error' => 'Invalid file format'];
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Process each file
        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                continue; // Skip files with errors
            }

            $fileType = $files['type'][$key];
            $fileSize = $files['size'][$key];
            $fileName = $files['name'][$key];

            // Validate file type
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "File {$fileName}: Chỉ chấp nhận file ảnh";
                continue;
            }

            // Validate file size
            if ($fileSize > $maxSize) {
                $errors[] = "File {$fileName}: Kích thước không được vượt quá 5MB";
                continue;
            }

            // Generate unique filename
            $newFileName = uniqid() . '_' . time() . '_' . $key . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $filePath = $uploadDir . $newFileName;

            // Upload file
            if (move_uploaded_file($tmpName, $filePath)) {
                $uploadedPaths[] = '/uploads/products/' . $newFileName;
                error_log("DEBUG uploadMultipleProductImages - Uploaded: " . $newFileName);
            } else {
                $errors[] = "File {$fileName}: Không thể upload";
            }
        }

        if (!empty($errors)) {
            error_log("DEBUG uploadMultipleProductImages - Errors: " . json_encode($errors));
            return ['success' => false, 'error' => implode('; ', $errors)];
        }

        if (empty($uploadedPaths)) {
            return ['success' => false, 'error' => 'Không có file nào được upload'];
        }

        error_log("DEBUG uploadMultipleProductImages - Success: " . json_encode($uploadedPaths));
        return ['success' => true, 'paths' => $uploadedPaths];
    }

    /**
     * Remove Vietnamese accents
     */
    private function removeVietnameseAccents($str)
    {
        $accents = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ'
        ];

        $noAccents = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
            'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
            'I', 'I', 'I', 'I', 'I',
            'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'Y', 'Y', 'Y', 'Y', 'Y',
            'D'
        ];

        return str_replace($accents, $noAccents, $str);
    }

    /**
     * Get product variants
     */
    private function getProductVariants($productId)
    {
        try {
            $db = Database::getInstance();

            // Lấy variant cùng với thuộc tính màu sắc và kích cỡ
            $query = "SELECT pv.*,
                      MAX(CASE WHEN pa.type = 'color' THEN pav.value END) as color,
                      MAX(CASE WHEN pa.type = 'color' THEN pav.color_code END) as color_code,
                      MAX(CASE WHEN pa.type = 'size' THEN pav.value END) as size
                      FROM product_variants pv
                      LEFT JOIN product_variant_attributes pva ON pv.id = pva.variant_id
                      LEFT JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
                      LEFT JOIN product_attributes pa ON pav.attribute_id = pa.id
                      WHERE pv.product_id = ?
                      GROUP BY pv.id
                      ORDER BY pv.sort_order, pv.id ASC";

            $stmt = $db->query($query, [$productId]);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error getting product variants: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for proper Excel display
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys($data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Attach tags to product
     */
    private function attachProductTags($productId, $tagsString)
    {
        if (empty($tagsString)) {
            return;
        }

        try {
            // Get database instance
            $db = Database::getInstance();

            // Parse tags from comma-separated string
            $tags = array_map('trim', explode(',', $tagsString));
            $tags = array_filter($tags); // Remove empty tags

            if (empty($tags)) {
                return;
            }

            // Store tags as comma-separated string in products table
            // since we don't have a separate tags/product_tags table structure
            $cleanTagsString = implode(', ', $tags);
            $updateQuery = "UPDATE products SET tags = ? WHERE id = ?";
            $db->execute($updateQuery, [$cleanTagsString, $productId]);

        } catch (Exception $e) {
            // Log error but don't stop product creation
            error_log("Error attaching tags: " . $e->getMessage());
        }
    }    /**
     * Upload product images (featured and gallery)
     */
    private function uploadProductImages($productId, $files)
    {
        if (empty($files) || empty($files['tmp_name'])) {
            return;
        }

        try {
            $db = Database::getInstance();
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/zone-fashion/public/uploads/products/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $imagePaths = [];

            // Handle single file or multiple files
            if (is_array($files['tmp_name'])) {
                // Multiple files
                foreach ($files['tmp_name'] as $key => $tmpName) {
                    if ($files['error'][$key] === UPLOAD_ERR_OK) {
                        $imagePath = $this->processImageUpload($files, $key, $uploadDir);
                        if ($imagePath) {
                            $imagePaths[] = $imagePath;
                        }
                    }
                }
            } else {
                // Single file
                if ($files['error'] === UPLOAD_ERR_OK) {
                    $imagePath = $this->processImageUpload($files, null, $uploadDir);
                    if ($imagePath) {
                        $imagePaths[] = $imagePath;
                    }
                }
            }

            // Update product with gallery images
            if (!empty($imagePaths)) {
                $galleryJson = json_encode($imagePaths);
                $updateQuery = "UPDATE products SET gallery = ? WHERE id = ?";
                $db->execute($updateQuery, [$galleryJson, $productId]);
            }

        } catch (Exception $e) {
            error_log("Error uploading images: " . $e->getMessage());
        }
    }

    /**
     * Process individual image upload
     */
    private function processImageUpload($files, $key, $uploadDir)
    {
        try {
            if ($key !== null) {
                $originalName = $files['name'][$key];
                $tmpName = $files['tmp_name'][$key];
            } else {
                $originalName = $files['name'];
                $tmpName = $files['tmp_name'];
            }

            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($fileExtension, $allowedExtensions)) {
                return false;
            }

            $fileName = uniqid('product_') . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $filePath)) {
                return 'uploads/products/' . $fileName;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error processing image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create product variants
     */
    private function createProductVariants($productId, $variants)
    {
        if (empty($variants)) {
            return;
        }

        try {
            $db = Database::getInstance();

            foreach ($variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    $query = "INSERT INTO product_variants (product_id, size, color, stock_quantity, additional_price, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                    $db->execute($query, [
                        $productId,
                        $variant['size'] ?? null,
                        $variant['color'] ?? null,
                        $variant['stock_quantity'] ?? 0,
                        $variant['additional_price'] ?? 0
                    ]);
                }
            }
        } catch (Exception $e) {
            error_log("Error creating variants: " . $e->getMessage());
        }
    }

    /**
     * Clean up product image files
     */
    private function cleanupProductFiles($productId)
    {
        try {
            // Get product data to find image paths
            $product = $this->productModel->find($productId);
            if (!$product) {
                return;
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/zone-fashion/public/uploads/products/';

            // Delete featured image
            if (!empty($product['featured_image'])) {
                $imagePath = $uploadDir . basename($product['featured_image']);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete gallery images
            if (!empty($product['gallery'])) {
                $galleryImages = json_decode($product['gallery'], true);
                if (is_array($galleryImages)) {
                    foreach ($galleryImages as $imagePath) {
                        $fullPath = $uploadDir . basename($imagePath);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }
            }

        } catch (Exception $e) {
            error_log("Error cleaning up product files: " . $e->getMessage());
        }
    }

    /**
     * Manage product variants
     */
    public function variants($productId)
    {
        try {
            // Get product details
            $product = $this->productModel->find($productId);
            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Check if product has variants enabled
            if (empty($product['has_variants'])) {
                header('Location: /zone-fashion/admin/products/' . $productId . '/edit?error=' . urlencode('Sản phẩm này chưa bật chế độ biến thể'));
                exit;
            }

            // Include ProductVariantsController to handle variants
            require_once __DIR__ . '/ProductVariantsController.php';
            $variantsController = new ProductVariantsController();
            $variantsController->index($productId);

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/products?error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
