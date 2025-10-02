<?php
include("../connection/connect.php");
session_start();

header('Content-Type: application/json');

if(empty($_SESSION["adm_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $f_name = mysqli_real_escape_string($db, $_POST['f_name']);
    $l_name = mysqli_real_escape_string($db, $_POST['l_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $address = mysqli_real_escape_string($db, $_POST['address']);

    // Build update query
    $sql = "UPDATE users SET 
            username = ?, 
            f_name = ?, 
            l_name = ?, 
            email = ?, 
            phone = ?, 
            address = ?";
    
    $params = [$username, $f_name, $l_name, $email, $phone, $address];
    $types = "ssssss";

    // Add password if provided
    if(!empty($_POST['password'])) {
        $sql .= ", password = ?";
        $params[] = md5($_POST['password']);
        $types .= "s";
    }

    $sql .= " WHERE u_id = ?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if(mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($db)]);
    }
}
?>