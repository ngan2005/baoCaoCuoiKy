<?php
require_once '../../includes/database.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_Tro'] !== 'admin') {
    header("Location: ../dangNhap_DangKy.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = "";
$editing = false;
$product = [];

// Tạo thư mục uploads
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Lấy danh sách hỗ trợ
try {
    $categories = $conn->query("SELECT * FROM danh_muc ORDER BY ten_Danh_Muc")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

try {
    $discounts = $conn->query("SELECT * FROM ma_giam_gia ORDER BY ma_Giam_Gia")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $discounts = [];
}

// Xác định tên bảng
$tableName = 'san_pham';
try {
    $conn->query("SELECT 1 FROM san_pham LIMIT 1");
} catch (Exception $e) {
    try {
        $conn->query("SELECT 1 FROM sanpham LIMIT 1");
        $tableName = 'sanpham';
    } catch (Exception $e2) {
        die("Lỗi: Không tìm thấy bảng sản phẩm trong database.");
    }
}

// XÓA SẢN PHẨM
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        // Xóa variants nếu có
        try {
            $stmtVar = $conn->prepare("DELETE FROM variants WHERE id_SP = ?");
            $stmtVar->execute([$id]);
        } catch (Exception $e) {}
        
        $stmt = $conn->prepare("DELETE FROM $tableName WHERE id_SP = ?");
        $stmt->execute([$id]);
        $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa sản phẩm <strong>$id</strong>!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// LẤY THÔNG TIN SẢN PHẨM ĐỂ SỬA
if (isset($_GET['edit'])) {
    $id = trim($_GET['edit']);
    try {
        $stmt = $conn->prepare("SELECT * FROM $tableName WHERE id_SP = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $editing = true;
        } else {
            $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Không tìm thấy sản phẩm!</div>";
        }
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// THÊM/SỬA SẢN PHẨM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_SP = trim($_POST['id_SP'] ?? '');
    $ten_San_Pham = trim($_POST['ten_San_Pham'] ?? '');
    $gia_Ban = floatval($_POST['gia_Ban'] ?? 0);
    $gia_Goc = floatval($_POST['gia_Goc'] ?? 0);
    $mo_Ta = trim($_POST['mo_Ta'] ?? '');
    $id_DM = intval($_POST['id_DM'] ?? 0);
    $thuong_Hieu = trim($_POST['thuong_Hieu'] ?? '');
    $so_Luong_Ton = intval($_POST['so_Luong_Ton'] ?? 0);
    $trang_Thai = trim($_POST['trang_Thai'] ?? 'Còn hàng');
    $ma_Giam_Gia = trim($_POST['ma_Giam_Gia'] ?? '');
    $hinh_Anh = trim($_POST['link_hinh'] ?? '');

    $isUpdating = isset($_POST['sua']);
    $errors = [];
    
    // Validation
    if (empty($id_SP)) $errors[] = "Mã sản phẩm không được để trống.";
    if (empty($ten_San_Pham)) $errors[] = "Tên sản phẩm không được để trống.";
    if ($gia_Ban <= 0) $errors[] = "Giá bán phải lớn hơn 0.";
    if ($id_DM <= 0) $errors[] = "Vui lòng chọn danh mục hợp lệ.";
    
    // Kiểm tra trùng mã khi thêm mới
    if (!$isUpdating) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $tableName WHERE id_SP = ?");
        $stmt->execute([$id_SP]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Mã sản phẩm <strong>$id_SP</strong> đã tồn tại.";
        }
    }
    
    // Upload hình ảnh
    if (isset($_FILES['file_hinh']) && $_FILES['file_hinh']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . basename($_FILES['file_hinh']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['file_hinh']['tmp_name'], $targetPath)) {
            $hinh_Anh = "uploads/" . $fileName;
        } else {
            $errors[] = "Lỗi khi upload file hình ảnh.";
        }
    }
    
    if (!empty($errors)) {
        $msg = "<div class='msg error'><ul>" . implode("</li><li>", $errors) . "</ul></div>";
    } else {
        try {
            if ($isUpdating) {
                // CẬP NHẬT
                $sql = "UPDATE $tableName SET 
                    ten_San_Pham=?, gia_Ban=?, gia_Goc=?, mo_Ta=?, id_DM=?, 
                    thuong_Hieu=?, so_Luong_Ton=?, trang_Thai=?, ma_Giam_Gia=?, hinh_Anh=?
                    WHERE id_SP=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$ten_San_Pham, $gia_Ban, $gia_Goc, $mo_Ta, $id_DM, 
                               $thuong_Hieu, $so_Luong_Ton, $trang_Thai, $ma_Giam_Gia, $hinh_Anh, $id_SP]);
                $msg = "<div class='msg success'><i class='fas fa-check-circle'></i> Cập nhật sản phẩm thành công!</div>";
                $editing = false;
                $product = [];
            } else {
                $ngay_Tao = date('Y-m-d H:i:s'); // thời gian hiện tại

                $sql = "INSERT INTO $tableName (id_SP, ten_San_Pham, gia_Ban, gia_Goc, mo_Ta, id_DM, 
        thuong_Hieu, so_Luong_Ton, trang_Thai, ma_Giam_Gia, hinh_Anh, ngay_Tao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_SP, $ten_San_Pham, $gia_Ban, $gia_Goc, $mo_Ta, $id_DM,
                    $thuong_Hieu, $so_Luong_Ton, $trang_Thai, $ma_Giam_Gia, $hinh_Anh, $ngay_Tao]);

                $msg = "<div class='msg success'><i class='fas fa-check-circle'></i> Thêm sản phẩm thành công!</div>";
            }
        } catch (Exception $e) {
            $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// TÌM KIẾM
$search = trim($_GET['search'] ?? '');
$sql = "SELECT sp.*, dm.ten_Danh_Muc 
        FROM $tableName sp 
        LEFT JOIN danh_muc dm ON sp.id_DM = dm.id_DM
        WHERE sp.ten_San_Pham LIKE :s OR sp.id_SP LIKE :s
        ORDER BY sp.id_SP DESC";
$stmt = $conn->prepare($sql);
$stmt->execute(['s' => "%$search%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản phẩm | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h2 {
            color: #fff;
            font-size: 32px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        /* Messages */
        .msg {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }

        .msg.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .msg.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .msg ul {
            list-style: none;
            padding-left: 0;
        }

        .msg li {
            padding: 3px 0;
        }

        /* Search Bar */
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-search, .btn-refresh {
            padding: 12px 25px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-search:hover, .btn-refresh:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-refresh {
            background: #ff4757;
        }

        .btn-refresh:hover {
            background: #ee5a6f;
        }

        /* Form Card */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .form-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group input[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        /* Table */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        td {
            padding: 15px;
            font-size: 14px;
            color: #555;
        }

        .product-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #f0f0f0;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: #ffc107;
            color: #333;
        }

        .btn-edit:hover {
            background: #ffca2c;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .search-bar {
                flex-direction: column;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <h2><i class="fas fa-box"></i> Quản lý Sản phẩm</h2>
    <?= $msg ?>

    <!-- TÌM KIẾM -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="search-input" placeholder="Tìm kiếm mã hoặc tên sản phẩm..." 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn-search">
            <i class="fas fa-search"></i> Tìm kiếm
        </button>
        <a href="quanLySanPham.php" class="btn-refresh">
            <i class="fas fa-sync"></i> Làm mới
        </a>
    </form>

    <!-- FORM THÊM/SỬA -->
    <div class="form-card">
        <h3 class="form-title">
            <?= $editing ? '<i class="fas fa-edit"></i> Sửa sản phẩm' : '<i class="fas fa-plus"></i> Thêm sản phẩm mới' ?>
        </h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                
                <div class="form-group">
                    <label><i class="fas fa-barcode"></i> Mã sản phẩm *</label>
                    <input type="text" name="id_SP" value="<?= htmlspecialchars($product['id_SP'] ?? '') ?>" 
                           <?= $editing ? 'readonly' : 'required' ?> placeholder="VD: SP001">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Tên sản phẩm *</label>
                    <input type="text" name="ten_San_Pham" value="<?= htmlspecialchars($product['ten_San_Pham'] ?? '') ?>" 
                           required placeholder="Áo thun nam cổ tròn">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> Giá bán (VNĐ) *</label>
                    <input type="number" name="gia_Ban" value="<?= htmlspecialchars($product['gia_Ban'] ?? '') ?>" 
                           required placeholder="199000" min="0">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-money-bill"></i> Giá gốc (VNĐ)</label>
                    <input type="number" name="gia_Goc" value="<?= htmlspecialchars($product['gia_Goc'] ?? '') ?>" 
                           placeholder="299000" min="0">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-list"></i> Danh mục *</label>
                    <select name="id_DM" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id_DM'] ?>" <?= ($product['id_DM'] ?? '') == $c['id_DM'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['ten_Danh_Muc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-trademark"></i> Thương hiệu</label>
                    <input type="text" name="thuong_Hieu" value="<?= htmlspecialchars($product['thuong_Hieu'] ?? '') ?>" 
                           placeholder="160STORE">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-warehouse"></i> Số lượng tồn</label>
                    <input type="number" name="so_Luong_Ton" value="<?= htmlspecialchars($product['so_Luong_Ton'] ?? 0) ?>" 
                           placeholder="100" min="0">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-percent"></i> Mã giảm giá</label>
                    <select name="ma_Giam_Gia">
                        <option value="">-- Không áp dụng --</option>
                        <?php foreach ($discounts as $d): ?>
                            <option value="<?= htmlspecialchars($d['ma_Giam_Gia']) ?>" 
                                <?= ($product['ma_Giam_Gia'] ?? '') == $d['ma_Giam_Gia'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['ma_Giam_Gia']) ?> - <?= htmlspecialchars($d['mo_Ta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full">
                    <label><i class="fas fa-align-left"></i> Mô tả sản phẩm</label>
                    <textarea name="mo_Ta" rows="3" placeholder="Chất liệu cotton, thoáng mát..."><?= htmlspecialchars($product['mo_Ta'] ?? '') ?></textarea>
                </div>

                <div class="form-group full">
                    <label><i class="fas fa-image"></i> Hình ảnh sản phẩm</label>
                    <input type="file" name="file_hinh" accept="image/*">
                    <input type="text" name="link_hinh" placeholder="Hoặc nhập link ảnh..." 
                            value="<?= htmlspecialchars($product['hinh_Anh'] ?? '') ?>" style="margin-top:10px;">
                    <?php if ($editing && !empty($product['hinh_Anh'])): ?>
                        <small style="color:#888; margin-top:5px;">
                            Ảnh hiện tại: <a href="<?= htmlspecialchars($product['hinh_Anh']) ?>" target="_blank">Xem ảnh</a>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <?php if ($editing): ?>
                        <button type="submit" name="sua" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="quanLySanPham.php" class="btn btn-cancel">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    <?php else: ?>
                        <button type="submit" name="them" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm sản phẩm
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Ảnh</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Danh mục</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-inbox" style="font-size:32px;"></i><br>
                            Không tìm thấy sản phẩm nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['id_SP']) ?></strong></td>
                            <td><?= htmlspecialchars($p['ten_San_Pham']) ?></td>
                            <td>
                                <?php if (!empty($p['hinh_Anh'])): ?>
                                    <img src="<?= htmlspecialchars($p['hinh_Anh']) ?>" class="product-thumb" alt="Product">
                                <?php else: ?>
                                    <div style="width:60px;height:60px;background:#e0e0e0;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-image" style="color:#999;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= number_format($p['gia_Ban'], 0, ',', '.') ?>đ</strong></td>
                            <td>
                                <span style="padding:5px 10px; background:#e3f2fd; color:#1976d2; border-radius:5px; font-weight:600;">
                                    <?= $p['so_Luong_Ton'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['ten_Danh_Muc'] ?? '-') ?></td>
                            <td class="actions">
                                <a href="?edit=<?= urlencode($p['id_SP']) ?><?= $search ? '&search='.urlencode($search) : '' ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="?delete=<?= urlencode($p['id_SP']) ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm <?= htmlspecialchars($p['ten_San_Pham']) ?>?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
// Tự động ẩn thông báo sau 5 giây
setTimeout(function() {
    const msg = document.querySelector('.msg');
    if (msg) {
        msg.style.transition = 'opacity 0.5s';
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 500);
    }
}, 5000);
</script>

</body>
</html>