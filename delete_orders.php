    <?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Xử lý xóa/hủy đơn hàng theo order_id (có thể là order_code hoặc date)
if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $delete = isset($_GET['delete']) ? true : false;
    
    if($delete) {
        // Xóa hoàn toàn đơn hàng
        mysqli_query($db, "DELETE FROM users_orders WHERE (order_code='".$order_id."' OR DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s')='".$order_id."')");
    } else {
        // Hủy đơn hàng (set status = rejected)
        mysqli_query($db, "UPDATE users_orders SET status='rejected' WHERE (order_code='".$order_id."' OR DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s')='".$order_id."')");
    }
} else if(isset($_GET['order_del'])) {
    // Backward compatibility: xóa theo o_id
    mysqli_query($db,"DELETE FROM users_orders WHERE o_id = '".$_GET['order_del']."'");
}

header("location:your_orders.php"); 
?>
    