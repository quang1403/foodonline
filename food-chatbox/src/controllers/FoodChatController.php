<?php
require_once __DIR__ . '/../models/FoodChatModel.php';
require_once __DIR__ . '/../services/OpenAIService.php';

class FoodChatController {
    private $model;
    private $openai;
    
    public function __construct() {
        $this->model = new FoodChatModel();
        $this->openai = new OpenAIService();
    }
    
    /**
     * Xử lý tin nhắn từ người dùng
     */
    public function processMessage($message, $session_id, $user_id = null) {
        try {
            // Lấy hoặc tạo conversation
            $conversation = $this->model->getOrCreateConversation($session_id, $user_id);
            
            // Lưu tin nhắn của user
            $this->model->saveMessage($conversation['id'], 'user', $message);
            
            // Lấy lịch sử hội thoại
            $history = $this->model->getConversationHistory($conversation['id'], 10);
            
            // Xây dựng messages cho OpenAI
            $messages = $this->openai->buildMessages($history, $message);
            
            // Gọi OpenAI API với Function Calling
            $response = $this->openai->sendMessage($messages, AI_FUNCTIONS);
            
            $reply = '';
            $functionCalled = false;
            
            // Xử lý response
            if (isset($response['message']['function_call'])) {
                // AI muốn gọi function
                $functionCalled = true;
                $functionName = $response['message']['function_call']['name'];
                $functionArgs = json_decode($response['message']['function_call']['arguments'], true);
                
                // Thực thi function
                $functionResult = $this->executeFunction($functionName, $functionArgs);
                
                // Gửi lại kết quả cho AI để tạo response cuối cùng
                $messages[] = $response['message'];
                $messages[] = [
                    'role' => 'function',
                    'name' => $functionName,
                    'content' => json_encode($functionResult, JSON_UNESCAPED_UNICODE)
                ];
                
                // Gọi lại OpenAI để có response cuối cùng
                $finalResponse = $this->openai->sendMessage($messages);
                $reply = $finalResponse['message']['content'];
                
            } else {
                // Response thông thường
                $reply = $response['message']['content'];
            }
            
            // Lưu tin nhắn phản hồi
            $this->model->saveMessage($conversation['id'], 'assistant', $reply);
            
            return [
                'success' => true,
                'reply' => $reply,
                'conversation_id' => $conversation['id'],
                'function_called' => $functionCalled
            ];
            
        } catch (Exception $e) {
            // Fallback về logic cũ nếu OpenAI fail
            return $this->fallbackResponse($message, $session_id, $user_id, $e->getMessage());
        }
    }
    
    /**
     * Fallback response khi OpenAI không hoạt động
     */
    private function fallbackResponse($message, $session_id, $user_id, $error) {
        try {
            $conversation = $this->model->getOrCreateConversation($session_id, $user_id);
            $this->model->saveMessage($conversation['id'], 'user', $message);
            
            $reply = $this->generateReply($message, $conversation['id']);
            
            $this->model->saveMessage($conversation['id'], 'assistant', $reply);
            
            return [
                'success' => true,
                'reply' => $reply,
                'conversation_id' => $conversation['id'],
                'fallback' => true,
                'error' => $error
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Xin lỗi, hệ thống đang gặp sự cố. Vui lòng thử lại sau.'
            ];
        }
    }
    
    /**
     * Thực thi function được AI gọi
     */
    private function executeFunction($functionName, $args) {
        switch ($functionName) {
            case 'search_dishes':
                $keyword = $args['keyword'] ?? '';
                $dishes = $this->model->searchFood($keyword, 8);
                return [
                    'success' => true,
                    'dishes' => $dishes,
                    'count' => count($dishes),
                    'keyword' => $keyword
                ];
                
            case 'get_featured_dishes':
                $limit = $args['limit'] ?? 6;
                $dishes = $this->model->getBestSellingFoods($limit);
                return [
                    'success' => true,
                    'dishes' => $dishes,
                    'count' => count($dishes)
                ];
                
            case 'get_dish_details':
                $dishId = $args['dish_id'] ?? 0;
                $dish = $this->model->getDishById($dishId);
                return [
                    'success' => $dish !== null,
                    'dish' => $dish
                ];
                
            case 'get_restaurants':
                $location = $args['location'] ?? null;
                $restaurants = $this->model->getRestaurants($location);
                return [
                    'success' => true,
                    'restaurants' => $restaurants,
                    'count' => count($restaurants)
                ];
                
            case 'get_dishes_by_restaurant':
                $restaurantName = $args['restaurant_name'] ?? '';
                $dishes = $this->model->getDishesByRestaurant($restaurantName);
                return [
                    'success' => true,
                    'dishes' => $dishes,
                    'count' => count($dishes),
                    'restaurant_name' => $restaurantName
                ];
                
            default:
                return [
                    'success' => false,
                    'error' => 'Function not found: ' . $functionName
                ];
        }
    }
    
    /**
     * Tạo phản hồi dựa trên tin nhắn
     */
    private function generateReply($message, $conversation_id = null) {
        $message_lower = strtolower(trim($message));
        
        // 1. Xử lý lời chào
        if (preg_match('/^(xin chào|chào|hello|hi|hey)$/i', $message_lower)) {
            return "Xin chào! 👋 Tôi là trợ lý ảo của DelishHub.\n\n" .
                   "Tôi có thể giúp bạn:\n" .
                   "🔍 Tìm món: 'món phở nào', 'có bún gì'\n" .
                   "🔥 Món bán chạy: 'món bán chạy', 'món nào hot'\n" .
                   "🏪 Tìm nhà hàng: 'nhà hàng nào ngon'\n" .
                   "💰 Hỏi giá: 'giá phở'\n" .
                   "📋 Gợi ý: 'món nào ngon'\n" .
                   "🛒 Đặt hàng: 'đặt phở bò'\n\n" .
                   "Bạn muốn tìm món gì? 😊";
        }
        
        // 2. Xử lý yêu cầu xem món bán chạy - CHỈ LẤY 3-4 MÓN
        if (preg_match('/(món|món ăn|món nào)\s*(bán chạy|hot|phổ biến|nổi tiếng|best|top)/i', $message_lower)) {
            $bestSelling = $this->model->getBestSellingFoods(4); // Chỉ lấy 4 món
            if (!empty($bestSelling)) {
                return $this->formatBestSellingResponse($bestSelling);
            }
        }
        
        // 3. Xử lý top nhà hàng - CHỈ LẤY 3 NHÀ HÀNG
        if (preg_match('/(nhà hàng|quán)\s*(bán chạy|hot|phổ biến|nổi tiếng|top)/i', $message_lower)) {
            $topRestaurants = $this->model->getTopRestaurants(3); // Chỉ lấy 3 nhà hàng
            if (!empty($topRestaurants)) {
                return $this->formatTopRestaurantsResponse($topRestaurants);
            }
        }
        
        // 4. Xử lý lệnh đặt hàng trực tiếp
        if (preg_match('/^(đặt|order|mua)\s+(.+)/i', $message_lower, $matches)) {
            $keyword = trim($matches[2]);
            return $this->handleOrderRequest($keyword);
        }
        
        // 5. Xử lý các câu hỏi dạng "có ... gì/nào"
        if (preg_match('/(có|có những)\s+(loại|món)?\s*(\w+)\s+(gì|nào)/i', $message_lower, $matches)) {
            $keyword = $matches[3]; // Lấy từ khóa chính (bún, phở, cơm...)
            
            $foods = $this->model->searchFood($keyword, 8);
            
            if (!empty($foods)) {
                return "🍜 Chúng tôi có các món " . $keyword . " sau:\n\n" . 
                       $this->formatFoodResponseSeparate($foods, $keyword);
            } else {
                return "😔 Xin lỗi, hiện tại chưa có món " . $keyword . ".\n\n" .
                       "💡 Bạn có thể thử:\n" .
                       "• 'món bán chạy' - xem món hot nhất\n" .
                       "• 'có món phở gì' - tìm món phở\n" .
                       "• 'có bún gì' - tìm món bún";
            }
        }
        
        // 6. Xử lý câu hỏi dạng "... gì/nào"
        if (preg_match('/(\w+)\s+(gì|nào)/i', $message_lower, $matches)) {
            $keyword = $matches[1]; // phở, bún, cơm...
            
            // Loại bỏ các từ không cần thiết
            $stopWords = ['món', 'có', 'tìm', 'xem', 'được', 'không'];
            if (!in_array($keyword, $stopWords)) {
                $foods = $this->model->searchFood($keyword, 8);
                
                if (!empty($foods)) {
                    return "🍜 Các món " . $keyword . " hiện có:\n\n" . 
                           $this->formatFoodResponseSeparate($foods, $keyword);
                }
            }
        }
        
        // 7. Tìm kiếm món ăn theo từ khóa thông thường
        $keyword = $this->extractFoodKeyword($message_lower);
        
        if ($keyword && strlen($keyword) >= 2) {
            $foods = $this->model->searchFood($keyword, 5);
            
            if (!empty($foods)) {
                return $this->formatFoodResponseSeparate($foods, $keyword);
            }
        }
        
        // 8. Tìm kiếm trong knowledge base
        $kb = $this->model->searchKnowledgeBase($message_lower);
        if ($kb) {
            return $kb['answer'];
        }
        
        // 9. Hiển thị món phổ biến (gợi ý chung) - CHỈ LẤY 4 MÓN
        if (preg_match('/(món gì|có gì|menu|thực đơn|gợi ý)/i', $message_lower)) {
            $popular = $this->model->getBestSellingFoods(4); // Chỉ lấy 4 món
            if (!empty($popular)) {
                return "🔥 Các món ăn được yêu thích:\n\n" . $this->formatFoodResponseSeparate($popular, '');
            }
        }
        
        // 10. Phản hồi mặc định
        return "😊 Xin lỗi, tôi chưa hiểu câu hỏi của bạn.\n\n" .
               "💡 Gợi ý:\n" .
               "• 'món bán chạy' - xem món hot nhất\n" .
               "• 'có món phở gì' - tìm món phở\n" .
               "• 'có bún nào' - tìm món bún\n" .
               "• 'đặt phở bò' - đặt món\n" .
               "• 'món nào ngon' - xem gợi ý";
    }
    
    /**
     * Trích xuất từ khóa món ăn từ câu hỏi
     */
    private function extractFoodKeyword($message) {
        // Loại bỏ các từ không cần thiết
        $stopWords = ['có', 'những', 'loại', 'món', 'gì', 'nào', 'tìm', 'xem', 'được', 'không', 'à', 'nhé', 'ạ', 'cho', 'tôi', 'mình', 'được', 'ở', 'đây', 'quán'];
        
        $message = preg_replace('/\s+/', ' ', $message); // Loại bỏ khoảng trắng thừa
        $words = explode(' ', $message);
        $keywords = [];
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) >= 2 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        
        return !empty($keywords) ? implode(' ', $keywords) : '';
    }
    
    /**
     * Xử lý yêu cầu đặt hàng
     */
    private function handleOrderRequest($keyword) {
        if (empty($keyword)) {
            return "Bạn muốn đặt món gì? 😊\nVí dụ: 'đặt phở bò', 'đặt bún chả'";
        }
        
        $foods = $this->model->searchFood($keyword, 3);
        
        if (empty($foods)) {
            return "😔 Xin lỗi, tôi không tìm thấy món '{$keyword}'.\n\n" .
                   "💡 Bạn có thể:\n" .
                   "• Thử tên món khác\n" .
                   "• Gõ 'món nào ngon' để xem gợi ý";
        }
        
        return $this->formatFoodResponseSeparate($foods, $keyword, true);
    }
    
    /**
     * Format hiển thị từng món với nút riêng biệt
     */
    private function formatFoodResponseSeparate($foods, $query = '', $isOrder = false) {
        $count = count($foods);
        $messages = [];
        
        // Mỗi món là một message riêng
        foreach ($foods as $food) {
            $msg = "[FOOD_CARD_START]\n";
            $msg .= "🍜 " . $food['title'] . "\n\n";
            
            // Mô tả
            if (!empty($food['slogan'])) {
                $slogan = trim(strip_tags($food['slogan']));
                if (strlen($slogan) > 100) {
                    $slogan = substr($slogan, 0, 100) . '...';
                }
                $msg .= "📝 " . $slogan . "\n\n";
            }
            
            // Giá
            $msg .= "💰 Giá: " . number_format($food['price'], 0, ',', '.') . "đ\n";
            
            // Nhà hàng
            if (!empty($food['restaurant_name'])) {
                $msg .= "🏪 Nhà hàng: " . $food['restaurant_name'] . "\n";
            }
            
            // Địa chỉ
            if (!empty($food['address'])) {
                $msg .= "📍 " . $food['address'] . "\n";
            }
            
            // Thêm marker cho nút hành động
            $msg .= "\n[DISH_ACTION:" . $food['d_id'] . ":" . $food['rs_id'] . ":" . urlencode($food['title']) . "]";
            $msg .= "[FOOD_CARD_END]";
            
            $messages[] = $msg;
        }
        
        // Footer message
        if (!$isOrder && $count > 0) {
            $messages[] = "💬 Click nút để đặt hàng món bạn thích nhé!";
        }
        
        return implode("\n\n", $messages);
    }
    
    /**
     * Format thông tin nhà hàng
     */
    private function formatRestaurantResponse($restaurants) {
        $reply = "";
        
        foreach ($restaurants as $index => $rest) {
            $reply .= "━━━━━━━━━━━━━━━━\n";
            $reply .= ($index + 1) . ". 🏪 " . strtoupper($rest['title']) . "\n\n";
            
            if (!empty($rest['address'])) {
                $reply .= "📍 " . $rest['address'] . "\n";
            }
            
            if (!empty($rest['o_hr']) && !empty($rest['c_hr'])) {
                $reply .= "⏰ " . $rest['o_hr'] . " - " . $rest['c_hr'] . "\n";
            }
            
            $reply .= "\n[RESTAURANT:" . $rest['rs_id'] . "]\n";
            $reply .= "━━━━━━━━━━━━━━━━\n\n";
        }
        
        return $reply;
    }
    
    /**
     * Format hiển thị món bán chạy - COMPACT VERSION
     */
    private function formatBestSellingResponse($foods) {
        $count = count($foods);
        $messages = [];
        
        // Kiểm tra xem có món nào thực sự được bán không
        $hasSales = false;
        foreach ($foods as $food) {
            if (isset($food['total_sold']) && $food['total_sold'] > 0) {
                $hasSales = true;
                break;
            }
        }
        
        if ($hasSales) {
            $messages[] = "🔥 TOP " . $count . " MÓN BÁN CHẠY:\n";
        } else {
            $messages[] = "🔥 TOP " . $count . " MÓN PHỔ BIẾN:\n";
        }
        
        foreach ($foods as $index => $food) {
            $msg = "[FOOD_CARD_START]\n";
            
            // Chỉ dùng huy chương cho top 3
            $badge = "";
            if ($index == 0) {
                $badge = "🥇 ";
            } elseif ($index == 1) {
                $badge = "🥈 ";
            } elseif ($index == 2) {
                $badge = "🥉 ";
            } elseif ($index == 3) {
                $badge = "#4 ";
            }
            
            $msg .= $badge . "🍜 " . $food['title'] . "\n\n";
            
            // Hiển thị số lượng đã bán nếu có
            if (isset($food['total_sold']) && $food['total_sold'] > 0) {
                $msg .= "📊 Đã bán: " . $food['total_sold'] . " suất";
                if (isset($food['total_orders']) && $food['total_orders'] > 0) {
                    $msg .= " (" . $food['total_orders'] . " đơn)";
                }
                $msg .= "\n";
            }
            
            // Mô tả - rút ngắn xuống 80 ký tự
            if (!empty($food['slogan'])) {
                $slogan = trim(strip_tags($food['slogan']));
                if (strlen($slogan) > 80) {
                    $slogan = substr($slogan, 0, 80) . '...';
                }
                $msg .= "📝 " . $slogan . "\n\n";
            }
            
            // Giá
            $msg .= "💰 " . number_format($food['price'], 0, ',', '.') . "đ\n";
            
            // Nhà hàng - gọn hơn
            if (!empty($food['restaurant_name'])) {
                $msg .= "🏪 " . $food['restaurant_name'] . "\n";
            }
            
            // Thêm marker cho nút hành động
            $msg .= "\n[DISH_ACTION:" . $food['d_id'] . ":" . $food['rs_id'] . ":" . urlencode($food['title']) . "]";
            $msg .= "[FOOD_CARD_END]";
            
            $messages[] = $msg;
        }
        
        if ($hasSales) {
            $messages[] = "⭐ Đây là những món khách đặt nhiều nhất!";
        } else {
            $messages[] = "💡 Hãy là người đầu tiên đặt món!";
        }
        
        return implode("\n\n", $messages);
    }
    
    /**
     * Format hiển thị top nhà hàng - COMPACT VERSION
     */
    private function formatTopRestaurantsResponse($restaurants) {
        $reply = "🏆 TOP 3 NHÀ HÀNG BÁN CHẠY:\n\n";
        
        foreach ($restaurants as $index => $rest) {
            $badge = "";
            if ($index == 0) $badge = "🥇 ";
            elseif ($index == 1) $badge = "🥈 ";
            elseif ($index == 2) $badge = "🥉 ";
            
            $reply .= "━━━━━━━━━━━━━━━━\n";
            $reply .= $badge . "🏪 " . $rest['title'] . "\n\n";
            
            // Thống kê gọn hơn
            if (isset($rest['total_orders']) && $rest['total_orders'] > 0) {
                $reply .= "📊 " . $rest['total_orders'] . " đơn";
            }
            if (isset($rest['total_dishes_sold']) && $rest['total_dishes_sold'] > 0) {
                $reply .= " • " . $rest['total_dishes_sold'] . " món\n";
            } else {
                $reply .= "\n";
            }
            
            if (!empty($rest['address'])) {
                $reply .= "📍 " . $rest['address'] . "\n";
            }
            
            $reply .= "\n[RESTAURANT:" . $rest['rs_id'] . "]\n";
            $reply .= "━━━━━━━━━━━━━━━━\n\n";
        }
        
        return $reply;
    }
}
