<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["res_admin_id"])) {
    header('location:login.php');
    exit();
}

$res_id = $_SESSION["res_id"];

// Thêm món mới
if(isset($_POST['submit'])) {
    if(empty($_POST['d_name']) || empty($_POST['about']) || $_POST['price']=='') {
        $error = '<div class="alert alert-danger">Vui lòng điền đầy đủ thông tin!</div>';
    } else {
        // Upload image and insert dish
        $target_dir = "../admin/Res_img/dishes/";
        $file = $_FILES["file"]["name"];
        $fnew = uniqid().'-'.basename($file);
        $target_file = $target_dir . $fnew;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Kiểm tra định dạng hình ảnh
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if($check === false) {
            $error = '<div class="alert alert-danger">Tập tin không phải là hình ảnh!</div>';
        } else {
            // Di chuyển tệp tin hình ảnh vào thư mục mong muốn
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Chèn món ăn vào cơ sở dữ liệu
                $sql = "INSERT INTO dishes(rs_id,title,slogan,price,img) VALUE(?,?,?,?,?)";
                $stmt = mysqli_prepare($db, $sql);
                mysqli_stmt_bind_param($stmt, "issss", $res_id, $_POST['d_name'], $_POST['about'], $_POST['price'], $fnew);
                mysqli_stmt_execute($stmt);
                $success = '<div class="alert alert-success">Thêm món ăn thành công!</div>';
            } else {
                $error = '<div class="alert alert-danger">Có lỗi khi tải hình ảnh lên!</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý menu</title>
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
                <a href="menu.php" class="sidebar-link active">
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
                    <h4 class="mb-0">Quản lý Menu</h4>
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
                <?php if(isset($error)) echo $error; ?>
                <?php if(isset($success)) echo $success; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách món ăn</h5>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addDishModal">
                            <i class="fas fa-plus me-2"></i>Thêm món
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="menuTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Món ăn</th>
                                        <th>Mô tả</th>
                                        <th>Giá</th>
                                        <th>Hình ảnh</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM dishes WHERE rs_id = ? ORDER BY d_id DESC";
                                    $stmt = mysqli_prepare($db, $sql);
                                    mysqli_stmt_bind_param($stmt, "i", $res_id);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    
                                    while($row = mysqli_fetch_array($result)) {
                                        echo '<tr>
                                            <td>'.$row['title'].'</td>
                                            <td>'.$row['slogan'].'</td>
                                            <td>'.number_format($row['price']).' VNĐ</td>
                                            <td><img src="../admin/Res_img/dishes/'.$row['img'].'" style="width:80px"></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm edit-dish" data-id="'.$row['d_id'].'">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm delete-dish" data-id="'.$row['d_id'].'">
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

    <!-- Add Dish Modal -->
    <div class="modal fade" id="addDishModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Thêm món ăn mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tên món</label>
                            <input type="text" name="d_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="about" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
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
            $('#menuTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                }
            });
        });
    </script>
</body>
</html>