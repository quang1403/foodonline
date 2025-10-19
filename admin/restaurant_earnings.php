<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thu nhập theo nhà hàng</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
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
        
        .sidebar-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }

        .navbar {
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        }

        .card {
            border: none;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            border-radius: 0.5rem;
        }

        .card-header {
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 1rem;
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        .table {
            margin-bottom: 0;
        }

        .btn-info {
            background: var(--primary-color);
            border: none;
            color: white;
        }

        .btn-info:hover {
            background: #224abe;
            color: white;
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
                        <h4 class="mb-0">Thu nhập theo nhà hàng</h4>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="images/bookingSystem/logot.jpg" alt="Profile" class="rounded-circle" width="32">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="container-fluid p-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thống kê thu nhập</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="earningsTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên nhà hàng</th>
                                        <th>Số lượng đơn hàng</th>
                                        <th>Tổng thu nhập</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Truy vấn dữ liệu thu nhập theo từng nhà hàng
                                    $sql = "SELECT r.rs_id, r.title, 
                                        COUNT(DISTINCT o.o_id) AS total_orders,
                                        COALESCE(SUM(o.price * o.quantity), 0) AS total_earnings
                                    FROM restaurant r
                                    LEFT JOIN users_orders o ON r.rs_id = o.rs_id AND o.status = 'closed'
                                    GROUP BY r.rs_id
                                    ORDER BY total_earnings DESC";
                                    
                                    $query = mysqli_query($db, $sql);
                                    $stt = 1;
                                    
                                    if(mysqli_num_rows($query) > 0) {
                                        while($result = mysqli_fetch_array($query)) {
                                            // Kiểm tra nếu không có thu nhập, hiển thị 0
                                            $total_orders = $result['total_orders'] ? $result['total_orders'] : 0;
                                            $total_earnings = $result['total_earnings'] ? $result['total_earnings'] : 0;
                                            
                                            echo '<tr>
                                                    <td>'.$stt.'</td>
                                                    <td>'.$result['title'].'</td>
                                                    <td>'.$total_orders.'</td>
                                                    <td>'.number_format($total_earnings, 0, ',', '.').' VNĐ</td>
                                                    <td>
                                                        <a href="restaurant_orders.php?rs_id='.$result['rs_id'].'" class="btn btn-info btn-flat btn-addon btn-sm m-b-10 m-l-5"><i class="fa fa-search"></i> Chi tiết</a>
                                                    </td>
                                                  </tr>';
                                            $stt++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="5">Không có dữ liệu</td></tr>';
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
    
    <script>
        $(document).ready(function() {
            $('#earningsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
                }
            });
        });
    </script>
</body>
</html>