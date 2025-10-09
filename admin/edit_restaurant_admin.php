<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$id = intval($_POST['id']);
$rs_id = intval($_POST['rs_id']);
$name = mysqli_real_escape_string($db, $_POST['name']);
$username = mysqli_real_escape_string($db, $_POST['username']);
$email = mysqli_real_escape_string($db, $_POST['email']);
$phone = mysqli_real_escape_string($db, $_POST['phone']);
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if($password != '') {
    $password_hash = md5($password);
    $sql = "UPDATE restaurant_admin SET rs_id='$rs_id', name='$name', username='$username', password='$password_hash', email='$email', phone='$phone' WHERE id='$id'";
} else {
    $sql = "UPDATE restaurant_admin SET rs_id='$rs_id', name='$name', username='$username', email='$email', phone='$phone' WHERE id='$id'";
}

if(mysqli_query($db, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật']);
}
?>