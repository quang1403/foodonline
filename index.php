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

        .restaurant-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .navbar .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
            border-radius: 0.5rem;
        }

        .navbar .dropdown-item {
            padding: 0.7rem 1.2rem;
            transition: all 0.2s;
        }

        .navbar .dropdown-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-2"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php">
                            <i class="fas fa-store me-2"></i>Nhà hàng
                        </a>
                    </li>
                    <?php if(!empty($_SESSION["user_id"])) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="your_orders.php">
                            <i class="fas fa-list me-2"></i>Đơn hàng
                        </a>
                    </li>
                    <?php } ?>
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
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-center mb-4">Món ăn bán chạy nhất</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php 
                // Lấy 20 món bán chạy nhất, sắp xếp giảm dần theo số lượng bán
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
                    while($food = mysqli_fetch_array($popular_foods)) {
                ?>
                    <div class="col-md-3">
                        <div class="food-card h-100">
                            <div class="position-relative">
                                <img src="admin/Res_img/dishes/<?php echo htmlspecialchars($food['img']); ?>" 
                                     class="card-img-top food-image" 
                                     alt="<?php echo htmlspecialchars($food['title']); ?>">
                                <div class="position-absolute top-0 end-0 m-2 px-2 py-1 bg-primary text-white rounded">
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    Đã bán: <?php echo number_format($food['total_quantity']); ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($food['title']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($food['slogan']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold">
                                        <?php echo number_format($food['price'], 0, ',', '.'); ?> VNĐ
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-store me-1"></i><?php echo htmlspecialchars($food['restaurant_name']); ?>
                                    </small>
                                </div>
                                <a href="dishes.php?res_id=<?php echo $food['rs_id']; ?>" 
                                   class="btn btn-outline-primary w-100 mt-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Đặt ngay
                                </a>
                            </div>
                        </div>
                    </div>
                <?php 
                    }
                } else {
                ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Chưa có món ăn nào được bán
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-center mb-4">Nhà hàng nổi bật</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php 
                $featured_restaurants = mysqli_query($db,"SELECT * FROM restaurant ORDER BY RAND() LIMIT 6");
                while($restaurant = mysqli_fetch_array($featured_restaurants)) {
                ?>
                <div class="col-md-4">
                    <div class="restaurant-card">
                        <img src="admin/Res_img/<?php echo $restaurant['image']; ?>" 
                             class="card-img-top food-image" alt="<?php echo $restaurant['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $restaurant['title']; ?></h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i><?php echo $restaurant['address']; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="fas fa-motorcycle me-1"></i>30-45 phút
                                </span>
                                <a href="dishes.php?res_id=<?php echo $restaurant['rs_id']; ?>" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-utensils me-2"></i>Xem menu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="text-center mt-4">
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-store me-2"></i>Xem tất cả nhà hàng
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
</body>
</html>