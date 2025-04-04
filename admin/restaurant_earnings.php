<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if(empty($_SESSION["adm_id"]))
{
    header('location:index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Thu nhập theo nhà hàng</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header fix-sidebar">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    
    <div id="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>
        
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="col-lg-12">
                            <div class="card card-outline-primary">
                                <div class="card-header">
                                    <h4 class="m-b-0 text-white">Thu nhập theo nhà hàng</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive m-t-40">
                                        <table id="myTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên nhà hàng</th>
                                                    <th>Số lượng đơn hàng</th>
                                                    <th>Tổng thu nhập</th>
                                                    <th>Chi tiết</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Truy vấn dữ liệu thu nhập theo từng nhà hàng
                                                $sql = "SELECT r.rs_id, r.title, 
                                                    COUNT(DISTINCT o.o_id) AS total_orders,
                                                    COALESCE(SUM(o.price * o.quantity), 0) AS total_earnings
                                                FROM restaurant r
                                                LEFT JOIN users_orders o ON r.rs_id = o.rs_id AND o.status = 'closed'
                                                GROUP BY r.rs_id
                                                ORDER BY total_earnings DESC";
                                                
                                                $query = mysqli_query($db, $sql);
                                                $stt = 1;
                                                
                                                if(mysqli_num_rows($query) > 0) {
                                                    while($result = mysqli_fetch_array($query)) {
                                                        // Kiểm tra nếu không có thu nhập, hiển thị 0
                                                        $total_orders = $result['total_orders'] ? $result['total_orders'] : 0;
                                                        $total_earnings = $result['total_earnings'] ? $result['total_earnings'] : 0;
                                                        
                                                        echo '<tr>
                                                                <td>'.$stt.'</td>
                                                                <td>'.$result['title'].'</td>
                                                                <td>'.$total_orders.'</td>
                                                                <td>'.number_format($total_earnings, 0, ',', '.').' VNĐ</td>
                                                                <td>
                                                                    <a href="restaurant_orders.php?rs_id='.$result['rs_id'].'" class="btn btn-info btn-flat btn-addon btn-sm m-b-10 m-l-5"><i class="fa fa-search"></i> Chi tiết</a>
                                                                </td>
                                                              </tr>';
                                                        $stt++;
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="5">Không có dữ liệu</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <div class="m-t-30">
                                        <a href="dashboard.php" class="btn btn-primary">Quay lại</a>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>

</html>