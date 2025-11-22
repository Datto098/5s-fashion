<?php


require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;

class GoogleAuthHelper
{
    private Google_Client $client;
    private array $config;

    public function __construct()
    {
        // Load Google OAuth config
        $this->config = require APP_PATH . '/config/google.php';
        
        $this->initClient();

        // Prevent CSRF attacks by setting state
        if (!isset($_SESSION['google_auth_state'])) {
            $_SESSION['google_auth_state'] = bin2hex(random_bytes(16));
        }
        $this->client->setState($_SESSION['google_auth_state']);
    }

    private function initClient(): void
    {
        $this->client = new Google_Client();
        $this->client->setClientId($this->config['client_id']);
        $this->client->setClientSecret($this->config['client_secret']);
        $this->client->setRedirectUri($this->config['redirect_uri']);
        $this->client->addScope($this->config['scopes']);
        $this->client->setPrompt('select_account');
        
        // Tắt kiểm tra SSL - Chỉ dành cho môi trường phát triển
        // CHÚ Ý: Trong môi trường production, hãy xóa hoặc comment dòng code dưới đây
        // và cấu hình SSL certificates đúng cách
        $httpClient = new \GuzzleHttp\Client(['verify' => false]);
        $this->client->setHttpClient($httpClient);
        error_log('SSL verification disabled - ONLY FOR DEVELOPMENT');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }
    
    public function getUserInfo(string $code): ?array
    {
        try {
            error_log('===== STARTING GOOGLE AUTH =====');
            error_log('Starting getUserInfo with code: ' . substr($code, 0, 10) . '...');
            
            // SSL settings for debug
            $verify = false;
            error_log('SSL Verification setting: ' . ($verify ? 'ENABLED' : 'DISABLED'));
            
            // Make sure we have a fresh client with the right settings
            $httpClient = new \GuzzleHttp\Client(['verify' => $verify]);
            $this->client->setHttpClient($httpClient);
            
            // Exchange authorization code for access token
            error_log('Attempting to fetch access token with auth code');
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                error_log('Google token error: ' . $token['error']);
                error_log('Error description: ' . ($token['error_description'] ?? 'No description'));
                throw new Exception('Failed to obtain access token: ' . $token['error']);
            }
            
            error_log('Token successfully obtained: ' . json_encode(array_keys($token)));
            $this->client->setAccessToken($token);
            
            try {
                // Get user profile from Google
                error_log('Creating OAuth2 service and fetching user info');
                $oauth2 = new Google_Service_Oauth2($this->client);
                $userInfo = $oauth2->userinfo->get();
                
                error_log('Google user info obtained - Email: ' . $userInfo->getEmail());
    
                // Extract and return user data
                $userData = [
                    'email' => $userInfo->getEmail(),
                    'name' => $userInfo->getName(),
                    'given_name' => $userInfo->getGivenName(),
                    'family_name' => $userInfo->getFamilyName(),
                    'picture' => $userInfo->getPicture(),
                    'google_id' => $userInfo->getId(),
                    'verified_email' => $userInfo->getVerifiedEmail() ? true : false, // Convert to boolean
                    'locale' => $userInfo->getLocale()
                ];
                
                error_log('User data prepared successfully: ' . json_encode(array_keys($userData)));
                return $userData;
            } catch (Exception $e) {
                error_log('Error fetching user profile: ' . $e->getMessage());
                throw $e;
            }

        } catch (Exception $e) {
            error_log('Google Auth Error: ' . $e->getMessage());
            error_log('Error trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    public function verifyState(string $state): bool
    {
        if (isset($_SESSION['google_auth_state']) && $state === $_SESSION['google_auth_state']) {
            // State đã được xác minh, xóa nó đi để tránh sử dụng lại
            unset($_SESSION['google_auth_state']);
            return true;
        }
        return false;
    }
}