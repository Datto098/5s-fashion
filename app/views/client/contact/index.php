    <div class="container p-5">
        <div class="contact-section">
            <h1 class="text-center mb-4">Gửi liên hệ cho chúng tôi</h1>
            <form id="contactForm" method="post" action="/5s-fashion/contact/send">
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
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
    $title = 'Liên hệ - 5S Fashion';
    $meta_description = 'Liên hệ tại 5S Fashion';


    // Include main layout
    include VIEW_PATH . '/client/layouts/app.php';
    ?>