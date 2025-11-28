<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Trả về user_id từ session
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

echo json_encode([
    'success' => true,
    'user_id' => $user_id,
    'logged_in' => ($user_id !== null && $user_id > 0)
]);
?>
