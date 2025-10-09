<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["res_admin_id"])) {
    header('location:login.php');
    exit();
}

// Lấy rs_id từ session hoặc từ bảng restaurant_admin
$res_admin_id = $_SESSION["res_admin_id"];
$res_query = mysqli_query($db, "SELECT rs_id FROM restaurant_admin WHERE id='$res_admin_id'");
$res_row = mysqli_fetch_assoc($res_query);
$res_id = $res_row['rs_id'];
$_SESSION["res_id"] = $res_id;
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
$query_res = mysqli_query($db,"SELECT uo.*, u.username 
    FROM users_orders uo
    JOIN users u ON uo.u_id = u.u_id
    WHERE uo.rs_id='$res_id'
    ORDER BY uo.date DESC");

if(!mysqli_num_rows($query_res)) {
    echo '<tr><td colspan="8" class="text-center">Không có đơn hàng nào.</td></tr>';
} else {
    while($row = mysqli_fetch_assoc($query_res)) {
        echo '<tr>
            <td>'.htmlspecialchars($row['username']).'</td>
            <td>'.htmlspecialchars($row['title']).'</td>
            <td>'.$row['quantity'].'</td>
            <td>'.number_format($row['price'], 0, ',', '.').' VNĐ</td>
            <td>'.htmlspecialchars($row['address']).'</td>';

        // Hiển thị trạng thái hiện tại và trạng thái chờ phê duyệt
        $status = $row['status'];
        $pending_status = $row['pending_status'];
        
        echo '<td>';
        
        // Trạng thái hiện tại
        $badge_class = '';
        $status_text = '';
        $icon = '';
        switch($status) {
            case '':
            case 'NULL':
                $badge_class = 'bg-info';
                $status_text = 'Chờ xác nhận';
                $icon = 'clock';
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
        echo '<span class="badge '.$badge_class.' status-badge">
                <i class="fas fa-'.$icon.' me-1"></i>'.$status_text.'
              </span>';
        
        // Nếu có trạng thái chờ phê duyệt
        if(!empty($pending_status) && $pending_status != $status) {
            $pending_badge_class = '';
            $pending_status_text = '';
            $pending_icon = '';
            switch($pending_status) {
                case 'preparing':
                    $pending_badge_class = 'bg-secondary';
                    $pending_status_text = 'Đang chuẩn bị';
                    $pending_icon = 'hourglass-half';
                    break;
                case 'prepared':
                    $pending_badge_class = 'bg-primary';
                    $pending_status_text = 'Đã chuẩn bị';
                    $pending_icon = 'check';
                    break;
                case 'in process':
                    $pending_badge_class = 'bg-warning';
                    $pending_status_text = 'Đang giao';
                    $pending_icon = 'motorcycle';
                    break;
                case 'closed':
                    $pending_badge_class = 'bg-success';
                    $pending_status_text = 'Đã giao';
                    $pending_icon = 'check-circle';
                    break;
                case 'rejected':
                    $pending_badge_class = 'bg-danger';
                    $pending_status_text = 'Đã hủy';
                    $pending_icon = 'times-circle';
                    break;
            }
            echo '<br><small class="text-muted">Chờ phê duyệt:</small><br>
                  <span class="badge '.$pending_badge_class.' status-badge">
                    <i class="fas fa-'.$pending_icon.' me-1"></i>'.$pending_status_text.'
                  </span>';
        }
        
        echo '</td>';

        echo '<td>'.date('d/m/Y H:i', strtotime($row['date'])).'</td>
            <td>';
        
        // Chỉ cho phép cập nhật nếu chưa hoàn thành/hủy
        if($status != 'closed' && $status != 'rejected') {
            echo '<button class="btn btn-primary btn-sm update-status" data-id="'.$row['o_id'].'">
                    <i class="fas fa-edit"></i>
                  </button>';
        }
        
        echo '</td>
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Cập nhật trạng thái</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Trạng thái sẽ được gửi đến admin để phê duyệt trước khi hiển thị cho khách hàng.
                    </div>
                    <form id="updateStatusForm">
                        <input type="hidden" name="order_id" id="order_id">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái mới</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="preparing">Đang chuẩn bị</option>
                                <option value="prepared">Đã chuẩn bị</option>
                                <option value="in process">Đang giao</option>
                                <option value="closed">Đã giao</option>
                                <option value="rejected">Đã hủy</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveStatus">Gửi yêu cầu</button>
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

    // Handle update status button - sử dụng event delegation
    $(document).on('click', '.update-status', function() {
        const orderId = $(this).data('id');
        console.log('Order ID:', orderId); // Debug
        $('#order_id').val(orderId);
        $('#updateStatusModal').modal('show');
    });

    // Handle save status
    $('#saveStatus').click(function() {
        const $btn = $(this);
        const originalText = $btn.text();
        const formData = $('#updateStatusForm').serialize();
        
        console.log('Form data:', formData); // Debug
        
        // Kiểm tra form validation
        const status = $('select[name="status"]').val();
        if(!status) {
            alert('Vui lòng chọn trạng thái!');
            return;
        }
        
        $btn.text('Đang gửi...').prop('disabled', true);
        
        $.ajax({
            url: 'update_order_status.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response); // Debug
                if(response.success) {
                    alert(response.message);
                    $('#updateStatusModal').modal('hide');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (response.message || 'Có lỗi xảy ra'));
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText); // Debug
                alert('Có lỗi xảy ra khi gửi yêu cầu: ' + error);
            },
            complete: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
});
    </script>
</body>
</html>