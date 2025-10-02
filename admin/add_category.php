<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
} else {

// Thêm xử lý form submit
if(isset($_POST['submit'])) {
    if(empty($_POST['c_name'])) {
        $error = '<div class="alert alert-danger">Vui lòng nhập tên phân loại!</div>';
    } else {
        // Kiểm tra phân loại đã tồn tại chưa
        $check = mysqli_query($db, "SELECT c_name FROM res_category WHERE c_name = '".$_POST['c_name']."'");
        if(mysqli_num_rows($check) > 0) {
            $error = '<div class="alert alert-danger">Phân loại này đã tồn tại!</div>';
        } else {
            // Thêm phân loại mới
            $sql = "INSERT INTO res_category(c_name) VALUES('".$_POST['c_name']."')";
            $result = mysqli_query($db, $sql);
            if($result) {
                $success = '<div class="alert alert-success">Thêm phân loại thành công!</div>';
                // Reload trang sau 2 giây
                header("refresh:2;url=add_category.php");
            } else {
                $error = '<div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại!</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm phân loại nhà hàng</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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

        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
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
                <a href="all_restaurant.php" class="sidebar-link active">
                    <i class="fas fa-store me-2"></i> Nhà hàng
                </a>
                <a href="all_menu.php" class="sidebar-link">
                    <i class="fas fa-utensils me-2"></i> Menu
                </a>
                <a href="all_orders.php" class="sidebar-link">
                    <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center">
                            <a href="all_restaurant.php" class="btn btn-link text-decoration-none me-3">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h4 class="mb-0">Thêm phân loại nhà hàng</h4>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="container-fluid p-4">
                <div class="row">
                    <!-- Form thêm mới -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Thêm phân loại mới</h5>
                            </div>
                            <div class="card-body">
                                <?php echo $error; echo $success; ?>
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Tên phân loại</label>
                                        <input type="text" name="c_name" class="form-control" required>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Lưu
                                        </button>
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="fas fa-undo me-2"></i>Làm mới
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng danh sách -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Danh sách phân loại</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="categoryTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên phân loại</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM res_category ORDER BY c_id DESC";
                                            $query = mysqli_query($db, $sql);
                                            
                                            if(!mysqli_num_rows($query) > 0) {
                                                echo '<tr><td colspan="4" class="text-center">Không có dữ liệu</td></tr>';
                                            } else {
                                                while($row = mysqli_fetch_array($query)) {
                                                    echo '<tr>
                                                        <td>'.$row['c_id'].'</td>
                                                        <td>'.$row['c_name'].'</td>
                                                        <td>'.date('d/m/Y', strtotime($row['date'])).'</td>
                                                        <td>
                                                            <a href="update_category.php?cat_upd='.$row['c_id'].'" class="btn btn-primary btn-action" title="Sửa">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete_category.php?cat_del='.$row['c_id'].'" class="btn btn-danger btn-action" 
                                                               onclick="return confirm(\'Bạn có chắc muốn xóa phân loại này?\')" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>';
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
            $('#categoryTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                }
            });
        });
    </script>
</body>
</html>
<?php } ?>