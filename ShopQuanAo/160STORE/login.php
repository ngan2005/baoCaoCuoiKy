<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();                     // Bắt đầu output buffering
session_start();
// Phải ở đầu file, không có khoảng trắng trước <?php
require_once './includes/database.php';
require_once './includes/config.php';
$db   = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';

/* ==== ĐĂNG NHẬP ==== */
if (isset($_POST['dangNhap'])) {
    $user = trim($_POST['ten_Dang_Nhap']);
    $pass = trim($_POST['mat_Khau']);

    $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_Dang_Nhap = ? AND mat_Khau = ?");
    $stmt->execute([$user, $pass]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u) {
        $_SESSION['user'] = $u;

        // Chuẩn hoá vai trò (loại bỏ khoảng trắng, chuyển về chữ thường)
        $role = trim(strtolower($u['vai_Tro']));

        if ($role === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: TrangChu.php");   // <-- Đảm bảo file tồn tại
        }
        exit;                                   // DỪNG HOÀN TOÀN
    } else {
        $msg = "<div class='msg error'>Sai tài khoản hoặc mật khẩu!</div>";
    }
}

/* ==== ĐĂNG KÝ ==== */
if (isset($_POST['dangKy'])) {
    $ten   = trim($_POST['ten_Dang_Nhap']);
    $pass  = trim($_POST['mat_Khau']);
    $hoTen = trim($_POST['ho_Ten']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $dia_chi = trim($_POST['dia_chi']);

    $vaiTro = 'khach_hang';
    $ngayTao = date('Y-m-d H:i:s');

    $check = $conn->prepare("SELECT id_ND FROM nguoi_dung WHERE ten_Dang_Nhap = ?");
    $check->execute([$ten]);
    if ($check->fetch()) {
        $msg = "<div class='msg error'>Tên đăng nhập đã tồn tại!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO nguoi_dung 
    (ten_Dang_Nhap, mat_Khau, ho_Ten, email, sdt, dia_chi, vai_Tro, ngay_Tao)
    VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$ten, $pass, $hoTen, $email, $sdt, $dia_chi, $vaiTro, $ngayTao]);

        $msg = "<div class='msg success'>Đăng ký thành công! Hãy đăng nhập.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập / Đăng ký</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/logincss.css">
</head>
<body>
<div class="container">
  <h2 id="form-title">Đăng nhập</h2>
  <?= $msg ?>
  <div class="tab">
    <button type="button" onclick="showForm('login')" id="btnLogin" class="active">Đăng nhập</button>
    <button type="button" onclick="showForm('register')" id="btnRegister">Đăng ký</button>
  </div>

  <!-- FORM ĐĂNG NHẬP -->
  <form method="POST" id="form-login">
    <input type="text" name="ten_Dang_Nhap" placeholder="Tên đăng nhập" autocomplete="username" required>
    <div class="input-wrap">
      <input type="password" name="mat_Khau" id="pass-login" placeholder="Mật khẩu" autocomplete="current-password" required>
      <span class="toggle-pass" onclick="togglePass('pass-login')">Mắt</span>
    </div>
    <button type="submit" name="dangNhap">Đăng nhập</button>
  </form>

  <!-- FORM ĐĂNG KÝ -->
  <form method="POST" id="form-register" class="hidden">
    <input type="text" name="ho_Ten" placeholder="Họ tên" autocomplete="name" required>
    <input type="text" name="ten_Dang_Nhap" placeholder="Tên đăng nhập" autocomplete="username" required>
    <div class="input-wrap">
      <input type="password" name="mat_Khau" id="pass-reg" placeholder="Mật khẩu" autocomplete="new-password" required>
      <span class="toggle-pass" onclick="togglePass('pass-reg')">Mắt</span>
    </div>
      <input type="text" name="sdt" placeholder="Số điện thoại" required>
      <input type="text" name="dia_chi" placeholder="Địa chỉ" required>
      <input type="email" name="email" placeholder="Email" autocomplete="email">
    <button type="submit" name="dangKy">Đăng ký</button>
  </form>
</div>

<script>
function showForm(name) {
    document.querySelectorAll('form').forEach(f => f.classList.add('hidden'));
    document.querySelectorAll('.tab button').forEach(b => b.classList.remove('active'));
    document.getElementById('form-' + name).classList.remove('hidden');
    document.getElementById('btn' + name.charAt(0).toUpperCase() + name.slice(1)).classList.add('active');
    document.getElementById('form-title').innerText = name === 'login' ? 'Đăng nhập' : 'Đăng ký';
}
function togglePass(id) {
    const el = document.getElementById(id);
    el.type = (el.type === 'password') ? 'text' : 'password';
}
</script>
</body>
</html>
<?php
ob_end_flush();   // Kết thúc buffering, gửi HTML
?>