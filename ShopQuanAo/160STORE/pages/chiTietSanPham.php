<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // phải đầu file

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

// Lấy id sản phẩm từ URL
$id_SP = isset($_GET['id']) ? $_GET['id'] : '';
if (!$id_SP) {
    echo "Sản phẩm không tồn tại";
    exit;
}

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT sp.*, dm.ten_Danh_Muc 
                        FROM san_pham sp 
                        LEFT JOIN danh_muc dm ON sp.id_DM = dm.id_DM
                        WHERE sp.id_SP = :id_SP");
$stmt->bindParam(':id_SP', $id_SP);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Sản phẩm không tồn tại";
    exit;
}

// Lấy biến thể sản phẩm (size, màu)
$stmt2 = $conn->prepare("SELECT * FROM bien_the_san_pham WHERE id_SP = :id_SP");
$stmt2->bindParam(':id_SP', $id_SP);
$stmt2->execute();
$variants = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Xử lý form khi click "Thêm vào giỏ hàng" hoặc "Mua ngay"
$added = false; // flag thông báo thêm giỏ hàng

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_size = $_POST['size'] ?? '';
    $selected_color = $_POST['color'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    // Kiểm tra giỏ hàng của user
    $stmt = $conn->prepare("SELECT * FROM gio_hang WHERE id_ND = :id_ND");
    $stmt->bindParam(':id_ND', $user_id);
    $stmt->execute();
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        // Tạo id_GH mới bằng PHP
        $stmtMax = $conn->query("SELECT MAX(id_GH) AS max_id FROM gio_hang");
        $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'];
        $newCartId = $maxId ? $maxId + 1 : 1;

        // Tạo giỏ hàng mới
        $stmt = $conn->prepare("INSERT INTO gio_hang (id_GH, id_ND, ngay_Tao) VALUES (:id_GH, :id_ND, NOW())");
        $stmt->execute([
            ':id_GH' => $newCartId,
            ':id_ND' => $user_id
        ]);
        $cart_id = $newCartId;
    } else {
        $cart_id = $cart['id_GH'];
    }

    // Thêm chi tiết giỏ hàng
    $stmt = $conn->prepare("INSERT INTO gio_hang_chi_tiet (id_GH, id_SP, so_Luong, ten_san_pham, mau_sac, kich_Thuoc) 
                            VALUES (:id_GH, :id_SP, :so_Luong, :ten_san_pham, :mau_sac, :kich_Thuoc)");
    $stmt->execute([
        ':id_GH' => $cart_id,
        ':id_SP' => $id_SP,
        ':so_Luong' => $quantity,
        ':ten_san_pham' => $product['ten_San_Pham'],
        ':mau_sac' => $selected_color,
        ':kich_Thuoc' => $selected_size
    ]);

    if (isset($_POST['buy_now'])) {
        header("Location: /ShopQuanAo/160STORE/pages/checkout.php");
        exit;
    } else {
        $added = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['ten_San_Pham']) ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-detail img { max-width: 100%; border-radius: 8px; }
        .variant-select { max-width: 200px; margin-right: 15px; }
        .added-msg { position: fixed; top: 80px; right: 20px; z-index: 1000; }
    </style>
</head>
<body>
<?php include('../layouts/header.php'); ?>
<?php include('../layouts/navbar.php'); ?>
<?php include('../layouts/breadcrumb_detail.php'); ?>

<div class="container my-5">
    <div class="row g-4">
        <!-- Hình ảnh -->
        <div class="col-md-5">

            <img src="<?=
            stripos($product['hinh_Anh'], 'http') === 0
                ? htmlspecialchars($product['hinh_Anh'])
                : 'http://' . $_SERVER['HTTP_HOST'] . '/ShopQuanAo/160STORE/pages/admin/' . htmlspecialchars($product['hinh_Anh'])
            ?>"
                 alt="<?= htmlspecialchars($product['ten_San_Pham']) ?>"
                 class="img-fluid">

        </div>
        <!-- Thông tin sản phẩm -->
        <div class="col-md-7">
            <h2><?= htmlspecialchars($product['ten_San_Pham']) ?></h2>
            <p class="text-muted mb-2"><strong>Danh mục:</strong> <?= htmlspecialchars($product['ten_Danh_Muc']) ?></p>
            <h4 class="text-danger"><?= number_format($product['gia_Ban']) ?> VNĐ</h4>
            <p><?= htmlspecialchars($product['mo_Ta']) ?></p>

            <?php if ($added): ?>
                <div class="alert alert-success added-msg" role="alert">
                    ✅ Đã thêm vào giỏ hàng thành công!
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="d-flex flex-column flex-md-row align-items-start gap-2 mt-3 mb-4">
                    <select name="color" class="form-select variant-select" required>
                        <option value="">--Chọn màu--</option>
                        <?php foreach ($variants as $v): ?>
                            <?php if ($v['mau_Sac']): ?>
                                <option value="<?= htmlspecialchars($v['mau_Sac']) ?>"><?= htmlspecialchars($v['mau_Sac']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
    
                    <select name="size" class="form-select variant-select" required>
                        <option value="">--Chọn size--</option>
                        <?php foreach ($variants as $v): ?>
                            <?php if ($v['kich_Thuoc']): ?>
                                <option value="<?= htmlspecialchars($v['kich_Thuoc']) ?>"><?= htmlspecialchars($v['kich_Thuoc']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
    
                    <input type="number" name="quantity" value="1" min="1" class="form-control variant-select" style="max-width:100px;" required>
                </div>

                <div class="d-flex flex-column flex-md-row gap-2 mt-2 mt-md-0">
                    <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ hàng</button>
                    <button type="submit" name="buy_now" class="btn btn-success">Mua ngay</button>
                </div>
            </form>
        </div>
    </div>
    <?php include('../pages/goiYSanPham.php') ?>
</div>

<!-- Bootstrap JS (optional, cho dropdown, modal...) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Ẩn thông báo sau 3 giây
    const msg = document.querySelector('.added-msg');
    if(msg){
        setTimeout(() => msg.remove(), 3000);
    }
</script>

<?php include('../layouts/footer.php'); ?>
</body>
</html>
