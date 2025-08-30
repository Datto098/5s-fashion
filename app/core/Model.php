<?php
/**
 * Base Model Class
 * zone Fashion E-commerce Platform
 */

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Find record by column and value
     */
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        return $this->db->fetchOne($sql, ['value' => $value]);
    }

    /**
     * Get all records
     */
    public function all($orderBy = 'id', $direction = 'ASC')
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get records with WHERE condition
     */
    public function where($conditions = [], $orderBy = 'id', $direction = 'ASC')
    {
        $whereClause = '';
        $params = [];

        if (!empty($conditions)) {
            $whereConditions = [];
            foreach ($conditions as $column => $value) {
                $whereConditions[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }

        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY {$orderBy} {$direction}";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Create new record
     */
    public function create($data)
    {
        // Filter only fillable fields
        $filteredData = $this->filterFillable($data);

        // Debug log for Order model
        if ($this->table === 'orders') {
            error_log('[MODEL CREATE] ===== ORDER CREATION DEBUG =====');
            error_log('[MODEL CREATE] Original data keys: ' . implode(', ', array_keys($data)));
            error_log('[MODEL CREATE] Fillable fields: ' . implode(', ', $this->fillable));
            error_log('[MODEL CREATE] Original data: ' . print_r($data, true));
            error_log('[MODEL CREATE] Filtered data: ' . print_r($filteredData, true));
            error_log('[MODEL CREATE] Status in original: ' . (isset($data['status']) ? $data['status'] : 'NOT SET'));
            error_log('[MODEL CREATE] Status in filtered: ' . (isset($filteredData['status']) ? $filteredData['status'] : 'NOT SET'));
        }

        // Add timestamps
        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        // Debug the SQL and parameters for all models, not just orders
        error_log('[MODEL CREATE] Table: ' . $this->table . ', SQL: ' . $sql);
        error_log('[MODEL CREATE] Filtered data: ' . json_encode($filteredData));

        if ($this->db->execute($sql, $filteredData)) {
            $insertId = $this->db->lastInsertId();
            error_log('[MODEL CREATE] Insert ID: ' . $insertId);

            // Get the inserted record for all models
            $insertedRecord = $this->find($insertId);
            
            if ($insertedRecord) {
                error_log('[MODEL CREATE] Record created successfully. ID: ' . $insertId);
                return $insertedRecord;
            } else {
                error_log('[MODEL CREATE] WARNING: Could not retrieve created record with ID: ' . $insertId);
                // Return a minimal record with at least the ID
                return ['id' => (int)$insertId];
            }
        }

        error_log('[MODEL CREATE] ERROR: Failed to insert record into ' . $this->table);
        return false;
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        // Filter only fillable fields
        $filteredData = $this->filterFillable($data);

        // Add updated timestamp
        if ($this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = [];
        foreach ($filteredData as $column => $value) {
            $setClause[] = "{$column} = :{$column}";
        }

        $filteredData['id'] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = :id";

        error_log("DEBUG Model Update - Table: {$this->table}, ID: {$id}");
        error_log("DEBUG Model Update - SQL: {$sql}");
        error_log("DEBUG Model Update - Data: " . json_encode($filteredData));

        if ($this->db->execute($sql, $filteredData)) {
            return $this->find($id);
        }

        return false;
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Count total records
     */
    public function count($conditions = [])
    {
        $whereClause = '';
        $params = [];

        if (!empty($conditions)) {
            $whereConditions = [];
            foreach ($conditions as $column => $value) {
                $whereConditions[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Paginate records
     */
    public function paginate($page = 1, $limit = 15, $conditions = [], $orderBy = 'id', $direction = 'DESC')
    {
        $offset = ($page - 1) * $limit;

        $whereClause = '';
        $params = [];

        if (!empty($conditions)) {
            $whereConditions = [];
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    // Handle IN conditions
                    $placeholders = [];
                    foreach ($value as $i => $val) {
                        $key = $column . '_' . $i;
                        $placeholders[] = ':' . $key;
                        $params[$key] = $val;
                    }
                    $whereConditions[] = "{$column} IN (" . implode(', ', $placeholders) . ")";
                } else {
                    $whereConditions[] = "{$column} = :{$column}";
                    $params[$column] = $value;
                }
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $totalResult = $this->db->fetchOne($countSql, $params);
        $total = $totalResult ? (int)$totalResult['total'] : 0;

        // Get paginated data
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY {$orderBy} {$direction} LIMIT :limit OFFSET :offset";
        $data = $this->db->fetchAll($sql, $params);

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
                'from' => $offset + 1,
                'to' => min($offset + $limit, $total)
            ]
        ];
    }

    /**
     * Filter only fillable fields
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Remove hidden fields from result
     */
    protected function hideFields($data)
    {
        if (empty($this->hidden) || !is_array($data)) {
            return $data;
        }

        return array_diff_key($data, array_flip($this->hidden));
    }

    /**
     * Validate data before insert/update
     */
    protected function validate($data)
    {
        // Override in child classes for specific validation
        return [];
    }

    /**
     * Generate slug from string
     */
    protected function generateSlug($string, $table = null, $column = 'slug')
    {
        $table = $table ?: $this->table;

        // Convert to lowercase and replace spaces/special chars
        $slug = strtolower(trim($string));
        $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $table, $column)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists($slug, $table, $column)
    {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :slug";
        $result = $this->db->fetchOne($sql, ['slug' => $slug]);
        return $result && $result['count'] > 0;
    }
}
