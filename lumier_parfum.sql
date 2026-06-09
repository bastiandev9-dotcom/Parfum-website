-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2026 at 12:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lumier_parfum`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT 'Rumah',
  `nama_penerima` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `kota` varchar(100) NOT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kodepos` varchar(10) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `label`, `nama_penerima`, `telepon`, `alamat_lengkap`, `kota`, `kecamatan`, `kodepos`, `is_default`, `created_at`, `updated_at`) VALUES
(2, 2, 'Rumah', 'Customer Test', '081234567890', 'Jl. Merdeka No. 10 RT 01/RW 02', 'Jakarta Selatan', 'Kebayoran Baru', '12345', 1, '2026-05-21 09:57:21', '2026-05-21 09:57:21'),
(3, 2, 'Rumah', 'Ridho', '081546474', 'jalan taman', 'pemalang', 'taman', '64894', 0, '2026-05-21 10:16:24', '2026-05-21 10:16:24');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(10) UNSIGNED NOT NULL,
  `nama_brand` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `negara_asal` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `nama_brand`, `slug`, `logo`, `deskripsi`, `negara_asal`, `website`, `is_active`, `created_at`) VALUES
(1, 'Mykonos', 'lumiere', NULL, NULL, 'Prancis', NULL, 1, '2026-05-21 07:18:37'),
(2, 'Chanel', 'chanel', NULL, NULL, 'Prancis', NULL, 1, '2026-05-21 07:18:37'),
(3, 'Dior', 'dior', NULL, NULL, 'Prancis', NULL, 1, '2026-05-21 07:18:37'),
(4, 'Versace', 'versace', NULL, NULL, 'Italia', NULL, 1, '2026-05-21 07:18:37'),
(5, 'Giorgio Armani', 'giorgio-armani', NULL, NULL, 'Italia', NULL, 1, '2026-05-21 07:18:37'),
(6, 'Tom Ford', 'tom-ford', NULL, NULL, 'Amerika', NULL, 1, '2026-05-21 07:18:37'),
(7, 'Etienne Aigner', 'etienne-aigner', NULL, NULL, 'Jerman', NULL, 1, '2026-05-22 15:57:09'),
(8, 'Carolina Herrera', 'carolina-herrera', NULL, NULL, 'Amerika Serikat', NULL, 1, '2026-05-22 16:21:18'),
(9, 'French Avenue', 'French Avenue', NULL, NULL, 'Uni Emirat Arab', NULL, 1, '2026-05-22 16:41:00'),
(10, 'Jean Paul Gaultier', 'Jean-Paul-Gaultier', NULL, NULL, 'Prancis', NULL, 1, '2026-05-22 17:05:52'),
(11, 'Calvin Klein', 'Calvin Klein', NULL, NULL, 'Amerika Serikat', NULL, 1, '2026-05-23 13:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED DEFAULT NULL,
  `qty` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `harga_satuan` int(10) UNSIGNED NOT NULL,
  `catatan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `product_id`, `variant_id`, `qty`, `harga_satuan`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 2, 3, NULL, 1, 850000, NULL, '2026-05-23 16:26:37', '2026-05-23 16:26:37'),
(2, 2, 3, NULL, 1, 850000, NULL, '2026-06-04 03:42:20', '2026-06-04 03:42:20'),
(3, 2, 4, NULL, 1, 1450000, NULL, '2026-06-04 03:55:21', '2026-06-04 03:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `urutan` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `nama_kategori`, `slug`, `deskripsi`, `icon`, `urutan`, `is_active`, `created_at`) VALUES
(1, 'Eau de Parfum', 'eau-de-parfum', NULL, NULL, 1, 1, '2026-05-21 07:18:37'),
(2, 'Eau de Toilette', 'eau-de-toilette', NULL, NULL, 2, 1, '2026-05-21 07:18:37'),
(3, 'Parfum Eksklusif', 'parfum-eksklusif', NULL, NULL, 3, 1, '2026-05-21 07:18:37');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_code` varchar(20) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `alamat_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`alamat_snapshot`)),
  `kurir` enum('jne','jnt','sicepat','anteraja','pos') NOT NULL,
  `kurir_nama` varchar(50) DEFAULT NULL,
  `ongkir` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resi` varchar(50) DEFAULT NULL,
  `metode_bayar` enum('transfer_bca','transfer_bni','transfer_mandiri','cod','e_wallet') NOT NULL,
  `cod_fee` int(10) UNSIGNED DEFAULT 0,
  `subtotal` int(10) UNSIGNED NOT NULL,
  `diskon` int(10) UNSIGNED DEFAULT 0,
  `total` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','diproses','dikirim','selesai','batal','refund') NOT NULL DEFAULT 'pending',
  `status_bayar` enum('belum','pending','lunas','gagal') NOT NULL DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_code`, `user_id`, `alamat_snapshot`, `kurir`, `kurir_nama`, `ongkir`, `resi`, `metode_bayar`, `cod_fee`, `subtotal`, `diskon`, `total`, `status`, `status_bayar`, `created_at`, `updated_at`, `paid_at`, `shipped_at`, `delivered_at`, `cancelled_at`) VALUES
(1, 'LMR-52064509', 1, '{\"nama\":\"bastian\",\"telepon\":\"085469875214\",\"alamat\":\"jalan pekalongan\",\"kota\":\"pekalongan\",\"kodepos\":\"54895\"}', 'jnt', 'J&T Express', 15000, '', '', 0, 2100000, 0, 2115000, 'batal', 'belum', '2026-05-21 07:58:36', '2026-05-21 08:40:00', NULL, NULL, NULL, '2026-05-21 08:40:00'),
(2, 'LMR-6BED4B1B', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jne', 'JNE Reguler', 18000, '', '', 0, 1250000, 0, 1268000, 'selesai', 'pending', '2026-05-21 08:03:59', '2026-05-21 08:46:20', NULL, '2026-05-21 08:46:12', '2026-05-21 08:46:20', NULL),
(3, 'LMR-9998B7F7', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'anteraja', 'AnterAja', 14000, '', '', 0, 850000, 0, 864000, 'selesai', 'pending', '2026-05-21 08:09:35', '2026-05-21 15:56:18', NULL, '2026-05-21 08:41:57', '2026-05-21 15:56:18', NULL),
(4, 'LMR-6AA0C800', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jnt', 'J&T Express', 15000, '', '', 0, 850000, 0, 865000, 'selesai', 'pending', '2026-05-21 08:12:13', '2026-05-21 08:39:33', NULL, '2026-05-21 08:38:57', '2026-05-21 08:39:33', NULL),
(5, 'LMR-50D3587C', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jnt', 'J&T Express', 15000, '', '', 0, 650000, 0, 665000, 'selesai', 'pending', '2026-05-21 08:14:53', '2026-05-21 08:38:46', NULL, '2026-05-21 08:31:37', '2026-05-21 08:38:46', NULL),
(6, 'LMR-EE030AC7', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jnt', 'J&T Express', 15000, 'JNT26052156B88B69', '', 0, 850000, 0, 865000, 'dikirim', 'pending', '2026-05-21 09:01:31', '2026-05-21 09:04:39', NULL, '2026-05-21 09:04:39', NULL, NULL),
(7, 'LMR-10B05379', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jnt', 'J&T Express', 15000, NULL, '', 0, 1450000, 0, 1465000, 'diproses', 'pending', '2026-05-21 09:46:44', '2026-05-21 09:46:49', NULL, NULL, NULL, NULL),
(8, 'LMR-6385CBFB', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jnt', 'J&T Express', 15000, 'JNT260522CA3D35E5', '', 0, 890000, 0, 905000, 'dikirim', 'pending', '2026-05-21 09:47:55', '2026-05-22 15:06:56', NULL, '2026-05-22 15:06:56', NULL, NULL),
(9, 'LMR-293CB64E', 2, '{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}', 'jnt', 'J&T Express', 15000, NULL, '', 0, 1800000, 0, 1815000, 'pending', 'belum', '2026-05-21 09:48:33', '2026-05-21 09:48:33', NULL, NULL, NULL, NULL),
(10, 'LMR-E1245E40', 2, '0', 'jnt', 'J&T Express', 15000, NULL, '', 0, 1250000, 0, 1265000, 'pending', 'belum', '2026-05-21 10:08:12', '2026-05-21 10:30:20', NULL, NULL, NULL, NULL),
(11, 'LMR-22B8ED4E', 2, '0', 'jnt', 'J&T Express', 15000, NULL, '', 0, 480000, 0, 495000, 'pending', 'belum', '2026-05-21 10:14:29', '2026-05-21 10:30:20', NULL, NULL, NULL, NULL),
(12, 'LMR-A315C745', 2, '{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}', 'jnt', 'J&T Express', 15000, NULL, 'transfer_bca', 0, 1100000, 100000, 1015000, 'pending', 'belum', '2026-05-21 10:28:30', '2026-05-21 10:28:30', NULL, NULL, NULL, NULL),
(13, 'LMR-0C02EFF1', 2, '{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}', 'jnt', 'J&T Express', 15000, 'JNT26052232D4701B', 'transfer_bni', 0, 850000, 85000, 780000, 'dikirim', 'pending', '2026-05-21 10:32:44', '2026-05-22 15:07:09', NULL, '2026-05-22 15:07:09', NULL, NULL),
(14, 'LMR-B9749781', 2, '{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}', 'jnt', 'J&T Express', 15000, NULL, 'transfer_bni', 0, 1350000, 100000, 1265000, 'diproses', 'pending', '2026-05-21 10:35:48', '2026-05-21 17:37:24', NULL, NULL, NULL, NULL),
(15, 'LMR-3DF9D1E7', 2, '{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}', 'jnt', 'J&T Express', 15000, 'JNT260521DAB994D2', 'transfer_bca', 0, 850000, 85000, 780000, 'selesai', 'pending', '2026-05-21 13:47:27', '2026-05-21 13:48:39', NULL, '2026-05-21 13:48:25', '2026-05-21 13:48:39', NULL),
(16, 'LMR-68810EEB', 2, '{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}', 'jnt', 'J&T Express', 15000, 'JNT26052181BA7D71', 'transfer_bca', 0, 850000, 85000, 780000, 'selesai', 'pending', '2026-05-21 17:07:28', '2026-05-21 17:35:55', NULL, '2026-05-21 17:08:13', '2026-05-21 17:35:55', NULL),
(17, 'LMR-A40D1F9D', 2, '{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}', 'sicepat', 'SiCepat BEST', 12000, NULL, 'transfer_bca', 0, 1450000, 0, 1462000, 'pending', 'belum', '2026-05-23 15:41:52', '2026-05-23 15:41:52', NULL, NULL, NULL, NULL),
(18, 'LMR-200B09D3', 2, '{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}', 'jnt', 'J&T Express', 15000, 'JNT260604AE7595CA', 'transfer_bni', 0, 850000, 0, 865000, 'selesai', 'pending', '2026-06-04 03:42:41', '2026-06-04 03:43:58', NULL, '2026-06-04 03:43:41', '2026-06-04 03:43:58', NULL),
(19, 'LMR-06804B1C', 2, '{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}', 'jnt', 'J&T Express', 15000, 'JNT2606043806458E', 'transfer_bni', 0, 1450000, 0, 1465000, 'selesai', 'pending', '2026-06-04 03:55:57', '2026-06-04 03:58:01', NULL, '2026-06-04 03:57:07', '2026-06-04 03:58:01', NULL);

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_after_order_completed` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status = 'selesai' AND OLD.status != 'selesai' THEN
        UPDATE products p
        JOIN order_items oi ON p.product_id = oi.product_id
        SET p.total_terjual = p.total_terjual + oi.qty
        WHERE oi.order_id = NEW.order_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_order_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
    
    
    INSERT INTO order_status_logs (order_id, status_lama, status_baru, keterangan)
    VALUES (NEW.order_id, NULL, NEW.status, 'Order dibuat');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_status_change` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status != OLD.status THEN
        INSERT INTO order_status_logs (order_id, status_lama, status_baru, keterangan)
        VALUES (NEW.order_id, OLD.status, NEW.status, CONCAT('Status berubah dari ', OLD.status, ' ke ', NEW.status));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED DEFAULT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `ukuran` varchar(20) DEFAULT NULL,
  `harga_satuan` int(10) UNSIGNED NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `subtotal` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `variant_id`, `nama_produk`, `brand`, `gambar`, `ukuran`, `harga_satuan`, `qty`, `subtotal`) VALUES
(1, 17, 4, NULL, 'Bad Boy Elixir Man', 'Carolina Herrera', 'assets/images/products/badboy-removebg-preview.png', NULL, 1450000, 1, 1450000),
(2, 18, 3, NULL, 'Pinnace Oryn Man', 'French Avenue', 'assets/images/products/pinnace-removebg-preview.png', NULL, 850000, 1, 850000),
(3, 19, 4, NULL, 'Bad Boy Elixir Man', 'Carolina Herrera', 'assets/images/products/badboy-removebg-preview.png', NULL, 1450000, 1, 1450000);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_logs`
--

CREATE TABLE `order_status_logs` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `status_lama` varchar(20) DEFAULT NULL,
  `status_baru` varchar(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_logs`
--

INSERT INTO `order_status_logs` (`log_id`, `order_id`, `status_lama`, `status_baru`, `keterangan`, `changed_by`, `created_at`) VALUES
(1, 8, 'diproses', 'dikirim', 'Status berubah dari diproses ke dikirim', NULL, '2026-05-22 15:06:56'),
(2, 8, NULL, 'dikirim', NULL, 1, '2026-05-22 15:06:56'),
(3, 13, 'diproses', 'dikirim', 'Status berubah dari diproses ke dikirim', NULL, '2026-05-22 15:07:09'),
(4, 13, NULL, 'dikirim', NULL, 1, '2026-05-22 15:07:09'),
(5, 17, NULL, 'pending', 'Order dibuat', NULL, '2026-05-23 15:41:52'),
(6, 18, NULL, 'pending', 'Order dibuat', NULL, '2026-06-04 03:42:41'),
(7, 18, 'pending', 'diproses', 'Status berubah dari pending ke diproses', NULL, '2026-06-04 03:43:03'),
(8, 18, 'diproses', 'dikirim', 'Status berubah dari diproses ke dikirim', NULL, '2026-06-04 03:43:41'),
(9, 18, NULL, 'dikirim', NULL, 1, '2026-06-04 03:43:41'),
(10, 18, 'dikirim', 'selesai', 'Status berubah dari dikirim ke selesai', NULL, '2026-06-04 03:43:58'),
(11, 18, NULL, 'selesai', NULL, 1, '2026-06-04 03:43:58'),
(12, 19, NULL, 'pending', 'Order dibuat', NULL, '2026-06-04 03:55:57'),
(13, 19, 'pending', 'diproses', 'Status berubah dari pending ke diproses', NULL, '2026-06-04 03:56:04'),
(14, 19, 'diproses', 'dikirim', 'Status berubah dari diproses ke dikirim', NULL, '2026-06-04 03:57:07'),
(15, 19, NULL, 'dikirim', NULL, 1, '2026-06-04 03:57:07'),
(16, 19, 'dikirim', 'selesai', 'Status berubah dari dikirim ke selesai', NULL, '2026-06-04 03:58:01'),
(17, 19, NULL, 'selesai', NULL, 1, '2026-06-04 03:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `metode` varchar(50) NOT NULL,
  `jumlah` int(10) UNSIGNED NOT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `external_status` varchar(50) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `bank_pengirim` varchar(50) DEFAULT NULL,
  `status` enum('pending','success','failed','expired','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `metode`, `jumlah`, `external_id`, `external_status`, `bukti_transfer`, `nama_pengirim`, `bank_pengirim`, `status`, `created_at`, `verified_at`, `verified_by`) VALUES
(2, 7, 'transfer_bca', 1268000, NULL, NULL, NULL, 'Customer Test', 'BCA', 'pending', '2026-05-21 09:58:15', NULL, 1),
(3, 14, 'transfer_bni', 1265000, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-05-21 10:35:48', NULL, NULL),
(4, 15, 'transfer_bca', 780000, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-05-21 13:47:27', NULL, NULL),
(5, 16, 'transfer_bca', 780000, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-05-21 17:07:28', NULL, NULL),
(6, 17, 'transfer_bca', 1462000, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-05-23 15:41:52', NULL, NULL),
(7, 18, 'transfer_bni', 865000, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-06-04 03:42:41', NULL, NULL),
(8, 19, 'transfer_bni', 1465000, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-06-04 03:55:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `brand_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `harga` int(10) UNSIGNED NOT NULL,
  `harga_diskon` int(10) UNSIGNED DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `aroma` enum('woody','floral','fresh','oriental','citrus','spicy','aquatic','gourmand') NOT NULL,
  `gender` enum('pria','wanita','unisex') NOT NULL,
  `stok` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stok_minimum` int(10) UNSIGNED DEFAULT 5,
  `gambar_utama` varchar(255) NOT NULL,
  `rating_avg` decimal(2,1) DEFAULT 0.0,
  `total_review` int(10) UNSIGNED DEFAULT 0,
  `total_terjual` int(10) UNSIGNED DEFAULT 0,
  `status` enum('aktif','habis','nonaktif','draft') NOT NULL DEFAULT 'aktif',
  `is_best_seller` tinyint(1) DEFAULT 0,
  `is_new_arrival` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `nama_produk`, `slug`, `brand_id`, `category_id`, `harga`, `harga_diskon`, `deskripsi`, `aroma`, `gender`, `stok`, `stok_minimum`, `gambar_utama`, `rating_avg`, `total_review`, `total_terjual`, `status`, `is_best_seller`, `is_new_arrival`, `created_at`, `updated_at`) VALUES
(1, 'Carolina Herrera', 'carolina-herrera', 8, 2, 1250000, NULL, '-', 'floral', 'wanita', 25, 5, 'assets/images/products/carolina.png', 0.0, 0, 1, 'aktif', 1, 0, '2026-05-21 07:19:47', '2026-05-23 14:15:53'),
(2, 'Icon Elixir Woman EDP', 'etienne-aigner', 7, 1, 980000, 850000, '-.', 'floral', 'wanita', 12, 5, 'assets/images/products/icon-elixir-woman-edp-100-ml-removebg-preview.png', 5.0, 1, 3, 'aktif', 1, 0, '2026-05-21 07:19:47', '2026-05-23 14:14:31'),
(3, 'Pinnace Oryn Man', 'Pinnace Oryn Man', 9, 1, 850000, NULL, '-', 'citrus', 'pria', 30, 5, 'assets/images/products/pinnace-removebg-preview.png', 0.0, 0, 2, 'aktif', 0, 1, '2026-05-21 07:19:47', '2026-06-04 03:43:58'),
(4, 'Bad Boy Elixir Man', 'Bad Boy Elixir Man', 8, 1, 1450000, NULL, '-', 'woody', 'pria', 18, 5, 'assets/images/products/badboy-removebg-preview.png', 0.0, 0, 1, 'aktif', 1, 0, '2026-05-21 07:19:47', '2026-06-04 03:58:01'),
(5, 'Versace Eros Pour Femme', 'Versace-Eros-Pour-Femme', 4, 1, 750000, 650000, '-', 'floral', 'wanita', 20, 5, 'assets/images/products/versace-eros-removebg-preview.png', 5.0, 1, 1, 'aktif', 0, 0, '2026-05-21 07:19:47', '2026-05-23 14:16:46'),
(6, 'Le Beau Paradise Garden Man', 'Le-Beau-Paradise-Garden-Man', 10, 1, 1100000, NULL, '-', 'woody', 'pria', 15, 5, 'assets/images/products/jeanpg-removebg-preview.png', 0.0, 0, 0, 'aktif', 0, 0, '2026-05-21 07:19:47', '2026-05-23 14:18:55'),
(7, 'La Belle Paradise Garden Woman', 'La Belle Paradise Garden Woman', 10, 1, 1350000, NULL, '-', 'floral', 'pria', 22, 5, 'assets/images/products/labelle-removebg-preview.png', 0.0, 0, 0, 'aktif', 1, 0, '2026-05-21 07:19:47', '2026-05-23 14:20:08'),
(8, 'Le Beau Man', 'Le Beau Man', 10, 1, 620000, NULL, '-', 'woody', 'pria', 28, 5, 'assets/images/products/lebeauman-removebg-preview.png', 0.0, 0, 0, 'aktif', 0, 1, '2026-05-21 07:19:47', '2026-05-23 14:21:27'),
(9, 'Aqua Di Gio', 'aqua-di-gio', 5, 1, 1050000, 920000, '-', 'woody', 'pria', 19, 5, 'assets/images/products/aqua-removebg-preview.png', 0.0, 0, 0, 'aktif', 1, 0, '2026-05-21 07:19:47', '2026-05-23 14:22:12'),
(10, 'Vanilla Orchid', 'vanilla-orchid', 6, 3, 1800000, NULL, 'Vanilla Madagascar dipadukan anggrek eksotis. Manis, sensual, dan mewah.', 'oriental', 'wanita', 10, 5, 'assets/images/products/vanilla-removebg-preview.png', 0.0, 0, 0, 'aktif', 1, 0, '2026-05-21 07:19:47', '2026-05-23 14:22:46'),
(11, 'Christian Dior Sauvage Elixir Man', 'Christian Dior Sauvage Elixir Man', 3, 2, 890000, NULL, '-', 'woody', 'pria', 14, 5, 'assets/images/products/sauvage-removebg-preview.png', 0.0, 0, 0, 'aktif', 0, 0, '2026-05-21 07:19:47', '2026-05-23 14:23:17'),
(12, 'Calvin Klein CK One Essence', 'Calvin Klein CK One Essence', 11, 1, 550000, 480000, '-', 'fresh', 'unisex', 35, 5, 'assets/images/products/one_essence-removebg-preview.png', 0.0, 0, 0, 'aktif', 0, 1, '2026-05-21 07:19:47', '2026-05-23 14:23:53');

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `trg_after_product_price_update` AFTER UPDATE ON `products` FOR EACH ROW BEGIN
    IF NEW.harga != OLD.harga THEN
        UPDATE carts 
        SET harga_satuan = NEW.harga
        WHERE product_id = NEW.product_id AND harga_satuan = OLD.harga;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_gallery`
--

CREATE TABLE `product_gallery` (
  `gallery_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_gallery`
--

INSERT INTO `product_gallery` (`gallery_id`, `product_id`, `gambar`, `urutan`, `created_at`) VALUES
(1, 1, 'assets/images/products/carolina.png', 1, '2026-05-21 07:41:46');

-- --------------------------------------------------------

--
-- Table structure for table `product_notes`
--

CREATE TABLE `product_notes` (
  `note_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `note` varchar(100) NOT NULL,
  `tipe_note` enum('top','middle','base') DEFAULT 'middle',
  `urutan` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_notes`
--

INSERT INTO `product_notes` (`note_id`, `product_id`, `note`, `tipe_note`, `urutan`) VALUES
(1, 1, 'Bergamot, Grapefruit and Lemon', 'top', 0),
(2, 1, 'Orange Blossom, Rose and Jasmine', 'middle', 0),
(3, 1, 'eather, Patchouli, Praline, Sandalwood, Cedar and Musk', 'base', 0),
(6, 2, 'Grapefruit , Pear, Raspberry', 'top', 0),
(7, 2, 'Lily of the valley, Rose absolute, Peach', 'middle', 0),
(8, 2, 'Amber, Moss,Vanilla', 'base', 0),
(10, 3, 'Orange, Mandarin and Bergamot', 'top', 0),
(12, 3, 'Ginger', 'middle', 0),
(13, 3, 'Ambergris', 'base', 0),
(14, 4, 'Sage', 'top', 0),
(15, 4, 'Leather   ', 'middle', 0),
(16, 4, 'Cedarwood', 'base', 0),
(18, 5, 'Sicilian Lemon, Calabrian bergamot', 'top', 0),
(20, 5, 'Jasmine Sambac, Lemon Blossom', 'middle', 0),
(21, 5, 'Woodsy Notes, Musk', 'base', 0),
(22, 6, 'Green Notes, Watery Notes, Mint and Ginger', 'top', 0),
(23, 6, 'Coconut, Fig and Salt', 'middle', 0),
(24, 6, 'Tonka Bean and Sandalwood', 'base', 0),
(26, 7, 'Blue Lotus', 'top', 0),
(27, 7, 'Iris', 'middle', 0),
(28, 7, 'Tonka Bean', 'base', 0),
(30, 8, 'Bergamot', 'top', 0),
(31, 8, 'Coconut', 'middle', 0),
(32, 8, 'Tonka Bean', 'base', 0),
(34, 9, 'Bergamot', 'top', 0),
(35, 9, 'Neroli', 'top', 0),
(36, 9, 'Rosemary', 'middle', 0),
(37, 9, 'Cedarwood', 'base', 0),
(38, 10, 'Madagascar Vanilla', 'middle', 0),
(39, 10, 'Orchid', 'top', 0),
(40, 10, 'Jasmine', 'middle', 0),
(41, 10, 'Amber', 'base', 0),
(42, 11, 'Cedarwood', 'middle', 0),
(43, 11, 'Vetiver', 'base', 0),
(44, 11, 'Bergamot', 'top', 0),
(45, 11, 'Leather', 'base', 0),
(46, 12, 'Green Tea', 'top', 0),
(47, 12, 'Bergamot', 'top', 0),
(48, 12, 'Lemon', 'top', 0),
(49, 12, 'White Musk', 'base', 0),
(50, 7, 'Vanilla', 'base', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `ukuran` varchar(20) NOT NULL,
  `stok` int(10) UNSIGNED DEFAULT 0,
  `sku` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `ukuran`, `stok`, `sku`, `is_active`) VALUES
(1, 1, '100ml', 20, NULL, 1),
(3, 2, '100ml', 20, NULL, 1),
(5, 3, '100ml', 20, NULL, 1),
(8, 4, '100ml', 20, NULL, 1),
(9, 5, '100ml', 20, NULL, 1),
(10, 6, '125ml', 20, NULL, 1),
(13, 7, '100ml', 20, NULL, 1),
(14, 8, '125ml', 20, NULL, 1),
(15, 9, '50ml', 20, NULL, 1),
(18, 10, '100ml', 20, NULL, 1),
(19, 11, '50ml', 20, NULL, 1),
(20, 12, '50ml', 20, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `promo_id` int(10) UNSIGNED NOT NULL,
  `kode` varchar(20) NOT NULL,
  `tipe` enum('persen','nominal') NOT NULL,
  `nilai` int(10) UNSIGNED NOT NULL,
  `min_pembelian` int(10) UNSIGNED DEFAULT 0,
  `max_diskon` int(10) UNSIGNED DEFAULT NULL,
  `max_penggunaan` int(10) UNSIGNED DEFAULT NULL,
  `terpakai` int(10) UNSIGNED DEFAULT 0,
  `berlaku_dari` date NOT NULL,
  `berlaku_sampai` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`promo_id`, `kode`, `tipe`, `nilai`, `min_pembelian`, `max_diskon`, `max_penggunaan`, `terpakai`, `berlaku_dari`, `berlaku_sampai`, `is_active`, `created_at`) VALUES
(1, 'LUMIERE10', 'persen', 10, 500000, 100000, 100, 6, '2026-01-01', '2026-12-31', 1, '2026-05-21 09:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `reviews`
--
DELIMITER $$
CREATE TRIGGER `trg_before_review_insert` BEFORE INSERT ON `reviews` FOR EACH ROW BEGIN
    DECLARE v_count INT;

    SELECT COUNT(*) INTO v_count
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = NEW.user_id 
        AND oi.product_id = NEW.product_id
        AND o.status = 'selesai';

    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'User harus membeli produk terlebih dahulu sebelum review';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` enum('aktif','nonaktif','diblokir') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama`, `email`, `password_hash`, `telepon`, `avatar`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin Lumiere', 'admin@lumier.com', '$2y$10$Z3D4QZJdDuvm/ZlpR8JYOulLmMmvSju0rA0lL03NdN5f3cWQzWeaa', NULL, NULL, 'admin', 'aktif', '2026-05-21 07:01:53', '2026-05-21 07:15:55'),
(2, 'Customer Test', 'customer@email.com', '$2y$10$b1gOCDhf7nKt2fKsXWQiduBhAsywspLqm3g6/iiCqwdMnhby0S0wO', '081234567890', NULL, 'customer', 'aktif', '2026-05-21 07:01:53', '2026-05-21 07:15:55'),
(3, 'Bastian', 'Bastian@gmail.com', '$2y$10$kgZbwF7SQ0Z3VrZakwu64OsCpGsNF1.0F4a2b/G16b.C9zos.Wbe.', '085554567852', NULL, 'customer', 'aktif', '2026-05-21 07:06:11', '2026-05-21 07:06:11');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_orders_full`
-- (See below for the actual view)
--
CREATE TABLE `v_orders_full` (
`order_id` int(10) unsigned
,`order_code` varchar(20)
,`user_id` int(10) unsigned
,`alamat_snapshot` longtext
,`kurir` enum('jne','jnt','sicepat','anteraja','pos')
,`kurir_nama` varchar(50)
,`ongkir` int(10) unsigned
,`resi` varchar(50)
,`metode_bayar` enum('transfer_bca','transfer_bni','transfer_mandiri','cod','e_wallet')
,`cod_fee` int(10) unsigned
,`subtotal` int(10) unsigned
,`diskon` int(10) unsigned
,`total` int(10) unsigned
,`status` enum('pending','diproses','dikirim','selesai','batal','refund')
,`status_bayar` enum('belum','pending','lunas','gagal')
,`created_at` timestamp
,`updated_at` timestamp
,`paid_at` timestamp
,`shipped_at` timestamp
,`delivered_at` timestamp
,`cancelled_at` timestamp
,`nama_user` varchar(100)
,`email_user` varchar(100)
,`telepon_user` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_products_full`
-- (See below for the actual view)
--
CREATE TABLE `v_products_full` (
`product_id` int(10) unsigned
,`nama_produk` varchar(150)
,`slug` varchar(150)
,`brand_id` int(10) unsigned
,`category_id` int(10) unsigned
,`harga` int(10) unsigned
,`harga_diskon` int(10) unsigned
,`deskripsi` text
,`aroma` enum('woody','floral','fresh','oriental','citrus','spicy','aquatic','gourmand')
,`gender` enum('pria','wanita','unisex')
,`stok` int(10) unsigned
,`stok_minimum` int(10) unsigned
,`gambar_utama` varchar(255)
,`rating_avg` decimal(2,1)
,`total_review` int(10) unsigned
,`total_terjual` int(10) unsigned
,`status` enum('aktif','habis','nonaktif','draft')
,`is_best_seller` tinyint(1)
,`is_new_arrival` tinyint(1)
,`created_at` timestamp
,`updated_at` timestamp
,`nama_brand` varchar(100)
,`brand_slug` varchar(100)
,`nama_kategori` varchar(50)
,`category_slug` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `v_orders_full`
--
DROP TABLE IF EXISTS `v_orders_full`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_orders_full`  AS SELECT `o`.`order_id` AS `order_id`, `o`.`order_code` AS `order_code`, `o`.`user_id` AS `user_id`, `o`.`alamat_snapshot` AS `alamat_snapshot`, `o`.`kurir` AS `kurir`, `o`.`kurir_nama` AS `kurir_nama`, `o`.`ongkir` AS `ongkir`, `o`.`resi` AS `resi`, `o`.`metode_bayar` AS `metode_bayar`, `o`.`cod_fee` AS `cod_fee`, `o`.`subtotal` AS `subtotal`, `o`.`diskon` AS `diskon`, `o`.`total` AS `total`, `o`.`status` AS `status`, `o`.`status_bayar` AS `status_bayar`, `o`.`created_at` AS `created_at`, `o`.`updated_at` AS `updated_at`, `o`.`paid_at` AS `paid_at`, `o`.`shipped_at` AS `shipped_at`, `o`.`delivered_at` AS `delivered_at`, `o`.`cancelled_at` AS `cancelled_at`, `u`.`nama` AS `nama_user`, `u`.`email` AS `email_user`, `u`.`telepon` AS `telepon_user` FROM (`orders` `o` left join `users` `u` on(`o`.`user_id` = `u`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_products_full`
--
DROP TABLE IF EXISTS `v_products_full`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_products_full`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`nama_produk` AS `nama_produk`, `p`.`slug` AS `slug`, `p`.`brand_id` AS `brand_id`, `p`.`category_id` AS `category_id`, `p`.`harga` AS `harga`, `p`.`harga_diskon` AS `harga_diskon`, `p`.`deskripsi` AS `deskripsi`, `p`.`aroma` AS `aroma`, `p`.`gender` AS `gender`, `p`.`stok` AS `stok`, `p`.`stok_minimum` AS `stok_minimum`, `p`.`gambar_utama` AS `gambar_utama`, `p`.`rating_avg` AS `rating_avg`, `p`.`total_review` AS `total_review`, `p`.`total_terjual` AS `total_terjual`, `p`.`status` AS `status`, `p`.`is_best_seller` AS `is_best_seller`, `p`.`is_new_arrival` AS `is_new_arrival`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `b`.`nama_brand` AS `nama_brand`, `b`.`slug` AS `brand_slug`, `c`.`nama_kategori` AS `nama_kategori`, `c`.`slug` AS `category_slug` FROM ((`products` `p` left join `brands` `b` on(`p`.`brand_id` = `b`.`brand_id`)) left join `categories` `c` on(`p`.`category_id` = `c`.`category_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_default` (`user_id`,`is_default`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `nama_brand` (`nama_brand`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `uk_user_product` (`user_id`,`product_id`,`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_status_bayar` (`status_bayar`),
  ADD KEY `idx_order_code` (`order_code`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_order` (`order_id`,`created_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_brand` (`brand_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_aroma` (`aroma`),
  ADD KEY `idx_gender` (`gender`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_harga` (`harga`),
  ADD KEY `idx_best_seller` (`is_best_seller`),
  ADD KEY `idx_new` (`is_new_arrival`);
ALTER TABLE `products` ADD FULLTEXT KEY `ft_nama_desc` (`nama_produk`,`deskripsi`);

--
-- Indexes for table `product_gallery`
--
ALTER TABLE `product_gallery`
  ADD PRIMARY KEY (`gallery_id`),
  ADD KEY `idx_product` (`product_id`,`urutan`);

--
-- Indexes for table `product_notes`
--
ALTER TABLE `product_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `idx_product` (`product_id`,`tipe_note`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD UNIQUE KEY `uk_product_ukuran` (`product_id`,`ukuran`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_sku` (`sku`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`promo_id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `idx_kode` (`kode`),
  ADD KEY `idx_berlaku` (`berlaku_dari`,`berlaku_sampai`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `uk_user_product_order` (`user_id`,`product_id`,`order_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_product` (`product_id`,`is_approved`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `uk_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_gallery`
--
ALTER TABLE `product_gallery`
  MODIFY `gallery_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_notes`
--
ALTER TABLE `product_notes`
  MODIFY `note_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `promo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carts_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_status_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_gallery`
--
ALTER TABLE `product_gallery`
  ADD CONSTRAINT `product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_notes`
--
ALTER TABLE `product_notes`
  ADD CONSTRAINT `product_notes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
