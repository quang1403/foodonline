<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

header('Content-Type: application/json');

if(empty($_SESSION["adm_id"])) {
    echo json_encode(["success" => false, "message" => "Chưa đăng nhập admin"]);
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if($order_id <= 0) {
    echo json_encode(["success" => false, "message" => "ID đơn hàng không hợp lệ"]);
    exit();
}

$ret1 = mysqli_query($db, "SELECT * FROM users_orders WHERE o_id='$order_id'");
$ro = mysqli_fetch_array($ret1);
if(!$ro) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy đơn hàng"]);
    exit();
}
$ret2 = mysqli_query($db, "SELECT * FROM users WHERE u_id='".$ro['u_id']."'");
$user = mysqli_fetch_array($ret2);
if(!$user) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy khách hàng"]);
    exit();
}

// Format ngày đăng ký
$user_date = date('d/m/Y', strtotime($user['date']));

// Trả về dữ liệu JSON
$data = [
    "f_name" => $user['f_name'],
    "l_name" => $user['l_name'],
    "username" => $user['username'],
    "email" => $user['email'],
    "phone" => $user['phone'],
    "date" => $user_date,
    "status" => $user['status']
];

echo json_encode(["success" => true, "data" => $data]);
