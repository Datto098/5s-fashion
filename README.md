# 5S FASHION - NỀN TẢNG THƯƠNG MẠI ĐIỆN TỬ THỜI TRANG

## ĐẶT VẤN ĐỀ

Trong bối cảnh phát triển mạnh mẽ của thương mại điện tử Việt Nam, ngành thời trang đang chiếm một thị phần đáng kể với tốc độ tăng trưởng cao. Tuy nhiên, nhiều doanh nghiệp vừa và nhỏ trong lĩnh vực thời trang vẫn gặp khó khăn trong việc số hóa kinh doanh, thiếu một nền tảng toàn diện, đáp ứng đặc thù của ngành thời trang Việt Nam như quản lý biến thể sản phẩm phức tạp (kích cỡ, màu sắc), tính mùa vụ của sản phẩm, và các phương thức thanh toán phổ biến tại Việt Nam.

Dự án 5S Fashion ra đời nhằm cung cấp một giải pháp toàn diện cho các doanh nghiệp thời trang, từ quy mô nhỏ đến vừa, với đầy đủ tính năng thiết yếu, được tối ưu hóa cho thị trường Việt Nam, và dễ dàng tùy biến theo nhu cầu riêng của từng thương hiệu.

### 1.1. Mô tả bài toán

5S Fashion là một nền tảng thương mại điện tử chuyên biệt cho ngành thời trang, tập trung vào các vấn đề chính sau:

- **Quản lý sản phẩm phức tạp**: Thời trang đòi hỏi khả năng quản lý nhiều biến thể sản phẩm (màu sắc, kích cỡ), thuộc tính đa dạng (chất liệu, mùa, phong cách), và hình ảnh chất lượng cao.

- **Trải nghiệm mua sắm liền mạch**: Thiết kế giao diện thân thiện với người dùng, dễ dàng tìm kiếm, lọc sản phẩm theo nhiều tiêu chí đặc thù của thời trang.

- **Hệ thống thanh toán đa dạng**: Tích hợp các phương thức thanh toán phổ biến tại Việt Nam như VNPay, COD, chuyển khoản ngân hàng.

- **Quản lý đơn hàng và kho vận**: Theo dõi trạng thái đơn hàng, quản lý tồn kho theo biến thể sản phẩm.

- **Marketing và bán hàng**: Hệ thống mã giảm giá, chương trình khuyến mãi, danh sách yêu thích, đánh giá sản phẩm.

### 1.2. Mục đích

Mục tiêu chính của dự án 5S Fashion bao gồm:

1. **Xây dựng nền tảng thương mại điện tử chuyên biệt cho thời trang**: Tạo ra một hệ thống toàn diện với đầy đủ tính năng thiết yếu cho kinh doanh thời trang trực tuyến.

2. **Tối ưu hóa cho thị trường Việt Nam**: Phát triển hệ thống phù hợp với thói quen mua sắm và phương thức thanh toán phổ biến tại Việt Nam.

3. **Đơn giản hóa quy trình vận hành**: Cung cấp giao diện quản trị trực quan, dễ sử dụng cho chủ doanh nghiệp vừa và nhỏ.

4. **Tăng trải nghiệm khách hàng**: Thiết kế giao diện thân thiện với người dùng, tối ưu tốc độ tải trang và trải nghiệm mua sắm trên cả máy tính và thiết bị di động.

5. **Khả năng mở rộng và tùy biến**: Xây dựng kiến trúc cho phép dễ dàng thêm tính năng mới và tùy chỉnh theo nhu cầu của từng thương hiệu.

### 1.3. Quy ước tài liệu

#### 1.3.1. Cấu trúc dự án

Dự án tuân theo mô hình MVC (Model-View-Controller) với cấu trúc thư mục như sau:

```
app/
├── api/           # API endpoints và xử lý
├── config/        # Cấu hình ứng dụng
├── controllers/   # Các controller xử lý logic
├── core/          # Hạt nhân của framework
├── helpers/       # Các hàm tiện ích
├── middleware/    # Middleware xác thực, phân quyền
├── models/        # Mô hình dữ liệu
├── routes/        # Định nghĩa route
└── views/         # Giao diện người dùng
public/
├── assets/        # CSS, JavaScript, hình ảnh
└── uploads/       # Tệp tải lên từ người dùng
vendor/            # Thư viện bên thứ ba
```

#### 1.3.2. Quy ước đặt tên

- **Classes**: PascalCase (ví dụ: `ProductController`, `OrderModel`)
- **Methods**: camelCase (ví dụ: `findBySlug()`, `getActiveProducts()`)
- **Variables**: camelCase (ví dụ: `$productId`, `$orderStatus`)
- **Constants**: UPPER_SNAKE_CASE (ví dụ: `DEFAULT_LIMIT`, `UPLOAD_PATH`)
- **Files**: snake_case hoặc PascalCase tương ứng với nội dung (ví dụ: `client_routes.php`, `ProductController.php`)

#### 1.3.3. Quy ước tài liệu code

- Mỗi class đều có phần mô tả (docblock) với thông tin về chức năng
- Các method có tính phức tạp cao đều được mô tả tham số, kiểu trả về, và chức năng
- Các hàm API đều có mô tả endpoint, method, tham số, và response format

#### 1.3.4. Quy ước phiên bản

Dự án sử dụng Semantic Versioning (SemVer):
- MAJOR: thay đổi API không tương thích ngược
- MINOR: thêm tính năng mới, tương thích ngược
- PATCH: sửa lỗi, tương thích ngược

### 1.4. Các yêu cầu nghiệp vụ

#### 1.4.1. Quản lý sản phẩm

- **Danh mục sản phẩm**: Hệ thống danh mục đa cấp, phân loại sản phẩm theo nhiều tiêu chí (giới tính, mùa, phong cách...)
- **Thuộc tính sản phẩm**: Quản lý thuộc tính động như màu sắc, kích cỡ, chất liệu
- **Biến thể sản phẩm**: Tạo và quản lý biến thể sản phẩm với giá, tồn kho, và hình ảnh riêng
- **Thông tin sản phẩm**: Mô tả, hình ảnh đa dạng, video, hướng dẫn bảo quản, bảng kích cỡ
- **Đánh giá sản phẩm**: Người dùng có thể đánh giá, xếp hạng sao, và thảo luận về sản phẩm

#### 1.4.2. Quản lý người dùng

- **Đăng ký và đăng nhập**: Hệ thống đăng ký, đăng nhập truyền thống và tích hợp Google OAuth
- **Phân quyền**: Phân chia người dùng thành khách hàng và quản trị viên với quyền truy cập khác nhau
- **Thông tin cá nhân**: Lưu trữ và quản lý thông tin cá nhân, địa chỉ giao hàng, lịch sử mua hàng
- **Danh sách yêu thích**: Lưu trữ sản phẩm yêu thích của khách hàng

#### 1.4.3. Giỏ hàng và đặt hàng

- **Giỏ hàng**: Thêm, sửa, xóa sản phẩm trong giỏ hàng, lưu giỏ hàng cho người dùng đã đăng nhập
- **Quy trình thanh toán**: Thu thập thông tin giao hàng, tính phí vận chuyển, áp dụng mã giảm giá
- **Phương thức thanh toán**: Hỗ trợ thanh toán qua VNPay, COD, chuyển khoản ngân hàng
- **Xác nhận đơn hàng**: Gửi email xác nhận, hiển thị trang cảm ơn với thông tin đơn hàng

#### 1.4.4. Quản lý đơn hàng

- **Xem và quản lý đơn hàng**: Khách hàng có thể theo dõi trạng thái đơn hàng
- **Quản lý bởi admin**: Xử lý đơn hàng, cập nhật trạng thái, in hóa đơn
- **Lịch sử đơn hàng**: Lưu trữ và hiển thị lịch sử đơn hàng của khách hàng
- **Hủy đơn hàng**: Cho phép hủy đơn hàng trong một số trường hợp nhất định

#### 1.4.5. Marketing và khuyến mãi

- **Mã giảm giá**: Tạo và quản lý mã giảm giá với nhiều điều kiện (giảm phần trăm, giảm số tiền cố định)
- **Sản phẩm nổi bật**: Đánh dấu và hiển thị sản phẩm nổi bật trên trang chủ
- **Sản phẩm liên quan**: Hiển thị sản phẩm liên quan trên trang chi tiết sản phẩm
- **Email marketing**: Hệ thống gửi email thông báo về sản phẩm mới, khuyến mãi

### 1.5. Yêu cầu phi nghiệp vụ

#### 1.5.1. Yêu cầu hiệu suất

- **Thời gian phản hồi**: Trang chủ và trang danh sách sản phẩm tải trong dưới 2 giây
- **Thời gian xử lý API**: API endpoints phản hồi trong dưới 500ms
- **Khả năng chịu tải**: Hỗ trợ tối thiểu 100 người dùng đồng thời
- **Tối ưu hóa hình ảnh**: Nén và tối ưu hình ảnh sản phẩm mà không làm giảm chất lượng hiển thị

#### 1.5.2. Yêu cầu bảo mật

- **Xác thực người dùng**: JWT cho API, session cho giao diện web
- **Bảo vệ dữ liệu**: Mã hóa mật khẩu, thông tin thanh toán
- **CSRF Protection**: Bảo vệ khỏi tấn công CSRF
- **Validation**: Kiểm tra và lọc dữ liệu đầu vào
- **Phân quyền**: Kiểm soát quyền truy cập vào các tính năng quản trị

#### 1.5.3. Yêu cầu sẵn sàng

- **Uptime**: Hệ thống phải có uptime tối thiểu 99.5%
- **Backup**: Sao lưu dữ liệu hàng ngày
- **Khả năng phục hồi**: Có kế hoạch phục hồi sau sự cố

#### 1.5.4. Yêu cầu khả năng mở rộng

- **Kiến trúc module**: Thiết kế để dễ dàng thêm các module mới
- **API-first**: Phát triển API đầy đủ cho tích hợp sau này
- **Cấu hình linh hoạt**: Tham số hóa các cấu hình để dễ dàng điều chỉnh

#### 1.5.5. Yêu cầu giao diện người dùng

- **Responsive Design**: Tương thích với mọi kích thước màn hình (desktop, tablet, mobile)
- **Trải nghiệm người dùng**: Giao diện trực quan, dễ sử dụng
- **Tốc độ tải trang**: Tối ưu hóa tốc độ tải trang
- **Khả năng tùy biến giao diện**: Dễ dàng thay đổi màu sắc, logo theo thương hiệu

### 1.6. Các kỹ thuật áp dụng để giải quyết bài toán

#### 1.6.1. Kiến trúc phần mềm

- **Mô hình MVC**: Tách biệt logic xử lý, dữ liệu và giao diện người dùng
- **Repository Pattern**: Tách biệt logic truy cập dữ liệu khỏi logic nghiệp vụ
- **Front Controller**: Tập trung xử lý request và routing
- **Service Layer**: Tách biệt business logic thành các service riêng biệt

#### 1.6.2. Công nghệ back-end

- **PHP 7.4+**: Ngôn ngữ lập trình chính
- **MySQL/MariaDB**: Hệ quản trị cơ sở dữ liệu quan hệ
- **Composer**: Quản lý các dependency
- **PHPMailer**: Xử lý gửi email
- **JWT**: Xác thực API bảo mật
- **Google API Client**: Tích hợp đăng nhập Google

#### 1.6.3. Công nghệ front-end

- **Bootstrap 5**: Framework CSS cho responsive design
- **JavaScript/jQuery**: Tương tác người dùng phía client
- **Swiper**: Slider và carousel hiệu ứng cao
- **FontAwesome**: Thư viện icon
- **AJAX**: Tương tác không đồng bộ với server

#### 1.6.4. Tích hợp thanh toán

- **VNPay**: Cổng thanh toán trực tuyến phổ biến tại Việt Nam
- **Xử lý IPN (Instant Payment Notification)**: Cập nhật trạng thái đơn hàng tự động

#### 1.6.5. Tối ưu hóa hiệu suất

- **Caching**: Lưu trữ tạm thời dữ liệu thường xuyên truy cập
- **Lazy Loading**: Tải hình ảnh khi cần thiết
- **Query Optimization**: Tối ưu hóa câu truy vấn cơ sở dữ liệu
- **Minification**: Nén CSS và JavaScript

#### 1.6.6. Bảo mật

- **Password Hashing**: Sử dụng thuật toán mã hóa mật khẩu mạnh
- **Input Validation**: Kiểm tra và lọc dữ liệu đầu vào
- **Prepared Statements**: Phòng chống SQL Injection
- **CSRF Protection**: Token bảo vệ các form
- **Rate Limiting**: Giới hạn số lượng request từ một IP

#### 1.6.7. Phương pháp phát triển

- **Git Flow**: Quy trình quản lý mã nguồn
- **TDD (Test-Driven Development)**: Phát triển dựa trên kiểm thử
- **Agile/Scrum**: Phương pháp phát triển linh hoạt
- **CI/CD**: Tích hợp và triển khai liên tục

## 3. PHÂN TÍCH YÊU CẦU PHẦN MỀM

### 3.1. Xác định Actor của hệ thống

Hệ thống 5S Fashion gồm ba Actor chính:

#### Người dùng (Customer):
- **Mô tả**: Người sử dụng nền tảng để mua sắm thời trang trực tuyến, có thể đăng ký tài khoản để sử dụng các tính năng cá nhân.
- **Chức năng chính**: Duyệt và tìm kiếm sản phẩm, thêm vào giỏ hàng, thanh toán đơn hàng, tạo và quản lý danh sách yêu thích, theo dõi lịch sử mua hàng, đánh giá sản phẩm, tương tác với hệ thống hỗ trợ khách hàng.

#### Admin (Quản trị viên):
- **Mô tả**: Người quản lý và vận hành toàn bộ hệ thống 5S Fashion, có quyền hạn cao nhất để quản lý sản phẩm, người dùng và các cài đặt hệ thống.
- **Chức năng chính**: Quản lý sản phẩm và danh mục, quản lý người dùng, quản lý đơn hàng, xem thống kê và báo cáo hệ thống, quản lý quảng cáo và khuyến mãi, cấu hình hệ thống, sao lưu và khôi phục dữ liệu.

#### Nhà cung cấp (Vendor/Supplier):
- **Mô tả**: Thương hiệu thời trang hoặc nhà phân phối sản phẩm trên nền tảng 5S Fashion, quản lý sản phẩm và kho hàng của mình.
- **Chức năng chính**: Đăng và quản lý sản phẩm, cập nhật tình trạng tồn kho, xử lý đơn hàng, theo dõi doanh số bán hàng, quản lý khuyến mãi cho sản phẩm của mình, tương tác với khách hàng thông qua hệ thống phản hồi.

### 3.2. Xác định yêu cầu các bên liên quan

#### 3.1.1. Người dùng

| Mô tả | Người dùng trong ứng dụng 5S Fashion là những người sử dụng ứng dụng để mua sắm thời trang trực tuyến. Họ có thể đăng ký tài khoản để sử dụng các tính năng cá nhân như tạo danh sách yêu thích, lưu địa chỉ giao hàng, và theo dõi lịch sử mua sắm. |
|-------|------------------------------------------------------------------------|
| Nhu cầu | • Cần có tài khoản để lưu trữ thông tin cá nhân <br>• Muốn mua sắm thời trang dễ dàng và tiện lợi <br>• Cần tạo và quản lý danh sách yêu thích riêng <br>• Muốn lưu lại những sản phẩm yêu thích <br>• Cần tìm kiếm và khám phá sản phẩm thời trang mới |
| Quyền lợi | • Mua sắm: Xem tất cả sản phẩm có trong hệ thống miễn phí <br>• Tìm kiếm: Tìm sản phẩm theo tên, danh mục, kích cỡ, màu sắc <br>• Danh sách yêu thích: Tạo và quản lý danh sách yêu thích riêng <br>• Giỏ hàng: Lưu những sản phẩm muốn mua vào giỏ hàng riêng <br>• Lịch sử: Xem lại những đơn hàng đã mua <br>• Cá nhân hóa: Cập nhật thông tin profile, avatar <br>• Khám phá: Xem các danh mục trending, sản phẩm mới, sản phẩm nổi bật |
| Các tính năng chính người dùng có thể sử dụng | • Đăng ký/Đăng nhập: Tạo tài khoản và đăng nhập vào hệ thống <br>• Product Viewer: Xem sản phẩm với đầy đủ chức năng hiển thị hình ảnh, thông tin <br>• Quản lý giỏ hàng: Thêm, xóa, điều chỉnh số lượng sản phẩm <br>• Thanh toán: Thực hiện thanh toán đơn hàng qua nhiều phương thức <br>• Danh sách yêu thích: Thêm/bỏ sản phẩm yêu thích để dành <br>• Lịch sử mua hàng: Theo dõi những gì đã mua <br>• Cập nhật profile: Thay đổi thông tin cá nhân, địa chỉ giao hàng <br>• Responsive: Sử dụng được trên máy tính, điện thoại, tablet |

#### 3.1.2. Admin (Quản trị viên hệ thống)

| Vai trò | Admin (Quản trị viên) là người quản lý và vận hành toàn bộ hệ thống 5S Fashion. Họ có quyền hạn cao nhất để quản lý sản phẩm, người dùng và các cài đặt hệ thống. Admin đảm bảo ứng dụng hoạt động ổn định và nội dung phù hợp. |
|---------|------------------------------------------------------------------|
| Trách nhiệm | • Quản lý và kiểm duyệt toàn bộ nội dung sản phẩm trong hệ thống <br>• Giám sát hoạt động của người dùng và xử lý vi phạm <br>• Đảm bảo chất lượng thông tin và hình ảnh sản phẩm chính xác <br>• Cập nhật và bảo trì hệ thống thường xuyên <br>• Phản hồi và hỗ trợ người dùng khi có vấn đề <br>• Sao lưu dữ liệu và đảm bảo bảo mật |
| Chức năng quản lý hệ thống của Admin | • Quản lý sản phẩm: Upload, chỉnh sửa, xóa sản phẩm và thông tin metadata <br>• Quản lý người dùng: Xem danh sách, kích hoạt/khóa tài khoản người dùng <br>• Quản lý thể loại: Tạo, sửa, xóa các danh mục sản phẩm <br>• Quản lý đơn hàng: Thêm thông tin vận chuyển, cập nhật trạng thái <br>• Thống kê hệ thống: Xem báo cáo lượt mua, người dùng mới, top trending <br>• Cài đặt banner: Quản lý quảng cáo và banner trang chủ <br>• Phân quyền: Cấp quyền admin cho người dùng khác <br>• Backup/Restore: Sao lưu và khôi phục dữ liệu hệ thống |
| Công cụ giám sát & báo cáo | • Dashboard quản trị: Giao diện tổng quan với các thống kê quan trọng <br>• Báo cáo hoạt động: Thống kê lượt mua, sản phẩm phổ biến, người dùng hoạt động <br>• Giám sát hệ thống: Theo dõi hiệu suất server, dung lượng lưu trữ <br>• Log hoạt động: Ghi lại các thao tác quan trọng của admin và người dùng <br>• Cảnh báo bảo mật: Phát hiện và cảnh báo các hoạt động bất thường <br>• Quản lý file: Kiểm soát dung lượng và định dạng file upload <br>• Kiểm duyệt nội dung: Xem xét và phê duyệt nội dung do người dùng đóng góp |

#### 3.1.3. Nhà cung cấp dịch vụ bán hàng

| Vai trò | Nhà cung cấp là các thương hiệu thời trang hoặc nhà phân phối sản phẩm thông qua nền tảng 5S Fashion. Họ cung cấp sản phẩm, thông tin chi tiết và quản lý kho hàng của mình trên hệ thống. |
|---------|------------------------------------------------------------------|
| Trách nhiệm | • Cung cấp thông tin sản phẩm chính xác và đầy đủ <br>• Cập nhật tình trạng tồn kho thường xuyên <br>• Đảm bảo chất lượng sản phẩm đúng như mô tả <br>• Xử lý đơn hàng kịp thời <br>• Giải quyết khiếu nại và đổi trả sản phẩm |
| Chức năng quản lý | • Quản lý sản phẩm: Thêm, sửa, xóa sản phẩm thuộc thương hiệu <br>• Quản lý kho hàng: Cập nhật số lượng tồn kho, thông báo hết hàng <br>• Quản lý đơn hàng: Xem và xử lý đơn hàng từ khách <br>• Báo cáo doanh số: Xem thống kê doanh thu, sản phẩm bán chạy <br>• Khuyến mãi: Tạo mã giảm giá hoặc khuyến mãi cho sản phẩm <br>• Phản hồi khách hàng: Trả lời đánh giá và câu hỏi về sản phẩm |
| Công cụ hỗ trợ | • Dashboard vendor: Giao diện quản lý dành riêng cho nhà cung cấp <br>• Công cụ phân tích: Thống kê về lượt xem, tỷ lệ chuyển đổi <br>• Thông báo: Nhận thông báo khi có đơn hàng mới hoặc hàng sắp hết <br>• Tích hợp kho hàng: Kết nối với hệ thống quản lý kho ngoài |

### 3.2. Yêu cầu chức năng hệ thống

#### 3.2.1. Quản lý sản phẩm

| Chức năng | Mô tả | Độ ưu tiên |
|-----------|-------|-----------|
| Thêm sản phẩm | Cho phép thêm sản phẩm mới với đầy đủ thông tin: tên, mô tả, giá, danh mục, thuộc tính, biến thể | Cao |
| Sửa sản phẩm | Cho phép chỉnh sửa thông tin sản phẩm đã có | Cao |
| Xóa sản phẩm | Cho phép xóa hoặc ẩn sản phẩm khỏi hệ thống | Cao |
| Quản lý biến thể | Cho phép tạo và quản lý các biến thể sản phẩm (kích cỡ, màu sắc) với giá và số lượng riêng | Cao |
| Quản lý hình ảnh | Cho phép tải lên và quản lý nhiều hình ảnh cho mỗi sản phẩm và biến thể | Cao |
| Phân loại sản phẩm | Cho phép phân loại sản phẩm theo nhiều tiêu chí (danh mục, thương hiệu, phong cách) | Trung bình |

#### 3.2.2. Quản lý người dùng

| Chức năng | Mô tả | Độ ưu tiên |
|-----------|-------|-----------|
| Đăng ký/Đăng nhập | Cho phép người dùng tạo tài khoản mới và đăng nhập | Cao |
| Xác thực đa yếu tố | Bổ sung lớp bảo mật với xác thực qua email/SMS | Trung bình |
| Quản lý thông tin cá nhân | Cho phép người dùng cập nhật thông tin cá nhân, địa chỉ giao hàng | Cao |
| Quản lý quyền | Phân quyền người dùng: khách hàng, quản trị viên, nhà cung cấp | Cao |
| Khóa/Mở tài khoản | Cho phép admin khóa hoặc mở tài khoản người dùng | Cao |
| Tích hợp đăng nhập xã hội | Cho phép đăng nhập qua Google, Facebook | Thấp |

#### 3.2.3. Quản lý giỏ hàng và đặt hàng

| Chức năng | Mô tả | Độ ưu tiên |
|-----------|-------|-----------|
| Thêm vào giỏ hàng | Cho phép thêm sản phẩm vào giỏ hàng | Cao |
| Cập nhật giỏ hàng | Cho phép thay đổi số lượng, xóa sản phẩm khỏi giỏ hàng | Cao |
| Lưu giỏ hàng | Lưu giỏ hàng cho người dùng đã đăng nhập | Cao |
| Quy trình thanh toán | Quy trình thanh toán đơn giản với thu thập thông tin giao hàng | Cao |
| Áp dụng mã giảm giá | Cho phép áp dụng mã giảm giá khi thanh toán | Trung bình |
| Nhiều phương thức thanh toán | Hỗ trợ VNPay, COD, chuyển khoản ngân hàng | Cao |

#### 3.2.4. Quản lý đơn hàng

| Chức năng | Mô tả | Độ ưu tiên |
|-----------|-------|-----------|
| Xem đơn hàng | Cho phép người dùng xem đơn hàng của mình | Cao |
| Cập nhật trạng thái | Cho phép admin cập nhật trạng thái đơn hàng | Cao |
| Hủy đơn hàng | Cho phép hủy đơn hàng trong một số trường hợp | Cao |
| In hóa đơn | Cho phép tạo và in hóa đơn | Trung bình |
| Thông báo trạng thái | Gửi thông báo khi trạng thái đơn hàng thay đổi | Trung bình |
| Lịch sử đơn hàng | Lưu trữ và hiển thị lịch sử đơn hàng | Cao |

### 3.3. Yêu cầu phi chức năng

#### 3.3.1. Hiệu suất

| Yêu cầu | Mô tả | Tiêu chí đánh giá |
|---------|-------|------------------|
| Thời gian phản hồi | Trang chủ và trang danh sách sản phẩm tải trong dưới 2 giây | Đo thời gian tải trang |
| Xử lý API | API endpoints phản hồi trong dưới 500ms | Đo thời gian phản hồi API |
| Khả năng chịu tải | Hỗ trợ tối thiểu 100 người dùng đồng thời | Kiểm tra tải với công cụ load testing |
| Tối ưu hóa hình ảnh | Nén và tối ưu hình ảnh sản phẩm | So sánh thời gian tải trước và sau khi tối ưu |

#### 3.3.2. Bảo mật

| Yêu cầu | Mô tả | Tiêu chí đánh giá |
|---------|-------|------------------|
| Mã hóa dữ liệu | Mã hóa thông tin người dùng và dữ liệu thanh toán | Kiểm tra việc sử dụng HTTPS và mã hóa |
| Phòng chống tấn công | Bảo vệ khỏi các cuộc tấn công phổ biến (SQL Injection, XSS, CSRF) | Thực hiện kiểm thử bảo mật |
| Quyền truy cập | Kiểm soát quyền truy cập vào các tính năng | Kiểm tra phân quyền người dùng |
| Xác thực người dùng | JWT cho API, session cho giao diện web | Kiểm tra tính hợp lệ của token |

#### 3.3.3. Độ tin cậy và sẵn sàng

| Yêu cầu | Mô tả | Tiêu chí đánh giá |
|---------|-------|------------------|
| Uptime | Hệ thống phải có uptime tối thiểu 99.5% | Đo uptime trong 30 ngày |
| Sao lưu dữ liệu | Sao lưu dữ liệu hàng ngày | Kiểm tra lịch sao lưu tự động |
| Khả năng phục hồi | Có kế hoạch phục hồi sau sự cố | Thử nghiệm quy trình khôi phục |
| Xử lý lỗi | Xử lý lỗi ứng dụng một cách nhất quán | Kiểm tra hiển thị thông báo lỗi |

### 3.4. Biểu đồ ca sử dụng (Use Case Diagram)

#### 3.4.1. Tổng quan hệ thống

Biểu đồ 3.1 minh hoạ tổng quát các ca sử dụng chính trong hệ thống 5S Fashion:

![Biểu đồ Use Case - Hệ thống 5S Fashion](./docs/images/use-case-diagram.png)

*Hình 3.1: Biểu đồ Use Case tổng quan hệ thống 5S Fashion*

#### 3.4.2. Mô tả các ca sử dụng chính

Chi tiết mô tả use case đã được tách ra trong file [USE_CASE_DESCRIPTIONS.md](./docs/USE_CASE_DESCRIPTIONS.md) để thuận tiện cho việc theo dõi.

Dưới đây là tóm tắt các ca sử dụng chính:

##### Ca sử dụng của Người dùng (Customer):

1. **Đăng ký/Đăng nhập**:
   - Người dùng có thể tạo tài khoản mới hoặc đăng nhập với tài khoản hiện có
   - Bao gồm đăng nhập thông thường và đăng nhập qua Google

2. **Xem và tìm kiếm sản phẩm**:
   - Duyệt danh sách sản phẩm theo danh mục
   - Tìm kiếm sản phẩm theo nhiều tiêu chí (tên, giá, màu sắc, kích cỡ)
   - Xem chi tiết sản phẩm với hình ảnh và thông tin đầy đủ

3. **Quản lý giỏ hàng**:
   - Thêm sản phẩm vào giỏ hàng
   - Cập nhật số lượng sản phẩm
   - Xóa sản phẩm khỏi giỏ hàng

4. **Thanh toán đơn hàng**:
   - Cung cấp thông tin giao hàng
   - Chọn phương thức thanh toán
   - Áp dụng mã giảm giá (nếu có)
   - Hoàn tất đặt hàng

5. **Quản lý danh sách yêu thích**:
   - Thêm sản phẩm vào danh sách yêu thích
   - Xóa sản phẩm khỏi danh sách yêu thích
   - Xem danh sách sản phẩm yêu thích

6. **Cập nhật thông tin cá nhân**:
   - Thay đổi thông tin cá nhân
   - Thêm/sửa/xóa địa chỉ giao hàng
   - Thay đổi mật khẩu

7. **Đánh giá sản phẩm**:
   - Đánh giá sản phẩm đã mua (xếp hạng sao)
   - Viết nhận xét về sản phẩm
   - Xem đánh giá của người khác

##### Ca sử dụng của Admin (Quản trị viên):

1. **Quản lý sản phẩm**:
   - Thêm sản phẩm mới
   - Chỉnh sửa thông tin sản phẩm
   - Xóa/ẩn sản phẩm
   - Quản lý biến thể và thuộc tính sản phẩm

2. **Quản lý danh mục**:
   - Tạo danh mục mới
   - Chỉnh sửa thông tin danh mục
   - Xóa danh mục
   - Sắp xếp thứ tự danh mục

3. **Quản lý người dùng**:
   - Xem danh sách người dùng
   - Kích hoạt/khóa tài khoản người dùng
   - Phân quyền cho người dùng
   - Xem lịch sử hoạt động của người dùng

4. **Quản lý đơn hàng**:
   - Xem danh sách đơn hàng
   - Cập nhật trạng thái đơn hàng
   - Xem chi tiết đơn hàng
   - In hóa đơn

5. **Xem thống kê và báo cáo**:
   - Xem báo cáo doanh thu
   - Xem thống kê sản phẩm bán chạy
   - Xem thống kê người dùng mới
   - Xem báo cáo tồn kho

6. **Quản lý khuyến mãi**:
   - Tạo mã giảm giá mới
   - Chỉnh sửa/xóa mã giảm giá
   - Thiết lập điều kiện áp dụng mã giảm giá
   - Theo dõi lượt sử dụng mã giảm giá

7. **Sao lưu và phục hồi dữ liệu**:
   - Thực hiện sao lưu dữ liệu
   - Khôi phục dữ liệu từ bản sao lưu
   - Quản lý lịch sử sao lưu

##### Ca sử dụng của Nhà cung cấp (Vendor/Supplier):

1. **Quản lý sản phẩm riêng**:
   - Thêm sản phẩm mới thuộc thương hiệu
   - Cập nhật thông tin sản phẩm
   - Ẩn/hiện sản phẩm
   - Quản lý biến thể sản phẩm

2. **Quản lý tồn kho**:
   - Cập nhật số lượng tồn kho
   - Thiết lập ngưỡng cảnh báo hết hàng
   - Nhận thông báo khi sản phẩm sắp hết hàng

3. **Xử lý đơn hàng**:
   - Xem đơn hàng liên quan đến sản phẩm của mình
   - Cập nhật trạng thái xử lý đơn hàng
   - Cung cấp thông tin vận chuyển

4. **Xem báo cáo doanh số**:
   - Xem doanh thu theo thời gian
   - Xem sản phẩm bán chạy nhất
   - Xem thống kê đánh giá sản phẩm

5. **Quản lý khuyến mãi sản phẩm**:
   - Tạo chương trình giảm giá cho sản phẩm
   - Thiết lập sản phẩm nổi bật
   - Tạo gói ưu đãi sản phẩm

## Kết luận

5S Fashion là một nền tảng thương mại điện tử toàn diện, được phát triển riêng cho ngành thời trang Việt Nam. Với kiến trúc linh hoạt, tính năng đa dạng và hiệu suất tối ưu, dự án đáp ứng được các yêu cầu đặc thù của doanh nghiệp thời trang vừa và nhỏ, giúp họ chuyển đổi số một cách hiệu quả và nâng cao trải nghiệm mua sắm cho khách hàng.

Các kỹ thuật và công nghệ được áp dụng trong dự án đều là những giải pháp hiện đại, bảo mật và có khả năng mở rộng cao, đảm bảo hệ thống có thể phát triển theo nhu cầu kinh doanh trong tương lai.
