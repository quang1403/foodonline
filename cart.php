<?php
// filepath: c:\study\foodonline\cart.php
include("connection/connect.php");
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <!-- Chatbox CSS -->
    <link rel="stylesheet" href="food-chatbox/assets/css/chatbox.css">
    <style>
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
            background: linear-gradient(135deg, #fd4d40, #ff9b44);
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
        .cart-table th, .cart-table td {
            vertical-align: middle;
        }
        .cart-table th {
            background: linear-gradient(135deg, #fd4d40, #ff9b44);
            color: white;
        }
        .btn-success {
            background: linear-gradient(135deg, #fd4d40, #ff9b44);
            border: none;
        }
        .btn-success:hover {
            background: #fd4d40;
        }
    </style>
</head>
<body>
     <!-- Hidden field for user ID -->
    <?php if(!empty($_SESSION["user_id"])) { ?>
        <input type="hidden" id="chat-user-id" value="<?php echo $_SESSION['user_id']; ?>">
    <?php } ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Logo" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-2"></i>Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php"><i class="fas fa-store me-2"></i>Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng
                            <?php 
                            $cart_count = 0;
                            if(isset($_SESSION["cart_item"])) {
                                foreach($_SESSION["cart_item"] as $item) {
                                    $cart_count += $item["quantity"];
                                }
                            }
                            if($cart_count > 0): ?>
                                <span class="badge bg-warning text-dark ms-1"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if(empty($_SESSION["user_id"])) { ?>
                        <a href="login.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </a>
                        <a href="registration.php" class="btn btn-light">
                            <i class="fas fa-user-plus me-2"></i>Đăng ký
                        </a>
                    <?php } else { ?>
                        <div class="dropdown">
                            <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : 'Tài khoản'; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="your_orders.php">
                                        <i class="fas fa-list me-2"></i>Đơn hàng của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="userprofile.php">
                                        <i class="fas fa-user-circle me-2"></i>Thông tin cá nhân
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-5">
            <h2 class="mb-4 text-center">Giỏ hàng của bạn</h2>
            <?php
            // Xóa món khỏi giỏ hàng
            if(isset($_GET["action"]) && $_GET["action"] == "remove" && isset($_GET["d_id"])) {
                foreach($_SESSION["cart_item"] as $k => $v) {
                    if($_GET["d_id"] == $v["d_id"]) {
                        unset($_SESSION["cart_item"][$k]);
                    }
                }
                if(empty($_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"]);
                }
                header("Location: cart.php");
                exit();
            }
            ?>
            <?php if(!empty($_SESSION["cart_item"])): ?>
            <div class="table-responsive">
                <table class="table table-bordered cart-table">
                    <thead>
                        <tr>
                            <th>Món ăn</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach($_SESSION["cart_item"] as $item):
                            $item_total = $item["price"] * $item["quantity"];
                            $total += $item_total;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item["title"]); ?></td>
                            <td><?php echo $item["quantity"]; ?></td>
                            <td><?php echo number_format($item["price"], 0, ',', '.'); ?> VNĐ</td>
                            <td><?php echo number_format($item_total, 0, ',', '.'); ?> VNĐ</td>
                            <td>
                                <a href="cart.php?action=remove&d_id=<?php echo $item["d_id"]; ?>" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                            <td colspan="2" class="fw-bold"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <a href="restaurants.php" class="btn btn-secondary btn-lg me-2">
                    <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua hàng
                </a>
                <a href="checkout.php" class="btn btn-success btn-lg">
                    <i class="fas fa-credit-card me-2"></i>Thanh toán
                </a>
            </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h5>Giỏ hàng của bạn đang trống.</h5>
                    <p>Hãy thêm những món ăn yêu thích!</p>
                    <a href="restaurants.php" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-utensils me-2"></i>Khám phá món ăn
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include "include/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chatbox JS -->
    <script src="food-chatbox/assets/js/chatbox.js"></script>
</body>
</html>