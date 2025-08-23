# Tài liệu hướng dẫn sử dụng Chatbot 5S Fashion

## Giới thiệu

Chatbot 5S Fashion là một trợ lý ảo được phát triển để giúp khách hàng của 5S Fashion tương tác và tìm kiếm thông tin về sản phẩm, khuyến mãi, đơn hàng và các thông tin khác một cách nhanh chóng, hiệu quả.

## Tính năng chính

### 1. Tư vấn sản phẩm
- **Sản phẩm bán chạy**: Hiển thị các sản phẩm bán chạy nhất hiện tại
- **Sản phẩm giảm giá**: Hiển thị các sản phẩm đang có khuyến mãi giảm giá
- **Sản phẩm mới**: Hiển thị các sản phẩm mới nhất được thêm vào hệ thống
- **Tư vấn thời trang**: Cung cấp gợi ý phối đồ và tư vấn thời trang

### 2. Thông tin đơn hàng
- Hướng dẫn cách kiểm tra trạng thái đơn hàng
- Giải đáp thắc mắc về đơn hàng
- Hướng dẫn liên hệ để hỗ trợ đơn hàng

### 3. Hỗ trợ thanh toán
- Cung cấp thông tin về các phương thức thanh toán
- Giải đáp câu hỏi về quy trình thanh toán

### 4. Chính sách và hỗ trợ
- Hướng dẫn chọn size
- Thông tin về chính sách đổi trả
- Thông tin về vận chuyển
- Thông tin liên hệ của cửa hàng

## Cách sử dụng

### Bắt đầu cuộc hội thoại
1. Nhấp vào biểu tượng chat ở góc dưới phải màn hình
2. Chatbot sẽ tự động gửi lời chào và danh sách các dịch vụ có thể hỗ trợ

### Sử dụng nút tác vụ nhanh
Bạn có thể nhấp vào các nút tác vụ nhanh ở dưới cùng của cửa sổ chat:
- **Sản phẩm hot**: Hiển thị sản phẩm bán chạy
- **Khuyến mãi**: Hiển thị sản phẩm đang giảm giá
- **Tư vấn**: Nhận tư vấn thời trang

### Tìm kiếm thông tin bằng cách nhắn tin
Bạn có thể nhập trực tiếp câu hỏi hoặc yêu cầu vào ô chat, ví dụ:
- "Cho tôi xem sản phẩm bán chạy"
- "Tôi cần thông tin về chính sách đổi trả"
- "Làm sao để kiểm tra đơn hàng của tôi?"
- "Các phương thức thanh toán hiện có?"
- "Hướng dẫn chọn size áo"

## Lưu ý kỹ thuật

### API Endpoints
- Endpoint chính: `/chatbot-api.php`
- Phương thức: POST
- Body: JSON với trường `message` chứa nội dung tin nhắn của người dùng

### Cơ chế hoạt động
1. Chatbot phân tích tin nhắn của người dùng thông qua các mẫu regex
2. Dựa vào từ khóa, chatbot truy vấn cơ sở dữ liệu để lấy thông tin sản phẩm hoặc trả về câu trả lời được định nghĩa trước
3. Dữ liệu trả về được hiển thị dưới dạng tin nhắn văn bản hoặc thẻ sản phẩm (đối với các truy vấn về sản phẩm)

### Lịch sử trò chuyện
- Lịch sử trò chuyện được lưu trong localStorage của trình duyệt
- Tối đa 20 tin nhắn gần nhất được lưu lại
- Lịch sử trò chuyện sẽ được hiển thị lại khi người dùng mở lại chatbot

### Các hàm JavaScript có thể gọi từ bên ngoài
- `window.openChatbot()`: Mở cửa sổ chatbot
- `window.sendToChatbot(message)`: Gửi một tin nhắn tới chatbot từ bên ngoài
