# Kiến Trúc Hệ Thống 5S Fashion

## Kiến trúc logic mô tả luồng xử lý và các lớp (layer) bên trong hệ thống:

### Tầng Giao diện (Presentation Layer)

* **Front-end PHP & JavaScript:**
  * **Views/Components:** Thể hiện từng màn hình, trang (Page), quản lý logic UI và sự kiện người dùng cho các chức năng như:
    * Trang chủ với các danh mục sản phẩm thời trang (trending, mới nhất, khuyến mãi)
    * Trang chi tiết sản phẩm với hình ảnh, mô tả, giá, biến thể và đánh giá
    * Trang giỏ hàng và thanh toán
    * Trang quản lý tài khoản cá nhân và đơn hàng
    * Trang quản trị viên (admin) quản lý hệ thống (sản phẩm, người dùng, danh mục, đơn hàng)
  * **JavaScript Client-side:** Xử lý tương tác người dùng, gửi/nhận dữ liệu qua AJAX:
    * **CartService**: Quản lý giỏ hàng, thêm/xóa/cập nhật sản phẩm
    * **AuthService**: Xử lý đăng nhập/đăng ký, quản lý phiên người dùng
    * **ProductService**: Hiển thị sản phẩm, lọc, tìm kiếm
    * **CheckoutService**: Xử lý quy trình thanh toán
    * **WishlistService**: Quản lý danh sách yêu thích
  * **Router:** Định tuyến sang từng màn hình với phân quyền:
    * Route công khai: Trang chủ, sản phẩm, đăng nhập, đăng ký
    * Route người dùng: Giỏ hàng, đơn hàng, danh sách yêu thích, tài khoản
    * Route admin: Dashboard quản trị, quản lý sản phẩm, đơn hàng, người dùng

* **Nhiệm vụ:**
  * Hiển thị giao diện mua sắm thời trang, xử lý thao tác xem sản phẩm, quản lý giỏ hàng, đặt hàng, tìm kiếm sản phẩm, đăng nhập/đăng ký người dùng, và cung cấp giao diện quản trị hệ thống.

### Tầng Dịch vụ & Nghiệp vụ (Business Logic Layer)

* **PHP Controller & Services:**
  * **Controllers:** Tiếp nhận và xử lý request từ người dùng:
    * **HomeController**: Hiển thị trang chủ và danh mục sản phẩm
    * **ProductController**: Quản lý hiển thị sản phẩm và chi tiết
    * **CartController**: Xử lý thao tác giỏ hàng
    * **OrderController**: Quản lý quy trình đặt hàng
    * **AuthController**: Đăng nhập, đăng ký, xác thực email
    * **PaymentController**: Xử lý thanh toán qua VNPay
    * **ReviewController**: Quản lý đánh giá sản phẩm
    * **WishlistController**: Quản lý danh sách yêu thích
    * **AdminController**: Quản lý hệ thống cho admin
  * **Services:** Thực hiện nghiệp vụ cốt lõi của hệ thống thương mại điện tử:
    * **ProductService**: Quản lý sản phẩm, thuộc tính và biến thể
    * **OrderService**: Xử lý đơn hàng, trạng thái, hóa đơn
    * **UserService**: Quản lý tài khoản, profile
    * **CategoryService**: Phân loại sản phẩm theo danh mục
    * **VoucherService**: Quản lý mã giảm giá và khuyến mãi
    * **PaymentService**: Tích hợp với cổng thanh toán VNPay
    * **GoogleAuthService**: Xác thực qua Google OAuth
  * **Security (JWT & Auth Middleware):**
    * Kiểm tra phân quyền User/Admin
    * Xác thực người dùng qua JWT token và session
    * Bảo vệ API endpoints theo vai trò
    * Xác thực email người dùng

* **Nhiệm vụ:**
  * Đảm bảo quy trình nghiệp vụ e-commerce (quản lý sản phẩm, giỏ hàng, đơn hàng, tài khoản), thực hiện các logic phức tạp như tính toán giá, xử lý thanh toán online, gửi email xác nhận đơn hàng, và quản lý khuyến mãi.

### Tầng Truy xuất dữ liệu (Data Access Layer)

* **Models & Database:**
  * **Models:** Sử dụng custom ORM tương tác với MySQL Database:
    * **UserModel**: Truy xuất thông tin người dùng và xác thực
    * **ProductModel**: Quản lý database sản phẩm và biến thể
    * **OrderModel**: Xử lý đơn hàng và chi tiết đơn hàng
    * **CategoryModel**: Truy xuất danh mục sản phẩm
    * **ReviewModel**: Quản lý đánh giá sản phẩm
    * **CouponModel**: Xử lý mã giảm giá
    * **WishlistModel**: Quản lý danh sách yêu thích
    * **SettingModel**: Cấu hình hệ thống
  * **Entities:**
    * **User**: Thông tin tài khoản người dùng (id, username, email, role)
    * **Product**: Sản phẩm (id, name, price, description, category_id)
    * **ProductVariant**: Biến thể sản phẩm (id, product_id, sku, price, stock)
    * **Order**: Đơn hàng (id, user_id, status, payment_method, total)
    * **OrderDetail**: Chi tiết đơn hàng (order_id, product_id, variant_id, quantity, price)
    * **Category**: Danh mục sản phẩm (id, name, parent_id)
    * **Review**: Đánh giá sản phẩm (id, product_id, user_id, rating, comment)
    * **Coupon**: Mã giảm giá (id, code, discount, valid_until)
    * **Wishlist**: Danh sách yêu thích (user_id, product_id)

* **Nhiệm vụ:**
  * Giao tiếp với MySQL database, thực hiện CRUD operations, đảm bảo tính toàn vẹn dữ liệu sản phẩm và thông tin người dùng, tối ưu hóa query cho hiệu suất trang web.

### Tầng Hệ thống & Tích hợp

* **Web Server (WAMP):**
  * Apache Web Server: Phục vụ ứng dụng PHP
  * MySQL Database Server: Lưu trữ dữ liệu
  * PHP 7.4+: Ngôn ngữ back-end

* **CSDL MySQL:**
  * Lưu toàn bộ dữ liệu liên quan đến:
    * Người dùng và phân quyền (users, roles)
    * Danh mục sản phẩm (categories)
    * Sản phẩm và biến thể (products, product_variants, product_attributes)
    * Đơn hàng (orders, order_details)
    * Đánh giá sản phẩm (reviews)
    * Khuyến mãi và mã giảm giá (coupons)

* **External Services:**
  * VNPay: Tích hợp thanh toán trực tuyến
  * Google OAuth: Đăng nhập bằng tài khoản Google
  * PHPMailer: Gửi email xác thực tài khoản và thông báo đơn hàng

## Front-end (MVC Views)

* **Vai trò:**
  * Đây là lớp giao diện người dùng (frontend) cho hệ thống 5S Fashion. Giao diện được xây dựng bằng PHP, HTML, CSS (Bootstrap), JavaScript và jQuery, hiển thị trong trình duyệt của người dùng.

* **Chức năng:**
  * **Hiển thị giao diện:** Cung cấp giao diện tương tác cho mua sắm thời trang, hiển thị danh sách sản phẩm, danh mục, chi tiết sản phẩm, giỏ hàng và quy trình thanh toán.
  * **Tương tác với người dùng:** Thu thập dữ liệu từ người dùng (form đăng nhập, đánh giá sản phẩm, thông tin thanh toán) và xử lý sự kiện (thêm vào giỏ hàng, thêm vào yêu thích, đặt hàng).
  * **Gọi API/Controller:** Gửi các yêu cầu đến back-end để:
    * Xem và tìm kiếm sản phẩm
    * Xử lý giỏ hàng và thanh toán
    * Xác thực người dùng và phân quyền
    * Quản lý đơn hàng và danh sách yêu thích
    * Thống kê và báo cáo cho admin

## PHP Back-end (Controller → Service → Model)

* **Vai trò:**
  * Đây là lớp Back-end của hệ thống 5S Fashion, chịu trách nhiệm xử lý logic nghiệp vụ và quản lý giao tiếp với cơ sở dữ liệu.

* **Kiến trúc nội bộ:**
  * **Controller:** Tiếp nhận yêu cầu HTTP từ views, thực hiện các thao tác kiểm tra ban đầu và định tuyến:
    * **AuthController:** Xử lý đăng nhập/đăng ký, xác thực email
    * **HomeController:** Hiển thị trang chủ và danh mục
    * **ProductController:** Hiển thị sản phẩm và chi tiết
    * **CartController:** Quản lý giỏ hàng
    * **OrderController:** Quản lý đơn hàng và thanh toán
    * **ApiController:** API endpoints cho AJAX và mobile (nếu có)
    * **AdminController:** Quản lý hệ thống cho admin

  * **Service:** Chứa logic nghiệp vụ chính của hệ thống thương mại điện tử:
    * Xử lý giỏ hàng và tính toán giá
    * Quản lý quy trình thanh toán và tích hợp VNPay
    * Xử lý mã giảm giá và chương trình khuyến mãi
    * Quản lý quyền truy cập theo vai trò (User/Admin)
    * Validation dữ liệu và bảo mật

  * **Model:** Lớp truy cập dữ liệu, sử dụng custom ORM để:
    * Thực hiện CRUD operations trên MySQL database
    * Tối ưu hóa query cho tìm kiếm và lọc sản phẩm
    * Quản lý relationships giữa các entities
    * Tracking đơn hàng và analytics

## MySQL Database

* **Vai trò:**
  * Đây là cơ sở dữ liệu chính lưu trữ toàn bộ dữ liệu của hệ thống 5S Fashion.

* **Chức năng:**
  * **Lưu trữ dữ liệu:** Dữ liệu được lưu theo các bảng đã chuẩn hóa:
    * **users:** Thông tin tài khoản và phân quyền
    * **products:** Thông tin sản phẩm (tên, mô tả, giá, hình ảnh)
    * **product_variants:** Biến thể sản phẩm (kích cỡ, màu sắc)
    * **product_attributes:** Thuộc tính sản phẩm
    * **categories:** Danh mục sản phẩm
    * **orders:** Đơn hàng của người dùng
    * **order_details:** Chi tiết đơn hàng
    * **reviews:** Đánh giá sản phẩm
    * **coupons:** Mã giảm giá
    * **wishlists:** Danh sách yêu thích

  * **Đảm bảo tính toàn vẹn dữ liệu:**
    * Foreign key constraints giữa các bảng liên quan
    * Indexes cho search performance
    * Triggers cho trạng thái đơn hàng và tồn kho

## Luồng Dữ liệu Tổng thể

* **Từ Front-end MVC Views:**
  * Người dùng tương tác với giao diện mua sắm trên trình duyệt
  * Khi thực hiện thao tác (xem sản phẩm, thêm vào giỏ hàng), request được gửi đến Controller
  * AJAX calls cho các thao tác không yêu cầu refresh trang

* **Tại PHP Back-end:**
  * **Controller:**
    * Nhận requests từ views
    * Validate dữ liệu đầu vào và phân quyền
    * Route requests tới service phù hợp

  * **Service:**
    * Xử lý business logic cho thương mại điện tử
    * Tính toán giá và áp dụng khuyến mãi
    * Xử lý thanh toán và cập nhật trạng thái đơn hàng
    * Gửi email xác nhận

  * **Model:**
    * Query MySQL để lấy dữ liệu sản phẩm
    * Cập nhật giỏ hàng và đơn hàng
    * Quản lý tồn kho và trạng thái sản phẩm
    * Tracking số liệu cho admin dashboard

* **MySQL Database:**
  * Xử lý queries cho tìm kiếm và lọc sản phẩm
  * Lưu trữ và truy xuất thông tin sản phẩm, đơn hàng
  * Duy trì dữ liệu người dùng và lịch sử mua hàng
  * Đảm bảo tính nhất quán dữ liệu giữa các người dùng đồng thời

* **Trả kết quả về cho Views:**
  * Dữ liệu sản phẩm, giỏ hàng, đơn hàng được truyền vào views
  * Views render HTML cho người dùng
  * AJAX responses cập nhật UI động
  * Thông báo kết quả các thao tác (thành công, lỗi)

## Đặc điểm Kỹ thuật của Hệ thống

* **Performance Optimizations:**
  * **Caching:** Sử dụng caching cho danh mục sản phẩm và trang chủ
  * **Lazy Loading:** Tải hình ảnh sản phẩm khi cần
  * **Database Indexing:** Tối ưu hóa queries tìm kiếm sản phẩm

* **Security Measures:**
  * **JWT Authentication:** Bảo mật phiên người dùng và API access
  * **Role-based Access Control:** Phân quyền User/Admin/Vendor
  * **Form Validation:** Kiểm tra dữ liệu đầu vào
  * **CSRF Protection:** Chống tấn công Cross-Site Request Forgery
  * **Password Hashing:** Bảo vệ mật khẩu người dùng

* **Scalability Considerations:**
  * **Module-based Architecture:** Tách biệt các thành phần của hệ thống
  * **API Endpoints:** Sẵn sàng cho tích hợp mobile app trong tương lai
  * **Database Optimization:** Thiết kế schema cho việc mở rộng dữ liệu
