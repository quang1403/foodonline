<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/food-chatbox/src/controllers/FoodChatController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$controller = new FoodChatController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['message']) && isset($input['session_id'])) {
        $message = $input['message'];
        $session_id = $input['session_id'];
        $user_id = $input['user_id'] ?? null;

        $response = $controller->processMessage($message, $session_id, $user_id);
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>