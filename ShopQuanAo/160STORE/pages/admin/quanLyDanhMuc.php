<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/database.php';

$db = new Database();
$conn = $db->connect();

$msg = '';
$editRow = null;

/* ================================
   XÓA DANH MỤC
   ================================ */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM danh_muc WHERE id_DM = ?");
        $stmt->execute([$id]);
        $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa danh mục ID <strong>$id</strong>!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/* ================================
   LẤY DỮ LIỆU KHI SỬA
   ================================ */
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM danh_muc WHERE id_DM = ?");
    $stmt->execute([$id]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ================================
   THÊM HOẶC CẬP NHẬT DANH MỤC
   ================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten_Danh_Muc'] ?? '');
    $id_DM = intval($_POST['id_DM'] ?? 0);

    if ($ten === '') {
        $msg = "<div class='msg error'><i class='fas fa-exclamation-triangle'></i> Tên danh mục không được để trống!</div>";
    } else {
        try {
            if (isset($_POST['sua_danh_muc'])) {
                $stmt = $conn->prepare("UPDATE danh_muc SET ten_Danh_Muc = ? WHERE id_DM = ?");
                $stmt->execute([$ten, $id_DM]);
                $msg = "<div class='msg success'><i class='fas fa-save'></i> Cập nhật danh mục thành công!</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO danh_muc (ten_Danh_Muc) VALUES (?)");
                $stmt->execute([$ten]);
                $msg = "<div class='msg success'><i class='fas fa-plus-circle'></i> Đã thêm danh mục <strong>" . htmlspecialchars($ten) . "</strong>!</div>";
            }
        } catch (Exception $e) {
            $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi SQL: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

/* ================================
   LẤY DANH SÁCH DANH MỤC
   ================================ */
$stmt = $conn->query("SELECT * FROM danh_muc ORDER BY id_DM ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/Admin_css/style.css">
</head>
<body>

<div class="container">

    <h2>Quản lý Danh mục Sản phẩm</h2>
    <?= $msg ?>

    <!-- FORM THÊM / SỬA -->
    <div class="form-card">
        <h3 class="form-title">
            <?= $editRow ? '<i class="fas fa-edit"></i> Sửa danh mục' : '<i class="fas fa-plus-circle"></i> Thêm danh mục mới' ?>
        </h3>
        <form method="POST" class="form-row">
            <?php if ($editRow): ?>
                <input type="hidden" name="id_DM" value="<?= htmlspecialchars($editRow['id_DM']) ?>">
            <?php endif; ?>

            <input type="text" name="ten_Danh_Muc" class="form-input" 
                   placeholder="Nhập tên danh mục (VD: Áo thun, Quần jeans...)"
                   value="<?= htmlspecialchars($editRow['ten_Danh_Muc'] ?? '') ?>" required style="padding:10px; width:30%; border-radius:8px;outline:none; border: 1px solid #007bff; margin-bottom:12px">

            <?php if ($editRow): ?>
                <button type="submit" name="sua_danh_muc" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <button type="button" class="btn btn-cancel" onclick="loadContent('quanLyDanhMuc.php')">
                    <i class="fas fa-times"></i> Hủy
                </button>
            <?php else: ?>
                <button type="submit" name="them_danh_muc" class="btn btn-primary mt-6">
                    <i class="fas fa-plus"></i> Thêm mới
                </button>
            <?php endif; ?>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Tên danh mục</th>
                    <th width="140">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-inbox"></i> Chưa có danh mục nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><strong>#<?= $r['id_DM'] ?></strong></td>
                            <td><?= htmlspecialchars($r['ten_Danh_Muc']) ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="editDm(<?= $r['id_DM'] ?>)">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="btn-delete" onclick="deleteDm(<?= $r['id_DM'] ?>)">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<script>
function loadContent(url) {
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const main = document.querySelector('#contentFrame')?.contentDocument || document.body;
            main.innerHTML = html;
        })
        .catch(err => alert('Lỗi tải: ' + err.message));
}

function editDm(id) {
    loadContent('../admin/quanLyDanhMuc.php?edit=' + id);
    return false;
}

function deleteDm(id) {
    if (!confirm(`Xóa danh mục ID #${id}?`)) return false;
    loadContent('../admin/quanLyDanhMuc.php?delete=' + id);
    return false;
}

</script>

</body>
</html>