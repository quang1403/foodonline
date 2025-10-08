<?php
include("../connection/connect.php");
header('Content-Type: application/json');
error_reporting(0);
session_start();

// Helper function to send JSON response
function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Check admin session
if(empty($_SESSION["adm_id"])) {
    sendResponse(false, 'Unauthorized access');
}

// Only accept POST requests
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Method not allowed');
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if(!isset($input['order_id']) || !isset($input['status']) || !isset($input['remark'])) {
    sendResponse(false, 'Missing required fields');
}

$order_id = intval($input['order_id']);
$status = trim($input['status']);
$remark = trim($input['remark']);

// Validate order ID
if($order_id <= 0) {
    sendResponse(false, 'Invalid order ID');
}

// Validate status
$valid_statuses = ['preparing', 'prepared', 'in process', 'closed', 'rejected'];
if(!in_array($status, $valid_statuses)) {
    sendResponse(false, 'Invalid status');
}

// Validate remark
if(empty($remark)) {
    sendResponse(false, 'Remark is required');
}

try {
    // Begin transaction
    mysqli_autocommit($db, false);
    
    // Check if order exists
    $check_sql = "SELECT o_id FROM users_orders WHERE o_id = ?";
    $check_stmt = mysqli_prepare($db, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $order_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if(mysqli_num_rows($check_result) === 0) {
        mysqli_rollback($db);
        sendResponse(false, 'Order not found');
    }
    
    // Insert remark
    $remark_sql = "INSERT INTO remark(frm_id, status, remark) VALUES(?, ?, ?)";
    $remark_stmt = mysqli_prepare($db, $remark_sql);
    mysqli_stmt_bind_param($remark_stmt, "iss", $order_id, $status, $remark);
    
    if(!mysqli_stmt_execute($remark_stmt)) {
        mysqli_rollback($db);
        sendResponse(false, 'Failed to insert remark');
    }
    
    // Update order status
    $update_sql = "UPDATE users_orders SET status = ? WHERE o_id = ?";
    $update_stmt = mysqli_prepare($db, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $status, $order_id);
    
    if(!mysqli_stmt_execute($update_stmt)) {
        mysqli_rollback($db);
        sendResponse(false, 'Failed to update order status');
    }
    
    // Commit transaction
    mysqli_commit($db);
    mysqli_autocommit($db, true);
    
    // Get updated order data for response
    $order_sql = "SELECT 
                      users_orders.*,
                      users.username,
                      users.f_name,
                      users.l_name
                  FROM users_orders 
                  INNER JOIN users ON users.u_id = users_orders.u_id
                  WHERE users_orders.o_id = ?";
    
    $order_stmt = mysqli_prepare($db, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "i", $order_id);
    mysqli_stmt_execute($order_stmt);
    $order_result = mysqli_stmt_get_result($order_stmt);
    $order_data = mysqli_fetch_assoc($order_result);
    
    // Format status for display
    $status_info = getStatusInfo($status);
    
    sendResponse(true, 'Order status updated successfully', [
        'order_id' => $order_id,
        'new_status' => $status,
        'status_info' => $status_info,
        'formatted_price' => number_format($order_data['price'], 0, ',', '.') . ' VNĐ',
        'formatted_date' => date('d/m/Y H:i', strtotime($order_data['date']))
    ]);
    
} catch(Exception $e) {
    mysqli_rollback($db);
    sendResponse(false, 'Server error: ' . $e->getMessage());
}

function getStatusInfo($status) {
    switch($status) {
        case '':
            return [
                'class' => 'bg-info',
                'text' => 'Chờ xác nhận',
                'icon' => 'clock'
            ];
        case 'preparing':
            return [
                'class' => 'bg-secondary',
                'text' => 'Đang chuẩn bị',
                'icon' => 'hourglass-half'
            ];
        case 'prepared':
            return [
                'class' => 'bg-primary',
                'text' => 'Đã chuẩn bị',
                'icon' => 'check'
            ];
        case 'in process':
            return [
                'class' => 'bg-warning',
                'text' => 'Đang giao',
                'icon' => 'motorcycle'
            ];
        case 'closed':
            return [
                'class' => 'bg-success',
                'text' => 'Đã giao',
                'icon' => 'check-circle'
            ];
        case 'rejected':
            return [
                'class' => 'bg-danger',
                'text' => 'Đã hủy',
                'icon' => 'times-circle'
            ];
        default:
            return [
                'class' => 'bg-secondary',
                'text' => 'Không xác định',
                'icon' => 'question'
            ];
    }
}
?>