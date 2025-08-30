<?php

class Setting extends BaseModel
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $fillable = ['key', 'value', 'category', 'type', 'label', 'description', 'options'];

    /**
     * Get all settings grouped by category
     */
    public function getAllGrouped()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY `group`, `key`";
        $allSettings = $this->db->fetchAll($sql);

        $grouped = [];
        foreach ($allSettings as $setting) {
            $grouped[$setting['group']][] = $setting;
        }

        return $grouped;
    }

    /**
     * Get setting value by key
     */
    public function getValue($key, $default = null)
    {
        $sql = "SELECT value FROM {$this->table} WHERE `key` = ?";
        $result = $this->db->fetchOne($sql, [$key]);

        return $result ? $result['value'] : $default;
    }

    /**
     * Update setting value
     */
    public function updateValue($key, $value)
    {
        $sql = "UPDATE {$this->table} SET value = ?, updated_at = NOW() WHERE `key` = ?";
        return $this->db->execute($sql, [$value, $key]);
    }

    /**
     * Reset category to default values
     */
    public function resetCategory($group)
    {
        // Get default values for the group
        $defaults = $this->getDefaultSettings();

        if (!isset($defaults[$group])) {
            return false;
        }

        $success = true;
        foreach ($defaults[$group] as $key => $defaultValue) {
            if (!$this->updateValue($key, $defaultValue)) {
                $success = false;
            }
        }

        return $success;
    }    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        $defaults = $this->getDefaultSettings();

        foreach ($defaults as $group => $settings) {
            foreach ($settings as $key => $config) {
                $existing = $this->getValue($key);
                if ($existing === null) {
                    $this->create([
                        'key' => $key,
                        'value' => $config['default'],
                        'group' => $group,
                        'type' => $config['type'] ?? 'string'
                    ]);
                }
            }
        }
    }

    /**
     * Get default settings configuration
     */
    private function getDefaultSettings()
    {
        return [
            'general' => [
                'site_name' => [
                    'default' => 'zone Fashion',
                    'type' => 'string'
                ],
                'site_description' => [
                    'default' => 'Website thời trang hàng đầu Việt Nam',
                    'type' => 'text'
                ],
                'site_keywords' => [
                    'default' => 'thời trang, quần áo, giày dép, phụ kiện',
                    'type' => 'string'
                ],
                'site_logo' => [
                    'default' => '',
                    'type' => 'string'
                ],
                'site_favicon' => [
                    'default' => '',
                    'type' => 'string'
                ]
            ],
            'contact' => [
                'contact_address' => [
                    'default' => '123 Đường ABC, Quận 1, TP.HCM',
                    'type' => 'text'
                ],
                'contact_phone' => [
                    'default' => '0123 456 789',
                    'type' => 'string'
                ],
                'contact_email' => [
                    'default' => 'info@zonefashion.com',
                    'type' => 'string'
                ],
                'working_hours' => [
                    'default' => 'Thứ 2 - Chủ nhật: 8:00 - 22:00',
                    'type' => 'string'
                ]
            ],
            'social' => [
                'facebook_url' => [
                    'default' => '',
                    'type' => 'string'
                ],
                'instagram_url' => [
                    'default' => '',
                    'type' => 'string'
                ],
                'youtube_url' => [
                    'default' => '',
                    'type' => 'string'
                ],
                'tiktok_url' => [
                    'default' => '',
                    'type' => 'string'
                ]
            ],
            'ecommerce' => [
                'currency' => [
                    'default' => 'VND',
                    'type' => 'string'
                ],
                'tax_rate' => [
                    'default' => '10',
                    'type' => 'string'
                ],
                'shipping_fee' => [
                    'default' => '30000',
                    'type' => 'string'
                ],
                'free_shipping_threshold' => [
                    'default' => '500000',
                    'type' => 'string'
                ]
            ]
        ];
    }
}
