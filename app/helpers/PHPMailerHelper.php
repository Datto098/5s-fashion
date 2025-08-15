<?php
// Simple PHPMailer wrapper for Gmail SMTP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class PHPMailerHelper {
    /**
     * Gửi email chung (dùng cho liên hệ, thông báo...)
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề
     * @param string $body Nội dung HTML
     * @param string|null $toName Tên người nhận (tùy chọn)
     * @return bool
     */
    public static function sendMail($to, $subject, $body, $toName = null) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '22211tt2029@mail.tdc.edu.vn';
            $mail->Password   = 'kpjy vikb dqmx wnmy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('22211tt2029@mail.tdc.edu.vn', '5S Fashion');
            if ($toName) {
                $mail->addAddress($to, $toName);
            } else {
                $mail->addAddress($to);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer error: ' . $mail->ErrorInfo);
            return false;
        }
    }
    public static function sendVerificationEmail($to, $toName, $verifyUrl) {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '22211tt2029@mail.tdc.edu.vn'; 
            $mail->Password   = 'kpjy vikb dqmx wnmy';  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            //Recipients
            $mail->setFrom('22211tt2029@mail.tdc.edu.vn', '5S Fashion');
            $mail->addAddress($to, $toName);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Xác thực email đăng ký 5S Fashion';
            $mail->Body    = '<p>Chào ' . htmlspecialchars($toName) . ',</p>' .
                '<p>Vui lòng nhấn vào liên kết dưới đây để xác thực email của bạn:</p>' .
                '<p><a href="' . $verifyUrl . '">' . $verifyUrl . '</a></p>' .
                '<p>Nếu bạn không đăng ký tài khoản, vui lòng bỏ qua email này.</p>';

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
