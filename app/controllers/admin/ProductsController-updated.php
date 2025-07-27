<?php

require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Category.php';

class ProductsController extends BaseController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin/login');
            exit;
        }

        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index()
    {
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
                'title' => 'Quản lý sản phẩm - 5S Fashion Admin',
                'products' => $products,
                'categories' => $categories,
                'stats' => $stats,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Sản phẩm']
                ]
            ];

            $this->render('admin/products/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            $data = [
                'title' => 'Quản lý sản phẩm - 5S Fashion Admin',
                'error' => 'Lỗi khi tải danh sách sản phẩm: ' . $e->getMessage(),
                'products' => [],
                'categories' => [],
                'stats' => [],
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
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
                'title' => 'Thêm sản phẩm mới - 5S Fashion Admin',
                'categories' => $categories,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Sản phẩm', 'url' => '/5s-fashion/admin/products'],
                    ['title' => 'Thêm mới']
                ]
            ];

            $this->render('admin/products/create', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/products?error=' . urlencode('Lỗi khi tải form tạo sản phẩm'));
            exit;
        }
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/products');
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
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'meta_keywords' => $_POST['meta_keywords'] ?? ''
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

            // Handle tags
            if (!empty($_POST['tags'])) {
                $this->attachProductTags($productId, $_POST['tags']);
            }

            header('Location: /5s-fashion/admin/products?success=' . urlencode('Tạo sản phẩm thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/products/create?error=' . urlencode($e->getMessage()));
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
                'title' => 'Chỉnh sửa sản phẩm - 5S Fashion Admin',
                'product' => $product,
                'categories' => $categories,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Sản phẩm', 'url' => '/5s-fashion/admin/products'],
                    ['title' => 'Chỉnh sửa']
                ]
            ];

            $this->render('admin/products/edit', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/products?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function update($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/products');
                exit;
            }

            // Check if product exists
            $existingProduct = $this->productModel->findById($id);
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
                'compare_price' => !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : null,
                'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                'sku' => $_POST['sku'] ?? $existingProduct['sku'],
                'barcode' => $_POST['barcode'] ?? null,
                'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                'dimensions' => $_POST['dimensions'] ?? null,
                'track_quantity' => isset($_POST['track_quantity']) ? 1 : 0,
                'continue_selling_when_out_of_stock' => isset($_POST['continue_selling_when_out_of_stock']) ? 1 : 0,
                'requires_shipping' => isset($_POST['requires_shipping']) ? 1 : 0,
                'is_taxable' => isset($_POST['is_taxable']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'active',
                'visibility' => $_POST['visibility'] ?? 'public',
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'meta_title' => $_POST['meta_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'meta_keywords' => $_POST['meta_keywords'] ?? ''
            ];

            // Update slug if name changed
            if ($updateData['name'] !== $existingProduct['name']) {
                $updateData['slug'] = $this->generateSlug($updateData['name']);
            }

            // Handle new featured image upload
            if (!empty($_FILES['featured_image']['name'])) {
                $uploadResult = $this->uploadProductImage($_FILES['featured_image']);
                if ($uploadResult['success']) {
                    // Delete old image
                    if ($existingProduct['featured_image']) {
                        $this->deleteProductImage($existingProduct['featured_image']);
                    }
                    $updateData['featured_image'] = $uploadResult['path'];
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Update product
            $result = $this->productModel->update($id, $updateData);

            if (!$result) {
                throw new Exception('Không thể cập nhật sản phẩm');
            }

            header('Location: /5s-fashion/admin/products?success=' . urlencode('Cập nhật sản phẩm thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/products/edit/' . $id . '?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function delete($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/products');
                exit;
            }

            // Check if product exists
            $product = $this->productModel->findById($id);
            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Soft delete product
            $result = $this->productModel->softDelete($id);

            if (!$result) {
                throw new Exception('Không thể xóa sản phẩm');
            }

            header('Location: /5s-fashion/admin/products?success=' . urlencode('Xóa sản phẩm thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/products?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/products');
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
                        if ($this->productModel->update($productId, ['is_featured' => 1])) {
                            $count++;
                        }
                        break;

                    case 'unfeature':
                        if ($this->productModel->update($productId, ['is_featured' => 0])) {
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

            header('Location: /5s-fashion/admin/products?success=' . urlencode("Đã thực hiện thao tác cho {$count} sản phẩm"));
            exit;

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/products?error=' . urlencode($e->getMessage()));
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
            header('Location: /5s-fashion/admin/products?error=' . urlencode('Lỗi khi xuất dữ liệu: ' . $e->getMessage()));
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
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File ảnh không được vượt quá 5MB'];
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => true, 'path' => '/uploads/products/' . $fileName];
        } else {
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
}
