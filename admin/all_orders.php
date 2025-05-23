
                <!DOCTYPE html>
                <html lang="en">
                <?php
include("../connection/connect.php");
error_reporting(0);
session_start();

?>


                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <meta name="description" content="">
                    <meta name="author" content="">
                    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
                    <title>All Orders</title>
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

                        <div class="header">
                            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                                <div class="navbar-header">
                                    <a class="navbar-brand" href="dashboard.php">

                                    <span><img width="80px" src="images/logo.png" alt="homepage" class="dark-logo" /></span>                                    </a>
                                </div>
                                <div class="navbar-collapse">

                                    <ul class="navbar-nav mr-auto mt-md-0">


                        


                                    </ul>

                                    <ul class="navbar-nav my-lg-0">



                                        <li class="nav-item dropdown">

                                            <div class="dropdown-menu dropdown-menu-right mailbox animated zoomIn">
                                                <ul>
                                                    <li>
                                                        <div class="drop-title">Notifications</div>
                                                    </li>

                                                    <li>
                                                        <a class="nav-link text-center" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                        

                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle text-muted  " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="images/bookingSystem/logot.jpg" alt="user" class="profile-pic" /></a>
                                            <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                                                <ul class="dropdown-user">
                                                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <div class="left-sidebar">

            <div class="scroll-sidebar">
            

                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Trang chính</li>
                        <li> <a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Tổng quan</span></a>
                        </li>
                        <li class="nav-label">Log</li>
                        <li> <a href="all_users.php"> <span><i class="fa fa-user f-s-20 "></i></span><span>Người dùng</span></a></li>
                        <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Nhà hàng</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_restaurant.php">Nhà hàng</a></li>
                                <li><a href="add_category.php">Thêm sản phẩm</a></li>
                                <li><a href="add_restaurant.php">Thêm nhà hàng</a></li>

                            </ul>
                        </li>
                        <li> <a class="has-arrow  " href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menu</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>

                            

                            </ul>
                        </li>
                        <li> <a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Đơn hàng</span></a></li>

                    </ul>
                </nav>

            </div>

        </div>k

        <div class="page-wrapper">

        
            <div style="padding-top: 10px;">
                <marquee onMouseOver="this.stop()" onMouseOut="this.start()"> <a href="#">ONLINE FOOD HQ</a> - HỌC VIỆN QUẢN LÝ GIÁO DỤC.</marquee>
            </div>

            


                            <div class="container-fluid">

                                <div class="row">
                                    <div class="col-12">


                                        <div class="col-lg-12">
                                            <div class="card card-outline-primary">
                                                <div class="card-header">
                                                    <h4 class="m-b-0 text-white">Đơn hàng</h4>
                                                </div>

                                                <div class="table-responsive m-t-40">
                                                    <table id="myTable" class="table table-bordered table-striped">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Khách hàng</th>
                                                                <th>Món ăn</th>
                                                                <th>Số lượng</th>
                                                                <th>Giá</th>
                                                                <th>Địa chỉ</th>
                                                                <th>Trạng thái</th>
                                                                <th>Thời gian</th>
                                                                <th>Hành động</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>


                                                            <?php
												$sql="SELECT users.*, users_orders.* FROM users INNER JOIN users_orders ON users.u_id=users_orders.u_id ";
												$query=mysqli_query($db,$sql);
												
													if(!mysqli_num_rows($query) > 0 )
														{
															echo '<td colspan="8"><center>Không có đơn hàng!</center></td>';
														}
													else
														{				
																	while($rows=mysqli_fetch_array($query))
																		{
																																							
																				?>
                                                            <?php
																					echo ' <tr>
																					           <td>'.$rows['username'].'</td>
																								<td>'.$rows['title'].'</td>
																								<td>'.$rows['quantity'].'</td>
																								<td>'. number_format($rows['price'], 0, ',', '.') . 'VND</td>
																								<td>'.$rows['address'].'</td>';
																								?>
                                                            <?php 
																			$status=$rows['status'];
																			if($status=="" or $status=="NULL")
																			{
																			?>
                                                            <td> <button type="button" class="btn btn-info"><span class="fa fa-clock" aria-hidden="true"></span> Chờ xác nhận</button></td>
                                                            <?php 
																			  }
																			   if($status=="in process")
																			 { ?>
                                                            <td> <button type="button" class="btn btn-warning"><span class="fa fa-motorcycle" aria-hidden="true"></span> Đang trên đường giao!</button></td>
                                                            <?php
																				}
																			if($status=="closed")
																				{
																			?>
                                                            <td> <button type="button" class="btn btn-primary"><span class="fa fa-check-circle" aria-hidden="true"></span> Đã giao</button></td>
                                                            <?php 
																			} 
																			?>
                                                            <?php
																			if($status=="rejected")
																				{
																			?>
                                                            <td> <button type="button" class="btn btn-danger"> <i class="fa fa-close"></i> Đã hủy</button></td>
                                                            <?php 
																			} 
																			?>
                                                                            <?php
																			if($status=="preparing")
																				{
																			?>
                                                            <td> <button type="button" class="btn btn-danger"> <i class="fa fa-hourglass-half"></i> Đang chuẩn bị</button></td>
                                                            <?php 
																			} 
																			?>
                                                                            <?php
																			if($status=="prepared")
																				{
																			?>
                                                            <td> <button type="button" class="btn btn-danger"> <i class="fa fa-check"></i> Đã chuẩn bị</button></td>
                                                            <?php 
																			} 
																			?>
                                                                            
                                                            <?php																									
																							echo '	<td>'.$rows['date'].'</td>';
																							?>
                                                            <td>
                                                                <a href="delete_orders.php?order_del=<?php echo $rows['o_id'];?>" onclick="return confirm('Bạn chắc chứ?');" class="btn btn-danger btn-flat btn-addon btn-xs m-b-10"><i class="fa fa-trash-o" style="font-size:16px"></i></a>
                                                                <?php
																								echo '<a href="view_order.php?user_upd='.$rows['o_id'].'" " class="btn btn-info btn-flat btn-addon btn-sm m-b-10 m-l-5"><i class="fa fa-edit"></i></a>
																									</td>
																									</tr>';
																					 
																						
																						
																		}	
														}
												
											
											?>



                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    

                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
    


                    <?php include "include/footer.php" ?>

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

                </body>

                </html>
