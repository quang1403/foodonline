<?php
include("../connection/connect.php");
session_start();

header('Content-Type: application/json');

if(empty($_SESSION["adm_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $rs_id = intval($_POST['rs_id']);

    // Check if username exists
    $check = mysqli_query($db, "SELECT id FROM restaurant_admin WHERE username = '$username'");
    if(mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username đã tồn tại']);
        exit();
    }

    $sql = "INSERT INTO restaurant_admin (username, password, name, email, phone, rs_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $username, $password, $name, $email, $phone, $rs_id);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Thêm admin thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . mysqli_error($db)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>