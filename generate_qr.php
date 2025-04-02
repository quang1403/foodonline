
<?php
// Lấy số tiền từ URL
$amount = isset($_GET['total']) ? trim($_GET['total']) : 0;

if (!$amount) {
    echo json_encode(["error" => "Số tiền không hợp lệ!"]);
    exit;
}

// Đường dẫn tới Python (cập nhật theo máy của bạn)
$pythonPath = "c:\\Users\\ASUS\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";
$scriptPath = __DIR__ . "/generate_qr.py";

// Chạy script Python
$output = shell_exec("$pythonPath $scriptPath " . escapeshellarg($amount));

if ($output) {
    echo json_encode(["qr_path" => trim($output)]);
} else {
    echo json_encode(["error" => "Không thể tạo mã QR!"]);
}
?>
