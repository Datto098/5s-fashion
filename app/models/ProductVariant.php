<?php

class ProductVariant extends BaseModel
{
    protected $table = 'product_variants';
    protected $fillable = [
        'product_id', 'sku', 'variant_name', 'price', 'sale_price', 'cost_price',
        'stock_quantity', 'reserved_quantity', 'weight', 'dimensions',
        'image', 'gallery', 'status', 'sort_order'
    ];

    /**
     * Get product for this variant
     */
    public function getProduct()
    {
        $sql = "SELECT * FROM products WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $this->product_id ?? 0]);
    }

    /**
     * Get variant attributes with values
     */
    public function getAttributes()
    {
        $sql = "
            SELECT pva.*, pav.value, pav.color_code, pav.image as attribute_image,
                   pa.name as attribute_name, pa.type as attribute_type
            FROM product_variant_attributes pva
            JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
            JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pva.variant_id = :variant_id
            ORDER BY pa.sort_order, pav.sort_order
        ";
        return $this->db->fetchAll($sql, ['variant_id' => $this->id ?? 0]);
    }

    /**
     * Get variants by product ID
     */
    public static function getByProduct($productId, $activeOnly = true)
    {
        $db = Database::getInstance();
        $whereClause = $activeOnly ? "AND status = 'active'" : '';
        $sql = "SELECT * FROM product_variants WHERE product_id = :product_id {$whereClause} ORDER BY sort_order";
        return $db->fetchAll($sql, ['product_id' => $productId]);
    }

    /**
     * Get variant with attributes
     */
    public static function getWithAttributes($variantId)
    {
        $db = Database::getInstance();

        // Get variant info
        $variant = $db->fetchOne("SELECT * FROM product_variants WHERE id = :id", ['id' => $variantId]);

        if (!$variant) {
            return null;
        }

        // Get attributes
        $sql = "
            SELECT pva.*, pav.value, pav.color_code, pav.image as attribute_image,
                   pa.name as attribute_name, pa.type as attribute_type
            FROM product_variant_attributes pva
            JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
            JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pva.variant_id = :variant_id
            ORDER BY pa.sort_order, pav.sort_order
        ";
        $variant['attributes'] = $db->fetchAll($sql, ['variant_id' => $variantId]);

        return $variant;
    }

    /**
     * Get product variants with full details
     */
    public static function getProductVariantsWithDetails($productId)
    {
        $db = Database::getInstance();
        $sql = "
            SELECT pv.*,
                   GROUP_CONCAT(
                       CONCAT(pa.name, ': ', pav.value)
                       ORDER BY pa.sort_order
                       SEPARATOR ', '
                   ) as variant_attributes,
                   GROUP_CONCAT(
                       CONCAT(pa.type, ':', pav.id, ':', pav.value, ':', IFNULL(pav.color_code, ''))
                       ORDER BY pa.sort_order
                       SEPARATOR '|'
                   ) as attribute_details
            FROM product_variants pv
            LEFT JOIN product_variant_attributes pva ON pv.id = pva.variant_id
            LEFT JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
            LEFT JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pv.product_id = :product_id
            GROUP BY pv.id
            ORDER BY pv.sort_order
        ";
        return $db->fetchAll($sql, ['product_id' => $productId]);
    }

    /**
     * Check available stock
     */
    public function getAvailableStock()
    {
        return max(0, ($this->stock_quantity ?? 0) - ($this->reserved_quantity ?? 0));
    }

    /**
     * Reserve stock for order
     */
    public function reserveStock($quantity)
    {
        if ($this->getAvailableStock() < $quantity) {
            return false;
        }

        $sql = "UPDATE product_variants SET reserved_quantity = reserved_quantity + :quantity WHERE id = :id";
        return $this->db->execute($sql, ['quantity' => $quantity, 'id' => $this->id]);
    }

    /**
     * Release reserved stock
     */
    public function releaseStock($quantity)
    {
        $sql = "UPDATE product_variants SET reserved_quantity = GREATEST(0, reserved_quantity - :quantity) WHERE id = :id";
        return $this->db->execute($sql, ['quantity' => $quantity, 'id' => $this->id]);
    }

    /**
     * Update actual stock (after sale completion)
     */
    public function updateStock($quantity, $operation = 'decrease')
    {
        if ($operation === 'decrease') {
            $sql = "UPDATE product_variants SET
                        stock_quantity = GREATEST(0, stock_quantity - :quantity),
                        reserved_quantity = GREATEST(0, reserved_quantity - :quantity)
                    WHERE id = :id";
        } else {
            $sql = "UPDATE product_variants SET stock_quantity = stock_quantity + :quantity WHERE id = :id";
        }

        return $this->db->execute($sql, ['quantity' => $quantity, 'id' => $this->id]);
    }

    /**
     * Get variant by SKU
     */
    public static function getBySku($sku)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM product_variants WHERE sku = :sku LIMIT 1";
        return $db->fetchOne($sql, ['sku' => $sku]);
    }

    /**
     * Create variant with attributes
     */
    public static function createWithAttributes($variantData, $attributeValueIds)
    {
        $db = Database::getInstance();

        try {
            // Only begin transaction if one isn't already active
            $transactionStarted = false;
            if (!$db->getConnection()->inTransaction()) {
                $db->beginTransaction();
                $transactionStarted = true;
            }

            // Create variant
            $sql = "INSERT INTO product_variants (product_id, sku, variant_name, price, sale_price, cost_price, stock_quantity, weight, dimensions, image, status, sort_order)
                    VALUES (:product_id, :sku, :variant_name, :price, :sale_price, :cost_price, :stock_quantity, :weight, :dimensions, :image, :status, :sort_order)";

            $db->execute($sql, $variantData);
            $variantId = $db->lastInsertId();

            // Đảm bảo không lưu nhiều thuộc tính cùng loại
            $attributesByType = [];

            // Lấy type của từng attribute value và nhóm theo type
            foreach ($attributeValueIds as $attributeValueId) {
                $sql = "SELECT pav.id, pa.type
                        FROM product_attribute_values pav
                        JOIN product_attributes pa ON pav.attribute_id = pa.id
                        WHERE pav.id = :id";
                $attributeInfo = $db->fetchOne($sql, ['id' => $attributeValueId]);

                if ($attributeInfo) {
                    $type = $attributeInfo['type'] ?? 'other';
                    // Chỉ lưu một thuộc tính cho mỗi loại
                    if (!isset($attributesByType[$type])) {
                        $attributesByType[$type] = $attributeValueId;
                    }
                }
            }

            // Lưu các thuộc tính đã được lọc
            foreach ($attributesByType as $attributeValueId) {
                $sql = "INSERT INTO product_variant_attributes (variant_id, attribute_value_id) VALUES (:variant_id, :attribute_value_id)";
                $db->execute($sql, [
                    'variant_id' => $variantId,
                    'attribute_value_id' => $attributeValueId
                ]);
            }

            // Only commit if we started the transaction
            if ($transactionStarted) {
                $db->commit();
            }
            return $variantId;

        } catch (Exception $e) {
            // Only rollback if we started the transaction
            if ($transactionStarted && $db->getConnection()->inTransaction()) {
                $db->rollback();
            }
            throw $e;
        }
    }
}
