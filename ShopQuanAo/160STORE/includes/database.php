<?php
require_once 'config.php';
class Database {
    private $host = 'localhost';          // Địa chỉ server MySQL
    private $db_name = 'shop_thoi_trang'; // Tên database
    private $username = 'root';           // Username phpMyAdmin
    private $password = '';               // Password (rỗng nếu dùng XAMPP mặc định)
    private $conn;
    // Hàm kết nối
    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Kết nối database thành công!"; // Bỏ comment để test
        } catch(PDOException $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
        }
        return $this->conn;
    }

    // Lấy kết nối để dùng ở nơi khác
    public function getConnection() {
        return $this->conn;
    }
}
?> 
