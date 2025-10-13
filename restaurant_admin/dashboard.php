<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["res_admin_id"])) {
    header('location:login.php');
    exit();
}

$res_id = $_SESSION["res_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhà hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            text-decoration: none;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            color: #fff;
            background: rgba(255,255,255,.1);
        }

        .card {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
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
                <img src="../admin/images/logo.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
            </div>
            <div class="nav flex-column">
                <a href="dashboard.php" class="sidebar-link active">
                    <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                </a>
                <a href="menu.php" class="sidebar-link">
                    <i class="fas fa-utensils me-2"></i> Menu
                </a>
                <a href="orders.php" class="sidebar-link">
                    <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <h4 class="mb-0">Tổng quan</h4>
                    <div class="dropdown">
                        <a class="btn btn-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="../admin/images/bookingSystem/logot.jpg" alt="Profile" class="rounded-circle" width="32">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-danger" href="logout.php">
    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="container-fluid p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase mb-1">Tổng số món</h6>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM dishes WHERE rs_id = ?";
                                        $stmt = mysqli_prepare($db, $sql);
                                        mysqli_stmt_bind_param($stmt, "i", $res_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        $row = mysqli_fetch_assoc($result);
                                        ?>
                                        <h3 class="mb-0"><?php echo $row['total']; ?></h3>
                                    </div>
                                    <div class="fs-1 opacity-75">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase mb-1">Đơn hàng mới</h6>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM users_orders WHERE rs_id = ? AND status = ''";
                                        $stmt = mysqli_prepare($db, $sql);
                                        mysqli_stmt_bind_param($stmt, "i", $res_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        $row = mysqli_fetch_assoc($result);
                                        ?>
                                        <h3 class="mb-0"><?php echo $row['total']; ?></h3>
                                    </div>
                                    <div class="fs-1 opacity-75">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase mb-1">Doanh thu</h6>
                                        <?php
                                        $sql = "SELECT SUM(price * quantity) as total FROM users_orders WHERE rs_id = ? AND status = 'closed'";
                                        $stmt = mysqli_prepare($db, $sql);
                                        mysqli_stmt_bind_param($stmt, "i", $res_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        $row = mysqli_fetch_assoc($result);
                                        ?>
                                        <h3 class="mb-0"><?php echo number_format($row['total'] ?? 0, 0, ',', '.'); ?> VNĐ</h3>
                                    </div>
                                    <div class="fs-1 opacity-75">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Statistics -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Món ăn bán chạy</h5>
                                    <div class="mt-2">
                                        <button class="btn btn-success btn-sm me-2" id="exportExcel">
                                            <i class="fas fa-file-excel me-1"></i> Xuất Excel
                                        </button>
                                        <button class="btn btn-info btn-sm" id="printTable">
                                            <i class="fas fa-print me-1"></i> In
                                        </button>
                                    </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="banchayTable">
                                        <thead>
                                            <tr>
                                                <th>Món ăn</th>
                                                <th>Số lượng đã bán</th>
                                                <th>Doanh thu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT 
                                                    title,
                                                    SUM(quantity) as total_quantity,
                                                    SUM(price * quantity) as total_revenue
                                                FROM users_orders 
                                                WHERE rs_id = ? AND status = 'closed'
                                                GROUP BY title
                                                ORDER BY total_quantity DESC
                                                LIMIT 5";
                                            $stmt = mysqli_prepare($db, $sql);
                                            mysqli_stmt_bind_param($stmt, "i", $res_id);
                                            mysqli_stmt_execute($stmt);
                                            $result = mysqli_stmt_get_result($stmt);
                                            
                                            while($row = mysqli_fetch_assoc($result)) {
                                                echo '<tr>
                                                    <td>'.$row['title'].'</td>
                                                    <td>'.$row['total_quantity'].'</td>
                                                    <td>'.number_format($row['total_revenue'], 0, ',', '.').' VNĐ</td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Thống kê đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                // Đơn hàng đã giao
                                $sql = "SELECT 
                                        COUNT(*) as total_orders,
                                        SUM(price * quantity) as total_revenue
                                        FROM users_orders 
                                        WHERE rs_id = ? AND status = 'closed'";
                                $stmt = mysqli_prepare($db, $sql);
                                mysqli_stmt_bind_param($stmt, "i", $res_id);
                                mysqli_stmt_execute($stmt);
                                $completed = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

                                // Đơn hàng bị hủy
                                $sql = "SELECT 
                                        COUNT(*) as total_orders,
                                        SUM(price * quantity) as total_revenue
                                        FROM users_orders 
                                        WHERE rs_id = ? AND status = 'rejected'";
                                $stmt = mysqli_prepare($db, $sql);
                                mysqli_stmt_bind_param($stmt, "i", $res_id);
                                mysqli_stmt_execute($stmt);
                                $canceled = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                                ?>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="border rounded p-3 text-center bg-success bg-opacity-10">
                                            <h6 class="text-success">Đơn đã giao</h6>
                                            <p class="mb-1">Số lượng: <?php echo $completed['total_orders']; ?></p>
                                            <p class="mb-0">Doanh thu: <?php echo number_format($completed['total_revenue'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3 text-center bg-danger bg-opacity-10">
                                            <h6 class="text-danger">Đơn bị hủy</h6>
                                            <p class="mb-1">Số lượng: <?php echo $canceled['total_orders']; ?></p>
                                            <p class="mb-0">Giá trị: <?php echo number_format($canceled['total_revenue'] ?? 0, 0, ',', '.'); ?> VNĐ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script>
        document.getElementById('exportExcel').onclick = function() {
            var wb = XLSX.utils.table_to_book(document.getElementById('banchayTable'), {sheet: "BanChay"});
            XLSX.writeFile(wb, 'monan_banchay.xlsx');
        };
        document.getElementById('printTable').onclick = function() {
            var printContents = document.getElementById('banchayTable').outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        };
        </script>
</body>
</html>