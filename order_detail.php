<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Xử lý hủy đơn hàng
if(isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['code'])) {
    $order_code = mysqli_real_escape_string($db, $_GET['code']);
    mysqli_query($db, "UPDATE users_orders SET status = 'rejected' WHERE order_code = '$order_code'");
    echo "<script>alert('Đơn hàng đã được hủy!');</script>";
    echo "<script>window.location.href='order_detail.php?code=$order_code';</script>";
    exit();
}

// Xử lý xóa đơn hàng
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['code'])) {
    $order_code = mysqli_real_escape_string($db, $_GET['code']);
    mysqli_query($db, "DELETE FROM users_orders WHERE order_code = '$order_code'");
    echo "<script>alert('Đơn hàng đã được xóa!');</script>";
    echo "<script>window.location.href='your_orders.php';</script>";
    exit();
}

// Lấy mã đơn hàng từ URL
$order_code = isset($_GET['code']) ? mysqli_real_escape_string($db, $_GET['code']) : '';

if(empty($order_code)) {
    header('location:index.php');
    exit();
}

// Lấy thông tin đơn hàng
$order_query = mysqli_query($db, "SELECT uo.*, u.username, u.email, u.phone, r.title as restaurant_name, r.address as restaurant_address 
                                   FROM users_orders uo 
                                   LEFT JOIN users u ON uo.u_id = u.u_id 
                                   LEFT JOIN restaurant r ON uo.rs_id = r.rs_id 
                                   WHERE uo.order_code = '$order_code' 
                                   ORDER BY uo.date ASC");

if(mysqli_num_rows($order_query) == 0) {
    if(isset($_GET['ajax'])) {
        echo '<div class="alert alert-danger">Không tìm thấy đơn hàng!</div>';
        exit();
    } else {
        header('location:index.php');
        exit();
    }
}

// Lấy thông tin chung của đơn hàng
$first_order = mysqli_fetch_assoc($order_query);
mysqli_data_seek($order_query, 0); // Reset pointer về đầu

// Tính tổng tiền
$total_amount = 0;
$temp_orders = array();
while($row = mysqli_fetch_assoc($order_query)) {
    $total_amount += ($row['price'] * $row['quantity']);
    $temp_orders[] = $row;
}

// Nếu là AJAX (modal), chỉ trả về phần nội dung chính
if(isset($_GET['ajax'])) {
    ob_start();
    ?>
    <div class="order-detail-container">
        <div class="order-card">
            <div class="order-header">
                <i class="fas fa-receipt fa-2x mb-2"></i>
                <h4 class="mb-2">CHI TIẾT ĐƠN HÀNG</h4>
                <div class="order-code"><?php echo $order_code; ?></div>
                <div style="font-size: 0.9rem;">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo date('d/m/Y H:i', strtotime($first_order['date'])); ?>
                </div>
            </div>
            <div class="p-3">
                <div class="text-center mb-3">
                    <?php 
                    $status = $first_order['status'];
                    switch($status) {
                        case "NULL":
                        case "":
                            echo '<span class="status-badge bg-secondary"><i class="fas fa-clock me-2"></i>Chờ xác nhận</span>';
                            break;
                        case "preparing":
                            echo '<span class="status-badge bg-info"><i class="fas fa-hourglass-half me-2"></i>Đang chuẩn bị</span>';
                            break;
                        case "prepared":
                            echo '<span class="status-badge bg-primary"><i class="fas fa-check me-2"></i>Đã chuẩn bị</span>';
                            break;
                        case "in process":
                            echo '<span class="status-badge bg-warning"><i class="fas fa-motorcycle me-2"></i>Đang giao</span>';
                            break;
                        case "closed":
                            echo '<span class="status-badge bg-success"><i class="fas fa-check-circle me-2"></i>Đã giao</span>';
                            break;
                        case "rejected":
                            echo '<span class="status-badge bg-danger"><i class="fas fa-times-circle me-2"></i>Đã hủy</span>';
                            break;
                    }
                    ?>
                </div>
                <h5 class="section-title"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h5>
                <div class="info-row d-flex"><span class="info-label">Tên khách hàng:</span><span class="info-value"><?php echo htmlspecialchars($first_order['username']); ?></span></div>
                <div class="info-row d-flex"><span class="info-label">Email:</span><span class="info-value"><?php echo htmlspecialchars($first_order['email']); ?></span></div>
                <div class="info-row d-flex"><span class="info-label">Số điện thoại:</span><span class="info-value"><?php echo htmlspecialchars($first_order['phone']); ?></span></div>
                <div class="info-row d-flex"><span class="info-label">Địa chỉ giao hàng:</span><span class="info-value"><?php echo htmlspecialchars($first_order['address']); ?></span></div>
                <h5 class="section-title"><i class="fas fa-store me-2"></i>Thông tin nhà hàng</h5>
                <div class="info-row d-flex"><span class="info-label">Tên nhà hàng:</span><span class="info-value"><?php echo htmlspecialchars($first_order['restaurant_name']); ?></span></div>
                <div class="info-row d-flex"><span class="info-label">Địa chỉ:</span><span class="info-value"><?php echo htmlspecialchars($first_order['restaurant_address']); ?></span></div>
                <h5 class="section-title"><i class="fas fa-shopping-bag me-2"></i>Chi tiết món ăn</h5>
                <?php foreach($temp_orders as $order) { ?>
                <div class="item-card">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0"><?php echo htmlspecialchars($order['title']); ?></h6>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="badge bg-light text-dark">x<?php echo $order['quantity']; ?></span>
                        </div>
                        <div class="col-md-2">
                            <?php echo number_format($order['price'], 0, ',', '.'); ?> VNĐ
                        </div>
                        <div class="col-md-2 text-end">
                            <strong class="text-primary">
                                <?php echo number_format($order['price'] * $order['quantity'], 0, ',', '.'); ?> VNĐ
                            </strong>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="total-section">
                    <div class="row align-items-center">
                        <div class="col-md-6"><h5 class="mb-0">Tổng cộng:</h5></div>
                        <div class="col-md-6 text-end"><div class="total-amount"><?php echo number_format($total_amount, 0, ',', '.'); ?> VNĐ</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo ob_get_clean();
    exit();
}
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết đơn hàng - <?php echo $order_code; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #fd4d40;
            --secondary-color: #ff9b44;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 1rem 0;
        }

        .order-detail-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .order-code {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0.5rem 0;
            letter-spacing: 2px;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin: 1.5rem 0 0.75rem 0;
            padding-bottom: 0.4rem;
            border-bottom: 2px solid var(--primary-color);
            font-size: 1.1rem;
        }

        .info-row {
            padding: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.95rem;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 130px;
        }

        .info-value {
            color: #333;
        }

        .status-badge {
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .item-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .total-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .btn-print, .btn-cancel, .btn-delete {
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: var(--primary-color);
        }

        .btn-cancel {
            background: #ffc107;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .order-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="order-detail-container">
        <div class="order-card">
            <!-- Header -->
            <div class="order-header">
                <i class="fas fa-receipt fa-2x mb-2"></i>
                <h4 class="mb-2">CHI TIẾT ĐƠN HÀNG</h4>
                <div class="order-code"><?php echo $order_code; ?></div>
                <div style="font-size: 0.9rem;">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo date('d/m/Y H:i', strtotime($first_order['date'])); ?>
                </div>
            </div>

            <!-- Content -->
            <div class="p-3">
                <!-- Trạng thái đơn hàng -->
                <div class="text-center mb-4">
                    <?php 
                    $status = $first_order['status'];
                    switch($status) {
                        case "NULL":
                        case "":
                            echo '<span class="status-badge bg-secondary"><i class="fas fa-clock me-2"></i>Chờ xác nhận</span>';
                            break;
                        case "preparing":
                            echo '<span class="status-badge bg-info"><i class="fas fa-hourglass-half me-2"></i>Đang chuẩn bị</span>';
                            break;
                        case "prepared":
                            echo '<span class="status-badge bg-primary"><i class="fas fa-check me-2"></i>Đã chuẩn bị</span>';
                            break;
                        case "in process":
                            echo '<span class="status-badge bg-warning"><i class="fas fa-motorcycle me-2"></i>Đang giao</span>';
                            break;
                        case "closed":
                            echo '<span class="status-badge bg-success"><i class="fas fa-check-circle me-2"></i>Đã giao</span>';
                            break;
                        case "rejected":
                            echo '<span class="status-badge bg-danger"><i class="fas fa-times-circle me-2"></i>Đã hủy</span>';
                            break;
                    }
                    ?>
                </div>

                <!-- Thông tin khách hàng -->
                <h5 class="section-title">
                    <i class="fas fa-user me-2"></i>Thông tin khách hàng
                </h5>
                <div class="info-row d-flex">
                    <span class="info-label">Tên khách hàng:</span>
                    <span class="info-value"><?php echo htmlspecialchars($first_order['username']); ?></span>
                </div>
                <div class="info-row d-flex">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($first_order['email']); ?></span>
                </div>
                <div class="info-row d-flex">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value"><?php echo htmlspecialchars($first_order['phone']); ?></span>
                </div>
                <div class="info-row d-flex">
                    <span class="info-label">Địa chỉ giao hàng:</span>
                    <span class="info-value"><?php echo htmlspecialchars($first_order['address']); ?></span>
                </div>

                <!-- Thông tin nhà hàng -->
                <h5 class="section-title">
                    <i class="fas fa-store me-2"></i>Thông tin nhà hàng
                </h5>
                <div class="info-row d-flex">
                    <span class="info-label">Tên nhà hàng:</span>
                    <span class="info-value"><?php echo htmlspecialchars($first_order['restaurant_name']); ?></span>
                </div>
                <div class="info-row d-flex">
                    <span class="info-label">Địa chỉ:</span>
                    <span class="info-value"><?php echo htmlspecialchars($first_order['restaurant_address']); ?></span>
                </div>

                <!-- Chi tiết món ăn -->
                <h5 class="section-title">
                    <i class="fas fa-shopping-bag me-2"></i>Chi tiết món ăn
                </h5>
                <?php foreach($temp_orders as $order) { ?>
                <div class="item-card">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0"><?php echo htmlspecialchars($order['title']); ?></h6>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="badge bg-light text-dark">x<?php echo $order['quantity']; ?></span>
                        </div>
                        <div class="col-md-2">
                            <?php echo number_format($order['price'], 0, ',', '.'); ?> VNĐ
                        </div>
                        <div class="col-md-2 text-end">
                            <strong class="text-primary">
                                <?php echo number_format($order['price'] * $order['quantity'], 0, ',', '.'); ?> VNĐ
                            </strong>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Tổng tiền -->
                <div class="total-section">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">Tổng cộng:</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="total-amount">
                                <?php echo number_format($total_amount, 0, ',', '.'); ?> VNĐ
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-3 no-print">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="<?php echo !empty($_SESSION['user_id']) ? 'your_orders.php' : 'index.php'; ?>" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if($status == "rejected") { ?>
                                <a href="order_detail.php?action=delete&code=<?php echo $order_code; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?');" 
                                   class="btn-delete">
                                    <i class="fas fa-trash me-2"></i>Xóa
                                </a>
                            <?php } elseif($status != "closed" && $status != "rejected") { ?>
                                <a href="order_detail.php?action=cancel&code=<?php echo $order_code; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?');" 
                                   class="btn-cancel">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                            <?php } ?>
                            <button onclick="window.print()" class="btn-print">
                                <i class="fas fa-print me-2"></i>In
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        <div class="alert alert-info no-print" role="alert" style="font-size: 0.9rem;">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Lưu ý:</strong> Lưu mã đơn hàng <strong><?php echo $order_code; ?></strong> để tra cứu sau.
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</body>
</html>
