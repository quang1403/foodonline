<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if(empty($_SESSION["adm_id"]))
{
    header('location:index.php');
}

// Kiểm tra tham số rs_id
if(empty($_GET['rs_id'])) {
    header('location:restaurant_earnings.php');
    exit();
}

$rs_id = $_GET['rs_id'];

// Lấy thông tin nhà hàng
$sql_restaurant = "SELECT * FROM restaurant WHERE rs_id = $rs_id";
$query_restaurant = mysqli_query($db, $sql_restaurant);
$restaurant = mysqli_fetch_assoc($query_restaurant);

if(!$restaurant) {
    header('location:restaurant_earnings.php');
    exit();
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
    <title>Chi tiết thu nhập nhà hàng <?php echo $restaurant['title']; ?></title>
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
                                    <h4 class="m-b-0 text-white">Chi tiết thu nhập: <?php echo $restaurant['title']; ?></h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Thông tin nhà hàng:</h5>
                                            <p><strong>Tên:</strong> <?php echo $restaurant['title']; ?></p>
                                            <p><strong>Địa chỉ:</strong> <?php echo $restaurant['address']; ?></p>
                                            <p><strong>Email:</strong> <?php echo $restaurant['email']; ?></p>
                                            <p><strong>Điện thoại:</strong> <?php echo $restaurant['phone']; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Tổng kết:</h5>
                                            <?php
                                            // Tính tổng số đơn hàng và doanh thu
                                            $sql_summary = "SELECT 
                                                COUNT(DISTINCT o_id) as total_orders,
                                                SUM(price) as total_revenue,
                                                COUNT(CASE WHEN status = 'closed' THEN o_id END) as completed_orders,
                                                SUM(CASE WHEN status = 'closed' THEN price ELSE 0 END) as completed_revenue
                                            FROM users_orders 
                                            WHERE rs_id = $rs_id";
                                            
                                            $query_summary = mysqli_query($db, $sql_summary);
                                            $summary = mysqli_fetch_assoc($query_summary);
                                            ?>
                                            <p><strong>Tổng số đơn hàng:</strong> <?php echo $summary['total_orders']; ?></p>
                                            <p><strong>Tổng doanh thu:</strong> <?php echo number_format($summary['total_revenue'], 0, ',', '.'); ?> VNĐ</p>
                                            <p><strong>Đơn hàng đã hoàn thành:</strong> <?php echo $summary['completed_orders']; ?></p>
                                            <p><strong>Doanh thu từ đơn hàng hoàn thành:</strong> <?php echo number_format($summary['completed_revenue'], 0, ',', '.'); ?> VNĐ</p>
                                        </div>
                                    </div>
                                    
                                    <h5 class="m-t-30">Danh sách đơn hàng:</h5>
                                    <div class="table-responsive m-t-20">
                                        <table id="myTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Mã đơn</th>
                                                    <th>Khách hàng</th>
                                                    <th>Món đặt</th>
                                                    <th>Số lượng</th>
                                                    <th>Giá</th>
                                                    <th>Trạng thái</th>
                                                    <th>Ngày đặt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql_orders = "SELECT o.*, u.f_name, u.l_name 
                                                               FROM users_orders o
                                                               LEFT JOIN users u ON o.u_id = u.u_id
                                                               WHERE o.rs_id = $rs_id
                                                               ORDER BY o.date DESC";
                                                
                                                $query_orders = mysqli_query($db, $sql_orders);
                                                $stt = 1;
                                                
                                                if(mysqli_num_rows($query_orders) > 0) {
                                                    while($order = mysqli_fetch_array($query_orders)) {
                                                        $status_color = '';
                                                        $status_text = 'Đang xử lý';
                                                        
                                                        if($order['status'] == 'closed') {
                                                            $status_color = 'label-success';
                                                            $status_text = 'Hoàn thành';
                                                        } else if($order['status'] == 'rejected') {
                                                            $status_color = 'label-danger';
                                                            $status_text = 'Đã hủy';
                                                        } else if($order['status'] == 'in process') {
                                                            $status_color = 'label-warning';
                                                            $status_text = 'Đang xử lý';
                                                        } else {
                                                            $status_color = 'label-info';
                                                            $status_text = 'Chờ xác nhận';
                                                        }
                                                        
                                                        echo '<tr>
                                                                <td>'.$stt.'</td>
                                                                <td>'.$order['o_id'].'</td>
                                                                <td>'.$order['f_name'].' '.$order['l_name'].'</td>
                                                                <td>'.$order['title'].'</td>
                                                                <td>'.$order['quantity'].'</td>
                                                                <td>'.number_format($order['price'], 0, ',', '.').' VNĐ</td>
                                                                <td><span class="label '.$status_color.'">'.$status_text.'</span></td>
                                                                <td>'.date('d/m/Y H:i', strtotime($order['date'])).'</td>
                                                              </tr>';
                                                        $stt++;
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="8">Không có đơn hàng nào</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="m-t-30">
                                        <a href="restaurant_earnings.php" class="btn btn-primary">Quay lại</a>
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