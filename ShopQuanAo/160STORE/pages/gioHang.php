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

// Xử lý cập nhật số lượng
if (isset($_POST['update_qty'])) {
    $id_ct = intval($_POST['id_GHCT']);
    $qty = max(1, intval($_POST['quantity']));
    $stmt = $conn->prepare("UPDATE gio_hang_chi_tiet SET so_Luong = :so_Luong WHERE id_GHCT = :id_GHCT");
    $stmt->execute([':so_Luong'=>$qty, ':id_GHCT'=>$id_ct]);
    header("Location: gioHang.php");
    exit;
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id_ct = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM gio_hang_chi_tiet WHERE id_GHCT = ?");
    $stmt->execute([$id_ct]);
    header("Location: gioHang.php");
    exit;
}

// Lấy giỏ hàng của user
$stmt = $conn->prepare("SELECT * FROM gio_hang WHERE id_ND = :id_ND");
$stmt->bindParam(':id_ND', $user_id);
$stmt->execute();
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

$cart_items = [];
$total = 0;

if ($cart) {
    $cart_id = $cart['id_GH'];
    $stmt2 = $conn->prepare("SELECT * FROM gio_hang_chi_tiet WHERE id_GH = :id_GH");
    $stmt2->bindParam(':id_GH', $cart_id);
    $stmt2->execute();
    $cart_items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $stmt3 = $conn->prepare("SELECT gia_Ban, hinh_Anh FROM san_pham WHERE id_SP = :id_SP");
        $stmt3->bindParam(':id_SP', $item['id_SP']);
        $stmt3->execute();
        $product = $stmt3->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $item['gia_Ban'] = $product['gia_Ban'];
            $item['hinh_Anh'] = $product['hinh_Anh'];
            $total += $product['gia_Ban'] * $item['so_Luong'];
            $cart_with_product[] = $item;
        }
    }
} else {
    $cart_with_product = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table img { max-width: 80px; border-radius: 5px; }
        .total-row { font-weight: bold; font-size: 1.2rem; }
        .empty-cart { text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
<?php include('../layouts/header.php'); ?>
<?php include('../layouts/navbar.php'); ?>

<div class="container my-5">
    <h2 class="mb-4">Giỏ Hàng Của Bạn</h2>

    <?php if (empty($cart_with_product)): ?>
        <div class="empty-cart alert alert-info">
            <i class="fas fa-shopping-cart fa-2x"></i>
            <p class="mt-2">Giỏ hàng của bạn đang trống.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                <tr>
                    <th>Hình</th>
                    <th>Tên sản phẩm</th>
                    <th>Màu sắc</th>
                    <th>Size</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cart_with_product as $item):
                    $line_total = $item['gia_Ban'] * $item['so_Luong'];
                    ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($item['hinh_Anh']) ?>" alt="<?= htmlspecialchars($item['ten_san_pham']) ?>"></td>
                        <td><?= htmlspecialchars($item['ten_san_pham']) ?></td>
                        <td><?= htmlspecialchars($item['mau_sac']) ?></td>
                        <td><?= htmlspecialchars($item['kich_Thuoc']) ?></td>
                        <td><?= number_format($item['gia_Ban'],0,',','.') ?> VNĐ</td>
                        <td>
                            <form method="POST" class="d-flex justify-content-center align-items-center">
                                <input type="number" name="quantity" value="<?= $item['so_Luong'] ?>" min="1" class="form-control form-control-sm w-50 me-2">
                                <input type="hidden" name="id_GHCT" value="<?= $item['id_GHCT'] ?>">
                                <button type="submit" name="update_qty" class="btn btn-sm btn-primary">Cập nhật</button>
                            </form>
                        </td>
                        <td><?= number_format($line_total,0,',','.') ?> VNĐ</td>
                        <td>
                            <a href="gioHang.php?delete=<?= $item['id_GHCT'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này khỏi giỏ?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="6" class="text-end">Tổng tiền:</td>
                    <td><?= number_format($total,0,',','.') ?> VNĐ</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-4">
            <form action="checkout.php" method="POST">
                <button type="submit" class="btn btn-success btn-lg">Thanh toán</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../layouts/footer.php'); ?>
</body>
</html>
