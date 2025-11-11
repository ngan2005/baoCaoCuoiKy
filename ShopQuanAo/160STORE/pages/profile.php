<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user = $_SESSION['user'];
$msg = "";

// LẤY THÔNG TIN MỚI NHẤT
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE id_ND = ?");
$stmt->execute([$user['id_ND']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// CẬP NHẬT THÔNG TIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_Ten = trim($_POST['ho_Ten']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $dia_Chi = trim($_POST['dia_Chi']);
    $mat_Khau = trim($_POST['mat_Khau']);

    try {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_Ten=?, email=?, sdt=?, dia_Chi=?, mat_Khau=? WHERE id_ND=?");
        $stmt->execute([$ho_Ten, $email, $sdt, $dia_Chi, $mat_Khau, $user['id_ND']]);
        $msg = "<div class='msg success'>Cập nhật thành công!</div>";

        $_SESSION['user']['ho_Ten'] = $ho_Ten;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['sdt'] = $sdt;
        $_SESSION['user']['dia_Chi'] = $dia_Chi;
    } catch (Exception $e) {
        $msg = "<div class='msg error'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân | 160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar">
                <?= strtoupper(substr($user['ho_Ten'], 0, 1)) ?>
            </div>
            <h2><?= htmlspecialchars($user['ho_Ten']) ?></h2>
            <p><?= htmlspecialchars($user['email']) ?></p>
            <div class="role">
                <?= $user['vai_Tro'] === 'admin' ? 'QUẢN TRỊ VIÊN' : 'KHÁCH HÀNG' ?>
            </div>
        </div>

        <?= $msg ?>

        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" value="<?= htmlspecialchars($user['ten_Dang_Nhap']) ?>" readonly>
            </div>

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="ho_Ten" value="<?= htmlspecialchars($user['ho_Ten']) ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="sdt" value="<?= htmlspecialchars($user['sdt']) ?>">
            </div>

            <div class="form-group">
                <label>Địa chỉ nhận hàng</label>
                <input type="text" name="dia_Chi" value="<?= htmlspecialchars($user['dia_Chi']) ?>">
            </div>

            <div class="form-group">
                <label>Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" name="mat_Khau" placeholder="Nhập mật khẩu mới...">
            </div>

            <button type="submit" class="btn">
                LƯU THAY ĐỔI
            </button>
        </form>

        <a href="../TrangChu.php" class="back-link">
            Quay lại trang chủ
        </a>
    </div>
</div>
</body>
</html>