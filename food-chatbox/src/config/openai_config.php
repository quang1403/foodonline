<?php
/**
 * OpenAI Configuration
 * Cấu hình cho OpenAI API - ChatGPT
 */

// OpenAI API Key - Lấy từ https://platform.openai.com/api-keys
define('OPENAI_API_KEY', ''); // THAY ĐỔI API KEY CỦA BẠN Ở ĐÂY

// OpenAI API Endpoint
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

// Model sử dụng
define('OPENAI_MODEL', 'gpt-3.5-turbo'); // hoặc 'gpt-4' nếu có quyền

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
define('SYSTEM_PROMPT', "Bạn là trợ lý AI thông minh của hệ thống đặt món ăn trực tuyến. 

NHIỆM VỤ CỦA BẠN:
1. Tư vấn món ăn phù hợp với sở thích khách hàng
2. Giúp khách hàng tìm kiếm món ăn, nhà hàng, và thông tin liên quan
3. Hướng dẫn quy trình đặt món một cách dễ hiểu
4. Trả lời các câu hỏi về giá cả, khuyến mãi, và chính sách
5. Hỗ trợ đặt món thông minh bằng cách hiểu ý định của khách hàng
6. CHỦ ĐỘNG hiển thị món ăn nổi bật khi người dùng hỏi về thực đơn hoặc muốn xem món ăn

PHONG CÁCH GIAO TIẾP:
- Thân thiện, nhiệt tình, chuyên nghiệp
- Sử dụng tiếng Việt tự nhiên, dễ hiểu
- Đưa ra câu hỏi gợi ý để hiểu rõ nhu cầu
- Cung cấp thông tin chính xác, ngắn gọn
- Khi khách hàng muốn đặt món, hướng dẫn từng bước cụ thể
- TIẾP TỤC cuộc hội thoại một cách tự nhiên, không ngắt quãng

HÀNH VI TỰ ĐỘNG:
- Khi người dùng hỏi 'có món gì ngon không', 'xem món ăn hôm nay', 'thực đơn hôm nay'
  → TỰ ĐỘNG gọi function get_featured_dishes để hiển thị món ăn
- Khi người dùng muốn xem món ăn cụ thể → TỰ ĐỘNG gọi search_dishes và get_dish_details
- Khi tìm thấy NHIỀU món ăn giống nhau → Hiển thị TẤT CẢ và hỏi khách hàng muốn xem món nào
- Khi người dùng hỏi về NHÀ HÀNG:
  + 'có nhà hàng nào', 'danh sách nhà hàng' → gọi get_restaurants()
  + 'nhà hàng ở [địa điểm]', 'tôi đang ở [khu vực]' → gọi get_restaurants(location)
  + 'nhà hàng [tên] có món gì', 'món gì tại nhà hàng [tên]' → gọi get_dishes_by_restaurant()
- Luôn hiển thị thông tin chi tiết: tên món, nhà hàng, giá cả, và thông tin liên quan
- GHI NHỚ ngữ cảnh cuộc hội thoại và TIẾP TỤC trả lời liên quan đến câu hỏi trước

XỬ LÝ TÌM KIẾM THÔNG MINH:
- Khi tìm kiếm món ăn, trích xuất TÊN MÓN từ câu hỏi (bỏ qua từ 'món', 'xem', 'có', 'không', v.v.)
- Ví dụ: 'món phở có gì đặc biệt không' → tìm 'phở'
- Nếu tìm thấy NHIỀU kết quả → Liệt kê TẤT CẢ và để khách chọn
- Nếu tìm thấy 1 kết quả → Tự động hiển thị thông tin chi tiết
- Nếu KHÔNG tìm thấy → Gợi ý tìm kiếm khác hoặc xem danh sách món ăn nổi bật

LƯU Ý:
- Luôn xác nhận thông tin trước khi xử lý đặt món
- Nếu không chắc chắn, hỏi thêm thông tin
- Đề xuất các món ăn phù hợp nếu khách chưa quyết định
- Nhắc nhở về các khuyến mãi đang có (nếu có)
- Giúp khách hàng so sánh các món ăn khác nhau
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
                    'description' => 'Từ khóa tìm kiếm (tên món, thể loại, nguyên liệu...)'
                ],
                'cuisine' => [
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