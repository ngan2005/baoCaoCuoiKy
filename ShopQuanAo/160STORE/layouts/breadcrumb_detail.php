<!---->
<?php
//// navbarCTSP.php
//// ... (Phần định nghĩa $breadcrumb_map như cũ) ...
//// File này chỉ nên được include vào chiTietSanPham.php
//
//// Kiểm tra xem biến $product đã tồn tại và chứa dữ liệu không
//// (Giả định $product đã được truy vấn từ CSDL trong chiTietSanPham.php)
//if (isset($product) && $product) {
//    // Tên và ID danh mục đã được lấy trong truy vấn JOIN của chiTietSanPham.php
//    $productName = htmlspecialchars($product['ten_San_Pham']);
//    $categoryName = htmlspecialchars($product['ten_Danh_Muc'] ?? 'Sản Phẩm');
//    // Thiết lập link danh mục một cách thông minh hơn
//    $categoryLink = 'danhSachSanPham.php';
//    if ($categoryName === 'Áo') $categoryLink = 'danhSachAoNam.php';
//    if ($categoryName === 'Quần') $categoryLink = 'danhSachQuanNam.php';
//    if ($categoryName === 'Combo') $categoryLink = 'danhSachCombo.php';
//    if ($categoryName === 'Phụ Kiện') $categoryLink = 'danhSachPhuKien.php';
//
//    // Bắt đầu Breadcrumb HTML
//    echo '<div class="breadcrumb">';
//    echo '<a href="../TrangChu.php">Trang chủ</a>';
//    // Mục Danh mục
//    echo '<span class="breadcrumb-separator">/</span>';
//    echo '<a href="' . $categoryLink . '">' . $categoryName . '</a>';
//
//    // Mục Sản phẩm (Mục hiện tại)
//    echo '<span class="breadcrumb-separator">/</span>';
//    echo '<span>' . $productName . '</span>';
//
//    echo '</div>';
//}
//// Nếu $product không tồn tại, không hiển thị gì cả
//?>