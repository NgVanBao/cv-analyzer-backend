-- cv_analyzer.migrations definition

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.nguoi_dung definition

CREATE TABLE `nguoi_dung` (
  `MaTaiKhoan` int unsigned NOT NULL AUTO_INCREMENT,
  `HoTen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MatKhau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Vaitro` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaTaiKhoan`),
  UNIQUE KEY `nguoi_dung_email_unique` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.tin_tuyen_dung definition

CREATE TABLE `tin_tuyen_dung` (
  `MaTuyenDung` int unsigned NOT NULL AUTO_INCREMENT,
  `TieuDe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TenCongTy` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTaChiTiet` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `LuongToiThieu` int DEFAULT NULL,
  `LuongToiDa` int DEFAULT NULL,
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaTuyenDung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.tu_dien_ky_nang definition

CREATE TABLE `tu_dien_ky_nang` (
  `MaKyNang` int unsigned NOT NULL AUTO_INCREMENT,
  `TenKyNang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LoaiKyNang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaKyNang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.ho_so_cv definition

CREATE TABLE `ho_so_cv` (
  `MaCV` int unsigned NOT NULL AUTO_INCREMENT,
  `MaTaiKhoan` int unsigned NOT NULL,
  `TenFile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DuongDanFile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DuLieuAITrichXuat` text COLLATE utf8mb4_unicode_ci,
  `TrangThaiXuLy` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TrinhDoHocVan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `KinhNghiem` text COLLATE utf8mb4_unicode_ci,
  `KyNang` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaCV`),
  KEY `ho_so_cv_mataikhoan_foreign` (`MaTaiKhoan`),
  CONSTRAINT `ho_so_cv_mataikhoan_foreign` FOREIGN KEY (`MaTaiKhoan`) REFERENCES `nguoi_dung` (`MaTaiKhoan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.hoc_van definition

CREATE TABLE `hoc_van` (
  `MaHocVan` int unsigned NOT NULL AUTO_INCREMENT,
  `MaCV` int unsigned NOT NULL,
  `TenTruong` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ChuyenNganh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BangCap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ThoiGianTu` date NOT NULL,
  `ThoiGianDen` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaHocVan`),
  KEY `hoc_van_macv_foreign` (`MaCV`),
  CONSTRAINT `hoc_van_macv_foreign` FOREIGN KEY (`MaCV`) REFERENCES `ho_so_cv` (`MaCV`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.ket_qua_goi_y definition

CREATE TABLE `ket_qua_goi_y` (
  `MaKetQua` int unsigned NOT NULL AUTO_INCREMENT,
  `MaCV` int unsigned NOT NULL,
  `MaTuyenDung` int unsigned NOT NULL,
  `TyLePhuHop` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaKetQua`),
  KEY `ket_qua_goi_y_macv_foreign` (`MaCV`),
  KEY `ket_qua_goi_y_matuyendung_foreign` (`MaTuyenDung`),
  CONSTRAINT `ket_qua_goi_y_macv_foreign` FOREIGN KEY (`MaCV`) REFERENCES `ho_so_cv` (`MaCV`) ON DELETE CASCADE,
  CONSTRAINT `ket_qua_goi_y_matuyendung_foreign` FOREIGN KEY (`MaTuyenDung`) REFERENCES `tin_tuyen_dung` (`MaTuyenDung`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.kinh_nghiem_lam_viec definition

CREATE TABLE `kinh_nghiem_lam_viec` (
  `MaKinhNghiem` int unsigned NOT NULL AUTO_INCREMENT,
  `MaCV` int unsigned NOT NULL,
  `TenCongTy` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ViTriCongTac` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ThoiGianTu` date NOT NULL,
  `ThoiGianDen` date DEFAULT NULL,
  `MoTaChiTiet` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaKinhNghiem`),
  KEY `kinh_nghiem_lam_viec_macv_foreign` (`MaCV`),
  CONSTRAINT `kinh_nghiem_lam_viec_macv_foreign` FOREIGN KEY (`MaCV`) REFERENCES `ho_so_cv` (`MaCV`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.ky_nang_trong_cv definition

CREATE TABLE `ky_nang_trong_cv` (
  `MaKyNang` int unsigned NOT NULL,
  `MaCV` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaKyNang`,`MaCV`),
  KEY `ky_nang_trong_cv_macv_foreign` (`MaCV`),
  CONSTRAINT `ky_nang_trong_cv_macv_foreign` FOREIGN KEY (`MaCV`) REFERENCES `ho_so_cv` (`MaCV`) ON DELETE CASCADE,
  CONSTRAINT `ky_nang_trong_cv_makynang_foreign` FOREIGN KEY (`MaKyNang`) REFERENCES `tu_dien_ky_nang` (`MaKyNang`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.ky_nang_yeu_cau definition

CREATE TABLE `ky_nang_yeu_cau` (
  `MaKyNang` int unsigned NOT NULL,
  `MaTuyenDung` int unsigned NOT NULL,
  `TrongSoDiem` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaKyNang`,`MaTuyenDung`),
  KEY `ky_nang_yeu_cau_matuyendung_foreign` (`MaTuyenDung`),
  CONSTRAINT `ky_nang_yeu_cau_makynang_foreign` FOREIGN KEY (`MaKyNang`) REFERENCES `tu_dien_ky_nang` (`MaKyNang`) ON DELETE CASCADE,
  CONSTRAINT `ky_nang_yeu_cau_matuyendung_foreign` FOREIGN KEY (`MaTuyenDung`) REFERENCES `tin_tuyen_dung` (`MaTuyenDung`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- cv_analyzer.ai_log definition

CREATE TABLE `ai_log` (
  `MaLog` int unsigned NOT NULL AUTO_INCREMENT,
  `MaCV` int unsigned NOT NULL,
  `ThoiGian` date NOT NULL,
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NoiDungLog` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`MaLog`),
  KEY `ai_log_macv_foreign` (`MaCV`),
  CONSTRAINT `ai_log_macv_foreign` FOREIGN KEY (`MaCV`) REFERENCES `ho_so_cv` (`MaCV`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;