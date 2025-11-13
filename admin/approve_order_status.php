<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["adm_id"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_code = mysqli_real_escape_string($db, $_POST['order_code']);
    $action = $_POST['action']; // 'approve' hoặc 'reject'
    $admin_id = $_SESSION["adm_id"];
    
    if($action == 'approve') {
        // Phê duyệt: chuyển pending_status thành status chính thức cho tất cả items trong order_code
        $update_query = "UPDATE users_orders SET 
                         status = pending_status,
                         pending_status = NULL,
                         approved_by = '$admin_id',
                         approved_at = NOW()
                         WHERE order_code = '$order_code' AND pending_status IS NOT NULL";
        
        if(mysqli_query($db, $update_query)) {
            if(mysqli_affected_rows($db) > 0) {
                echo json_encode(['success' => true, 'message' => 'Đã phê duyệt trạng thái thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có trạng thái chờ phê duyệt']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật database: ' . mysqli_error($db)]);
        }
        
    } elseif($action == 'reject') {
        // Từ chối: xóa pending_status cho tất cả items trong order_code
        $update_query = "UPDATE users_orders SET 
                         pending_status = NULL
                         WHERE order_code = '$order_code' AND pending_status IS NOT NULL";
        
        if(mysqli_query($db, $update_query)) {
            if(mysqli_affected_rows($db) > 0) {
                echo json_encode(['success' => true, 'message' => 'Đã từ chối phê duyệt']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có trạng thái chờ phê duyệt']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật database: ' . mysqli_error($db)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>