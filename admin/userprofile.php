
                <?php

include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

$order_id = isset($_GET['newform_id']) ? intval($_GET['newform_id']) : 0;
if($order_id <= 0) {
    echo "<script>alert('ID đơn hàng không hợp lệ'); window.close();</script>";
    exit();
}

$ret1 = mysqli_query($db, "SELECT * FROM users_orders WHERE o_id='$order_id'");
$ro = mysqli_fetch_array($ret1);
$ret2 = mysqli_query($db, "SELECT * FROM users WHERE u_id='".$ro['u_id']."'");
$user = mysqli_fetch_array($ret2);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 600px;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .profile-icon {
            font-size: 2.5rem;
            color: #4e73df;
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
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .btn-close {
            float: right;
        }
    </style>
</head>
<body>
    <div class="container animate-fade-in">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card mt-5">
                    <div class="card-header text-white d-flex align-items-center">
                        <i class="fas fa-user-circle profile-icon me-3"></i>
                        <h4 class="mb-0">Thông tin khách hàng</h4>
                        <button type="button" class="btn-close btn-close-white ms-auto" onclick="window.close()"></button>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-row">
                            <span class="label-text">Họ tên:</span>
                            <span class="value-text ms-2"><?php echo htmlentities($user['f_name'] . ' ' . $user['l_name']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label-text">Tên đăng nhập:</span>
                            <span class="value-text ms-2"><?php echo htmlentities($user['username']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label-text">Email:</span>
                            <span class="value-text ms-2"><?php echo htmlentities($user['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label-text">Số điện thoại:</span>
                            <span class="value-text ms-2"><?php echo htmlentities($user['phone']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label-text">Ngày đăng ký:</span>
                            <span class="value-text ms-2"><?php echo date('d/m/Y', strtotime($user['date'])); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label-text">Trạng thái:</span>
                            <?php if($user['status']==1) { ?>
                                <span class="badge bg-primary status-badge ms-2">Hoạt động</span>
                            <?php } else { ?>
                                <span class="badge bg-danger status-badge ms-2">Đã khóa</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>