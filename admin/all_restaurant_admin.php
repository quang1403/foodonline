<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Admin nhà hàng</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
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
        
        .sidebar-link:hover {
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
                <a href="all_restaurant_admin.php" class="sidebar-link active">
                    <i class="fas fa-user-cog me-2"></i> Admin nhà hàng
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <h4 class="mb-0">Quản lý Admin nhà hàng</h4>
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
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách Admin nhà hàng</h5>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="fas fa-plus me-2"></i>Thêm mới
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="adminTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên nhà hàng</th>
                                        <th>Tên Admin</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT ra.*, r.title as restaurant_name 
                                           FROM restaurant_admin ra
                                           LEFT JOIN restaurant r ON ra.rs_id = r.rs_id
                                           ORDER BY ra.id DESC";
                                    $query = mysqli_query($db, $sql);
                                    
                                    while($row = mysqli_fetch_array($query)) {
                                        echo '<tr>
                                            <td>'.$row['restaurant_name'].'</td>
                                            <td>'.$row['name'].'</td>
                                            <td>'.$row['username'].'</td>
                                            <td>'.$row['email'].'</td>
                                            <td>'.$row['phone'].'</td>
                                            <td>'.$row['created_at'].'</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm edit-admin" data-id="'.$row['id'].'">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm delete-admin" data-id="'.$row['id'].'">
                                                    <i class="fas fa-trash"></i>
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

    <!-- Add Admin Modal -->
    <div class="modal fade" id="addAdminModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Admin nhà hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm">
                        <div class="mb-3">
                            <label>Nhà hàng</label>
                            <select name="rs_id" class="form-select" required>
                                <option value="">Chọn nhà hàng</option>
                                <?php
                                $sql = "SELECT rs_id, title FROM restaurant ORDER BY title";
                                $result = mysqli_query($db, $sql);
                                while($row = mysqli_fetch_array($result)) {
                                    echo '<option value="'.$row['rs_id'].'">'.$row['title'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Tên Admin</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveAdmin">Lưu</button>
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
            // Initialize DataTable
            $('#adminTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                }
            });

            // Save new admin
            $('#saveAdmin').click(function() {
                $.ajax({
                    url: 'save_restaurant_admin.php',
                    type: 'POST',
                    data: $('#addAdminForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            $('#addAdminModal').modal('hide');
                            alert('Thêm admin thành công!');
                            location.reload();
                        } else {
                            alert(response.message || 'Có lỗi xảy ra');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Lỗi: ' + error);
                        console.log(xhr.responseText);
                    }
                });
            });

            // Handle form submission
            $('#addAdminForm').on('submit', function(e) {
                e.preventDefault();
                $('#saveAdmin').click();
            });
        });
    </script>
</body>
</html>