<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã Giảm Giá | 160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <script>
        function copyVoucher(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Đã sao chép mã giảm giá: ' + code);
            });
        }
    </script>
</head>
<body>
    <!-- Ưu Đãi Dành Cho Bạn -->
    <blockquote><h3>Ưu Đãi Dành Cho Bạn</h3></blockquote>
    <div class="voucher-list">
        <?php foreach ($vouchers as $v): ?>
            <div class="voucher">
                <div class="voucher-code">
                    <span>Mã giảm giá:</span> <strong><?= htmlspecialchars($v['ma_Giam_Gia']) ?></strong>
                </div>
                <div class="voucher-info">
                    <?= htmlspecialchars($v['mo_Ta']) ?><br>
                    <small>Áp dụng đến <?= date('d/m/Y', strtotime($v['ngay_Ket_Thuc'])) ?></small>
                </div>
                <button class="copy-btn" onclick="copyVoucher('<?= htmlspecialchars($v['ma_Giam_Gia']) ?>')">Sao chép mã</button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>