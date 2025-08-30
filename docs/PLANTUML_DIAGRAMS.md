# PlantUML Diagrams for 5S Fashion Use Cases

## Tổng quan

Tài liệu này chứa tất cả các biểu đồ PlantUML đã được tạo cho các use case trong hệ thống 5S Fashion. Mỗi use case có hai loại biểu đồ:
- **Class Diagram**: Mô tả cấu trúc lớp và mối quan hệ giữa các thành phần
- **Sequence Diagram**: Mô tả luồng tương tác và trình tự thực hiện

## Danh sách các biểu đồ

### 1. Đăng ký tài khoản (User Registration)
- **Class Diagram**: `docs/diagrams/user-registration-class.puml`
- **Sequence Diagram**: `docs/diagrams/user-registration-sequence.puml`

**Mô tả**: Biểu đồ mô tả quá trình đăng ký tài khoản mới, bao gồm validation, tạo user, gửi email xác thực.

### 2. Đăng nhập (User Login)
- **Class Diagram**: `docs/diagrams/user-login-class.puml`
- **Sequence Diagram**: `docs/diagrams/user-login-sequence.puml`

**Mô tả**: Biểu đồ mô tả quá trình đăng nhập thông thường và đăng nhập qua Google OAuth.

### 3. Xem và tìm kiếm sản phẩm (Product Search and View)
- **Class Diagram**: `docs/diagrams/product-search-class.puml`
- **Sequence Diagram**: `docs/diagrams/product-search-sequence.puml`

**Mô tả**: Biểu đồ mô tả việc xem danh sách sản phẩm, tìm kiếm, lọc và xem chi tiết sản phẩm.

### 4. Thêm sản phẩm vào giỏ hàng (Add to Cart)
- **Class Diagram**: `docs/diagrams/add-to-cart-class.puml`
- **Sequence Diagram**: `docs/diagrams/add-to-cart-sequence.puml`

**Mô tả**: Biểu đồ mô tả quá trình thêm sản phẩm vào giỏ hàng, kiểm tra tồn kho, và cập nhật giỏ hàng.

### 5. Thanh toán đơn hàng (Checkout Process)
- **Class Diagram**: `docs/diagrams/checkout-process-class.puml`
- **Sequence Diagram**: `docs/diagrams/checkout-process-sequence.puml`

**Mô tả**: Biểu đồ mô tả quy trình thanh toán, tạo đơn hàng, xử lý thanh toán VNPay và COD.

### 6. Quản lý sản phẩm (Product Management - Admin)
- **Class Diagram**: `docs/diagrams/product-management-class.puml`
- **Sequence Diagram**: `docs/diagrams/product-management-sequence.puml`

**Mô tả**: Biểu đồ mô tả các chức năng quản lý sản phẩm của admin: thêm, sửa, xóa, quản lý biến thể.

### 7. Quản lý đơn hàng (Order Management - Admin)
- **Class Diagram**: `docs/diagrams/order-management-class.puml`
- **Sequence Diagram**: `docs/diagrams/order-management-sequence.puml`

**Mô tả**: Biểu đồ mô tả việc admin quản lý đơn hàng: xem, cập nhật trạng thái, hủy, in hóa đơn.

### 8. Đánh giá sản phẩm (Product Review)
- **Class Diagram**: `docs/diagrams/product-review-class.puml`
- **Sequence Diagram**: `docs/diagrams/product-review-sequence.puml`

**Mô tả**: Biểu đồ mô tả việc khách hàng đánh giá sản phẩm và admin kiểm duyệt đánh giá.

## Cách sử dụng

### 1. Xem biểu đồ trong VS Code
- Cài đặt extension "PlantUML" trong VS Code
- Mở file `.puml` và sử dụng preview để xem biểu đồ

### 2. Tạo hình ảnh từ PlantUML
- Sử dụng PlantUML online: http://www.plantuml.com/plantuml/
- Sử dụng PlantUML command line tool
- Export từ VS Code extension

### 3. Tích hợp vào tài liệu
```bash
# Cài đặt PlantUML (yêu cầu Java)
npm install -g node-plantuml

# Tạo hình ảnh PNG
plantuml docs/diagrams/*.puml
```

## Mô tả các thành phần chính

### Controllers
- **AuthController**: Xử lý đăng nhập, đăng ký
- **ProductController**: Quản lý sản phẩm (client)
- **CartController**: Quản lý giỏ hàng
- **OrderController**: Xử lý đơn hàng
- **PaymentController**: Xử lý thanh toán
- **ReviewController**: Quản lý đánh giá
- **Admin Controllers**: Các controller dành cho admin

### Models
- **User**: Người dùng
- **Product**: Sản phẩm
- **ProductVariant**: Biến thể sản phẩm
- **Category**: Danh mục
- **Cart**: Giỏ hàng
- **Order**: Đơn hàng
- **OrderDetail**: Chi tiết đơn hàng
- **Review**: Đánh giá sản phẩm
- **Coupon**: Mã giảm giá

### Helpers
- **Validator**: Validation dữ liệu
- **FileUploader**: Upload file
- **PHPMailerHelper**: Gửi email
- **VNPayHelper**: Tích hợp VNPay
- **GoogleAuthHelper**: Tích hợp Google OAuth
- **JWT**: Xử lý JSON Web Token

### Core
- **Database**: Quản lý kết nối CSDL
- **Router**: Định tuyến
- **App**: Bootstrap ứng dụng

## Quy ước trong biểu đồ

### Class Diagram
- `!define ENTITY class`: Định nghĩa entity/model
- `!define CONTROLLER class`: Định nghĩa controller
- `!define SERVICE class`: Định nghĩa service/helper
- `-->`: Quan hệ sử dụng/phụ thuộc
- `--`: Quan hệ kế thừa

### Sequence Diagram
- `actor`: Người dùng/Actor
- `participant`: Các thành phần hệ thống
- `activate/deactivate`: Kích hoạt/vô hiệu hóa
- `alt/else/end`: Điều kiện rẽ nhánh
- `loop/end`: Vòng lặp
- `note over`: Ghi chú

## Lưu ý kỹ thuật

1. **Transaction Management**: Các biểu đồ sequence đều mô tả việc sử dụng database transaction
2. **Error Handling**: Bao gồm các trường hợp lỗi và exception handling
3. **Security**: Mô tả các bước authentication và authorization
4. **Performance**: Tối ưu hóa query và caching được thể hiện trong biểu đồ
5. **Integration**: Tích hợp với external services (VNPay, Google, Email)

## Cập nhật biểu đồ

Khi có thay đổi trong hệ thống, cần cập nhật các biểu đồ tương ứng:
1. Thêm/sửa/xóa class trong class diagram
2. Cập nhật sequence trong sequence diagram
3. Đảm bảo tính nhất quán giữa các biểu đồ
4. Kiểm tra và test biểu đồ sau khi thay đổi
