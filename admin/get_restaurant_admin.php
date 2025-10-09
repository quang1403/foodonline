<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM restaurant_admin WHERE id = '$id' LIMIT 1";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'id' => $row['id'],
        'rs_id' => $row['rs_id'],
        'name' => $row['name'],
        'username' => $row['username'],
        'email' => $row['email'],
        'phone' => $row['phone']
    ]);
} else {
    echo json_encode(['error' => 'Not found']);
}
?>