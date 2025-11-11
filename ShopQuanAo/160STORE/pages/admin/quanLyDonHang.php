<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/database.php';

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';

// XÓA ĐƠN HÀNG
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM don_hang WHERE id_DH = ?");
    $stmt->execute([$id]);
    $stmt = $conn->prepare("DELETE FROM chi_tiet_hoa_don WHERE id_DH = ?");
    $stmt->execute([$id]);
    $msg = "Đã xóa đơn hàng #$id";
}

// CẬP NHẬT TRẠNG THÁI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE don_hang SET trang_Thai = ? WHERE id_DH = ?");
    $stmt->execute([$_POST['trang_Thai'], $_POST['id_DH']]);
    $msg = "Cập nhật trạng thái thành công!";
}

// LẤY DANH SÁCH ĐƠN HÀNG
$rows = $conn->query("SELECT * FROM don_hang ORDER BY id_DH DESC")->fetchAll(PDO::FETCH_ASSOC);

// XỬ LÝ CHI TIẾT (popup)
if (isset($_GET['details'])) {
    $id_DH = intval($_GET['details']);

    // Lấy chi tiết hóa đơn kèm tên sản phẩm
    $stmt = $conn->prepare("
        SELECT cthd.*, sp.ten_San_Pham 
        FROM chi_tiet_hoa_don cthd
        LEFT JOIN san_pham sp ON cthd.id_SP = sp.id_SP
        WHERE cthd.id_DH = ?
    ");
    $stmt->execute([$id_DH]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy thông tin người nhận hàng
    $stmt2 = $conn->prepare("
        SELECT nd.ho_ten, nd.email, nd.sdt, dh.dia_Chi_Giao ,dh.tong_tien,dh.ma_Giam_Gia
        FROM don_hang dh
        LEFT JOIN nguoi_dung nd ON dh.id_ND = nd.id_ND
        WHERE dh.id_DH = ?
    ");
    $stmt2->execute([$id_DH]);
    $user_info = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo "<h5>Thông tin người nhận</h5>";
    echo "<p><strong>Tên:</strong> " . htmlspecialchars($user_info['ho_ten'] ?? '') . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($user_info['email'] ?? '') . "</p>";
    echo "<p><strong>SĐT:</strong> " . htmlspecialchars($user_info['sdt'] ?? '') . "</p>";
    echo "<p><strong>Địa chỉ giao:</strong> " . htmlspecialchars($user_info['dia_Chi_Giao'] ?? '') . "</p>";
    echo "<p><strong>Tổng tiền:</strong> " . htmlspecialchars($user_info['tong_tien'] ?? '') . "</p>";
    echo "<p><strong>Voucher:</strong> " . htmlspecialchars($user_info['ma_Giam_Gia'] ?? 'Không áp dụng') . "</p>";


    echo "<h5>Chi tiết sản phẩm</h5>";
    echo "<table border='1' width='100%' style='border-collapse:collapse; text-align:center;'>
            <tr>
                <th>Tên SP</th><th>Màu</th><th>Size</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th>
            </tr>";
    foreach($details as $d){
        $ten = $d['ten_San_Pham'] ?? '';
        $mau = $d['mau_sac'] ?? '';
        $size = $d['kich_thuoc'] ?? '';
        $so_luong = $d['so_Luong'] ?? 0;
        $gia = $d['gia_Ban'] ?? 0;
        $total = $so_luong * $gia;

        echo "<tr>
                <td>{$ten}</td>
                <td>{$mau}</td>
                <td>{$size}</td>
                <td>{$so_luong}</td>
                <td>".number_format($gia,0,',','.')."</td>
                <td>".number_format($total,0,',','.')."</td>
              </tr>";
    }
    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding:20px; font-family:Arial, sans-serif; background:#f9f9f9; }
        table th, table td { vertical-align: middle !important; }
        .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);}
        .modal-content { background:#fff; margin:5% auto; padding:20px; width:80%; max-width:700px; border-radius:6px; position:relative;}
        .close { position:absolute; top:10px; right:15px; cursor:pointer; font-size:24px; }
        .status-Chờ-xác-nhận { background:#ffc107; color:#fff; }
        .status-Đang-giao { background:#0d6efd; color:#fff; }
        .status-Đã-giao { background:#198754; color:#fff; }
        .status-Đã-hủy { background:#dc3545; color:#fff; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Quản lý Đơn Hàng</h2>
    <?php if($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <table class="table table-striped table-bordered align-middle text-center">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Địa chỉ giao</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): ?>
            <tr>
                <td><?= $r['id_DH'] ?></td>
                <td><?= $r['ngay_Dat'] ?></td>
                <td><?= number_format($r['tong_Tien'],0,',','.') ?></td>
                <td>
                    <select class="form-select form-select-sm <?= 'status-'.str_replace(' ', '-', $r['trang_Thai']) ?>"
                            onchange="updateStatus(this, <?= $r['id_DH'] ?>)">
                        <?php
                        $statuses = ['Chờ xác nhận','Đang giao','Đã giao','Đã hủy'];
                        foreach($statuses as $st):
                            ?>
                            <option value="<?= $st ?>" <?= $st==$r['trang_Thai']?'selected':'' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><?= htmlspecialchars($r['dia_Chi_Giao']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewDetails(<?= $r['id_DH'] ?>)">Chi tiết</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteOrder(<?= $r['id_DH'] ?>)">Xóa</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalBody"></div>
    </div>
</div>

<script>
    function deleteOrder(id){
        if(confirm('Xóa đơn #'+id+'?')) location.href='?delete='+id;
    }

    function updateStatus(sel, id){
        let s = sel.value;
        let f = new FormData();
        f.append('update_status',1);
        f.append('id_DH',id);
        f.append('trang_Thai',s);
        fetch('',{method:'POST',body:f})
            .then(()=>alert('Cập nhật trạng thái thành công!'))
            .catch(()=>alert('Lỗi khi cập nhật!'));
    }

    function viewDetails(id){
        fetch('?details='+id)
            .then(r=>r.text())
            .then(html=>{
                document.getElementById('modalBody').innerHTML=html;
                document.getElementById('orderModal').style.display='block';
            });
    }

    function closeModal(){ document.getElementById('orderModal').style.display='none'; }

    window.onclick = function(e){ if(e.target==document.getElementById('orderModal')) closeModal(); }
</script>

</body>
</html>
