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

// Xóa tài khoản
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id_ND=?");
        $stmt->execute([$id]);
        $msg = "<div class='msg success'><i class='fas fa-trash'></i> Đã xóa tài khoản ID <strong>$id</strong>!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-times-circle'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Cập nhật vai trò
if (isset($_POST['update_role'])) {
    $id = intval($_POST['id_ND']);
    $role = in_array($_POST['vai_Tro'], ['admin', 'khach_hang']) ? $_POST['vai_Tro'] : 'khach_hang';
    try {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET vai_Tro=? WHERE id_ND=?");
        $stmt->execute([$role, $id]);
        $msg = "<div class='msg success'><i class='fas fa-check-circle'></i> Cập nhật vai trò thành công!</div>";
    } catch (Exception $e) {
        $msg = "<div class='msg error'><i class='fas fa-bug'></i> Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Tìm kiếm
$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM nguoi_dung 
        WHERE ten_Dang_Nhap LIKE :s 
           OR ho_Ten LIKE :s 
           OR email LIKE :s 
        ORDER BY ngay_Tao DESC";
$stmt = $conn->prepare($sql);
$stmt->execute(['s' => "%$search%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài khoản | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/Admin_css/style.css">
</head>
<body>

<div class="container">

    <h2>Quản lý Tài khoản Người dùng</h2>
    <?= $msg ?>

    <!-- TÌM KIẾM -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="search-input" placeholder="Tìm kiếm tên, email, số điện thoại..." 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn-search">
            <i class="fas fa-search"></i> Tìm kiếm
        </button>
        <a href="quanLyTaiKhoan.php" class="btn-refresh">
            <i class="fas fa-sync"></i> Làm mới
        </a>
    </form>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Địa chỉ</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-inbox"></i> Không tìm thấy người dùng nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong>#<?= $u['id_ND'] ?></strong></td>
                            <td><?= htmlspecialchars($u['ten_Dang_Nhap']) ?></td>
                            <td><?= htmlspecialchars($u['ho_Ten']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['sdt'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($u['dia_Chi'] ?: '-') ?></td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="id_ND" value="<?= $u['id_ND'] ?>">
                                    <select name="vai_Tro" class="role-select" onchange="this.form.submit()">
                                        <option value="khach_hang" <?= $u['vai_Tro'] === 'khach_hang' ? 'selected' : '' ?>>Khách hàng</option>
                                        <option value="admin" <?= $u['vai_Tro'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="update_role" value="1">
                                </form>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['ngay_Tao'])) ?></td>
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
            const frame = document.querySelector('#contentFrame')?.contentDocument || document.body;
            frame.innerHTML = html;
        })
        .catch(err => alert('Lỗi: ' + err.message));
}
</script>

</body>
</html>