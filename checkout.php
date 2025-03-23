<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
include_once 'product-action.php';
error_reporting(0);
session_start();


function function_alert() { 
      

    echo "<script>alert('Thank you. Your Order has been placed!');</script>"; 
    echo "<script>window.location.replace('your_orders.php');</script>"; 
} 

if(empty($_SESSION["user_id"])) {
    header('location:login.php');
} else {
    foreach ($_SESSION["cart_item"] as $item) {
        $item_total += ($item["price"] * $item["quantity"]);

        if($_POST['submit']) {
            $SQL = "INSERT INTO users_orders(u_id, title, quantity, price) 
                    VALUES ('".$_SESSION["user_id"]."', '".$item["title"]."', '".$item["quantity"]."', '".$item["price"]."')";

            mysqli_query($db, $SQL);

            // Cập nhật rs_id sau khi đặt hàng thành công
            $update_rs_id = "UPDATE users_orders uo 
                             JOIN dishes d ON uo.title = d.title
                             SET uo.rs_id = d.rs_id
                             WHERE uo.rs_id = 0"; 

            mysqli_query($db, $update_rs_id);

            unset($_SESSION["cart_item"]);
            unset($item["title"]);
            unset($item["quantity"]);
            unset($item["price"]);

            function_alert();
        }
    }
}
?>


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Checkout || DelishHub-Đồ ăn nhanh HQ</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
   
    <div class="site-wrapper">
        <header id="header" class="header-scroll top-header headrom">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                    <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/logo.png" alt="" width="18%"> </a>
                    <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"> <a class="nav-link active" href="index.php">Trang chủ <span class="sr-only">(current)</span></a> </li>
                            <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Nhà hàng <span class="sr-only"></span></a> </li>

                            <?php
						if(empty($_SESSION["user_id"]))
							{
								echo '<li class="nav-item"><a href="login.php" class="nav-link active">Đăng nhập</a> </li>
							  <li class="nav-item"><a href="registration.php" class="nav-link active">Đăng ký</a> </li>';
							}
						else
							{
									
									
										echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">Đơn hàng</a> </li>';
									echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Đăng xuất</a> </li>';
							}

						?>
                           
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="page-wrapper">
            <div class="top-links">
                <div class="container">
                    <ul class="row links">

                        <li class="col-xs-12 col-sm-4 link-item"><span>1</span><a href="restaurants.php">Lựa chọn nhà hàng</a></li>
                        <li class="col-xs-12 col-sm-4 link-item "><span>2</span><a href="#">Chọn món</a></li>
                        <li class="col-xs-12 col-sm-4 link-item active"><span>3</span><a href="checkout.php">Xác nhận và thanh toán</a></li>
                    </ul>
                </div>
            </div>

            <div class="container">

                <span style="color:green;">
                    <?php echo $success; ?>
                </span>

            </div>


           

            <div class="container m-t-30">
                <form action="" method="post">
                    <div class="widget clearfix">

                        <div class="widget-body">
                            <form method="post" action="#">
                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="cart-totals margin-b-20">
                                            <div class="cart-totals-title">
                                                <h4>Thông tin thanh toán</h4>
                                            </div>
                                            <div class="cart-totals-fields">

                                                <table class="table">
                                                    <tbody>

                                                       

                                                        <tr>
                                                            <td>Tổng tiền giỏ hàng</td>
                                                            <td> <?php echo $item_total."VND"; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Phí vận chuyển</td>
                                                            <td>Miễn phí</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-color"><strong>Tổng</strong></td>
                                                            <td class="text-color"><strong> <?php echo $item_total."VND"; ?></strong></td>
                                                        </tr>
                                                    </tbody>

                                                   


                                                </table>
                                            </div>
                                        </div>
                                        <div class="payment-option">
                                            <ul class=" list-unstyled">
                                                <li>
                                                    <label class="custom-control custom-radio  m-b-20">
                                                        <input name="mod" id="radioStacked1" checked value="COD" type="radio" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description">Thanh toán khi nhận hàng</span>
                                                    </label>
                                                </li>
                                                <li>
                                                <label class="custom-control custom-radio  m-b-10">
                                                        <input name="mod" type="radio" value="qr" class="custom-control-input" onclick="showQR()"> 
                                                        <span class="custom-control-indicator"></span> 
                                                        <span class="custom-control-description">Pay via QR Code <img src="images/qr_cod.jpg" alt="" width="30"></span>
                                                </label>
                                                </li>
                                        <div id="qrPayment" style="display: none; text-align: center;">
                                        <p>Quét mã QR để hoàn tất thanh toán:</p>
                                        <img src="images/qr_code.jpg" alt="QR Code for Payment" width="200">
                                        </div>

                                        <script>
                                         function toggleQR(selectedPayment) {
                                             if (selectedPayment === 'qr') {
                                                document.getElementById('qrPayment').style.display = 'block';
                                        } else {
                                                 document.getElementById('qrPayment').style.display = 'none';
                                             }
                                     }
                                    document.querySelectorAll('input[name="mod"]').forEach((radio) => {
                                     radio.addEventListener('change', function() {
                                    toggleQR(this.value);
                                });
                             });
                                        </script>

                                        </script>
                                                </li>
                                            </ul>
                                            <p class="text-xs-center"> <input type="submit" onclick="return confirm('Xác nhận thanh toán?');" name="submit" class="btn btn-success btn-block" value="Thanh toán"> </p>
                                        </div>
                            </form>
                        </div>
                    </div>

            </div>
        </div>
        </form>
    </div>
                            </form>
                        </div>
                    </div>

            </div>
        </div>
        </form>
    </div>
   
    <?php include "include/footer.php" ?>
    </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>

</html>

<?php

?>