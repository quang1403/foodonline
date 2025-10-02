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
    <title>Quản lý đơn hàng</title>
    
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
                <a href="all_orders.php" class="sidebar-link active">
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
                    <h4 class="mb-0">Quản lý đơn hàng</h4>
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
                                    $sql = "SELECT users.*, users_orders.* FROM users INNER JOIN users_orders ON users.u_id=users_orders.u_id ";
                                    $query = mysqli_query($db, $sql);
                                    
                                    if(!mysqli_num_rows($query) > 0) {
                                        echo '<tr><td colspan="8" class="text-center">Không có đơn hàng!</td></tr>';
                                    } else {
                                        while($rows = mysqli_fetch_array($query)) {
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
                                                case 'preparing':
                                                    $badge_class = 'bg-secondary';
                                                    $status_text = 'Đang chuẩn bị';
                                                    $icon = 'hourglass-half';
                                                    break;
                                                case 'prepared':
                                                    $badge_class = 'bg-primary';
                                                    $status_text = 'Đã chuẩn bị';
                                                    $icon = 'check';
                                                    break;
                                            }
                                            
                                            echo '<td><span class="badge '.$badge_class.' status-badge">
                                                    <i class="fas fa-'.$icon.' me-1"></i>'.$status_text.'
                                                  </span></td>';
                                            
                                            echo '<td>'.$rows['date'].'</td>
                                                <td>
                                                    <a href="#" class="btn btn-primary btn-sm" data-id="'.$rows['o_id'].'">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="delete_orders.php?order_del='.$rows['o_id'].'" 
                                                       onclick="return confirm(\'Bạn có chắc muốn xóa đơn hàng này?\')" 
                                                       class="btn btn-danger btn-sm">
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

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Thông tin đơn hàng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width: 30%"><strong>Tên đăng nhập:</strong></td>
                                    <td id="orderUsername"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary update-status">
                                            Cập nhật trạng thái
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Món ăn:</strong></td>
                                    <td id="orderTitle"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary view-user">
                                            Thông tin khách hàng
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Số lượng:</strong></td>
                                    <td id="orderQuantity"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><strong>Giá:</strong></td>
                                    <td id="orderPrice"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><strong>Địa chỉ:</strong></td>
                                    <td id="orderAddress"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày đặt:</strong></td>
                                    <td id="orderDate"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td id="orderStatus"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                }
            });

            // Handle view button click - sửa selector để chỉ bắt nút view
            $(document).on('click', '.btn-primary[data-id]', function(e) {
                e.preventDefault();
                const orderId = $(this).data('id');
                
                // Load order details
                $.ajax({
                    url: 'get_order.php',
                    type: 'GET',
                    data: {id: orderId},
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        
                        const data = response.data; // Lấy data từ response
                        
                        // Fill modal data
                        $('#orderUsername').text(data.username);
                        $('#orderTitle').text(data.title);
                        $('#orderQuantity').text(data.quantity);
                        $('#orderPrice').text(data.formatted_price);
                        $('#orderAddress').text(data.address);
                        $('#orderDate').text(data.formatted_date);
                        
                        // Set status badge
                        let statusHtml = getStatusBadge(data.status);
                        $('#orderStatus').html(statusHtml);
                        
                        // Store order ID for buttons
                        $('.update-status').data('id', orderId);
                        $('.view-user').data('id', orderId);
                        
                        // Show modal
                        $('#viewOrderModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Không thể tải thông tin đơn hàng: ' + error);
                    }
                });
            });

            // Handle status update button
            $('.update-status').click(function() {
                const orderId = $(this).data('id');
                window.open(`order_update.php?form_id=${orderId}`, 'Update Status', 
                    'width=800,height=600,left=200,top=200');
            });

            // Handle view user button
            $('.view-user').click(function() {
                const orderId = $(this).data('id');
                window.open(`userprofile.php?newform_id=${orderId}`, 'User Profile', 
                    'width=800,height=600,left=200,top=200');
            });

            // Helper function for status badge
            function getStatusBadge(status) {
                let badge_class = '';
                let status_text = '';
                let icon = '';
                
                switch(status) {
                    case '':
                        badge_class = 'bg-info';
                        status_text = 'Chờ xác nhận';
                        icon = 'clock';
                        break;
                    case 'in process':
                        badge_class = 'bg-warning';
                        status_text = 'Đang giao';
                        icon = 'motorcycle';
                        break;
                    case 'closed':
                        badge_class = 'bg-success';
                        status_text = 'Đã giao';
                        icon = 'check-circle';
                        break;
                    case 'rejected':
                        badge_class = 'bg-danger';
                        status_text = 'Đã hủy';
                        icon = 'times-circle';
                        break;
                    case 'preparing':
                        badge_class = 'bg-secondary';
                        status_text = 'Đang chuẩn bị';
                        icon = 'hourglass-half';
                        break;
                    case 'prepared':
                        badge_class = 'bg-primary';
                        status_text = 'Đã chuẩn bị';
                        icon = 'check';
                        break;
                }
                
                return `<span class="badge ${badge_class} status-badge">
                            <i class="fas fa-${icon} me-1"></i>${status_text}
                        </span>`;
            }
        });
    </script>
</body>
</html>
<?php } ?>
