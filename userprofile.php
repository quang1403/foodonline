<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Check if user is logged in
if(strlen($_SESSION['user_id'])==0)
{ 
    header('location:login.php');
}
else
{
    $current_user_id = $_SESSION['user_id'];
    
    // Get current user information
    $query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$current_user_id'");
    $user_data = mysqli_fetch_array($query);
    
    // Handle avatar update
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Thông tin người dùng</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <style type="text/css" rel="stylesheet">
            .indent-small {
        margin-left: 8px;
    }

    .form-group.internal {
        margin-bottom: 10px;
    }

    .dialog-panel {
        margin: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .datepicker-dropdown {
        z-index: 200 !important;
    }

    .panel-body {
        background: linear-gradient(to bottom, #e5e5e5, #ffffff);
        font: 600 15px "Open Sans", Arial, sans-serif;
        padding: 20px;
        border-radius: 10px;
    }

    label.control-label {
        font-weight: 600;
        color: #555;
    }

    table {
        width: 100%;
        max-width: 800px;
        border-collapse: collapse;
        margin: 30px auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    tr:nth-of-type(odd) {
        background: #f9f9f9;
    }

    th {
        background: #004684;
        color: white;
        font-weight: bold;
        text-align: left;
    }

    td, th {
        padding: 12px;
        border: 1px solid #ddd;
        font-size: 14px;
    }
    
    .user-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #004684;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }
    
    .avatar-container {
        text-align: center;
        margin-bottom: 20px;
    }

    #edit-profile-btn {
        height: 40px;
        line-height: 40px;
        border-radius: 4px;
        border: none;
        background: #007bff;
        color: white;
        cursor: pointer;
        transition: 0.3s;
    }

    #edit-profile-btn:hover {
        background: #0056b3;
    }

    .btn-logout {
        background-color: #dc3545;
        height: 40px;
        line-height: 40px;
        text-decoration: none;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        transition: 0.3s;
    }
    
    .btn-logout:hover {
        background-color: #c82333;
    }
    
    .profile-section {
        margin-bottom: 30px;
    }
    
    .section-title {
        color: #004684;
        border-bottom: 3px solid #004684;
        padding-bottom: 8px;
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: bold;
    }
    
    .edit-mode {
        margin-top: 15px;
    }
    
    .password-toggle {
        cursor: pointer;
        margin-top: 15px;
        display: block;
        color: #004684;
        text-decoration: underline;
        font-size: 14px;
        transition: 0.3s;
    }
    
    .password-toggle:hover {
        color: #002b5c;
    }
    
    .back-button {
        display: inline-block;
        padding: 10px 18px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        margin-right: 10px;
        transition: 0.3s;
    }
    
    .back-button:hover {
        background-color: #218838;
    }
    
    .top-actions {
        margin-bottom: 20px;
        text-align: left;
    }
    
    .password-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px dashed #ccc;
    }

    /* Edit Profile Form Styling */
    #edit-profile-form {
        display: none;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    #edit-profile-form .form-group {
        margin-bottom: 15px;
    }

    #edit-profile-form label {
        font-weight: 600;
        color: #333;
    }

    #edit-profile-form input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    #edit-profile-form button {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    #edit-profile-form .btn-success {
        background-color: #28a745;
        color: white;
    }

    #edit-profile-form .btn-success:hover {
        background-color: #218838;
    }

    #edit-profile-form .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    #edit-profile-form .btn-secondary:hover {
        background-color: #545b62;
    }
    </style>
    
    <script language="javascript" type="text/javascript">
    function f2() {
        window.close();
    }

    function f3() {
        window.print();
    }
    
    $(document).ready(function() {
        // Toggle edit mode for profile
        $("#edit-profile-btn").click(function() {
            $("#profile-info").hide();
            $("#edit-profile-form").show();
        });
        
        $("#cancel-edit").click(function() {
            $("#edit-profile-form").hide();
            $("#profile-info").show();
        });
        
        // Toggle password change section
        $("#toggle-password").click(function() {
            $("#password-section").slideToggle();
        });
    });
    </script>
</head>

<body>
    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <div class="top-actions">
            <a href="index.php" class="back-button"><i class="fa fa-arrow-left"></i> Trở về trang chủ</a>
        </div>
        
        <div class="avatar-container">
            <?php
            // Check if avatar column exists
            $check_column = mysqli_query($db, "SHOW COLUMNS FROM users LIKE 'avatar'");
            $avatar_exists = (mysqli_num_rows($check_column) > 0);
            
            // Default avatar
            $avatar = "default_avatar.png";
            
            // If avatar column exists and user has an avatar
            if($avatar_exists && !empty($user_data['avatar'])) {
                $avatar = $user_data['avatar'];
            }
            
            if(file_exists("images/avatars/" . $avatar)): ?>
                <img src="images/avatars/<?php echo $avatar; ?>" alt="Avatar" class="user-avatar">
            <?php else: ?>
                <img src="images/default_avatar.png" alt="Default Avatar" class="user-avatar">
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div style="margin-bottom: 10px;">
                    <input type="file" name="avatar" id="avatar">
                </div>
                <button type="submit" name="update_avatar" class="btn btn-primary">Cập nhật ảnh đại diện</button>
            </form>
        </div>
        
        <!-- Profile Information Display -->
        <div id="profile-info">
            <h3 class="section-title">Thông tin cá nhân</h3>
            <table>
                <tr>
                    <td><b>Ngày đăng ký:</b></td>
                    <td><?php echo htmlentities($user_data['date']); ?></td>
                </tr>
                <tr>
                    <td><b>Họ:</b></td>
                    <td><?php echo htmlentities($user_data['f_name']); ?></td>
                </tr>
                <tr>
                    <td><b>Tên:</b></td>
                    <td><?php echo htmlentities($user_data['l_name']); ?></td>
                </tr>
                <tr>
                    <td><b>Email:</b></td>
                    <td><?php echo htmlentities($user_data['email']); ?></td>
                </tr>
                <tr>
                    <td><b>Số điện thoại:</b></td>
                    <td><?php echo htmlentities($user_data['phone']); ?></td>
                </tr>
                <tr>
                    <td><b>Trạng thái:</b></td>
                    <td>
                        <?php if($user_data['status']==1) { 
                            echo "<div class='btn btn-primary'>Đang hoạt động</div>";
                        } else {
                            echo "<div class='btn btn-danger'>Bị khóa</div>";
                        } ?>
                    </td>
                </tr>
            </table>
            
            <div style="text-align: center; margin-top: 20px;">
                <button id="edit-profile-btn" class="btn btn-primary">Sửa thông tin</button>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            </div>
        </div>
        
        <!-- Edit Profile Form -->
        <div id="edit-profile-form" style="display: none;">
            <h3 class="section-title">Cập nhật thông tin cá nhân</h3>
            <form method="post">
                <div class="form-group">
                    <label for="f_name">Họ:</label>
                    <input type="text" class="form-control" id="f_name" name="f_name" value="<?php echo htmlentities($user_data['f_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="l_name">Tên:</label>
                    <input type="text" class="form-control" id="l_name" name="l_name" value="<?php echo htmlentities($user_data['l_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlentities($user_data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlentities($user_data['phone']); ?>" required>
                </div>
                
                <a href="javascript:void(0)" id="toggle-password" class="password-toggle">Thay đổi mật khẩu</a>
                
                <!-- Password Change Section (Hidden by default) -->
                <div id="password-section" class="password-section" style="display: none;">
                    <h4>Thay đổi mật khẩu</h4>
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại:</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <button type="submit" name="update_profile" class="btn btn-success">Lưu thay đổi</button>
                    <button type="button" id="cancel-edit" class="btn btn-secondary">Hủy</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="js/lib/bootstrap/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>