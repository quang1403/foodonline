<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");  
error_reporting(0);  
session_start(); 
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DelishHub-Đồ ăn nhanh HQ</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #fd4d40;
            --secondary-color: #ff9b44;
            --card-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
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

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('images/img/banner.png');
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            color: white;
        }

        .search-form input {
            border-radius: 20px 0 0 20px;
            border: none;
        }

        .search-form button {
            border-radius: 0 20px 20px 0;
            border: none;
        }

        .progress-steps {
            background: #f8f9fa;
            padding: 2rem 0;
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
        }

        .food-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .food-image {
            height: 200px;
            object-fit: cover;
        }

        /* Restaurant Card Modern Styles */
.restaurant-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.restaurant-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: var(--card-hover-shadow);
}

.restaurant-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.restaurant-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.restaurant-card-modern:hover .restaurant-image {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.restaurant-card-modern:hover .image-overlay {
    opacity: 1;
}

.card-content {
    padding: 1.5rem;
}

.restaurant-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.restaurant-address {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.restaurant-stats {
    display: flex;
    justify-content: center; /* Thay đổi từ space-between thành center */
    padding: 1rem 0;
    border-top: 1px solid #f1f3f4;
    border-bottom: 1px solid #f1f3f4;
    margin-bottom: 1rem;
    gap: 1.25rem; /* Khoảng cách giữa các stat items */
    flex-wrap: wrap; /* Cho phép xuống hàng trên màn hình nhỏ */
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
    background: #fff;
    padding: 8px 12px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.restaurant-actions {
    margin-top: 1rem;
}

.btn-order {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    padding: 0.8rem;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(253, 77, 64, 0.3);
}

/* Section Header Styles */
.section-header {
    position: relative;
}

.bg-primary-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
}

.divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
}

/* Modern Food Card */
.food-card-modern {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 1px solid rgba(0,0,0,0.05);
}

.food-card-modern:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

/* Rank Badge */
.rank-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 10;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
.rank-2 { background: linear-gradient(135deg, #C0C0C0, #A9A9A9); }
.rank-3 { background: linear-gradient(135deg, #CD7F32, #B8860B); }
.rank-badge:not(.rank-1):not(.rank-2):not(.rank-3) {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

/* Image Container */
.food-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.food-image-modern {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.food-card-modern:hover .food-image-modern {
    transform: scale(1.1);
}

/* Image Overlay */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0.7));
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 20px;
}

.food-card-modern:hover .image-overlay {
    opacity: 1;
}

/* Sales Badge */
.sales-badge {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.sales-count {
    font-weight: 700;
    color: var(--primary-color);
}

/* Card Content */
.card-content-modern {
    padding: 25px;
}

.restaurant-info {
    margin-bottom: 12px;
}

.restaurant-badge {
    display: inline-block;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #6c757d;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

.food-title {
    font-size: 18px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.food-description {
    color: #718096;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Food Stats */
.food-stats {
    display: flex;
    justify-content: center; /* Thay đổi từ space-between thành center */
    padding: 1rem 0;
    border-top: 1px solid #f1f3f4;
    border-bottom: 1px solid #f1f3f4;
    margin-bottom: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
}

/* Price and Action Row */
.price-action-row {
    display: block;
    margin-top: 1rem;
}

.price-action-container {
    margin-top: 1rem;
}

.price-section {
    text-align: right;
    margin-bottom: 1rem;
}

.current-price {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary-color);
}

.order-section {
    width: 100%;
}

.btn-order {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    padding: 0.8rem;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(253, 77, 64, 0.3);
}

/* Xóa các CSS không cần thiết */
.original-price,
.action-buttons,
.add-to-cart {
    display: none;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 24px;
    color: #6c757d;
}

/* Featured Restaurants Section */
.featured-restaurants {
    background-color: #f8f9fa;
    padding: 60px 0;
}

.restaurant-card-modern {
    transition: transform 0.3s;
}

.restaurant-card-modern:hover {
    transform: translateY(-5px);
}

.restaurant-image-container {
    position: relative;
    height: 180px;
    overflow: hidden;
    border-radius: 15px 15px 0 0;
}

.restaurant-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.restaurant-card-modern:hover .restaurant-image {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    opacity: 0;
    transition: opacity 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.restaurant-card-modern:hover .image-overlay {
    opacity: 1;
}

.btn-light {
    background-color: rgba(255, 255, 255, 0.8);
    color: #333;
    transition: background-color 0.3s, color 0.3s;
}

.btn-light:hover {
    background-color: var(--primary-color);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .food-card-modern {
        margin-bottom: 20px;
    }
    
    .card-content-modern {
        padding: 20px;
    }
    
    .food-title {
        font-size: 16px;
    }
    
    .price-action-row {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.food-card-modern {
    animation: fadeInUp 0.6s ease-out;
}

/* Hover Effects */
.quick-view:hover {
    transform: scale(1.05);
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}
    </style>
    <!-- Chatbox CSS -->
    <link rel="stylesheet" href="food-chatbox/assets/css/chatbox.css">
</head>

<body>
    <!-- Hidden field for user ID -->
    <?php if(!empty($_SESSION["user_id"])) { ?>
        <input type="hidden" id="chat-user-id" value="<?php echo $_SESSION['user_id']; ?>">
        <!-- Debug: Session user_id = <?php echo $_SESSION['user_id']; ?> -->
    <?php } else { ?>
        <!-- Debug: No user_id in session -->
    <?php } ?>
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php">
                            <i class="fas fa-utensils me-1"></i>Nhà hàng
                        </a>
                    </li>
                    <?php if(empty($_SESSION["user_id"])) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registration.php">
                                <i class="fas fa-user-plus me-1"></i>Đăng ký
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="your_orders.php">
                                <i class="fas fa-receipt me-1"></i>Đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                            </a>
                        </li>
                    <?php } ?>
                </ul>

                <?php if(!empty($_SESSION["user_id"])) { ?>
                <div class="d-flex align-items-center">
                    <!-- Add cart icon with count -->
                    <a href="cart.php" class="nav-link text-white me-3 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        if(!empty($_SESSION["cart_item"])) {
                            $cart_count = count(array_keys($_SESSION["cart_item"]));
                        ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php } ?>
                    </a>
                    <div class="dropdown">
                        <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo $_SESSION["username"]; ?>
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
                            <li>
                                <a class="dropdown-item" href="manage_addresses.php">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng
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
                </div>
                <?php } ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="mb-4">Hương vị tuyệt vời, giao hàng thần tốc!</h1>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form class="d-flex search-form" id="searchForm">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Tìm kiếm món ăn hoặc nhà hàng...">
                            <select class="form-select" id="searchType" style="max-width: 130px;">
                                <option value="all">Tất cả</option>
                                <option value="restaurant">Nhà hàng</option>
                                <option value="dish">Món ăn</option>
                            </select>
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Foods Section -->
    <section class="py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <div class="section-header">
                        <span class="badge bg-primary-gradient px-4 py-2 rounded-pill mb-3">
                            <i class="fas fa-fire me-2"></i>HOT TREND
                        </span>
                        <h2 class="display-5 fw-bold mb-3">Món ăn bán chạy nhất</h2>
                        <p class="lead text-muted">Khám phá những món ăn được yêu thích nhất</p>
                        <div class="divider mx-auto"></div>
                    </div>
                </div>
            </div>

            <!-- Food Grid -->
            <div class="row g-4">
                <?php 
                // Giữ nguyên code PHP logic gốc
                $popular_foods = mysqli_query($db,"
                    SELECT 
                        d.*,
                        r.title as restaurant_name,
                        COUNT(uo.o_id) as order_count,
                        SUM(uo.quantity) as total_quantity
                    FROM dishes d
                    INNER JOIN restaurant r ON d.rs_id = r.rs_id
                    INNER JOIN users_orders uo ON uo.title = d.title
                    WHERE uo.status = 'closed'
                    GROUP BY d.d_id, d.title, r.title, d.slogan, d.price, d.img, d.rs_id
                    ORDER BY total_quantity DESC
                    LIMIT 6
                ");

                if(mysqli_num_rows($popular_foods) > 0) {
                    $rank = 1;
                    while($food = mysqli_fetch_array($popular_foods)) {
                ?>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="food-card-modern h-100" data-aos="fade-up" data-aos-delay="<?php echo $rank * 100; ?>">
                            <!-- Rank Badge -->
                            <div class="rank-badge rank-<?php echo $rank; ?>">
                                <span class="rank-number">#<?php echo $rank; ?></span>
                            </div>
                            
                            <!-- Image Container -->
                            <div class="food-image-container">
                                <img src="admin/Res_img/dishes/<?php echo htmlspecialchars($food['img']); ?>" 
                                     class="food-image-modern" 
                                     alt="<?php echo htmlspecialchars($food['title']); ?>">
                                
                                <!-- Overlay -->
                                <div class="image-overlay">
                                    <div class="overlay-content">
                                        <button class="btn btn-light btn-sm rounded-pill quick-view" 
                                                data-food-id="<?php echo $food['d_id']; ?>"
                                                data-restaurant-id="<?php echo $food['rs_id']; ?>">
                                            <i class="fas fa-eye me-1"></i>Xem nhanh
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Sales Badge -->
                                <div class="sales-badge">
                                    <i class="fas fa-chart-line me-1"></i>
                                    <span class="sales-count"><?php echo number_format($food['total_quantity']); ?></span>
                                    <small>đã bán</small>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="card-content-modern">
                                <!-- Restaurant Info -->
                                <div class="restaurant-info">
                                    <span class="restaurant-badge">
                                        <i class="fas fa-store me-1"></i>
                                        <?php echo htmlspecialchars($food['restaurant_name']); ?>
                                    </span>
                                </div>

                                <!-- Food Title -->
                                <h5 class="food-title"><?php echo htmlspecialchars($food['title']); ?></h5>
                                
                                <!-- Description -->
                                <p class="food-description"><?php echo htmlspecialchars($food['slogan']); ?></p>
                                
                                <!-- Price and Order Button -->
                                <div class="price-action-container">
                                    <div class="price-section">
                                        <span class="current-price">
                                            <?php echo number_format($food['price'], 0, ',', '.'); ?>₫
                                        </span>
                                    </div>
                                    <div class="order-section">
                                        <a href="dishes.php?res_id=<?php echo $food['rs_id']; ?>" 
                                           class="btn btn-primary btn-order w-100">
                                            <i class="fas fa-utensils me-2"></i>Đặt món ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    $rank++;
                    }
                } else {
                ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h4>Chưa có món ăn nào</h4>
                            <p class="text-muted">Các món ăn bán chạy sẽ xuất hiện tại đây</p>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-5">
                <a href="restaurants.php" class="btn btn-outline-primary btn-lg rounded-pill">
                    <i class="fas fa-utensils me-2"></i>Khám phá thêm món ăn
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants Section -->
    <section class="featured-restaurants py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <div class="section-header">
                        <span class="badge bg-primary-gradient px-4 py-2 rounded-pill mb-3">
                            <i class="fas fa-store me-2"></i>NHÀ HÀNG NỔI BẬT
                        </span>
                        <h2 class="display-5 fw-bold mb-3">Nhà hàng được yêu thích</h2>
                        <p class="lead text-muted">Khám phá những nhà hàng tuyệt vời nhất</p>
                        <div class="divider mx-auto"></div>
                    </div>
                </div>
            </div>

            <!-- Restaurants Grid -->
            <div class="row g-4">
                <?php 
                $stmt = mysqli_query($db, "SELECT * FROM restaurant ORDER BY RAND() LIMIT 6");
                while($restaurant = mysqli_fetch_array($stmt)) {
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="restaurant-card-modern h-100">
                        <div class="restaurant-image-container">
                            <img src="admin/Res_img/<?php echo htmlspecialchars($restaurant['image']); ?>" 
                                 class="restaurant-image" 
                                 alt="<?php echo htmlspecialchars($restaurant['title']); ?>">
                            <div class="image-overlay">
                                <a href="dishes.php?res_id=<?php echo $restaurant['rs_id']; ?>" 
                                   class="btn btn-light btn-sm rounded-pill">
                                    <i class="fas fa-eye me-1"></i>Xem menu
                                </a>
                            </div>
                        </div>

                        <div class="card-content">
                            <div class="restaurant-info">
                                <h5 class="restaurant-title">
                                    <?php echo htmlspecialchars($restaurant['title']); ?>
                                </h5>
                                <p class="restaurant-address">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($restaurant['address']); ?>
                                </p>
                            </div>

                            <div class="restaurant-stats">
                                <div class="stat-item">
                                    <i class="fas fa-star text-warning"></i>
                                    <span>4.<?php echo rand(5,9); ?></span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-motorcycle text-primary"></i>
                                    <span>30-45 phút</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-tag text-success"></i>
                                    <span>Miễn phí ship</span>
                                </div>
                            </div>

                            <div class="restaurant-actions">
                                <a href="dishes.php?res_id=<?php echo $restaurant['rs_id']; ?>" 
                                   class="btn btn-primary btn-order w-100">
                                    <i class="fas fa-utensils me-2"></i>Đặt món ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-5">
                <a href="restaurants.php" class="btn btn-outline-primary btn-lg rounded-pill">
                    <i class="fas fa-store me-2"></i>Xem tất cả nhà hàng
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <?php include "include/footer.php" ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script>
        // Your existing JavaScript
        // ...
    </script>

    <!-- Add before closing body tag -->
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kết quả tìm kiếm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="searchResults"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update JavaScript -->
    <script>
    $(document).ready(function() {
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            const searchTerm = $('#searchInput').val().trim();
            const searchType = $('#searchType').val();
            
            if(searchTerm.length < 2) {
                alert('Vui lòng nhập ít nhất 2 ký tự');
                return;
            }
            
            $.ajax({
                url: 'search_results.php',
                method: 'GET',
                data: { 
                    term: searchTerm,
                    type: searchType
                },
                beforeSend: function() {
                    $('#searchResults').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tìm kiếm...</div>');
                    $('#searchModal').modal('show');
                },
                success: function(response) {
                    $('#searchResults').html(response);
                },
                error: function() {
                    $('#searchResults').html('<div class="alert alert-danger">Có lỗi xảy ra khi tìm kiếm</div>');
                }
            });
        });
    });
    </script>

    <!-- Thêm JavaScript cho tương tác -->
    <script>
$(document).ready(function() {
    // Quick view functionality - đi thẳng đến nhà hàng và highlight món ăn
    $('.quick-view').on('click', function() {
        const foodId = $(this).data('food-id');
        const restaurantId = $(this).data('restaurant-id');
        
        // Chuyển đến trang nhà hàng với highlight món ăn
        window.location.href = `dishes.php?res_id=${restaurantId}&highlight=${foodId}`;
    });
    
    // Add to cart functionality
    $('.add-to-cart').on('click', function() {
        const foodId = $(this).data('food-id');
        const restaurantId = $(this).data('restaurant-id');
        
        // Kiểm tra đăng nhập
        <?php if(empty($_SESSION["user_id"])) { ?>
            alert('Vui lòng đăng nhập để thêm món vào giỏ hàng');
            window.location.href = 'login.php';
            return;
        <?php } ?>
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        // AJAX thêm vào giỏ hàng
        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: {
                dish_id: foodId,
                restaurant_id: restaurantId,
                quantity: 1
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $btn.html('<i class="fas fa-check"></i>').removeClass('btn-outline-primary').addClass('btn-success');
                    
                    // Cập nhật số lượng giỏ hàng trong navbar
                    updateCartCount();
                    
                    // Hiển thị thông báo thành công
                    showNotification('success', 'Đã thêm vào giỏ hàng!');
                    
                    setTimeout(() => {
                        $btn.html(originalHtml).removeClass('btn-success').addClass('btn-outline-primary').prop('disabled', false);
                    }, 2000);
                } else {
                    showNotification('error', response.message || 'Có lỗi xảy ra');
                    $btn.html(originalHtml).prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });
    
    // Function để cập nhật số lượng giỏ hàng
    function updateCartCount() {
        $.get('get_cart_count.php', function(data) {
            const cartBadge = $('.fa-shopping-cart').siblings('.badge');
            if(data.count > 0) {
                if(cartBadge.length) {
                    cartBadge.text(data.count);
                } else {
                    $('.fa-shopping-cart').after('<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' + data.count + '</span>');
                }
            }
        }, 'json');
    }
    
    // Function hiển thị thông báo
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 10px;">
                <i class="fas fa-${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.alert('close');
        }, 3000);
    }
});
</script>

<!-- Chatbox JavaScript -->
<script src="food-chatbox/assets/js/chatbox.js"></script>
</body>
</html>