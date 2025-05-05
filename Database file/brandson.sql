-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 05, 2025 at 07:13 PM
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
-- Database: `brandson`
--

-- --------------------------------------------------------

--
-- Table structure for table `cold_storages`
--

CREATE TABLE `cold_storages` (
  `coldstorage_id` int(11) NOT NULL,
  `location` varchar(50) NOT NULL,
  `total_capacity` decimal(10,2) NOT NULL,
  `used_capacity` decimal(10,2) NOT NULL,
  `current_temp` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `status` enum('normal','warning','critical') DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sensor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cold_storages`
--

INSERT INTO `cold_storages` (`coldstorage_id`, `location`, `total_capacity`, `used_capacity`, `current_temp`, `humidity`, `status`, `created_at`, `sensor_id`) VALUES
(16, 'cold', 534.00, 44.00, 0.00, 0.00, 'normal', '2025-05-05 15:04:23', 45),
(17, 'Cold Storage A66', 654.00, 6.00, 0.00, 0.00, 'normal', '2025-05-05 15:05:02', 567);

-- --------------------------------------------------------

--
-- Table structure for table `expiring_inventory`
--

CREATE TABLE `expiring_inventory` (
  `id` int(11) NOT NULL,
  `product` varchar(100) NOT NULL,
  `batch` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `expires` datetime NOT NULL,
  `storage` varchar(100) NOT NULL,
  `status` enum('urgent','warning') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expiring_inventory`
--

INSERT INTO `expiring_inventory` (`id`, `product`, `batch`, `quantity`, `expires`, `storage`, `status`, `created_at`) VALUES
(93, 'Chicken', '134', 234234.00, '2025-04-08 21:42:00', 'Cold Storage C', '', '2025-04-25 15:42:31'),
(100, 'Pork', '324325', 452.00, '2025-04-23 00:00:00', 'Freeze 1', '', '2025-04-26 10:40:53'),
(104, 'Chicken', '12324', 234234.00, '2025-05-09 17:05:00', 'Cold Storage B', '', '2025-04-26 11:05:23'),
(106, 'Lamb', '14134', 14.00, '2025-05-03 19:01:00', 'Cold Storage C', '', '2025-04-27 13:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loststock`
--

CREATE TABLE `loststock` (
  `loss_id` int(11) UNSIGNED NOT NULL,
  `date_time` datetime NOT NULL,
  `facility` varchar(255) NOT NULL,
  `stage` varchar(255) NOT NULL,
  `product_type` varchar(255) NOT NULL,
  `quantity_lost` decimal(10,2) NOT NULL,
  `loss_reason` text NOT NULL,
  `evidence` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loststock`
--

INSERT INTO `loststock` (`loss_id`, `date_time`, `facility`, `stage`, `product_type`, `quantity_lost`, `loss_reason`, `evidence`, `created_at`) VALUES
(14, '2025-04-30 12:01:00', 'no', 'Storage', 'Poultry', 0.01, 'vv', '', '2025-04-30 19:01:17'),
(15, '2025-04-30 12:23:00', 'no', 'Handling', 'Lamb', 0.01, 'koooo', '', '2025-04-30 19:23:36'),
(32, '2025-05-08 17:18:00', 'bbbbbb', 'Slaughter', 'Beef', 33.00, '33', '', '2025-05-05 11:18:57'),
(34, '2025-05-09 17:54:00', 'aaa', 'Processing', 'aa', 2.00, '222', NULL, '2025-05-05 11:54:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('Pending','Processing','Delivered','Cancelled') DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_harvest_loss`
--

CREATE TABLE `post_harvest_loss` (
  `loss_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity_lost` decimal(10,2) NOT NULL,
  `loss_reason` varchar(255) NOT NULL,
  `loss_date` date NOT NULL,
  `action_taken` varchar(255) DEFAULT NULL,
  `improvement_notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sensor`
--

CREATE TABLE `sensor` (
  `sensor_id` int(11) NOT NULL,
  `temperature` int(11) DEFAULT NULL,
  `humidity` int(11) DEFAULT NULL,
  `sensor_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sensor`
--

INSERT INTO `sensor` (`sensor_id`, `temperature`, `humidity`, `sensor_name`) VALUES
(33, 3, 3, '3'),
(45, 4, 4, 'fgewa'),
(333, 3, 3, 'fs'),
(523, 5, 5, '523'),
(567, 5, 55, 'hdtr'),
(3352, 3, 3, 'gasre');

-- --------------------------------------------------------

--
-- Table structure for table `stockdata`
--

CREATE TABLE `stockdata` (
  `batch_id` varchar(50) NOT NULL,
  `type` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `processing_date` date NOT NULL,
  `expiration_Date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `coldstorage_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockdata`
--

INSERT INTO `stockdata` (`batch_id`, `type`, `quantity`, `supplier`, `cost`, `processing_date`, `expiration_Date`, `location`, `date_added`, `coldstorage_id`) VALUES
('2261', 'Beef', 44.00, '4', 4.00, '2025-05-03', '2025-06-02', 'Cold Storage Bdddddddddd', '2025-05-03 21:40:00', NULL),
('3948', 'Beef', 22.00, 'sam', 2.00, '2025-04-23', '2025-05-23', 'Cold Storage B', '2025-04-23 06:15:50', NULL),
('4261', 'Chicken', 8.00, '2', 2.00, '2025-04-23', '2025-03-05', 'Cold Storage B', '2025-04-23 06:16:11', NULL),
('5148', 'Beef', 500.00, 'sam', 5.00, '2025-04-29', '2025-05-29', 'Cold Storage B', '2025-04-29 16:30:13', NULL),
('7342', 'Beef', 2.00, '3', 3.00, '2025-05-05', '2025-06-05', 'Cold Storage C', '2025-05-05 10:13:08', NULL),
('9091', 'Beef', 22.00, '2', 2.00, '2025-04-29', '2025-05-29', 'Cold Storage B', '2025-04-29 12:24:56', NULL),
('9569', 'Beef', 55.00, '33', 3.00, '2025-05-04', '2025-06-03', 'Cold Storage D', '2025-05-04 10:17:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `storage_zones`
--

CREATE TABLE `storage_zones` (
  `zone_id` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `target_temp_min` decimal(5,2) DEFAULT -20.00,
  `target_temp_max` decimal(5,2) DEFAULT -17.00,
  `target_humidity_min` decimal(5,2) DEFAULT 85.00,
  `target_humidity_max` decimal(5,2) DEFAULT 92.00,
  `total_capacity_kg` decimal(10,2) DEFAULT 10000.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storage_zones`
--

INSERT INTO `storage_zones` (`zone_id`, `zone_name`, `target_temp_min`, `target_temp_max`, `target_humidity_min`, `target_humidity_max`, `total_capacity_kg`) VALUES
(1, 'Zone A', -20.00, -17.00, 85.00, 92.00, 15000.00),
(2, 'Zone B', -20.00, -17.00, 85.00, 92.00, 10000.00),
(3, 'Zone C', -18.00, -15.00, 88.00, 95.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `transport`
--

CREATE TABLE `transport` (
  `transport_id` int(11) NOT NULL,
  `meat_type` varchar(100) DEFAULT NULL,
  `meat_quantity` decimal(10,2) DEFAULT NULL,
  `start_location` varchar(255) DEFAULT NULL,
  `end_location` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `sensor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport`
--

INSERT INTO `transport` (`transport_id`, `meat_type`, `meat_quantity`, `start_location`, `end_location`, `tracking_number`, `sensor_id`) VALUES
(222, 'beef', 22.00, 'ddew', 'e', '3', 33),
(333, 'chicken', 3.00, 'few', 'fwe', '532', 3352),
(342, 'CHI', 45.00, 'GFAWE', 'GFAEW', '4532', 523),
(532, 'gfewa', 33.00, 'few', 'da', '324233', 333);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `user_type`, `password`) VALUES
(1, 'muaaz', 'khan', 'muaazkhan502@gmail.com', 'user', '$2y$10$OPB3YwzcibhjpE73PRUJ1.nx3RTqU7CnAikkhxtTLvHx2A8hfwxiq'),
(2, 'Muaaz', 'khan', '2220401@iub.edu.bd', 'admin', '$2y$10$rC4p/0tzF6bofKFocC5X4up1rjZ01/N2KYUpyzVyQOVvElX0hbnyq'),
(14, 'Samiul', 'Haque', '2210968@iub.edu.bd', 'admin', '$2y$10$SWtNOTxmYoGZ4ZCMkUL4HeUw/Zz1ecZIsVazQuMUwPpL9z/54vR2W'),
(15, 'aaaaaaaaa', 'aaaaaaaaaaa', 'rejwan.rizvi@yahoo.com', 'admin', '$2y$10$5MqBK9RAnKAYMuzpw1T6t.2OmtIsuq2fulrFAolea6EO7hOHlP5Ky');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cold_storages`
--
ALTER TABLE `cold_storages`
  ADD PRIMARY KEY (`coldstorage_id`),
  ADD KEY `fk_sensor_id` (`sensor_id`);

--
-- Indexes for table `expiring_inventory`
--
ALTER TABLE `expiring_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loststock`
--
ALTER TABLE `loststock`
  ADD PRIMARY KEY (`loss_id`);

--
-- Indexes for table `post_harvest_loss`
--
ALTER TABLE `post_harvest_loss`
  ADD PRIMARY KEY (`loss_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `sensor`
--
ALTER TABLE `sensor`
  ADD PRIMARY KEY (`sensor_id`);

--
-- Indexes for table `stockdata`
--
ALTER TABLE `stockdata`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `fk_coldstorage_id` (`coldstorage_id`);

--
-- Indexes for table `storage_zones`
--
ALTER TABLE `storage_zones`
  ADD PRIMARY KEY (`zone_id`),
  ADD UNIQUE KEY `zone_name` (`zone_name`);

--
-- Indexes for table `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`transport_id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cold_storages`
--
ALTER TABLE `cold_storages`
  MODIFY `coldstorage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `expiring_inventory`
--
ALTER TABLE `expiring_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `loststock`
--
ALTER TABLE `loststock`
  MODIFY `loss_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `post_harvest_loss`
--
ALTER TABLE `post_harvest_loss`
  MODIFY `loss_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storage_zones`
--
ALTER TABLE `storage_zones`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transport`
--
ALTER TABLE `transport`
  MODIFY `transport_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42424;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cold_storages`
--
ALTER TABLE `cold_storages`
  ADD CONSTRAINT `fk_sensor_id` FOREIGN KEY (`sensor_id`) REFERENCES `sensor` (`sensor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_harvest_loss`
--
ALTER TABLE `post_harvest_loss`
  ADD CONSTRAINT `post_harvest_loss_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `stockdata`
--
ALTER TABLE `stockdata`
  ADD CONSTRAINT `fk_coldstorage_id` FOREIGN KEY (`coldstorage_id`) REFERENCES `cold_storages` (`coldstorage_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
