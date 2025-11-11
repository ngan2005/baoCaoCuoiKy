<?php
// header.php
?>

<header class="top-header">
    <!-- Logo -->
    <div class="logo">
        <img src="https://file.hstatic.net/1000253775/file/logo_no_bf-05_3e6797f31bda4002a22464d6f2787316.png" alt="Logo">
    </div>

    <!-- Thanh tìm kiếm -->
    <div class="search-box">
        <form action="" method="GET">
            <input type="text" name="search" placeholder="Bạn đang tìm gì..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Icon -->
    <div class="header-icons">
        <li><a href="/ShopQuanAo/160STORE/pages/profile.php" target="contentFrame"><i class="fa fa-user"></i><span>Trang cá nhân</span></a></li>
        <a href="/ShopQuanAo/160STORE/login.php"><i class="fas fa-user"></i> Đăng nhập</a>
        <a href="/ShopQuanAo/160STORE/pages/gioHang.php"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
        <a href="/ShopQuanAo/160STORE/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </div>
</header>
