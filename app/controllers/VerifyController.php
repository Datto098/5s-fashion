<?php
/**
 * VerifyController: Xác thực email đăng ký
 */
class VerifyController extends Controller
{
    public function email($token)
    {
        $userModel = $this->model('User');
        $user = $userModel->findBy('email_verify_token', $token);
        if (!$user) {
            setFlash('error', 'Liên kết xác thực không hợp lệ hoặc đã hết hạn.');
            redirect('login');
        }
        // Đánh dấu đã xác thực
        $userModel->update($user['id'], [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'email_verify_token' => null
        ]);
        setFlash('success', 'Xác thực email thành công! Bạn có thể đăng nhập.');
        redirect('login');
    }
}
