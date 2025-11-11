<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/database.php';


$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';
$editing = false;
$discount = [];

/* === TÌM KIẾM === */
$keyword = $_GET['search'] ?? '';
if ($keyword !== '') {
    $stmt = $conn->prepare("SELECT * FROM ma_giam_gia 
                            WHERE ma_Giam_Gia LIKE :kw 
                               OR mo_Ta LIKE :kw 
                               OR trang_Thai LIKE :kw
                            ORDER BY ngay_Bat_Dau DESC");
    $stmt->execute(['kw' => "%$keyword%"]);
    $discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $discounts = $conn->query("SELECT * FROM ma_giam_gia ORDER BY ngay_Bat_Dau DESC")->fetchAll(PDO::FETCH_ASSOC);
}

/* === SỬA === */
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM ma_giam_gia WHERE ma_Giam_Gia = ?");
    $stmt->execute([$id]);
    $discount = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($discount) $editing = true;
}

/* === XÓA === */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM ma_giam_gia WHERE ma_Giam_Gia = ?");
        $stmt->execute([$id]);
        $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa mã <strong>$id</strong>!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/* === THÊM / SỬA === */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_Giam_Gia = trim($_POST['ma_Giam_Gia']);
    $mo_Ta = trim($_POST['mo_Ta']);
    $gia_Tri_Giam = floatval($_POST['gia_Tri_Giam']);
    $dieu_Kien = trim($_POST['dieu_Kien']);
    $ngay_Bat_Dau = $_POST['ngay_Bat_Dau'] ?: null;
    $ngay_Ket_Thuc = $_POST['ngay_Ket_Thuc'] ?: null;
    $trang_Thai = $_POST['trang_Thai'] ?? 'Đang hoạt động';
    $gia_Tri_Toi_Thieu = floatval($_POST['gia_Tri_Toi_Thieu'] ?? 0);
    $loai_Giam = $_POST['loai_Giam'] ?? 'phan_tram';

    if ($ma_Giam_Gia === '' || $gia_Tri_Giam <= 0) {
        $msg = "<div class='msg error'><i class='fas fa-exclamation-triangle'></i> Mã và giá trị giảm không hợp lệ!</div>";
    } else {
        try {
            if (isset($_POST['them'])) {
                $stmt = $conn->prepare("INSERT INTO ma_giam_gia 
                    (ma_Giam_Gia, mo_Ta, gia_Tri_Giam, dieu_Kien, ngay_Bat_Dau, ngay_Ket_Thuc, trang_Thai, gia_Tri_Toi_Thieu, loai_Giam)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$ma_Giam_Gia, $mo_Ta, $gia_Tri_Giam, $dieu_Kien, $ngay_Bat_Dau, $ngay_Ket_Thuc, $trang_Thai, $gia_Tri_Toi_Thieu, $loai_Giam]);
                $msg = "<div class='msg success'><i class='fas fa-check-circle'></i> Thêm mã mới thành công!</div>";
            } elseif (isset($_POST['sua'])) {
                $stmt = $conn->prepare("UPDATE ma_giam_gia SET 
                    mo_Ta=?, gia_Tri_Giam=?, dieu_Kien=?, ngay_Bat_Dau=?, ngay_Ket_Thuc=?, trang_Thai=?, gia_Tri_Toi_Thieu=?, loai_Giam=?
                    WHERE ma_Giam_Gia=?");
                $stmt->execute([$mo_Ta, $gia_Tri_Giam, $dieu_Kien, $ngay_Bat_Dau, $ngay_Ket_Thuc, $trang_Thai, $gia_Tri_Toi_Thieu, $loai_Giam, $ma_Giam_Gia]);
                $msg = "<div class='msg success'><i class='fas fa-save'></i> Cập nhật <strong>$ma_Giam_Gia</strong> thành công!</div>";
                $editing = false;
            }
        } catch (Exception $e) {
            $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi SQL: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Mã Giảm Giá | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/Admin_css/style.css">
</head>
<body>

<div class="container">
    <h2>Quản lý Mã Giảm Giá</h2>
    <?= $msg ?>

    <!-- TÌM KIẾM -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="search-input" placeholder="Tìm theo mã, mô tả, trạng thái..." 
               value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit" class="btn-search">
            <i class="fas fa-search"></i> Tìm kiếm
        </button>
        <?php if ($keyword !== ''): ?>
            <a href="themMaGiamGia.php" class="btn-refresh">
                <i class="fas fa-sync"></i> Làm mới
            </a>
        <?php endif; ?>
    </form>

    <!-- FORM THÊM/SỬA -->
    <div class="form-card">
        <h3 class="form-title">
            <?= $editing ? '<i class="fas fa-edit"></i> Sửa mã giảm giá' : '<i class="fas fa-plus-circle"></i> Thêm mã mới' ?>
        </h3>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Mã giảm giá *</label>
                    <input type="text" name="ma_Giam_Gia" value="<?= htmlspecialchars($discount['ma_Giam_Gia'] ?? '') ?>" 
                           <?= $editing ? 'readonly' : 'required' ?> placeholder="VD: SALE50">
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <input type="text" name="mo_Ta" value="<?= htmlspecialchars($discount['mo_Ta'] ?? '') ?>" placeholder="Giảm 50% cho đơn từ 500k">
                </div>

                <div class="form-group">
                    <label>Giá trị giảm *</label>
                    <input type="number" step="0.01" name="gia_Tri_Giam" value="<?= htmlspecialchars($discount['gia_Tri_Giam'] ?? '') ?>" required placeholder="30">
                </div>

                <div class="form-group">
                    <label>Loại giảm</label>
                    <select name="loai_Giam">
                        <option value="phan_tram" <?= ($discount['loai_Giam'] ?? '') === 'phan_tram' ? 'selected' : '' ?>>Phần trăm (%)</option>
                        <option value="tien_mat" <?= ($discount['loai_Giam'] ?? '') === 'tien_mat' ? 'selected' : '' ?>>Tiền mặt (VNĐ)</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Điều kiện áp dụng</label>
                    <textarea name="dieu_Kien" rows="2" placeholder="Áp dụng cho đơn từ 300.000đ, tối đa 100.000đ"><?= htmlspecialchars($discount['dieu_Kien'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Ngày bắt đầu</label>
                    <input type="datetime-local" name="ngay_Bat_Dau" 
                           value="<?= isset($discount['ngay_Bat_Dau']) ? date('Y-m-d\TH:i', strtotime($discount['ngay_Bat_Dau'])) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Ngày kết thúc</label>
                    <input type="datetime-local" name="ngay_Ket_Thuc" 
                           value="<?= isset($discount['ngay_Ket_Thuc']) ? date('Y-m-d\TH:i', strtotime($discount['ngay_Ket_Thuc'])) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Giá trị tối thiểu</label>
                    <input type="number" step="0.01" name="gia_Tri_Toi_Thieu" value="<?= htmlspecialchars($discount['gia_Tri_Toi_Thieu'] ?? '') ?>" placeholder="0">
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="trang_Thai">
                        <option value="Đang hoạt động" <?= ($discount['trang_Thai'] ?? '') === 'Đang hoạt động' ? 'selected' : '' ?>>Đang hoạt động</option>
                        <option value="Ngưng" <?= ($discount['trang_Thai'] ?? '') === 'Ngưng' ? 'selected' : '' ?>>Ngưng</option>
                    </select>
                </div>

                <div class="form-actions">
                    <?php if ($editing): ?>
                        <button type="submit" name="sua" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="quanLyMaGiamGia.php" class="btn btn-cancel">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    <?php else: ?>
                        <button type="submit" name="them" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm mới
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
                    <th>Mã</th>
                    <th>Mô tả</th>
                    <th>Giá trị</th>
                    <th>Loại</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($discounts)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-inbox"></i> Không có mã giảm giá nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($discounts as $d): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($d['ma_Giam_Gia']) ?></strong></td>
                            <td><?= htmlspecialchars($d['mo_Ta']) ?: '<em>Không có mô tả</em>' ?></td>
                            <td><?= number_format($d['gia_Tri_Giam'], 2) ?></td>
                            <td>
                                <span style="color:#00ff88;">
                                    <?= $d['loai_Giam'] === 'phan_tram' ? '%' : 'VNĐ' ?>
                                </span>
                            </td>
                            <td><?= $d['ngay_Bat_Dau'] ? date('d/m/Y H:i', strtotime($d['ngay_Bat_Dau'])) : '-' ?></td>
                            <td><?= $d['ngay_Ket_Thuc'] ? date('d/m/Y H:i', strtotime($d['ngay_Ket_Thuc'])) : '-' ?></td>
                            <td>
                                <span class="status <?= $d['trang_Thai'] === 'Đang hoạt động' ? 'active' : 'inactive' ?>">
                                    <?= htmlspecialchars($d['trang_Thai']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?edit=<?= urlencode($d['ma_Giam_Gia']) ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="?delete=<?= urlencode($d['ma_Giam_Gia']) ?>" class="btn-delete" 
                                   onclick="return confirm('Xóa mã <?= htmlspecialchars($d['ma_Giam_Gia']) ?>?')">
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

</body>
</html>