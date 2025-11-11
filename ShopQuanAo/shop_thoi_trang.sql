-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th10 11, 2025 lúc 10:17 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shop_thoi_trang`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bien_the_san_pham`
--

CREATE TABLE `bien_the_san_pham` (
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm',
  `id_Bien_The` int(11) NOT NULL,
  `mau_Sac` varchar(100) DEFAULT NULL COMMENT 'màu sắc sản phẩm',
  `kich_Thuoc` varchar(100) DEFAULT NULL COMMENT 'kích thước sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bien_the_san_pham`
--

INSERT INTO `bien_the_san_pham` (`id_SP`, `id_Bien_The`, `mau_Sac`, `kich_Thuoc`) VALUES
('SP001', 4, 'Đen', 'S'),
('SP001', 5, 'Đen', 'M'),
('SP001', 6, 'Đen', 'XL'),
('SP002', 7, 'Đen', 'L'),
('SP002', 8, 'Đen', 'M'),
('SP002', 9, 'Đen', 'S'),
('SP002', 10, 'Đen', 'XL'),
('SP003', 11, 'Trắng', 'S'),
('SP003', 12, 'Trắng', 'M'),
('SP003', 13, 'Trắng', 'L'),
('SP003', 14, 'Trắng', 'XL'),
('SP004', 16, 'Đen', 'S'),
('SP004', 17, 'Đen', 'L'),
('SP004', 18, 'Đen', 'M'),
('SP004', 19, 'Đen', 'XL'),
('SP005', 20, 'Nâu', 'S'),
('SP005', 21, 'Nâu', 'M'),
('SP005', 22, 'Nâu', 'L'),
('SP005', 23, 'Nâu', 'XL'),
('SP006', 24, 'Đen', 'S'),
('SP006', 25, 'Đen', 'L'),
('SP006', 26, 'Đen', 'M'),
('SP006', 27, 'Đen', 'XL'),
('SP006', 28, 'Trắng', 'S'),
('SP006', 29, 'Trắng', 'L'),
('SP006', 30, 'Trắng', 'XL'),
('SP006', 31, 'Trắng', 'M'),
('SP007', 32, 'Đen', 'S'),
('SP007', 33, 'Đen', 'L'),
('SP007', 34, 'Đen', 'M'),
('SP007', 35, 'Đen', 'XL'),
('SP007', 36, 'Nâu', 'S'),
('SP007', 37, 'Nâu', 'M'),
('SP007', 38, 'Nâu', 'L'),
('SP007', 39, 'Nâu', 'XL'),
('SP008', 40, 'Đen', 'S'),
('SP008', 41, 'Đen', 'L'),
('SP008', 42, 'Đen', 'M'),
('SP008', 43, 'Đen', 'XL'),
('SP011', 48, 'Đen', '39'),
('SP011', 49, 'Đen', '40'),
('SP011', 50, 'Đen', '41'),
('SP011', 51, 'Đen', '42'),
('SP012', 52, 'Đen', NULL),
('SP013', 53, 'Đen', NULL),
('SP014', 54, 'Trắng', 'S'),
('SP014', 55, 'Trắng', 'L'),
('SP014', 56, 'Trắng', 'M'),
('SP014', 57, 'Trắng', 'XL'),
('SP015', 58, 'Trắng', NULL),
('SP016', 59, 'Đen', 'S'),
('SP016', 60, 'Đen', 'L'),
('SP016', 61, 'Đen', 'XL'),
('SP016', 62, 'Đen', 'M'),
('SP017', 63, 'Trắng', NULL),
('SP018', 64, 'Đen', NULL),
('SP019', 70, 'Xanh', NULL),
('SP020', 71, 'Xám', 'S'),
('SP020', 72, 'Xám', 'L'),
('SP020', 73, 'Xám', 'M'),
('SP020', 74, 'Xám', 'XL'),
('SP021', 75, 'Trắng', 'S'),
('SP021', 76, 'Trắng', 'L'),
('SP021', 77, 'Trắng', 'M'),
('SP021', 78, 'Trắng', 'XL'),
('SP022', 79, 'Xanh', 'S'),
('SP022', 80, 'Xanh', 'L'),
('SP022', 81, 'Xanh', 'M'),
('SP022', 82, 'Xanh', 'XL'),
('SP023', 95, 'Xanh', 'S'),
('SP023', 96, 'Xanh', 'L'),
('SP023', 97, 'Xanh', 'M'),
('SP023', 98, 'Xanh', 'XL'),
('SP0025', 100, 'Xám', 'S');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id_BL` int(11) NOT NULL COMMENT 'khóa chính, id bình luận',
  `id_BL_cha` int(11) DEFAULT NULL,
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa ngoại, id người dùng',
  `noi_Dung` varchar(255) NOT NULL COMMENT 'nội dung',
  `so_Sao` int(11) NOT NULL COMMENT 'số sao ',
  `ngay_Binh_Luan` datetime NOT NULL COMMENT 'ngày bình luận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `binh_luan`
--

INSERT INTO `binh_luan` (`id_BL`, `id_BL_cha`, `id_SP`, `id_ND`, `noi_Dung`, `so_Sao`, `ngay_Binh_Luan`) VALUES
(1, NULL, 'SP001', 10, 'tốt', 5, '2025-11-01 18:20:14'),
(2, NULL, 'SP002', 10, 'cx đc', 3, '2025-11-01 18:24:15'),
(3, NULL, 'SP002', 10, 'tốt', 5, '2025-11-01 18:30:38'),
(9, 2, 'SP002', 12, 'hehe', 5, '2025-11-01 22:32:34'),
(10, 2, 'SP002', 12, 'ok', 5, '2025-11-01 22:35:59'),
(11, 9, 'SP002', 7, 'cảm ơn bạn', 5, '2025-11-01 23:21:01'),
(12, 1, 'SP001', 7, 'cảm ơn bạn nha', 5, '2025-11-02 14:00:30'),
(13, NULL, 'SP022', 7, 'TỐT', 5, '2025-11-02 17:06:36'),
(14, 12, 'SP001', 10, 'ok shop', 5, '2025-11-03 22:27:00'),
(17, 1, 'SP001', 16, 'ok', 5, '2025-11-11 15:50:38'),
(18, NULL, 'SP001', 16, '123123123', 5, '2025-11-11 15:51:43'),
(19, NULL, 'SP001', 16, '123123123', 5, '2025-11-11 15:52:56'),
(20, 1, 'SP001', 16, '123123', 5, '2025-11-11 15:53:21'),
(21, 1, 'SP001', 16, '123123', 5, '2025-11-11 15:55:11'),
(22, 1, 'SP001', 16, '123123', 5, '2025-11-11 16:00:01'),
(23, 1, 'SP001', 16, 'ok', 5, '2025-11-11 16:01:07'),
(24, 1, 'SP001', 16, 'okkkkkkkkkkkkkkkk', 5, '2025-11-11 16:01:17'),
(25, 14, 'SP001', 16, 'ok', 5, '2025-11-11 16:01:40'),
(26, 14, 'SP001', 16, 'ok', 5, '2025-11-11 16:04:20'),
(27, 14, 'SP001', 16, 'ok', 5, '2025-11-11 16:04:30'),
(28, 27, 'SP001', 16, 'duoc do', 5, '2025-11-11 16:04:59'),
(29, 27, 'SP001', 16, 'kkkk', 5, '2025-11-11 16:05:10'),
(30, 14, 'SP001', 16, 'kkkk', 5, '2025-11-11 16:05:21'),
(31, 25, 'SP001', 16, 'test', 5, '2025-11-11 16:06:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `id_CTHD` int(11) NOT NULL COMMENT 'khóa chính, id chi tiết hóa đơn',
  `id_DH` int(11) NOT NULL COMMENT 'khóa ngoại, id hóa đơn',
  `so_Luong` int(11) NOT NULL COMMENT 'số lượng',
  `gia_Ban` int(11) NOT NULL COMMENT 'giá bán',
  `mau_sac` varchar(255) DEFAULT NULL,
  `kich_thuoc` varchar(255) DEFAULT NULL,
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_hoa_don`
--

INSERT INTO `chi_tiet_hoa_don` (`id_CTHD`, `id_DH`, `so_Luong`, `gia_Ban`, `mau_sac`, `kich_thuoc`, `id_SP`) VALUES
(1, 1, 1, 299000, 'Đen', 'S', 'SP001'),
(2, 2, 1, 379000, 'Đen', 'L', 'SP002'),
(3, 2, 1, 310000, 'Đen', 'M', 'SP007'),
(4, 3, 1, 379000, 'Đen', 'M', 'SP002'),
(5, 4, 1, 299000, 'Đen', 'S', 'SP001'),
(6, 5, 1, 299000, 'Đen', 'M', 'SP001'),
(7, 6, 1, 299000, 'Đen', 'M', 'SP001'),
(8, 7, 1, 299000, '', '', 'SP001'),
(9, 7, 1, 299000, '', '', 'SP001'),
(10, 7, 1, 299000, '', '', 'SP001'),
(11, 7, 1, 299000, '', '', 'SP001'),
(12, 7, 1, 299000, '', '', 'SP001'),
(13, 7, 1, 299000, '', '', 'SP001'),
(14, 7, 1, 299000, '', '', 'SP001'),
(15, 7, 1, 299000, '', '', 'SP001'),
(16, 7, 1, 299000, '', '', 'SP001'),
(17, 7, 1, 299000, '', '', 'SP001'),
(18, 8, 5, 299000, 'Đen', 'M', 'SP001'),
(19, 8, 1, 12, 'Xám', 'S', 'SP0025');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id_DM` int(11) NOT NULL COMMENT 'khóa chính, id danh mục',
  `ten_Danh_Muc` varchar(100) NOT NULL COMMENT 'tên danh mục'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id_DM`, `ten_Danh_Muc`) VALUES
(1, 'ÁO NAM'),
(2, 'QUẦN NAM'),
(3, 'PHỤ KIỆN'),
(5, 'COMBO'),
(15, '1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dia_chi_giao_hang`
--

CREATE TABLE `dia_chi_giao_hang` (
  `id_ND` int(10) UNSIGNED NOT NULL COMMENT 'khóa ngoại, khóa chính, id người dùng',
  `ho_Ten_Nguoi_Nhan` varchar(100) NOT NULL COMMENT 'họ tên người nhận',
  `so_Dien_Thoai` int(11) NOT NULL COMMENT 'số điện thoại người nhận',
  `dia_Chi` varchar(255) NOT NULL COMMENT 'địa chỉ người nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_DH` int(11) NOT NULL COMMENT 'khóa chính, id đơn hàng',
  `id_ND` int(11) UNSIGNED NOT NULL,
  `ngay_Dat` datetime NOT NULL,
  `tong_Tien` int(11) NOT NULL,
  `trang_Thai` varchar(255) NOT NULL,
  `dia_Chi_Giao` varchar(255) NOT NULL,
  `ma_Giam_Gia` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_DH`, `id_ND`, `ngay_Dat`, `tong_Tien`, `trang_Thai`, `dia_Chi_Giao`, `ma_Giam_Gia`) VALUES
(1, 16, '2025-11-11 13:57:07', 299000, 'Đang giao', '123123', NULL),
(2, 16, '2025-11-11 14:10:55', 689000, 'Chờ xác nhận', '123123', NULL),
(3, 16, '2025-11-11 14:22:29', 379000, 'Chờ xác nhận', '13123123', NULL),
(4, 16, '2025-11-11 14:47:08', 299000, 'Chờ xác nhận', '123123', NULL),
(5, 16, '2025-11-11 14:49:13', 299000, 'Chờ xác nhận', '123123123123123', NULL),
(6, 16, '2025-11-11 14:52:15', 149500, 'Chờ xác nhận', 'hcm', 'SEP30'),
(7, 16, '2025-11-11 16:04:47', 2990000, 'Chờ xác nhận', '12312321', NULL),
(8, 16, '2025-11-11 16:08:02', 747506, 'Đã giao', 'Test', 'SEP30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giao_dich_thanh_toan`
--

CREATE TABLE `giao_dich_thanh_toan` (
  `id_GDTT` int(11) NOT NULL COMMENT 'khóa chính, id giao dịch thanh toán',
  `id_DH` int(11) NOT NULL COMMENT 'khóa ngoại, id đơn hàng',
  `hinh_Thuc_Thanh_Toan` varchar(100) NOT NULL COMMENT 'hình thức thanh toán',
  `trang_Thai` varchar(100) NOT NULL COMMENT 'trạng thái giao dich',
  `thoi_Gian` datetime NOT NULL COMMENT 'thời gian giao dich'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id_GH` int(11) NOT NULL COMMENT 'khóa chính, id giỏ hàng',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa ngoại, id người dùng',
  `ngay_Tao` datetime NOT NULL COMMENT 'ngày tạo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id_GH`, `id_ND`, `ngay_Tao`) VALUES
(0, 10, '2025-10-31 18:08:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang_chi_tiet`
--

CREATE TABLE `gio_hang_chi_tiet` (
  `id_GHCT` int(11) NOT NULL COMMENT 'khóa chính, id chi tiết giỏ hàng',
  `id_GH` int(11) NOT NULL COMMENT 'khóa ngoại, id giỏ hàng',
  `id_SP` char(50) NOT NULL COMMENT 'khóa ngoại, id sản phẩm',
  `so_Luong` int(11) NOT NULL COMMENT 'số lượng',
  `ten_san_pham` varchar(255) NOT NULL COMMENT 'tên sản phẩm được thêm vào giỏ hàng',
  `mau_sac` varchar(50) DEFAULT NULL COMMENT 'màu sắc sản phẩm ',
  `kich_Thuoc` varchar(100) DEFAULT NULL COMMENT 'kích thước sản phẩm',
  `ma_Giam_Gia` varchar(255) DEFAULT NULL COMMENT 'khóa ngoại, mã giảm giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_don_hang`
--

CREATE TABLE `lich_su_don_hang` (
  `id_LSDH` int(11) NOT NULL COMMENT 'khóa chính, id lịch sử đơn hàng',
  `id_DH` int(11) NOT NULL COMMENT 'id lịch sử đơn hàng',
  `trang_Thai_Cu` varchar(255) DEFAULT NULL COMMENT 'trạng thái mới ',
  `trang_Thai_Moi` varchar(255) DEFAULT NULL COMMENT 'trạng thái cũ',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'id người dùng ',
  `thoi_Gian` datetime NOT NULL COMMENT 'thời gian lịch sử hóa đơn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_tim_kiem`
--

CREATE TABLE `lich_su_tim_kiem` (
  `id_LSTK` int(11) UNSIGNED NOT NULL COMMENT 'khóa chính, id lịch sử tìm kiếm',
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa ngoại, id người dùng',
  `tu_Khoa` varchar(255) NOT NULL COMMENT 'từ khóa tìm ',
  `ngay_Tim_Kiem` datetime NOT NULL COMMENT 'ngày tìm kiếm',
  `ket_Qua_Tim_Kiem` int(11) NOT NULL COMMENT 'KẾT QUẢ TÌM KIẾM'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su_tim_kiem`
--

INSERT INTO `lich_su_tim_kiem` (`id_LSTK`, `id_ND`, `tu_Khoa`, `ngay_Tim_Kiem`, `ket_Qua_Tim_Kiem`) VALUES
(1, 10, 'áo', '2025-11-02 23:26:43', 0),
(2, 10, 'Áo Hoodie Nam ICONDENIM Stronger Life', '2025-11-02 23:26:49', 0),
(3, 10, 'quần', '2025-11-02 23:30:25', 0),
(4, 10, 'Set đồ Nam ICONDENIM New York Cozy', '2025-11-02 23:30:44', 0),
(5, 14, 'SP016', '0000-00-00 00:00:00', 1),
(6, 14, 'SP016', '0000-00-00 00:00:00', 1),
(7, 14, 'SP016', '0000-00-00 00:00:00', 1),
(8, 14, 'SP016', '0000-00-00 00:00:00', 1),
(9, 14, 'áo', '0000-00-00 00:00:00', 10),
(10, 14, 'áo', '0000-00-00 00:00:00', 10),
(11, 14, 'áo', '0000-00-00 00:00:00', 10),
(12, 14, 'áo', '0000-00-00 00:00:00', 10),
(13, 14, 'áo', '0000-00-00 00:00:00', 10),
(14, 14, 'áo', '0000-00-00 00:00:00', 10),
(15, 14, 'áo', '0000-00-00 00:00:00', 10),
(16, 14, 'áo', '0000-00-00 00:00:00', 10),
(17, 14, 'áo', '0000-00-00 00:00:00', 10),
(18, 14, 'áo', '0000-00-00 00:00:00', 10),
(19, 14, 'áo', '0000-00-00 00:00:00', 10),
(20, 14, 'áo', '0000-00-00 00:00:00', 10),
(21, 14, 'áo', '0000-00-00 00:00:00', 10),
(22, 14, 'SP016', '0000-00-00 00:00:00', 1),
(23, 14, 'SP016', '0000-00-00 00:00:00', 1),
(24, 14, 'SP016', '0000-00-00 00:00:00', 1),
(25, 7, 'SO', '0000-00-00 00:00:00', 2),
(26, 7, 'QDW', '0000-00-00 00:00:00', 0),
(27, 7, 'SO', '0000-00-00 00:00:00', 2),
(28, 7, 'SEP05', '0000-00-00 00:00:00', 0),
(29, 7, 'áo', '0000-00-00 00:00:00', 10),
(30, 7, 'áo', '0000-00-00 00:00:00', 10),
(31, 7, 'Áo Hoodie Nam ICONDENIM Stronger Life', '0000-00-00 00:00:00', 1),
(32, 7, 'quần', '0000-00-00 00:00:00', 5),
(33, 10, 'tìm kiến', '0000-00-00 00:00:00', 0),
(34, 10, 'tìm kiến', '0000-00-00 00:00:00', 0),
(35, 7, 'SP016', '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ma_giam_gia`
--

CREATE TABLE `ma_giam_gia` (
  `ma_Giam_Gia` varchar(255) NOT NULL COMMENT 'khóa chính, mã giảm giá',
  `mo_Ta` varchar(255) NOT NULL COMMENT 'mô tả sản phẩm',
  `gia_Tri_Giam` decimal(10,2) NOT NULL COMMENT 'giá trị ',
  `dieu_Kien` varchar(100) NOT NULL COMMENT 'điều kiện áp dụng mã giảm ',
  `ngay_Bat_Dau` datetime NOT NULL COMMENT 'ngày bắt đầu mã giảm',
  `ngay_Ket_Thuc` datetime NOT NULL COMMENT 'ngày kết thúc mã giảm',
  `trang_Thai` varchar(255) NOT NULL COMMENT 'trạng thái mã giảm giá',
  `gia_Tri_Toi_Thieu` decimal(10,2) NOT NULL COMMENT 'giá trị tối thiểu',
  `loai_Giam` varchar(100) NOT NULL COMMENT 'loại giảm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ma_giam_gia`
--

INSERT INTO `ma_giam_gia` (`ma_Giam_Gia`, `mo_Ta`, `gia_Tri_Giam`, `dieu_Kien`, `ngay_Bat_Dau`, `ngay_Ket_Thuc`, `trang_Thai`, `gia_Tri_Toi_Thieu`, `loai_Giam`) VALUES
('SEP01', 'a', 50000.00, 'phụ kiện', '2025-12-12 00:12:00', '2026-12-12 12:12:00', 'Đang hoạt động', 5000000.00, 'tien_mat'),
('SEP05', 'ff', 50000.00, 'quần áo', '2025-12-12 12:12:00', '2026-12-12 12:12:00', 'Đang hoạt động', 500000.00, 'phan_tram'),
('SEP30', 'a', 50.00, 'quần, áo nam', '2024-12-18 12:00:00', '2026-12-05 12:30:00', 'Đang hoạt động', 50.00, 'phan_tram');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id_ND` int(11) UNSIGNED NOT NULL COMMENT 'khóa chính, id người dùng',
  `ten_Dang_Nhap` varchar(50) NOT NULL COMMENT 'tên đăng nhập',
  `mat_Khau` char(6) NOT NULL COMMENT 'mật khẩu',
  `ho_Ten` varchar(50) NOT NULL COMMENT 'họ tên người dùng',
  `email` char(100) NOT NULL COMMENT 'email người dùng',
  `sdt` int(10) UNSIGNED NOT NULL COMMENT 'số điện thoại',
  `dia_Chi` varchar(255) NOT NULL COMMENT 'địa chỉ người dùng',
  `vai_Tro` varchar(50) NOT NULL COMMENT 'vai trò người dùng',
  `ngay_Tao` datetime NOT NULL COMMENT 'ngày tạo tài khoản '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_ND`, `ten_Dang_Nhap`, `mat_Khau`, `ho_Ten`, `email`, `sdt`, `dia_Chi`, `vai_Tro`, `ngay_Tao`) VALUES
(7, 'admin', '123456', 'Quản trị viên', 'admin@160store.com', 367196252, 'TP.HCM', 'admin', '2025-10-26 23:31:05'),
(10, 'KHÔI', '', 'NGÔ MINH KHÔI', '2345677890@gmail.com', 367196252, 'TIỀN GIANG', 'khach_hang', '2025-10-30 19:24:57'),
(11, 'HẰNG', '098765', 'PHAN THỊ THÚY HẰNG', '12345678@gmail.com', 0, '', 'khach_hang', '2025-10-31 07:38:45'),
(12, 'NGÂN', '', 'LƯU THỊ KIM NGÂN', '123@gmail.com', 0, '', 'khach_hang', '2025-11-01 12:32:53'),
(13, 'TRẦN VŨ PHƯƠNG THÙY', '110320', 'THÙY', 'tranvuphuongthuy48@gmail.com', 0, '', 'khach_hang', '2025-11-03 16:25:10'),
(14, 'VÕ ĐOÀN TRỌNG PHÚ', '281220', 'PHÚ', '6543@gmail.com', 0, '', 'khach_hang', '2025-11-04 08:44:07'),
(15, 'LƯU THỊ KIM', '12345', 'KIM', 'luu.kimngan205@gmail.com', 367196252, '', 'khach_hang', '2025-11-07 08:21:32'),
(16, 'dhquan', '123123', 'dhquan', 'dhquan@gmail.com', 334205811, 'hcmmm', 'khach_hang', '2025-11-11 06:19:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id_SP` char(50) NOT NULL COMMENT 'khóa chính, id sản phẩm',
  `ten_San_Pham` varchar(100) NOT NULL COMMENT 'tên sản phẩm',
  `gia_Ban` int(11) NOT NULL COMMENT 'giá bán sản phẩm',
  `gia_Goc` int(11) NOT NULL COMMENT 'giá gốc sản phẩm ',
  `mo_Ta` varchar(255) NOT NULL COMMENT 'mô tả sản phẩm',
  `hinh_Anh` varchar(255) NOT NULL COMMENT 'hình ảnh sản phẩm',
  `id_DM` int(11) NOT NULL COMMENT 'khóa ngoại, id danh mục',
  `thuong_Hieu` varchar(255) NOT NULL COMMENT 'thương hiệu',
  `so_Luong_Ton` int(11) NOT NULL COMMENT 'số lượng hàng tồn',
  `trang_Thai` varchar(100) NOT NULL COMMENT 'trạng thái sản phẩm',
  `ngay_Tao` datetime NOT NULL COMMENT 'ngày tạo sản ',
  `ngay_Cap_Nhat` datetime NOT NULL COMMENT 'ngày cập nhật',
  `ma_Giam_Gia` varchar(100) NOT NULL COMMENT 'khóa ngoại, mã giảm giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id_SP`, `ten_San_Pham`, `gia_Ban`, `gia_Goc`, `mo_Ta`, `hinh_Anh`, `id_DM`, `thuong_Hieu`, `so_Luong_Ton`, `trang_Thai`, `ngay_Tao`, `ngay_Cap_Nhat`, `ma_Giam_Gia`) VALUES
('SP001', 'Áo Thun Nam ICONDENIM  ICDN Basket', 299000, 399000, 'ÁO THUN NAN ĐẸP', 'https://cdn.hstatic.net/products/1000253775/160_set_bo_006-1_acce9c6ad99f4feaae5c2d32f09fb76d_large.jpg', 1, 'WAYFARER', 60, 'Còn hàng', '2025-10-29 20:38:34', '2025-10-29 21:01:28', 'SEP30'),
('SP002', 'Áo Polo Nam ICONDENIM Shoulder Line', 379000, 500000, 'áo polo', 'https://cdn.hstatic.net/products/1000253775/160_ao_polo_237-1_5cbb21b31fbb4b02b4e7b23c2cfe50bb_large.jpg', 1, 'WAYFARER', 30, 'Còn hàng', '2025-10-28 17:40:30', '2025-10-29 21:33:27', 'SEP30'),
('SP0025', 'kim', 12, 12, 'ksaodcoj', 'uploads/quanThun1.jpg', 1, 'ICONDENIM', 1, 'Còn hàng', '2025-11-08 12:24:06', '2025-11-08 12:24:41', 'SEP05'),
('SP003', 'Áo Thun Nam ICONDENIM pocket Edge', 211000, 300000, 'Áo Thun Nam ICONDENIM Pocket Edge, thiết kế trắng đen, túi ngực edge cá tính, chất cotton thoáng mát.', 'https://cdn.hstatic.net/products/1000253775/160_ao_thun_618-11_0093786b9e2e4fd18526940f91461c9a_1024x1024.jpg', 1, 'ICONDENIM', 30, 'Còn hàng', '2025-10-30 19:33:48', '2025-10-30 19:33:48', 'SEP30'),
('SP004', 'Quần Short Jean Nam ICONDENIM Dark Grey', 350000, 390000, 'Quần Short Jean Nam ICONDENIM Dark Grey, màu xám đen vintage, form Smart Fit co giãn thoải mái.', 'https://cdn.hstatic.net/products/1000253775/160_short_298-_14_eb0ba5e887b5487abcada4b1cfa6be66_large.jpg', 2, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 19:49:33', '2025-10-30 19:51:43', 'SEP30'),
('SP005', 'Áo Thun Nam ICONDENIM ICONDENIM Canyon', 350000, 390000, 'Áo Thun Nam ICONDENIM Canyon - màu nâu đất sang trọng, thiết kế tối giản tinh tế.', 'https://cdn.hstatic.net/products/1000253775/160_ao_thun_583-13_04b96863b6544cf9a497e12439339831_large.jpg', 1, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 19:55:51', '2025-10-30 19:55:51', 'SEP30'),
('SP006', 'Quần Short Kaki Nam ICONDENIM Corduroy OG Form Regular', 350000, 390000, 'Quần Short Kaki Nam ICONDENIM Corduroy OG, form Regular thoải mái, chất vải corduroy bền bỉ, thoáng mát.', 'https://cdn.hstatic.net/products/1000253775/160_short_221-11_3dc03944e4e0469ba0a5e62a489f12bb_1024x1024.jpg', 2, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:00:47', '2025-10-30 20:00:47', 'SEP30'),
('SP007', 'Quần Khaki Nam Dài Wash ICONDENIM ID', 310000, 390000, 'Quần Khaki Nam Dài Wash ICONDENIM ID, form Regular thoải mái, chất kaki wash mềm mại, thoáng khí.', 'https://cdn.hstatic.net/products/1000253775/160_kaki_053-7_aea992eed13243229319b4bd2f6f6d51_1024x1024.jpg', 2, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:25:53', '2025-10-30 20:25:53', 'SEP05'),
('SP008', 'Áo Sơ Mi Nam Tay Ngắn ICONDENIM Crinkle Crest Shirt', 300000, 390000, 'Áo Sơ Mi Nam Tay Ngắn ICONDENIM Crinkle Crest, chất vải crinkle thoáng mát, form Regular tinh tế.', 'https://cdn.hstatic.net/products/1000253775/160_somi_301-1_5ec575d6cf564661be04a962dfee8066_large.jpg', 1, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:31:27', '2025-10-30 20:31:27', 'SEP05'),
('SP010', 'Thắt Lưng Nam ICONDENIM Classic Leather Belt', 56000, 150000, 'Thắt Lưng Nam ICONDENIM Classic Leather Belt, da thật cao cấp, khóa kim loại chắc chắn, phong cách cổ điển.', 'https://product.hstatic.net/1000253775/product/that-lung-icondenim-urban-essential__11__293d7f6c57ab466ea13a11128154890a_large.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:49:34', '2025-10-30 20:49:34', 'SEP01'),
('SP011', 'Dép Quai Ngang Nam ICONDENIM Shade Flow', 99000, 149000, 'Dép quai ngang thiết kế hiện đại, đế cao su chống trượt, quai mềm êm chân, thoáng khí.', 'https://product.hstatic.net/1000253775/product/dep-quai-ngang-nam-icondenim-shade-flow__2__7d4589a380634ca3969b46ec27b9ab48_large.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:55:26', '2025-10-30 20:56:22', 'SEP01'),
('SP012', 'Mắt Kính Nam ICONDENIM BRONZE VIEW', 99000, 149000, 'Kính mát gọng kim loại bronze, tròng chống UV, thiết kế thời thượng, nhẹ mặt.', 'https://cdn.hstatic.net/products/1000253775/img_0769_90972cb51315445994a61df380e50fa4_1024x1024.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 20:59:16', '2025-10-30 20:59:16', 'SEP01'),
('SP013', 'Mắt Kính Nam ICONDENIM BRONZE VIEW', 99000, 159000, 'Kính mát gọng bronze thời thượng, tròng chống UV400, nhẹ và bền chắc.', 'https://cdn.hstatic.net/products/1000253775/160_kinh_033-1_1873cc0bd75a48508725e8af3ccb4e4f_1024x1024.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 21:03:23', '2025-10-30 21:03:23', 'SEP01'),
('SP014', 'Vớ Nam Yellowsoc High-Cut', 99000, 149000, 'Vớ cao cổ Yellowsoc, chất cotton thoáng khí, co giãn tốt, thiết kế thể thao.', 'https://product.hstatic.net/1000253775/product/160_vo_001-10_266766feab814f82a314ad417a6411ea_large.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 21:07:32', '2025-10-30 21:07:32', 'SEP01'),
('SP015', 'Mũ Lưỡi Trai Nam ICONDENIM Urban Essential', 99000, 149000, 'Mũ lưỡi trai form chuẩn, chất cotton thoáng mát, logo thêu tinh tế, điều chỉnh size linh hoạt.', 'https://product.hstatic.net/1000253775/product/160_non_037-9_915e4a02cbf245e292c353e1fbefea41_large.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-10-30 21:16:07', '2025-10-30 21:16:07', 'SEP01'),
('SP016', 'Áo Hoodie Nam ICONDENIM Stronger Life', 299000, 399900, 'Hoodie form rộng, chất cotton pha mềm mại, thoáng khí, mũ dây rút, in slogan mạnh mẽ.', 'https://product.hstatic.net/1000253775/product/160_ao_hoodie_036-10_69d5153589204ed08aafc31efcc10a6d_1024x1024.jpg', 1, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 14:48:25', '2025-11-02 14:48:25', 'SEP05'),
('SP017', 'Túi Tote Nam ICONDENIM Canvas Disney Cheer Up', 87000, 99000, 'Túi tote canvas bền chắc, in họa tiết Disney vui nhộn, dung tích rộng, quai đeo thoải mái.', 'https://cdn.hstatic.net/products/1000253775/160_tui_050-5_ced9c7ea7cce476eb5bbb8fc49f8c2dc_1024x1024.jpg', 3, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 14:56:31', '2025-11-02 14:56:31', 'SEP01'),
('SP018', 'Ví Da Nam ICONDENIM Multi-Functionalt', 199000, 299000, 'Ví da cao cấp, thiết kế đa năng với nhiều ngăn tiện lợi, chất liệu bền đẹp, đường may tinh tế.', 'https://product.hstatic.net/1000253775/product/vi-da-icondenim-multi-functionalt__3__aa3aa88561004748b1932bc1eacd691e_1024x1024.jpg', 3, 'ICONDENIM', 40, 'Còn hàng', '2025-11-02 15:00:32', '2025-11-02 15:00:32', 'SEP01'),
('SP019', 'Mũ Bucket Nam ICONDENIM Sticker ID', 279000, 400000, 'Mũ bucket form tròn thời thượng, chất cotton pha thoáng mát, in sticker ID cá tính.', 'https://product.hstatic.net/1000253775/product/160_non_059-9_e011230861ae407e844ed2ecebf537b0_1024x1024.jpg', 3, 'ICONDENIM', 100, 'Còn hàng', '2025-11-02 15:06:43', '2025-11-02 15:34:18', 'SEP01'),
('SP020', 'Combo Áo Thun + Quần Short ICONDENIM', 599000, 1500000, 'Combo áo thun xám cotton thoáng mát và quần short form chuẩn, phong cách casual trẻ trung.', 'https://file.hstatic.net/1000253775/file/01_4595a5853bd346f289dcba2c7dd1bad2.jpg', 5, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 15:43:42', '2025-11-02 15:43:42', 'SEP05'),
('SP021', 'Combo Áo Thun + Quần Jean ICONDENIM', 570000, 1300000, 'Combo áo thun trắng cotton mềm mại và quần jean form chuẩn, phong cách năng động, tinh tế.', 'https://file.hstatic.net/1000253775/file/01_978de6abb2b945a1a9cb2e9872b91e39.jpg', 5, 'ICONDENIM', 109, 'Còn hàng', '2025-11-02 15:55:52', '2025-11-02 15:55:52', 'SEP30'),
('SP022', 'Set đồ Nam ICONDENIM New York Cozy', 679000, 2900000, 'Set áo thun và quần đen cotton pha, form thoải mái, phong cách New York năng động, cá tính.', 'https://product.hstatic.net/1000253775/product/z6151746879077_6061968530a58331f1919bbf68d8bd46_bd6f2d1604434764ade9b56ad82d2df0_1024x1024.jpg', 5, 'ICONDENIM', 40, 'Còn hàng', '2025-11-02 16:01:22', '2025-11-02 16:01:22', 'SEP01'),
('SP023', 'Áo Khoác Bomber Nam ICONDENIM Freshman Varsity', 129000, 145000, 'Áo khoác bomber form varsity cổ điển, chất vải cotton pha thoáng mát, chi tiết thêu tinh tế.', 'https://cdn.hstatic.net/products/1000253775/160_ao_khoac_228-3_249b8725d78b485d9b446784df774192_1024x1024.jpg', 1, 'ICONDENIM', 60, 'Còn hàng', '2025-11-02 16:59:01', '2025-11-07 20:05:18', 'SEP30');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  ADD PRIMARY KEY (`id_Bien_The`),
  ADD KEY `fk_BTSP_sanPham` (`id_SP`);

--
-- Chỉ mục cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id_BL`),
  ADD KEY `fk_binhLuan_nguoiDung` (`id_ND`),
  ADD KEY `fk_binhLuan_sanPham` (`id_SP`);

--
-- Chỉ mục cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD PRIMARY KEY (`id_CTHD`),
  ADD KEY `fk_CTHD_donHang` (`id_DH`),
  ADD KEY `fk_CTHD_sanPham` (`id_SP`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id_DM`);

--
-- Chỉ mục cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD PRIMARY KEY (`id_ND`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_DH`),
  ADD KEY `fk_donHang_nguoiDung1` (`id_ND`);

--
-- Chỉ mục cho bảng `giao_dich_thanh_toan`
--
ALTER TABLE `giao_dich_thanh_toan`
  ADD PRIMARY KEY (`id_GDTT`),
  ADD KEY `fk_GDTT_donHang` (`id_DH`);

--
-- Chỉ mục cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id_GH`),
  ADD KEY `fk_gioHang_nguoiDung` (`id_ND`);

--
-- Chỉ mục cho bảng `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  ADD PRIMARY KEY (`id_GHCT`),
  ADD KEY `fk_GHCT_gioHang` (`id_GH`),
  ADD KEY `fk_GHCT_sanPham` (`id_SP`),
  ADD KEY `fk_maGiamGia_GHCT` (`ma_Giam_Gia`);

--
-- Chỉ mục cho bảng `lich_su_don_hang`
--
ALTER TABLE `lich_su_don_hang`
  ADD PRIMARY KEY (`id_LSDH`),
  ADD KEY `fk_LSDH_donHang` (`id_DH`),
  ADD KEY `fk_LSDH_nguoiDung` (`id_ND`);

--
-- Chỉ mục cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD PRIMARY KEY (`id_LSTK`),
  ADD KEY `fk_LSTT_nguoiDung` (`id_ND`);

--
-- Chỉ mục cho bảng `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  ADD PRIMARY KEY (`ma_Giam_Gia`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id_ND`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id_SP`),
  ADD KEY `fk_sanPham_danhMuc` (`id_DM`),
  ADD KEY `fk_sanPham_maGiamGia` (`ma_Giam_Gia`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  MODIFY `id_Bien_The` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  MODIFY `id_BL` int(11) NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id bình luận', AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id_DM` int(11) NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id danh mục', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  MODIFY `id_GHCT` int(11) NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id chi tiết giỏ hàng', AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  MODIFY `id_LSTK` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id lịch sử tìm kiếm', AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id_ND` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'khóa chính, id người dùng', AUTO_INCREMENT=17;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bien_the_san_pham`
--
ALTER TABLE `bien_the_san_pham`
  ADD CONSTRAINT `fk_BTSP_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `fk_binhLuan_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_binhLuan_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD CONSTRAINT `fk_CTHD_donHang` FOREIGN KEY (`id_DH`) REFERENCES `don_hang` (`id_DH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_CTHD_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD CONSTRAINT `fk_DCGH_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `fk_donHang_nguoiDung1` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`);

--
-- Các ràng buộc cho bảng `giao_dich_thanh_toan`
--
ALTER TABLE `giao_dich_thanh_toan`
  ADD CONSTRAINT `fk_GDTT_donHang` FOREIGN KEY (`id_DH`) REFERENCES `don_hang` (`id_DH`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `fk_gioHang_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  ADD CONSTRAINT `fk_GHCT_gioHang` FOREIGN KEY (`id_GH`) REFERENCES `gio_hang` (`id_GH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_GHCT_sanPham` FOREIGN KEY (`id_SP`) REFERENCES `san_pham` (`id_SP`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maGiamGia_GHCT` FOREIGN KEY (`ma_Giam_Gia`) REFERENCES `ma_giam_gia` (`ma_Giam_Gia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `lich_su_don_hang`
--
ALTER TABLE `lich_su_don_hang`
  ADD CONSTRAINT `fk_LSDH_donHang` FOREIGN KEY (`id_DH`) REFERENCES `don_hang` (`id_DH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_LSDH_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `lich_su_tim_kiem`
--
ALTER TABLE `lich_su_tim_kiem`
  ADD CONSTRAINT `fk_LSTT_nguoiDung` FOREIGN KEY (`id_ND`) REFERENCES `nguoi_dung` (`id_ND`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_sanPham_danhMuc` FOREIGN KEY (`id_DM`) REFERENCES `danh_muc` (`id_DM`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sanPham_maGiamGia` FOREIGN KEY (`ma_Giam_Gia`) REFERENCES `ma_giam_gia` (`ma_Giam_Gia`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
