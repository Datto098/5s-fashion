# PlantUML Fix Guide

## Vấn đề
Các file PlantUML có sử dụng `!define` statements không tương thích với PlantText.com

## Giải pháp
Thay thế tất cả:
- `!define ENTITY class` → xóa dòng này
- `!define CONTROLLER class` → xóa dòng này
- `!define SERVICE class` → xóa dòng này
- `ENTITY ClassName` → `class ClassName`
- `CONTROLLER ClassName` → `class ClassName`
- `SERVICE ClassName` → `class ClassName`

## Files cần sửa:
1. ✅ user-registration-class.puml (đã sửa)
2. ✅ user-login-class.puml (đã sửa)
3. product-search-class.puml
4. add-to-cart-class.puml
5. checkout-process-class.puml
6. product-management-class.puml
7. order-management-class.puml
8. product-review-class.puml

## Cách sửa nhanh:
Mở từng file và tìm kiếm thay thế:
1. Xóa 3 dòng đầu có `!define`
2. Find & Replace: `ENTITY ` → `class `
3. Find & Replace: `CONTROLLER ` → `class `
4. Find & Replace: `SERVICE ` → `class `

## Test:
Sau khi sửa, copy nội dung file .puml vào https://www.planttext.com/ để kiểm tra.
