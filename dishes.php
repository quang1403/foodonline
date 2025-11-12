<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Xử lý thêm vào giỏ hàng trước khi có AJAX request
if(isset($_POST['addtocart']) && !isset($_POST['ajax'])) {
    $product_id = $_POST['addtocart'];
    
    // Lấy thông tin món ăn
    $stmt = $db->prepare("SELECT * FROM dishes WHERE d_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if($product) {
        // Thêm vào session cart
        if(isset($_SESSION["cart_item"][$product_id])) {
            $_SESSION["cart_item"][$product_id]["quantity"] += 1;
        } else {
            $_SESSION["cart_item"][$product_id] = array(
                'd_id' => $product['d_id'],
                'title' => $product['title'],
                'price' => $product['price'],
                'quantity' => 1,
                'img' => $product['img']
            );
        }
    }
    
    // Redirect về dishes.php với thông báo
    $res_id = isset($_GET['res_id']) ? $_GET['res_id'] : (isset($_POST['res_id']) ? $_POST['res_id'] : 1);
    header("Location: dishes.php?res_id=" . $res_id . "&added=1");
    exit();
}

// Include cart functionality 
include_once 'product-action.php';

// Handle AJAX requests
if(isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    header('Content-Type: application/json');
    
    if(isset($_POST['action'])) {
        $action = $_POST['action'];
        $product_id = isset($_POST['id']) ? $_POST['id'] : null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        // Handle clear action first (doesn't need product details)
        if($action === 'clear') {
            unset($_SESSION["cart_item"]);
        } else {
            // Get product details for other actions
            $stmt = $db->prepare("SELECT * FROM dishes WHERE d_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            
            if($product) {
                switch($action) {
                    case 'add':
                        if(isset($_SESSION["cart_item"][$product_id])) {
                            $_SESSION["cart_item"][$product_id]["quantity"] += $quantity;
                        } else {
                            $_SESSION["cart_item"][$product_id] = array(
                                'd_id' => $product['d_id'],
                                'title' => $product['title'],
                                'price' => $product['price'],
                                'quantity' => $quantity,
                                'img' => $product['img']
                            );
                        }
                        break;
                        
                    case 'increase':
                        if(isset($_SESSION["cart_item"][$product_id])) {
                            $_SESSION["cart_item"][$product_id]["quantity"]++;
                        }
                        break;
                        
                    case 'decrease':
                        if(isset($_SESSION["cart_item"][$product_id])) {
                            if($_SESSION["cart_item"][$product_id]["quantity"] > 1) {
                                $_SESSION["cart_item"][$product_id]["quantity"]--;
                            } else {
                                unset($_SESSION["cart_item"][$product_id]);
                            }
                        }
                        break;
                        
                    case 'remove':
                        if(isset($_SESSION["cart_item"][$product_id])) {
                            unset($_SESSION["cart_item"][$product_id]);
                        }
                        break;
                }
            }
        }
        
        // Return updated cart data
        $cart_html = '';
        $total_items = 0;
        $item_total = 0;
        
        if(isset($_SESSION["cart_item"]) && count($_SESSION["cart_item"]) > 0) {
            foreach($_SESSION["cart_item"] as $item) {
                $total_items += $item["quantity"];
                $item_subtotal = $item["price"] * $item["quantity"];
                $item_total += $item_subtotal;
                
                // Check if image exists, if not use default
                $img_path = "admin/Res_img/dishes/" . htmlspecialchars($item['img']);
                $default_img = "https://via.placeholder.com/100x60/ff9800/white?text=Món+ăn";
                
                // Use default image if original doesn't exist
                if (!file_exists($img_path) || empty($item['img'])) {
                    $img_src = $default_img;
                } else {
                    $img_src = $img_path;
                }
                
                $cart_html .= '
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <div class="cart-item-img">
                                <img src="' . $img_src . '" 
                                     class="img-fluid rounded" alt="' . htmlspecialchars($item['title']) . '"
                                     onerror="this.src=\'' . $default_img . '\'">
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="cart-item-title mb-1">' . htmlspecialchars($item["title"]) . '</h6>
                            <div class="cart-item-price">
                                ' . number_format($item["price"], 0, ',', '.') . ' VNĐ
                            </div>
                            <div class="cart-item-subtotal">
                                Subtotal: <strong>' . number_format($item_subtotal, 0, ',', '.') . ' VNĐ</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="quantity-controls">
                                <div class="quantity-wrapper">
                                    <a href="#" class="quantity-btn quantity-minus" data-action="decrease" data-id="' . $item["d_id"] . '">
                                        <i class="fas fa-minus"></i>
                                    </a>
                                    <span class="quantity-display">' . $item["quantity"] . '</span>
                                    <a href="#" class="quantity-btn quantity-plus" data-action="increase" data-id="' . $item["d_id"] . '">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <a href="#" class="remove-item-btn mt-2" data-action="remove" data-id="' . $item["d_id"] . '">
                                    <i class="fas fa-trash me-1"></i>Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            $cart_html = '
            <div class="cart-empty">
                <div class="text-center py-4">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h6 class="empty-cart-title">Giỏ hàng trống</h6>
                    <p class="empty-cart-text">Hãy thêm những món ăn yêu thích của bạn!</p>
                    <div class="empty-cart-animation">
                        <div class="bounce-1"></div>
                        <div class="bounce-2"></div>
                        <div class="bounce-3"></div>
                    </div>
                </div>
            </div>';
        }
        
        echo json_encode([
            'success' => true,
            'cart_html' => $cart_html,
            'total_items' => $total_items,
            'item_total' => $item_total,
            'formatted_total' => number_format($item_total, 0, ',', '.'),
            'action' => $action
        ]);
        exit();
    }
}

// Check if restaurant ID is provided
if(!isset($_GET['res_id']) || !is_numeric($_GET['res_id'])) {
    header('location: restaurants.php');
    exit();
}

// Get restaurant details
$stmt = mysqli_prepare($db, "SELECT * FROM restaurant WHERE rs_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_GET['res_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$restaurant = mysqli_fetch_array($result);

if(!$restaurant) {
    header('location: restaurants.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu nhà hàng</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chatbox CSS -->
    <link rel="stylesheet" href="food-chatbox/assets/css/chatbox.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #ff7b54 0%, #ff6b35 100%);
            --secondary-gradient: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
            --accent-gradient: linear-gradient(135deg, #ffab40 0%, #ff8f00 100%);
            --success-gradient: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
            --warning-gradient: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            --dark-gradient: linear-gradient(135deg, #d84315 0%, #bf360c 100%);
            --light-bg: #fff8f5;
            --card-shadow: 0 10px 30px rgba(255,107,53,0.15);
            --card-hover-shadow: 0 20px 40px rgba(255,107,53,0.25);
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--light-bg);
        }
        
        .navbar {
            background: var(--primary-gradient);
            box-shadow: 0 2px 20px rgba(255,107,53,0.2);
            backdrop-filter: blur(10px);
        }

        .navbar-brand img {
            filter: brightness(1.2);
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: white;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        .step-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--card-shadow);
        }

        .step-item {
            position: relative;
            text-align: center;
            padding: 1rem;
        }

        .step-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 80%;
            width: 40%;
            height: 2px;
            background: #ffcc80;
            z-index: 1;
        }

        .step-item:last-child::before {
            display: none;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: var(--accent-gradient);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(255,171,64,0.4);
            position: relative;
            z-index: 2;
        }

        .step-item.active .step-number {
            background: var(--secondary-gradient);
            box-shadow: 0 5px 15px rgba(255,152,0,0.5);
            animation: pulse 2s infinite;
        }

        .step-item.active::before {
            background: var(--secondary-gradient);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .restaurant-banner {
            background: var(--dark-gradient);
            padding: 4rem 0;
            position: relative;
            border-radius: 0 0 50px 50px;
            margin-bottom: 3rem;
            overflow: hidden;
        }

        .restaurant-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.08)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .restaurant-info {
            position: relative;
            color: white;
            z-index: 2;
        }

        .restaurant-logo {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 4px solid rgba(255,255,255,0.2);
        }

        .restaurant-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 0%, #ffccbc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .restaurant-address {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .menu-section {
            margin-bottom: 3rem;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .menu-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            border: none;
            height: 100%;
        }

        .menu-item:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--card-hover-shadow);
        }

        .menu-item-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 20px 20px 0 0;
        }

        .menu-item .card-body {
            padding: 1.5rem;
        }

        .menu-item-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #d84315;
        }

        .menu-item-description {
            color: #8d6e63;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .menu-item-price {
            font-size: 1.4rem;
            font-weight: 700;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .quantity-control {
            border-radius: 10px;
            border: 2px solid #ffcc80;
            max-width: 70px;
            text-align: center;
            font-weight: 500;
        }

        .quantity-control:focus {
            border-color: #ff9800;
            box-shadow: 0 0 0 3px rgba(255,152,0,0.2);
        }

        .add-to-cart-btn {
            background: var(--accent-gradient);
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.2rem;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255,171,64,0.4);
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,171,64,0.5);
            background: var(--secondary-gradient);
        }

        /* Enhanced Cart Styles */
        .cart-sidebar {
            position: sticky;
            top: 2rem;
            background: white;
            border-radius: 25px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 2px solid rgba(255,171,64,0.1);
        }

        .cart-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
        }

        .cart-count-badge .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
        }

        .cart-body {
            max-height: 60vh;
            overflow-y: auto;
            padding: 0;
        }

        .cart-body::-webkit-scrollbar {
            width: 6px;
        }

        .cart-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .cart-body::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 3px;
        }

        .cart-item {
            padding: 1.5rem;
            border-bottom: 1px solid #ffe0b2;
            transition: all 0.3s ease;
            position: relative;
        }

        .cart-item:hover {
            background: linear-gradient(135deg, #fff8f5 0%, #ffebe0 100%);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent-gradient);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .cart-item:hover::before {
            transform: scaleY(1);
        }

        .cart-item-img {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .cart-item-img img {
            width: 100%;
            height: 60px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .cart-item:hover .cart-item-img img {
            transform: scale(1.05);
        }

        .cart-item-title {
            font-weight: 600;
            color: #d84315;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .cart-item-price {
            color: #ff9800;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .cart-item-subtotal {
            color: #8d6e63;
            font-size: 0.8rem;
            margin-top: 0.2rem;
        }

        .quantity-controls {
            text-align: center;
        }

        .quantity-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff8f5;
            border-radius: 20px;
            padding: 0.3rem;
            box-shadow: 0 2px 8px rgba(255,171,64,0.2);
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            border: none;
        }

        .quantity-minus {
            background: #ffccbc;
            color: #d84315;
        }

        .quantity-plus {
            background: var(--accent-gradient);
            color: white;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .quantity-display {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
            color: #d84315;
            font-size: 0.9rem;
        }

        .remove-item-btn {
            color: #ff5722;
            text-decoration: none;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            display: inline-block;
        }

        .remove-item-btn:hover {
            background: #ffebee;
            color: #d32f2f;
            transform: translateY(-1px);
        }

        .cart-empty {
            padding: 2rem 1rem;
        }

        .empty-cart-icon {
            font-size: 3rem;
            color: #ffcc80;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .empty-cart-title {
            color: #d84315;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-cart-text {
            color: #8d6e63;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .empty-cart-animation {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .empty-cart-animation div {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-gradient);
            animation: bounce 1.4s ease-in-out infinite both;
        }

        .bounce-1 { animation-delay: -0.32s; }
        .bounce-2 { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .cart-summary {
            background: linear-gradient(135deg, #fff8f5 0%, #ffebe0 100%);
            padding: 1.5rem;
            margin: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .total-row {
            font-size: 1.1rem;
            color: #d84315;
        }

        .total-amount {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cart-actions {
            padding: 1.5rem;
            background: white;
        }

        .checkout-btn {
            background: var(--success-gradient);
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102,187,106,0.3);
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102,187,106,0.4);
            color: white;
        }

        .clear-cart-btn {
            background: transparent;
            border: 2px solid #ffab40;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            color: #ff9800;
            font-weight: 500;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .clear-cart-btn:hover {
            background: #ffab40;
            color: white;
            transform: translateY(-1px);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Loading animation */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .btn-loading {
            position: relative;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .cart-sidebar {
                margin-top: 2rem;
            }
            
            .cart-body {
                max-height: 50vh;
            }
            
            .cart-item {
                padding: 1rem;
            }
            
            .quantity-wrapper {
                flex-direction: column;
                gap: 0.3rem;
            }
        }

        /* Thêm vào phần style */
        .add-to-cart-main-btn {
            background: var(--warning-gradient);
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255,152,0,0.3);
        }

        .add-to-cart-main-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,152,0,0.4);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
            border: none;
            color: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(102,187,106,0.3);
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .alert-success .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
</head>

<body>
    
    <!-- Hidden field for user ID -->
    <?php if(!empty($_SESSION["user_id"])) { ?>
        <input type="hidden" id="chat-user-id" value="<?php echo $_SESSION['user_id']; ?>">
    <?php } ?>

    <div class="floating-elements"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" height="40" alt="Logo">
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
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Progress Steps -->
    <div class="container">
        <div class="step-container">
            <div class="row">
                <div class="col-md-4 step-item">
                    <div class="step-number">
                        <i class="fas fa-store"></i>
                    </div>
                    <h6 class="fw-semibold">Chọn nhà hàng</h6>
                    <small class="text-muted">Tìm nhà hàng yêu thích</small>
                </div>
                <div class="col-md-4 step-item active">
                    <div class="step-number">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h6 class="fw-semibold">Chọn món ăn</h6>
                    <small class="text-muted">Khám phá thực đơn</small>
                </div>
                <div class="col-md-4 step-item">
                    <div class="step-number">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h6 class="fw-semibold">Thanh toán</h6>
                    <small class="text-muted">Hoàn tất đơn hàng</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurant Banner -->
    <div class="restaurant-banner">
        <div class="container">
            <div class="restaurant-info">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <img src="admin/Res_img/<?php echo $restaurant['image']; ?>" 
                             class="restaurant-logo img-fluid" alt="Restaurant logo"
                             style="max-height: 150px;"
                             onerror="this.src='images/no-image.png'">
                    </div>
                    <div class="col-md-9">
                        <h1 class="restaurant-title"><?php echo $restaurant['title']; ?></h1>
                        <p class="restaurant-address mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo $restaurant['address']; ?>
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-star text-warning me-1"></i>4.5
                            </span>
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-clock me-1"></i>30-45 phút
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-shipping-fast me-1"></i>Miễn phí ship
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Content -->
    <div class="container mb-5">
        <?php if(isset($_GET['added']) && $_GET['added'] == '1'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Thành công!</strong> Món ăn đã được thêm vào giỏ hàng.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Menu Items -->
            <div class="col-lg-8">
                <div class="menu-section">
                    <div class="section-title">
                        <h2><i class="fas fa-utensils me-2"></i>Thực đơn hấp dẫn</h2>
                        <p class="text-muted">Khám phá những món ăn ngon nhất từ nhà hàng</p>
                    </div>
                    
                    <div class="row g-4">
                        <?php  
                        $stmt = $db->prepare("SELECT * FROM dishes WHERE rs_id=?");
                        $stmt->bind_param("i", $_GET['res_id']);
                        $stmt->execute();
                        $products = $stmt->get_result();
                        
                        if($products->num_rows > 0) {
                            while($product = $products->fetch_assoc()) {
                                // Check if image exists
                                $img_path = "admin/Res_img/dishes/" . $product['img'];
                                $default_img = "images/default-food.jpg";
                                
                                if (!file_exists($img_path) || empty($product['img'])) {
                                    $display_img = $default_img;
                                } else {
                                    $display_img = $img_path;
                                }
                        ?>
                        <div class="col-md-6">
                            <div class="menu-item card h-100">
                                <img src="<?php echo $display_img; ?>" 
                                     class="menu-item-img" alt="<?php echo htmlspecialchars($product['title']); ?>"
                                     onerror="this.src='<?php echo $default_img; ?>'">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="menu-item-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                                    <p class="menu-item-description flex-grow-1">
                                        <?php echo htmlspecialchars($product['slogan']); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <h6 class="menu-item-price mb-0">
                                            <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ
                                        </h6>
                                        <form class="add-to-cart-form d-flex align-items-center gap-2">
                                            <input type="hidden" name="product_id" value="<?php echo $product['d_id']; ?>">
                                            <input type="number" name="quantity" value="1" min="1"
                                                   class="form-control quantity-control">
                                            <button type="submit" class="add-to-cart-btn">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            }
                        } else {
                        ?>
                        <div class="col-12">
                            <div class="empty-menu">
                                <i class="fas fa-utensils"></i>
                                <h4 class="text-muted">Chưa có món ăn nào</h4>
                                <p class="text-muted">Nhà hàng đang cập nhật thực đơn. Vui lòng quay lại sau!</p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Cart Sidebar -->
            <div class="col-lg-4">
                <div class="cart-sidebar">
                    <div class="cart-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng
                            </h5>
                            <div class="cart-count-badge">
                                <span class="badge bg-light text-dark rounded-pill" id="cart-count">
                                    <?php 
                                    $total_items = 0;
                                    if(isset($_SESSION["cart_item"])) {
                                        foreach($_SESSION["cart_item"] as $item) {
                                            $total_items += $item["quantity"];
                                        }
                                    }
                                    echo $total_items;
                                    ?> món
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cart-body" id="cart-body">
                        <?php
                        $item_total = 0;
                        if(isset($_SESSION["cart_item"]) && count($_SESSION["cart_item"]) > 0) {
                            foreach($_SESSION["cart_item"] as $item) {
                                // Use placeholder image service if no image
                                $img_path = "admin/Res_img/dishes/" . $item['img'];
                                $default_img = "https://via.placeholder.com/100x60/ff9800/white?text=Món+ăn";
                                
                                if (!file_exists($img_path) || empty($item['img'])) {
                                    $display_img = $default_img;
                                } else {
                                    $display_img = $img_path;
                                }
                        ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-3">
                                    <div class="cart-item-img">
                                        <img src="<?php echo $display_img; ?>" 
                                             class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['title']); ?>"
                                             onerror="this.src='<?php echo $default_img; ?>'">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6 class="cart-item-title mb-1"><?php echo htmlspecialchars($item["title"]); ?></h6>
                                    <div class="cart-item-price">
                                        <?php echo number_format($item["price"], 0, ',', '.'); ?> VNĐ
                                    </div>
                                    <div class="cart-item-subtotal">
                                        Subtotal: <strong><?php echo number_format($item["price"] * $item["quantity"], 0, ',', '.'); ?> VNĐ</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="quantity-controls">
                                        <div class="quantity-wrapper">
                                            <a href="#" class="quantity-btn quantity-minus" data-action="decrease" data-id="<?php echo $item["d_id"]; ?>">
                                                <i class="fas fa-minus"></i>
                                            </a>
                                            <span class="quantity-display"><?php echo $item["quantity"]; ?></span>
                                            <a href="#" class="quantity-btn quantity-plus" data-action="increase" data-id="<?php echo $item["d_id"]; ?>">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                        <a href="#" class="remove-item-btn mt-2" data-action="remove" data-id="<?php echo $item["d_id"]; ?>">
                                            <i class="fas fa-trash me-1"></i>Xóa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                                $item_total += ($item["price"] * $item["quantity"]);
                            }
                        } else {
                        ?>
                        <div class="cart-empty">
                            <div class="text-center py-4">
                                <div class="empty-cart-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h6 class="empty-cart-title">Giỏ hàng trống</h6>
                                <p class="empty-cart-text">Hãy thêm những món ăn yêu thích của bạn!</p>
                                <div class="empty-cart-animation">
                                    <div class="bounce-1"></div>
                                    <div class="bounce-2"></div>
                                    <div class="bounce-3"></div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <div id="cart-summary-section" style="<?php echo $item_total > 0 ? '' : 'display: none;'; ?>">
                        <div class="cart-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal"><?php echo number_format($item_total, 0, ',', '.'); ?> VNĐ</span>
                            </div>
                            <div class="summary-row">
                                <span>Phí giao hàng:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr class="my-2">
                            <div class="summary-row total-row">
                                <span class="fw-bold">Tổng cộng:</span>
                                <span class="fw-bold total-amount" id="total-amount"><?php echo number_format($item_total, 0, ',', '.'); ?> VNĐ</span>
                            </div>
                        </div>
                        
                        <div class="cart-actions">
                            <div class="row g-2">
                                <div class="col-12">
                                    <a href="checkout.php?res_id=<?php echo $_GET['res_id']; ?>&action=check" 
                                       class="checkout-btn">
                                        <i class="fas fa-credit-card me-2"></i>Thanh toán ngay
                                    </a>
                                </div>
                                <div class="col-12">
                                    <a href="cart.php" class="add-to-cart-main-btn">
                                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                    </a>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="clear-cart-btn" data-action="clear">
                                        <i class="fas fa-trash me-2"></i>Xóa tất cả
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "include/footer.php" ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // AJAX Cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart form
            document.querySelectorAll('.add-to-cart-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const btn = this.querySelector('.add-to-cart-btn');
                    const originalHTML = btn.innerHTML;
                    
                    // Show loading
                    btn.classList.add('btn-loading');
                    btn.innerHTML = '';
                    
                    const formData = new FormData();
                    formData.append('ajax', '1');
                    formData.append('action', 'add');
                    formData.append('id', this.querySelector('input[name="product_id"]').value);
                    formData.append('quantity', this.querySelector('input[name="quantity"]').value);
                    
                    fetch('dishes.php?res_id=<?php echo $_GET["res_id"]; ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            updateCart(data);
                            
                            // Success animation
                            btn.classList.remove('btn-loading');
                            btn.innerHTML = '<i class="fas fa-check"></i>';
                            
                            setTimeout(() => {
                                btn.innerHTML = originalHTML;
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.classList.remove('btn-loading');
                        btn.innerHTML = originalHTML;
                    });
                });
            });
            
            // Cart actions (quantity buttons, remove, clear)
            document.addEventListener('click', function(e) {
                if(e.target.closest('[data-action]')) {
                    e.preventDefault();
                    
                    const element = e.target.closest('[data-action]');
                    const action = element.dataset.action;
                    const id = element.dataset.id;
                    
                    // Show confirmation for destructive actions
                    if(action === 'clear') {
                        if(!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
                            return;
                        }
                    }
                    
                    if(action === 'remove') {
                        if(!confirm('Bạn có chắc muốn xóa món này?')) {
                            return;
                        }
                    }
                    
                    // Show loading for clear button
                    if(action === 'clear') {
                        element.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xóa...';
                        element.disabled = true;
                    }
                    
                    const formData = new FormData();
                    formData.append('ajax', '1');
                    formData.append('action', action);
                    if(id) formData.append('id', id);
                    
                    fetch('dishes.php?res_id=<?php echo $_GET["res_id"]; ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            updateCart(data);
                            
                            // Show success message for clear action
                            if(data.action === 'clear') {
                                showNotification('Đã xóa toàn bộ giỏ hàng!', 'success');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Có lỗi xảy ra!', 'error');
                    })
                    .finally(() => {
                        // Reset clear button
                        if(action === 'clear') {
                            element.innerHTML = '<i class="fas fa-trash me-2"></i>Xóa tất cả';
                            element.disabled = false;
                        }
                    });
                }
            });
        });
        
        function updateCart(data) {
            document.getElementById('cart-count').textContent = data.total_items + ' món';
            document.getElementById('cart-body').innerHTML = data.cart_html;
            
            if(data.item_total > 0) {
                document.getElementById('subtotal').textContent = data.formatted_total + ' VNĐ';
                document.getElementById('total-amount').textContent = data.formatted_total + ' VNĐ';
                document.getElementById('cart-summary-section').style.display = 'block';
            } else {
                document.getElementById('cart-summary-section').style.display = 'none';
            }
        }
        
        // Show notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
            `;
            
            // Add notification styles
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'var(--success-gradient)' : 'var(--warning-gradient)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                z-index: 9999;
                transform: translateX(400px);
                transition: transform 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Hide notification
            setTimeout(() => {
                notification.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    <!-- Chatbox JS -->
    <script src="food-chatbox/assets/js/chatbox.js"></script>
</body>
</html>