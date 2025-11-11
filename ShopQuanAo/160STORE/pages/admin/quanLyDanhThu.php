<?php
// quanLyDanhThu.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/database.php'; // Cập nhật đường dẫn nếu cần

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lấy tháng và năm hiện tại (hoặc từ GET)
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

try {
    // Lấy doanh thu theo ngày trong tháng
    $stmt = $conn->prepare("
        SELECT DAY(ngay_Dat) as ngay, SUM(tong_Tien) as doanhThu
        FROM don_hang
        WHERE MONTH(ngay_Dat) = :month AND YEAR(ngay_Dat) = :year
        GROUP BY DAY(ngay_Dat)
        ORDER BY DAY(ngay_Dat)
    ");
    $stmt->execute([':month' => $month, ':year' => $year]);
    $dataChart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn bị dữ liệu cho chart
    $labels = [];
    $data = [];
    $tongDoanhThu = 0;
    foreach ($dataChart as $row) {
        $labels[] = $row['ngay'];
        $data[] = $row['doanhThu'];
        $tongDoanhThu += $row['doanhThu'];
    }
    $trungBinh = count($data) ? $tongDoanhThu / count($data) : 0;
    $maxDoanhThu = count($data) ? max($data) : 0;
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Doanh Thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Quản Lý Doanh Thu</h2>

    <!-- Form chọn tháng/năm -->
    <form method="GET" class="mb-3 row g-2">
        <div class="col-auto">
            <label>Tháng:</label>
            <input type="number" name="month" min="1" max="12" class="form-control" value="<?= htmlspecialchars($month) ?>">
        </div>
        <div class="col-auto">
            <label>Năm:</label>
            <input type="number" name="year" min="2000" max="2100" class="form-control" value="<?= htmlspecialchars($year) ?>">
        </div>
        <div class="col-auto align-self-end">
            <button class="btn btn-primary">Xem</button>
        </div>
    </form>

    <!-- Biểu đồ doanh thu -->
    <canvas id="doanhThuChart" height="100"></canvas>

    <!-- Thống kê nhanh -->
    <div class="mt-3">
        <p><strong>Tổng doanh thu:</strong> <?= number_format($tongDoanhThu) ?> VNĐ</p>
        <p><strong>Trung bình:</strong> <?= number_format($trungBinh, 2) ?> VNĐ/ngày</p>
        <p><strong>Ngày doanh thu cao nhất:</strong> <?= number_format($maxDoanhThu) ?> VNĐ</p>
    </div>
</div>

<script>
    const ctx = document.getElementById('doanhThuChart').getContext('2d');
    const doanhThuChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Doanh Thu (VNĐ)',
                data: <?= json_encode($data) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { title: { display: true, text: 'Ngày trong tháng' } },
                y: { title: { display: true, text: 'Doanh Thu (VNĐ)' } }
            }
        }
    });
</script>
</body>
</html>
