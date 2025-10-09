<?php
include("../connection/connect.php");
session_start();

// Debug: Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header JSON
header('Content-Type: application/json');

if(empty($_SESSION["res_admin_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Vui lòng đăng nhập lại']);
    exit();
}

try {
    $res_admin_id = $_SESSION["res_admin_id"];
    $res_query = mysqli_query($db, "SELECT rs_id FROM restaurant_admin WHERE id='$res_admin_id'");
    
    if(!$res_query) {
        throw new Exception('Lỗi truy vấn database: ' . mysqli_error($db));
    }
    
    $res_row = mysqli_fetch_assoc($res_query);
    if(!$res_row) {
        throw new Exception('Không tìm thấy thông tin nhà hàng');
    }
    
    $res_id = $res_row['rs_id'];

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Kiểm tra dữ liệu POST
        if(!isset($_POST['order_id']) || !isset($_POST['status'])) {
            throw new Exception('Thiếu thông tin đơn hàng hoặc trạng thái');
        }
        
        $order_id = (int)$_POST['order_id'];
        $status = mysqli_real_escape_string($db, $_POST['status']);
        
        if($order_id <= 0) {
            throw new Exception('ID đơn hàng không hợp lệ');
        }
        
        // Kiểm tra order thuộc về restaurant này
        $check_query = mysqli_query($db, "SELECT * FROM users_orders WHERE o_id='$order_id' AND rs_id='$res_id'");
        if(!$check_query) {
            throw new Exception('Lỗi kiểm tra đơn hàng: ' . mysqli_error($db));
        }
        
        if(!mysqli_num_rows($check_query)) {
            throw new Exception('Không tìm thấy đơn hàng hoặc bạn không có quyền cập nhật');
        }
        
        // Cập nhật pending_status (không dùng cột remark)
        $update_query = "UPDATE users_orders SET 
                         pending_status = '$status'
                         WHERE o_id = '$order_id' AND rs_id = '$res_id'";
        
        $result = mysqli_query($db, $update_query);
        if(!$result) {
            throw new Exception('Lỗi cập nhật database: ' . mysqli_error($db));
        }
        
        if(mysqli_affected_rows($db) > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Đã gửi yêu cầu cập nhật trạng thái đến admin để phê duyệt'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Không có thay đổi nào được thực hiện'
            ]);
        }
    } else {
        throw new Exception('Phương thức không được hỗ trợ');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>