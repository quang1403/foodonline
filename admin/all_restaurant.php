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
    <title>Quản lý nhà hàng</title>
    
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

        .stat-card {
            border-radius: 0.5rem;
            border: none;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
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

        .restaurant-stats {
            margin-bottom: 2rem;
        }

        .data-card {
            border-radius: 0.5rem;
            border: none;
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
                <a href="all_restaurant.php" class="sidebar-link active">
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
                        <h4 class="mb-0">Quản lý nhà hàng</h4>
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
                <!-- Restaurant Stats -->
                <div class="row g-4 restaurant-stats">
                    <div class="col-md-4">
                        <div class="card stat-card h-100" style="background: #2eaa7c;">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-white">Tổng nhà hàng</span>
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fas fa-store text-white"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-white mt-3 mb-0">
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM restaurant";
                                        $result = mysqli_query($db, $sql);
                                        $data = mysqli_fetch_assoc($result);
                                        echo $data['total'];
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card stat-card h-100" style="background: #ffc107;">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-white">Phân loại</span>
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fas fa-th-large text-white"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-white mt-3 mb-0">
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM res_category";
                                        $result = mysqli_query($db, $sql);
                                        $data = mysqli_fetch_assoc($result);
                                        echo $data['total'];
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card stat-card h-100" style="background: #dc3545;">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-white">Tổng món ăn</span>
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fas fa-utensils text-white"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-white mt-3 mb-0">
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM dishes";
                                        $result = mysqli_query($db, $sql);
                                        $data = mysqli_fetch_assoc($result);
                                        echo $data['total'];
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant Table -->
                <div class="card data-card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách nhà hàng</h5>
                        <div>
                            <a href="add_category.php" class="btn btn-light me-2">
                                <i class="fas fa-th-large me-2"></i>Thêm phân loại
                            </a>
                            <a href="add_restaurant.php" class="btn btn-light">
                                <i class="fas fa-plus me-2"></i>Thêm nhà hàng
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="restaurantTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Phân loại</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Giờ mở cửa</th>
                                        <th>Giờ đóng cửa</th>
                                        <th>Ngày mở cửa</th>
                                        <th>Địa chỉ</th>
                                        <th>Ảnh</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Restaurant Modal -->
    <div class="modal fade" id="editRestaurantModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Cập nhật nhà hàng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editRestaurantForm" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phân loại</label>
                                    <select name="c_name" class="form-select" required>
                                        <option value="">Chọn phân loại</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên nhà hàng</label>
                                    <input type="text" name="res_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Giờ mở cửa</label>
                                    <input type="time" name="o_hr" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Giờ đóng cửa</label>
                                    <input type="time" name="c_hr" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày mở cửa</label>
                                    <input type="text" name="o_days" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="url" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ</label>
                                    <textarea name="address" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Hình ảnh</label>
                                    <input type="file" name="file" class="form-control">
                                    <div id="currentImage" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="restaurant_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveRestaurant">Lưu thay đổi</button>
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
            $('#restaurantTable').DataTable({
                processing: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                },
                ajax: {
                    url: 'get_restaurants.php',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: 'category_name' }, // Thay đổi từ 'category' thành 'category_name'
                    { data: 'title' },
                    { data: 'email' },
                    { data: 'phone' },
                    { data: 'o_hr' },
                    { data: 'c_hr' },
                    { data: 'o_days' },
                    { data: 'address' },
                    { 
                        data: 'image',
                        render: function(data) {
                            return `<img src="Res_img/${data}" class="img-thumbnail" style="width:100px">`;
                        }
                    },
                    {
                        data: 'rs_id',
                        render: function(data) {
                            return `
                                <a href="#" class="btn btn-primary btn-action edit-restaurant" data-id="${data}" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-action delete-restaurant" data-id="${data}" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            `;
                        }
                    }
                ]
            });

            // Xử lý khi click nút sửa
            $(document).on('click', '.edit-restaurant', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                
                // Load danh sách phân loại trước
                $.ajax({
                    url: 'get_categories.php',
                    type: 'GET',
                    success: function(response) {
                        const categories = JSON.parse(response);
                        let options = '<option value="">Chọn phân loại</option>';
                        categories.forEach(cat => {
                            options += `<option value="${cat.c_id}">${cat.c_name}</option>`;
                        });
                        $('select[name="c_name"]').html(options);
                        
                        // Sau khi load xong categories mới load thông tin nhà hàng
                        $.ajax({
                            url: 'get_restaurant.php',
                            type: 'GET',
                            data: {id: id},
                            success: function(response) {
                                const data = JSON.parse(response);
                                
                                // Điền dữ liệu vào form
                                $('select[name="c_name"]').val(data.c_id);
                                $('input[name="res_name"]').val(data.title);
                                $('input[name="email"]').val(data.email);
                                $('input[name="phone"]').val(data.phone);
                                $('input[name="o_hr"]').val(data.o_hr);
                                $('input[name="c_hr"]').val(data.c_hr);
                                $('input[name="o_days"]').val(data.o_days);
                                $('input[name="url"]').val(data.url);
                                $('textarea[name="address"]').val(data.address);
                                $('input[name="restaurant_id"]').val(data.rs_id);
                                
                                if(data.image) {
                                    $('#currentImage').html(`<img src="Res_img/${data.image}" class="img-thumbnail" width="100">`);
                                }
                                
                                // Hiển thị modal
                                $('#editRestaurantModal').modal('show');
                            }
                        });
                    }
                });
            });

            // Xử lý khi submit form
            $('#saveRestaurant').click(function() {
                const form = $('#editRestaurantForm')[0];
                const formData = new FormData(form);
                
                $.ajax({
                    url: 'update_restaurant_ajax.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if(result.success) {
                            // Đóng modal
                            $('#editRestaurantModal').modal('hide');
                            // Reload table
                            $('#restaurantTable').DataTable().ajax.reload();
                            // Hiển thị thông báo
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