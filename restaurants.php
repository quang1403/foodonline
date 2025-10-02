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
    <title>Nhà hàng</title>
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

        .search-form input {
            border-radius: 20px;
            border: none;
            padding-left: 1rem;
        }

        .search-form button {
            border-radius: 20px;
            padding: 0.375rem 1rem;
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

        .restaurant-image {
            height: 200px;
            object-fit: cover;
        }

        .filter-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 1.5rem;
            position: sticky;
            top: 1rem;
        }

        .progress-steps {
            padding: 2rem 0;
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
        }

        .step-item.active .step-number {
            background: var(--secondary-color);
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
                
                <form class="d-flex search-form">
                    <input class="form-control me-2" type="search" id="searchInput" placeholder="Tìm nhà hàng...">
                    <button class="btn btn-light" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Progress Steps -->
    <div class="progress-steps">
        <div class="container">
            <div class="row">
                <div class="col-md-4 step-item active">
                    <div class="step-number">1</div>
                    <h6>Chọn nhà hàng</h6>
                </div>
                <div class="col-md-4 step-item">
                    <div class="step-number">2</div>
                    <h6>Chọn món ăn</h6>
                </div>
                <div class="col-md-4 step-item">
                    <div class="step-number">3</div>
                    <h6>Thanh toán</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Filter Section -->
            <div class="col-lg-3 mb-4">
                <div class="filter-card">
                    <h5 class="mb-3">Lọc theo khu vực</h5>
                    <form method="GET">
                        <div class="mb-3">
                            <input type="text" id="district-input" name="district" 
                                   class="form-control" placeholder="Nhập tên khu vực...">
                            <div id="suggestions" class="list-group mt-2"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                    </form>
                </div>
            </div>

            <!-- Restaurants List -->
            <div class="col-lg-9">
                <div class="row">
                    <?php
                    $where = " WHERE 1=1 ";
                    if (!empty($_GET['district'])) {
                        $district = mysqli_real_escape_string($db, $_GET['district']);
                        $where .= " AND (district LIKE '%$district%' OR address LIKE '%$district%')";
                    }
                    
                    $ress = mysqli_query($db, "SELECT * FROM restaurant $where");
                    if (mysqli_num_rows($ress) > 0) {
                        while ($row = mysqli_fetch_array($ress)) {
                            echo '<div class="col-md-6 mb-4 restaurant-item">
                                <div class="card restaurant-card">
                                    <img src="admin/Res_img/'.$row['image'].'" 
                                         class="card-img-top restaurant-image" alt="'.$row['title'].'">
                                    <div class="card-body">
                                        <h5 class="card-title">'.$row['title'].'</h5>
                                        <p class="card-text">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            '.$row['address'].'
                                        </p>
                                        <a href="dishes.php?res_id='.$row['rs_id'].'" 
                                           class="btn btn-primary w-100">
                                            <i class="fas fa-utensils me-2"></i>Xem menu
                                        </a>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Không tìm thấy nhà hàng nào.
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include "include/footer.php" ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchButton').on('click', function() {
                $('#searchInput').toggleClass('d-none').focus();
            });

            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.restaurant-item').each(function() {
                    var title = $(this).find('.entry-dscr h5 a').text().toLowerCase();
                    $(this).toggle(title.indexOf(value) > -1);
                });
            });
        });
        $(document).ready(function() {
            $('#district-input').keyup(function() {
                let query = $(this).val();
                if (query.length > 0) {
                    $.ajax({
                        url: "get_districts.php",
                        method: "GET",
                        data: { q: query },
                        success: function(data) {
                            $('#suggestions').fadeIn().html(data);
                        }
                    });
                } else {
                    $('#suggestions').fadeOut();
                }
            });

            // Khi click vào gợi ý
            $(document).on('click', '.suggestion-item', function(){
                $('#searchInput').val($(this).text());
                $('.suggestions-list').hide();
            });

            // Ẩn gợi ý khi click ngoài
            $(document).click(function(e) {
                if (!$(e.target).closest('#district-input, #suggestions').length) {
                    $('#suggestions').fadeOut();
                }
            });
        });
    </script>
</body>
</html>