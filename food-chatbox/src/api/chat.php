<?php
require_once __DIR__ . '/../controllers/FoodChatController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$controller = new FoodChatController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Debug: log input
    error_log("=== CHAT API DEBUG ===");
    error_log("Chat API Input: " . print_r($input, true));
    error_log("User ID value: " . ($input['user_id'] ?? 'NOT SET'));
    error_log("User ID type: " . gettype($input['user_id'] ?? null));
    
    if (isset($input['message']) && isset($input['session_id'])) {
        $message = $input['message'];
        $session_id = $input['session_id'];
        $user_id = $input['user_id'] ?? null;
        
        error_log("Processing - Message: $message, User ID: " . ($user_id ?? 'NULL'));

        $response = $controller->processMessage($message, $session_id, $user_id);
        
        error_log("Response success: " . ($response['success'] ? 'TRUE' : 'FALSE'));
        if (isset($response['reply'])) {
            error_log("Response reply (first 100 chars): " . substr($response['reply'], 0, 100));
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid input.',
            'debug' => $input
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>