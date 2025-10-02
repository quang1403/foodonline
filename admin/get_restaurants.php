<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

// Nếu có id thì lấy chi tiết nhà hàng đó
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT r.*, c.c_name FROM restaurant r 
            LEFT JOIN res_category c ON r.c_id = c.c_id 
            WHERE r.rs_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $restaurant = mysqli_fetch_assoc($result);
    
    header('Content-Type: application/json');
    echo json_encode($restaurant);
}
// Ngược lại lấy danh sách tất cả nhà hàng cho DataTable
else {
    $sql = "SELECT r.*, c.c_name as category_name 
            FROM restaurant r 
            LEFT JOIN res_category c ON r.c_id = c.c_id 
            ORDER BY r.rs_id DESC";
    $result = mysqli_query($db, $sql);
    $restaurants = array();
    
    while($row = mysqli_fetch_assoc($result)) {
        $restaurants[] = array(
            'rs_id' => $row['rs_id'],
            'category_name' => $row['category_name'],
            'title' => $row['title'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'url' => $row['url'],
            'o_hr' => $row['o_hr'],
            'c_hr' => $row['c_hr'],
            'o_days' => $row['o_days'],
            'address' => $row['address'],
            'image' => $row['image']
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($restaurants);
}
?>