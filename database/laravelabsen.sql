-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for laravelabsen
CREATE DATABASE IF NOT EXISTS `laravelabsen` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `laravelabsen`;

-- Dumping structure for table laravelabsen.absens
CREATE TABLE IF NOT EXISTS `absens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time DEFAULT NULL,
  `foto_masuk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_masuk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `laporan_masuk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `laporan_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_validasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_absen_user_id` (`user_id`),
  CONSTRAINT `fk_absen_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.absens: ~0 rows (approximately)
INSERT INTO `absens` (`id`, `user_id`, `email`, `nama`, `status`, `keterangan`, `tanggal`, `tanggal_keluar`, `jam_masuk`, `jam_keluar`, `foto_masuk`, `foto_keluar`, `lokasi_masuk`, `lokasi_keluar`, `laporan_masuk`, `laporan_keluar`, `status_validasi`, `created_at`, `updated_at`) VALUES
	(9, 3, 'fariz@gmail.com', 'Fariz', 'H', '', '2025-07-26', NULL, '14:35:28', '14:37:44', 'fariz@gmail.com-2025-07-26-masuk.jpeg', 'fariz@gmail.com-2025-07-26-keluar.jpeg', '-6.029312,106.0503552|Jalan Gelatik, Kompleks BBS II, Bendungan, Cilegon, Java, 42411, Indonesia', '-6.029312,106.0503552|Jalan Gelatik, Kompleks BBS II, Bendungan, Cilegon, Java, 42411, Indonesia', '', '', '0', '2025-07-26 07:35:28', '2025-07-26 07:37:44'),
	(10, 2, 'adimasrizki926@gmail.com', 'Adimas Rizki', 'H', '', '2025-07-26', NULL, '16:13:44', NULL, 'adimasrizki926@gmail.com-2025-07-26-masuk.jpeg', '', '-6.029312,106.0503552|Jalan Gelatik, Kompleks BBS II, Bendungan, Cilegon, Java, 42411, Indonesia', '', '', '', '0', '2025-07-26 09:13:44', '2025-07-26 09:13:44');

-- Dumping structure for table laravelabsen.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table laravelabsen.jabatan
CREATE TABLE IF NOT EXISTS `jabatan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_jabatan` int DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jabatan_id_jabatan_unique` (`id_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.jabatan: ~5 rows (approximately)
INSERT INTO `jabatan` (`id`, `id_jabatan`, `jabatan`, `created_at`, `updated_at`) VALUES
	(1, 1, 'SUPERADMIN', NULL, NULL),
	(2, 2, 'OFFICE', NULL, NULL),
	(6, 3, 'MANAGER', '2025-07-26 08:10:23', '2025-07-26 08:14:36'),
	(7, 4, 'HRD', '2025-07-26 08:16:18', '2025-07-26 08:16:18');

-- Dumping structure for table laravelabsen.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.migrations: ~0 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2023_12_04_050038_create_absensi_table', 1),
	(6, '2023_12_09_134907_create_pengajuan_izin_table', 1),
	(7, '2023_12_11_190640_create_jabatan_table', 1),
	(8, '2025_07_26_141530_create_settings_table', 2);

-- Dumping structure for table laravelabsen.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table laravelabsen.pengajuan_izin
CREATE TABLE IF NOT EXISTS `pengajuan_izin` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_izin` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `evident` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approved` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.pengajuan_izin: ~0 rows (approximately)

-- Dumping structure for table laravelabsen.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table laravelabsen.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.settings: ~3 rows (approximately)
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'jam_masuk_start', '16:00:00', '2025-07-26 07:16:04', '2025-07-26 09:13:16'),
	(2, 'jam_masuk_end', '16:15:00', '2025-07-26 07:16:04', '2025-07-26 09:13:16'),
	(3, 'jam_keluar_min', '16:20:00', '2025-07-26 07:16:04', '2025-07-26 09:13:16');

-- Dumping structure for table laravelabsen.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `perner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_telegram` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravelabsen.users: ~2 rows (approximately)
INSERT INTO `users` (`id`, `perner`, `nama`, `email`, `email_verified_at`, `password`, `jabatan`, `id_telegram`, `foto`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Super Admin', 'superadmin@gmail.com', NULL, '$2y$12$aqVWfBHzdlQz1ur5WbhHReTuGLQndSHMtFv/Dlcm8us/9CdIjNRvq', 'SUPERADMIN', '', NULL, NULL, NULL, NULL),
	(2, NULL, 'Adimas Rizki', 'adimasrizki926@gmail.com', NULL, '$2y$12$mVuo/Yz1/4NtPq.Ey7jFPeo3mfc9DHBDzusLKU4flt1Id72R0IriC', 'MANAJER AREA', '', 'adimasrizki926@gmail.com.jpeg', NULL, NULL, '2025-07-26 08:52:15'),
	(3, NULL, 'Fariz', 'fariz@gmail.com', NULL, '$2y$12$OSzqLfbqK2hpX2XoXaGhbu2cz2PFysiojjGjDklQjyx8zJIuyNv5a', 'OFFICE', '', 'fariz@gmail.com.jpeg', NULL, NULL, NULL),
	(4, NULL, 'test', 'ridhoaziz18@gmail.com', NULL, '$2y$12$JMnBlIr2SxCZNQ8HqwL3FezAnmuioAYC9SaT3p0WZ6snwt5K4g3AC', 'MANAGER', '', 'ridhoaziz18@gmail.com.jpg', NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
