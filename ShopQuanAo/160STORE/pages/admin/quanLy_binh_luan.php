<?php
session_start();
require_once '../../includes/database.php';

$db = new Database();
$pdo = $db->connect();

// Lấy danh sách bình luận và tính sao trung bình cho mỗi sản phẩm
$stmt = $pdo->prepare("
    SELECT 
        bl.id_BL, 
        bl.noi_Dung, 
        bl.so_Sao, 
        bl.ngay_Binh_Luan, 
        bl.id_BL_cha, 
        sp.ten_San_Pham, 
        nd.ten_Dang_Nhap, 
        sp.id_SP,
        (SELECT AVG(so_Sao) FROM binh_luan bl2 WHERE bl2.id_SP = sp.id_SP) as avg_rating,
        (SELECT COUNT(*) FROM binh_luan bl3 WHERE bl3.id_SP = sp.id_SP) as rating_count
    FROM 
        binh_luan bl
    JOIN 
        san_pham sp ON bl.id_SP = sp.id_SP
    JOIN 
        nguoi_dung nd ON bl.id_ND = nd.id_ND
    ORDER BY bl.ngay_Binh_Luan DESC
");
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = ""; // Khởi tạo thông báo
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bình luận | 160STORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/Admin_css/style.css">
</head>
<body>
<div class="container">
    <h2>Quản lý Bình luận Sản phẩm</h2>
    <?= $msg ?>

    <!-- BẢNG DANH SÁCH -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Bình luận</th>
                    <th>Tên người dùng</th>
                    <th>Sản phẩm</th>
                    <th>Nội dung</th>
                    <th>Số sao</th>
                    <th>Ngày bình luận</th>
                    <th>Sao trung bình</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888; padding:30px;">
                            <i class="fas fa-comment-slash"></i> Không có bình luận nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($comment['id_BL']) ?></strong></td>
                            <td><?= htmlspecialchars($comment['ten_Dang_Nhap']) ?></td>
                            <td><?= htmlspecialchars($comment['ten_San_Pham']) ?></td>
                            <td><?= nl2br(htmlspecialchars($comment['noi_Dung'])) ?></td>
                            <td class="stars"><?= htmlspecialchars($comment['so_Sao']) ?>⭐</td>
                            <td><?= date('d/m/Y H:i', strtotime($comment['ngay_Binh_Luan'])) ?></td>
                            <td class="avg-rating">
                                <?php
                                $avg = $comment['avg_rating'];
                                $count = $comment['rating_count'];
                                echo $count > 0 ? number_format($avg, 1) . '⭐' : '0⭐';
                                ?>
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