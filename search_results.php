<?php
include("connection/connect.php");
session_start();

if(isset($_GET['term'])) {
    $term = mysqli_real_escape_string($db, $_GET['term']);
    $type = $_GET['type'];
    $output = '';

    if($type === 'restaurant' || $type === 'all') {
        // Search restaurants - removed c_name from query
        $rest_query = "SELECT * FROM restaurant 
                      WHERE title LIKE '%$term%' 
                      OR address LIKE '%$term%'";
        
        $rest_result = mysqli_query($db, $rest_query);
        
        if(mysqli_num_rows($rest_result) > 0) {
            $output .= '<h6 class="mb-3">Nhà hàng</h6>';
            while($restaurant = mysqli_fetch_assoc($rest_result)) {
                $output .= '
                <div class="card mb-3">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <img src="admin/Res_img/'.$restaurant['image'].'" 
                                 class="img-fluid rounded-start" alt="'.$restaurant['title'].'"
                                 style="height: 120px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h5 class="card-title">'.$restaurant['title'].'</h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt me-2"></i>'.$restaurant['address'].'
                                </p>
                                <a href="dishes.php?res_id='.$restaurant['rs_id'].'" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-utensils me-2"></i>Xem menu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }
    }

    if($type === 'dish' || $type === 'all') {
        // Search dishes - only search in title and description
        $dish_query = "SELECT d.*, r.title as restaurant_name, r.rs_id 
                      FROM dishes d
                      JOIN restaurant r ON d.rs_id = r.rs_id
                      WHERE d.title LIKE '%$term%'"; // Only search by dish title
        
        $dish_result = mysqli_query($db, $dish_query);
        
        if(mysqli_num_rows($dish_result) > 0) {
            $output .= '<h6 class="mb-3 mt-4">Món ăn</h6>';
            while($dish = mysqli_fetch_assoc($dish_result)) {
                $output .= '
                <div class="card mb-3">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <img src="admin/Res_img/dishes/'.$dish['img'].'" 
                                 class="img-fluid rounded-start" alt="'.$dish['title'].'"
                                 style="height: 120px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h5 class="card-title">'.$dish['title'].'</h5>
                                <p class="card-text small">'.$dish['slogan'].'</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-primary fw-bold">
                                            '.number_format($dish['price'], 0, ',', '.').' VNĐ
                                        </span>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-store me-1"></i>'.$dish['restaurant_name'].'
                                        </small>
                                    </div>
                                    <a href="dishes.php?res_id='.$dish['rs_id'].'" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-shopping-cart me-2"></i>Đặt món
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }
    }

    if(empty($output)) {
        echo '<div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Không tìm thấy kết quả nào
              </div>';
    } else {
        echo $output;
    }
}
?>