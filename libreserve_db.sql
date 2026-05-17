-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 17 May 2026, 17:06:31
-- Sunucu sürümü: 8.4.7
-- PHP Sürümü: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `libreserve_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

DROP TABLE IF EXISTS `kullanicilar`;
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad_soyad` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `sifre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('ogrenci','admin') COLLATE utf8mb4_general_ci DEFAULT 'ogrenci',
  `kayit_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ogrenci_no` (`email`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `ad_soyad`, `email`, `sifre`, `rol`, `kayit_tarihi`) VALUES
(4, 'Kübra Özaslan', 'ozaslnkubra@gmail.com', '$2y$10$zbRK/mCVeRdUCoBShX/B0ehu.GaYxdjK/PB6VryVs55heyhOqD4QS', 'ogrenci', '2026-05-09 16:14:12'),
(5, 'Seçil Aktaş', 'secil08@gmail.com', '$2y$10$OKGQNil5TGnB9nPBi5CpiehDCcQVz2M9b77naKMWgYKu.Wq/qnUKy', 'admin', '2026-05-09 16:54:51'),
(6, 'Zerya Eda ÖZER', 'zerya123@gmail.com', '$2y$10$TkvhxPy1yasZQDZ4xvFNeOQ54DGwcPf6ZrwhDiUVJ/hDMK36ImXze', 'ogrenci', '2026-05-09 20:00:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `masalar`
--

DROP TABLE IF EXISTS `masalar`;
CREATE TABLE IF NOT EXISTS `masalar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `masa_kodu` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `salon_adi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `durum` enum('bos','dolu','arizali') COLLATE utf8mb4_general_ci DEFAULT 'bos',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `masalar`
--

INSERT INTO `masalar` (`id`, `masa_kodu`, `salon_adi`, `durum`) VALUES
(1, 'A1', 'Ana Salon', 'bos'),
(2, 'A2', 'Ana Salon', 'bos'),
(3, 'A3', 'Ana Salon', 'bos'),
(4, 'A4', 'Ana Salon', 'bos'),
(5, 'A5', 'Ana Salon', 'bos'),
(6, 'A6', 'Ana Salon', 'bos'),
(7, 'A7', 'Ana Salon', 'bos'),
(8, 'A8', 'Ana Salon', 'bos'),
(9, 'A9', 'Ana Salon', 'bos'),
(10, 'A10', 'Ana Salon', 'bos'),
(11, 'A11', 'Ana Salon', 'bos'),
(12, 'A12', 'Ana Salon', 'bos'),
(13, 'A13', 'Ana Salon', 'bos'),
(14, 'A14', 'Ana Salon', 'bos'),
(15, 'A15', 'Ana Salon', 'bos'),
(16, 'A16', 'Ana Salon', 'bos'),
(17, 'A17', 'Ana Salon', 'bos'),
(18, 'A18', 'Ana Salon', 'bos'),
(19, 'A19', 'Ana Salon', 'bos'),
(20, 'A20', 'Ana Salon', 'bos'),
(21, 'A21', 'Ana Salon', 'bos'),
(22, 'A22', 'Ana Salon', 'bos'),
(23, 'A23', 'Ana Salon', 'bos'),
(24, 'A24', 'Ana Salon', 'bos'),
(25, 'A25', 'Ana Salon', 'bos'),
(26, 'A26', 'Ana Salon', 'bos'),
(27, 'A27', 'Ana Salon', 'bos'),
(28, 'A28', 'Ana Salon', 'bos'),
(29, 'A29', 'Ana Salon', 'bos'),
(30, 'A30', 'Ana Salon', 'bos'),
(31, 'A31', 'Ana Salon', 'bos'),
(32, 'A32', 'Ana Salon', 'bos'),
(33, 'A33', 'Ana Salon', 'bos'),
(34, 'A34', 'Ana Salon', 'bos'),
(35, 'A35', 'Ana Salon', 'bos'),
(36, 'A36', 'Ana Salon', 'bos'),
(37, 'A37', 'Ana Salon', 'bos'),
(38, 'A38', 'Ana Salon', 'bos'),
(39, 'A39', 'Ana Salon', 'bos'),
(40, 'A40', 'Ana Salon', 'bos'),
(41, 'A41', 'Ana Salon', 'bos'),
(42, 'A42', 'Ana Salon', 'bos'),
(43, 'A43', 'Ana Salon', 'bos'),
(44, 'A44', 'Ana Salon', 'bos'),
(45, 'A45', 'Ana Salon', 'bos'),
(46, 'A46', 'Ana Salon', 'bos'),
(47, 'A47', 'Ana Salon', 'bos'),
(48, 'A48', 'Ana Salon', 'bos'),
(49, 'A49', 'Ana Salon', 'bos'),
(50, 'A50', 'Ana Salon', 'bos'),
(51, 'A51', 'Ana Salon', 'bos'),
(52, 'A52', 'Ana Salon', 'bos'),
(53, 'A53', 'Ana Salon', 'bos'),
(54, 'A54', 'Ana Salon', 'bos'),
(55, 'A55', 'Ana Salon', 'bos'),
(56, 'A56', 'Ana Salon', 'bos'),
(57, 'A57', 'Ana Salon', 'bos'),
(58, 'A58', 'Ana Salon', 'bos'),
(59, 'A59', 'Ana Salon', 'bos'),
(60, 'A60', 'Ana Salon', 'bos'),
(61, 'A61', 'Ana Salon', 'bos'),
(62, 'A62', 'Ana Salon', 'bos'),
(63, 'A63', 'Ana Salon', 'bos'),
(64, 'A64', 'Ana Salon', 'bos'),
(65, 'A65', 'Ana Salon', 'bos'),
(66, 'A66', 'Ana Salon', 'bos'),
(67, 'A67', 'Ana Salon', 'bos'),
(68, 'A68', 'Ana Salon', 'bos'),
(69, 'A69', 'Ana Salon', 'bos'),
(70, 'A70', 'Ana Salon', 'bos'),
(71, 'A71', 'Ana Salon', 'bos'),
(72, 'A72', 'Ana Salon', 'bos'),
(73, 'A73', 'Ana Salon', 'bos'),
(74, 'A74', 'Ana Salon', 'bos'),
(75, 'A75', 'Ana Salon', 'bos'),
(76, 'A76', 'Ana Salon', 'bos'),
(77, 'A77', 'Ana Salon', 'bos'),
(78, 'A78', 'Ana Salon', 'bos'),
(79, 'A79', 'Ana Salon', 'bos'),
(80, 'A80', 'Ana Salon', 'bos'),
(81, 'S1', 'Sessiz Salon', 'bos'),
(82, 'S2', 'Sessiz Salon', 'bos'),
(83, 'S3', 'Sessiz Salon', 'bos'),
(84, 'S4', 'Sessiz Salon', 'bos'),
(85, 'S5', 'Sessiz Salon', 'bos'),
(86, 'S6', 'Sessiz Salon', 'bos'),
(87, 'S7', 'Sessiz Salon', 'bos'),
(88, 'S8', 'Sessiz Salon', 'bos'),
(89, 'S9', 'Sessiz Salon', 'bos'),
(90, 'S10', 'Sessiz Salon', 'bos'),
(91, 'S11', 'Sessiz Salon', 'bos'),
(92, 'S12', 'Sessiz Salon', 'bos'),
(93, 'S13', 'Sessiz Salon', 'bos'),
(94, 'S14', 'Sessiz Salon', 'bos'),
(95, 'S15', 'Sessiz Salon', 'bos'),
(96, 'S16', 'Sessiz Salon', 'bos'),
(97, 'S17', 'Sessiz Salon', 'bos'),
(98, 'S18', 'Sessiz Salon', 'bos'),
(99, 'S19', 'Sessiz Salon', 'bos'),
(100, 'S20', 'Sessiz Salon', 'bos'),
(101, 'S21', 'Sessiz Salon', 'bos'),
(102, 'S22', 'Sessiz Salon', 'bos'),
(103, 'S23', 'Sessiz Salon', 'bos'),
(104, 'S24', 'Sessiz Salon', 'bos'),
(105, 'S25', 'Sessiz Salon', 'bos'),
(106, 'S26', 'Sessiz Salon', 'bos'),
(107, 'S27', 'Sessiz Salon', 'bos'),
(108, 'S28', 'Sessiz Salon', 'bos'),
(109, 'S29', 'Sessiz Salon', 'bos'),
(110, 'S30', 'Sessiz Salon', 'bos'),
(111, 'S31', 'Sessiz Salon', 'bos'),
(112, 'S32', 'Sessiz Salon', 'bos'),
(113, 'S33', 'Sessiz Salon', 'bos'),
(114, 'S34', 'Sessiz Salon', 'bos'),
(115, 'S35', 'Sessiz Salon', 'bos'),
(116, 'S36', 'Sessiz Salon', 'bos'),
(117, 'S37', 'Sessiz Salon', 'bos'),
(118, 'S38', 'Sessiz Salon', 'bos'),
(119, 'S39', 'Sessiz Salon', 'bos'),
(120, 'S40', 'Sessiz Salon', 'bos'),
(121, 'S41', 'Sessiz Salon', 'bos'),
(122, 'S42', 'Sessiz Salon', 'bos'),
(123, 'S43', 'Sessiz Salon', 'bos'),
(124, 'S44', 'Sessiz Salon', 'bos'),
(125, 'S45', 'Sessiz Salon', 'bos'),
(126, 'S46', 'Sessiz Salon', 'bos'),
(127, 'S47', 'Sessiz Salon', 'bos'),
(128, 'S48', 'Sessiz Salon', 'bos'),
(129, 'G1', 'Grup Çalışma Salonu', 'bos'),
(130, 'G2', 'Grup Çalışma Salonu', 'bos'),
(131, 'G3', 'Grup Çalışma Salonu', 'bos'),
(132, 'G4', 'Grup Çalışma Salonu', 'bos'),
(133, 'G5', 'Grup Çalışma Salonu', 'bos'),
(134, 'G6', 'Grup Çalışma Salonu', 'bos'),
(135, 'B1', 'Bilgisayar Salonu', 'bos'),
(136, 'B2', 'Bilgisayar Salonu', 'bos'),
(137, 'B3', 'Bilgisayar Salonu', 'bos'),
(138, 'B4', 'Bilgisayar Salonu', 'bos'),
(139, 'B5', 'Bilgisayar Salonu', 'bos'),
(140, 'B6', 'Bilgisayar Salonu', 'bos'),
(141, 'B7', 'Bilgisayar Salonu', 'bos'),
(142, 'B8', 'Bilgisayar Salonu', 'bos'),
(143, 'B9', 'Bilgisayar Salonu', 'bos'),
(144, 'B10', 'Bilgisayar Salonu', 'bos'),
(145, 'B11', 'Bilgisayar Salonu', 'bos'),
(146, 'B12', 'Bilgisayar Salonu', 'bos'),
(147, 'B13', 'Bilgisayar Salonu', 'bos'),
(148, 'B14', 'Bilgisayar Salonu', 'bos'),
(149, 'B15', 'Bilgisayar Salonu', 'bos'),
(150, 'B16', 'Bilgisayar Salonu', 'bos'),
(151, 'B17', 'Bilgisayar Salonu', 'bos'),
(152, 'B18', 'Bilgisayar Salonu', 'bos'),
(153, 'B19', 'Bilgisayar Salonu', 'bos'),
(154, 'B20', 'Bilgisayar Salonu', 'bos'),
(155, 'B21', 'Bilgisayar Salonu', 'bos'),
(156, 'B22', 'Bilgisayar Salonu', 'bos'),
(157, 'B23', 'Bilgisayar Salonu', 'bos'),
(158, 'B24', 'Bilgisayar Salonu', 'bos'),
(159, 'B25', 'Bilgisayar Salonu', 'bos'),
(160, 'B26', 'Bilgisayar Salonu', 'bos'),
(161, 'B27', 'Bilgisayar Salonu', 'bos'),
(162, 'B28', 'Bilgisayar Salonu', 'bos');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rezervasyonlar`
--

DROP TABLE IF EXISTS `rezervasyonlar`;
CREATE TABLE IF NOT EXISTS `rezervasyonlar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kullanici_id` int NOT NULL,
  `masa_id` int NOT NULL,
  `tarih` date NOT NULL,
  `baslangic_saati` time NOT NULL,
  `bitis_saati` time NOT NULL,
  `durum` enum('aktif','tamamlandi','iptal') COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `olusturulma_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `onaylandi` tinyint(1) DEFAULT '0',
  `iptal_edildi` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `masa_id` (`masa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `rezervasyonlar`
--

INSERT INTO `rezervasyonlar` (`id`, `kullanici_id`, `masa_id`, `tarih`, `baslangic_saati`, `bitis_saati`, `durum`, `olusturulma_tarihi`, `onaylandi`, `iptal_edildi`) VALUES
(4, 4, 93, '2026-05-09', '20:24:00', '20:25:00', 'aktif', '2026-05-09 17:24:00', 1, 1),
(5, 4, 9, '2026-05-09', '20:26:00', '20:41:00', 'aktif', '2026-05-09 17:25:04', 1, 1),
(6, 4, 111, '2026-05-09', '20:33:00', '20:35:00', 'aktif', '2026-05-09 17:31:29', 1, 1),
(7, 4, 86, '2026-05-09', '20:33:00', '20:35:00', 'aktif', '2026-05-09 17:32:56', 0, 1),
(8, 4, 93, '2026-05-09', '20:37:00', '20:39:00', 'aktif', '2026-05-09 17:36:54', 1, 1),
(9, 4, 131, '2026-05-09', '20:40:00', '20:56:00', 'aktif', '2026-05-09 17:39:11', 1, 1),
(10, 4, 106, '2026-05-09', '22:00:00', '23:00:00', 'aktif', '2026-05-09 18:06:06', 0, 1),
(11, 4, 129, '2026-05-10', '01:00:00', '03:00:00', 'aktif', '2026-05-09 18:06:37', 0, 1),
(12, 4, 150, '2026-05-09', '21:26:00', '21:28:00', 'aktif', '2026-05-09 18:25:41', 1, 1),
(13, 4, 14, '2026-05-09', '21:29:00', '21:46:00', 'aktif', '2026-05-09 18:28:31', 0, 1),
(14, 4, 129, '2026-05-09', '22:51:00', '22:53:00', 'aktif', '2026-05-09 19:51:01', 1, 0),
(15, 6, 10, '2026-05-09', '23:03:00', '23:10:00', 'aktif', '2026-05-09 20:01:25', 0, 1),
(16, 4, 14, '2026-05-10', '08:00:00', '16:00:00', 'aktif', '2026-05-10 10:50:53', 0, 1),
(17, 4, 131, '2026-05-10', '14:00:00', '15:00:00', 'aktif', '2026-05-10 10:51:42', 0, 1),
(18, 4, 5, '2026-05-10', '15:00:00', '20:00:00', 'aktif', '2026-05-10 10:53:34', 0, 2),
(19, 4, 5, '2026-05-10', '20:00:00', '23:00:00', 'aktif', '2026-05-10 11:10:46', 0, 1),
(20, 4, 9, '2026-05-10', '18:00:00', '21:00:00', 'aktif', '2026-05-10 11:28:23', 0, 2),
(21, 4, 131, '2026-05-10', '18:00:00', '22:00:00', 'aktif', '2026-05-10 11:50:09', 0, 3),
(22, 4, 129, '2026-05-10', '16:00:00', '17:00:00', 'aktif', '2026-05-10 12:34:28', 0, 2),
(23, 6, 142, '2026-05-10', '16:00:00', '17:00:00', 'aktif', '2026-05-10 12:57:57', 0, 2),
(24, 6, 130, '2026-05-10', '17:00:00', '18:00:00', 'aktif', '2026-05-10 12:58:14', 0, 2),
(25, 4, 9, '2026-05-10', '22:00:00', '23:00:00', 'aktif', '2026-05-10 18:59:21', 0, 0),
(26, 4, 70, '2026-05-13', '14:00:00', '16:00:00', 'aktif', '2026-05-12 12:54:41', 0, 1),
(27, 4, 131, '2026-05-12', '19:00:00', '20:00:00', 'aktif', '2026-05-12 13:12:06', 0, 0),
(28, 4, 95, '2026-05-13', '14:00:00', '15:00:00', 'aktif', '2026-05-13 10:56:22', 0, 0),
(29, 5, 109, '2026-05-25', '21:00:00', '22:00:00', 'aktif', '2026-05-15 19:14:32', 0, 3),
(30, 5, 109, '2026-05-16', '11:00:00', '14:00:00', 'aktif', '2026-05-15 19:15:24', 0, 3),
(31, 4, 92, '2026-05-16', '22:00:00', '23:00:00', 'aktif', '2026-05-16 18:33:57', 0, 2),
(32, 4, 6, '2026-05-17', '12:00:00', '13:00:00', 'aktif', '2026-05-16 21:54:45', 0, 1),
(33, 4, 131, '2026-05-17', '18:00:00', '21:00:00', 'aktif', '2026-05-16 21:59:43', 0, 1),
(34, 5, 11, '2026-05-17', '18:00:00', '20:00:00', 'aktif', '2026-05-16 22:01:56', 0, 3),
(35, 4, 93, '2026-05-17', '17:00:00', '19:00:00', 'aktif', '2026-05-17 11:43:54', 0, 2),
(36, 4, 13, '2026-05-17', '19:00:00', '21:00:00', 'aktif', '2026-05-17 15:35:49', 0, 2),
(37, 4, 129, '2026-05-17', '22:00:00', '23:00:00', 'aktif', '2026-05-17 15:36:03', 0, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
