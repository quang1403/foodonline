-- filepath: food-chatbox/src/database/food_chat_schema.sql
-- Bảng lưu thông tin cuộc hội thoại
CREATE TABLE IF NOT EXISTS food_chat_conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(u_id) ON DELETE SET NULL,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lưu tin nhắn chat
CREATE TABLE IF NOT EXISTS food_chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    role ENUM('user', 'assistant') NOT NULL,
    content TEXT NOT NULL,
    metadata JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES food_chat_conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lưu context/knowledge base cho AI
CREATE TABLE IF NOT EXISTS food_chat_knowledge_base (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category VARCHAR(50) NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    keywords TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert dữ liệu mẫu cho knowledge base (Tiếng Việt)
INSERT INTO food_chat_knowledge_base (category, question, answer, keywords) VALUES
-- Món ăn
('menu', 'có món gì ngon', 'Chúng tôi có nhiều món ngon như: Bún Chả Hương Liên, Phở Thìn, Chả Cá Lã Vọng. Bạn muốn xem chi tiết món nào?', 'món ăn, menu, đặc sản'),
('menu', 'giá bún chả bao nhiêu', 'Bún Chả Đặc Biệt giá 65.000đ, Bún Chả thường giá từ 50.000đ. Bạn muốn đặt món không?', 'giá, bún chả, giá tiền'),
('menu', 'giá phở bao nhiêu', 'Phở có nhiều loại: Phở Tái Lăn 70.000đ, Phở Gầu 75.000đ, Phở Gà Xé 65.000đ', 'giá phở, phở, giá tiền'),

-- Nhà hàng
('restaurant', 'nhà hàng nào ngon', 'Các nhà hàng nổi tiếng: Bún Chả Hương Liên (24 Lê Văn Hưu), Phở Thìn (13 Lò Đúc), Chả Cá Lã Vọng (14 Chả Cá)', 'nhà hàng, quán ăn, địa chỉ'),
('restaurant', 'giờ mở cửa', 'Hầu hết nhà hàng mở cửa từ 6h-22h. Highlands Coffee mở cửa 7h-23h', 'giờ mở cửa, thời gian, mở cửa'),
('restaurant', 'địa chỉ nhà hàng', 'Bạn muốn biết địa chỉ nhà hàng nào? Chúng tôi có: Bún Chả Hương Liên, Phở Thìn, Chả Cá Lã Vọng...', 'địa chỉ, ở đâu, chỗ nào'),

-- Đồ uống
('drink', 'có đồ uống gì', 'Chúng tôi có: Cà Phê Sữa Đá (45k), Bạc Xỉu (50k), Trà Sữa (từ 49k-57k), Sinh Tố (55k)', 'đồ uống, nước, cafe, trà sữa'),
('drink', 'trà sữa nào ngon', 'Trà Sữa Đường Đen (57k), Trà Sữa Trân Châu Hoàng Gia (49k), Trà Sữa Matcha (57k) đều rất được yêu thích', 'trà sữa, milk tea'),

-- Đặt hàng
('order', 'đặt món như thế nào', 'Bạn chọn món trong menu, thêm vào giỏ hàng, sau đó điền thông tin giao hàng và thanh toán', 'đặt món, order, đặt hàng'),
('order', 'thanh toán thế nào', 'Bạn có thể thanh toán khi nhận hàng (COD) hoặc chuyển khoản trước', 'thanh toán, payment, trả tiền'),
('order', 'giao hàng mất bao lâu', 'Thời gian giao hàng trung bình 30-45 phút tùy khoảng cách', 'giao hàng, ship, thời gian giao'),

-- Chung
('general', 'xin chào', 'Xin chào! Tôi là trợ lý ảo của DelishHub. Tôi có thể giúp bạn đặt món ăn, tìm nhà hàng hoặc tư vấn menu. Bạn cần gì?', 'chào, hello, hi'),
('general', 'cảm ơn', 'Rất vui được giúp đỡ bạn! Chúc bạn dùng bữa ngon miệng!', 'cảm ơn, thank, thanks'),
('general', 'liên hệ', 'Bạn có thể liên hệ hotline: 1900-xxxx hoặc email: support@delishhub.com', 'liên hệ, contact, hỗ trợ'),

-- Món bán chạy
('bestseller', 'Món nào bán chạy nhất?', 'Để xem món bán chạy nhất, bạn gõ: "món bán chạy" hoặc "món nào hot"', 'bán chạy,hot,top,nổi tiếng,phổ biến,best seller'),
('bestseller', 'Món nào được yêu thích?', 'Gõ "món bán chạy" để xem danh sách món được khách hàng yêu thích nhất', 'yêu thích,ưa chuộng,nhiều người đặt');