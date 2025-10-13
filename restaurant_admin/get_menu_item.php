<?php
include("../connection/connect.php");
$id = $_POST['id'];
$sql = "SELECT * FROM dishes WHERE d_id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
echo json_encode($row);
?>