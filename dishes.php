<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Include cart functionality 
include_once 'product-action.php';

// Check if restaurant ID is provided
if(!isset($_GET['res_id']) || !is_numeric($_GET['res_id'])) {
    header('location: restaurants.php');
    exit();
}

// Get restaurant details
$stmt = mysqli_prepare($db, "SELECT * FROM restaurant WHERE rs_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_GET['res_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$restaurant = mysqli_fetch_array($result);

if(!$restaurant) {
    header('location: restaurants.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu nhà hàng</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #fd4d40;
            --secondary-color: #ff9b44;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .step-item {
            position: relative;
            padding: 1rem;
            text-align: center;
        }

        .step-number {
            width: 35px;
            height: 35px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .step-item.active .step-number {
            background: var(--secondary-color);
        }

        .restaurant-banner {
            background-size: cover;
            background-position: center;
            padding: 3rem 0;
            position: relative;
        }

        .restaurant-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
        }

        .restaurant-info {
            position: relative;
            color: white;
        }

        .menu-item {
            transition: all 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .cart-sidebar {
            position: sticky;
            top: 1rem;
        }

        .quantity-control {
            max-width: 80px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" height="40" alt="Logo">
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
                        <a class="nav-link active" href="restaurants.php">Nhà hàng</a>
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
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4 step-item">
                <div class="step-number">1</div>
                <h6>Chọn nhà hàng</h6>
            </div>
            <div class="col-md-4 step-item active">
                <div class="step-number">2</div>
                <h6>Chọn món ăn</h6>
            </div>
            <div class="col-md-4 step-item">
                <div class="step-number">3</div>
                <h6>Thanh toán</h6>
            </div>
        </div>
    </div>

    <!-- Restaurant Banner -->
    <div class="restaurant-banner mb-4" 
         style="background-image: url('images/img/restrrr.png')">
        <div class="container">
            <div class="restaurant-info">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="admin/Res_img/<?php echo $restaurant['image']; ?>" 
                             class="img-fluid rounded" alt="Restaurant logo">
                    </div>
                    <div class="col-md-10">
                        <h2><?php echo $restaurant['title']; ?></h2>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo $restaurant['address']; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Content -->
    <div class="container mb-5">
        <div class="row">
            <!-- Menu Items -->
            <div class="col-md-8">
                <div class="row g-4">
                    <?php  
                    $stmt = $db->prepare("SELECT * FROM dishes WHERE rs_id=?");
                    $stmt->bind_param("i", $_GET['res_id']);
                    $stmt->execute();
                    $products = $stmt->get_result();
                    
                    if($products->num_rows > 0) {
                        while($product = $products->fetch_assoc()) {
                    ?>
                    <div class="col-md-6">
                        <div class="menu-item">
                            <img src="admin/Res_img/dishes/<?php echo $product['img']; ?>" 
                                 alt="<?php echo $product['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['title']; ?></h5>
                                <p class="card-text"><?php echo $product['slogan']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ
                                    </h6>
                                    <form method="post" class="d-flex align-items-center gap-2"
                                          action="dishes.php?res_id=<?php echo $_GET['res_id']; ?>&action=add&id=<?php echo $product['d_id']; ?>">
                                        <input type="number" name="quantity" value="1" min="1"
                                               class="form-control quantity-control">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        }
                    } else {
                        echo '<div class="col-12">
                                <div class="alert alert-info">
                                    Chưa có món ăn nào.
                                </div>
                              </div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Cart Sidebar -->
            <div class="col-md-4">
                <div class="cart-sidebar card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $item_total = 0;
                        if(isset($_SESSION["cart_item"])) {
                            foreach($_SESSION["cart_item"] as $item) {
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0"><?php echo $item["title"]; ?></h6>
                                <small>
                                    <?php echo number_format($item["price"], 0, ',', '.'); ?> VNĐ 
                                    x <?php echo $item["quantity"]; ?>
                                </small>
                            </div>
                            <div>
                                <a href="dishes.php?res_id=<?php echo $_GET['res_id']; ?>&action=remove&id=<?php echo $item["d_id"]; ?>" 
                                   class="text-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php
                                $item_total += ($item["price"] * $item["quantity"]);
                            }
                        }
                        ?>
                    </div>
                    <div class="card-footer">
                        <div class="text-center">
                            <h5 class="mb-3">
                                Tổng cộng: <?php echo number_format($item_total, 0, ',', '.'); ?> VNĐ
                            </h5>
                            <a href="checkout.php?res_id=<?php echo $_GET['res_id']; ?>&action=check" 
                               class="btn btn-primary w-100 <?php echo ($item_total == 0) ? 'disabled' : ''; ?>">
                                <i class="fas fa-check me-2"></i>Thanh toán
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "include/footer.php" ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>