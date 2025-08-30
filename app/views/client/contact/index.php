<?php
require_once __DIR__ . '/../../../helpers/PHPMailerHelper.php';

$successMsg = $errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate đơn giản
    if ($name && $email && $message) {
        $subject = "Liên hệ mới từ $name";
        $body = "<h3>Thông tin liên hệ:</h3>"
            . "<p><strong>Họ tên:</strong> $name</p>"
            . "<p><strong>Email:</strong> $email</p>"
            . ($phone ? "<p><strong>Số điện thoại:</strong> $phone</p>" : "")
            . "<p><strong>Nội dung:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";
        $adminEmail = '22211tt2029@mail.tdc.edu.vn'; 
        $mailer = new PHPMailerHelper();
        $result = $mailer->sendMail($adminEmail, $subject, $body);
        if ($result === true) {
            $successMsg = 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.';
        } else {
            $errorMsg = 'Gửi liên hệ thất bại. Vui lòng thử lại sau!';
        }
    } else {
        $errorMsg = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    }
}
?>
<div class="container p-5">
    <div class="contact-section">
        <h1 class="text-center mb-4">Gửi liên hệ cho chúng tôi</h1>
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success text-center"> <?= $successMsg ?> </div>
        <?php elseif (!empty($errorMsg)): ?>
            <div class="alert alert-danger text-center"> <?= $errorMsg ?> </div>
        <?php endif; ?>
        <form id="contactForm" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Gửi liên hệ</button>
            </div>
        </form>
    </div>
</div>
<?php
// Get content from buffer
$content = ob_get_clean();

// Set page variables for layout
$title = 'Liên hệ - zone Fashion';
$meta_description = 'Liên hệ tại zone Fashion';


// Include main layout
include VIEW_PATH . '/client/layouts/app.php';
?>