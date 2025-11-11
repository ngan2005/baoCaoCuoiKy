<?php
// navbar.php - CHỈ DÙNG CHO MENU VÀ BREADCRUMB CÁC TRANG DANH SÁCH

// Mảng định nghĩa Breadcrumb cho các trang tĩnh
$breadcrumb_map = [
    'TrangChu.php' => ['Hàng Mới'],
    'danhSachCombo.php' => ['Sản Phẩm', 'Combo'],
    'danhSachAoNam.php' => ['Áo Nam'],
    'danhSachQuanNam.php' => ['Quần Nam'],
    'danhSachPhuKien.php' => ['Sản Phẩm', 'Phụ Kiện'],
];

$currentPage = basename($_SERVER['PHP_SELF']);
$currentBreadcrumb = [];

// 1. LOẠI BỎ LOGIC XỬ LÝ 'chiTietSanPham.php' - Việc này được chuyển sang breadcrumb_detail.php

// 2. Xử lý cho các trang danh sách tĩnh
if (array_key_exists($currentPage, $breadcrumb_map)) {
    $currentBreadcrumb = [];
    $count = count($breadcrumb_map[$currentPage]);
    
    foreach ($breadcrumb_map[$currentPage] as $index => $text) {
        // Thiết lập link cho các mục. Mục cuối cùng (current pages) là null.
        $link = null; 
        if ($text === 'Áo Nam') $link = 'danhSachAoNam.php';
        elseif ($text === 'Quần Nam') $link = 'danhSachQuanNam.php';
        elseif ($text === 'Hàng Mới') $link = 'TrangChu.php';
        // Các mục cấp cao hơn (như 'Sản Phẩm') có thể không cần link cụ thể

        // Chỉ thêm link nếu không phải là mục cuối cùng của Breadcrumb
        if ($index < $count - 1 && !$link) {
             // Để tránh link vô nghĩa, chỉ thêm link nếu đã định nghĩa ở trên
             $link = '#'; // Hoặc để null nếu bạn không muốn mục cha có link
        }
        
        $currentBreadcrumb[] = ['text' => $text, 'link' => $link];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        /* Tối ưu CSS cho Breadcrumb trong file stylee.css, chỉ để lại HTML/PHP ở đây */
        .breadcrumb { padding: 10px 20px; font-size: 14px; margin-top: 10px; }
        .breadcrumb a { color: #333; text-decoration: none; }
        .breadcrumb span { color: #000; font-weight: 600; }
        .breadcrumb-separator { padding: 0 5px; color: #ccc; }
    </style>
</head>
<body>
    <nav class="main-nav">
        <ul class="menu">
            <li><a href="/ShopQuanAo/160STORE/TrangChu.php" class="<?php echo $currentPage == 'TrangChu.php' ? 'active' : ''; ?>">HÀNG MỚI</a></li>
            <li>
                <a href="#">SẢN PHẨM</a>
                <ul class="submenu">
                    <li><a href="/ShopQuanAo/160STORE/pages/danhSachCombo.php">Combo</a></li>
                    <li><a href="/ShopQuanAo/160STORE/pages/danhSachAoNam.php">Áo</a></li>
                    <li><a href="/ShopQuanAo/160STORE/pages/danhSachQuanNam.php">Quần</a></li>
                    <li><a href="/ShopQuanAo/160STORE/pages/danhSachPhuKien.php">Phụ Kiện</a></li>
                </ul>
            </li>
            <li><a href="/ShopQuanAo/160STORE/pages/danhSachAoNam.php" class="<?php echo $currentPage == 'danhSachAoNam.php' ? 'active' : ''; ?>">ÁO NAM</a></li>
            <li><a href="/ShopQuanAo/160STORE/pages/danhSachQuanNam.php" class="<?php echo $currentPage == 'danhSachQuanNam.php' ? 'active' : ''; ?>">QUẦN NAM</a></li>
        </ul>
        
        <?php if (!empty($currentBreadcrumb)) : ?>
            <div class="breadcrumb">
                <a href="/ShopQuanAo/160STORE/TrangChu.php">Trang chủ</a>

                <?php foreach ($currentBreadcrumb as $item) : ?>
                    <span class="breadcrumb-separator">/</span>
                    <?php if ($item['link']) : ?>
                        <a href="<?= $item['link'] ?>"><?= $item['text'] ?></a>
                    <?php else : ?>
                        <span><?= $item['text'] ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>
</body>
</html>