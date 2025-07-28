-- Sample data for chatbot testing
-- Insert some sample products if not exists

INSERT IGNORE INTO categories (id, name, slug, description, status, created_at, updated_at) VALUES
(1, 'Áo thun', 'ao-thun', 'Các loại áo thun thời trang', 'active', NOW(), NOW()),
(2, 'Quần jeans', 'quan-jeans', 'Quần jeans nam nữ', 'active', NOW(), NOW()),
(3, 'Váy đầm', 'vay-dam', 'Váy đầm công sở, dạo phố', 'active', NOW(), NOW()),
(4, 'Phụ kiện', 'phu-kien', 'Túi xách, giày dép, trang sức', 'active', NOW(), NOW());

INSERT IGNORE INTO products (id, category_id, name, slug, description, price, discount_percentage, image, status, created_at, updated_at) VALUES
(1, 1, 'Áo thun cotton basic', 'ao-thun-cotton-basic', 'Áo thun cotton 100% cao cấp, form dáng chuẩn, nhiều màu sắc', 299000, 20, 'ao-thun-basic.jpg', 'active', '2025-07-20 00:00:00', NOW()),
(2, 1, 'Áo thun polo nam', 'ao-thun-polo-nam', 'Áo thun polo lịch lãm cho nam giới, chất liệu cotton mềm mại', 450000, 15, 'ao-polo-nam.jpg', 'active', '2025-07-22 00:00:00', NOW()),
(3, 2, 'Quần jeans skinny nữ', 'quan-jeans-skinny-nu', 'Quần jeans skinny fit nữ, co giãn tốt, tôn dáng', 599000, 25, 'jeans-skinny-nu.jpg', 'active', '2025-07-25 00:00:00', NOW()),
(4, 3, 'Váy đầm công sở', 'vay-dam-cong-so', 'Váy đầm công sở thanh lịch, chất liệu cao cấp', 799000, 30, 'vay-cong-so.jpg', 'active', '2025-07-24 00:00:00', NOW()),
(5, 1, 'Áo sơ mi trắng', 'ao-so-mi-trang', 'Áo sơ mi trắng basic, phù hợp mọi phong cách', 399000, 0, 'ao-so-mi-trang.jpg', 'active', '2025-07-28 00:00:00', NOW()),
(6, 4, 'Túi xách nữ', 'tui-xach-nu', 'Túi xách da cao cấp cho nữ, thiết kế sang trọng', 1299000, 35, 'tui-xach-nu.jpg', 'active', '2025-07-26 00:00:00', NOW()),
(7, 2, 'Quần short jean nam', 'quan-short-jean-nam', 'Quần short jean nam mùa hè, thoáng mát', 349000, 10, 'short-jean-nam.jpg', 'active', '2025-07-23 00:00:00', NOW()),
(8, 3, 'Chân váy midi', 'chan-vay-midi', 'Chân váy midi xòe nhẹ, phong cách Hàn Quốc', 459000, 20, 'chan-vay-midi.jpg', 'active', '2025-07-21 00:00:00', NOW());

-- Sample order data for best selling calculation
INSERT IGNORE INTO orders (id, user_id, total_amount, status, created_at, updated_at) VALUES
(1, 1, 1500000, 'completed', '2025-07-20 10:00:00', NOW()),
(2, 1, 899000, 'completed', '2025-07-22 14:30:00', NOW()),
(3, 1, 2100000, 'completed', '2025-07-25 16:00:00', NOW());

INSERT IGNORE INTO order_items (id, order_id, product_id, quantity, price, created_at, updated_at) VALUES
(1, 1, 1, 3, 299000, '2025-07-20 10:00:00', NOW()),
(2, 1, 2, 1, 450000, '2025-07-20 10:00:00', NOW()),
(3, 2, 3, 1, 599000, '2025-07-22 14:30:00', NOW()),
(4, 3, 4, 2, 799000, '2025-07-25 16:00:00', NOW()),
(5, 3, 6, 1, 1299000, '2025-07-25 16:00:00', NOW());
