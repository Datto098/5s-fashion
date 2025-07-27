<?php

/**
 * API Authentication Middleware
 * Handles JWT token validation
 */
class ApiAuthMiddleware
{
    /**
     * Handle authentication
     */
    public function handle()
    {
        $token = $this->getBearerToken();

        if (!$token) {
            ApiResponse::unauthorized('Token is required');
        }

        $decoded = $this->validateToken($token);

        if (!$decoded) {
            ApiResponse::unauthorized('Invalid or expired token');
        }

        // Store user data in global scope for use in controllers
        $GLOBALS['api_user'] = $decoded;
    }

    /**
     * Get bearer token from Authorization header
     */
    private function getBearerToken()
    {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];

            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Validate JWT token
     * @param string $token
     * @return array|false
     */
    private function validateToken($token)
    {
        try {
            // Simple token validation - in production use proper JWT library
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                return false;
            }

            // Decode payload (base64)
            $payload = base64_decode($parts[1]);
            $data = json_decode($payload, true);

            if (!$data) {
                return false;
            }

            // Check expiration
            if (isset($data['exp']) && $data['exp'] < time()) {
                return false;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate JWT token (simplified version)
     * @param array $payload
     * @return string
     */
    public static function generateToken($payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Add expiration (24 hours)
        $payload['exp'] = time() + (24 * 60 * 60);
        $payload['iat'] = time();

        $payloadJson = json_encode($payload);

        $base64Header = base64_encode($header);
        $base64Payload = base64_encode($payloadJson);

        // Simple signature (in production use proper HMAC)
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, 'your-secret-key', true);
        $base64Signature = base64_encode($signature);

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }
}
