<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once('../includes/database.php');
require_once('../includes/config.php');

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Kiểm tra login
if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập trước";
    header("Location: /ShopQuanAo/160STORE/login.php");
    exit;
}

$user_id = $_SESSION['user']['id_ND'];

// Lấy giỏ hàng của user
$stmt = $conn->prepare("SELECT * FROM gio_hang WHERE id_ND = :id_ND");
$stmt->bindParam(':id_ND', $user_id);
$stmt->execute();
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    die("Giỏ hàng của bạn đang trống!");
}

$cart_id = $cart['id_GH'];

// Lấy chi tiết giỏ hàng
$stmt2 = $conn->prepare("SELECT * FROM gio_hang_chi_tiet WHERE id_GH = :id_GH");
$stmt2->bindParam(':id_GH', $cart_id);
$stmt2->execute();
$cart_items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Lấy giá bán và tên sản phẩm
$total = 0;
foreach ($cart_items as $k => $item) {
    $stmt3 = $conn->prepare("SELECT ten_San_Pham, gia_Ban FROM san_pham WHERE id_SP = :id_SP");
    $stmt3->bindParam(':id_SP', $item['id_SP']);
    $stmt3->execute();
    $product = $stmt3->fetch(PDO::FETCH_ASSOC);
    $cart_items[$k]['ten_san_pham'] = $product['ten_San_Pham'];
    $cart_items[$k]['gia_Ban'] = $product['gia_Ban'];
    $total += $product['gia_Ban'] * $item['so_Luong'];
}

// Xử lý mã giảm giá (voucher)
$discount_amount = $_SESSION['discount_amount'] ?? 0;
$voucher_code = $_SESSION['voucher_code'] ?? null;
$voucher_msg = '';

if (isset($_POST['apply_voucher'])) {
    $code = trim($_POST['ma_giam_gia']);
    $stmt = $conn->prepare("SELECT * FROM ma_giam_gia WHERE ma_Giam_Gia = :code AND trang_Thai='Đang hoạt động'");
    $stmt->execute([':code'=>$code]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voucher) {
        $voucher_msg = "Mã giảm giá không hợp lệ hoặc đã hết hạn!";
        unset($_SESSION['discount_amount'], $_SESSION['voucher_code']);
    } else {
        $min_total = floatval($voucher['gia_Tri_Toi_Thieu']);
        $now = date('Y-m-d H:i:s');
        if ($total < $min_total) {
            $voucher_msg = "Đơn hàng phải ≥ ".number_format($min_total,0,',','.')." VNĐ để áp dụng mã này.";
            unset($_SESSION['discount_amount'], $_SESSION['voucher_code']);
        } elseif ($now < $voucher['ngay_Bat_Dau'] || $now > $voucher['ngay_Ket_Thuc']) {
            $voucher_msg = "Mã giảm giá chưa tới hạn hoặc đã hết hạn!";
            unset($_SESSION['discount_amount'], $_SESSION['voucher_code']);
        } else {
            // Tính giảm
            if ($voucher['loai_Giam'] == 'phan_tram') {
                $discount_amount = $total * floatval($voucher['gia_Tri_Giam'])/100;
            } else {
                $discount_amount = floatval($voucher['gia_Tri_Giam']);
            }
            $_SESSION['discount_amount'] = $discount_amount;
            $_SESSION['voucher_code'] = $code;
            $voucher_msg = "Áp dụng thành công mã {$code}, giảm ".number_format($discount_amount,0,',','.')." VNĐ!";
        }
    }
}

// Xử lý checkout
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $dia_chi = trim($_POST['dia_chi'] ?? '');

    if (!$dia_chi) {
        $stmt = $conn->prepare("SELECT dia_Chi FROM dia_chi_giao_hang WHERE id_ND = :id_ND LIMIT 1");
        $stmt->bindParam(':id_ND', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $dia_chi = $row['dia_Chi'] ?? '';
    }

    if (!$dia_chi) {
        $msg = "Bạn cần nhập địa chỉ giao hàng!";
    } else {
        $stmt = $conn->query("SELECT MAX(id_DH) AS max_id FROM don_hang");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $new_order_id = ($row['max_id'] ?? 0) + 1;

        // Thành tiền sau giảm
        $final_total = $total - $discount_amount;

        $stmt = $conn->prepare("INSERT INTO don_hang 
            (id_DH, id_ND, ngay_Dat, tong_Tien, trang_Thai, dia_Chi_Giao, ma_Giam_Gia)
            VALUES (:id_DH, :id_ND, NOW(), :tong_Tien, 'Chờ xác nhận', :dia_Chi_Giao, :ma_Giam_Gia)");
        $stmt->execute([
            ':id_DH'=>$new_order_id,
            ':id_ND'=>$user_id,
            ':tong_Tien'=>$final_total,
            ':dia_Chi_Giao'=>$dia_chi,
            ':ma_Giam_Gia'=>$voucher_code
        ]);

        // Insert chi tiết hóa đơn
        $stmt = $conn->query("SELECT MAX(id_CTHD) AS max_id FROM chi_tiet_hoa_don");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cthd_id = ($row['max_id'] ?? 0) + 1;

        $stmt_insert_cthd = $conn->prepare("
            INSERT INTO chi_tiet_hoa_don
            (id_CTHD, id_DH, id_SP, so_Luong, gia_Ban, mau_sac, kich_Thuoc)
            VALUES (:id_CTHD, :id_DH, :id_SP, :so_Luong, :gia_Ban, :mau_sac, :kich_Thuoc)
        ");
        foreach ($cart_items as $item) {
            $stmt_insert_cthd->execute([
                ':id_CTHD'=>$cthd_id++,
                ':id_DH'=>$new_order_id,
                ':id_SP'=>$item['id_SP'],
                ':so_Luong'=>$item['so_Luong'],
                ':gia_Ban'=>$item['gia_Ban'],
                ':mau_sac'=>$item['mau_sac'] ?? '',
                ':kich_Thuoc'=>$item['kich_Thuoc'] ?? ''
            ]);
        }

        // Xóa giỏ hàng
        $stmt = $conn->prepare("DELETE FROM gio_hang_chi_tiet WHERE id_GH = :id_GH");
        $stmt->bindParam(':id_GH', $cart_id);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM gio_hang WHERE id_GH = :id_GH");
        $stmt->bindParam(':id_GH', $cart_id);
        $stmt->execute();

        $msg = "Thanh toán thành công! Đơn hàng của bạn đã được tạo.";
        $cart_items = [];
        $total = 0;
        $discount_amount = 0;
        $voucher_code = null;
        unset($_SESSION['discount_amount'], $_SESSION['voucher_code']);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('../layouts/header.php'); ?>
<?php include('../layouts/navbar.php'); ?>

<div class="container mt-4 mb-5">
    <h2>Thanh toán</h2>

    <?php if($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if($voucher_msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($voucher_msg) ?></div>
    <?php endif; ?>

    <?php if(!$cart_items): ?>
        <p>Giỏ hàng trống.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Màu sắc</th>
                <th>Size</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['ten_san_pham']) ?></td>
                    <td><?= htmlspecialchars($item['mau_sac']) ?></td>
                    <td><?= htmlspecialchars($item['kich_Thuoc']) ?></td>
                    <td><?= number_format($item['gia_Ban'],0,',','.') ?> VNĐ</td>
                    <td><?= $item['so_Luong'] ?></td>
                    <td><?= number_format($item['gia_Ban'] * $item['so_Luong'],0,',','.') ?> VNĐ</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mb-3">
            <label>Mã giảm giá:</label>
            <form method="POST" class="d-flex mb-2">
                <input type="text" name="ma_giam_gia" class="form-control me-2" placeholder="Nhập mã giảm giá">
                <button type="submit" name="apply_voucher" class="btn btn-success">Áp dụng</button>
            </form>
        </div>

        <h4>Tổng tiền: <?= number_format($total,0,',','.') ?> VNĐ</h4>
        <?php if($discount_amount>0): ?>
            <h5>Giảm: -<?= number_format($discount_amount,0,',','.') ?> VNĐ</h5>
            <h4>Thành tiền cuối: <?= number_format($total - $discount_amount,0,',','.') ?> VNĐ</h4>
        <?php endif; ?>

        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label>Địa chỉ giao hàng</label>
                <input type="text" name="dia_chi" class="form-control"
                       value="<?= htmlspecialchars($_SESSION['user']['dia_chi'] ?? '') ?>" required>
            </div>
            <button type="submit" name="checkout" class="btn btn-primary">Thanh toán</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../layouts/footer.php'); ?>
</body>
</html>
