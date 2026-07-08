-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: lumier_parfum
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT 'Rumah',
  `nama_penerima` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `kota` varchar(100) NOT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kodepos` varchar(10) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`address_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_default` (`user_id`,`is_default`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (2,2,'Rumah','Customer Test','081234567890','Jl. Merdeka No. 10 RT 01/RW 02','Jakarta Selatan','Kebayoran Baru','12345',1,'2026-05-21 09:57:21','2026-05-21 09:57:21'),(3,2,'Rumah','Ridho','081546474','jalan taman','pemalang','taman','64894',0,'2026-05-21 10:16:24','2026-05-21 10:16:24');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_brand` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `negara_asal` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `nama_brand` (`nama_brand`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Mykonos','lumiere',NULL,NULL,'Prancis',NULL,1,'2026-05-21 07:18:37'),(2,'Chanel','chanel',NULL,NULL,'Prancis',NULL,1,'2026-05-21 07:18:37'),(3,'Dior','dior',NULL,NULL,'Prancis',NULL,1,'2026-05-21 07:18:37'),(4,'Versace','versace',NULL,NULL,'Italia',NULL,1,'2026-05-21 07:18:37'),(5,'Giorgio Armani','giorgio-armani',NULL,NULL,'Italia',NULL,1,'2026-05-21 07:18:37'),(6,'Tom Ford','tom-ford',NULL,NULL,'Amerika',NULL,1,'2026-05-21 07:18:37'),(7,'Etienne Aigner','etienne-aigner',NULL,NULL,'Jerman',NULL,1,'2026-05-22 15:57:09'),(8,'Carolina Herrera','carolina-herrera',NULL,NULL,'Amerika Serikat',NULL,1,'2026-05-22 16:21:18'),(9,'French Avenue','French Avenue',NULL,NULL,'Uni Emirat Arab',NULL,1,'2026-05-22 16:41:00'),(10,'Jean Paul Gaultier','Jean-Paul-Gaultier',NULL,NULL,'Prancis',NULL,1,'2026-05-22 17:05:52'),(11,'Calvin Klein','Calvin Klein',NULL,NULL,'Amerika Serikat',NULL,1,'2026-05-23 13:50:54');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `cart_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `variant_id` int(10) unsigned DEFAULT NULL,
  `qty` int(10) unsigned NOT NULL DEFAULT 1,
  `harga_satuan` int(10) unsigned NOT NULL,
  `catatan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cart_id`),
  UNIQUE KEY `uk_user_product` (`user_id`,`product_id`,`variant_id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `carts_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,2,3,NULL,1,850000,NULL,'2026-05-23 16:26:37','2026-05-23 16:26:37'),(2,2,3,NULL,1,850000,NULL,'2026-06-04 03:42:20','2026-06-04 03:42:20'),(3,2,4,NULL,1,1450000,NULL,'2026-06-04 03:55:21','2026-06-04 03:55:21');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `urutan` int(10) unsigned DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `nama_kategori` (`nama_kategori`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Eau de Parfum','eau-de-parfum',NULL,NULL,1,1,'2026-05-21 07:18:37'),(2,'Eau de Toilette','eau-de-toilette',NULL,NULL,2,1,'2026-05-21 07:18:37'),(3,'Parfum Eksklusif','parfum-eksklusif',NULL,NULL,3,1,'2026-05-21 07:18:37');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `variant_id` int(10) unsigned DEFAULT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `ukuran` varchar(20) DEFAULT NULL,
  `harga_satuan` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `subtotal` int(10) unsigned NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,17,4,NULL,'Bad Boy Elixir Man','Carolina Herrera','assets/images/products/badboy-removebg-preview.png',NULL,1450000,1,1450000),(2,18,3,NULL,'Pinnace Oryn Man','French Avenue','assets/images/products/pinnace-removebg-preview.png',NULL,850000,1,850000),(3,19,4,NULL,'Bad Boy Elixir Man','Carolina Herrera','assets/images/products/badboy-removebg-preview.png',NULL,1450000,1,1450000);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_logs`
--

DROP TABLE IF EXISTS `order_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `status_lama` varchar(20) DEFAULT NULL,
  `status_baru` varchar(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `changed_by` (`changed_by`),
  KEY `idx_order` (`order_id`,`created_at`),
  CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_status_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_logs`
--

LOCK TABLES `order_status_logs` WRITE;
/*!40000 ALTER TABLE `order_status_logs` DISABLE KEYS */;
INSERT INTO `order_status_logs` VALUES (1,8,'diproses','dikirim','Status berubah dari diproses ke dikirim',NULL,'2026-05-22 15:06:56'),(2,8,NULL,'dikirim',NULL,1,'2026-05-22 15:06:56'),(3,13,'diproses','dikirim','Status berubah dari diproses ke dikirim',NULL,'2026-05-22 15:07:09'),(4,13,NULL,'dikirim',NULL,1,'2026-05-22 15:07:09'),(5,17,NULL,'pending','Order dibuat',NULL,'2026-05-23 15:41:52'),(6,18,NULL,'pending','Order dibuat',NULL,'2026-06-04 03:42:41'),(7,18,'pending','diproses','Status berubah dari pending ke diproses',NULL,'2026-06-04 03:43:03'),(8,18,'diproses','dikirim','Status berubah dari diproses ke dikirim',NULL,'2026-06-04 03:43:41'),(9,18,NULL,'dikirim',NULL,1,'2026-06-04 03:43:41'),(10,18,'dikirim','selesai','Status berubah dari dikirim ke selesai',NULL,'2026-06-04 03:43:58'),(11,18,NULL,'selesai',NULL,1,'2026-06-04 03:43:58'),(12,19,NULL,'pending','Order dibuat',NULL,'2026-06-04 03:55:57'),(13,19,'pending','diproses','Status berubah dari pending ke diproses',NULL,'2026-06-04 03:56:04'),(14,19,'diproses','dikirim','Status berubah dari diproses ke dikirim',NULL,'2026-06-04 03:57:07'),(15,19,NULL,'dikirim',NULL,1,'2026-06-04 03:57:07'),(16,19,'dikirim','selesai','Status berubah dari dikirim ke selesai',NULL,'2026-06-04 03:58:01'),(17,19,NULL,'selesai',NULL,1,'2026-06-04 03:58:01');
/*!40000 ALTER TABLE `order_status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_code` varchar(20) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `alamat_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`alamat_snapshot`)),
  `kurir` enum('jne','jnt','sicepat','anteraja','pos') NOT NULL,
  `kurir_nama` varchar(50) DEFAULT NULL,
  `ongkir` int(10) unsigned NOT NULL DEFAULT 0,
  `resi` varchar(50) DEFAULT NULL,
  `metode_bayar` enum('transfer_bca','transfer_bni','transfer_mandiri','cod','e_wallet') NOT NULL,
  `cod_fee` int(10) unsigned DEFAULT 0,
  `subtotal` int(10) unsigned NOT NULL,
  `diskon` int(10) unsigned DEFAULT 0,
  `total` int(10) unsigned NOT NULL,
  `status` enum('pending','diproses','dikirim','selesai','batal','refund') NOT NULL DEFAULT 'pending',
  `status_bayar` enum('belum','pending','lunas','gagal') NOT NULL DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_status_bayar` (`status_bayar`),
  KEY `idx_order_code` (`order_code`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'LMR-52064509',1,'{\"nama\":\"bastian\",\"telepon\":\"085469875214\",\"alamat\":\"jalan pekalongan\",\"kota\":\"pekalongan\",\"kodepos\":\"54895\"}','jnt','J&T Express',15000,'','',0,2100000,0,2115000,'batal','belum','2026-05-21 07:58:36','2026-05-21 08:40:00',NULL,NULL,NULL,'2026-05-21 08:40:00'),(2,'LMR-6BED4B1B',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jne','JNE Reguler',18000,'','',0,1250000,0,1268000,'selesai','pending','2026-05-21 08:03:59','2026-05-21 08:46:20',NULL,'2026-05-21 08:46:12','2026-05-21 08:46:20',NULL),(3,'LMR-9998B7F7',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','anteraja','AnterAja',14000,'','',0,850000,0,864000,'selesai','pending','2026-05-21 08:09:35','2026-05-21 15:56:18',NULL,'2026-05-21 08:41:57','2026-05-21 15:56:18',NULL),(4,'LMR-6AA0C800',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jnt','J&T Express',15000,'','',0,850000,0,865000,'selesai','pending','2026-05-21 08:12:13','2026-05-21 08:39:33',NULL,'2026-05-21 08:38:57','2026-05-21 08:39:33',NULL),(5,'LMR-50D3587C',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jnt','J&T Express',15000,'','',0,650000,0,665000,'selesai','pending','2026-05-21 08:14:53','2026-05-21 08:38:46',NULL,'2026-05-21 08:31:37','2026-05-21 08:38:46',NULL),(6,'LMR-EE030AC7',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jnt','J&T Express',15000,'JNT26052156B88B69','',0,850000,0,865000,'dikirim','pending','2026-05-21 09:01:31','2026-05-21 09:04:39',NULL,'2026-05-21 09:04:39',NULL,NULL),(7,'LMR-10B05379',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jnt','J&T Express',15000,NULL,'',0,1450000,0,1465000,'diproses','pending','2026-05-21 09:46:44','2026-05-21 09:46:49',NULL,NULL,NULL,NULL),(8,'LMR-6385CBFB',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jnt','J&T Express',15000,'JNT260522CA3D35E5','',0,890000,0,905000,'dikirim','pending','2026-05-21 09:47:55','2026-05-22 15:06:56',NULL,'2026-05-22 15:06:56',NULL,NULL),(9,'LMR-293CB64E',2,'{\"nama\":\"ridho\",\"telepon\":\"546156853255\",\"alamat\":\"jalan cibelok\",\"kota\":\"cibelok\",\"kodepos\":\"57956\"}','jnt','J&T Express',15000,NULL,'',0,1800000,0,1815000,'pending','belum','2026-05-21 09:48:33','2026-05-21 09:48:33',NULL,NULL,NULL,NULL),(10,'LMR-E1245E40',2,'0','jnt','J&T Express',15000,NULL,'',0,1250000,0,1265000,'pending','belum','2026-05-21 10:08:12','2026-05-21 10:30:20',NULL,NULL,NULL,NULL),(11,'LMR-22B8ED4E',2,'0','jnt','J&T Express',15000,NULL,'',0,480000,0,495000,'pending','belum','2026-05-21 10:14:29','2026-05-21 10:30:20',NULL,NULL,NULL,NULL),(12,'LMR-A315C745',2,'{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}','jnt','J&T Express',15000,NULL,'transfer_bca',0,1100000,100000,1015000,'pending','belum','2026-05-21 10:28:30','2026-05-21 10:28:30',NULL,NULL,NULL,NULL),(13,'LMR-0C02EFF1',2,'{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}','jnt','J&T Express',15000,'JNT26052232D4701B','transfer_bni',0,850000,85000,780000,'dikirim','pending','2026-05-21 10:32:44','2026-05-22 15:07:09',NULL,'2026-05-22 15:07:09',NULL,NULL),(14,'LMR-B9749781',2,'{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}','jnt','J&T Express',15000,NULL,'transfer_bni',0,1350000,100000,1265000,'diproses','pending','2026-05-21 10:35:48','2026-05-21 17:37:24',NULL,NULL,NULL,NULL),(15,'LMR-3DF9D1E7',2,'{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}','jnt','J&T Express',15000,'JNT260521DAB994D2','transfer_bca',0,850000,85000,780000,'selesai','pending','2026-05-21 13:47:27','2026-05-21 13:48:39',NULL,'2026-05-21 13:48:25','2026-05-21 13:48:39',NULL),(16,'LMR-68810EEB',2,'{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}','jnt','J&T Express',15000,'JNT26052181BA7D71','transfer_bca',0,850000,85000,780000,'selesai','pending','2026-05-21 17:07:28','2026-05-21 17:35:55',NULL,'2026-05-21 17:08:13','2026-05-21 17:35:55',NULL),(17,'LMR-A40D1F9D',2,'{\"nama\":\"Customer Test\",\"telepon\":\"081234567890\",\"alamat\":\"Jl. Merdeka No. 10 RT 01\\/RW 02\",\"kota\":\"Jakarta Selatan\",\"kodepos\":\"12345\"}','sicepat','SiCepat BEST',12000,NULL,'transfer_bca',0,1450000,0,1462000,'pending','belum','2026-05-23 15:41:52','2026-05-23 15:41:52',NULL,NULL,NULL,NULL),(18,'LMR-200B09D3',2,'{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}','jnt','J&T Express',15000,'JNT260604AE7595CA','transfer_bni',0,850000,0,865000,'selesai','pending','2026-06-04 03:42:41','2026-06-04 03:43:58',NULL,'2026-06-04 03:43:41','2026-06-04 03:43:58',NULL),(19,'LMR-06804B1C',2,'{\"nama\":\"Ridho\",\"telepon\":\"081546474\",\"alamat\":\"jalan taman\",\"kota\":\"pemalang\",\"kodepos\":\"64894\"}','jnt','J&T Express',15000,'JNT2606043806458E','transfer_bni',0,1450000,0,1465000,'selesai','pending','2026-06-04 03:55:57','2026-06-04 03:58:01',NULL,'2026-06-04 03:57:07','2026-06-04 03:58:01',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_after_order_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    
    
    INSERT INTO order_status_logs (order_id, status_lama, status_baru, keterangan)
    VALUES (NEW.order_id, NULL, NEW.status, 'Order dibuat');
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_after_order_completed
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'selesai' AND OLD.status != 'selesai' THEN
        UPDATE products p
        JOIN order_items oi ON p.product_id = oi.product_id
        SET p.total_terjual = p.total_terjual + oi.qty
        WHERE oi.order_id = NEW.order_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_order_status_change
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status != OLD.status THEN
        INSERT INTO order_status_logs (order_id, status_lama, status_baru, keterangan)
        VALUES (NEW.order_id, OLD.status, NEW.status, CONCAT('Status berubah dari ', OLD.status, ' ke ', NEW.status));
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `metode` varchar(50) NOT NULL,
  `jumlah` int(10) unsigned NOT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `external_status` varchar(50) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `bank_pengirim` varchar(50) DEFAULT NULL,
  `status` enum('pending','success','failed','expired','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `verified_by` (`verified_by`),
  KEY `idx_order` (`order_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (2,7,'transfer_bca',1268000,NULL,NULL,NULL,'Customer Test','BCA','pending','2026-05-21 09:58:15',NULL,1),(3,14,'transfer_bni',1265000,NULL,NULL,NULL,NULL,NULL,'pending','2026-05-21 10:35:48',NULL,NULL),(4,15,'transfer_bca',780000,NULL,NULL,NULL,NULL,NULL,'pending','2026-05-21 13:47:27',NULL,NULL),(5,16,'transfer_bca',780000,NULL,NULL,NULL,NULL,NULL,'pending','2026-05-21 17:07:28',NULL,NULL),(6,17,'transfer_bca',1462000,NULL,NULL,NULL,NULL,NULL,'pending','2026-05-23 15:41:52',NULL,NULL),(7,18,'transfer_bni',865000,NULL,NULL,NULL,NULL,NULL,'pending','2026-06-04 03:42:41',NULL,NULL),(8,19,'transfer_bni',1465000,NULL,NULL,NULL,NULL,NULL,'pending','2026-06-04 03:55:57',NULL,NULL);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_gallery`
--

DROP TABLE IF EXISTS `product_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_gallery` (
  `gallery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`gallery_id`),
  KEY `idx_product` (`product_id`,`urutan`),
  CONSTRAINT `product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_gallery`
--

LOCK TABLES `product_gallery` WRITE;
/*!40000 ALTER TABLE `product_gallery` DISABLE KEYS */;
INSERT INTO `product_gallery` VALUES (1,1,'assets/images/products/carolina.webp',1,'2026-05-21 07:41:46');
/*!40000 ALTER TABLE `product_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_notes`
--

DROP TABLE IF EXISTS `product_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_notes` (
  `note_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `note` varchar(100) NOT NULL,
  `tipe_note` enum('top','middle','base') DEFAULT 'middle',
  `urutan` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`note_id`),
  KEY `idx_product` (`product_id`,`tipe_note`),
  CONSTRAINT `product_notes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_notes`
--

LOCK TABLES `product_notes` WRITE;
/*!40000 ALTER TABLE `product_notes` DISABLE KEYS */;
INSERT INTO `product_notes` VALUES (1,1,'Bergamot, Grapefruit and Lemon','top',0),(2,1,'Orange Blossom, Rose and Jasmine','middle',0),(3,1,'eather, Patchouli, Praline, Sandalwood, Cedar and Musk','base',0),(6,2,'Grapefruit , Pear, Raspberry','top',0),(7,2,'Lily of the valley, Rose absolute, Peach','middle',0),(8,2,'Amber, Moss,Vanilla','base',0),(10,3,'Orange, Mandarin and Bergamot','top',0),(12,3,'Ginger','middle',0),(13,3,'Ambergris','base',0),(14,4,'Sage','top',0),(15,4,'Leather   ','middle',0),(16,4,'Cedarwood','base',0),(18,5,'Sicilian Lemon, Calabrian bergamot','top',0),(20,5,'Jasmine Sambac, Lemon Blossom','middle',0),(21,5,'Woodsy Notes, Musk','base',0),(22,6,'Green Notes, Watery Notes, Mint and Ginger','top',0),(23,6,'Coconut, Fig and Salt','middle',0),(24,6,'Tonka Bean and Sandalwood','base',0),(26,7,'Blue Lotus','top',0),(27,7,'Iris','middle',0),(28,7,'Tonka Bean','base',0),(30,8,'Bergamot','top',0),(31,8,'Coconut','middle',0),(32,8,'Tonka Bean','base',0),(34,9,'Bergamot','top',0),(35,9,'Neroli','top',0),(36,9,'Rosemary','middle',0),(37,9,'Cedarwood','base',0),(38,10,'Madagascar Vanilla','middle',0),(39,10,'Orchid','top',0),(40,10,'Jasmine','middle',0),(41,10,'Amber','base',0),(42,11,'Cedarwood','middle',0),(43,11,'Vetiver','base',0),(44,11,'Bergamot','top',0),(45,11,'Leather','base',0),(46,12,'Green Tea','top',0),(47,12,'Bergamot','top',0),(48,12,'Lemon','top',0),(49,12,'White Musk','base',0),(50,7,'Vanilla','base',0);
/*!40000 ALTER TABLE `product_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `variant_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `ukuran` varchar(20) NOT NULL,
  `stok` int(10) unsigned DEFAULT 0,
  `sku` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`variant_id`),
  UNIQUE KEY `uk_product_ukuran` (`product_id`,`ukuran`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_sku` (`sku`),
  CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'100ml',20,NULL,1),(3,2,'100ml',20,NULL,1),(5,3,'100ml',20,NULL,1),(8,4,'100ml',20,NULL,1),(9,5,'100ml',20,NULL,1),(10,6,'125ml',20,NULL,1),(13,7,'100ml',20,NULL,1),(14,8,'125ml',20,NULL,1),(15,9,'50ml',20,NULL,1),(18,10,'100ml',20,NULL,1),(19,11,'50ml',20,NULL,1),(20,12,'50ml',20,NULL,1);
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `brand_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `harga` int(10) unsigned NOT NULL,
  `harga_diskon` int(10) unsigned DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `aroma` enum('woody','floral','fresh','oriental','citrus','spicy','aquatic','gourmand') NOT NULL,
  `gender` enum('pria','wanita','unisex') NOT NULL,
  `stok` int(10) unsigned NOT NULL DEFAULT 0,
  `stok_minimum` int(10) unsigned DEFAULT 5,
  `gambar_utama` varchar(255) NOT NULL,
  `rating_avg` decimal(2,1) DEFAULT 0.0,
  `total_review` int(10) unsigned DEFAULT 0,
  `total_terjual` int(10) unsigned DEFAULT 0,
  `status` enum('aktif','habis','nonaktif','draft') NOT NULL DEFAULT 'aktif',
  `is_best_seller` tinyint(1) DEFAULT 0,
  `is_new_arrival` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_brand` (`brand_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_aroma` (`aroma`),
  KEY `idx_gender` (`gender`),
  KEY `idx_status` (`status`),
  KEY `idx_harga` (`harga`),
  KEY `idx_best_seller` (`is_best_seller`),
  KEY `idx_new` (`is_new_arrival`),
  FULLTEXT KEY `ft_nama_desc` (`nama_produk`,`deskripsi`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON UPDATE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Carolina Herrera','carolina-herrera',8,2,1250000,NULL,'-','floral','wanita',25,5,'assets/images/products/carolina.webp',0.0,0,1,'aktif',1,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(2,'Icon Elixir Woman EDP','etienne-aigner',7,1,980000,850000,'-.','floral','wanita',12,5,'assets/images/products/icon-elixir-woman-edp-100-ml-removebg-preview.webp',5.0,1,3,'aktif',1,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(3,'Pinnace Oryn Man','Pinnace Oryn Man',9,1,850000,NULL,'-','citrus','pria',30,5,'assets/images/products/pinnace-removebg-preview.webp',0.0,0,2,'aktif',0,1,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(4,'Bad Boy Elixir Man','Bad Boy Elixir Man',8,1,1450000,NULL,'-','woody','pria',18,5,'assets/images/products/badboy-removebg-preview.webp',0.0,0,1,'aktif',1,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(5,'Versace Eros Pour Femme','Versace-Eros-Pour-Femme',4,1,750000,650000,'-','floral','wanita',20,5,'assets/images/products/versace-eros-removebg-preview.webp',5.0,1,1,'aktif',0,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(6,'Le Beau Paradise Garden Man','Le-Beau-Paradise-Garden-Man',10,1,1100000,NULL,'-','woody','pria',15,5,'assets/images/products/jeanpg-removebg-preview.webp',0.0,0,0,'aktif',0,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(7,'La Belle Paradise Garden Woman','La Belle Paradise Garden Woman',10,1,1350000,NULL,'-','floral','pria',22,5,'assets/images/products/labelle-removebg-preview.webp',0.0,0,0,'aktif',1,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(8,'Le Beau Man','Le Beau Man',10,1,620000,NULL,'-','woody','pria',28,5,'assets/images/products/lebeauman-removebg-preview.webp',0.0,0,0,'aktif',0,1,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(9,'Aqua Di Gio','aqua-di-gio',5,1,1050000,920000,'-','woody','pria',19,5,'assets/images/products/aqua-removebg-preview.webp',0.0,0,0,'aktif',1,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(10,'Vanilla Orchid','vanilla-orchid',6,3,1800000,NULL,'Vanilla Madagascar dipadukan anggrek eksotis. Manis, sensual, dan mewah.','oriental','wanita',10,5,'assets/images/products/vanilla-removebg-preview.webp',0.0,0,0,'aktif',1,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(11,'Christian Dior Sauvage Elixir Man','Christian Dior Sauvage Elixir Man',3,2,890000,NULL,'-','woody','pria',14,5,'assets/images/products/sauvage-removebg-preview.webp',0.0,0,0,'aktif',0,0,'2026-05-21 07:19:47','2026-06-10 07:56:52'),(12,'Calvin Klein CK One Essence','Calvin Klein CK One Essence',11,1,550000,480000,'-','fresh','unisex',35,5,'assets/images/products/one_essence-removebg-preview.webp',0.0,0,0,'aktif',0,1,'2026-05-21 07:19:47','2026-06-10 07:56:52');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_after_product_price_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.harga != OLD.harga THEN
        UPDATE carts 
        SET harga_satuan = NEW.harga
        WHERE product_id = NEW.product_id AND harga_satuan = OLD.harga;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `promo_codes`
--

DROP TABLE IF EXISTS `promo_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo_codes` (
  `promo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL,
  `tipe` enum('persen','nominal') NOT NULL,
  `nilai` int(10) unsigned NOT NULL,
  `min_pembelian` int(10) unsigned DEFAULT 0,
  `max_diskon` int(10) unsigned DEFAULT NULL,
  `max_penggunaan` int(10) unsigned DEFAULT NULL,
  `terpakai` int(10) unsigned DEFAULT 0,
  `berlaku_dari` date NOT NULL,
  `berlaku_sampai` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`promo_id`),
  UNIQUE KEY `kode` (`kode`),
  KEY `idx_kode` (`kode`),
  KEY `idx_berlaku` (`berlaku_dari`,`berlaku_sampai`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_codes`
--

LOCK TABLES `promo_codes` WRITE;
/*!40000 ALTER TABLE `promo_codes` DISABLE KEYS */;
INSERT INTO `promo_codes` VALUES (1,'LUMIERE10','persen',10,500000,100000,100,6,'2026-01-01','2026-12-31',1,'2026-05-21 09:55:48');
/*!40000 ALTER TABLE `promo_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `uk_user_product_order` (`user_id`,`product_id`,`order_id`),
  KEY `order_id` (`order_id`),
  KEY `idx_product` (`product_id`,`is_approved`),
  KEY `idx_rating` (`rating`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_before_review_insert
BEFORE INSERT ON reviews
FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `s_key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `label` varchar(200) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`s_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sliders`
--

DROP TABLE IF EXISTS `sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sliders` (
  `slider_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) DEFAULT NULL,
  `subjudul` varchar(300) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`slider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` enum('aktif','nonaktif','diblokir') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Lumiere','admin@lumier.com','$2y$10$Z3D4QZJdDuvm/ZlpR8JYOulLmMmvSju0rA0lL03NdN5f3cWQzWeaa',NULL,NULL,'admin','aktif','2026-05-21 07:01:53','2026-05-21 07:15:55'),(2,'Customer Test','customer@email.com','$2y$10$b1gOCDhf7nKt2fKsXWQiduBhAsywspLqm3g6/iiCqwdMnhby0S0wO','081234567890',NULL,'customer','aktif','2026-05-21 07:01:53','2026-05-21 07:15:55'),(3,'Bastian','Bastian@gmail.com','$2y$10$kgZbwF7SQ0Z3VrZakwu64OsCpGsNF1.0F4a2b/G16b.C9zos.Wbe.','085554567852',NULL,'customer','aktif','2026-05-21 07:06:11','2026-05-21 07:06:11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_orders_full`
--

DROP TABLE IF EXISTS `v_orders_full`;
/*!50001 DROP VIEW IF EXISTS `v_orders_full`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_orders_full` AS SELECT
 1 AS `order_id`,
  1 AS `order_code`,
  1 AS `user_id`,
  1 AS `alamat_snapshot`,
  1 AS `kurir`,
  1 AS `kurir_nama`,
  1 AS `ongkir`,
  1 AS `resi`,
  1 AS `metode_bayar`,
  1 AS `cod_fee`,
  1 AS `subtotal`,
  1 AS `diskon`,
  1 AS `total`,
  1 AS `status`,
  1 AS `status_bayar`,
  1 AS `created_at`,
  1 AS `updated_at`,
  1 AS `paid_at`,
  1 AS `shipped_at`,
  1 AS `delivered_at`,
  1 AS `cancelled_at`,
  1 AS `nama_user`,
  1 AS `email_user`,
  1 AS `telepon_user` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_products_full`
--

DROP TABLE IF EXISTS `v_products_full`;
/*!50001 DROP VIEW IF EXISTS `v_products_full`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_products_full` AS SELECT
 1 AS `product_id`,
  1 AS `nama_produk`,
  1 AS `slug`,
  1 AS `brand_id`,
  1 AS `category_id`,
  1 AS `harga`,
  1 AS `harga_diskon`,
  1 AS `deskripsi`,
  1 AS `aroma`,
  1 AS `gender`,
  1 AS `stok`,
  1 AS `stok_minimum`,
  1 AS `gambar_utama`,
  1 AS `rating_avg`,
  1 AS `total_review`,
  1 AS `total_terjual`,
  1 AS `status`,
  1 AS `is_best_seller`,
  1 AS `is_new_arrival`,
  1 AS `created_at`,
  1 AS `updated_at`,
  1 AS `nama_brand`,
  1 AS `brand_slug`,
  1 AS `nama_kategori`,
  1 AS `category_slug` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `wishlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `uk_user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `v_orders_full`
--

/*!50001 DROP VIEW IF EXISTS `v_orders_full`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_orders_full` AS select `o`.`order_id` AS `order_id`,`o`.`order_code` AS `order_code`,`o`.`user_id` AS `user_id`,`o`.`alamat_snapshot` AS `alamat_snapshot`,`o`.`kurir` AS `kurir`,`o`.`kurir_nama` AS `kurir_nama`,`o`.`ongkir` AS `ongkir`,`o`.`resi` AS `resi`,`o`.`metode_bayar` AS `metode_bayar`,`o`.`cod_fee` AS `cod_fee`,`o`.`subtotal` AS `subtotal`,`o`.`diskon` AS `diskon`,`o`.`total` AS `total`,`o`.`status` AS `status`,`o`.`status_bayar` AS `status_bayar`,`o`.`created_at` AS `created_at`,`o`.`updated_at` AS `updated_at`,`o`.`paid_at` AS `paid_at`,`o`.`shipped_at` AS `shipped_at`,`o`.`delivered_at` AS `delivered_at`,`o`.`cancelled_at` AS `cancelled_at`,`u`.`nama` AS `nama_user`,`u`.`email` AS `email_user`,`u`.`telepon` AS `telepon_user` from (`orders` `o` left join `users` `u` on(`o`.`user_id` = `u`.`user_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_products_full`
--

/*!50001 DROP VIEW IF EXISTS `v_products_full`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_products_full` AS select `p`.`product_id` AS `product_id`,`p`.`nama_produk` AS `nama_produk`,`p`.`slug` AS `slug`,`p`.`brand_id` AS `brand_id`,`p`.`category_id` AS `category_id`,`p`.`harga` AS `harga`,`p`.`harga_diskon` AS `harga_diskon`,`p`.`deskripsi` AS `deskripsi`,`p`.`aroma` AS `aroma`,`p`.`gender` AS `gender`,`p`.`stok` AS `stok`,`p`.`stok_minimum` AS `stok_minimum`,`p`.`gambar_utama` AS `gambar_utama`,`p`.`rating_avg` AS `rating_avg`,`p`.`total_review` AS `total_review`,`p`.`total_terjual` AS `total_terjual`,`p`.`status` AS `status`,`p`.`is_best_seller` AS `is_best_seller`,`p`.`is_new_arrival` AS `is_new_arrival`,`p`.`created_at` AS `created_at`,`p`.`updated_at` AS `updated_at`,`b`.`nama_brand` AS `nama_brand`,`b`.`slug` AS `brand_slug`,`c`.`nama_kategori` AS `nama_kategori`,`c`.`slug` AS `category_slug` from ((`products` `p` left join `brands` `b` on(`p`.`brand_id` = `b`.`brand_id`)) left join `categories` `c` on(`p`.`category_id` = `c`.`category_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-10 16:28:32

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + INTERVAL 1 HOUR),
  `used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
