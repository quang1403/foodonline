<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["res_admin_id"])) {
    echo '<div class="alert alert-danger">Bạn chưa đăng nhập.</div>';
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if($order_id <= 0) {
    echo '<div class="alert alert-danger">Mã đơn hàng không hợp lệ.</div>';
    exit();
}

// Lấy thông tin đơn hàng
$query = mysqli_query($db, "SELECT uo.*, u.username, u.phone, u.email FROM users_orders uo JOIN users u ON uo.u_id = u.u_id WHERE uo.o_id='$order_id'");
if(!mysqli_num_rows($query)) {
    echo '<div class="alert alert-danger">Không tìm thấy đơn hàng.</div>';
    exit();
}
$row = mysqli_fetch_assoc($query);

// Hiển thị thông tin chi tiết
?>
<div class="row g-3">
    <div class="col-md-6">
        <h6 class="fw-bold">Thông tin khách hàng</h6>
        <ul class="list-unstyled mb-3">
            <li><i class="fas fa-user me-2"></i> Tên: <b><?= htmlspecialchars($row['username']) ?></b></li>
            <li><i class="fas fa-phone me-2"></i> SĐT: <b><?= htmlspecialchars($row['phone']) ?></b></li>
            <li><i class="fas fa-envelope me-2"></i> Email: <b><?= htmlspecialchars($row['email']) ?></b></li>
            <li><i class="fas fa-map-marker-alt me-2"></i> Địa chỉ: <b><?= htmlspecialchars($row['address']) ?></b></li>
        </ul>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold">Thông tin đơn hàng</h6>
        <ul class="list-unstyled mb-3">
            <li><i class="fas fa-utensils me-2"></i> Món ăn: <b><?= htmlspecialchars($row['title']) ?></b></li>
            <li><i class="fas fa-sort-numeric-up me-2"></i> Số lượng: <b><?= $row['quantity'] ?></b></li>
            <li><i class="fas fa-money-bill-wave me-2"></i> Giá: <b><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</b></li>
            <li><i class="fas fa-calendar-alt me-2"></i> Thời gian đặt: <b><?= date('d/m/Y H:i', strtotime($row['date'])) ?></b></li>
        </ul>
    </div>
</div>
<hr>
<div>
    <h6 class="fw-bold">Trạng thái đơn hàng</h6>
    <?php
    $status = $row['status'];
    $pending_status = $row['pending_status'];
    $badge_class = '';
    $status_text = '';
    $icon = '';
    switch($status) {
        case '':
        case 'NULL':
            $badge_class = 'bg-info';
            $status_text = 'Chờ xác nhận';
            $icon = 'clock';
            break;
        case 'preparing':
            $badge_class = 'bg-secondary';
            $status_text = 'Đang chuẩn bị';
            $icon = 'hourglass-half';
            break;
        case 'prepared':
            $badge_class = 'bg-primary';
            $status_text = 'Đã chuẩn bị';
            $icon = 'check';
            break;
        case 'in process':
            $badge_class = 'bg-warning';
            $status_text = 'Đang giao';
            $icon = 'motorcycle';
            break;
        case 'closed':
            $badge_class = 'bg-success';
            $status_text = 'Đã giao';
            $icon = 'check-circle';
            break;
        case 'rejected':
            $badge_class = 'bg-danger';
            $status_text = 'Đã hủy';
            $icon = 'times-circle';
            break;
    }
    ?>
    <span class="badge <?= $badge_class ?> status-badge">
        <i class="fas fa-<?= $icon ?> me-1"></i><?= $status_text ?>
    </span>
    <?php if(!empty($pending_status) && $pending_status != $status): ?>
        <?php
        $pending_badge_class = '';
        $pending_status_text = '';
        $pending_icon = '';
        switch($pending_status) {
            case 'preparing':
                $pending_badge_class = 'bg-secondary';
                $pending_status_text = 'Đang chuẩn bị';
                $pending_icon = 'hourglass-half';
                break;
            case 'prepared':
                $pending_badge_class = 'bg-primary';
                $pending_status_text = 'Đã chuẩn bị';
                $pending_icon = 'check';
                break;
            case 'in process':
                $pending_badge_class = 'bg-warning';
                $pending_status_text = 'Đang giao';
                $pending_icon = 'motorcycle';
                break;
            case 'closed':
                $pending_badge_class = 'bg-success';
                $pending_status_text = 'Đã giao';
                $pending_icon = 'check-circle';
                break;
            case 'rejected':
                $pending_badge_class = 'bg-danger';
                $pending_status_text = 'Đã hủy';
                $pending_icon = 'times-circle';
                break;
        }
        ?>
        <br><small class="text-muted">Chờ phê duyệt:</small>
        <span class="badge <?= $pending_badge_class ?> status-badge">
            <i class="fas fa-<?= $pending_icon ?> me-1"></i><?= $pending_status_text ?>
        </span>
    <?php endif; ?>
</div>
