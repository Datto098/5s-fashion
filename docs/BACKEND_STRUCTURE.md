# Cấu Trúc Thư Mục Back-end 5S Fashion

## Hình 4.1.4: Kiến trúc mã nguồn Back-end

### Giải thích chi tiết từng thư mục

| Tên thư mục | Mục đích | Ví dụ |
|-------------|----------|-------|
| **app/** | Thư mục chính chứa toàn bộ logic ứng dụng | core/ - Lõi hệ thống<br>controllers/ - Điều khiển<br>models/ - Mô hình dữ liệu |
| **app/core/** | Chứa các class cốt lõi của framework | App.php - Bootstrap ứng dụng<br>Database.php - Kết nối CSDL<br>Router.php - Định tuyến |
| **app/core/App.php** | Class chính khởi tạo và điều phối ứng dụng | Khởi tạo router<br>Xử lý request<br>Load configuration |
| **app/core/Database.php** | Quản lý kết nối và thao tác cơ sở dữ liệu | PDO connection<br>Query builder<br>Transaction handling |
| **app/core/Router.php** | Xử lý định tuyến URL và phân phối request | Route matching<br>Controller loading<br>Parameter parsing |
| **app/core/Controller.php** | Base class cho tất cả controllers | Load views<br>Redirect methods<br>Session handling |
| **app/core/Model.php** | Base class cho tất cả models | CRUD operations<br>Database interaction<br>Validation rules |
| **app/core/ApiController.php** | Base controller cho API endpoints | JSON responses<br>HTTP status codes<br>API authentication |
| **app/controllers/** | Chứa tất cả logic điều khiển ứng dụng | HomeController.php - Trang chủ<br>AuthController.php - Xác thực<br>ProductController.php - Sản phẩm |
| **app/controllers/BaseController.php** | Controller cơ sở với các method chung | Authentication check<br>Permission validation<br>Common utilities |
| **app/controllers/HomeController.php** | Xử lý logic trang chủ | Hiển thị sản phẩm nổi bật<br>Banner slideshow<br>Danh mục hot |
| **app/controllers/AuthController.php** | Quản lý xác thực người dùng | login() - Đăng nhập<br>register() - Đăng ký<br>logout() - Đăng xuất |
| **app/controllers/ProductController.php** | Quản lý sản phẩm | index() - Danh sách<br>show() - Chi tiết<br>search() - Tìm kiếm |
| **app/controllers/OrderController.php** | Xử lý đơn hàng | create() - Tạo đơn<br>track() - Theo dõi<br>history() - Lịch sử |
| **app/controllers/PaymentController.php** | Xử lý thanh toán | vnpay() - VNPay gateway<br>callback() - Xử lý callback<br>verify() - Xác thực |
| **app/controllers/CartController.php** | Quản lý giỏ hàng | add() - Thêm sản phẩm<br>update() - Cập nhật<br>remove() - Xóa |
| **app/controllers/WishlistController.php** | Quản lý danh sách yêu thích | add() - Thêm yêu thích<br>remove() - Bỏ yêu thích<br>list() - Xem danh sách |
| **app/controllers/ReviewController.php** | Quản lý đánh giá sản phẩm | create() - Tạo đánh giá<br>moderate() - Kiểm duyệt<br>like() - Like review |
| **app/controllers/GoogleAuthController.php** | Xác thực qua Google OAuth | login() - Đăng nhập Google<br>callback() - Xử lý callback<br>profile() - Lấy thông tin |
| **app/controllers/admin/** | Controllers dành cho quản trị viên | DashboardController.php<br>ProductsController.php<br>OrdersController.php |
| **app/controllers/admin/DashboardController.php** | Bảng điều khiển admin | index() - Tổng quan<br>stats() - Thống kê<br>charts() - Biểu đồ |
| **app/controllers/admin/ProductsController.php** | Quản lý sản phẩm (Admin) | index() - Danh sách<br>create() - Thêm mới<br>edit() - Chỉnh sửa |
| **app/controllers/admin/OrdersController.php** | Quản lý đơn hàng (Admin) | index() - Danh sách đơn<br>updateStatus() - Cập nhật<br>print() - In hóa đơn |
| **app/controllers/admin/CategoriesController.php** | Quản lý danh mục | CRUD operations<br>Tree structure<br>Parent-child relationship |
| **app/controllers/admin/CustomersController.php** | Quản lý khách hàng | index() - Danh sách<br>view() - Chi tiết<br>orders() - Đơn hàng |
| **app/controllers/admin/CouponsController.php** | Quản lý mã giảm giá | create() - Tạo coupon<br>validate() - Kiểm tra<br>usage() - Thống kê dùng |
| **app/controllers/admin/AnalyticsController.php** | Báo cáo và phân tích | sales() - Doanh số<br>products() - Sản phẩm<br>customers() - Khách hàng |
| **app/models/** | Chứa các model tương tác với database | User.php - Người dùng<br>Product.php - Sản phẩm<br>Order.php - Đơn hàng |
| **app/models/User.php** | Model người dùng | authenticate() - Xác thực<br>register() - Đăng ký<br>updateProfile() - Cập nhật |
| **app/models/Product.php** | Model sản phẩm | getAll() - Lấy tất cả<br>getByCategory() - Theo danh mục<br>search() - Tìm kiếm |
| **app/models/ProductVariant.php** | Model biến thể sản phẩm | getByProduct() - Theo sản phẩm<br>updateStock() - Cập nhật kho<br>getPrice() - Lấy giá |
| **app/models/Order.php** | Model đơn hàng | create() - Tạo đơn<br>updateStatus() - Cập nhật<br>getByUser() - Theo user |
| **app/models/Cart.php** | Model giỏ hàng | addItem() - Thêm item<br>updateQuantity() - Cập nhật<br>getTotal() - Tính tổng |
| **app/models/Category.php** | Model danh mục | getTree() - Cây danh mục<br>getProducts() - Sản phẩm<br>getParents() - Danh mục cha |
| **app/models/Review.php** | Model đánh giá | create() - Tạo đánh giá<br>moderate() - Kiểm duyệt<br>getByProduct() - Theo sản phẩm |
| **app/models/Coupon.php** | Model mã giảm giá | validate() - Kiểm tra hợp lệ<br>apply() - Áp dụng<br>getDiscount() - Tính giảm |
| **app/models/Wishlist.php** | Model danh sách yêu thích | add() - Thêm<br>remove() - Xóa<br>getByUser() - Theo user |
| **app/config/** | Chứa các file cấu hình hệ thống | database.php - CSDL<br>app.php - Ứng dụng<br>vnpay.php - Thanh toán |
| **app/config/database.php** | Cấu hình kết nối cơ sở dữ liệu | Host, username, password<br>Database name<br>Connection options |
| **app/config/app.php** | Cấu hình chung ứng dụng | App name, URL<br>Timezone, locale<br>Debug mode |
| **app/config/vnpay.php** | Cấu hình VNPay payment gateway | Merchant ID<br>Secret key<br>Return URLs |
| **app/config/google.php** | Cấu hình Google OAuth | Client ID<br>Client Secret<br>Redirect URI |
| **app/config/constants.php** | Định nghĩa các hằng số | Status codes<br>Error messages<br>Default values |
| **app/helpers/** | Chứa các class và function hỗ trợ | Validator.php - Validation<br>JWT.php - Token<br>FileUploader.php - Upload |
| **app/helpers/Validator.php** | Class validation dữ liệu | validateEmail() - Email<br>validatePhone() - SĐT<br>sanitize() - Làm sạch |
| **app/helpers/JWT.php** | Xử lý JSON Web Token | encode() - Mã hóa<br>decode() - Giải mã<br>verify() - Xác thực |
| **app/helpers/FileUploader.php** | Upload và quản lý file | upload() - Tải lên<br>resize() - Thay đổi kích thước<br>validate() - Kiểm tra |
| **app/helpers/PHPMailerHelper.php** | Gửi email | sendMail() - Gửi email<br>setTemplate() - Template<br>addAttachment() - Đính kèm |
| **app/helpers/VNPayHelper.php** | Tích hợp VNPay | createPayment() - Tạo thanh toán<br>verifyReturn() - Xác thực<br>getStatus() - Trạng thái |
| **app/helpers/GoogleAuthHelper.php** | Tích hợp Google Auth | getAuthUrl() - URL xác thực<br>getToken() - Lấy token<br>getUserInfo() - Thông tin user |
| **app/helpers/functions.php** | Các function tiện ích chung | formatPrice() - Format giá<br>generateSlug() - Tạo slug<br>timeAgo() - Thời gian |
| **app/middleware/** | Chứa các middleware xử lý request | AuthMiddleware.php - Xác thực<br>ApiAuthMiddleware.php - API auth |
| **app/middleware/AuthMiddleware.php** | Middleware kiểm tra đăng nhập | checkLogin() - Kiểm tra<br>checkRole() - Phân quyền<br>redirect() - Chuyển hướng |
| **app/middleware/ApiAuthMiddleware.php** | Middleware cho API | validateToken() - Kiểm tra token<br>checkPermission() - Quyền<br>handleError() - Xử lý lỗi |
| **app/routes/** | Định nghĩa các route của ứng dụng | web.php - Web routes<br>client_routes.php - Client<br>menu_routes.php - Menu |
| **app/routes/web.php** | Routes chính của ứng dụng | GET, POST routes<br>Route groups<br>Middleware assignment |
| **app/routes/client_routes.php** | Routes dành cho client | Home, product routes<br>Cart, checkout<br>User account |
| **app/routes/menu_routes.php** | Routes cho menu động | Category routes<br>Dynamic menu<br>Breadcrumb |
| **app/api/** | API endpoints và logic | routes.php - API routes<br>Responses - JSON responses |
| **app/api/routes.php** | Định nghĩa API routes | RESTful endpoints<br>API versioning<br>Rate limiting |
| **app/logs/** | Thư mục chứa log files | error.log - Lỗi hệ thống<br>access.log - Truy cập<br>debug.log - Debug |
| **vendor/** | Thư mục composer dependencies | autoload.php - Autoloader<br>Third-party packages<br>Framework libraries |

### Mô tả chi tiết các thành phần chính:

#### 1. **Core Framework (app/core/)**
- **App.php**: Điểm khởi đầu, khởi tạo router và xử lý request
- **Database.php**: Singleton pattern cho kết nối DB, query builder
- **Router.php**: URL routing với support cho RESTful routes
- **Controller/Model**: Base classes với common functionality

#### 2. **MVC Architecture**
- **Controllers**: Xử lý business logic, validate input, call models
- **Models**: Data layer, database operations, business rules
- **Views**: Presentation layer (trong thư mục views/)

#### 3. **Configuration Management (app/config/)**
- **Environment-based**: Khác nhau cho dev/staging/production
- **Security**: Sensitive data được encrypt hoặc ENV variables
- **Modular**: Tách riêng từng service (database, payment, auth)

#### 4. **Helper Classes (app/helpers/)**
- **Utilities**: Common functions được tái sử dụng
- **Third-party Integration**: VNPay, Google Auth, PHPMailer
- **Security**: JWT handling, file upload validation

#### 5. **Middleware System (app/middleware/)**
- **Authentication**: Kiểm tra user login status
- **Authorization**: Role-based access control
- **API Security**: Token validation cho API endpoints

#### 6. **Routing System (app/routes/)**
- **Web Routes**: Traditional web application routes
- **API Routes**: RESTful API endpoints
- **Middleware Integration**: Route-level middleware assignment

### Quy ước đặt tên:
- **Controllers**: PascalCase với suffix "Controller"
- **Models**: PascalCase tương ứng với table name
- **Methods**: camelCase mô tả chức năng
- **Files**: snake_case cho config và routes
- **Constants**: UPPER_CASE trong constants.php

### Bảo mật:
- **Input Validation**: Tất cả input được validate
- **SQL Injection**: Sử dụng prepared statements
- **XSS Protection**: Output escaping
- **CSRF Protection**: Token validation
- **Authentication**: Session + JWT cho API
