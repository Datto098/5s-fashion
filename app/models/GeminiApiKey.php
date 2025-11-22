<?php
/**
 * Gemini API Key Model
 * zone Fashion E-commerce Platform
 * 
 * Manages Gemini AI API keys for chatbot functionality
 * Handles CRUD operations, status tracking, and usage monitoring
 */

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/User.php';

class GeminiApiKey
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all API keys with creator information and pagination
     */
    public function getAll($search = '', $filters = [], $page = 1, $limit = 20)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = ['1=1'];
            $params = [];

            // Search by name or notes
            if (!empty($search)) {
                $whereConditions[] = "(g.name LIKE ? OR g.notes LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }

            // Filter by status
            if (!empty($filters['status'])) {
                $whereConditions[] = "g.status = ?";
                $params[] = $filters['status'];
            }

            // Filter by test status
            if (!empty($filters['test_status'])) {
                $whereConditions[] = "g.last_test_status = ?";
                $params[] = $filters['test_status'];
            }

            $whereClause = implode(' AND ', $whereConditions);
            $params[] = $limit;
            $params[] = $offset;

            $sql = "SELECT 
                        g.*,
                        u.full_name as creator_name,
                        u.email as creator_email,
                        CASE 
                            WHEN g.last_test_status = 'failed' THEN 'warning'
                            WHEN g.last_test_status = 'success' THEN 'success'
                            ELSE 'pending'
                        END as health_status,
                        CASE 
                            WHEN g.daily_limit > 0 THEN ROUND((g.current_daily_usage / g.daily_limit) * 100, 2)
                            ELSE 0
                        END as daily_usage_percent,
                        CASE 
                            WHEN g.monthly_limit > 0 THEN ROUND((g.current_monthly_usage / g.monthly_limit) * 100, 2)
                            ELSE 0
                        END as monthly_usage_percent
                    FROM gemini_api_keys g 
                    LEFT JOIN users u ON g.created_by = u.id 
                    WHERE {$whereClause}
                    ORDER BY g.created_at DESC
                    LIMIT ? OFFSET ?";

            return $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            error_log("Error getting API keys: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of API keys for pagination
     */
    public function getCount($search = '', $filters = [])
    {
        try {
            $whereConditions = ['1=1'];
            $params = [];

            if (!empty($search)) {
                $whereConditions[] = "(name LIKE ? OR notes LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }

            if (!empty($filters['status'])) {
                $whereConditions[] = "status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['test_status'])) {
                $whereConditions[] = "last_test_status = ?";
                $params[] = $filters['test_status'];
            }

            $whereClause = implode(' AND ', $whereConditions);
            $sql = "SELECT COUNT(*) as count FROM gemini_api_keys WHERE {$whereClause}";

            $result = $this->db->fetchOne($sql, $params);
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log("Error counting API keys: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get API key by ID
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT 
                        g.*,
                        u.full_name as creator_name,
                        u.email as creator_email,
                        CASE 
                            WHEN g.last_test_status = 'failed' THEN 'warning'
                            WHEN g.last_test_status = 'success' THEN 'success'
                            ELSE 'pending'
                        END as health_status
                    FROM gemini_api_keys g 
                    LEFT JOIN users u ON g.created_by = u.id 
                    WHERE g.id = ?";
            
            return $this->db->fetchOne($sql, [$id]);
        } catch (Exception $e) {
            error_log("Error getting API key by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new API key
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO gemini_api_keys (
                        name, api_key, status, daily_limit, monthly_limit, 
                        notes, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $data['name'],
                $data['api_key'],
                $data['status'] ?? 'active',
                $data['daily_limit'] ?? 1000,
                $data['monthly_limit'] ?? 30000,
                $data['notes'] ?? '',
                $data['created_by']
            ];

            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Error creating API key: " . $e->getMessage());
            throw new Exception("Không thể tạo API key: " . $e->getMessage());
        }
    }

    /**
     * Update API key
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        name = ?, 
                        api_key = ?, 
                        status = ?, 
                        daily_limit = ?, 
                        monthly_limit = ?, 
                        notes = ?,
                        updated_at = NOW()
                    WHERE id = ?";

            $params = [
                $data['name'],
                $data['api_key'],
                $data['status'],
                $data['daily_limit'] ?? 1000,
                $data['monthly_limit'] ?? 30000,
                $data['notes'] ?? '',
                $id
            ];

            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Error updating API key: " . $e->getMessage());
            throw new Exception("Không thể cập nhật API key: " . $e->getMessage());
        }
    }

    /**
     * Delete API key
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM gemini_api_keys WHERE id = ?";
            return $this->db->execute($sql, [$id]);
        } catch (Exception $e) {
            error_log("Error deleting API key: " . $e->getMessage());
            throw new Exception("Không thể xóa API key: " . $e->getMessage());
        }
    }

    /**
     * Get active API key for use (load balancing)
     */
    public function getActiveKey()
    {
        try {
            $sql = "SELECT * FROM gemini_api_keys 
                    WHERE status = 'active' 
                      AND (daily_limit = 0 OR current_daily_usage < daily_limit)
                      AND (monthly_limit = 0 OR current_monthly_usage < monthly_limit)
                      AND (last_test_status IS NULL OR last_test_status = 'success')
                    ORDER BY 
                      current_daily_usage ASC,
                      current_monthly_usage ASC,
                      last_used_at ASC
                    LIMIT 1";
            
            return $this->db->fetchOne($sql);
        } catch (Exception $e) {
            error_log("Error getting active API key: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update usage statistics for API key
     */
    public function updateUsage($id, $incrementDaily = 1, $incrementMonthly = 1)
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        current_daily_usage = current_daily_usage + ?,
                        current_monthly_usage = current_monthly_usage + ?,
                        usage_count = usage_count + ?,
                        last_used_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?";

            return $this->db->execute($sql, [$incrementDaily, $incrementMonthly, $incrementDaily, $id]);
        } catch (Exception $e) {
            error_log("Error updating API key usage: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset daily usage counters (should be run daily via cron)
     */
    public function resetDailyUsage()
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        current_daily_usage = 0,
                        updated_at = NOW()";

            return $this->db->execute($sql);
        } catch (Exception $e) {
            error_log("Error resetting daily usage: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset monthly usage counters (should be run monthly via cron)
     */
    public function resetMonthlyUsage()
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        current_monthly_usage = 0,
                        updated_at = NOW()";

            return $this->db->execute($sql);
        } catch (Exception $e) {
            error_log("Error resetting monthly usage: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get API keys statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_keys' => 0,
                'active_keys' => 0,
                'expired_keys' => 0,
                'expiring_soon' => 0,
                'total_usage_today' => 0,
                'total_usage_month' => 0
            ];

            // Basic counts
            $sql = "SELECT 
                        COUNT(*) as total_keys,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_keys,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as error_keys,
                        SUM(CASE WHEN last_test_status = 'failed' THEN 1 ELSE 0 END) as failed_tests,
                        SUM(current_daily_usage) as total_usage_today,
                        SUM(current_monthly_usage) as total_usage_month
                    FROM gemini_api_keys";

            $result = $this->db->fetchOne($sql);
            if ($result) {
                $stats = array_merge($stats, $result);
            }

            return $stats;
        } catch (Exception $e) {
            error_log("Error getting API keys statistics: " . $e->getMessage());
            return $stats;
        }
    }

    /**
     * Test API key validity
     */
    public function testApiKey($apiKey)
    {
        try {
            // Use the correct endpoint for Gemini Pro
            $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=' . $apiKey;
            $testData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Hello, this is a test message. Please respond with "API key is working"']
                        ]
                    ]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);

            // SSL configuration
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            // Use certificate bundle if available
            $certPath = APP_PATH . '/certs/cacert.pem';
            if (file_exists($certPath)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_CAINFO, $certPath);
            }

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ['success' => false, 'message' => 'cURL Error: ' . $error];
            }

            if ($httpCode !== 200) {
                $errorMsg = 'HTTP Error: ' . $httpCode;
                if ($result) {
                    $errorData = json_decode($result, true);
                    if (isset($errorData['error']['message'])) {
                        $errorMsg .= ' - ' . $errorData['error']['message'];
                    }
                }
                return ['success' => false, 'message' => $errorMsg];
            }

            $data = json_decode($result, true);
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return ['success' => true, 'message' => 'API key hoạt động bình thường'];
            }

            // Check for other response formats
            if (isset($data['error'])) {
                return ['success' => false, 'message' => 'API Error: ' . $data['error']['message']];
            }

            return ['success' => false, 'message' => 'Phản hồi không hợp lệ từ API'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi test API: ' . $e->getMessage()];
        }
    }

    /**
     * Update test status for API key
     */
    public function updateTestStatus($keyId, $status, $errorMessage = null)
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        last_test_at = NOW(),
                        last_test_status = ?,
                        last_error_message = ?,
                        updated_at = NOW()
                    WHERE id = ?";

            return $this->db->execute($sql, [$status, $errorMessage, $keyId]);
        } catch (Exception $e) {
            throw new Exception("Error updating test status: " . $e->getMessage());
        }
    }

    /**
     * Execute raw SQL query
     */
    public function executeQuery($sql, $params = [])
    {
        try {
            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Database query error: " . $e->getMessage());
        }
    }

    /**
     * Fetch single row with raw SQL
     */
    public function fetchOne($sql, $params = [])
    {
        try {
            return $this->db->fetchOne($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Database fetch error: " . $e->getMessage());
        }
    }
}