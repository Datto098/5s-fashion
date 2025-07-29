# BUG FIX: Session vs JWT Authentication Inconsistency

## Vấn đề:
- AuthApiController.php đang mixing session-based authentication với JWT
- Method check() đang sử dụng $_SESSION['user'] thay vì JWT token
- Gây confusion và security issues

## Cần fix:
1. File: app/controllers/api/AuthApiController.php - method check()
2. Sử dụng JWT token consistently trong API endpoints
3. Session chỉ dùng cho web interface, JWT cho API

## Code cần sửa:
```php
// Thay thế method check() hiện tại
public function check()
{
    try {
        $currentUser = JWT::getCurrentUser();

        if (!$currentUser) {
            ApiResponse::success([
                'authenticated' => false
            ], 'User is not authenticated');
            return;
        }

        // Get fresh user data from database
        $stmt = $this->pdo->prepare("
            SELECT id, full_name, email, phone, role, status, created_at, updated_at
            FROM users
            WHERE id = :id AND status = 'active'
        ");
        $stmt->execute([':id' => $currentUser['sub']]);
        $user = $stmt->fetch();

        if (!$user) {
            ApiResponse::success([
                'authenticated' => false
            ], 'User not found or inactive');
            return;
        }

        ApiResponse::success([
            'authenticated' => true,
            'user' => $this->formatUser($user)
        ], 'User is authenticated');

    } catch (Exception $e) {
        ApiResponse::success([
            'authenticated' => false
        ], 'Invalid token');
    }
}
```
