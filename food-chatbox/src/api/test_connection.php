<?php
/**
 * Test OpenAI API Connection
 * Endpoint để kiểm tra kết nối với OpenAI API
 */

require_once __DIR__ . '/../services/OpenAIService.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $service = new OpenAIService();
    $result = $service->testConnection();
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
