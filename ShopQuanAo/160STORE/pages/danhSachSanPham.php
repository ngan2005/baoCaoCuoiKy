<?php
// Bao gồm các file cấu hình và kết nối cơ sở dữ liệu
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/database.php');

// Tạo đối tượng Database và kết nối
$db = new Database();
$pdo = $db->connect();

// Lấy từ khóa search từ URL (nếu có)
$search = trim($_GET['search'] ?? '');
// Chuẩn bị câu SQL
$sql = "SELECT * FROM san_pham";  // Mặc định lấy tất cả sản phẩm
$params = [];

if ($search !== '') {
    // Nếu có từ khóa search, thêm điều kiện lọc
    $sql .= " WHERE ten_San_Pham LIKE ?";
    $params[] = "%$search%";  // Dùng LIKE với % để tìm gần đúng
}

// Thực thi truy vấn
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy tất cả sản phẩm
?>

<div class="product-list">
    <?php
    // Kiểm tra nếu biến $products không được khởi tạo hoặc rỗng
    if (empty($products)) {
        echo "<p>Không có sản phẩm nào.</p>";
    } else {
        // Hiển thị danh sách sản phẩm
        foreach ($products as $p):
    ?>
            <div class="product-card">
                <a href="pages/chiTietSanPham.php?id=<?= urlencode($p['id_SP']) ?>">
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
    <?php
        endforeach;
    }
    ?>

</div>


