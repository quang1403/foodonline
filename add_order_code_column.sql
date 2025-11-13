-- Script để thêm cột order_code vào bảng users_orders
-- Chạy script này trong phpMyAdmin hoặc MySQL command line

-- Thêm cột order_code
ALTER TABLE `users_orders` 
ADD COLUMN `order_code` VARCHAR(20) UNIQUE NULL AFTER `o_id`;

-- Tạo index cho cột order_code để tìm kiếm nhanh hơn
CREATE INDEX idx_order_code ON users_orders(order_code);

-- Cập nhật các đơn hàng cũ với mã đơn hàng (optional)
-- UPDATE users_orders SET order_code = CONCAT('ORD', LPAD(o_id, 8, '0')) WHERE order_code IS NULL;
