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
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
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
            transition: all 0.3s;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        .nav-link:hover {
            transform: translateY(-2px);
        }

        .order-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }

        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
        }
    </style>
</head>

<body>
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
            <div class="card order-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Lịch sử đơn hàng</h4>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Lấy danh sách các order_code hoặc date của user (group by order_code nếu có, nếu không thì date)
                                $orders = [];
                                $q = mysqli_query($db, "SELECT COALESCE(order_code, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s')) as order_id, MIN(date) as min_date 
                                                        FROM users_orders 
                                                        WHERE u_id='".$_SESSION['user_id']."' 
                                                        GROUP BY order_id 
                                                        ORDER BY min_date DESC");
                                while($r = mysqli_fetch_assoc($q)) {
                                    $orders[] = $r['order_id'];
                                }
                                if(empty($orders)) {
                                    echo '<tr><td colspan="5" class="text-center">Không có đơn hàng nào.</td></tr>';
                                } else {
                                    foreach($orders as $order_id) {
                                        // Lấy tất cả món trong đơn này
                                        $items = [];
                                        $total = 0;
                                        $status = '';
                                        $date = '';
                                        $address = '';
                                        
                                        // Kiểm tra xem order_id là order_code hay timestamp
                                        $q2 = mysqli_query($db, "SELECT * FROM users_orders 
                                                                WHERE u_id='".$_SESSION['user_id']."' 
                                                                AND (order_code='".$order_id."' OR DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s')='".$order_id."')");
                                        while($item = mysqli_fetch_assoc($q2)) {
                                            $items[] = $item;
                                            $total += $item['price'] * $item['quantity'];
                                            $status = $item['status'];
                                            $date = $item['date'];
                                            $address = $item['address'];
                                        }
                                        
                                        // Tạo mã đơn hiển thị
                                        $display_code = !empty($items[0]['order_code']) ? $items[0]['order_code'] : 'DH'.date('YmdHis', strtotime($date));
                                        
                                        // Hiển thị dòng đơn hàng
                                        echo '<tr>';
                                        echo '<td>'.$display_code.'</td>';
                                        echo '<td>'.date('d/m/Y H:i', strtotime($date)).'</td>';
                                        echo '<td>';
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
                                        echo '</td>';
                                        echo '<td>'.number_format($total, 0, ',', '.').' VNĐ</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-info btn-sm text-white view-order-detail" data-order="'.htmlspecialchars($order_id).'"><i class="fas fa-eye me-1"></i>Chi tiết</button> ';
                                        if($status != "closed" && $status != "rejected") {
                                            echo '<a href="delete_orders.php?order_id='.urlencode($order_id).'" onclick="return confirm(\'Bạn có chắc muốn hủy đơn hàng này?\');" class="btn btn-danger btn-sm"><i class="fas fa-times me-1"></i>Hủy</a>';
                                        } else if($status == "rejected") {
                                            echo '<a href="delete_orders.php?order_id='.urlencode($order_id).'&delete=1" onclick="return confirm(\'Bạn có chắc muốn xóa đơn hàng này?\');" class="btn btn-secondary btn-sm"><i class="fas fa-trash me-1"></i>Xóa</a>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                        // Modal chi tiết đơn hàng (ẩn, sẽ show bằng JS)
                                        $modal_id = 'orderDetailModal-'.md5($order_id);
                                        echo '<div class="modal fade" id="'.$modal_id.'" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">';
                                        echo '<div class="modal-header"><h5 class="modal-title">Chi tiết đơn hàng '.$display_code.'</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>';
                                        echo '<div class="modal-body">';
                                        echo '<div class="mb-2"><b>Thời gian đặt:</b> '.date('d/m/Y H:i', strtotime($date)).'</div>';
                                        echo '<div class="mb-2"><b>Địa chỉ giao hàng:</b> '.htmlspecialchars($address).'</div>';
                                        echo '<div class="mb-2"><b>Trạng thái:</b> ';
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
                                        echo '</div>';
                                        echo '<div class="mb-2"><b>Danh sách món:</b></div>';
                                        echo '<ul class="list-group mb-3">';
                                        foreach($items as $it) {
                                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">'.htmlspecialchars($it['title']).' <span class="badge bg-primary">x'.$it['quantity'].'</span> <span>'.number_format($it['price'],0,',','.').' VNĐ</span></li>';
                                        }
                                        echo '</ul>';
                                        echo '<div class="mb-2"><b>Tổng tiền:</b> '.number_format($total,0,',','.').' VNĐ</div>';
                                        echo '</div>';
                                        echo '<div class="modal-footer">';
                                        if($status != "closed" && $status != "rejected") {
                                            echo '<a href="delete_orders.php?order_id='.urlencode($order_id).'" onclick="return confirm(\'Bạn có chắc muốn hủy đơn hàng này?\');" class="btn btn-danger"><i class="fas fa-times me-1"></i>Hủy đơn</a>';
                                        } else if($status == "rejected") {
                                            echo '<a href="delete_orders.php?order_id='.urlencode($order_id).'&delete=1" onclick="return confirm(\'Bạn có chắc muốn xóa đơn hàng này?\');" class="btn btn-secondary"><i class="fas fa-trash me-1"></i>Xóa đơn</a>';
                                        }
                                        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>';
                                        echo '</div>';
                                        echo '</div></div></div>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "include/footer.php" ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
    $(function() {
        $('.view-order-detail').click(function() {
            var orderId = $(this).data('order');
            var modalId = '#orderDetailModal-' + md5(orderId);
            $(modalId).modal('show');
        });
        
        function md5(str) {
            // Simple hash function for modal ID
            var hash = 0;
            if (str.length == 0) return hash;
            for (var i = 0; i < str.length; i++) {
                var char = str.charCodeAt(i);
                hash = ((hash<<5)-hash)+char;
                hash = hash & hash;
            }
            return Math.abs(hash).toString(16);
        }
    });
    </script>
</body>
</html>