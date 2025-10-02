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
    <title>Quản lý đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
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
                <a href="dashboard.php" class="sidebar-link">
                    <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                </a>
                <a href="menu.php" class="sidebar-link">
                    <i class="fas fa-utensils me-2"></i> Menu
                </a>
                <a href="orders.php" class="sidebar-link active">
                    <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <h4 class="mb-0">Quản lý đơn hàng</h4>
                    <div class="dropdown">
                        <a class="btn btn-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="../admin/images/bookingSystem/logot.jpg" alt="Profile" class="rounded-circle" width="32">
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
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Danh sách đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orderTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Món ăn</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                        <th>Địa chỉ</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT users.*, users_orders.* 
                                           FROM users 
                                           INNER JOIN users_orders ON users.u_id = users_orders.u_id 
                                           WHERE users_orders.rs_id = ? 
                                           ORDER BY users_orders.date DESC";
                                    
                                    $stmt = mysqli_prepare($db, $sql);
                                    mysqli_stmt_bind_param($stmt, "i", $res_id);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    
                                    while($rows = mysqli_fetch_array($result)) {
                                        echo '<tr>
                                            <td>'.$rows['username'].'</td>
                                            <td>'.$rows['title'].'</td>
                                            <td>'.$rows['quantity'].'</td>
                                            <td>'.number_format($rows['price'], 0, ',', '.').' VNĐ</td>
                                            <td>'.$rows['address'].'</td>';
                                        
                                        // Status badge
                                        $status = $rows['status'];
                                        $badge_class = '';
                                        $status_text = '';
                                        $icon = '';
                                        
                                        switch($status) {
                                            case '':
                                                $badge_class = 'bg-info';
                                                $status_text = 'Chờ xác nhận';
                                                $icon = 'clock';
                                                break;
                                            case 'in process':
                                                $badge_class = 'bg-warning';
                                                $status_text = 'Đang giao';
                                                $icon = 'motorcycle';
                                                break;
                                            case 'closed':
                                                $badge_class = 'bg-success';
                                                $status_text = 'Đã giao';
                                                $icon = 'check-circle';
                                                break;
                                            case 'rejected':
                                                $badge_class = 'bg-danger';
                                                $status_text = 'Đã hủy';
                                                $icon = 'times-circle';
                                                break;
                                        }
                                        
                                        echo '<td><span class="badge '.$badge_class.' status-badge">
                                                <i class="fas fa-'.$icon.' me-1"></i>'.$status_text.'
                                              </span></td>';
                                        
                                        echo '<td>'.$rows['date'].'</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm update-status" data-id="'.$rows['o_id'].'">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>';
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Cập nhật trạng thái</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" name="order_id" id="order_id">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select" required>
                                <option value="">Chờ xác nhận</option>
                                <option value="in process">Đang giao</option>
                                <option value="closed">Đã giao</option>
                                <option value="rejected">Đã hủy</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveStatus">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#orderTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                },
                order: [[6, 'desc']]
            });

            // Handle update status button
            $('.update-status').click(function() {
                const orderId = $(this).data('id');
                $('#order_id').val(orderId);
                $('#updateStatusModal').modal('show');
            });

            // Handle save status
            $('#saveStatus').click(function() {
                $.ajax({
                    url: 'update_order_status.php',
                    type: 'POST',
                    data: $('#updateStatusForm').serialize(),
                    success: function(response) {
                        if(response.success) {
                            alert('Cập nhật trạng thái thành công!');
                            location.reload();
                        } else {
                            alert(response.message || 'Có lỗi xảy ra');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>