<?php
include("../connection/connect.php");
session_start();

header('Content-Type: application/json');

if(empty($_SESSION["res_admin_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $res_id = $_SESSION["res_id"];

    // Verify order belongs to restaurant
    $check = mysqli_query($db, "SELECT rs_id FROM users_orders WHERE o_id = $order_id");
    $order = mysqli_fetch_assoc($check);
    
    if($order['rs_id'] != $res_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized order']);
        exit();
    }

    $sql = "UPDATE users_orders SET status = ? WHERE o_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($db)]);
    }
}
?>