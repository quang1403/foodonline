<?php
// Sử dụng đường dẫn tuyệt đối
require_once __DIR__ . '/../../../connection/connect.php';

class FoodChatModel {
    private $conn;
    
    public function __construct() {
        global $db;
        $this->conn = $db;
    }
    
    /**
     * Tạo hoặc lấy conversation dựa trên session_id
     */
    public function getOrCreateConversation($session_id, $user_id = null) {
        // Kiểm tra xem conversation đã tồn tại chưa
        $stmt = $this->conn->prepare("
            SELECT * FROM food_chat_conversations WHERE session_id = ?
        ");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Tạo conversation mới
        $stmt = $this->conn->prepare("
            INSERT INTO food_chat_conversations (session_id, user_id) VALUES (?, ?)
        ");
        $stmt->bind_param("si", $session_id, $user_id);
        $stmt->execute();
        
        return [
            'id' => $this->conn->insert_id,
            'session_id' => $session_id,
            'user_id' => $user_id
        ];
    }
    
    /**
     * Lưu tin nhắn vào database
     */
    public function saveMessage($conversation_id, $role, $content, $metadata = null) {
        $metadata_json = $metadata ? json_encode($metadata) : null;
        
        $stmt = $this->conn->prepare("
            INSERT INTO food_chat_messages (conversation_id, role, content, metadata) VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $conversation_id, $role, $content, $metadata_json);
        
        return $stmt->execute();
    }
    
    /**
     * Lấy lịch sử chat của một conversation
     */
    public function getConversationHistory($conversation_id, $limit = 20) {
        $stmt = $this->conn->prepare("
            SELECT * FROM food_chat_messages WHERE conversation_id = ? ORDER BY created_at DESC LIMIT ?
        ");
        $stmt->bind_param("ii", $conversation_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        
        // Đảo ngược để có thứ tự từ cũ đến mới
        return array_reverse($messages);
    }
    
    /**
     * Tìm kiếm món ăn chính xác - chỉ tìm món có chứa từ khóa
     */
    public function searchFood($keyword, $limit = 5) {
        $keyword = strtolower(trim($keyword));
        
        // Tìm kiếm chính xác - món phải chứa từ khóa
        $sql = "SELECT d.*, r.title as restaurant_name, r.address, r.rs_id
                FROM dishes d 
                LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
                WHERE LOWER(d.title) LIKE ?
                ORDER BY 
                    CASE 
                        WHEN LOWER(d.title) LIKE ? THEN 1
                        WHEN LOWER(d.title) LIKE ? THEN 2
                        ELSE 3
                    END
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $exactMatch = $keyword . '%'; // Bắt đầu bằng từ khóa
        $startMatch = '% ' . $keyword . '%'; // Có khoảng trắng trước
        $anyMatch = '%' . $keyword . '%'; // Chứa từ khóa bất kỳ
        
        $stmt->bind_param("sssi", $anyMatch, $exactMatch, $startMatch, $limit);
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $foods = [];
        while ($row = $result->fetch_assoc()) {
            $foods[] = $row;
        }
        
        return $foods;
    }
    
    /**
     * Lấy món ăn theo ID để thêm vào giỏ
     */
    public function getFoodById($food_id) {
        $stmt = $this->conn->prepare("
            SELECT d.*, r.title as restaurant_name, r.address, r.rs_id
            FROM dishes d 
            LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
            WHERE d.d_id = ?
        ");
        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Tìm kiếm theo category/loại món
     */
    public function searchFoodByCategory($category, $limit = 10) {
        $category = strtolower(trim($category));
        
        $stmt = $this->conn->prepare("
            SELECT d.*, r.title as restaurant_name, r.address, r.rs_id
            FROM dishes d 
            LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
            WHERE LOWER(d.title) LIKE ? OR LOWER(d.slogan) LIKE ?
            LIMIT ?
        ");
        $searchTerm = '%' . $category . '%';
        $stmt->bind_param("ssi", $searchTerm, $searchTerm, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $foods = [];
        while ($row = $result->fetch_assoc()) {
            $foods[] = $row;
        }
        
        return $foods;
    }
    
    /**
     * Lấy danh sách món ăn phổ biến
     */
    public function getPopularFoods($limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT d.*, r.title as restaurant_name, r.address, r.rs_id
            FROM dishes d 
            LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
            ORDER BY d.d_id DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $foods = [];
        while ($row = $result->fetch_assoc()) {
            $foods[] = $row;
        }
        
        return $foods;
    }
    
    /**
     * Tìm kiếm trong knowledge base
     */
    public function searchKnowledgeBase($keyword) {
        $keyword = strtolower(trim($keyword));
        
        $stmt = $this->conn->prepare("
            SELECT * FROM food_chat_knowledge_base 
            WHERE LOWER(keywords) LIKE ? OR LOWER(question) LIKE ?
            ORDER BY 
                CASE 
                    WHEN LOWER(question) LIKE ? THEN 1
                    WHEN LOWER(keywords) LIKE ? THEN 2
                    ELSE 3
                END
            LIMIT 1
        ");
        $searchTerm = '%' . $keyword . '%';
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Lấy danh sách nhà hàng (có thể filter theo location)
     */
    public function getRestaurants($location = null) {
        if ($location) {
            $sql = "SELECT * FROM restaurant 
                    WHERE LOWER(address) LIKE ? AND status = 1
                    ORDER BY title ASC 
                    LIMIT 10";
            
            $stmt = $this->conn->prepare($sql);
            $search = '%' . strtolower($location) . '%';
            $stmt->bind_param("s", $search);
        } else {
            $sql = "SELECT * FROM restaurant 
                    WHERE status = 1
                    ORDER BY title ASC 
                    LIMIT 20";
            
            $stmt = $this->conn->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $restaurants = [];
        while ($row = $result->fetch_assoc()) {
            $restaurants[] = $row;
        }
        
        return $restaurants;
    }
    
    /**
     * Tìm kiếm nhà hàng theo từ khóa
     */
    public function searchRestaurant($keyword, $limit = 5) {
        $keyword = strtolower(trim($keyword));
        
        $stmt = $this->conn->prepare("
            SELECT * FROM restaurant 
            WHERE LOWER(title) LIKE ? AND status = 1 
            LIMIT ?
        ");
        $searchTerm = '%' . $keyword . '%';
        $stmt->bind_param("si", $searchTerm, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $restaurants = [];
        while ($row = $result->fetch_assoc()) {
            $restaurants[] = $row;
        }
        
        return $restaurants;
    }
    
    /**
     * Lấy món ăn bán chạy nhất dựa trên số lượng đặt hàng thực tế
     */
    public function getBestSellingFoods($limit = 10) {
        $sql = "SELECT d.*, 
                r.title as restaurant_name, 
                r.address, 
                r.rs_id,
                COALESCE(SUM(uo.quantity), 0) as total_sold,
                COUNT(DISTINCT uo.o_id) as total_orders
                FROM dishes d 
                LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
                LEFT JOIN users_orders uo ON d.title = uo.title
                GROUP BY d.d_id, d.rs_id, d.title, d.slogan, d.price, d.img
                ORDER BY total_sold DESC, total_orders DESC, d.d_id DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $foods = [];
        while ($row = $result->fetch_assoc()) {
            $foods[] = $row;
        }
        
        return $foods;
    }
    
    /**
     * Lấy món ăn theo category với thống kê bán hàng
     */
    public function getFoodsByCategory($category, $limit = 10) {
        $category = strtolower(trim($category));
        
        $sql = "SELECT d.*, 
                r.title as restaurant_name, 
                r.address, 
                r.rs_id,
                COALESCE(SUM(uo.quantity), 0) as total_sold,
                COUNT(DISTINCT uo.o_id) as total_orders
                FROM dishes d 
                LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
                LEFT JOIN users_orders uo ON d.title = uo.title
                WHERE LOWER(d.title) LIKE ?
                GROUP BY d.d_id, d.rs_id, d.title, d.slogan, d.price, d.img
                ORDER BY total_sold DESC, total_orders DESC, d.d_id DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $searchTerm = '%' . $category . '%';
        $stmt->bind_param("si", $searchTerm, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $foods = [];
        while ($row = $result->fetch_assoc()) {
            $foods[] = $row;
        }
        
        return $foods;
    }
    
    /**
     * Lấy thống kê chi tiết món ăn
     */
    public function getFoodStats($food_id) {
        $sql = "SELECT d.*, 
                COALESCE(SUM(uo.quantity), 0) as total_sold,
                COUNT(DISTINCT uo.o_id) as total_orders,
                r.title as restaurant_name, 
                r.address, 
                r.rs_id
                FROM dishes d 
                LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
                LEFT JOIN users_orders uo ON d.title = uo.title
                WHERE d.d_id = ?
                GROUP BY d.d_id, d.rs_id, d.title, d.slogan, d.price, d.img";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Lấy top nhà hàng bán chạy
     */
    public function getTopRestaurants($limit = 5) {
        $sql = "SELECT r.*, 
                COUNT(DISTINCT uo.o_id) as total_orders,
                COALESCE(SUM(uo.quantity), 0) as total_dishes_sold,
                COALESCE(SUM(uo.price * uo.quantity), 0) as total_revenue
                FROM restaurant r
                LEFT JOIN users_orders uo ON r.rs_id = uo.rs_id
                WHERE r.rs_id IN (SELECT DISTINCT rs_id FROM dishes)
                GROUP BY r.rs_id
                ORDER BY total_orders DESC, total_revenue DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $restaurants = [];
        while ($row = $result->fetch_assoc()) {
            $restaurants[] = $row;
        }
        
        return $restaurants;
    }
    
    /**
     * L?y th�ng tin chi ti?t m�n an theo ID
     */
    public function getDishById($dish_id) {
        $sql = "SELECT d.*, r.title as restaurant_name, r.address, r.phone, r.rs_id
                FROM dishes d 
                LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
                WHERE d.d_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $dish_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * L?y danh s�ch m�n an theo t�n nh� h�ng
     */
    public function getDishesByRestaurant($restaurant_name) {
        $sql = "SELECT d.*, r.title as restaurant_name, r.address, r.rs_id
                FROM dishes d 
                INNER JOIN restaurant r ON d.rs_id = r.rs_id 
                WHERE LOWER(r.title) LIKE ?
                ORDER BY d.title ASC
                LIMIT 20";
        
        $stmt = $this->conn->prepare($sql);
        $search = '%' . strtolower($restaurant_name) . '%';
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $dishes = [];
        while ($row = $result->fetch_assoc()) {
            $dishes[] = $row;
        }
        
        return $dishes;
    }
}
