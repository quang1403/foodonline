<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
} else {
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
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

        .stat-card {
            border-radius: 0.5rem;
            border: none;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .navbar {
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
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
                <a href="dashboard.php" class="sidebar-link active">
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
                        <h4 class="mb-0">Dashboard</h4>
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
                <!-- Stats Row 1 -->
                <div class="row g-4 mb-4">
                    <!-- Nhà hàng -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary bg-gradient h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-white text-opacity-75 mb-1">Nhà hàng</p>
                                        <h3 class="text-white mb-0">
                                            <?php 
                                            $sql="select * from restaurant";
                                            $result=mysqli_query($db,$sql); 
                                            echo mysqli_num_rows($result);
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="stat-icon bg-white bg-opacity-25 rounded-circle p-3">
                                        <i class="fas fa-store text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Món ăn -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-success bg-gradient h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-white text-opacity-75 mb-1">Món ăn</p>
                                        <h3 class="text-white mb-0">
                                            <?php 
                                            $sql="select * from dishes";
                                            $result=mysqli_query($db,$sql); 
                                            echo mysqli_num_rows($result);
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="stat-icon bg-white bg-opacity-25 rounded-circle p-3">
                                        <i class="fas fa-utensils text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Khách hàng -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-info bg-gradient h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-white text-opacity-75 mb-1">Khách hàng</p>
                                        <h3 class="text-white mb-0">
                                            <?php 
                                            $sql="select * from users";
                                            $result=mysqli_query($db,$sql); 
                                            echo mysqli_num_rows($result);
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="stat-icon bg-white bg-opacity-25 rounded-circle p-3">
                                        <i class="fas fa-users text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn hàng -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning bg-gradient h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-white text-opacity-75 mb-1">Đơn hàng</p>
                                        <h3 class="text-white mb-0">
                                            <?php 
                                            $sql="select * from users_orders";
                                            $result=mysqli_query($db,$sql); 
                                            echo mysqli_num_rows($result);
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="stat-icon bg-white bg-opacity-25 rounded-circle p-3">
                                        <i class="fas fa-shopping-cart text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Row 2 -->
                <div class="row g-4 mb-4">
                    <!-- Phân loại -->
                    <div class="col-md-4">
                        <div class="card stat-card bg-purple bg-gradient h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-white text-opacity-75 mb-1">Phân loại nhà hàng</p>
                                        <h3 class="text-white mb-0">
                                            <?php 
                                            $sql="select * from res_category";
                                            $result=mysqli_query($db,$sql); 
                                            echo mysqli_num_rows($result);
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="stat-icon bg-white bg-opacity-25 rounded-circle p-3">
                                        <i class="fas fa-th-large text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn treo -->
                    <div class="col-md-4">
                        <div class="card stat-card bg-orange bg-gradient h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-white text-opacity-75 mb-1">Đơn treo</p>
                                        <h3 class="text-white mb-0">
                                            <?php 
                                            $sql="select * from users_orders WHERE status = 'in process'";
                                            $result=mysqli_query($db,$sql); 
                                            echo mysqli_num_rows($result);
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="stat-icon bg-white bg-opacity-25 rounded-circle p-3">
                                        <i class="fas fa-spinner text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doanh thu -->
                    <div class="col-md-6">
                        <div class="card stat-card h-100" style="background: #2eaa7c;">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-white">Doanh thu đã thu</span>
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fas fa-money-bill-wave text-white"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-white mt-3 mb-2">
                                        <?php 
                                        $total_earnings = mysqli_query($db, 'SELECT COALESCE(SUM(price * quantity), 0) AS total FROM users_orders WHERE status = "closed"');
                                        $total_row = mysqli_fetch_assoc($total_earnings);
                                        echo number_format($total_row['total'], 0, ',', '.') . ' VNĐ';
                                        ?>
                                    </h2>
                                    <a href="restaurant_earnings.php" class="text-white text-decoration-none">
                                        Chi tiết <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card stat-card h-100" style="background: #ffc107;">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-white">Doanh thu chờ thu</span>
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-white mt-3 mb-2">
                                        <?php 
                                        $pending_earnings = mysqli_query($db, 'SELECT COALESCE(SUM(price * quantity), 0) AS pending FROM users_orders WHERE status = "in process"');
                                        $pending_row = mysqli_fetch_assoc($pending_earnings);
                                        echo number_format($pending_row['pending'], 0, ',', '.') . ' VNĐ';
                                        ?>
                                    </h2>
                                    <span class="text-white">
                                        Từ <?php echo mysqli_num_rows(mysqli_query($db, "SELECT * FROM users_orders WHERE status = 'in process'")); ?> đơn hàng
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Row 2 -->
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Thống kê doanh thu</h5>
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Đơn hàng gần đây</h5>
                                <div class="list-group list-group-flush">
                                    <?php
                                    $sql = "SELECT * FROM users_orders ORDER BY date DESC LIMIT 5";
                                    $result = mysqli_query($db, $sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <span>Đơn #'.$row['o_id'].'</span>
                                                <span class="badge bg-'.($row['status']=='closed'?'success':'warning').'">
                                                    '.$row['status'].'</span>
                                            </div>
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart configuration
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                datasets: [{
                    label: 'Doanh thu',
                    data: [12, 19, 3, 5, 2, 3, 7],
                    borderColor: '#4e73df',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php } ?>