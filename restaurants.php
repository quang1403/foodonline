<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Restaurants</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<header id="header" class="header-scroll top-header headrom">
    <nav class="navbar navbar-dark position-relative">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="index.php">
                <img class="img-rounded" src="images/logo.png" alt="" width="18%">
            </a>
            
            <ul class="nav navbar-nav d-flex flex-row">
                <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link active" href="restaurants.php">Nhà hàng</a></li>
                <?php
                if(empty($_SESSION["user_id"])) {
                    echo '<li class="nav-item"><a href="login.php" class="nav-link active">Đăng nhập</a></li>
                          <li class="nav-item"><a href="registration.php" class="nav-link active">Đăng ký</a></li>';
                } else {
                    echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">Đơn hàng</a></li>';
                    echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Đăng xuất</a></li>';
                }
                ?>
            </ul>

            <!-- Thanh tìm kiếm -->
            <div class="form-inline d-flex search-form">
                <input type="text" id="searchInput" class="form-control d-none" placeholder="Search restaurants...">
                <button id="searchButton" class="btn btn-light"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </nav>
</header>

<style>
    .search-form {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    .filter-form {
        margin-top: 20px;
    }
</style>

<div class="page-wrapper">
    <div class="top-links">
        <div class="container">
            <ul class="row links">
                <li class="col-xs-12 col-sm-4 link-item active"><span>1</span><a href="#">Lựa chọn nhà hàng</a></li>
                <li class="col-xs-12 col-sm-4 link-item"><span>2</span><a href="#">Lựa chọn món ăn yêu thích</a></li>
                <li class="col-xs-12 col-sm-4 link-item"><span>3</span><a href="#">Xác nhận & Thanh toán</a></li>
            </ul>
        </div>
    </div>

    <div class="inner-page-hero bg-image" data-image-src="images/img/pimg.jpg">
        <div class="container"></div>
    </div>

    <div class="result-show">
        <div class="container">
            <div class="row"></div>
        </div>
    </div>

    <section class="restaurants-page">
        <div class="container">
            <div class="row">

                <!-- Bộ lọc khu vực -->
<div class="col-xs-12 col-sm-5 col-md-5 col-lg-3">
    <form method="GET" class="filter-form">
        <h5><strong>Lọc theo khu vực</strong></h5>
        <div class="form-group position-relative">
            <input type="text" id="district-input" name="district" class="form-control" autocomplete="off" placeholder="Nhập tên khu vực...">
            <div id="suggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Lọc</button>
    </form>
</div>


                <!-- Danh sách nhà hàng -->
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-9">
                    <div class="bg-gray restaurant-entry">
                        <div class="row">
                            <?php
                            $where = " WHERE 1=1 ";
                            if (!empty($_GET['district'])) {
                                $district = mysqli_real_escape_string($db, $_GET['district']);
                                $where .= " AND district = '$district' ";
                            }

                            $ress = mysqli_query($db, "SELECT * FROM restaurant $where");
                            if (mysqli_num_rows($ress) > 0) {
                                while ($rows = mysqli_fetch_array($ress)) {
                                    echo '
                                    <div class="restaurant-item col-sm-12 col-md-12 col-lg-8 text-xs-center text-sm-left">
                                        <div class="entry-logo">
                                            <a class="img-fluid" href="dishes.php?res_id='.$rows['rs_id'].'" > 
                                                <img src="admin/Res_img/'.$rows['image'].'" alt="Food logo">
                                            </a>
                                        </div>
                                        <div class="entry-dscr">
                                            <h5><a href="dishes.php?res_id='.$rows['rs_id'].'" >'.$rows['title'].'</a></h5> 
                                            <span>'.$rows['address'].'</span>
                                        </div>
                                    </div>';
                                }
                            } else {
                                echo '<div class="col-md-12"><p>Không tìm thấy nhà hàng nào.</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include "include/footer.php" ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
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
    </script>
    <script>
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
