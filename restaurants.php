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
    <!-- Chatbox CSS -->
    <link rel="stylesheet" href="food-chatbox/assets/css/chatbox.css">
    <style>
        :root {
            --primary-color: #fd4d40;
            --secondary-color: #ff9b44;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            height: 40px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: white;
            transition: all 0.3s;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        .nav-link:hover {
            transform: translateY(-2px);
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

        .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            padding: 0.7rem 1.5rem;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .input-group {
            position: relative;
        }

        .input-group .form-control {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding-right: 40px;
        }

        .input-group .form-control:focus {
            background: rgba(255,255,255,0.2);
            box-shadow: none;
            border-color: rgba(255,255,255,0.3);
        }

        .input-group .form-control::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .input-group .btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
            padding: 0.375rem 0.75rem;
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.7);
        }

        .input-group .btn:hover {
            color: white;
            background: transparent;
        }

        #suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            display: none;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border-radius: 0.375rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .suggestion-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .suggestion-item:hover {
            background: #f8f9fa;
        }

        .badge {
            font-size: 0.7rem;
            padding: 0.35em 0.65em;
        }

        .no-results {
            animation: fadeInDown 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="restaurants.php">
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
                    <?php } ?>
                </ul>

                <!-- Search Form -->
                <form class="d-flex me-3" id="searchForm" onsubmit="return false;">
                    <div class="input-group">
                        <input type="search" 
                               class="form-control rounded-pill" 
                               placeholder="Tìm nhà hàng..." 
                               id="searchInput"
                               autocomplete="off">
                        <button class="btn btn-light rounded-pill ms-2" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <?php if(!empty($_SESSION["user_id"])) { ?>
                <div class="d-flex align-items-center">
                    <!-- Cart Icon -->
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
                    <!-- User Dropdown -->
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
                </div>
                <?php } ?>
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
                                        <h5 class="card-title restaurant-name">'.$row['title'].'</h5>
                                        <p class="card-text restaurant-address">
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
        // Live Search Function
        function performSearch() {
            let searchValue = $('#searchInput').val().toLowerCase().trim();
            let found = false;
            
            // Remove existing no-results message
            $('.no-results').remove();
            
            $('.restaurant-item').each(function() {
                let restaurantName = $(this).find('.restaurant-name').text().toLowerCase();
                let restaurantAddress = $(this).find('.restaurant-address').text().toLowerCase();
                
                if (restaurantName.includes(searchValue) || restaurantAddress.includes(searchValue)) {
                    $(this).fadeIn(300);
                    found = true;
                } else {
                    $(this).fadeOut(300);
                }
            });

            // Show no results message if needed
            if (!found && searchValue !== '') {
                $('.row:first').append(`
                    <div class="col-12 no-results">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-search me-2"></i>
                            Không tìm thấy nhà hàng phù hợp với từ khóa "${searchValue}"
                        </div>
                    </div>
                `);
            }
        }

        // Search on input change with debounce
        let searchTimeout;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        // Search when click button
        $('#searchBtn').on('click', performSearch);

        // Clear search
        $('#searchInput').on('search', function() {
            if ($(this).val() === '') {
                $('.restaurant-item').fadeIn(300);
                $('.no-results').remove();
            }
        });

        // District filter
        let districtSearchTimeout;
        $('#district-input').on('input', function() {
            clearTimeout(districtSearchTimeout);
            const query = $(this).val();
            
            districtSearchTimeout = setTimeout(function() {
                if (query.length > 0) {
                    $.get("get_districts.php", { q: query })
                        .done(function(data) {
                            $('#suggestions').html(data).slideDown();
                        })
                        .fail(function() {
                            $('#suggestions').empty().slideUp();
                        });
                } else {
                    $('#suggestions').empty().slideUp();
                }
            }, 300);
        });

        // Handle suggestion click
        $(document).on('click', '.suggestion-item', function() {
            $('#district-input').val($(this).text());
            $('#suggestions').slideUp();
        });

        // Close suggestions on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#district-input, #suggestions').length) {
                $('#suggestions').slideUp();
            }
        });
    });
    </script>
    <!-- Chatbox JavaScript -->
<script src="food-chatbox/assets/js/chatbox.js"></script>
</body>
</html>