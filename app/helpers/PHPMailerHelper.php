<?php
// Simple PHPMailer wrapper for Gmail SMTP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class PHPMailerHelper {
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
