-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for thuvienlibra
CREATE DATABASE IF NOT EXISTS `thuvienlibra` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `thuvienlibra`;

-- Dumping structure for table thuvienlibra.chinhsachmuon
CREATE TABLE IF NOT EXISTS `chinhsachmuon` (
  `maChinhSach` int NOT NULL AUTO_INCREMENT,
  `loaiDocGia` varchar(50) DEFAULT NULL,
  `soSachToiDa` int DEFAULT NULL,
  `soNgayMuon` int DEFAULT NULL,
  `soLanGiaHan` int DEFAULT NULL,
  `soNgayGiaHan` int NOT NULL DEFAULT '7',
  `tienPhatMoiNgay` decimal(12,2) DEFAULT NULL,
  `tienPhatHuHongNhe` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tienPhatHuHongNang` decimal(12,2) NOT NULL DEFAULT '0.00',
  `ngayApDung` date DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maChinhSach`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.chinhsachmuon: ~7 rows (approximately)
INSERT INTO `chinhsachmuon` (`maChinhSach`, `loaiDocGia`, `soSachToiDa`, `soNgayMuon`, `soLanGiaHan`, `soNgayGiaHan`, `tienPhatMoiNgay`, `tienPhatHuHongNhe`, `tienPhatHuHongNang`, `ngayApDung`, `trangThai`) VALUES
	(1, 'sinhVien', 3, 14, 1, 7, 5000.00, 0.00, 0.00, '2026-09-01', 'ngungApDung'),
	(2, 'giangVien', 5, 30, 2, 7, 5000.00, 0.00, 0.00, '2026-09-01', 'ngungApDung'),
	(3, 'sinhVien', 2, 10, 1, 7, 3000.00, 0.00, 0.00, '2026-01-01', 'ngungApDung'),
	(4, 'giangVien', 5, 30, 2, 14, 5000.00, 0.00, 0.00, '2026-10-03', 'ngungApDung'),
	(5, 'sinhVien', 3, 14, 1, 7, 500000.00, 50000.00, 100000.00, '2026-10-03', 'ngungApDung'),
	(6, 'sinhVien', 3, 14, 1, 7, 5000.00, 50000.00, 100000.00, '2026-10-03', 'dangApDung'),
	(7, 'giangVien', 5, 30, 2, 14, 5000.00, 50000.00, 100000.00, '2026-10-04', 'dangApDung');

-- Dumping structure for table thuvienlibra.chitietphieumuon
CREATE TABLE IF NOT EXISTS `chitietphieumuon` (
  `maChiTietPhieuMuon` int NOT NULL AUTO_INCREMENT,
  `maPhieuMuon` int DEFAULT NULL,
  `maTaiLieu` int NOT NULL,
  `hanTra` date DEFAULT NULL,
  `ngayTra` datetime DEFAULT NULL,
  `tinhTrangKhiTra` varchar(30) DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maChiTietPhieuMuon`),
  KEY `maPhieuMuon` (`maPhieuMuon`),
  KEY `fkChiTietPhieuMuonTaiLieu` (`maTaiLieu`),
  CONSTRAINT `chitietphieumuon_ibfk_1` FOREIGN KEY (`maPhieuMuon`) REFERENCES `phieumuon` (`maPhieuMuon`),
  CONSTRAINT `fkChiTietPhieuMuonTaiLieu` FOREIGN KEY (`maTaiLieu`) REFERENCES `tailieu` (`maTaiLieu`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.chitietphieumuon: ~7 rows (approximately)
INSERT INTO `chitietphieumuon` (`maChiTietPhieuMuon`, `maPhieuMuon`, `maTaiLieu`, `hanTra`, `ngayTra`, `tinhTrangKhiTra`, `trangThai`) VALUES
	(1, 1, 1, '2026-10-23', '2026-10-03 15:53:13', 'nguyenVen', 'daTra'),
	(2, 2, 6, '2026-10-17', '2026-10-03 16:08:38', 'nguyenVen', 'daTra'),
	(3, 3, 6, '2026-10-18', '2026-10-04 14:44:54', 'huHongNhe', 'daTra'),
	(4, 4, 5, '2026-09-30', '2026-10-04 15:02:03', 'nguyenVen', 'daTra'),
	(5, 5, 1, '2026-09-21', '2026-10-04 15:11:20', 'huHongNhe', 'daTra'),
	(6, 5, 3, '2026-09-21', '2026-10-04 15:11:20', 'huHongNhe', 'daTra'),
	(7, 5, 2, '2026-09-21', '2026-10-04 15:11:20', 'huHongNhe', 'daTra'),
	(8, 6, 8, '2026-10-24', NULL, NULL, 'dangMuon');

-- Dumping structure for table thuvienlibra.chitietyeucaumuon
CREATE TABLE IF NOT EXISTS `chitietyeucaumuon` (
  `maChiTietYeuCauMuon` int NOT NULL AUTO_INCREMENT,
  `maYeuCauMuon` int DEFAULT NULL,
  `maTaiLieu` int DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maChiTietYeuCauMuon`),
  KEY `maYeuCauMuon` (`maYeuCauMuon`),
  KEY `maTaiLieu` (`maTaiLieu`),
  CONSTRAINT `chitietyeucaumuon_ibfk_1` FOREIGN KEY (`maYeuCauMuon`) REFERENCES `yeucaumuon` (`maYeuCauMuon`),
  CONSTRAINT `chitietyeucaumuon_ibfk_2` FOREIGN KEY (`maTaiLieu`) REFERENCES `tailieu` (`maTaiLieu`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.chitietyeucaumuon: ~6 rows (approximately)
INSERT INTO `chitietyeucaumuon` (`maChiTietYeuCauMuon`, `maYeuCauMuon`, `maTaiLieu`, `trangThai`) VALUES
	(2, 2, 1, 'choDuyet'),
	(3, 3, 6, 'choDuyet'),
	(4, 4, 6, 'choDuyet'),
	(5, 5, 5, 'choDuyet'),
	(6, 6, 8, NULL),
	(7, 7, 5, NULL);

-- Dumping structure for table thuvienlibra.hosodocgia
CREATE TABLE IF NOT EXISTS `hosodocgia` (
  `maDocGia` int NOT NULL AUTO_INCREMENT,
  `maNguoiDung` int DEFAULT NULL,
  `maSo` varchar(30) DEFAULT NULL,
  `maKhoa` int DEFAULT NULL,
  `loaiDocGia` varchar(50) DEFAULT NULL,
  `trangThaiThe` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maDocGia`),
  UNIQUE KEY `maNguoiDung` (`maNguoiDung`),
  UNIQUE KEY `maSo` (`maSo`),
  KEY `maKhoa` (`maKhoa`),
  CONSTRAINT `hosodocgia_ibfk_1` FOREIGN KEY (`maNguoiDung`) REFERENCES `nguoidung` (`maNguoiDung`),
  CONSTRAINT `hosodocgia_ibfk_2` FOREIGN KEY (`maKhoa`) REFERENCES `khoa` (`maKhoa`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.hosodocgia: ~3 rows (approximately)
INSERT INTO `hosodocgia` (`maDocGia`, `maNguoiDung`, `maSo`, `maKhoa`, `loaiDocGia`, `trangThaiThe`) VALUES
	(1, 4, '22110432', 1, 'sinhVien', 'hoatDong'),
	(2, 5, '22110820', 2, 'sinhVien', 'hoatDong'),
	(3, 6, '22110615', 3, 'sinhVien', 'tamKhoa');

-- Dumping structure for table thuvienlibra.khoa
CREATE TABLE IF NOT EXISTS `khoa` (
  `maKhoa` int NOT NULL AUTO_INCREMENT,
  `ma` varchar(30) DEFAULT NULL,
  `ten` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`maKhoa`),
  UNIQUE KEY `ma` (`ma`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.khoa: ~4 rows (approximately)
INSERT INTO `khoa` (`maKhoa`, `ma`, `ten`) VALUES
	(1, 'CNTT', 'Công nghệ thông tin'),
	(2, 'KT', 'Kinh tế'),
	(3, 'LUAT', 'Luật'),
	(4, 'NN', 'Ngoại ngữ'),
	(5, 'YD', 'Y dược');

-- Dumping structure for table thuvienlibra.nguoidung
CREATE TABLE IF NOT EXISTS `nguoidung` (
  `maNguoiDung` int NOT NULL AUTO_INCREMENT,
  `maVaiTro` int DEFAULT NULL,
  `tenDangNhap` varchar(50) DEFAULT NULL,
  `matKhau` varchar(255) DEFAULT NULL,
  `hoTen` varchar(150) DEFAULT NULL,
  `thuDienTu` varchar(150) DEFAULT NULL,
  `soDienThoai` varchar(20) DEFAULT NULL,
  `ngaySinh` date DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  `ngayTao` datetime DEFAULT NULL,
  PRIMARY KEY (`maNguoiDung`),
  UNIQUE KEY `tenDangNhap` (`tenDangNhap`),
  KEY `maVaiTro` (`maVaiTro`),
  CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`maVaiTro`) REFERENCES `vaitro` (`maVaiTro`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.nguoidung: ~5 rows (approximately)
INSERT INTO `nguoidung` (`maNguoiDung`, `maVaiTro`, `tenDangNhap`, `matKhau`, `hoTen`, `thuDienTu`, `soDienThoai`, `ngaySinh`, `trangThai`, `ngayTao`) VALUES
	(1, 1, 'ADMIN', '$2y$10$FlLg6PiJW6AmNFvlCUI9meV38/MMVLiP/RekJAl5kxeIfyyB8Xi5q', '', '', '', '1999-01-01', 'hoatDong', '2026-09-01 08:00:00'),
	(2, 2, 'thuthu01', '$2y$10$iIlxmP2fy3ZEJh8JVJsW/eBIJ.mwmvqnez0JcnRY9qjZxWTltDWem', 'Phạm Khánh Linh', 'linh@libra.edu.vn', '0901000002', '1994-06-12', 'hoatDong', '2026-09-01 08:05:00'),
	(4, 3, '22110432', '$2y$10$UXJxYN/j3u6QvGIJtC1xXuWXZgMJpSvQQ.IY2ebxut/tFk8.410Jq', 'Nguyễn Minh Anh', 'minhanh@student.edu.vn', '0901234567', '2003-05-12', 'hoatDong', '2026-09-02 08:00:00'),
	(5, 3, '22110820', '$2y$12$ei9Sxrook5HdOSLXc6cIrOxS5Hn.Qwkks2tASSPi4yGs5cECJha02', 'Lê Thị Hương', 'huong@student.edu.vn', '0902345678', '2003-08-20', 'hoatDong', '2026-09-02 08:10:00'),
	(6, 3, '22110615', '$2y$10$QAwDQDw72KZq0ntyiqbKEudYy4i64xO.ahO.GcrlTSFejDgLLRaZO', 'Phạm Quốc Bảo', 'quocbao@student.edu.vn', '0903456789', '2003-06-15', 'tamKhoa', '2026-09-02 08:20:00');

-- Dumping structure for table thuvienlibra.nhatkyhethong
CREATE TABLE IF NOT EXISTS `nhatkyhethong` (
  `maNhatKy` int NOT NULL AUTO_INCREMENT,
  `maNguoiDung` int DEFAULT NULL,
  `hanhDong` varchar(100) DEFAULT NULL,
  `doiTuong` varchar(100) DEFAULT NULL,
  `maDoiTuong` int DEFAULT NULL,
  `ngayTao` datetime DEFAULT NULL,
  PRIMARY KEY (`maNhatKy`),
  KEY `maNguoiDung` (`maNguoiDung`),
  CONSTRAINT `nhatkyhethong_ibfk_1` FOREIGN KEY (`maNguoiDung`) REFERENCES `nguoidung` (`maNguoiDung`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.nhatkyhethong: ~4 rows (approximately)
INSERT INTO `nhatkyhethong` (`maNhatKy`, `maNguoiDung`, `hanhDong`, `doiTuong`, `maDoiTuong`, `ngayTao`) VALUES
	(1, 1, 'capNhatChinhSach', 'chinhSachMuon', 4, '2026-10-03 15:52:17'),
	(2, 1, 'capNhatChinhSach', 'chinhSachMuon', 5, '2026-10-03 16:27:12'),
	(3, 1, 'capNhatChinhSach', 'chinhSachMuon', 6, '2026-10-03 16:30:18'),
	(4, 1, 'capNhatChinhSach', 'chinhSachMuon', 7, '2026-10-04 14:20:20');

-- Dumping structure for table thuvienlibra.nhaxuatban
CREATE TABLE IF NOT EXISTS `nhaxuatban` (
  `maNhaXuatBan` int NOT NULL AUTO_INCREMENT,
  `ma` varchar(30) DEFAULT NULL,
  `ten` varchar(200) DEFAULT NULL,
  `diaChi` varchar(255) DEFAULT NULL,
  `soDienThoai` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`maNhaXuatBan`),
  UNIQUE KEY `ma` (`ma`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.nhaxuatban: ~8 rows (approximately)
INSERT INTO `nhaxuatban` (`maNhaXuatBan`, `ma`, `ten`, `diaChi`, `soDienThoai`) VALUES
	(1, 'NXBGD', 'Nhà xuất bản Giáo dục Việt Nam', 'Hà Nội', '02438220801'),
	(2, 'NXBLD', 'Nhà xuất bản Lao động', 'Hà Nội', '02438515380'),
	(3, 'PEARSON', 'Pearson Education', 'London', '442070100000'),
	(4, 'NXBDHQGHCM', 'Nhà xuất bản Đại học Quốc gia TP Hồ Chí Minh', NULL, NULL),
	(5, 'NXBKT', 'Nhà xuất bản Kinh tế', NULL, NULL),
	(6, 'NXBDHKTQD', 'Nhà xuất bản Đại học Kinh Tế Quốc Dân', NULL, NULL),
	(7, 'NXBDHQGHN', 'Nhà xuất bản Đại học Quốc Gia Hà Nội', NULL, NULL),
	(8, 'NXBYH', 'Nhà xuất bản Y Học', NULL, NULL);

-- Dumping structure for table thuvienlibra.phieumuon
CREATE TABLE IF NOT EXISTS `phieumuon` (
  `maPhieuMuon` int NOT NULL AUTO_INCREMENT,
  `maDocGia` int DEFAULT NULL,
  `maThuThu` int DEFAULT NULL,
  `maChinhSach` int DEFAULT NULL,
  `maYeuCauMuon` int DEFAULT NULL,
  `ngayMuon` datetime DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maPhieuMuon`),
  KEY `maDocGia` (`maDocGia`),
  KEY `maThuThu` (`maThuThu`),
  KEY `maChinhSach` (`maChinhSach`),
  KEY `maYeuCauMuon` (`maYeuCauMuon`),
  CONSTRAINT `phieumuon_ibfk_1` FOREIGN KEY (`maDocGia`) REFERENCES `hosodocgia` (`maDocGia`),
  CONSTRAINT `phieumuon_ibfk_2` FOREIGN KEY (`maThuThu`) REFERENCES `nguoidung` (`maNguoiDung`),
  CONSTRAINT `phieumuon_ibfk_3` FOREIGN KEY (`maChinhSach`) REFERENCES `chinhsachmuon` (`maChinhSach`),
  CONSTRAINT `phieumuon_ibfk_4` FOREIGN KEY (`maYeuCauMuon`) REFERENCES `yeucaumuon` (`maYeuCauMuon`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.phieumuon: ~6 rows (approximately)
INSERT INTO `phieumuon` (`maPhieuMuon`, `maDocGia`, `maThuThu`, `maChinhSach`, `maYeuCauMuon`, `ngayMuon`, `trangThai`) VALUES
	(1, 1, 2, 1, 2, '2026-10-02 23:01:33', 'daTra'),
	(2, 1, 2, 1, 3, '2026-10-03 16:08:04', 'daTra'),
	(3, 1, 2, 6, 4, '2026-10-04 14:44:39', 'daTra'),
	(4, 1, 2, 6, 5, '2026-09-23 14:56:41', 'daTra'),
	(5, 2, 2, 6, NULL, '2026-09-14 15:09:51', 'daTra'),
	(6, 1, 2, 6, 6, '2026-10-10 15:24:10', 'dangMuon');

-- Dumping structure for table thuvienlibra.phieuphat
CREATE TABLE IF NOT EXISTS `phieuphat` (
  `maPhieuPhat` int NOT NULL AUTO_INCREMENT,
  `maDocGia` int DEFAULT NULL,
  `maChiTietPhieuMuon` int DEFAULT NULL,
  `loaiPhat` varchar(30) DEFAULT NULL,
  `soTien` decimal(12,2) DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  `ngayLap` datetime DEFAULT NULL,
  `maNguoiThu` int DEFAULT NULL,
  `ngayThanhToan` datetime DEFAULT NULL,
  PRIMARY KEY (`maPhieuPhat`),
  KEY `maDocGia` (`maDocGia`),
  KEY `maChiTietPhieuMuon` (`maChiTietPhieuMuon`),
  KEY `maNguoiThu` (`maNguoiThu`),
  CONSTRAINT `phieuphat_ibfk_1` FOREIGN KEY (`maDocGia`) REFERENCES `hosodocgia` (`maDocGia`),
  CONSTRAINT `phieuphat_ibfk_2` FOREIGN KEY (`maChiTietPhieuMuon`) REFERENCES `chitietphieumuon` (`maChiTietPhieuMuon`),
  CONSTRAINT `phieuphat_ibfk_3` FOREIGN KEY (`maNguoiThu`) REFERENCES `nguoidung` (`maNguoiDung`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.phieuphat: ~8 rows (approximately)
INSERT INTO `phieuphat` (`maPhieuPhat`, `maDocGia`, `maChiTietPhieuMuon`, `loaiPhat`, `soTien`, `trangThai`, `ngayLap`, `maNguoiThu`, `ngayThanhToan`) VALUES
	(1, 1, 3, 'huHongNhe', 50000.00, 'daThanhToan', '2026-10-04 14:44:54', 2, '2026-10-04 14:52:11'),
	(2, 1, 4, 'quaHan', 20000.00, 'daThanhToan', '2026-10-04 15:02:03', 2, '2026-10-04 15:04:06'),
	(3, 2, 5, 'quaHan', 65000.00, 'daThanhToan', '2026-10-04 15:11:20', 2, '2026-10-04 15:11:27'),
	(4, 2, 5, 'huHongNhe', 50000.00, 'daThanhToan', '2026-10-04 15:11:20', 2, '2026-10-04 15:11:28'),
	(5, 2, 6, 'quaHan', 65000.00, 'daThanhToan', '2026-10-04 15:11:20', 2, '2026-10-04 15:11:29'),
	(6, 2, 6, 'huHongNhe', 50000.00, 'daThanhToan', '2026-10-04 15:11:20', 2, '2026-10-04 15:11:29'),
	(7, 2, 7, 'quaHan', 65000.00, 'daThanhToan', '2026-10-04 15:11:20', 2, '2026-10-04 15:11:30'),
	(8, 2, 7, 'huHongNhe', 50000.00, 'daThanhToan', '2026-10-04 15:11:20', 2, '2026-10-04 15:11:30');

-- Dumping structure for table thuvienlibra.tailieu
CREATE TABLE IF NOT EXISTS `tailieu` (
  `maTaiLieu` int NOT NULL AUTO_INCREMENT,
  `ma` varchar(30) DEFAULT NULL,
  `maKhoa` int NOT NULL,
  `maNhaXuatBan` int DEFAULT NULL,
  `tieuDe` varchar(500) DEFAULT NULL,
  `maIsbn` varchar(30) DEFAULT NULL,
  `loaiTaiLieu` varchar(50) DEFAULT NULL,
  `namXuatBan` int DEFAULT NULL,
  `anhBia` varchar(255) DEFAULT NULL,
  `soLuongTong` int NOT NULL DEFAULT '0',
  `soLuongCon` int NOT NULL DEFAULT '0',
  `tomTat` text,
  `duocMuon` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`maTaiLieu`),
  UNIQUE KEY `ma` (`ma`),
  UNIQUE KEY `maIsbn` (`maIsbn`),
  KEY `maNhaXuatBan` (`maNhaXuatBan`),
  KEY `fkTaiLieuKhoa` (`maKhoa`),
  CONSTRAINT `fkTaiLieuKhoa` FOREIGN KEY (`maKhoa`) REFERENCES `khoa` (`maKhoa`),
  CONSTRAINT `tailieu_ibfk_2` FOREIGN KEY (`maNhaXuatBan`) REFERENCES `nhaxuatban` (`maNhaXuatBan`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.tailieu: ~8 rows (approximately)
INSERT INTO `tailieu` (`maTaiLieu`, `ma`, `maKhoa`, `maNhaXuatBan`, `tieuDe`, `maIsbn`, `loaiTaiLieu`, `namXuatBan`, `anhBia`, `soLuongTong`, `soLuongCon`, `tomTat`, `duocMuon`) VALUES
	(1, 'CNTT-101', 1, 4, 'Cơ Sở Dữ Liệu', '9780073523323', 'giaoTrinh', 2019, 'assets/images/sach/843adc6ff01423144b04ccc7934689ac.jpg', 4, 4, 'Giáo trình nền tảng về hệ quản trị cơ sở dữ liệu.', 'co'),
	(2, 'CNTT-145', 1, 7, 'Nhập Môn Trí Tuệ Nhân Tạo', '9780136042594', 'giaoTrinh', 2021, 'assets/images/sach/c2e699386ebb3528adff82a6a1fb96c5.jpg', 5, 5, 'Giáo trình về trí tuệ nhân tạo hiện đại.', 'co'),
	(3, 'CNTT-128', 1, 3, 'Lập Trình Java Hướng Đối Tượng', '9781260440232', 'giaoTrinh', 2021, 'assets/images/sach/861aaca58c7202781426c046216c0fb4.jpg', 5, 5, 'Tài liệu học lập trình Java hướng đối tượng.', 'co'),
	(4, 'KT-021', 2, 6, 'Nguyên Lý Kế Toán', '9786040301234', 'giaoTrinh', 2022, 'assets/images/sach/2d3d6ea4b5f8b08f259fb31612a138fc.jpg', 4, 4, 'Kiến thức nền tảng về kế toán.', 'co'),
	(5, 'KT-044', 2, 5, 'Kinh Tế Vi Mô', '9781305585126', 'giaoTrinh', 2020, 'assets/images/sach/cddf4f8be54fe4f2c431f3fb87813d59.webp', 2, 2, 'Các nguyên lý kinh tế vi mô.', 'co'),
	(6, 'LUAT-032', 3, 2, 'Luật Dân Sự Việt Nam', '9786047269872', 'giaoTrinh', 2023, 'assets/images/sach/97de4473893eb444b3ff756a46d8363b.webp', 2, 2, 'Giáo trình luật dân sự.', 'co'),
	(7, 'YD-027', 5, 8, 'Sinh Lý Học Y Khoa', '9780323597128', 'giaoTrinh', 2021, 'assets/images/sach/b36ddb32b87bac3cc37a2a8a5cc92cb0.jpg', 3, 3, 'Tài liệu sinh lý học cơ bản.', 'co'),
	(8, 'NNA-019', 4, 3, 'English For Academic Purposes', '9780194001780', 'giaoTrinh', 2020, 'assets/images/sach/b9f53681e3c47a5480e4ddab30edba59.jpg', 1, 0, 'Tiếng Anh học thuật cho sinh viên.', 'co');

-- Dumping structure for table thuvienlibra.vaitro
CREATE TABLE IF NOT EXISTS `vaitro` (
  `maVaiTro` int NOT NULL AUTO_INCREMENT,
  `ma` varchar(30) DEFAULT NULL,
  `ten` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`maVaiTro`),
  UNIQUE KEY `ma` (`ma`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.vaitro: ~3 rows (approximately)
INSERT INTO `vaitro` (`maVaiTro`, `ma`, `ten`) VALUES
	(1, 'ADMIN', 'Quản trị viên'),
	(2, 'THUTHU', 'Thủ thư'),
	(3, 'DOCGIA', 'Độc giả');

-- Dumping structure for table thuvienlibra.yeucaugiahan
CREATE TABLE IF NOT EXISTS `yeucaugiahan` (
  `maYeuCauGiaHan` int NOT NULL AUTO_INCREMENT,
  `maChiTietPhieuMuon` int DEFAULT NULL,
  `ngayYeuCau` datetime DEFAULT NULL,
  `hanTraMoi` date DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maYeuCauGiaHan`),
  KEY `maChiTietPhieuMuon` (`maChiTietPhieuMuon`),
  CONSTRAINT `yeucaugiahan_ibfk_1` FOREIGN KEY (`maChiTietPhieuMuon`) REFERENCES `chitietphieumuon` (`maChiTietPhieuMuon`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.yeucaugiahan: ~1 rows (approximately)
INSERT INTO `yeucaugiahan` (`maYeuCauGiaHan`, `maChiTietPhieuMuon`, `ngayYeuCau`, `hanTraMoi`, `trangThai`) VALUES
	(1, 1, '2026-10-02 23:03:03', '2026-10-23', 'daDuyet');

-- Dumping structure for table thuvienlibra.yeucaumuon
CREATE TABLE IF NOT EXISTS `yeucaumuon` (
  `maYeuCauMuon` int NOT NULL AUTO_INCREMENT,
  `maDocGia` int DEFAULT NULL,
  `maNguoiXuLy` int DEFAULT NULL,
  `ngayYeuCau` datetime DEFAULT NULL,
  `ngayXuLy` datetime DEFAULT NULL,
  `trangThai` varchar(30) DEFAULT NULL,
  `lyDoTuChoi` text,
  PRIMARY KEY (`maYeuCauMuon`),
  KEY `maDocGia` (`maDocGia`),
  KEY `maNguoiXuLy` (`maNguoiXuLy`),
  CONSTRAINT `yeucaumuon_ibfk_1` FOREIGN KEY (`maDocGia`) REFERENCES `hosodocgia` (`maDocGia`),
  CONSTRAINT `yeucaumuon_ibfk_2` FOREIGN KEY (`maNguoiXuLy`) REFERENCES `nguoidung` (`maNguoiDung`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table thuvienlibra.yeucaumuon: ~6 rows (approximately)
INSERT INTO `yeucaumuon` (`maYeuCauMuon`, `maDocGia`, `maNguoiXuLy`, `ngayYeuCau`, `ngayXuLy`, `trangThai`, `lyDoTuChoi`) VALUES
	(2, 1, 2, '2026-10-02 22:58:02', '2026-10-02 23:01:33', 'daDuyet', NULL),
	(3, 1, 2, '2026-10-03 16:07:55', '2026-10-03 16:08:04', 'daDuyet', NULL),
	(4, 1, 2, '2026-10-04 14:44:28', '2026-10-04 14:44:39', 'daDuyet', NULL),
	(5, 1, 2, '2026-10-04 14:56:23', '2026-10-04 14:56:41', 'daDuyet', NULL),
	(6, 1, 2, '2026-10-10 15:19:37', '2026-10-10 15:24:10', 'daDuyet', NULL),
	(7, 1, 2, '2026-10-10 15:24:27', '2026-10-10 15:28:09', 'tuChoi', 'Đã hết, do lỗi nên chưa cập nhật');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
