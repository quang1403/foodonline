<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Trả về user_id từ session - sử dụng 'u_id' theo chuẩn của hệ thống
$user_id = isset($_SESSION['u_id']) ? intval($_SESSION['u_id']) : null;

// Debug log
error_log("=== GET_USER API ===");
error_log("Session data: " . print_r($_SESSION, true));
error_log("User ID found: " . ($user_id ?? 'NULL'));

echo json_encode([
    'success' => true,
    'user_id' => $user_id,
    'logged_in' => ($user_id !== null && $user_id > 0),
    'debug' => [
        'session_id' => session_id(),
        'u_id_exists' => isset($_SESSION['u_id']),
        'username_exists' => isset($_SESSION['username'])
    ]
], JSON_UNESCAPED_UNICODE);
?>
