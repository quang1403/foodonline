<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý menu</title>
    
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
                <a href="all_menu.php" class="sidebar-link active">
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
                    <h4 class="mb-0">Quản lý menu</h4>
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
                        <h5 class="mb-0">Danh sách món ăn</h5>
                        <a href="add_menu.php" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Thêm món
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="menuTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nhà hàng</th>
                                        <th>Món ăn</th>
                                        <th>Mô tả</th>
                                        <th>Giá</th>
                                        <th>Hình ảnh</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT d.*, r.title as restaurant_name 
                                           FROM dishes d 
                                           LEFT JOIN restaurant r ON d.rs_id = r.rs_id 
                                           ORDER BY d.d_id DESC";
                                    $query = mysqli_query($db, $sql);
                                    
                                    if(!mysqli_num_rows($query) > 0) {
                                        echo '<tr><td colspan="6" class="text-center">Không có dữ liệu</td></tr>';
                                    } else {
                                        while($rows = mysqli_fetch_array($query)) {
                                            echo '<tr>
                                                <td>'.$rows['restaurant_name'].'</td>
                                                <td>'.$rows['title'].'</td>
                                                <td>'.$rows['slogan'].'</td>
                                                <td>'.number_format($rows['price'], 0, ',', '.').' VNĐ</td>
                                                <td>
                                                    <img src="Res_img/dishes/'.$rows['img'].'" class="img-thumbnail" style="max-height:80px">
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-primary btn-sm edit-menu" data-id="'.$rows['d_id'].'">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete_menu.php?menu_del='.$rows['d_id'].'" class="btn btn-danger btn-sm" 
                                                       onclick="return confirm(\'Bạn có chắc muốn xóa món ăn này?\')">
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

    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Cập nhật món ăn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editMenuForm" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nhà hàng</label>
                                    <select name="res_name" class="form-select" required></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên món</label>
                                    <input type="text" name="d_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="about" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Giá</label>
                                    <input type="number" name="price" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Hình ảnh</label>
                                    <input type="file" name="file" class="form-control">
                                    <div id="currentImage" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="menu_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveMenu">Lưu thay đổi</button>
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

            // Handle edit button click
            $(document).on('click', '.edit-menu', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                
                // Load restaurants first
                $.ajax({
                    url: 'get_restaurants.php',
                    type: 'GET',
                    success: function(restaurants) {
                        // Populate restaurant dropdown
                        let options = '<option value="">Chọn nhà hàng</option>';
                        restaurants.forEach(rest => {
                            options += `<option value="${rest.rs_id}">${rest.title}</option>`;
                        });
                        $('select[name="res_name"]').html(options);
                        
                        // Then load menu item details
                        $.ajax({
                            url: 'get_menu_item.php',
                            type: 'GET',
                            data: {id: id},
                            success: function(response) {
                                const data = JSON.parse(response);
                                
                                // Fill form data
                                $('select[name="res_name"]').val(data.rs_id);
                                $('input[name="d_name"]').val(data.title);
                                $('textarea[name="about"]').val(data.slogan);
                                $('input[name="price"]').val(data.price);
                                $('input[name="menu_id"]').val(data.d_id);
                                
                                if(data.img) {
                                    $('#currentImage').html(`
                                        <img src="Res_img/dishes/${data.img}" 
                                             class="img-thumbnail" 
                                             style="max-height:100px">
                                    `);
                                }
                                
                                // Show modal after data is loaded
                                $('#editMenuModal').modal('show');
                            },
                            error: function(xhr, status, error) {
                                alert('Không thể tải thông tin món ăn: ' + error);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        alert('Không thể tải danh sách nhà hàng: ' + error);
                    }
                });
            });

            // Handle save changes
            $('#saveMenu').click(function() {
                const form = $('#editMenuForm')[0];
                const formData = new FormData(form);
                
                $.ajax({
                    url: 'update_menu_ajax.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if(result.success) {
                            $('#editMenuModal').modal('hide');
                            location.reload();
                            alert('Cập nhật thành công!');
                        } else {
                            alert('Có lỗi xảy ra: ' + result.message);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php } ?>
