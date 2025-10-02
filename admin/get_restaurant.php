<?php
include("../connection/connect.php");
session_start();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM restaurant WHERE rs_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $restaurant = mysqli_fetch_assoc($result);
    
    echo json_encode($restaurant);
}