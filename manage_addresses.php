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

// Get user data
$query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$current_user_id'");
$user_data = mysqli_fetch_array($query);

// Handle Add Address
if(isset($_POST['add_address'])) {
    $address = mysqli_real_escape_string($db, $_POST['address']);
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    
    // If this is set as default, unset all other defaults for this user
    if($is_default == 1) {
        mysqli_query($db, "UPDATE user_addresses SET is_default=0 WHERE user_id='$current_user_id'");
    }
    
    $insert = mysqli_query($db, "INSERT INTO user_addresses(user_id, address, is_default) VALUES('$current_user_id', '$address', '$is_default')");
    
    if($insert) {
        $success = "Địa chỉ đã được thêm thành công!";
    } else {
        $error = "Có lỗi xảy ra, vui lòng thử lại!";
    }
}

// Handle Set Default Address
if(isset($_POST['set_default'])) {
    $address_id = $_POST['address_id'];
    
    // Unset all defaults for this user
    mysqli_query($db, "UPDATE user_addresses SET is_default=0 WHERE user_id='$current_user_id'");
    
    // Set this address as default
    $update = mysqli_query($db, "UPDATE user_addresses SET is_default=1 WHERE id='$address_id' AND user_id='$current_user_id'");
    
    if($update) {
        $success = "Đã đặt làm địa chỉ mặc định!";
    }
}

// Handle Delete Address
if(isset($_POST['delete_address'])) {
    $address_id = $_POST['address_id'];
    $delete = mysqli_query($db, "DELETE FROM user_addresses WHERE id='$address_id' AND user_id='$current_user_id'");
    
    if($delete) {
        $success = "Địa chỉ đã được xóa!";
    }
}

// Handle Edit Address
if(isset($_POST['edit_address'])) {
    $address_id = $_POST['address_id'];
    $address = mysqli_real_escape_string($db, $_POST['address']);
    
    $update = mysqli_query($db, "UPDATE user_addresses SET address='$address' WHERE id='$address_id' AND user_id='$current_user_id'");
    
    if($update) {
        $success = "Địa chỉ đã được cập nhật!";
    } else {
        $error = "Có lỗi xảy ra, vui lòng thử lại!";
    }
}

// Get all addresses for this user
$addresses_query = mysqli_query($db, "SELECT * FROM user_addresses WHERE user_id='$current_user_id' ORDER BY is_default DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Quản lý địa chỉ giao hàng</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .address-card {
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s;
            position: relative;
        }
        .address-card.default {
            border-color: #2d9cdb;
            background: #f0f8ff;
        }
        .address-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .default-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #2d9cdb;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .address-text {
            font-size: 1.05rem;
            color: #333;
            margin: 8px 0 12px 0;
            padding-right: 100px;
        }
        .address-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .address-actions button {
            padding: 6px 16px;
            border-radius: 6px;
            border: none;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-default {
            background: #2d9cdb;
            color: #fff;
        }
        .btn-default:hover {
            background: #1e7ba8;
        }
        .btn-edit {
            background: #f39c12;
            color: #fff;
        }
        .btn-edit:hover {
            background: #d68910;
        }
        .btn-delete {
            background: #e74c3c;
            color: #fff;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .add-address-form {
            background: #f9f9f9;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 14px;
        }
        .btn-add {
            background: #27ae60;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-add:hover {
            background: #1e8449;
            transform: translateY(-1px);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 32px 0;
            margin-bottom: 32px;
            border-radius: 0 0 20px 20px;
        }
        .alert {
            border-radius: 8px;
            padding: 14px 20px;
            margin-bottom: 20px;
        }
        .modal-content {
            border-radius: 12px;
        }
    </style>
</head>
<body>
    
    <?php include("include/header.php"); ?>
    
    <div class="page-header">
        <div class="container">
            <h1 class="mb-0"><i class="fa fa-map-marker"></i> Quản lý địa chỉ giao hàng</h1>
        </div>
    </div>
    
    <div class="container" style="margin-bottom: 60px;">
        
        <?php if(isset($success)) { ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php } ?>
        
        <?php if(isset($error)) { ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>
        
        <!-- Add Address Form -->
        <div class="add-address-form">
            <h4 class="mb-3"><i class="fa fa-plus-circle"></i> Thêm địa chỉ mới</h4>
            <form method="POST">
                <div class="form-group">
                    <label for="address">Địa chỉ giao hàng</label>
                    <textarea class="form-control" name="address" id="address" rows="3" placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phường, quận, thành phố)" required></textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="is_default" id="is_default">
                    <label class="form-check-label" for="is_default">
                        Đặt làm địa chỉ mặc định
                    </label>
                </div>
                <button type="submit" name="add_address" class="btn-add">
                    <i class="fa fa-save"></i> Thêm địa chỉ
                </button>
            </form>
        </div>
        
        <!-- Addresses List -->
        <h4 class="mb-3"><i class="fa fa-list"></i> Danh sách địa chỉ của bạn</h4>
        
        <?php 
        if(mysqli_num_rows($addresses_query) > 0) {
            while($address = mysqli_fetch_array($addresses_query)) { 
        ?>
            <div class="address-card <?php echo $address['is_default'] == 1 ? 'default' : ''; ?>">
                <?php if($address['is_default'] == 1) { ?>
                    <span class="default-badge"><i class="fa fa-star"></i> Mặc định</span>
                <?php } ?>
                
                <div class="address-text" id="address-text-<?php echo $address['id']; ?>">
                    <i class="fa fa-map-marker text-danger"></i> <?php echo htmlspecialchars($address['address']); ?>
                </div>
                
                <!-- Edit form (hidden by default) -->
                <div id="edit-form-<?php echo $address['id']; ?>" style="display: none; margin-bottom: 12px;">
                    <form method="POST">
                        <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                        <textarea class="form-control mb-2" name="address" rows="2" required><?php echo htmlspecialchars($address['address']); ?></textarea>
                        <button type="submit" name="edit_address" class="btn-edit">
                            <i class="fa fa-save"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit(<?php echo $address['id']; ?>)">
                            Hủy
                        </button>
                    </form>
                </div>
                
                <div class="address-actions">
                    <?php if($address['is_default'] == 0) { ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                            <button type="submit" name="set_default" class="btn-default">
                                <i class="fa fa-star"></i> Đặt làm mặc định
                            </button>
                        </form>
                    <?php } ?>
                    
                    <button type="button" class="btn-edit" onclick="showEditForm(<?php echo $address['id']; ?>)">
                        <i class="fa fa-edit"></i> Sửa
                    </button>
                    
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                        <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                        <button type="submit" name="delete_address" class="btn-delete">
                            <i class="fa fa-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        <?php 
            }
        } else { 
        ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Bạn chưa có địa chỉ nào. Vui lòng thêm địa chỉ giao hàng!
            </div>
        <?php } ?>
        
        <div class="text-center mt-4">
            <a href="userprofile.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Quay lại trang cá nhân
            </a>
        </div>
        
    </div>
    
    <?php include("include/footer.php"); ?>
    
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    
    <script>
        function showEditForm(id) {
            document.getElementById('address-text-' + id).style.display = 'none';
            document.getElementById('edit-form-' + id).style.display = 'block';
        }
        
        function cancelEdit(id) {
            document.getElementById('address-text-' + id).style.display = 'block';
            document.getElementById('edit-form-' + id).style.display = 'none';
        }
    </script>
    
</body>
</html>
