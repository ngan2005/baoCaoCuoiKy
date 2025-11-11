<?php
session_start();
require_once './includes/database.php';
require_once './includes/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['user']['vai_Tro'] !== 'admin') {
    header("Location: TrangChu.php");
    exit;
}

// Lấy thông tin admin mới nhất
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare("SELECT ho_Ten, email FROM nguoi_dung WHERE id_ND = ?");
$stmt->execute([$_SESSION['user']['id_ND']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Thiết lập tiêu đề mặc định và đường dẫn mặc định
$default_page_title = "Thêm sản phẩm";
$default_page_url = "pages/admin/quanLySanPham.php";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | 160STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/Admin_css/style.css">
    <link rel="stylesheet" href="assets/Admin_css/Breadcrumb.css">
</head>
<body>
<aside class="sidebar">
    <div class="logo">
        <img src="https://file.hstatic.net/1000253775/file/logo_no_bf-05_3e6797f31bda4002a22464d6f2787316.png" alt="160STORE">
        <h2 > <a class="title_shop" href="/ShopQuanAo/160STORE/TrangChu.php" style="text-decoration: none !important;color:var(--primary)">160STORE</a></h2>
    </div>
    <div class="admin-info">
        <div class="admin-avatar">
            <i class="fas fa-user-shield"></i>
        </div>
        <h3><?= htmlspecialchars($admin['ho_Ten']) ?></h3>
        <p><?= htmlspecialchars($admin['email']) ?></p>
    </div>

    <nav>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="pages/admin/quanLySanPham.php" target="contentFrame" class="nav-link active" data-title="Quản lý sản phẩm">
                    <i class="fas fa-plus-circle"></i>
                    <span>Thêm sản phẩm</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pages/admin/quanLyDanhMuc.php" target="contentFrame" class="nav-link" data-title="Quản lý danh mục">
                    <i class="fas fa-folder-open"></i>
                    <span>Quản lý danh mục</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pages/admin/quanLyMaGiamGia.php" target="contentFrame" class="nav-link" data-title="Quản lý mã giảm giá">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Mã giảm giá</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pages/admin/quanLyDonHang.php" target="contentFrame" class="nav-link" data-title="Quản lý đơn hàng">
                    <i class="fas fa-receipt"></i>
                    <span>Quản lý đơn hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pages/admin/quanLyTaiKhoan.php" target="contentFrame" class="nav-link" data-title="Quản lý tài khoản">
                    <i class="fas fa-users-cog"></i>
                    <span>Quản lý tài khoản</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pages/admin/quanLy_binh_luan.php" target="contentFrame" class="nav-link" data-title="Quản lý bình luận">
                    <i class="fas fa-comments"></i>
                    <span>Quản lý bình luận</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pages/admin/quanLyDanhThu.php" target="contentFrame" class="nav-link" data-title="Quản lý doanh thu">
                    <i class="fas fa-chart-line"></i>
                    <span>Quản lý danh thu</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <div class="topbar">
        <div class="breadcrumb-container">
            <ul class="breadcrumb" id="breadcrumb">
                <li class="breadcrumb-item"><i class="fas fa-home"></i> Trang Chủ</li>
                <li class="breadcrumb-item active" id="currentBreadcrumbTitle"><?= $default_page_title ?></li>
            </ul>
        </div>
        
        <div class="topbar-header">
            <h1 class="page-title" id="pageTitle">
                <i class="fas fa-plus-circle"></i> <?= $default_page_title ?>
            </h1>
            <div class="user-actions">
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <button class="logout-btn" onclick="if(confirm('Đăng xuất ngay?')) window.location='logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </div>
        </div>
    </div>

    <iframe id="contentFrame" name="contentFrame" src="<?= $default_page_url ?>"></iframe>
</main>

<script>
    // Lấy các phần tử cần cập nhật
    const navLinks = document.querySelectorAll('.nav-link');
    const contentFrame = document.getElementById('contentFrame');
    const pageTitleElement = document.getElementById('pageTitle');
    const currentBreadcrumbTitleElement = document.getElementById('currentBreadcrumbTitle');

    // Hàm cập nhật tiêu đề và breadcrumb
    function updateHeader(linkElement) {
        const newTitle = linkElement.getAttribute('data-title') || linkElement.querySelector('span').textContent;
        const iconHtml = linkElement.querySelector('i').outerHTML;
        
        // 1. Cập nhật Page Title
        pageTitleElement.innerHTML = iconHtml + ' ' + newTitle;

        // 2. Cập nhật Breadcrumb
        currentBreadcrumbTitleElement.textContent = newTitle;
    }

    // Thiết lập trạng thái ban đầu (cho mục đầu tiên là active)
    const initialActiveLink = document.querySelector('.nav-link.active');
    if (initialActiveLink) {
        updateHeader(initialActiveLink);
    }

    // Active menu item và cập nhật header khi click
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Loại bỏ 'active' khỏi tất cả các link
            navLinks.forEach(el => el.classList.remove('active'));
            
            // Thêm 'active' cho link hiện tại
            this.classList.add('active');

            // Cập nhật tiêu đề và breadcrumb
            updateHeader(this);

            // Tải nội dung iframe (đã có target="contentFrame" nên không cần code ở đây, 
            // nhưng nên để hàm updateHeader() chạy trước khi iframe tải)
        });
    });
</script>

</body>
</html>