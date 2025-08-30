<!-- Footer -->
<footer class="main-footer bg-dark text-white">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="row py-5">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-section">
                    <h4 class="mb-3 fw-bold text-primary">Zone Fashion</h4>
                    <p class="mb-3">Zone Fashion - Thương hiệu thời trang cao cấp hàng đầu Việt Nam. Chúng tôi mang đến những sản phẩm thời trang chất lượng với phong cách hiện đại và xu hướng mới nhất.</p>

                    <div class="contact-info">
                        <div class="contact-item mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span>123 Nguyễn Huệ, Quận 1, TP.HCM</span>
                        </div>
                        <div class="contact-item mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:1900xxxx" class="text-white text-decoration-none">1900-xxxx</a>
                        </div>
                        <div class="contact-item mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:info@zonefashion.com" class="text-white text-decoration-none">info@zonefashion.com</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5 class="footer-title mb-3">Liên Kết</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="/" class="text-white text-decoration-none">Trang Chủ</a></li>
                        <li><a href="/shop" class="text-white text-decoration-none">Sản Phẩm</a></li>
                        <li><a href="/about" class="text-white text-decoration-none">Về Chúng Tôi</a></li>
                        <li><a href="/contact" class="text-white text-decoration-none">Liên Hệ</a></li>
                        <li><a href="/blog" class="text-white text-decoration-none">Blog</a></li>
                        <li><a href="/size-guide" class="text-white text-decoration-none">Hướng Dẫn Size</a></li>
                    </ul>
                </div>
            </div>

            <!-- Categories -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5 class="footer-title mb-3">Danh Mục</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="/shop?category=ao-nam" class="text-white text-decoration-none">Áo Nam</a></li>
                        <li><a href="/shop?category=quan-nam" class="text-white text-decoration-none">Quần Nam</a></li>
                        <li><a href="/shop?category=ao-nu" class="text-white text-decoration-none">Áo Nữ</a></li>
                        <li><a href="/shop?category=quan-nu" class="text-white text-decoration-none">Quần Nữ</a></li>
                        <li><a href="/shop?category=giay" class="text-white text-decoration-none">Giày Dép</a></li>
                        <li><a href="/shop?category=phu-kien" class="text-white text-decoration-none">Phụ Kiện</a></li>
                    </ul>
                </div>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5 class="footer-title mb-3">Hỗ Trợ</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="/shipping" class="text-white text-decoration-none">Chính Sách Giao Hàng</a></li>
                        <li><a href="/returns" class="text-white text-decoration-none">Đổi Trả</a></li>
                        <li><a href="/warranty" class="text-white text-decoration-none">Bảo Hành</a></li>
                        <li><a href="/privacy" class="text-white text-decoration-none">Bảo Mật</a></li>
                        <li><a href="/terms" class="text-white text-decoration-none">Điều Khoản</a></li>
                        <li><a href="/faq" class="text-white text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter & Social -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-section">
                    <h5 class="footer-title mb-3">Theo Dõi</h5>

                    <!-- Social Links -->
                    <div class="social-links mb-4" style="display: flex">
                        <a href="#" class="social-link me-2" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link me-2" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link me-2" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-link me-2" title="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>

                    <!-- Newsletter -->
                    <div class="newsletter">
                        <p class="mb-2"><small>Đăng ký nhận tin khuyến mãi</small></p>
                        <form action="/newsletter" method="POST" class="newsletter-form">
                            <div class="input-group input-group-sm">
                                <input type="email" class="form-control" name="email" placeholder="Email của bạn" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods & Certifications -->
        <div class="row py-3 border-top border-secondary">
            <div class="col-md-6 mb-3">
                <div class="payment-methods">
                    <h6 class="mb-2">Phương Thức Thanh Toán</h6>
                    <div class="payment-icons">
                        <img src="<?= asset('images/payments/visa.svg') ?>" alt="Visa" class="payment-icon">
                        <img src="<?= asset('images/payments/mastercard.svg') ?>" alt="Mastercard" class="payment-icon">
                        <img src="<?= asset('images/payments/vnpay.svg') ?>" alt="VNPay" class="payment-icon">
                        <img src="<?= asset('images/payments/momo.svg') ?>" alt="MoMo" class="payment-icon">
                        <img src="<?= asset('images/payments/zalopay.svg') ?>" alt="ZaloPay" class="payment-icon">
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="certifications text-md-end">
                    <h6 class="mb-2">Chứng Nhận</h6>
                    <div class="cert-icons">
                        <img src="<?= asset('images/certs/dmca.svg') ?>" alt="DMCA" class="cert-icon">
                        <img src="<?= asset('images/certs/ssl.svg') ?>" alt="SSL" class="cert-icon">
                        <img src="<?= asset('images/certs/bct.svg') ?>" alt="BCT" class="cert-icon">
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row py-3 border-top border-secondary">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?= date('Y') ?> zone Fashion. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <a href="/privacy" class="text-white text-decoration-none me-3">Chính Sách Bảo Mật</a>
                    <a href="/terms" class="text-white text-decoration-none">Điều Khoản Sử Dụng</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-links li {
    margin-bottom: 8px;
}

.footer-links a:hover {
    color: #007bff !important;
    transition: color 0.3s ease;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
}

.payment-icon, .cert-icon {
    height: 30px;
    margin-right: 10px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.payment-icon:hover, .cert-icon:hover {
    opacity: 1;
}

.footer-logo {
    filter: brightness(0) invert(1);
}

.newsletter-form .btn {
    border-radius: 0 0.375rem 0.375rem 0;
}

.contact-item {
    display: flex;
    align-items: center;
}

.contact-item i {
    color: #007bff;
    width: 16px;
}

@media (max-width: 768px) {
    .footer-section {
        text-align: center;
    }

    .payment-methods, .certifications {
        text-align: center !important;
    }
}
</style>
