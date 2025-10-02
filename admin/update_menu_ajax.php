<?php
include("../connection/connect.php");
session_start();

$response = array('success' => false, 'message' => '');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = $_POST['menu_id'];
    
    if($_FILES['file']['name'] != '') {
        $fname = $_FILES['file']['name'];
        $temp = $_FILES['file']['tmp_name'];
        $fsize = $_FILES['file']['size'];
        $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $fnew = uniqid() . '.' . $extension;
        $store = "Res_img/dishes/" . $fnew;
        
        if($extension == 'jpg' || $extension == 'png' || $extension == 'gif') {
            if($fsize >= 1000000) {
                $response['message'] = 'Kích thước ảnh tối đa là 1MB!';
            } else {
                move_uploaded_file($temp, $store);
                $sql = "UPDATE dishes SET 
                        rs_id=?, title=?, slogan=?, price=?, img=? 
                        WHERE d_id=?";
                $stmt = mysqli_prepare($db, $sql);
                mysqli_stmt_bind_param($stmt, "issssi", 
                    $_POST['res_name'], 
                    $_POST['d_name'],
                    $_POST['about'],
                    $_POST['price'],
                    $fnew,
                    $menu_id
                );
            }
        } else {
            $response['message'] = 'Chỉ chấp nhận file jpg, png, gif!';
        }
    } else {
        $sql = "UPDATE dishes SET 
                rs_id=?, title=?, slogan=?, price=?
                WHERE d_id=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "isssi",
            $_POST['res_name'],
            $_POST['d_name'],
            $_POST['about'],
            $_POST['price'],
            $menu_id
        );
    }
    
    if(isset($stmt) && mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Cập nhật thành công';
    } else {
        $response['message'] = 'Lỗi: ' . mysqli_error($db);
    }
}

echo json_encode($response);