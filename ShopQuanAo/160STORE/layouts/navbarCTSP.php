<?php
// navbar.php
// ... (Phần định nghĩa $breadcrumb_map như cũ) ...

$currentPage = basename($_SERVER['PHP_SELF']);
$currentBreadcrumb = [];

// 1. Xử lý cho trang chi tiết sản phẩm (DÙNG BIẾN TỪ FILE GỐC)
// Kiểm tra xem biến $navbar_data có được truyền vào không
if ($currentPage == 'chiTietSanPham.php' && isset($navbar_data) && $navbar_data['is_detail_page']) {

    $categoryName = htmlspecialchars($navbar_data['category_name'] ?? 'Sản Phẩm');
    $productName = htmlspecialchars($navbar_data['product_name'] ?? 'Chi tiết');

    // Thiết lập link danh mục một cách thủ công (hoặc thông minh hơn)
    $categoryLink = 'danhSachSanPham.php';
    if ($categoryName === 'Áo') $categoryLink = '/danhSachAoNam.php';
    if ($categoryName === 'Quần') $categoryLink = '/danhSachQuanNam.php';
    if ($categoryName === 'Combo') $categoryLink = '/danhSachCombo.php';
    if ($categoryName === 'Phụ Kiện') $categoryLink = '/danhSachPhuKien.php';

    // Thiết lập cấu trúc Breadcrumb
    $currentBreadcrumb = [
        ['text' => $categoryName, 'link' => $categoryLink],
        ['text' => $productName, 'link' => null] // Mục cuối không có link
    ];

}
// 2. Xử lý cho các trang danh sách tĩnh (Logic như cũ)
elseif (array_key_exists($currentPage, $breadcrumb_map)) {
    // ... (Logic cũ) ...
}

// ... (Phần HTML/CSS/Hiển thị Breadcrumb như cũ) ...
?>