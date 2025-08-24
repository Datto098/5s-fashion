<?php

class ProductAttributeValue extends BaseModel
{
    protected $table = 'product_attribute_values';
    protected $fillable = [
        'attribute_id', 'value', 'slug', 'color_code', 'image', 'sort_order', 'status'
    ];

    /**
     * Get attribute for this value
     */
    public function getAttribute()
    {
        $sql = "SELECT * FROM product_attributes WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $this->attribute_id ?? 0]);
    }

    /**
     * Get values by attribute ID
     */
    public static function getByAttribute($attributeId, $activeOnly = true)
    {
        $db = Database::getInstance();
        $whereClause = $activeOnly ? "AND status = 'active'" : '';
        $sql = "SELECT * FROM product_attribute_values WHERE attribute_id = :attribute_id {$whereClause} ORDER BY sort_order";
        return $db->fetchAll($sql, ['attribute_id' => $attributeId]);
    }

    /**
     * Get color values
     */
    public static function getColors()
    {
        $db = Database::getInstance();
        $sql = "
            SELECT pav.*
            FROM product_attribute_values pav
            JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pa.type = 'color' AND pav.status = 'active' AND pa.status = 'active'
            ORDER BY pav.sort_order
        ";
        return $db->fetchAll($sql);
    }

    /**
     * Get attribute type for a value ID
     */
    public function getAttributeType($valueId)
    {
        $sql = "
            SELECT pav.id, pa.id as attribute_id, pa.type, pa.name
            FROM product_attribute_values pav
            JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pav.id = :value_id
            LIMIT 1
        ";
        return $this->db->fetchOne($sql, ['value_id' => $valueId]);
    }

    /**
     * Get size values
     */
    public static function getSizes()
    {
        $db = Database::getInstance();
        $sql = "
            SELECT pav.*
            FROM product_attribute_values pav
            JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pa.type = 'size' AND pav.status = 'active' AND pa.status = 'active'
            ORDER BY pav.sort_order
        ";
        return $db->fetchAll($sql);
    }
}
