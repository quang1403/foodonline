
                <?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

$order_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;

if($order_id <= 0) {
    echo "<script>alert('ID đơn hàng không hợp lệ'); window.close();</script>";
    exit();
}

// Lấy thông tin đơn hàng
$sql = "SELECT 
            users_orders.*,
            users.username,
            users.f_name,
            users.l_name,
            users.email,
            users.phone,
            restaurant.title as restaurant_name
        FROM users_orders 
        INNER JOIN users ON users.u_id = users_orders.u_id
        LEFT JOIN restaurant ON restaurant.rs_id = users_orders.rs_id
        WHERE users_orders.o_id = ?";

$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if(!$order) {
    echo "<script>alert('Không tìm thấy đơn hàng'); window.close();</script>";
    exit();
}

// Lấy lịch sử remarks
$remark_sql = "SELECT * FROM remark WHERE frm_id = ? ORDER BY remarkDate DESC";
$remark_stmt = mysqli_prepare($db, $remark_sql);
mysqli_stmt_bind_param($remark_stmt, "i", $order_id);
mysqli_stmt_execute($remark_stmt);
$remark_result = mysqli_stmt_get_result($remark_stmt);

// Xử lý cập nhật
if(isset($_POST['update'])) {
    $status = $_POST['status'];
    $remark = $_POST['remark'];
    
    // Thêm remark mới
    $insert_remark = "INSERT INTO remark(frm_id, status, remark) VALUES(?, ?, ?)";
    $stmt_remark = mysqli_prepare($db, $insert_remark);
    mysqli_stmt_bind_param($stmt_remark, "iss", $order_id, $status, $remark);
    mysqli_stmt_execute($stmt_remark);
    
    // Cập nhật trạng thái đơn hàng
    $update_order = "UPDATE users_orders SET status = ? WHERE o_id = ?";
    $stmt_update = mysqli_prepare($db, $update_order);
    mysqli_stmt_bind_param($stmt_update, "si", $status, $order_id);
    mysqli_stmt_execute($stmt_update);
    
    echo "<script>
        alert('Cập nhật trạng thái thành công!');
        if(window.opener && window.opener.location) {
            window.opener.location.reload();
        }
        window.close();
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập nhật đơn hàng #<?php echo $order_id; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
            --secondary-color: #858796;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
        }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e3e6f0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #13855c 100%);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #5a5c69 100%);
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-color);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -37px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary-color);
        }
        
        .info-row {
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .label-text {
            font-weight: 600;
            color: #5a5c69;
        }
        
        .value-text {
            color: #3a3b45;
            font-weight: 500;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .order-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>
    <div class="container animate-fade-in">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                Cập nhật đơn hàng #<?php echo $order_id; ?>
                            </h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i>In
                                </button>
                                <button type="button" class="btn btn-outline-light btn-sm" onclick="window.close()">
                                    <i class="fas fa-times me-1"></i>Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Thông tin đơn hàng -->
                            <div class="col-md-8">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng
                                </h5>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Khách hàng:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="value-text"><?php echo htmlentities($order['username']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Họ tên:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="value-text"><?php echo htmlentities($order['f_name'] . ' ' . $order['l_name']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Món ăn:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="value-text"><?php echo htmlentities($order['title']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Số lượng:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="value-text"><?php echo $order['quantity']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Giá:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="value-text text-success fw-bold"><?php echo number_format($order['price'], 0, ',', '.'); ?> VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Ngày đặt:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="value-text"><?php echo date('d/m/Y H:i', strtotime($order['date'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span class="label-text">Trạng thái hiện tại:</span>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="order-status">
                                                <?php
                                                $status = $order['status'];
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
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?> status-badge">
                                                    <i class="fas fa-<?php echo $icon; ?> me-1"></i><?php echo $status_text; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Form cập nhật trạng thái -->
                                <div class="mt-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-edit me-2"></i>Cập nhật trạng thái
                                    </h5>
                                    
                                    <form method="post" id="updateForm">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="status" class="form-label">Trạng thái mới</label>
                                                <select name="status" id="status" class="form-select" required>
                                                    <option value="">Chọn trạng thái</option>
                                                    <option value="preparing">Đang chuẩn bị</option>
                                                    <option value="prepared">Đã chuẩn bị</option>
                                                    <option value="in process">Đang giao</option>
                                                    <option value="closed">Đã giao</option>
                                                    <option value="rejected">Đã hủy</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="remark" class="form-label">Ghi chú</label>
                                            <textarea name="remark" id="remark" class="form-control" rows="4" 
                                                      placeholder="Nhập ghi chú về việc cập nhật trạng thái..." required></textarea>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="update" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>Cập nhật
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="window.close()">
                                                <i class="fas fa-times me-2"></i>Hủy
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Lịch sử cập nhật -->
                            <div class="col-md-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-history me-2"></i>Lịch sử cập nhật
                                </h5>
                                
                                <div class="timeline">
                                    <?php 
                                    $has_remarks = false;
                                    while($remark = mysqli_fetch_assoc($remark_result)): 
                                        $has_remarks = true;
                                    ?>
                                    <div class="timeline-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary"><?php echo $remark['status']; ?></span>
                                            <small class="text-muted"><?php echo date('d/m H:i', strtotime($remark['remarkDate'])); ?></small>
                                        </div>
                                        <p class="mb-0 text-sm"><?php echo htmlentities($remark['remark']); ?></p>
                                    </div>
                                    <?php endwhile; ?>
                                    
                                    <?php if(!$has_remarks): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <p class="mb-0">Chưa có lịch sử cập nhật</p>
                                    </div>
                                    <?php endif; ?>
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
    <script>
        // Form validation
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            const remark = document.getElementById('remark').value.trim();
            
            if(!status) {
                e.preventDefault();
                alert('Vui lòng chọn trạng thái mới!');
                return;
            }
            
            if(!remark) {
                e.preventDefault();
                alert('Vui lòng nhập ghi chú!');
                return;
            }
            
            // Confirm before submit
            if(!confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng này?')) {
                e.preventDefault();
                return;
            }
        });
        
        // Auto-resize textarea
        const textarea = document.getElementById('remark');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close
            if(e.key === 'Escape') {
                window.close();
            }
            // Ctrl+Enter to submit
            if(e.ctrlKey && e.key === 'Enter') {
                document.getElementById('updateForm').submit();
            }
        });
    </script>
</body>
</html>