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

// Thêm code để lấy thông tin nhà hàng từ giỏ hàng
$restaurant_id = null;
if(!empty($_SESSION["cart_item"])) {
    foreach($_SESSION["cart_item"] as $item) {
        $dish_query = mysqli_query($db, "SELECT rs_id FROM dishes WHERE title = '".$item["title"]."' LIMIT 1");
        if($dish_query && mysqli_num_rows($dish_query) > 0) {
            $dish = mysqli_fetch_assoc($dish_query);
            $restaurant_id = $dish['rs_id'];
            break;
        }
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

        /* ...existing styles... */
        
        .back-button {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-button:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-top: 2rem;
        }
        
        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .breadcrumb-nav {
            background: rgba(253, 77, 64, 0.1);
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 2rem;
        }
        
        .breadcrumb {
            margin: 0;
            background: none;
            padding: 0;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "→";
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .checkout-header {
                flex-direction: column;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column-reverse;
                gap: 15px;
            }
            
            .back-button,
            .btn-gradient {
                width: 100%;
                justify-content: center;
            }
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
            <!-- Breadcrumb Navigation -->
            <nav class="breadcrumb-nav">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="restaurants.php">Nhà hàng</a>
                    </li>
                    <?php if($restaurant_id) { ?>
                    <li class="breadcrumb-item">
                        <a href="dishes.php?res_id=<?php echo $restaurant_id; ?>">Chọn món</a>
                    </li>
                    <?php } ?>
                    <li class="breadcrumb-item active">Thanh toán</li>
                </ol>
            </nav>
            
            <!-- Header với nút quay lại -->
            <div class="checkout-header">
                <h3 style="color:#1976d2; font-weight:700; font-size:2rem; margin:0;">
                    <i class="fas fa-credit-card me-2"></i>Thông tin thanh toán
                </h3>
                
                <?php if($restaurant_id) { ?>
                <a href="dishes.php?res_id=<?php echo $restaurant_id; ?>" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại chọn món
                </a>
                <?php } else { ?>
                <a href="restaurants.php" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
                <?php } ?>
            </div>

            <!-- Order Summary Card -->
            <div class="card mb-4" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 16px;">
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 16px 16px 0 0; border: none;">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>
                        Đơn hàng của bạn (<?php echo count($_SESSION["cart_item"]); ?> món)
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    if(!empty($_SESSION["cart_item"])) {
                        foreach($_SESSION["cart_item"] as $item) {
                            // Lấy thông tin món ăn từ database để có đường dẫn ảnh chính xác
                            $dish_query = mysqli_query($db, "SELECT img FROM dishes WHERE title = '".mysqli_real_escape_string($db, $item["title"])."' LIMIT 1");
                            $dish_img = '';
                            if($dish_query && mysqli_num_rows($dish_query) > 0) {
                                $dish_data = mysqli_fetch_assoc($dish_query);
                                $dish_img = $dish_data['img'];
                            } else {
                                $dish_img = 'default-food.jpg'; // ảnh mặc định nếu không tìm thấy
                            }
                            
                            echo '<div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid #f1f1f1;">
                                <div class="d-flex align-items-center">
                                    <div class="food-image-wrapper me-3" style="width: 60px; height: 60px; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
                            
                            // Kiểm tra xem file ảnh có tồn tại không
                            $image_path = "admin/Res_img/dishes/" . $dish_img;
                            if($dish_img && file_exists($image_path)) {
                                echo '<img src="'.$image_path.'" alt="'.htmlspecialchars($item["title"]).'" 
                                         style="width: 100%; height: 100%; object-fit: cover;">';
                            } else {
                                // Hiển thị icon mặc định nếu không có ảnh
                                echo '<div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f9fa, #e9ecef); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-utensils text-muted" style="font-size: 20px;"></i>
                                      </div>';
                            }
                            
                            echo '    </div>
                                    <div>
                                        <div class="fw-semibold mb-1" style="font-size: 1.1rem;">'.htmlspecialchars($item["title"]).'</div>
                                        <div class="d-flex align-items-center gap-3">
                                            <small class="text-muted">
                                                <i class="fas fa-sort-numeric-up me-1"></i>
                                                Số lượng: <span class="fw-medium">'.$item["quantity"].'</span>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-money-bill me-1"></i>
                                                Đơn giá: <span class="fw-medium">'.number_format($item["price"], 0, ',', '.').'₫</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary" style="font-size: 1.1rem;">
                                        '.number_format($item["price"] * $item["quantity"], 0, ',', '.').'₫
                                    </div>
                                    <small class="text-muted">Thành tiền</small>
                                </div>
                            </div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <form action="" method="post">
                <div class="mb-4">
                    <label for="delivery_address" class="form-label fw-bold">
                        <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng
                    </label>
                    <input type="text" class="form-control" id="delivery_address" name="delivery_address"
                           placeholder="Nhập địa chỉ nhận hàng của bạn" required
                           style="border-radius: 12px; padding: 12px 16px; border: 2px solid #e9ecef;">
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
                    <h6 class="mb-3">
                        <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
                    </h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="form-check p-3" style="border: 2px solid #e9ecef; border-radius: 12px; transition: all 0.3s ease;">
                            <input class="form-check-input" type="radio" name="mod" id="radioStacked1" value="COD" checked>
                            <label class="form-check-label fw-semibold ms-2" for="radioStacked1">
                                <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check p-3" style="border: 2px solid #e9ecef; border-radius: 12px; transition: all 0.3s ease;">
                            <input class="form-check-input" type="radio" name="mod" id="qrRadio" value="qr">
                            <label class="form-check-label fw-semibold ms-2" for="qrRadio">
                                <i class="fas fa-qrcode me-2 text-primary"></i>
                                Thanh toán bằng QR Code
                            </label>
                        </div>
                    </div>
                    
                    <div id="qrPaymentSection" style="display: none; text-align: center;">
                        <p class="mt-3 mb-2">Quét mã QR để thanh toán:</p>
                        <div id="qrImageContainer">
                            <img id="qrCodeImage" src="" alt="QR Code for Payment" width="180" 
                                 style="border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                        </div>
                        <p class="mt-2">Số tiền thanh toán: <strong><?php echo number_format($item_total, 0, ',', '.'); ?> VND</strong></p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if($restaurant_id) { ?>
                    <a href="dishes.php?res_id=<?php echo $restaurant_id; ?>" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại chọn món
                    </a>
                    <?php } else { ?>
                    <a href="cart.php" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Xem giỏ hàng
                    </a>
                    <?php } ?>
                    
                    <input type="submit" onclick="return confirm('Xác nhận thanh toán?');" name="submit"
                           class="btn btn-gradient btn-lg px-5 py-3"
                           style="background: linear-gradient(90deg,#fd4d40,#ff9b44); color:#fff; border:none; border-radius:12px; font-size:1.2rem; font-weight:700; box-shadow:0 4px 16px rgba(253,77,64,0.3); transition:0.3s;"
                           value="Xác nhận thanh toán">
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

        // Thêm hiệu ứng cho payment option selection
        const paymentOptions = document.querySelectorAll('.form-check');
        paymentOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            
            option.addEventListener('click', function() {
                // Reset all options
                paymentOptions.forEach(opt => {
                    opt.style.borderColor = '#e9ecef';
                    opt.style.backgroundColor = '#fff';
                });
                
                // Highlight selected option
                if(radio.checked) {
                    option.style.borderColor = 'var(--primary-color)';
                    option.style.backgroundColor = 'rgba(253, 77, 64, 0.05)';
                }
            });
            
            radio.addEventListener('change', function() {
                // Reset all options
                paymentOptions.forEach(opt => {
                    opt.style.borderColor = '#e9ecef';
                    opt.style.backgroundColor = '#fff';
                });
                
                // Highlight selected option
                if(this.checked) {
                    option.style.borderColor = 'var(--primary-color)';
                    option.style.backgroundColor = 'rgba(253, 77, 64, 0.05)';
                }
            });
        });

        // Initialize selected option
        const checkedRadio = document.querySelector('input[name="mod"]:checked');
        if(checkedRadio) {
            const selectedOption = checkedRadio.closest('.form-check');
            selectedOption.style.borderColor = 'var(--primary-color)';
            selectedOption.style.backgroundColor = 'rgba(253, 77, 64, 0.05)';
        }

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