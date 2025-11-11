<?php
session_start();
// Kết nối cơ sở dữ liệu, ví dụ: db_connection.php (nếu bạn cần kết nối cơ sở dữ liệu ở trang này)
require_once './includes/database.php';
require_once './includes/config.php';
// Bao gồm file header, sidebar, danh sách sản phẩm, footer
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/stylee.css">  <!-- Liên kết đến file CSS -->
</head>
<body>

    <!-- Bao gồm header.php -->
    <?php include('./layouts/header.php'); ?>
    <main>
        <!-- Bao gồm sidebar.php nếu có -->
        <?php include('./layouts/navbar.php'); ?>
        <!-- Bao gồm danh sách sản phẩm -->
        <?php include('./pages/danhSachSanPham.php'); ?>
    </main>
    <!-- Bao gồm footer.php -->
    <?php include('./layouts/footer.php'); ?>

</body>
</html>
