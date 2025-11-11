<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
require_once '../includes/database.php';

$db = new Database();
$pdo = $db->connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$isLoggedIn = isset($_SESSION['user']);
$user_id    = $isLoggedIn ? $_SESSION['user']['id_ND'] : null;
$user_name  = $isLoggedIn ? $_SESSION['user']['ho_Ten'] : '';

// Lấy id sản phẩm
$id_SP = $_GET['id'] ?? '';
if (empty($id_SP)) die("<p class='text-danger'>ID sản phẩm không hợp lệ.</p>");

// ===== XỬ LÝ POST BÌNH LUẬN =====
$error_msg = '';
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn && isset($_POST['guiBL'])) {
    $noi_Dung  = trim($_POST['noi_Dung'] ?? '');
    $so_Sao    = intval($_POST['so_Sao'] ?? 5);
    $id_BL_cha = !empty($_POST['id_BL_cha']) ? intval($_POST['id_BL_cha']) : NULL;

    if ($noi_Dung) {
        $stmt = $pdo->prepare("
            INSERT INTO binh_luan (id_SP, id_ND, id_BL_cha, noi_Dung, so_Sao, ngay_Binh_Luan)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$id_SP, $user_id, $id_BL_cha, $noi_Dung, $so_Sao]);
        $success_msg = "Bình luận đã được gửi!";
    } else {
        $error_msg = "Nội dung bình luận không được để trống!";
    }
}

// ===== LẤY SẢN PHẨM =====
$product_stmt = $pdo->prepare("SELECT id_SP, ten_San_Pham, gia_Ban, hinh_Anh FROM san_pham WHERE id_SP = ?");
$product_stmt->execute([$id_SP]);
$product = $product_stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) die("<p class='text-danger'>Sản phẩm không tồn tại.</p>");

// ===== GỢI Ý SẢN PHẨM =====
$suggest_stmt = $pdo->query("SELECT id_SP, ten_San_Pham, gia_Ban, hinh_Anh FROM san_pham ORDER BY RAND() LIMIT 4");
$suggest_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== LẤY BÌNH LUẬN =====
$bl_stmt = $pdo->prepare("
    SELECT id_BL, id_BL_cha, noi_Dung, so_Sao, ngay_Binh_Luan, nd.ho_ten
    FROM binh_luan bl
    LEFT JOIN nguoi_dung nd ON bl.id_ND = nd.id_ND
    WHERE id_SP = ?
    ORDER BY ngay_Binh_Luan ASC
");
$bl_stmt->execute([$id_SP]);
$binh_luans_raw = $bl_stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== XÂY DỰNG CÂY BÌNH LUẬN =====
function buildCommentTree($comments){
    $tree = [];
    $refs = [];
    foreach($comments as $c){
        $c['children'] = [];
        $refs[$c['id_BL']] = $c;
        if($c['id_BL_cha'] === NULL){
            $tree[$c['id_BL']] = &$refs[$c['id_BL']];
        } else {
            if(isset($refs[$c['id_BL_cha']])){
                $refs[$c['id_BL_cha']]['children'][$c['id_BL']] = &$refs[$c['id_BL']];
            }
        }
    }
    return $tree;
}

// ===== HIỂN THỊ BÌNH LUẬN ĐỆ QUY VỚI FORM TRẢ LỜI ẨN =====
function renderComments($comments, $isLoggedIn, $level=0){
    foreach($comments as $c){
        $collapseId = "replyForm".$c['id_BL'];
        ?>
        <div class="list-group-item mb-2 shadow-sm rounded" style="margin-left: <?= $level*20 ?>px;">
            <div class="d-flex justify-content-between">
                <strong><?= htmlspecialchars($c['ho_ten'] ?? 'Ẩn danh') ?></strong>
                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($c['ngay_Binh_Luan'])) ?></small>
            </div>
            <?php if(isset($c['so_Sao'])): ?>
                <div class="mb-1">
                    <?php for($i=0;$i<5;$i++): ?>
                        <span class="<?= $i<$c['so_Sao']?'text-warning':'text-secondary' ?>">&#9733;</span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($c['noi_Dung'])) ?></p>

            <?php if($isLoggedIn): ?>
                <button class="btn btn-sm btn-outline-primary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="false">
                    Trả lời
                </button>
                <div class="collapse" id="<?= $collapseId ?>">
                    <form method="post" class="mb-2 mt-2">
                        <input type="hidden" name="id_BL_cha" value="<?= $c['id_BL'] ?>">
                        <textarea name="noi_Dung" class="form-control mb-1" placeholder="Viết trả lời..." required></textarea>
                        <select name="so_Sao" class="form-select mb-1">
                            <option value="5">5 sao</option>
                            <option value="4">4 sao</option>
                            <option value="3">3 sao</option>
                            <option value="2">2 sao</option>
                            <option value="1">1 sao</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" name="guiBL">Gửi</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if(!empty($c['children'])) renderComments($c['children'], $isLoggedIn, $level+1); ?>
        </div>
        <?php
    }
}

$comments_tree = buildCommentTree($binh_luans_raw);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">



    <!-- GỢI Ý -->
    <h4>Gợi ý sản phẩm</h4>
    <div class="row mb-4">
        <?php foreach($suggest_products as $s): ?>
            <div class="col-md-3 col-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <a href="chiTietSanPham.php?id=<?= urlencode($s['id_SP']) ?>">
                        <img src="<?= stripos($s['hinh_Anh'],'http')===0?$s['hinh_Anh']:'http://'.$_SERVER['HTTP_HOST'].'/ShopQuanAo/160STORE/pages/admin/'.$s['hinh_Anh'] ?>"
                             class="card-img-top" alt="<?= htmlspecialchars($s['ten_San_Pham']) ?>" style="width:100%;height:200px;object-fit:cover;">
                    </a>
                    <div class="card-body text-center">
                        <h6 class="card-title"><?= htmlspecialchars($s['ten_San_Pham']) ?></h6>
                        <p class="card-text"><?= number_format($s['gia_Ban'],0,',','.') ?>đ</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- FORM BÌNH LUẬN GỐC -->
    <div class="my-4">
        <?php if($isLoggedIn): ?>
            <?php if($error_msg) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>
            <?php if($success_msg) echo "<div class='alert alert-success'>$success_msg</div>"; ?>

            <button class="btn btn-success mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#commentForm" aria-expanded="false">
                Viết bình luận
            </button>
            <div class="collapse" id="commentForm">
                <form method="post" class="mb-3 mt-2">
                    <input type="hidden" name="id_BL_cha" value="">
                    <textarea name="noi_Dung" class="form-control mb-2" placeholder="Viết bình luận..." required></textarea>
                    <select name="so_Sao" class="form-select mb-2">
                        <option value="5">5 sao</option>
                        <option value="4">4 sao</option>
                        <option value="3">3 sao</option>
                        <option value="2">2 sao</option>
                        <option value="1">1 sao</option>
                    </select>
                    <button type="submit" class="btn btn-success" name="guiBL">Gửi</button>
                </form>
            </div>
        <?php else: ?>
            <p class="text-warning">Bạn cần <a href="login.php">đăng nhập</a> để bình luận.</p>
        <?php endif; ?>
    </div>

    <!-- DANH SÁCH BÌNH LUẬN -->
    <div class="list-group mb-4">
        <?php
        if(empty($comments_tree)){
            echo "<p class='text-muted'>Chưa có bình luận nào.</p>";
        } else {
            renderComments($comments_tree, $isLoggedIn);
        }
        ?>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php ob_end_flush(); ?>
