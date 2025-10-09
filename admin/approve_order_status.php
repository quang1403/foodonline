<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["adm_id"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action']; // 'approve' hoặc 'reject'
    $admin_id = $_SESSION["adm_id"];
    
    if($action == 'approve') {
        // Phê duyệt: chuyển pending_status thành status chính thức
        $update_query = "UPDATE users_orders SET 
                         status = pending_status,
                         pending_status = NULL,
                         approved_by = '$admin_id',
                         approved_at = NOW()
                         WHERE o_id = '$order_id' AND pending_status IS NOT NULL";
        
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
        // Từ chối: xóa pending_status
        $update_query = "UPDATE users_orders SET 
                         pending_status = NULL
                         WHERE o_id = '$order_id' AND pending_status IS NOT NULL";
        
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