<?php

/**
 * JWT Helper Class
 * Simple JWT implementation for API authentication
 */
class JWT
{
    private static $secret = 'your-secret-key-change-in-production';
    private static $algorithm = 'HS256';

    /**
     * Generate JWT token
     */
    public static function encode($payload, $expiry = 86400)
    {
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];

        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;

        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Decode and verify JWT token
     */
    public static function decode($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

        $header = json_decode(self::base64UrlDecode($headerEncoded), true);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);

        if (!$header || !$payload) {
            throw new Exception('Invalid token data');
        }

        // Verify signature
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secret, true);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception('Invalid token signature');
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token has expired');
        }

        return $payload;
    }

    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get current user from token
     */
    public static function getCurrentUser()
    {
        try {
            $token = self::getTokenFromHeader();
            if (!$token) {
                return null;
            }

            $payload = self::decode($token);
            return $payload;

        } catch (Exception $e) {
            return null;
        }
    }
}

?>
