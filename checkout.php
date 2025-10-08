<?php
// filepath: c:\study\foodonline\checkout.php
include("connection/connect.php");
include_once 'product-action.php';
error_reporting(0);
session_start();

function function_alert() { 
    echo "<script>alert('Thank you. Your Order has been placed!');</script>"; 
    echo "<script>window.location.replace('your_orders.php');</script>"; 
} 

$item_total = 0;
if(empty($_SESSION["user_id"])) {
    header('location:login.php');
    exit();
} else {
    foreach ($_SESSION["cart_item"] as $item) {
        $item_total += ($item["price"] * $item["quantity"]);
        if(isset($_POST['submit'])) {
            $dish = mysqli_fetch_assoc(mysqli_query($db, "SELECT rs_id FROM dishes WHERE title = '".$item["title"]."' LIMIT 1"));
            $rs_id = $dish['rs_id'];
            $delivery_address = mysqli_real_escape_string($db, $_POST['delivery_address']);
            $SQL = "INSERT INTO users_orders(u_id, title, quantity, price, rs_id, address, date) 
                    VALUES ('".$_SESSION["user_id"]."', '".$item["title"]."', '".$item["quantity"]."', '".$item["price"]."', '".$rs_id."', '".$delivery_address."', NOW())";
            mysqli_query($db, $SQL);
        }
    }
    if(isset($_POST['submit'])) {
        unset($_SESSION["cart_item"]);
        function_alert();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Thanh toán | FoodOnline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }
        .main-content {
            flex: 1 0 auto;
        }
        footer {
            margin-top: auto;
        }
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
        }
        .navbar-brand img {
            height: 40px;
        }
        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .progress-steps {
            padding: 2rem 0 1rem 0;
            background: #f8f9fa;
        }
        .step-item {
            text-align: center;
            position: relative;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .step-item.active .step-number {
            background: var(--secondary-color);
        }
        .checkout-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(253,77,64,0.10);
            padding: 48px 36px;
            max-width: 800px; /* tăng chiều rộng */
            margin: 40px auto;
        }
        .checkout-card h3 {
            color: #1976d2;
            font-weight: 700;
            font-size: 2rem;
        }
        .table th {
            background: linear-gradient(90deg,#fd4d40,#ff9b44);
            color: #fff;
            border:none;
            font-weight:600;
        }
        .table td {
            background:#fff;
            border:none;
        }
        .btn-gradient {
            background: linear-gradient(90deg,#fd4d40,#ff9b44);
            color:#fff;
            border:none;
            border-radius:8px;
            font-size:1.15rem;
            font-weight:600;
            box-shadow:0 2px 8px rgba(253,77,64,0.12);
        }
        .btn-gradient:hover {
            background: #fd4d40;
            color: #fff;
        }
        .payment-option label {
            font-weight: 500;
            font-size: 1rem;
        }
        #qrPaymentSection {
            background: #fff3cd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php">Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Giỏ hàng</a>
                    </li>
                    <?php if(empty($_SESSION["user_id"])) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registration.php">Đăng ký</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="your_orders.php">Đơn hàng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Đăng xuất</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Progress Steps -->
    <div class="progress-steps">
        <div class="container">
            <div class="row">
                <div class="col-md-4 step-item">
                    <div class="step-number">1</div>
                    <h6>Lựa chọn nhà hàng</h6>
                </div>
                <div class="col-md-4 step-item">
                    <div class="step-number">2</div>
                    <h6>Chọn món</h6>
                </div>
                <div class="col-md-4 step-item active">
                    <div class="step-number">3</div>
                    <h6>Xác nhận & Thanh toán</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="checkout-card">
            <h3 class="mb-4 text-center" style="color:#1976d2; font-weight:700; font-size:2rem;">
                <i class="fas fa-credit-card me-2"></i>Thông tin thanh toán
            </h3>
            <form action="" method="post">
                <div class="mb-4">
                    <label for="delivery_address" class="form-label fw-bold">
                        <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng
                    </label>
                    <input type="text" class="form-control" id="delivery_address" name="delivery_address"
                           placeholder="Nhập địa chỉ nhận hàng của bạn" required>
                </div>
                <div class="mb-4 px-2">
                    <div style="background: linear-gradient(90deg,#fd4d40,#ff9b44); border-radius: 14px; padding: 24px 32px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-white fw-semibold" style="font-size:1.15rem;">
                                <i class="fas fa-shopping-basket me-2"></i>Tổng tiền hàng
                            </div>
                            <div class="text-white fw-semibold" style="font-size:1.15rem;">
                                <?php echo number_format($item_total, 0, ',', '.') . " VND"; ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-white fw-semibold" style="font-size:1.15rem;">
                                <i class="fas fa-truck me-2"></i>Phí vận chuyển
                            </div>
                            <div class="text-white fw-semibold" style="font-size:1.15rem;">
                                Miễn phí
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-white fw-bold" style="font-size:1.2rem;">
                                <i class="fas fa-money-bill-wave me-2"></i>Tổng
                            </div>
                            <div class="text-white fw-bold" style="font-size:1.2rem;">
                                <?php echo number_format($item_total, 0, ',', '.') . " VND"; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="payment-option mb-4">
                    <div class="d-flex flex-column gap-2">
                        <label class="form-check d-flex align-items-center gap-2">
                            <input name="mod" id="radioStacked1" checked value="COD" type="radio" class="form-check-input"> 
                            <span class="ms-1 fw-semibold">Thanh toán khi nhận hàng</span>
                        </label>
                        <label class="form-check d-flex align-items-center gap-2">
                            <input name="mod" type="radio" value="qr" class="form-check-input" id="qrRadio"> 
                            <span class="ms-1 fw-semibold">Thanh toán bằng QR Code</span>
                        </label>
                    </div>
                    <div id="qrPaymentSection" style="display: none; text-align: center;">
                        <p class="mt-3 mb-2">Quét mã QR để thanh toán:</p>
                        <div id="qrImageContainer">
                            <img id="qrCodeImage" src="" alt="QR Code for Payment" width="180" style="border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                        </div>
                        <p class="mt-2">Số tiền thanh toán: <strong><?php echo number_format($item_total, 0, ',', '.'); ?> VND</strong></p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <input type="submit" onclick="return confirm('Xác nhận thanh toán?');" name="submit"
                           class="btn btn-gradient btn-lg px-5 py-2"
                           style="background: linear-gradient(90deg,#fd4d40,#ff9b44); color:#fff; border:none; border-radius:12px; font-size:1.2rem; font-weight:700; box-shadow:0 2px 8px rgba(253,77,64,0.12); transition:0.3s;"
                           value="Thanh toán">
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include "include/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrRadio = document.getElementById('qrRadio');
        const codRadio = document.getElementById('radioStacked1');
        const qrPaymentSection = document.getElementById('qrPaymentSection');
        const qrCodeImage = document.getElementById('qrCodeImage');

        function toggleQRSection() {
            if (qrRadio.checked) {
                const totalAmount = "<?php echo $item_total; ?>";
                fetch(`generate_qr.php?total=${totalAmount}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.qr_path) {
                            qrCodeImage.src = data.qr_path;
                            qrPaymentSection.style.display = 'block';
                        } else {
                            console.error("Lỗi tạo QR:", data.error);
                        }
                    })
                    .catch(error => console.error("Lỗi khi fetch QR:", error));
            } else {
                qrPaymentSection.style.display = 'none';
            }
        }

        qrRadio.addEventListener('change', toggleQRSection);
        codRadio.addEventListener('change', toggleQRSection);
        toggleQRSection();
    });
    </script>
</body>
</html>