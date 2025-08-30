# Cấu Trúc Thư Mục Front-end 5S Fashion

## Hình 4.1.3: Kiến trúc mã nguồn Front-end

### Giải thích chi tiết từng thư mục

| Tên thư mục | Mục đích | Ví dụ |
|-------------|----------|-------|
| **public/** | Chứa các file công khai, điểm vào chính của ứng dụng | index.php - Điểm vào chính<br>admin.php - Điểm vào admin<br>api.php - API endpoints |
| **public/assets/** | Lưu trữ các tài nguyên tĩnh (CSS, JS, hình ảnh) | css/ - Stylesheet files<br>js/ - JavaScript files<br>images/ - Hình ảnh, icon |
| **public/assets/css/** | Chứa các file CSS cho giao diện | bootstrap.min.css - Framework CSS<br>style.css - CSS tùy chỉnh<br>admin.css - CSS cho admin |
| **public/assets/js/** | Chứa các file JavaScript | jquery.min.js - Thư viện jQuery<br>bootstrap.min.js - Bootstrap JS<br>main.js - JavaScript chính |
| **public/assets/images/** | Lưu trữ hình ảnh tĩnh của website | logo.png - Logo website<br>banners/ - Hình banner<br>icons/ - Biểu tượng |
| **public/uploads/** | Thư mục chứa file upload từ người dùng | products/ - Hình sản phẩm<br>categories/ - Hình danh mục<br>posts/ - Hình bài viết |
| **app/views/** | Chứa tất cả các template và view files | client/ - Views cho người dùng<br>admin/ - Views cho quản trị<br>components/ - Thành phần dùng chung |
| **app/views/client/** | Views dành cho người dùng cuối | home/ - Trang chủ<br>product/ - Trang sản phẩm<br>cart/ - Giỏ hàng |
| **app/views/client/layouts/** | Layout chính cho client | header.php - Phần đầu trang<br>footer.php - Phần cuối trang<br>sidebar.php - Thanh bên |
| **app/views/client/home/** | Các view của trang chủ | index.php - Trang chủ chính<br>featured.php - Sản phẩm nổi bật<br>categories.php - Danh mục |
| **app/views/client/product/** | Views liên quan đến sản phẩm | index.php - Danh sách sản phẩm<br>detail.php - Chi tiết sản phẩm<br>search.php - Tìm kiếm |
| **app/views/client/cart/** | Views cho giỏ hàng | index.php - Xem giỏ hàng<br>add.php - Thêm vào giỏ<br>update.php - Cập nhật giỏ |
| **app/views/client/checkout/** | Views cho thanh toán | index.php - Trang thanh toán<br>payment.php - Chọn thanh toán<br>success.php - Thanh toán thành công |
| **app/views/client/auth/** | Views xác thực người dùng | login.php - Đăng nhập<br>register.php - Đăng ký<br>forgot.php - Quên mật khẩu |
| **app/views/client/account/** | Views quản lý tài khoản | profile.php - Thông tin cá nhân<br>orders.php - Đơn hàng<br>wishlist.php - Yêu thích |
| **app/views/client/order/** | Views quản lý đơn hàng | index.php - Danh sách đơn hàng<br>detail.php - Chi tiết đơn hàng<br>track.php - Theo dõi đơn |
| **app/views/admin/** | Views dành cho quản trị viên | dashboard/ - Bảng điều khiển<br>products/ - Quản lý sản phẩm<br>orders/ - Quản lý đơn hàng |
| **app/views/admin/layouts/** | Layout cho admin panel | header.php - Header admin<br>sidebar.php - Menu bên<br>footer.php - Footer admin |
| **app/views/admin/dashboard/** | Views bảng điều khiển admin | index.php - Trang chính admin<br>stats.php - Thống kê<br>charts.php - Biểu đồ |
| **app/views/admin/products/** | Quản lý sản phẩm | index.php - Danh sách sản phẩm<br>create.php - Thêm sản phẩm<br>edit.php - Sửa sản phẩm |
| **app/views/admin/orders/** | Quản lý đơn hàng | index.php - Danh sách đơn hàng<br>detail.php - Chi tiết đơn<br>update.php - Cập nhật trạng thái |
| **app/views/admin/categories/** | Quản lý danh mục | index.php - Danh sách danh mục<br>create.php - Thêm danh mục<br>edit.php - Sửa danh mục |
| **app/views/admin/customers/** | Quản lý khách hàng | index.php - Danh sách khách<br>detail.php - Chi tiết khách<br>orders.php - Đơn hàng khách |
| **app/views/admin/coupons/** | Quản lý mã giảm giá | index.php - Danh sách coupon<br>create.php - Tạo mã giảm giá<br>edit.php - Sửa coupon |
| **app/views/admin/users/** | Quản lý người dùng hệ thống | index.php - Danh sách user<br>create.php - Tạo user mới<br>permissions.php - Phân quyền |
| **app/views/admin/settings/** | Cấu hình hệ thống | general.php - Cài đặt chung<br>payment.php - Cài đặt thanh toán<br>email.php - Cài đặt email |
| **app/views/admin/analytics/** | Báo cáo và thống kê | sales.php - Báo cáo doanh số<br>products.php - Thống kê sản phẩm<br>customers.php - Phân tích khách |
| **app/views/admin/reviews/** | Quản lý đánh giá | index.php - Danh sách review<br>moderate.php - Kiểm duyệt<br>reports.php - Báo cáo đánh giá |
| **app/views/components/** | Thành phần dùng chung | pagination.php - Phân trang<br>breadcrumb.php - Đường dẫn<br>modal.php - Hộp thoại |
| **app/views/errors/** | Trang lỗi | 404.php - Không tìm thấy<br>500.php - Lỗi server<br>403.php - Không có quyền |
| **app/views/partials/** | Các phần nhỏ có thể tái sử dụng | search-form.php - Form tìm kiếm<br>product-card.php - Card sản phẩm<br>notification.php - Thông báo |

### Mô tả chi tiết các thành phần chính:

#### 1. **Public Directory (public/)**
- **Mục đích**: Thư mục gốc có thể truy cập từ web, chứa điểm vào của ứng dụng
- **Bảo mật**: Chỉ có thư mục này được expose ra internet
- **Cấu trúc**: Tách biệt rõ ràng giữa assets tĩnh và file PHP

#### 2. **Assets (public/assets/)**
- **CSS**: Sử dụng Bootstrap framework kết hợp CSS tùy chỉnh
- **JavaScript**: jQuery, Bootstrap JS và các script tùy chỉnh
- **Images**: Tối ưu hóa cho web, có thể cache bởi browser

#### 3. **Views Structure (app/views/)**
- **Client Views**: Giao diện người dùng cuối với responsive design
- **Admin Views**: Dashboard quản trị với layout riêng biệt
- **Component-based**: Tái sử dụng code qua các component

#### 4. **Upload Management (public/uploads/)**
- **Phân loại**: Chia theo từng loại nội dung (sản phẩm, danh mục, bài viết)
- **Bảo mật**: Kiểm tra file type và kích thước
- **Tối ưu**: Resize và compress hình ảnh tự động

### Quy ước đặt tên:
- **Views**: Sử dụng tên mô tả chức năng (index.php, create.php, edit.php)
- **Assets**: Minify cho production (style.min.css, script.min.js)
- **Images**: Đặt tên mô tả và tối ưu kích thước
- **Components**: Tên ngắn gọn, mô tả chức năng cụ thể
