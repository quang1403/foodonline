<?php
header('Content-Type: application/json; charset=utf-8');

// Lấy số tiền từ URL
$amount = isset($_GET['total']) ? intval($_GET['total']) : 0;

if ($amount <= 0) {
    echo json_encode(["error" => "Số tiền không hợp lệ!"]);
    exit;
}

// ===== THÔNG TIN TÀI KHOẢN MB =====
$bank = "MB";                   
$account = "0362782295";         
$accountName = "NGUYEN MINH QUANG";   

// Tạo link QR VietQR
$qrUrl = "https://img.vietqr.io/image/{$bank}-{$account}-compact2.png"
       . "?amount={$amount}"
       . "&addInfo=" . urlencode("Thanh toan don hang")
       . "&accountName=" . urlencode($accountName);

// Trả về JSON cho front-end
echo json_encode([
    "qr_path" => $qrUrl
]);
?>
