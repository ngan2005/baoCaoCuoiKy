<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/database.php');

$db = new Database();
$conn = $db->connect();

// Lấy sản phẩm thuộc danh mục Áo Thun (id_DM = 1)
$query = "SELECT * FROM san_pham WHERE id_DM = 1 AND trang_Thai = 'Còn hàng'";
$params = []; // mảng tham số cho PDO

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    // Thêm điều kiện search, không phân biệt hoa thường
    $query .= " AND LOWER(ten_San_Pham) LIKE ?";
    $params[] = '%' . mb_strtolower($search, 'UTF-8') . '%';
}

// Thực thi truy vấn với params
$stmt = $conn->prepare($query);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: xem giá trị search và kết quả
// echo '<pre>'; var_dump($search, $products); echo '</pre>';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Áo Thun Nam - 160Store</title>
  <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/stylee.css">
</head>
<body>
 <?php include('../layouts/header.php'); ?>
 <?php include('../layouts/navbar.php'); ?>
  <h2 style="text-align:center; margin:20px 0;">Danh Sách Áo Thun Nam</h2>
  <div class="product-list" style="display:flex; flex-wrap:wrap; justify-content:center;">
    <?php foreach ($products as $p): ?>
      <div class="product-card" style="margin:15px;">
        <span class="new-icon">Hàng Mới</span>
        <a href="chiTietSanPham.php?id=<?= $p['id_SP'] ?>">
            <img src="<?= !empty($p['hinh_Anh'])
                ? (preg_match('#^https?://#', $p['hinh_Anh'])
                    ? $p['hinh_Anh']
                    : '/ShopQuanAo/160STORE/pages/admin/' . $p['hinh_Anh'])
                : '/ShopQuanAo/160STORE/pages/admin/uploads/default.jpg'
            ?>" width="100" ">
          <div class="product-info">
            <h4><?= htmlspecialchars($p['ten_San_Pham']) ?></h4>
            <p class="price">
              Giá:
              <span class="new-price"><?= number_format($p['gia_Ban'], 0, ',', '.') ?>đ</span>
              <span class="old-price">~<?= number_format($p['gia_Goc'], 0, ',', '.') ?>đ</span>
            </p>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
  <!-- logo vận chuyển  hàng hóa-->
 <?php include('../layouts/footer.php'); ?>
</body>
</html>
