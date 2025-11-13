<?php
require_once __DIR__ . '/../config/openai_config.php';

class OpenAIService {
    private $apiKey;
    private $apiUrl;
    private $model;
    
    public function __construct() {
        $this->apiKey = OPENAI_API_KEY;
        $this->apiUrl = OPENAI_API_URL;
        $this->model = OPENAI_MODEL;
    }
    
    /**
     * Gửi tin nhắn đến OpenAI và nhận phản hồi
     */
    public function sendMessage($messages, $functions = null) {
        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => OPENAI_CONFIG['temperature'],
            'max_tokens' => OPENAI_CONFIG['max_tokens'],
            'top_p' => OPENAI_CONFIG['top_p'],
            'frequency_penalty' => OPENAI_CONFIG['frequency_penalty'],
            'presence_penalty' => OPENAI_CONFIG['presence_penalty'],
        ];
        
        // Thêm function calling nếu có
        if ($functions !== null) {
            $data['functions'] = $functions;
            $data['function_call'] = 'auto';
        }
        
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = isset($errorData['error']['message']) ? $errorData['error']['message'] : 'Unknown error';
            throw new Exception('OpenAI API Error (HTTP ' . $httpCode . '): ' . $errorMsg);
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0])) {
            throw new Exception('Invalid response from OpenAI API');
        }
        
        return $result['choices'][0];
    }
    
    /**
     * Xây dựng messages cho OpenAI từ lịch sử hội thoại
     */
    public function buildMessages($conversationHistory, $userMessage) {
        $messages = [
            ['role' => 'system', 'content' => SYSTEM_PROMPT]
        ];
        
        // Thêm lịch sử hội thoại (giới hạn số tin nhắn)
        $maxHistory = 10; // Giữ 10 tin nhắn gần nhất
        $history = array_slice($conversationHistory, -$maxHistory);
        
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        
        // Thêm tin nhắn mới của user
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];
        
        return $messages;
    }
    
    /**
     * Kiểm tra kết nối với OpenAI API
     */
    public function testConnection() {
        try {
            $testMessage = [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => 'Say "Connection successful"']
            ];
            
            $response = $this->sendMessage($testMessage);
            return [
                'success' => true,
                'message' => 'Kết nối OpenAI API thành công!',
                'response' => $response['message']['content']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
