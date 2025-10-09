<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$id = intval($_POST['id']);
$sql = "DELETE FROM restaurant_admin WHERE id='$id' LIMIT 1";
if(mysqli_query($db, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa']);
}
?>