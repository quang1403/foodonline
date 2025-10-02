<?php
include("../connection/connect.php");
session_start();

// Return error response helper function
function sendError($message, $code = 404) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}

// Check admin session
if(empty($_SESSION["adm_id"])) {
    sendError('Unauthorized access', 401);
}

// Validate order ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    sendError('Invalid order ID');
}

try {
    $id = intval($_GET['id']);
    
    // Get order details with related information
    $sql = "SELECT 
                users_orders.*,
                users.username,
                users.f_name,
                users.l_name,
                users.email,
                users.phone,
                restaurant.title as restaurant_name
            FROM users_orders 
            INNER JOIN users ON users.u_id = users_orders.u_id
            LEFT JOIN restaurant ON restaurant.rs_id = users_orders.rs_id
            WHERE users_orders.o_id = ?";
    
    $stmt = mysqli_prepare($db, $sql);
    if(!$stmt) {
        sendError('Database preparation failed', 500);
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    if(!mysqli_stmt_execute($stmt)) {
        sendError('Database execution failed', 500);
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    
    if(!$order) {
        sendError('Order not found');
    }
    
    // Format price for display
    $order['formatted_price'] = number_format($order['price'], 0, ',', '.') . ' VNĐ';
    
    // Format date for display
    $order['formatted_date'] = date('d/m/Y H:i', strtotime($order['date']));
    
    // Send success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $order
    ]);

} catch(Exception $e) {
    sendError('Server error: ' . $e->getMessage(), 500);
}
?>