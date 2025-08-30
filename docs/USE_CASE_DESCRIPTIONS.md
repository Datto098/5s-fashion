# MÔ TẢ CHI TIẾT USE CASE 5S FASHION

## Đặc tả Use Case: Đăng ký tài khoản

| Mô tả ngắn | Người dùng tạo tài khoản mới để sử dụng dịch vụ mua sắm thời trang trên 5S Fashion |
|------------|--------------------------------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: User (Người dùng)<br>Actor phụ: Hệ thống email |
| Điều kiện kích hoạt | - Người dùng truy cập trang đăng ký<br>- Người dùng chưa có tài khoản trong hệ thống |
| Luồng chính | 1. Người dùng truy cập trang đăng ký<br>2. Hệ thống hiển thị form đăng ký (username, email, password, confirm password)<br>3. Người dùng điền thông tin và bấm "Đăng ký"<br>4. Hệ thống kiểm tra tính hợp lệ của dữ liệu<br>5. Hệ thống tạo tài khoản với trạng thái chưa xác thực<br>6. Hệ thống gửi email xác thực đến địa chỉ email đã đăng ký<br>7. Hệ thống hiển thị thông báo "Đăng ký thành công, vui lòng kiểm tra email" |
| Luồng phụ | Luồng phụ 4a: Dữ liệu không hợp lệ<br>4a.1. Hệ thống hiển thị thông báo lỗi cụ thể<br>4a.2. Quay lại bước 3<br><br>Luồng phụ 5a: Email/Username đã tồn tại<br>5a.1. Hệ thống thông báo "Email/Username đã được sử dụng"<br>5a.2. Quay lại bước 3 |
| Điều kiện sau | - Tài khoản mới được tạo với trạng thái isVerified = false<br>- Email xác thực được gửi thành công<br>- Người dùng có thể đăng nhập nhưng bị hạn chế một số tính năng |
| Điều kiện ngoại lệ | - Lỗi kết nối mạng<br>- Lỗi server email<br>- Database không khả dụng |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Thường xuyên - mỗi người dùng mới |

## Đặc tả Use Case: Đăng nhập

| Mô tả ngắn | Người dùng đăng nhập vào hệ thống bằng tài khoản đã đăng ký |
|------------|-----------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: User (Người dùng)<br>Actor phụ: Hệ thống xác thực Google (tùy chọn) |
| Điều kiện kích hoạt | - Người dùng truy cập trang đăng nhập<br>- Người dùng đã có tài khoản trong hệ thống |
| Luồng chính | 1. Người dùng truy cập trang đăng nhập<br>2. Hệ thống hiển thị form đăng nhập<br>3. Người dùng nhập email/username và mật khẩu<br>4. Người dùng bấm nút "Đăng nhập"<br>5. Hệ thống xác thực thông tin đăng nhập<br>6. Hệ thống tạo phiên làm việc cho người dùng<br>7. Hệ thống chuyển hướng người dùng đến trang chủ |
| Luồng phụ | Luồng phụ 5a: Thông tin đăng nhập không chính xác<br>5a.1. Hệ thống hiển thị thông báo lỗi<br>5a.2. Quay lại bước 3<br><br>Luồng phụ 3a: Đăng nhập bằng Google<br>3a.1. Người dùng chọn "Đăng nhập với Google"<br>3a.2. Hệ thống chuyển hướng đến trang xác thực Google<br>3a.3. Người dùng cấp quyền truy cập<br>3a.4. Google trả về thông tin người dùng<br>3a.5. Quay lại bước 6 |
| Điều kiện sau | - Người dùng đã đăng nhập thành công<br>- Hệ thống tạo phiên làm việc<br>- Người dùng có quyền truy cập vào các tính năng dành cho người dùng đã đăng nhập |
| Điều kiện ngoại lệ | - Lỗi kết nối mạng<br>- Lỗi xác thực bên thứ ba<br>- Tài khoản bị khóa |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Rất thường xuyên |

## Đặc tả Use Case: Xem và tìm kiếm sản phẩm

| Mô tả ngắn | Người dùng tìm kiếm và xem thông tin sản phẩm trên hệ thống |
|------------|-----------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: User (Người dùng) |
| Điều kiện kích hoạt | - Người dùng truy cập trang web<br>- Hệ thống đã có dữ liệu sản phẩm |
| Luồng chính | 1. Người dùng truy cập trang danh sách sản phẩm hoặc trang chủ<br>2. Hệ thống hiển thị danh sách sản phẩm<br>3. Người dùng có thể:<br>   a. Duyệt sản phẩm theo danh mục<br>   b. Sử dụng bộ lọc (giá, màu sắc, kích cỡ...)<br>   c. Sử dụng thanh tìm kiếm<br>   d. Xem chi tiết sản phẩm bằng cách nhấp vào sản phẩm<br>4. Hệ thống hiển thị kết quả dựa trên tiêu chí của người dùng |
| Luồng phụ | Luồng phụ 4a: Không có sản phẩm phù hợp<br>4a.1. Hệ thống hiển thị thông báo "Không tìm thấy sản phẩm"<br>4a.2. Hiển thị một số sản phẩm gợi ý<br><br>Luồng phụ 3d: Xem chi tiết sản phẩm<br>3d.1. Người dùng chọn một sản phẩm cụ thể<br>3d.2. Hệ thống hiển thị trang chi tiết sản phẩm với thông tin đầy đủ<br>3d.3. Người dùng có thể xem hình ảnh, mô tả, giá, đánh giá, và chọn biến thể sản phẩm |
| Điều kiện sau | - Người dùng xem được thông tin sản phẩm mong muốn<br>- Hệ thống ghi lại lịch sử xem sản phẩm (nếu người dùng đã đăng nhập) |
| Điều kiện ngoại lệ | - Lỗi kết nối dữ liệu<br>- Sản phẩm đã bị xóa hoặc ẩn |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Rất thường xuyên |

## Đặc tả Use Case: Thêm sản phẩm vào giỏ hàng

| Mô tả ngắn | Người dùng thêm sản phẩm vào giỏ hàng để chuẩn bị mua hàng |
|------------|-----------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: User (Người dùng) |
| Điều kiện kích hoạt | - Người dùng đang xem chi tiết sản phẩm<br>- Sản phẩm còn hàng |
| Luồng chính | 1. Người dùng truy cập trang chi tiết sản phẩm<br>2. Người dùng chọn biến thể sản phẩm (màu sắc, kích cỡ nếu có)<br>3. Người dùng chọn số lượng<br>4. Người dùng nhấn nút "Thêm vào giỏ hàng"<br>5. Hệ thống kiểm tra tính khả dụng của sản phẩm<br>6. Hệ thống thêm sản phẩm vào giỏ hàng<br>7. Hệ thống hiển thị thông báo xác nhận và hiển thị tóm tắt giỏ hàng |
| Luồng phụ | Luồng phụ 2a: Biến thể không được chọn<br>2a.1. Người dùng không chọn biến thể bắt buộc<br>2a.2. Hệ thống hiển thị thông báo yêu cầu chọn biến thể<br>2a.3. Quay lại bước 2<br><br>Luồng phụ 5a: Sản phẩm hết hàng<br>5a.1. Hệ thống hiển thị thông báo "Sản phẩm tạm hết hàng"<br>5a.2. Người dùng có thể đăng ký nhận thông báo khi có hàng |
| Điều kiện sau | - Sản phẩm được thêm vào giỏ hàng<br>- Số lượng và tổng giá giỏ hàng được cập nhật |
| Điều kiện ngoại lệ | - Lỗi kết nối<br>- Lỗi cơ sở dữ liệu<br>- Số lượng yêu cầu vượt quá tồn kho |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Rất thường xuyên |

## Đặc tả Use Case: Thanh toán đơn hàng

| Mô tả ngắn | Người dùng tiến hành thanh toán giỏ hàng của mình |
|------------|--------------------------------------------------|
| Các tác nhân tham gia | Actor chính: User (Người dùng)<br>Actor phụ: Cổng thanh toán (VNPay), Hệ thống email |
| Điều kiện kích hoạt | - Người dùng đã thêm sản phẩm vào giỏ hàng<br>- Người dùng đã nhấp vào nút "Thanh toán" |
| Luồng chính | 1. Người dùng xem lại giỏ hàng và nhấp "Tiến hành thanh toán"<br>2. Nếu chưa đăng nhập, hệ thống yêu cầu đăng nhập hoặc tiếp tục dưới dạng khách<br>3. Người dùng nhập thông tin giao hàng và thanh toán<br>4. Người dùng chọn phương thức thanh toán<br>5. Người dùng có thể nhập mã giảm giá (nếu có)<br>6. Hệ thống hiển thị tổng cộng cuối cùng<br>7. Người dùng xác nhận đặt hàng<br>8. Nếu thanh toán online, hệ thống chuyển hướng đến cổng thanh toán<br>9. Sau khi thanh toán thành công, hệ thống tạo đơn hàng<br>10. Hệ thống gửi email xác nhận đơn hàng<br>11. Hệ thống chuyển hướng đến trang xác nhận đơn hàng |
| Luồng phụ | Luồng phụ 5a: Áp dụng mã giảm giá<br>5a.1. Người dùng nhập mã giảm giá<br>5a.2. Hệ thống xác thực và áp dụng giảm giá<br>5a.3. Cập nhật tổng cộng<br>5a.4. Quay lại bước 6<br><br>Luồng phụ 8a: Thanh toán COD<br>8a.1. Bỏ qua cổng thanh toán<br>8a.2. Tiến thẳng đến bước 9<br><br>Luồng phụ 8b: Lỗi thanh toán<br>8b.1. Người dùng gặp lỗi hoặc hủy tại cổng thanh toán<br>8b.2. Hệ thống hiển thị thông báo lỗi<br>8b.3. Người dùng có thể thử lại hoặc chọn phương thức khác |
| Điều kiện sau | - Đơn hàng được tạo thành công<br>- Email xác nhận được gửi<br>- Giỏ hàng được làm trống<br>- Tồn kho sản phẩm được cập nhật |
| Điều kiện ngoại lệ | - Lỗi kết nối với cổng thanh toán<br>- Lỗi xử lý giao dịch<br>- Sản phẩm hết hàng trong quá trình thanh toán |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Thường xuyên |

## Đặc tả Use Case: Quản lý sản phẩm (Admin)

| Mô tả ngắn | Admin thêm, sửa, xóa và quản lý các sản phẩm trong hệ thống |
|------------|-----------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: Admin |
| Điều kiện kích hoạt | - Admin đã đăng nhập vào hệ thống<br>- Admin truy cập vào trang quản lý sản phẩm |
| Luồng chính | 1. Admin truy cập trang quản lý sản phẩm<br>2. Hệ thống hiển thị danh sách sản phẩm hiện có<br>3. Admin có thể:<br>   a. Thêm sản phẩm mới<br>   b. Chỉnh sửa sản phẩm hiện có<br>   c. Xóa sản phẩm<br>   d. Quản lý biến thể sản phẩm<br>4. Khi thêm/sửa sản phẩm, Admin nhập các thông tin cần thiết<br>5. Hệ thống kiểm tra tính hợp lệ của dữ liệu<br>6. Hệ thống lưu thông tin và hiển thị thông báo thành công |
| Luồng phụ | Luồng phụ 3a: Thêm sản phẩm mới<br>3a.1. Admin chọn "Thêm sản phẩm mới"<br>3a.2. Hệ thống hiển thị form thêm sản phẩm<br>3a.3. Admin điền thông tin sản phẩm và tải hình ảnh<br>3a.4. Admin bấm "Lưu"<br>3a.5. Tiếp tục bước 5<br><br>Luồng phụ 3d: Quản lý biến thể<br>3d.1. Admin chọn "Quản lý biến thể" cho một sản phẩm<br>3d.2. Hệ thống hiển thị giao diện quản lý biến thể<br>3d.3. Admin thêm/sửa/xóa biến thể<br>3d.4. Hệ thống lưu thay đổi |
| Điều kiện sau | - Thông tin sản phẩm được cập nhật trong cơ sở dữ liệu<br>- Sản phẩm có sẵn để hiển thị trên trang web (nếu trạng thái là "published") |
| Điều kiện ngoại lệ | - Lỗi tải lên hình ảnh<br>- Lỗi cơ sở dữ liệu |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Thường xuyên (đối với Admin) |

## Đặc tả Use Case: Quản lý đơn hàng (Admin)

| Mô tả ngắn | Admin xem và xử lý các đơn hàng trong hệ thống |
|------------|---------------------------------------------|
| Các tác nhân tham gia | Actor chính: Admin<br>Actor phụ: Hệ thống email |
| Điều kiện kích hoạt | - Admin đã đăng nhập vào hệ thống<br>- Admin truy cập vào trang quản lý đơn hàng |
| Luồng chính | 1. Admin truy cập trang quản lý đơn hàng<br>2. Hệ thống hiển thị danh sách đơn hàng với các bộ lọc<br>3. Admin có thể lọc đơn hàng theo trạng thái, ngày, khách hàng, v.v.<br>4. Admin chọn một đơn hàng để xem chi tiết<br>5. Admin có thể cập nhật trạng thái đơn hàng (đang xử lý, đã giao hàng, đã hủy...)<br>6. Hệ thống lưu thay đổi và gửi thông báo cho khách hàng về cập nhật trạng thái |
| Luồng phụ | Luồng phụ 5a: In hóa đơn<br>5a.1. Admin chọn "In hóa đơn"<br>5a.2. Hệ thống tạo và hiển thị tệp PDF hóa đơn<br><br>Luồng phụ 5b: Hủy đơn hàng<br>5b.1. Admin chọn "Hủy đơn hàng"<br>5b.2. Hệ thống yêu cầu xác nhận và lý do hủy<br>5b.3. Admin xác nhận và cung cấp lý do<br>5b.4. Hệ thống cập nhật trạng thái và cập nhật lại tồn kho<br>5b.5. Hệ thống gửi email thông báo cho khách hàng |
| Điều kiện sau | - Trạng thái đơn hàng được cập nhật<br>- Email thông báo được gửi đến khách hàng (tùy theo cập nhật)<br>- Tồn kho được cập nhật (nếu đơn hàng bị hủy) |
| Điều kiện ngoại lệ | - Lỗi cơ sở dữ liệu<br>- Lỗi gửi email |
| Mức độ ưu tiên | Cao (Essential) |
| Tần suất sử dụng | Thường xuyên (đối với Admin) |

## Đặc tả Use Case: Quản lý tồn kho (Vendor)

| Mô tả ngắn | Nhà cung cấp cập nhật thông tin tồn kho cho các sản phẩm của họ |
|------------|-------------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: Vendor (Nhà cung cấp) |
| Điều kiện kích hoạt | - Vendor đã đăng nhập vào hệ thống<br>- Vendor truy cập vào trang quản lý tồn kho |
| Luồng chính | 1. Vendor truy cập trang quản lý tồn kho<br>2. Hệ thống hiển thị danh sách sản phẩm của vendor<br>3. Vendor có thể lọc sản phẩm theo danh mục, tình trạng tồn kho<br>4. Vendor chọn sản phẩm hoặc biến thể sản phẩm để cập nhật<br>5. Vendor nhập số lượng tồn kho mới<br>6. Hệ thống kiểm tra tính hợp lệ của dữ liệu<br>7. Hệ thống lưu thay đổi và hiển thị thông báo thành công |
| Luồng phụ | Luồng phụ 4a: Cập nhật hàng loạt<br>4a.1. Vendor chọn nhiều sản phẩm<br>4a.2. Vendor chọn "Cập nhật hàng loạt"<br>4a.3. Vendor nhập số lượng hoặc tăng/giảm giá trị<br>4a.4. Hệ thống cập nhật tất cả sản phẩm đã chọn<br><br>Luồng phụ 5a: Thiết lập ngưỡng cảnh báo<br>5a.1. Vendor thiết lập ngưỡng cảnh báo hết hàng<br>5a.2. Hệ thống lưu ngưỡng cảnh báo<br>5a.3. Khi tồn kho giảm xuống dưới ngưỡng, hệ thống sẽ gửi thông báo |
| Điều kiện sau | - Thông tin tồn kho được cập nhật<br>- Sản phẩm được đánh dấu "hết hàng" nếu số lượng bằng 0<br>- Ngưỡng cảnh báo được thiết lập (nếu có) |
| Điều kiện ngoại lệ | - Lỗi cơ sở dữ liệu |
| Mức độ ưu tiên | Cao (Important) |
| Tần suất sử dụng | Thường xuyên (đối với Vendor) |

## Đặc tả Use Case: Xem báo cáo và thống kê (Admin)

| Mô tả ngắn | Admin xem các báo cáo và thống kê về hoạt động của hệ thống |
|------------|----------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: Admin |
| Điều kiện kích hoạt | - Admin đã đăng nhập vào hệ thống<br>- Admin truy cập vào trang báo cáo |
| Luồng chính | 1. Admin truy cập trang báo cáo và thống kê<br>2. Hệ thống hiển thị bảng điều khiển với tổng quan<br>3. Admin có thể chọn xem các loại báo cáo khác nhau:<br>   a. Báo cáo doanh thu<br>   b. Báo cáo sản phẩm bán chạy<br>   c. Báo cáo người dùng mới<br>   d. Báo cáo tồn kho<br>4. Admin có thể lọc báo cáo theo khoảng thời gian<br>5. Hệ thống hiển thị dữ liệu báo cáo dưới dạng biểu đồ và bảng |
| Luồng phụ | Luồng phụ 5a: Xuất báo cáo<br>5a.1. Admin chọn "Xuất báo cáo"<br>5a.2. Admin chọn định dạng (PDF, Excel)<br>5a.3. Hệ thống tạo và tải xuống tệp báo cáo<br><br>Luồng phụ 3e: Báo cáo tùy chỉnh<br>3e.1. Admin chọn "Báo cáo tùy chỉnh"<br>3e.2. Admin chọn các thông số và dữ liệu muốn xem<br>3e.3. Hệ thống tạo báo cáo theo yêu cầu |
| Điều kiện sau | - Admin xem được các số liệu thống kê và báo cáo<br>- Báo cáo được tải xuống (nếu chọn xuất báo cáo) |
| Điều kiện ngoại lệ | - Lỗi kết nối dữ liệu<br>- Không có dữ liệu để hiển thị trong khoảng thời gian đã chọn |
| Mức độ ưu tiên | Trung bình |
| Tần suất sử dụng | Thường xuyên (đối với Admin) |

## Đặc tả Use Case: Quản lý khuyến mãi (Admin)

| Mô tả ngắn | Admin tạo và quản lý các mã giảm giá và chương trình khuyến mãi |
|------------|--------------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: Admin |
| Điều kiện kích hoạt | - Admin đã đăng nhập vào hệ thống<br>- Admin truy cập vào trang quản lý khuyến mãi |
| Luồng chính | 1. Admin truy cập trang quản lý khuyến mãi<br>2. Hệ thống hiển thị danh sách khuyến mãi và mã giảm giá hiện có<br>3. Admin có thể:<br>   a. Tạo mã giảm giá mới<br>   b. Chỉnh sửa mã giảm giá hiện có<br>   c. Hủy hoặc xóa mã giảm giá<br>   d. Thiết lập sản phẩm nổi bật<br>4. Khi tạo mã giảm giá, Admin nhập các thông tin cần thiết<br>5. Hệ thống kiểm tra tính hợp lệ của dữ liệu<br>6. Hệ thống lưu thông tin và hiển thị thông báo thành công |
| Luồng phụ | Luồng phụ 3a: Tạo mã giảm giá<br>3a.1. Admin chọn "Tạo mã giảm giá mới"<br>3a.2. Admin điền thông tin: mã, loại giảm giá (phần trăm/số tiền), giá trị, điều kiện áp dụng, thời hạn<br>3a.3. Admin có thể giới hạn số lần sử dụng, giới hạn cho mỗi người dùng<br>3a.4. Tiếp tục bước 5<br><br>Luồng phụ 3d: Thiết lập sản phẩm nổi bật<br>3d.1. Admin chọn "Quản lý sản phẩm nổi bật"<br>3d.2. Hệ thống hiển thị giao diện chọn sản phẩm<br>3d.3. Admin chọn các sản phẩm để hiển thị ở vị trí nổi bật<br>3d.4. Admin thiết lập thời gian hiển thị<br>3d.5. Hệ thống lưu thay đổi |
| Điều kiện sau | - Mã giảm giá được tạo hoặc cập nhật trong cơ sở dữ liệu<br>- Sản phẩm nổi bật được hiển thị trên trang chủ (nếu có) |
| Điều kiện ngoại lệ | - Lỗi cơ sở dữ liệu |
| Mức độ ưu tiên | Trung bình |
| Tần suất sử dụng | Định kỳ (theo mùa, sự kiện) |

## Đặc tả Use Case: Đánh giá sản phẩm

| Mô tả ngắn | Người dùng đánh giá và viết nhận xét về sản phẩm đã mua |
|------------|------------------------------------------------------|
| Các tác nhân tham gia | Actor chính: User (Người dùng) |
| Điều kiện kích hoạt | - Người dùng đã đăng nhập vào hệ thống<br>- Người dùng đã mua và nhận sản phẩm<br>- Đơn hàng ở trạng thái "Đã giao" |
| Luồng chính | 1. Người dùng truy cập trang chi tiết sản phẩm hoặc trang đơn hàng<br>2. Người dùng chọn "Đánh giá sản phẩm"<br>3. Hệ thống hiển thị form đánh giá<br>4. Người dùng chọn số sao (1-5)<br>5. Người dùng viết nhận xét (tùy chọn)<br>6. Người dùng có thể tải lên hình ảnh (tùy chọn)<br>7. Người dùng gửi đánh giá<br>8. Hệ thống kiểm tra tính hợp lệ của dữ liệu<br>9. Hệ thống lưu đánh giá và hiển thị thông báo thành công |
| Luồng phụ | Luồng phụ 8a: Nội dung không phù hợp<br>8a.1. Hệ thống phát hiện nội dung không phù hợp<br>8a.2. Hệ thống hiển thị thông báo lỗi<br>8a.3. Quay lại bước 5<br><br>Luồng phụ 6a: Tải lên hình ảnh<br>6a.1. Người dùng chọn "Tải lên hình ảnh"<br>6a.2. Người dùng chọn hình ảnh từ thiết bị<br>6a.3. Hệ thống kiểm tra và tối ưu hình ảnh<br>6a.4. Tiếp tục bước 7 |
| Điều kiện sau | - Đánh giá được lưu và hiển thị trong trang sản phẩm<br>- Điểm đánh giá trung bình của sản phẩm được cập nhật |
| Điều kiện ngoại lệ | - Lỗi tải lên hình ảnh<br>- Lỗi cơ sở dữ liệu |
| Mức độ ưu tiên | Trung bình |
| Tần suất sử dụng | Thường xuyên |
