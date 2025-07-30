<?php

class ProductAttribute extends BaseModel
{
    protected $table = 'product_attributes';
    protected $fillable = [
        'name', 'slug', 'type', 'description', 'sort_order', 'status'
    ];

    /**
     * Get attribute values for this attribute
     */
    public function getValues($activeOnly = true)
    {
        $whereClause = $activeOnly ? "AND status = 'active'" : '';
        $sql = "SELECT * FROM product_attribute_values WHERE attribute_id = :attribute_id {$whereClause} ORDER BY sort_order";
        return $this->db->fetchAll($sql, ['attribute_id' => $this->id ?? 0]);
    }

    /**
     * Get active attribute values by attribute ID
     */
    public static function getActiveValues($attributeId)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM product_attribute_values WHERE attribute_id = :attribute_id AND status = 'active' ORDER BY sort_order";
        return $db->fetchAll($sql, ['attribute_id' => $attributeId]);
    }

    /**
     * Get attributes by type
     */
    public static function getByType($type)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM product_attributes WHERE type = :type AND status = 'active' ORDER BY sort_order";
        return $db->fetchAll($sql, ['type' => $type]);
    }

    /**
     * Get color attributes with values
     */
    public static function getColorsWithValues()
    {
        $db = Database::getInstance();
        $sql = "
            SELECT pa.*,
                   JSON_ARRAYAGG(
                       JSON_OBJECT(
                           'id', pav.id,
                           'value', pav.value,
                           'slug', pav.slug,
                           'color_code', pav.color_code,
                           'image', pav.image,
                           'sort_order', pav.sort_order
                       )
                   ) as `values`
            FROM product_attributes pa
            LEFT JOIN product_attribute_values pav ON pa.id = pav.attribute_id AND pav.status = 'active'
            WHERE pa.type = 'color' AND pa.status = 'active'
            GROUP BY pa.id
            ORDER BY pa.sort_order
        ";
        return $db->fetchAll($sql);
    }

    /**
     * Get size attributes with values
     */
    public static function getSizesWithValues()
    {
        $db = Database::getInstance();
        $sql = "
            SELECT pa.*,
                   JSON_ARRAYAGG(
                       JSON_OBJECT(
                           'id', pav.id,
                           'value', pav.value,
                           'slug', pav.slug,
                           'sort_order', pav.sort_order
                       )
                   ) as `values`
            FROM product_attributes pa
            LEFT JOIN product_attribute_values pav ON pa.id = pav.attribute_id AND pav.status = 'active'
            WHERE pa.type = 'size' AND pa.status = 'active'
            GROUP BY pa.id
            ORDER BY pa.sort_order
        ";
        return $db->fetchAll($sql);
    }
}
