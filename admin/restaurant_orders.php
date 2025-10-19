<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if(empty($_SESSION["adm_id"]))
{
    header('location:index.php');
}

// Kiểm tra tham số rs_id
if(empty($_GET['rs_id'])) {
    header('location:restaurant_earnings.php');
    exit();
}

$rs_id = $_GET['rs_id'];

// Lấy thông tin nhà hàng
$sql_restaurant = "SELECT * FROM restaurant WHERE rs_id = $rs_id";
$query_restaurant = mysqli_query($db, $sql_restaurant);
$restaurant = mysqli_fetch_assoc($query_restaurant);

if(!$restaurant) {
    header('location:restaurant_earnings.php');
    exit();
}

// Tính toán thống kê doanh thu cho nhà hàng cụ thể
$sql_summary = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(price * quantity) as total_revenue,
    SUM(CASE WHEN status = 'closed' THEN price * quantity ELSE 0 END) as completed_revenue
FROM users_orders 
WHERE rs_id = $rs_id 
GROUP BY rs_id";

$query_summary = mysqli_query($db, $sql_summary);
$summary = mysqli_fetch_assoc($query_summary);

// Xử lý khi không có dữ liệu
if (!$summary) {
    $summary = array(
        'total_orders' => 0,
        'completed_orders' => 0,
        'total_revenue' => 0,
        'completed_revenue' => 0
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết thu nhập - <?php echo $restaurant['title']; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
        }
        
        .sidebar-link {
            color: rgba(255,255,255,.8);
            padding: 1rem;
            display: block;
            transition: all 0.3s;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            color: #fff;
            background: rgba(255,255,255,.1);
            text-decoration: none;
        }

        .card {
            border: none;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            border-radius: 0.5rem;
        }

        .card-header {
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        .status-badge {
            padding: 0.5em 1em;
            border-radius: 30px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .status-pending { background: #ffeeba; color: #856404; }
        .status-preparing { background: #cce5ff; color: #004085; }
        .status-prepared { background: #d4edda; color: #155724; }
        .status-delivering { background: #fff3cd; color: #856404; }
        .status-completed { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #842029; }

        .dt-buttons .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            margin-right: 0.5rem;
            padding: 0.5rem 1rem;
        }

        .dt-buttons .btn:hover {
            background: #224abe;
        }

        /* Thay thế phần CSS liên quan đến card và row trong thẻ style */
        .row.g-4 {
            display: flex;
            margin: 0 !important;
            gap: 0 !important;
        }

        .row.g-4 > div {
            padding: 8px !important;
            flex: 1;
        }

        .row.g-4 .card {
            height: 100%;
            margin: 0 !important;
            border-radius: 15px;
            overflow: hidden;
        }

        .row.g-4 .card-body {
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .stat-icon {
            background: rgba(255,255,255,0.2);
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 1rem !important;
        }

        .row.g-4 .card h6 {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem !important;
            text-align: center;
        }

        .row.g-4 .card h3 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 !important;
            text-align: center;
        }

        /* Gradient backgrounds */
        .bg-gradient {
            background: linear-gradient(45deg, #4e73df, #224abe) !important;
        }

        .bg-success-gradient {
            background: linear-gradient(45deg, #1cc88a, #13855c) !important;
        }

        .bg-warning-gradient {
            background: linear-gradient(45deg, #f6c23e, #dda20a) !important;
        }

        .bg-info-gradient {
            background: linear-gradient(45deg, #36b9cc, #258391) !important;
        }

        /* Card hover effect */
        .row.g-4 .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar text-white" style="width: 250px;">
            <div class="p-3">
                <img src="images/logo.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
            </div>
            
            <div class="nav flex-column">
                <a href="dashboard.php" class="sidebar-link">
                    <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                </a>
                <a href="all_users.php" class="sidebar-link">
                    <i class="fas fa-users me-2"></i> Người dùng
                </a>
                <a href="all_restaurant.php" class="sidebar-link">
                    <i class="fas fa-store me-2"></i> Nhà hàng
                </a>
                <a href="all_menu.php" class="sidebar-link">
                    <i class="fas fa-utensils me-2"></i> Menu
                </a>
                <a href="all_orders.php" class="sidebar-link">
                    <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                </a>
               <a href="all_restaurant_admin.php" class="sidebar-link">
                    <i class="fas fa-user-cog me-2"></i> Quản lý Admin nhà hàng
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <div>
                        <h4 class="mb-0">Chi tiết thu nhập - <?php echo $restaurant['title']; ?></h4>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="images/admin.jpg" alt="Profile" class="rounded-circle" width="32">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="container-fluid p-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin nhà hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-store me-2"></i>Tên:</strong> <?php echo $restaurant['title']; ?></p>
                                <p><strong><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ:</strong> <?php echo $restaurant['address']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> <?php echo $restaurant['email']; ?></p>
                                <p><strong><i class="fas fa-phone me-2"></i>Điện thoại:</strong> <?php echo $restaurant['phone']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Tổng kết doanh thu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6>Tổng đơn hàng</h6>
                                    <h3><?php echo $summary['total_orders']; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6>Đơn đã hoàn thành</h6>
                                    <h3><?php echo $summary['completed_orders']; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6>Tổng doanh thu</h6>
                                    <h3><?php echo number_format($summary['total_revenue'], 0, ',', '.'); ?>đ</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6>Doanh thu thực</h6>
                                    <h3><?php echo number_format($summary['completed_revenue'], 0, ',', '.'); ?>đ</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách đơn hàng</h5>
                        <a href="restaurant_earnings.php" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Món đặt</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đặt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_orders = "SELECT o.*, u.f_name, u.l_name 
                                                       FROM users_orders o
                                                       LEFT JOIN users u ON o.u_id = u.u_id
                                                       WHERE o.rs_id = $rs_id
                                                       ORDER BY o.date DESC";
                                                
                                    $query_orders = mysqli_query($db, $sql_orders);
                                    $stt = 1;
                                                
                                    if(mysqli_num_rows($query_orders) > 0) {
                                        while($order = mysqli_fetch_array($query_orders)) {
                                            $status_color = '';
                                            $status_text = 'Đang xử lý';
                                                        
                                            if ($order['status'] == 'closed') {
                                                $status_color = 'label-success';
                                                $status_text = 'Hoàn thành';
                                            } else if ($order['status'] == 'rejected') {
                                                $status_color = 'label-danger';
                                                $status_text = 'Đã hủy';
                                            } else if ($order['status'] == 'in process') {
                                                $status_color = 'label-warning';
                                                $status_text = 'Đang giao';
                                            } else if ($order['status'] == 'preparing') {
                                                $status_color = 'label-primary';
                                                $status_text = 'Đang chuẩn bị';
                                            } else if ($order['status'] == 'prepared') {
                                                $status_color = 'label-purple';
                                                $status_text = 'Đã chuẩn bị';
                                            } else {
                                                $status_color = 'label-info';
                                                $status_text = 'Chờ xác nhận';
                                            }
                                                        
                                                        
                                            echo '<tr>
                                                    <td>'.$stt.'</td>
                                                    <td>'.$order['o_id'].'</td>
                                                    <td>'.$order['f_name'].' '.$order['l_name'].'</td>
                                                    <td>'.$order['title'].'</td>
                                                    <td>'.$order['quantity'].'</td>
                                                    <td>'.number_format($order['price'], 0, ',', '.').' VNĐ</td>
                                                    <td><span class="label '.$status_color.'">'.$status_text.'</span></td>
                                                    <td>'.date('d/m/Y H:i', strtotime($order['date'])).'</td>
                                                  </tr>';
                                            $stt++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="8">Không có đơn hàng nào</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel me-2"></i>Xuất Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-2"></i>In danh sách',
                        className: 'btn btn-info'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json'
                }
            });
        });
    </script>
</body>
</html>