<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

header('Content-Type: application/json');

if(empty($_SESSION["adm_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if(!isset($_GET['code'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order code']);
    exit();
}

$order_code = mysqli_real_escape_string($db, $_GET['code']);

// Lấy thông tin tổng hợp của đơn hàng theo mã
$sql = "SELECT 
            uo.order_code,
            uo.u_id,
            u.username,
            u.f_name,
            u.l_name,
            COUNT(uo.o_id) as total_items,
            SUM(uo.price * uo.quantity) as total_amount,
            MAX(uo.address) as address,
            MAX(uo.status) as status,
            MAX(uo.date) as date
        FROM users_orders uo
        INNER JOIN users u ON u.u_id = uo.u_id
        WHERE uo.order_code = '$order_code'
        GROUP BY uo.order_code, uo.u_id, u.username, u.f_name, u.l_name";

$result = mysqli_query($db, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = mysqli_fetch_assoc($result);

// Format dữ liệu
$order['total_amount'] = number_format($order['total_amount'], 0, ',', '.') . ' VNĐ';
$order['formatted_date'] = date('d/m/Y H:i', strtotime($order['date']));

echo json_encode([
    'success' => true,
    'data' => $order
]);
?>
