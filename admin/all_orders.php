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
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .table-success {
            transition: background-color 0.3s ease;
            background-color: #d1e7dd !important;
        }
        
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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
                                    $sql = "SELECT users.*, users_orders.*, restaurant.title as restaurant_name FROM users 
                                            INNER JOIN users_orders ON users.u_id=users_orders.u_id 
                                            LEFT JOIN restaurant ON users_orders.rs_id=restaurant.rs_id
                                            ORDER BY users_orders.date DESC";
                                    $query = mysqli_query($db, $sql);
                                    
                                    if(!mysqli_num_rows($query) > 0) {
                                        echo '<tr><td colspan="9" class="text-center">Không có đơn hàng!</td></tr>';
                                    } else {
                                        while($rows = mysqli_fetch_array($query)) {
                                            $has_pending = !empty($rows['pending_status']) && $rows['pending_status'] != $rows['status'];
                                            $row_class = $has_pending ? 'table-warning' : '';
                                            
                                            echo '<tr class="'.$row_class.'">
                                                <td>'.$rows['username'].'</td>
                                                <td>'.$rows['title'].'</td>
                                                <td>'.$rows['quantity'].'</td>
                                                <td>'.number_format($rows['price'], 0, ',', '.').' VNĐ</td>
                                                <td>'.$rows['address'].'</td>';
                                            
                                            // Status badge - hiển thị cả trạng thái hiện tại và chờ phê duyệt
                                            echo '<td>';
                                            
                                            // Trạng thái hiện tại
                                            $status = $rows['status'];
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
                                            
                                            echo '<span class="badge '.$badge_class.' status-badge">
                                                    <i class="fas fa-'.$icon.' me-1"></i>'.$status_text.'
                                                  </span>';
                                            
                                            // Nếu có trạng thái chờ phê duyệt
                                            if($has_pending) {
                                                $pending_status = $rows['pending_status'];
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
                                            
                                            echo '<td>'.$rows['date'].'</td>
                                                <td>
                                                    <a href="#" class="btn btn-primary btn-sm" data-id="'.$rows['o_id'].'">
                                                        <i class="fas fa-eye"></i>
                                                    </a>';
                                            
                                            // Nút phê duyệt nếu có trạng thái chờ
                                            if($has_pending) {
                                                echo '<button class="btn btn-success btn-sm approve-status ms-1" 
                                                            data-id="'.$rows['o_id'].'" 
                                                            data-status="'.$rows['pending_status'].'"
                                                            title="Phê duyệt trạng thái">
                                                        <i class="fas fa-check"></i>
                                                      </button>
                                                      <button class="btn btn-warning btn-sm reject-pending ms-1" 
                                                            data-id="'.$rows['o_id'].'"
                                                            title="Từ chối phê duyệt">
                                                        <i class="fas fa-times"></i>
                                                      </button>';
                                            }
                                            
                                            echo '<a href="delete_orders.php?order_del='.$rows['o_id'].'" 
                                                       onclick="return confirm(\'Bạn có chắc muốn xóa đơn hàng này?\')" 
                                                       class="btn btn-danger btn-sm ms-1">
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

    <!-- Modal cập nhật trạng thái -->
    <div class="modal fade" id="updateOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Cập nhật trạng thái đơn hàng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="updateOrderId" name="order_id">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">Thông tin đơn hàng</h6>
                                <p><strong>Khách hàng:</strong> <span id="updateCustomerName"></span></p>
                                <p><strong>Món ăn:</strong> <span id="updateDishName"></span></p>
                                <p><strong>Số lượng:</strong> <span id="updateQuantity"></span></p>
                                <p><strong>Giá:</strong> <span id="updatePrice"></span></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Trạng thái hiện tại</h6>
                                <div id="updateCurrentStatus"></div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="newStatus" class="form-label">Trạng thái mới <span class="text-danger">*</span></label>
                                <select name="status" id="newStatus" class="form-select" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="preparing">
                                        <i class="fas fa-hourglass-half"></i> Đang chuẩn bị
                                    </option>
                                    <option value="prepared">
                                        <i class="fas fa-check"></i> Đã chuẩn bị
                                    </option>
                                    <option value="in process">
                                        <i class="fas fa-motorcycle"></i> Đang giao
                                    </option>
                                    <option value="closed">
                                        <i class="fas fa-check-circle"></i> Đã giao
                                    </option>
                                    <option value="rejected">
                                        <i class="fas fa-times-circle"></i> Đã hủy
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="updateRemark" class="form-label">Ghi chú <span class="text-danger">*</span></label>
                            <textarea name="remark" id="updateRemark" class="form-control" rows="4" 
                                      placeholder="Nhập ghi chú về việc cập nhật trạng thái..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-success" id="saveStatusUpdate">
                        <i class="fas fa-save me-1"></i>Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal Thông tin khách hàng (đẹp hơn) -->
        <div class="modal fade" id="userProfileModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-0" style="border-radius: 15px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.12);">
                    <div class="card-header text-white d-flex align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 15px 15px 0 0 !important; padding: 20px;">
                        <i class="fas fa-user-circle profile-icon me-3" style="font-size:2.5rem;color:#fff;"></i>
                        <h4 class="mb-0">Thông tin khách hàng</h4>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="card-body p-4" style="background: rgba(255,255,255,0.98); border-radius: 0 0 15px 15px;">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 label-text">Họ tên:</div>
                            <div class="col-8 value-text" id="upFullName"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 label-text">Tên đăng nhập:</div>
                            <div class="col-8 value-text" id="upUsername"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 label-text">Email:</div>
                            <div class="col-8 value-text" id="upEmail"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 label-text">Số điện thoại:</div>
                            <div class="col-8 value-text" id="upPhone"></div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 label-text">Ngày đăng ký:</div>
                            <div class="col-8 value-text" id="upDate"></div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-4 label-text">Trạng thái:</div>
                            <div class="col-8" id="upStatus"></div>
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
                
                // Load order details for update modal
                $.ajax({
                    url: 'get_order.php',
                    type: 'GET',
                    data: {id: orderId},
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        
                        const data = response.data;
                        
                        // Populate update modal
                        $('#updateOrderId').val(orderId);
                        $('#updateCustomerName').text(data.username);
                        $('#updateDishName').text(data.title);
                        $('#updateQuantity').text(data.quantity);
                        $('#updatePrice').text(data.formatted_price);
                        
                        // Show current status
                        let statusHtml = getStatusBadge(data.status);
                        $('#updateCurrentStatus').html(statusHtml);
                        
                        // Reset form
                        $('#newStatus').val('');
                        $('#updateRemark').val('');
                        
                        // Hide view modal and show update modal
                        $('#viewOrderModal').modal('hide');
                        $('#updateOrderModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Không thể tải thông tin đơn hàng: ' + error);
                    }
                });
            });

            // Handle save status update
            $('#saveStatusUpdate').click(function() {
                const formData = {
                    order_id: parseInt($('#updateOrderId').val()),
                    status: $('#newStatus').val(),
                    remark: $('#updateRemark').val().trim()
                };
                
                // Validate form
                if(!formData.status) {
                    alert('Vui lòng chọn trạng thái mới!');
                    $('#newStatus').focus();
                    return;
                }
                
                if(!formData.remark) {
                    alert('Vui lòng nhập ghi chú!');
                    $('#updateRemark').focus();
                    return;
                }
                
                // Confirm update
                if(!confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng này?')) {
                    return;
                }
                
                // Show loading
                const $saveBtn = $('#saveStatusUpdate');
                const originalText = $saveBtn.html();
                $saveBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Đang cập nhật...').prop('disabled', true);
                
                // Send AJAX request
                $.ajax({
                    url: 'update_order_status.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if(response.success) {
                            // Show success message
                            showNotification('success', 'Cập nhật trạng thái thành công!');
                            
                            // Update the table row
                            updateTableRow(formData.order_id, response.data);
                            
                            // Close modal
                            $('#updateOrderModal').modal('hide');
                        } else {
                            alert('Lỗi: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Không thể cập nhật trạng thái: ' + error);
                    },
                    complete: function() {
                        // Restore button
                        $saveBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Function to update table row after status change
            function updateTableRow(orderId, data) {
                const $row = $(`a[data-id="${orderId}"]`).closest('tr');
                if($row.length) {
                    // Update status column
                    const statusHtml = getStatusBadge(data.new_status);
                    $row.find('td:eq(5)').html(statusHtml);
                    
                    // Add visual feedback
                    $row.addClass('table-success');
                    setTimeout(() => {
                        $row.removeClass('table-success');
                    }, 2000);
                }
            }

            // Function to show notification
            function showNotification(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
                
                const notification = $(`
                    <div class="alert ${alertClass} alert-dismissible fade show notification" role="alert">
                        <i class="fas fa-${icon} me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                // Add to top of container
                $('.container-fluid').prepend(notification);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    notification.alert('close');
                }, 5000);
            }

            // Handle view user button
            $('.view-user').click(function() {
                const orderId = $(this).data('id');
                // Gọi AJAX lấy thông tin khách hàng
                $.ajax({
                    url: 'get_userprofile.php',
                    type: 'GET',
                    data: {order_id: orderId},
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            const user = response.data;
                            $('#upFullName').text(user.f_name + ' ' + user.l_name);
                            $('#upUsername').text(user.username);
                            $('#upEmail').text(user.email);
                            $('#upPhone').text(user.phone);
                            $('#upDate').text(user.date);
                            if(user.status == 1) {
                                $('#upStatus').html('<span class="badge bg-primary status-badge ms-2">Hoạt động</span>');
                            } else {
                                $('#upStatus').html('<span class="badge bg-danger status-badge ms-2">Đã khóa</span>');
                            }
                            // Ẩn modal đơn hàng trước khi hiện modal khách hàng
                            $('#viewOrderModal').modal('hide');
                            $('#userProfileModal').modal('show');
                        } else {
                            alert('Không tìm thấy thông tin khách hàng!');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Lỗi khi lấy thông tin khách hàng: ' + error);
                    }
                });
            });

            // Khi đóng modal khách hàng, tự động mở lại modal đơn hàng nếu modal đơn hàng đang ẩn
            $('#userProfileModal').on('hidden.bs.modal', function () {
                $('#viewOrderModal').modal('show');
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
            
            // Handle approve status
            $(document).on('click', '.approve-status', function(e) {
                e.preventDefault();
                const orderId = $(this).data('id');
                
                if(confirm('Bạn có chắc muốn phê duyệt trạng thái này?')) {
                    $.ajax({
                        url: 'approve_order_status.php',
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            action: 'approve'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.success) {
                                                                alert('Đã phê duyệt trạng thái thành công!');
                                                                location.reload();
                                                            } else {
                                                                alert('Lỗi: ' + response.message);
                                                            }
                                                        },
                                                        error: function(xhr, status, error) {
                                                            alert('Không thể phê duyệt trạng thái: ' + error);
                                                        }
                                                    });
                                                }
                                            });
                                            
                                            // Handle reject pending status
                                            $(document).on('click', '.reject-pending', function(e) {
                                                e.preventDefault();
                                                const orderId = $(this).data('id');
                                                
                                                if(confirm('Bạn có chắc muốn từ chối cập nhật trạng thái này?')) {
                                                    $.ajax({
                                                        url: 'approve_order_status.php',
                                                        type: 'POST',
                                                        data: {
                                                            order_id: orderId,
                                                            action: 'reject'
                                                        },
                                                        dataType: 'json',
                                                        success: function(response) {
                                                            if(response.success) {
                                                                alert('Đã từ chối cập nhật trạng thái!');
                                                                location.reload();
                                                            } else {
                                                                alert('Lỗi: ' + response.message);
                                                            }
                                                        },
                                                        error: function(xhr, status, error) {
                                                            alert('Không thể từ chối cập nhật: ' + error);
                                                        }
                                                    });
                                                }
                                            });
                                        });
                                    </script>
                                </body>
                                </html>
                                <?php
                                }
                                ?>
                           