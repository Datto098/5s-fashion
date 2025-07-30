<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/ProductVariant.php';
require_once __DIR__ . '/../../models/ProductAttribute.php';
require_once __DIR__ . '/../../models/ProductAttributeValue.php';

class ProductVariantsController extends BaseController
{
    private $productModel;
    private $variantModel;
    private $attributeModel;
    private $attributeValueModel;

    public function __construct()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin/login');
            exit;
        }

        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
        $this->attributeModel = new ProductAttribute();
        $this->attributeValueModel = new ProductAttributeValue();
    }

    /**
     * Show variants for a product
     */
    public function index($productId)
    {
        try {
            // Get product info
            $product = $this->productModel->find($productId);
            if (!$product) {
                header('Location: /5s-fashion/admin/products?error=' . urlencode('Không tìm thấy sản phẩm'));
                exit;
            }

            // Get existing variants
            $variants = $this->productModel->getVariants($productId);

            // Get attribute data for dropdowns
            $colors = ProductAttribute::getColorsWithValues();
            $sizes = ProductAttribute::getSizesWithValues();

            // For now, create dummy materials - you can add this later
            $materials = [
                ['id' => 1, 'value' => 'Cotton'],
                ['id' => 2, 'value' => 'Polyester'],
                ['id' => 3, 'value' => 'Silk'],
                ['id' => 4, 'value' => 'Denim']
            ];

            // Simple data for now
            $data = [
                'title' => 'Quản lý biến thể - ' . $product['name'],
                'product' => $product,
                'variants' => $variants,
                'colors' => $colors ?: [],
                'sizes' => $sizes ?: [],
                'materials' => $materials,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Sản phẩm', 'url' => '/5s-fashion/admin/products'],
                    ['title' => $product['name'], 'url' => '/5s-fashion/admin/products/' . $productId . '/edit'],
                    ['title' => 'Quản lý biến thể']
                ]
            ];

            $this->render('admin/products/variants/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log("Error in ProductVariantsController::index: " . $e->getMessage());
            header('Location: /5s-fashion/admin/products?error=' . urlencode('Có lỗi xảy ra: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Create new variant
     */
    public function create($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate input
                $variantData = [
                    'product_id' => $productId,
                    'sku' => trim($_POST['sku']),
                    'variant_name' => trim($_POST['variant_name']),
                    'price' => !empty($_POST['price']) ? (float)$_POST['price'] : null,
                    'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
                    'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                    'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
                    'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                    'dimensions' => trim($_POST['dimensions'] ?? ''),
                    'image' => trim($_POST['image'] ?? ''),
                    'status' => $_POST['status'] ?? 'active',
                    'sort_order' => (int)($_POST['sort_order'] ?? 0)
                ];

                // Validate required fields
                if (empty($variantData['sku']) || empty($variantData['variant_name'])) {
                    throw new Exception('SKU và tên biến thể không được để trống');
                }

                // Check for duplicate SKU
                if ($this->variantModel->getBySku($variantData['sku'])) {
                    throw new Exception('SKU đã tồn tại');
                }

                // Get attribute value IDs
                $attributeValueIds = [];
                if (!empty($_POST['color_id'])) {
                    $attributeValueIds[] = (int)$_POST['color_id'];
                }
                if (!empty($_POST['size_id'])) {
                    $attributeValueIds[] = (int)$_POST['size_id'];
                }
                if (!empty($_POST['material_id'])) {
                    $attributeValueIds[] = (int)$_POST['material_id'];
                }

                // Create variant with attributes
                $variantId = $this->variantModel->createWithAttributes($variantData, $attributeValueIds);

                // Enable variants for product if not already enabled
                $product = $this->productModel->find($productId);
                if (!$product['has_variants']) {
                    $productObj = new Product();
                    $productObj->id = $productId;
                    $productObj->enableVariants();
                }

                $_SESSION['success'] = 'Tạo biến thể thành công';
                $this->redirect("/5s-fashion/admin/products/{$productId}/variants");

            } catch (Exception $e) {
                error_log("Error creating variant: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                $this->redirect("/5s-fashion/admin/products/{$productId}/variants");
            }
        }

        // Show create form
        $this->index($productId);
    }

    /**
     * Update variant
     */
    public function update($productId, $variantId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get existing variant
                $variant = $this->variantModel->find($variantId);
                if (!$variant || $variant['product_id'] != $productId) {
                    throw new Exception('Không tìm thấy biến thể');
                }

                // Prepare update data
                $updateData = [
                    'variant_name' => trim($_POST['variant_name']),
                    'price' => !empty($_POST['price']) ? (float)$_POST['price'] : null,
                    'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
                    'cost_price' => !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : null,
                    'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
                    'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                    'dimensions' => trim($_POST['dimensions'] ?? ''),
                    'image' => trim($_POST['image'] ?? ''),
                    'status' => $_POST['status'] ?? 'active',
                    'sort_order' => (int)($_POST['sort_order'] ?? 0)
                ];

                // Update SKU only if changed
                if (!empty($_POST['sku']) && $_POST['sku'] !== $variant['sku']) {
                    // Check for duplicate SKU
                    $existingSku = $this->variantModel->getBySku($_POST['sku']);
                    if ($existingSku && $existingSku['id'] != $variantId) {
                        throw new Exception('SKU đã tồn tại');
                    }
                    $updateData['sku'] = trim($_POST['sku']);
                }

                // Update variant
                $db = Database::getInstance();
                $setParts = [];
                $params = [];

                foreach ($updateData as $key => $value) {
                    $setParts[] = "{$key} = :{$key}";
                    $params[$key] = $value;
                }

                $params['id'] = $variantId;
                $sql = "UPDATE product_variants SET " . implode(', ', $setParts) . " WHERE id = :id";

                $db->execute($sql, $params);

                $_SESSION['success'] = 'Cập nhật biến thể thành công';

            } catch (Exception $e) {
                error_log("Error updating variant: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
            }
        }

        $this->redirect("/5s-fashion/admin/products/{$productId}/variants");
    }

    /**
     * Delete variant
     */
    public function delete($productId, $variantId)
    {
        try {
            // Get existing variant
            $variant = $this->variantModel->find($variantId);
            if (!$variant || $variant['product_id'] != $productId) {
                throw new Exception('Không tìm thấy biến thể');
            }

            // Delete variant (cascade will handle related records)
            $db = Database::getInstance();
            $sql = "DELETE FROM product_variants WHERE id = :id";
            $db->execute($sql, ['id' => $variantId]);

            // Check if product still has variants
            $remainingVariants = $this->variantModel->getByProduct($productId);
            if (empty($remainingVariants)) {
                // Disable variants for product
                $productObj = new Product();
                $productObj->id = $productId;
                $productObj->disableVariants();
            }

            $_SESSION['success'] = 'Xóa biến thể thành công';

        } catch (Exception $e) {
            error_log("Error deleting variant: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect("/5s-fashion/admin/products/{$productId}/variants");
    }

    /**
     * Generate variants automatically from selected attributes
     */
    public function generateVariants($productId)
    {
        error_log("generateVariants called for product {$productId}");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("POST data received: " . print_r($_POST, true));

            $db = Database::getInstance();

            try {
                $product = $this->productModel->find($productId);
                if (!$product) {
                    throw new Exception('Không tìm thấy sản phẩm');
                }

                // Get selected attribute values
                $selectedColors = $_POST['selected_colors'] ?? [];
                $selectedSizes = $_POST['selected_sizes'] ?? [];
                $selectedMaterials = $_POST['selected_materials'] ?? [];

                error_log("Selected attributes - Colors: " . implode(',', $selectedColors) .
                         ", Sizes: " . implode(',', $selectedSizes) .
                         ", Materials: " . implode(',', $selectedMaterials));

                // Validate that at least one attribute is selected
                if (empty($selectedColors) && empty($selectedSizes) && empty($selectedMaterials)) {
                    throw new Exception('Vui lòng chọn ít nhất một thuộc tính');
                }                // Default settings
                $baseSku = $_POST['base_sku'] ?? $product['sku'];
                $basePrice = !empty($_POST['base_price']) ? (float)$_POST['base_price'] : null;
                $baseStock = (int)($_POST['base_stock'] ?? 0);

                // Only begin transaction if one isn't already active
                $transactionStarted = false;
                if (!$db->getConnection()->inTransaction()) {
                    $db->beginTransaction();
                    $transactionStarted = true;
                }

                $variantCount = 0;

                // Generate all combinations
                $combinations = $this->generateCombinations([
                    'colors' => $selectedColors,
                    'sizes' => $selectedSizes,
                    'materials' => $selectedMaterials
                ]);

                foreach ($combinations as $combination) {
                    // Build variant name and SKU
                    $variantNameParts = [];
                    $skuParts = [$baseSku];
                    $attributeValueIds = [];

                    foreach ($combination as $type => $valueId) {
                        $attributeValue = $this->attributeValueModel->find($valueId);
                        if ($attributeValue) {
                            $variantNameParts[] = $attributeValue['value'];
                            $skuParts[] = strtoupper(substr($attributeValue['slug'], 0, 3));
                            $attributeValueIds[] = $valueId;
                        }
                    }

                    $variantName = $product['name'] . ' - ' . implode(' - ', $variantNameParts);
                    $variantSku = implode('-', $skuParts);

                    // Check if variant already exists
                    if ($this->variantModel->getBySku($variantSku)) {
                        continue; // Skip existing variants
                    }

                    // Create variant
                    $variantData = [
                        'product_id' => $productId,
                        'sku' => $variantSku,
                        'variant_name' => $variantName,
                        'price' => $basePrice,
                        'sale_price' => null,
                        'cost_price' => null,
                        'stock_quantity' => $baseStock,
                        'weight' => null,
                        'dimensions' => null,
                        'image' => null,
                        'status' => 'active',
                        'sort_order' => $variantCount
                    ];

                    $variantId = ProductVariant::createWithAttributes($variantData, $attributeValueIds);
                    $variantCount++;
                }

                // Enable variants for product
                $sql = "UPDATE products SET has_variants = 1 WHERE id = :id";
                $db->execute($sql, ['id' => $productId]);

                // Only commit if we started the transaction
                if ($transactionStarted) {
                    $db->commit();
                }

                error_log("Successfully created {$variantCount} variants for product {$productId}");
                $_SESSION['success'] = "Tạo thành công {$variantCount} biến thể";

            } catch (Exception $e) {
                // Safely rollback transaction if we started it and it's still active
                if ($transactionStarted && $db->getConnection()->inTransaction()) {
                    try {
                        $db->rollback();
                    } catch (Exception $rollbackException) {
                        error_log("Rollback error: " . $rollbackException->getMessage());
                    }
                }

                error_log("Error generating variants: " . $e->getMessage());
                $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            }
        }

        $this->redirect("/5s-fashion/admin/products/{$productId}/variants");
    }

    /**
     * Generate all combinations from selected attributes
     */
    private function generateCombinations($attributes)
    {
        $combinations = [[]];

        foreach ($attributes as $type => $values) {
            if (!empty($values)) {
                $newCombinations = [];
                foreach ($combinations as $combination) {
                    foreach ($values as $value) {
                        $newCombination = $combination;
                        $newCombination[$type] = $value;
                        $newCombinations[] = $newCombination;
                    }
                }
                $combinations = $newCombinations;
            }
        }

        // Remove empty combinations
        return array_filter($combinations, function($combination) {
            return !empty($combination);
        });
    }

    /**
     * AJAX: Get variant data
     */
    public function getVariantData($variantId)
    {
        header('Content-Type: application/json');

        try {
            $variant = $this->variantModel->getWithAttributes($variantId);

            if (!$variant) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy biến thể']);
                return;
            }

            echo json_encode(['success' => true, 'variant' => $variant]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
