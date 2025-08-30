<?php
/**
 * Professional Categories Controller
 * Business-grade category management interface
 * Clean MVC structure - all HTML in views
 */

require_once dirname(__DIR__) . '/BaseController.php';

class CategoriesController extends BaseController
{
    public function __construct()
    {
        // Session đã được start từ index.php

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /zone-fashion/admin/login');
            exit;
        }
    }

    public function index()
    {
        try {
            $db = Database::getInstance();

            // Get categories with products count and parent name
            $sql = "SELECT c.*,
                           p_cat.name as parent_name,
                           COUNT(DISTINCT p.id) as products_count
                    FROM categories c
                    LEFT JOIN categories p_cat ON c.parent_id = p_cat.id
                    LEFT JOIN products p ON c.id = p.category_id AND p.status != 'draft'
                    GROUP BY c.id
                    ORDER BY c.sort_order ASC, c.name ASC";

            $categories = $db->fetchAll($sql);

            $data = [
                'title' => 'Quản lý danh mục - zone Fashion Admin',
                'pageTitle' => 'Quản lý danh mục',
                'categories' => $categories,
                'breadcrumb' => ['Danh mục']
            ];

            $this->render('admin/categories/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log("Error in CategoriesController::index - " . $e->getMessage());

            // Fallback to empty data if database error
            $data = [
                'title' => 'Quản lý danh mục - zone Fashion Admin',
                'pageTitle' => 'Quản lý danh mục',
                'categories' => [],
                'breadcrumb' => ['Danh mục'],
                'error' => 'Có lỗi xảy ra khi tải danh mục'
            ];

            $this->render('admin/categories/index', $data, 'admin/layouts/main-inline');
        }
    }

    public function create()
    {
        try {
            $db = Database::getInstance();

            // Get parent categories for dropdown
            $parentCategories = $db->fetchAll("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");

            $data = [
                'title' => 'Thêm danh mục mới - zone Fashion Admin',
                'pageTitle' => 'Thêm danh mục mới',
                'parentCategories' => $parentCategories,
                'breadcrumb' => ['Danh mục', 'Thêm mới']
            ];

            $this->render('admin/categories/create', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log("Error in CategoriesController::create - " . $e->getMessage());
            header('Location: /zone-fashion/admin/categories?error=' . urlencode('Lỗi khi tải form tạo danh mục'));
            exit;
        }
    }

    public function store()
    {
        $this->ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/zone-fashion/admin/categories');
            return;
        }

        try {
            $db = Database::getInstance();

            // Get form data
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'meta_title' => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? '')
            ];

            // Validate required fields
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Tên danh mục không được để trống';
            }

            // Auto-generate slug if empty
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateSlug($data['name']);
            }

            // Check slug uniqueness
            $existingSlug = $db->fetchOne("SELECT id FROM categories WHERE slug = ? AND id != 0", [$data['slug']]);
            if ($existingSlug) {
                $data['slug'] = $data['slug'] . '-' . time();
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
                $this->redirect('/zone-fashion/admin/categories');
                return;
            }

            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->handleImageUpload($_FILES['image'], 'categories');
                if ($uploadResult['success']) {
                    $data['image'] = $uploadResult['path'];
                } else {
                    $_SESSION['errors'] = [$uploadResult['error']];
                    $_SESSION['old_data'] = $data;
                    $this->redirect('/zone-fashion/admin/categories');
                    return;
                }
            }

            // Insert category
            $sql = "INSERT INTO categories (name, slug, description, image, parent_id, sort_order, status, meta_title, meta_description, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $result = $db->execute($sql, [
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['image'] ?? null,
                $data['parent_id'],
                $data['sort_order'],
                $data['status'],
                $data['meta_title'],
                $data['meta_description']
            ]);

            if ($result) {
                $_SESSION['success_message'] = 'Đã thêm danh mục thành công!';
            } else {
                $_SESSION['error_message'] = 'Có lỗi xảy ra khi thêm danh mục!';
            }

        } catch (Exception $e) {
            error_log("Error in CategoriesController::store - " . $e->getMessage());
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        $this->redirect('/zone-fashion/admin/categories');
    }

    public function show($id)
    {
        try {
            $db = Database::getInstance();

            // Get category details with parent info
            $sql = "SELECT c.*, p.name as parent_name
                    FROM categories c
                    LEFT JOIN categories p ON c.parent_id = p.id
                    WHERE c.id = ?";
            $category = $db->fetchOne($sql, [$id]);

            if (!$category) {
                throw new Exception('Không tìm thấy danh mục');
            }

            // Get subcategories
            $subcategories = $db->fetchAll("SELECT * FROM categories WHERE parent_id = ? ORDER BY sort_order ASC", [$id]);

            // Get products in this category
            $products = $db->fetchAll("SELECT * FROM products WHERE category_id = ? ORDER BY name ASC LIMIT 10", [$id]);

            $data = [
                'title' => 'Chi tiết danh mục: ' . $category['name'] . ' - zone Fashion Admin',
                'pageTitle' => 'Chi tiết danh mục',
                'category' => $category,
                'subcategories' => $subcategories,
                'products' => $products,
                'breadcrumb' => ['Danh mục', 'Chi tiết']
            ];

            $this->render('admin/categories/show', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log("Error in CategoriesController::show - " . $e->getMessage());
            header('Location: /zone-fashion/admin/categories?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function edit($id)
    {
        try {
            $db = Database::getInstance();

            // Get category details
            $category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);

            if (!$category) {
                throw new Exception('Không tìm thấy danh mục');
            }

            // Get parent categories (exclude current category and its children)
            $parentCategories = $db->fetchAll("SELECT * FROM categories WHERE parent_id IS NULL AND id != ? ORDER BY name ASC", [$id]);

            $data = [
                'title' => 'Chỉnh sửa danh mục: ' . $category['name'] . ' - zone Fashion Admin',
                'pageTitle' => 'Chỉnh sửa danh mục',
                'category' => $category,
                'parentCategories' => $parentCategories,
                'breadcrumb' => ['Danh mục', 'Chỉnh sửa']
            ];

            $this->render('admin/categories/edit', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log("Error in CategoriesController::edit - " . $e->getMessage());
            header('Location: /zone-fashion/admin/categories?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function update($id)
    {
        $this->ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/zone-fashion/admin/categories');
            return;
        }

        try {
            $db = Database::getInstance();

            // Check if category exists
            $category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
            if (!$category) {
                $_SESSION['error_message'] = 'Danh mục không tồn tại!';
                $this->redirect('/zone-fashion/admin/categories');
                return;
            }

            // Get form data
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'meta_title' => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? '')
            ];

            // Validate required fields
            $errors = [];
            if (empty($data['name'])) {
                $errors[] = 'Tên danh mục không được để trống';
            }

            // Auto-generate slug if empty
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateSlug($data['name']);
            }

            // Check slug uniqueness (exclude current category)
            $existingSlug = $db->fetchOne("SELECT id FROM categories WHERE slug = ? AND id != ?", [$data['slug'], $id]);
            if ($existingSlug) {
                $data['slug'] = $data['slug'] . '-' . time();
            }

            // Prevent setting parent as itself or child
            if ($data['parent_id'] == $id) {
                $errors[] = 'Danh mục không thể là cha của chính nó';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
                $this->redirect('/zone-fashion/admin/categories');
                return;
            }

            // Handle image upload and removal
            $imageToUpdate = $category['image'];

            // Check if user wants to remove current image
            if (isset($_POST['remove_current_image']) && $_POST['remove_current_image'] == '1') {
                // Delete old image file
                if ($category['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $category['image'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $category['image']);
                }
                $imageToUpdate = null;
            }

            // Check if new image is uploaded
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->handleImageUpload($_FILES['image'], 'categories');
                if ($uploadResult['success']) {
                    // Delete old image if exists and we're replacing it
                    if ($category['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $category['image'])) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $category['image']);
                    }
                    $imageToUpdate = $uploadResult['path'];
                } else {
                    $_SESSION['errors'] = [$uploadResult['error']];
                    $_SESSION['old_data'] = $data;
                    $this->redirect('/zone-fashion/admin/categories');
                    return;
                }
            }

            // Update category
            $sql = "UPDATE categories SET
                        name = ?, slug = ?, description = ?, image = ?, parent_id = ?,
                        sort_order = ?, status = ?, meta_title = ?, meta_description = ?,
                        updated_at = NOW()
                    WHERE id = ?";

            $result = $db->execute($sql, [
                $data['name'],
                $data['slug'],
                $data['description'],
                $imageToUpdate,
                $data['parent_id'],
                $data['sort_order'],
                $data['status'],
                $data['meta_title'],
                $data['meta_description'],
                $id
            ]);

            if ($result) {
                $_SESSION['success_message'] = 'Đã cập nhật danh mục thành công!';
            } else {
                $_SESSION['error_message'] = 'Có lỗi xảy ra khi cập nhật danh mục!';
            }

        } catch (Exception $e) {
            error_log("Error in CategoriesController::update - " . $e->getMessage());
            $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        $this->redirect('/zone-fashion/admin/categories');
    }

    public function delete($id)
    {
        $this->ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $db = Database::getInstance();

            // Check if category exists
            $category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
            if (!$category) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Danh mục không tồn tại']);
                return;
            }

            // Check if category has products
            $productCount = $db->fetchOne("SELECT COUNT(*) as count FROM products WHERE category_id = ?", [$id]);
            if ($productCount && $productCount['count'] > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không thể xóa danh mục có sản phẩm']);
                return;
            }

            // Check if category has children
            $childCount = $db->fetchOne("SELECT COUNT(*) as count FROM categories WHERE parent_id = ?", [$id]);
            if ($childCount && $childCount['count'] > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không thể xóa danh mục có danh mục con']);
                return;
            }

            // Delete category
            $result = $db->execute("DELETE FROM categories WHERE id = ?", [$id]);

            if ($result) {
                // Delete image file if exists
                if ($category['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $category['image'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $category['image']);
                }

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Đã xóa danh mục thành công']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa danh mục']);
            }

        } catch (Exception $e) {
            error_log("Error in CategoriesController::delete - " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function api($id)
    {
        header('Content-Type: application/json');

        try {
            $db = Database::getInstance();

            // Get category details
            $category = $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);

            if (!$category) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy danh mục']);
                return;
            }

            echo json_encode(['success' => true, 'data' => $category]);

        } catch (Exception $e) {
            error_log("Error in CategoriesController::api - " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    private function generateSlug($text)
    {
        // Convert to lowercase and remove Vietnamese accents
        $slug = strtolower($text);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);

        // Replace Vietnamese characters
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];
        $english = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];

        $slug = str_replace($vietnamese, $english, $slug);

        // Remove special characters and replace spaces with hyphens
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    private function handleImageUpload($file, $folder)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Lỗi upload file'];
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ cho phép upload file hình ảnh (JPG, PNG, GIF, WebP)'];
        }

        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File không được vượt quá 5MB'];
        }

        // Create upload directory
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/zone-fashion/public/uploads/' . $folder . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $folder . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        // Debug log
        error_log("Upload Debug - Dir: $uploadDir, File: $filename, Full path: $uploadPath");

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'path' => 'uploads/' . $folder . '/' . $filename
            ];
        } else {
            return ['success' => false, 'error' => 'Không thể lưu file'];
        }
    }

    protected function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function redirect($url, $message = null, $type = 'success')
    {
        if ($message) {
            $_SESSION[$type . '_message'] = $message;
        }
        header('Location: ' . $url);
        exit;
    }
}
