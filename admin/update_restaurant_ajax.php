<?php
include("../connection/connect.php");
session_start();

$response = array('success' => false, 'message' => '');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate và xử lý dữ liệu
    $restaurant_id = $_POST['restaurant_id'];
    
    if($_FILES['file']['name'] != '') {
        // Xử lý upload file
        $fname = $_FILES['file']['name'];
        $temp = $_FILES['file']['tmp_name'];
        $fsize = $_FILES['file']['size'];
        $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $fnew = uniqid() . '.' . $extension;
        $store = "Res_img/" . $fnew;
        
        if($extension == 'jpg' || $extension == 'png' || $extension == 'gif') {
            move_uploaded_file($temp, $store);
            $sql = "UPDATE restaurant SET 
                    c_id=?, title=?, email=?, phone=?, url=?, 
                    o_hr=?, c_hr=?, o_days=?, address=?, image=? 
                    WHERE rs_id=?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssssssi", 
                $_POST['c_name'], $_POST['res_name'], $_POST['email'],
                $_POST['phone'], $_POST['url'], $_POST['o_hr'],
                $_POST['c_hr'], $_POST['o_days'], $_POST['address'],
                $fnew, $restaurant_id);
        }
    } else {
        $sql = "UPDATE restaurant SET 
                c_id=?, title=?, email=?, phone=?, url=?, 
                o_hr=?, c_hr=?, o_days=?, address=? 
                WHERE rs_id=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssi", 
            $_POST['c_name'], $_POST['res_name'], $_POST['email'],
            $_POST['phone'], $_POST['url'], $_POST['o_hr'],
            $_POST['c_hr'], $_POST['o_days'], $_POST['address'],
            $restaurant_id);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Cập nhật thành công';
    } else {
        $response['message'] = 'Lỗi: ' . mysqli_error($db);
    }
}

echo json_encode($response);