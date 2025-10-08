<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Check if user is logged in
if(strlen($_SESSION['user_id'])==0) { 
    header('location:login.php');
    exit();
}
$current_user_id = $_SESSION['user_id'];
$query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$current_user_id'");
$user_data = mysqli_fetch_array($query);

if(isset($_POST['update_avatar'])) {
        // Check if file was uploaded without errors
        if(isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == 0) {
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
            $filename = $_FILES["avatar"]["name"];
            $filetype = $_FILES["avatar"]["type"];
            $filesize = $_FILES["avatar"]["size"];
        
            // Verify file extension
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)) {
                echo "<script>alert('Lỗi: Vui lòng chọn đúng định dạng file');</script>";
            }
            else {
                // Verify file size - 5MB maximum
                $maxsize = 5 * 1024 * 1024;
                if($filesize > $maxsize) {
                    echo "<script>alert('Lỗi: Kích thước file quá lớn');</script>";
                }
                else {
                    // Verify MIME type of the file
                    if(in_array($filetype, $allowed)) {
                        // Check whether file exists before uploading it
                        $target_dir = "images/avatars/";
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        
                        $avatar_name = $current_user_id . "_" . time() . "." . $ext;
                        $target_file = $target_dir . $avatar_name;
                        
                        if(move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                            // Update avatar in database - first check if column exists
                            $check_column = mysqli_query($db, "SHOW COLUMNS FROM users LIKE 'avatar'");
                            
                            if(mysqli_num_rows($check_column) > 0) {
                                $sql = mysqli_query($db, "UPDATE users SET avatar='$avatar_name' WHERE u_id='$current_user_id'");
                                if($sql) {
                                    echo "<script>alert('Cập nhật ảnh đại diện thành công');</script>";
                                    // Refresh user data
                                    $query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$current_user_id'");
                                    $user_data = mysqli_fetch_array($query);
                                } else {
                                    echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại');</script>";
                                }
                            } else {
                                echo "<script>alert('Cột avatar không tồn tại trong bảng users');</script>";
                            }
                        } else {
                            echo "<script>alert('Có lỗi xảy ra khi tải file lên');</script>";
                        }
                    } else {
                        echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại');</script>";
                    }
                }
            }
        } else {
            echo "<script>alert('Vui lòng chọn file ảnh');</script>";
        }
    }
    
    // Handle profile update
    if(isset($_POST['update_profile'])) {
        $f_name = mysqli_real_escape_string($db, $_POST['f_name']);
        $l_name = mysqli_real_escape_string($db, $_POST['l_name']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $phone = mysqli_real_escape_string($db, $_POST['phone']);
        
        // Check if email already exists for other users
        $check_email = mysqli_query($db, "SELECT * FROM users WHERE email='$email' AND u_id != '$current_user_id'");
        if(mysqli_num_rows($check_email) > 0) {
            echo "<script>alert('Email đã được sử dụng bởi tài khoản khác');</script>";
        } else {
            // Update profile information
            $update_query = mysqli_query($db, "UPDATE users SET f_name='$f_name', l_name='$l_name', email='$email', phone='$phone' WHERE u_id='$current_user_id'");
            
            if($update_query) {
                echo "<script>alert('Cập nhật thông tin thành công!');</script>";
                // Refresh user data
                $query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$current_user_id'");
                $user_data = mysqli_fetch_array($query);
            } else {
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại');</script>";
            }
        }
        
        // Check if password fields are filled
if(!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $check_pass = mysqli_query($db, "SELECT password FROM users WHERE u_id='$current_user_id'");
    $pass_row = mysqli_fetch_array($check_pass);
    $db_password = $pass_row['password'];
    
    // Verify the current password matches the stored hash
    if(md5($current_password) === $db_password) {
        // Current password is correct, now check if new passwords match
        if($new_password === $confirm_password) {
            // Validate password length (matching your registration requirements)
            if(strlen($new_password) < 6) {
                echo "<script>alert('Mật khẩu phải có ít nhất 6 ký tự!');</script>";
            } else {
                // Hash new password using md5 (Note: consider using stronger hashing in production)
                $hashed_password = md5($new_password);
                
                // Update password
                $update_pass = mysqli_query($db, "UPDATE users SET password='$hashed_password' WHERE u_id='$current_user_id'");
                
                if($update_pass) {
                    echo "<script>alert('Mật khẩu đã được cập nhật thành công!');</script>";
                } else {
                    echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!');</script>";
                }
            }
        } else {
            echo "<script>alert('Mật khẩu mới không khớp!');</script>";
        }
    } else {
        echo "<script>alert('Mật khẩu hiện tại không đúng!');</script>";
    }
} else {
    echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
}
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Thông tin người dùng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }
        .main-content {
            flex: 1 0 auto;
        }
        footer {
            margin-top: auto;
        }
        .navbar {
            background: linear-gradient(135deg, #fd4d40, #ff9b44);
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
        .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 32px;
            max-width: 600px;
            margin: 40px auto;
        }
        .user-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fd4d40;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }
        .section-title {
            color: #fd4d40;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            margin-left: 10px;
        }
        .btn-logout:hover {
            background-color: #c82333;
        }
        .form-control:focus {
            border-color: #fd4d40;
            box-shadow: 0 0 0 0.2rem rgba(253,77,64,.25);
        }
        .avatar-upload {
            margin-bottom: 20px;
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
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-2"></i>Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php"><i class="fas fa-store me-2"></i>Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="your_orders.php"><i class="fas fa-list me-2"></i>Đơn hàng</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
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
                        <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>
                            <?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : 'Tài khoản'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="your_orders.php">
                                    <i class="fas fa-list me-2"></i>Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item active" href="userprofile.php">
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
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="profile-card">
            <div class="text-center">
                <?php
                $check_column = mysqli_query($db, "SHOW COLUMNS FROM users LIKE 'avatar'");
                $avatar_exists = (mysqli_num_rows($check_column) > 0);
                $avatar = "default_avatar.png";
                if($avatar_exists && !empty($user_data['avatar'])) {
                    $avatar = $user_data['avatar'];
                }
                if(file_exists("images/avatars/" . $avatar)): ?>
                    <img src="images/avatars/<?php echo $avatar; ?>" alt="Avatar" class="user-avatar">
                <?php else: ?>
                    <img src="images/default_avatar.png" alt="Default Avatar" class="user-avatar">
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="avatar-upload">
                    <input type="file" name="avatar" id="avatar" class="form-control mb-2">
                    <button type="submit" name="update_avatar" class="btn btn-primary btn-sm">Cập nhật ảnh đại diện</button>
                </form>
            </div>
            <h3 class="section-title text-center">Thông tin cá nhân</h3>
            <table class="table table-bordered mb-4">
                <tr>
                    <th>Ngày đăng ký</th>
                    <td><?php echo htmlentities($user_data['date']); ?></td>
                </tr>
                <tr>
                    <th>Họ</th>
                    <td><?php echo htmlentities($user_data['f_name']); ?></td>
                </tr>
                <tr>
                    <th>Tên</th>
                    <td><?php echo htmlentities($user_data['l_name']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlentities($user_data['email']); ?></td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td><?php echo htmlentities($user_data['phone']); ?></td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        <?php if($user_data['status']==1) { 
                            echo "<span class='badge bg-primary'>Đang hoạt động</span>";
                        } else {
                            echo "<span class='badge bg-danger'>Bị khóa</span>";
                        } ?>
                    </td>
                </tr>
            </table>
            <div class="text-center mb-3">
                <button id="edit-profile-btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editProfileModal">Sửa thông tin</button>
                <a href="logout.php" class="btn btn-logout">Đăng xuất</a>
            </div>
            <!-- Modal sửa thông tin cá nhân -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px;">
      <form method="post">
        <div class="modal-header" style="background: linear-gradient(135deg, #fd4d40, #ff9b44); color: #fff; border-top-left-radius:18px; border-top-right-radius:18px;">
          <h4 class="modal-title" id="editProfileModalLabel">
            <i class="fas fa-user-edit me-2"></i>Cập nhật thông tin cá nhân
          </h4>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body px-4 py-3">
          <div class="row mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
              <label for="f_name" class="form-label fw-bold"><i class="fas fa-user me-2"></i>Họ:</label>
              <input type="text" class="form-control" id="f_name" name="f_name" value="<?php echo htmlentities($user_data['f_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="l_name" class="form-label fw-bold"><i class="fas fa-user-tag me-2"></i>Tên:</label>
              <input type="text" class="form-control" id="l_name" name="l_name" value="<?php echo htmlentities($user_data['l_name']); ?>" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label fw-bold"><i class="fas fa-envelope me-2"></i>Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlentities($user_data['email']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label fw-bold"><i class="fas fa-phone me-2"></i>Số điện thoại:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlentities($user_data['phone']); ?>" required>
          </div>
          <a href="javascript:void(0)" id="toggle-password" class="password-toggle d-block mb-2 text-primary fw-bold">
            <i class="fas fa-key me-2"></i>Thay đổi mật khẩu
          </a>
          <div id="password-section" class="password-section" style="display:none;">
            <div class="row">
              <div class="col-md-4 mb-2">
                <label for="current_password" class="form-label">Mật khẩu hiện tại:</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
              </div>
              <div class="col-md-4 mb-2">
                <label for="new_password" class="form-label">Mật khẩu mới:</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
              </div>
              <div class="col-md-4 mb-2">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="border-bottom-left-radius:18px; border-bottom-right-radius:18px;">
          <button type="submit" name="update_profile" class="btn btn-success px-4">
            <i class="fas fa-save me-2"></i>Lưu thay đổi
          </button>
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Hủy
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
        </div>
    </div>
    <!-- Footer -->
    <?php include "include/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
           $(document).ready(function() {
        $("#toggle-password").click(function() {
            $("#password-section").slideToggle();
        });
    });
    </script>
</body>
</html>