<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM dishes WHERE d_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $menu = mysqli_fetch_assoc($result);
    
    echo json_encode($menu);
}
?>