# Hướng dẫn cấu hình Google OAuth cho Zone Fashion

## Bước 1: Tạo project trên Google Cloud Console

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới (hoặc chọn project hiện có)
3. Mở menu "APIs & Services" > "Credentials"
4. Click "Create Credentials" và chọn "OAuth client ID"
5. Nếu bạn chưa cấu hình OAuth consent screen, hãy làm theo các bước sau:
   - Chọn loại người dùng (External)
   - Điền thông tin ứng dụng (tên, email liên hệ, logo, v.v.)
   - Thêm các scopes: email, profile, openid
   - Thêm test users (email của bạn)
   - Lưu và tiếp tục

## Bước 2: Tạo OAuth Client ID

1. Chọn loại ứng dụng: "Web application"
2. Đặt tên cho client, ví dụ: "Zone Fashion Web Auth"
3. Thêm URI chuyển hướng:
   - `http://localhost/zone-fashion/auth/google-callback`
   - `http://your-domain.com/zone-fashion/auth/google-callback` (nếu có domain thật)
4. Nhấn "Create"
5. Sao chép Client ID và Client Secret

## Bước 3: Cập nhật file cấu hình Google OAuth

1. Mở file `app/config/google.php`
2. Cập nhật Client ID và Client Secret:

```php
'client_id' => 'YOUR_CLIENT_ID_HERE',
'client_secret' => 'YOUR_CLIENT_SECRET_HERE',
```

## Bước 4: Chạy script SQL để cập nhật database

1. Mở phpMyAdmin hoặc công cụ quản lý MySQL của bạn
2. Chọn database zone_fashion
3. Chạy nội dung của file `database/migrations/2024_08_14_000001_add_google_auth_to_users.sql`

## Bước 5: Test chức năng đăng nhập Google

1. Truy cập trang đăng nhập: `http://localhost/zone-fashion/login`
2. Click vào nút "Đăng nhập bằng Google"
3. Chọn tài khoản Google và cho phép Zone Fashion truy cập thông tin
4. Bạn sẽ được chuyển hướng về trang tài khoản sau khi đăng nhập thành công

## Xử lý sự cố

- Nếu gặp lỗi "Error 400: redirect_uri_mismatch", hãy kiểm tra lại URI chuyển hướng đã cấu hình trong Google Cloud Console
- Nếu gặp lỗi CORS, hãy thêm domain của bạn vào danh sách allowed origins
- Nếu gặp lỗi "Invalid client", hãy kiểm tra lại Client ID và Client Secret

## Ghi chú

- Trong môi trường production, hãy đảm bảo sử dụng HTTPS cho tất cả các URL
- Không bao giờ chia sẻ Client Secret với bất kỳ ai hoặc đưa lên GitHub
