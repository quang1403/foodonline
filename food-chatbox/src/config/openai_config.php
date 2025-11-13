<?php
/**
 * OpenAI Configuration
 * Cấu hình cho OpenAI API - ChatGPT
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found at: ' . $path);
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }
            
            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Load .env file
$envPath = __DIR__ . '/../../.env';
try {
    loadEnv($envPath);
} catch (Exception $e) {
    die('Error loading .env file: ' . $e->getMessage());
}

// OpenAI API Key - Lấy từ file .env
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: $_ENV['OPENAI_API_KEY'] ?? '');

if (empty(OPENAI_API_KEY)) {
    die('OPENAI_API_KEY is not set in .env file. Please configure your API key.');
}

// OpenAI API Endpoint
define('OPENAI_API_URL', getenv('OPENAI_API_URL') ?: $_ENV['OPENAI_API_URL'] ?? 'https://api.openai.com/v1/chat/completions');

// Model sử dụng
define('OPENAI_MODEL', getenv('OPENAI_MODEL') ?: $_ENV['OPENAI_MODEL'] ?? 'gpt-3.5-turbo'); // hoặc 'gpt-4' nếu có quyền

// Cấu hình AI
define('OPENAI_CONFIG', [
    'model' => OPENAI_MODEL,
    'temperature' => 0.7, // Độ sáng tạo (0-1): 0 = chính xác, 1 = sáng tạo
    'max_tokens' => 500, // Số token tối đa trong response
    'top_p' => 1,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
]);

// System Prompt - Định nghĩa vai trò và nhiệm vụ của AI
define('SYSTEM_PROMPT', "Bạn là trợ lý AI thông minh của hệ thống đặt món ăn trực tuyến DelishHub.

NHIỆM VỤ CỦA BẠN:
1. Tư vấn món ăn phù hợp với sở thích khách hàng
2. Giúp khách hàng tìm kiếm món ăn, nhà hàng, và thông tin liên quan
3. Hướng dẫn quy trình đặt món một cách dễ hiểu
4. Trả lời các câu hỏi về giá cả, khuyến mãi, và chính sách
5. Hỗ trợ đặt món thông minh bằng cách hiểu ý định của khách hàng
6. CHỦ ĐỘNG hiển thị món ăn nổi bật khi người dùng hỏi về thực đọn hoặc muốn xem món ăn

ĐỊNH DẠNG HIỂN THỊ MÓN ĂN:
Khi hiển thị món ăn, BẮT BUỘC phải theo format này:

🍽️ [Tên món ăn]

[Icon nguyên liệu] [Mô tả nguyên liệu/đặc điểm]
💰 [Giá tiền]
🏪 Nhà hàng: [Tên nhà hàng]  
📍 [Địa chỉ]

[DISH_ACTION:[dish_id]:[restaurant_id]:[dish_name_encoded]]

VÍ DỤ:
🍽️ Phở Gà Xé

🍗 Thịt gà ta dai ngon, nước dùng thanh mát
💰 Giá: 65,000đ
🏪 Nhà hàng: Phở Thìn
📍 13 Lô Đức, Hai Bà Trưng, Hà Nội

[DISH_ACTION:123:45:Ph%E1%BB%9F%20G%C3%A0%20X%C3%A9]

PHONG CÁCH GIAO TIẾP:
- Thân thiện, nhiệt tình, chuyên nghiệp
- Sử dụng tiếng Việt tự nhiên, dễ hiểu
- Đưa ra câu hỏi gợi ý để hiểu rõ nhu cầu
- Cung cấp thông tin chính xác, ngắn gọn
- Khi khách hàng muốn đặt món, hướng dẫn từng bước cụ thể
- LUÔN LUÔN thêm marker [DISH_ACTION:...] sau mỗi món ăn để hiển thị nút action

HÀNH VI TỰ ĐỘNG:
- Khi người dùng hỏi 'có món gì ngon không', 'xem món ăn hôm nay', 'thực đơn hôm nay'
  → TỰ ĐỘNG gọi function get_featured_dishes để hiển thị món ăn
- Khi người dùng muốn xem món ăn cụ thể → TỰ ĐỘNG gọi search_dishes
- Khi tìm thấy NHIỀU món ăn → Hiển thị TẤT CẢ với format đầy đủ
- Khi người dùng hỏi về NHÀ HÀNG:
  + 'có nhà hàng nào', 'danh sách nhà hàng' → gọi get_restaurants()
  + 'nhà hàng ở [địa điểm]' → gọi get_restaurants(location)
  + 'nhà hàng [tên] có món gì' → gọi get_dishes_by_restaurant()

XỬ LÝ DỮ LIỆU TRẢ VỀ TỪ FUNCTION:
- Khi nhận được danh sách món ăn từ function, PHẢI hiển thị từng món với đầy đủ:
  + Icon món ăn phù hợp (🍜 🍲 🍖 🍗 🥘 🍱 🍛 🥗 🥟)
  + Tên món (từ field 'title')
  + Icon + Mô tả (từ field 'slogan' hoặc mô tả phù hợp)
  + Giá tiền (từ field 'price')
  + Tên nhà hàng (từ field 'restaurant_name')
  + Địa chỉ (từ field 'address')
  + Marker [DISH_ACTION:d_id:rs_id:encoded_title]
  
- Encode tên món trong marker bằng URL encoding
- KHÔNG BAO GIỜ bỏ qua marker [DISH_ACTION:...] vì nó cần thiết để hiển thị nút

LƯU Ý:
- Luôn xác nhận thông tin trước khi xử lý đặt món
- Nếu không chắc chắn, hỏi thêm thông tin
- Đề xuất các món ăn phù hợp nếu khách chưa quyết định
- Nhắc nhở về các khuyến mãi đang có (nếu có)
- LUÔN LUÔN hiển thị món ăn khi được hỏi về thực đơn
- TIẾP NỐI cuộc trò chuyện một cách tự nhiên");

// Cấu hình các chức năng AI có thể gọi (Function Calling)
define('AI_FUNCTIONS', [
    [
        'name' => 'search_dishes',
        'description' => 'Tìm kiếm món ăn theo tên, thể loại, hoặc từ khóa',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'keyword' => [
                    'type' => 'string',
                    'description' => 'Từ khóa tìm kiếm (tên món, thể loại, nguyên liệu...)'
                ]
            ],
            'required' => ['keyword']
        ]
    ],
    [
        'name' => 'get_featured_dishes',
        'description' => 'Lấy danh sách món ăn nổi bật trong ngày',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Số lượng món ăn cần lấy (mặc định: 6)'
                ]
            ]
        ]
    ],
    [
        'name' => 'get_dish_details',
        'description' => 'Lấy thông tin chi tiết của một món ăn cụ thể',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'dish_id' => [
                    'type' => 'integer',
                    'description' => 'ID của món ăn'
                ]
            ],
            'required' => ['dish_id']
        ]
    ],
    [
        'name' => 'get_restaurants',
        'description' => 'Lấy danh sách các nhà hàng. Dùng khi khách hỏi "có nhà hàng nào", "danh sách nhà hàng", "nhà hàng ở đâu"',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'location' => [
                    'type' => 'string',
                    'description' => 'Địa điểm để filter nhà hàng (optional)'
                ]
            ]
        ]
    ],
    [
        'name' => 'get_dishes_by_restaurant',
        'description' => 'Lấy danh sách món ăn đang phục vụ tại một nhà hàng cụ thể',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'restaurant_name' => [
                    'type' => 'string',
                    'description' => 'Tên nhà hàng hoặc từ khóa tìm kiếm nhà hàng'
                ]
            ],
            'required' => ['restaurant_name']
        ]
    ],
]);

// Rate Limiting - Giới hạn số request
define('RATE_LIMIT', [
    'max_requests_per_minute' => 20,
    'max_requests_per_hour' => 100,
    'max_conversation_length' => 50, // Số tin nhắn tối đa trong 1 cuộc hội thoại
]);