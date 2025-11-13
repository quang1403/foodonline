<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đơn hàng của tôi</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chatbox CSS -->
    <link rel="stylesheet" href="food-chatbox/assets/css/chatbox.css">
   <style>
        /* Add these styles to your existing CSS */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1 0 auto;
        }

        footer {
            margin-top: auto;
        }

        /* Your existing styles */
        :root {
            --primary-color: #fd4d40;
            --secondary-color: #ff9b44;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            height: 40px;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: white;
            transform: translateX(-50%);
        }

        .nav-link.active::after {
            width: 80%;
        }

        .order-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            padding: 0.7rem 1.5rem;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }

        .order-summary-card {
            border-left: 4px solid var(--primary-color);
        }

        .order-items-collapse {
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .item-detail {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }

        .item-detail:last-child {
            border-bottom: none;
        }

        .total-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
 <!-- Hidden field for user ID -->
    <?php if(!empty($_SESSION["user_id"])) { ?>
        <input type="hidden" id="chat-user-id" value="<?php echo $_SESSION['user_id']; ?>">
    <?php } ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Logo" height="40">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php">
                            <i class="fas fa-utensils me-1"></i>Nhà hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                            <?php
                            if(!empty($_SESSION["cart_item"])) {
                                $cart_count = count(array_keys($_SESSION["cart_item"]));
                            ?>
                                <span class="badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="your_orders.php">
                            <i class="fas fa-list me-1"></i>Đơn hàng
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo $_SESSION["username"]; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="your_orders.php">
                                    <i class="fas fa-list me-2"></i>Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="userprofile.php">
                                    <i class="fas fa-user-circle me-2"></i>Thông tin cá nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="manage_addresses.php">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-5">
            <h2 class="mb-4">Lịch sử đơn hàng</h2>
            
            <?php 
            $query_res = mysqli_query($db,"SELECT * FROM users_orders WHERE u_id='".$_SESSION['user_id']."' ORDER BY date DESC");
            
            if(!mysqli_num_rows($query_res) > 0) {
                echo '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Không có đơn hàng nào.</div>';
            } else {
                // Group orders by order_code
                $orders_grouped = array();
                while($row = mysqli_fetch_array($query_res)) {
                    $order_code = $row['order_code'];
                    if(empty($order_code)) {
                        // Nếu đơn hàng cũ không có mã, tạo key theo thời gian
                        $order_code = 'legacy_' . date('Y-m-d H:i', strtotime($row['date']));
                    }
                    
                    if(!isset($orders_grouped[$order_code])) {
                        $orders_grouped[$order_code] = array();
                    }
                    $orders_grouped[$order_code][] = $row;
                }
                
                // Group by date
                $orders_by_date = array();
                foreach($orders_grouped as $order_code => $orders) {
                    $order_date = date('Y-m-d', strtotime($orders[0]['date']));
                    if(!isset($orders_by_date[$order_date])) {
                        $orders_by_date[$order_date] = array();
                    }
                    $orders_by_date[$order_date][$order_code] = $orders;
                }
                
                // Display orders grouped by date
                foreach($orders_by_date as $date => $order_groups) {
                    $date_display = date('d/m/Y', strtotime($date));
                    $day_of_week = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                    $day_name = $day_of_week[date('w', strtotime($date))];
                    
                    // Calculate total for this date
                    $date_total = 0;
                    foreach($order_groups as $orders) {
                        foreach($orders as $order) {
                            $date_total += $order['price'] * $order['quantity'];
                        }
                    }
            ?>
            
            <div class="card order-card mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-day me-2 text-primary"></i>
                            <?php echo $day_name . ', ' . $date_display; ?>
                        </h5>
                        <span class="badge bg-primary">
                            <?php echo count($order_groups); ?> đơn hàng - Tổng: <?php echo number_format($date_total, 0, ',', '.'); ?> VNĐ
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php 
                    $order_index = 0;
                    foreach($order_groups as $order_code => $orders) { 
                        $order_index++;
                        $order_time = date('H:i', strtotime($orders[0]['date']));
                        $item_count = count($orders);
                        $order_total = 0;
                        $first_status = $orders[0]['status'];
                        $is_legacy = strpos($order_code, 'legacy_') === 0;
                        
                        foreach($orders as $order) {
                            $order_total += $order['price'] * $order['quantity'];
                        }
                        
                        $collapse_id = 'order-' . md5($order_code);
                    ?>
                    
                    <div class="card order-summary-card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">
                                            <?php if(!$is_legacy) { ?>
                                                <strong><?php echo $order_code; ?></strong>
                                            <?php } else { ?>
                                                Đơn #<?php echo $order_index; ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div>
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        <strong><?php echo $order_time; ?></strong>
                                    </div>
                                    <div class="mt-2">
                                        <i class="fas fa-shopping-bag text-muted me-2"></i>
                                        <span class="text-muted"><?php echo $item_count; ?> món</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php 
                                    $status = $first_status;
                                    switch($status) {
                                        case "NULL":
                                        case "":
                                            echo '<span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>Chờ xác nhận</span>';
                                            break;
                                        case "preparing":
                                            echo '<span class="badge bg-info"><i class="fas fa-hourglass-half me-1"></i>Đang chuẩn bị</span>';
                                            break;
                                        case "prepared":
                                            echo '<span class="badge bg-primary"><i class="fas fa-check me-1"></i>Đã chuẩn bị</span>';
                                            break;
                                        case "in process":
                                            echo '<span class="badge bg-warning"><i class="fas fa-motorcycle me-1"></i>Đang giao</span>';
                                            break;
                                        case "closed":
                                            echo '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Đã giao</span>';
                                            break;
                                        case "rejected":
                                            echo '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Đã hủy</span>';
                                            break;
                                    }
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-primary fw-bold">
                                        <?php echo number_format($order_total, 0, ',', '.'); ?> VNĐ
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <?php if(!$is_legacy) { ?>
                                    <a href="order_detail.php?code=<?php echo $order_code; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Xem chi tiết
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php } ?>
                </div>
            </div>
            
            <?php 
                }
            } 
            ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include "include/footer.php" ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Chatbox JS -->
    <script src="food-chatbox/assets/js/chatbox.js"></script>
</body>
</html>