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
    <title>Quản lý người dùng</title>
    
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
        }
        
        .sidebar-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
            text-decoration: none;
        }

        .navbar {
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        }
        
        .table-action-btn {
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
                <a href="all_users.php" class="sidebar-link active">
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
                        <h4 class="mb-0">Quản lý người dùng</h4>
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
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Điện thoại</th>
                                        <th>Địa chỉ</th>
                                        <th>Ngày tham gia</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM users ORDER BY u_id DESC";
                                    $query = mysqli_query($db,$sql);
                                    
                                    if(mysqli_num_rows($query) > 0) {
                                        while($row = mysqli_fetch_array($query)) {
                                            // Lấy địa chỉ từ bảng user_addresses
                                            $addr_query = mysqli_query($db, "SELECT address, is_default FROM user_addresses WHERE user_id='".$row['u_id']."' ORDER BY is_default DESC, id DESC");
                                            $addresses = [];
                                            while($addr = mysqli_fetch_array($addr_query)) {
                                                $addresses[] = ($addr['is_default'] == 1 ? '<b>'.$addr['address'].' <span class=\'badge bg-success\'>Mặc định</span></b>' : $addr['address']);
                                            }
                                            $addr_html = count($addresses) ? implode('<br>', $addresses) : '<span class="text-muted">Chưa có</span>';
                                            echo '<tr>
                                                <td>'.$row['username'].'</td>
                                                <td>'.$row['f_name'].' '.$row['l_name'].'</td>
                                                <td>'.$row['email'].'</td>
                                                <td>'.$row['phone'].'</td>
                                                <td>'.$addr_html.'</td>
                                                <td>'.date('d/m/Y', strtotime($row['date'])).'</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm edit-user" data-id="'.$row['u_id'].'">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="delete_users.php?user_del='.$row['u_id'].'" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm(\'Bạn có chắc muốn xóa người dùng này?\')">
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

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Cập nhật người dùng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" name="user_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên đăng nhập</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Họ</label>
                                    <input type="text" name="f_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên</label>
                                    <input type="text" name="l_name" class="form-control" required>
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
                                    <label class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" class="form-control">
                                    <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu</small>
                                </div>
                            </div>
                            
                            <!-- User Addresses Section -->
                            <div class="col-12">
                                <hr class="my-3">
                                <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng đã lưu</h6>
                                <div id="userAddressesList" class="mb-3">
                                    <!-- Addresses will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveUser">Lưu thay đổi</button>
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
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-user', function() {
                const userId = $(this).data('id');
                
                // Load user data
                $.ajax({
                    url: 'get_user.php',
                    type: 'GET',
                    data: { id: userId },
                    dataType: 'json',
                    success: function(response) {
                        if(response.error) {
                            alert(response.error);
                            return;
                        }
                        
                        const data = response.data;
                        
                        // Fill form data
                        $('input[name="user_id"]').val(data.u_id);
                        $('input[name="username"]').val(data.username);
                        $('input[name="f_name"]').val(data.f_name);
                        $('input[name="l_name"]').val(data.l_name);
                        $('input[name="email"]').val(data.email);
                        $('input[name="phone"]').val(data.phone);
                        
                        // Display user addresses
                        displayUserAddresses(data.addresses, data.u_id);
                        
                        // Show modal
                        $('#editUserModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + error);
                        console.log(xhr.responseText);
                    }
                });
            });

            // Handle save changes
            $('#saveUser').click(function() {
                $.ajax({
                    url: 'update_user_ajax.php',
                    type: 'POST',
                    data: $('#editUserForm').serialize(),
                    success: function(response) {
                        const result = JSON.parse(response);
                        if(result.success) {
                            $('#editUserModal').modal('hide');
                            location.reload();
                        } else {
                            alert(result.message || 'Có lỗi xảy ra');
                        }
                    }
                });
            });
            
            // Function to display user addresses
            function displayUserAddresses(addresses, userId) {
                const container = $('#userAddressesList');
                container.empty();
                
                if(!addresses || addresses.length === 0) {
                    container.html('<div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>Người dùng chưa có địa chỉ giao hàng nào.</div>');
                    return;
                }
                
                addresses.forEach(function(addr) {
                    const defaultBadge = addr.is_default == 1 ? '<span class="badge bg-success ms-2"><i class="fas fa-star"></i> Mặc định</span>' : '';
                    const addressCard = `
                        <div class="card mb-2" style="border-left: 3px solid ${addr.is_default == 1 ? '#28a745' : '#6c757d'};">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span>${addr.address}</span>
                                        ${defaultBadge}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-clock me-1"></i>${new Date(addr.created_at).toLocaleDateString('vi-VN')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(addressCard);
                });
            }
        });
    </script>
</body>
</html>
<?php } ?>
