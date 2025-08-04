-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 29, 2025 at 09:03 PM
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
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `activity_id` int(11) NOT NULL,
  `activity_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `activity_type` enum('Arrival','Shipment','Loss Recording') NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `meat_type` varchar(100) DEFAULT NULL,
  `quantity_kg` decimal(10,2) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`activity_id`, `activity_timestamp`, `activity_type`, `batch_number`, `item_id`, `meat_type`, `quantity_kg`, `details`, `user_id`) VALUES
(1, '2025-04-18 15:41:13', 'Arrival', 'PORK-LOIN-105', 6, 'Pork', 500.00, 'From Processing Unit 2', NULL),
(2, '2025-04-18 14:41:13', 'Shipment', 'BEEF-GRND-010', 1, 'Beef', 300.00, 'To Distributor XYZ', NULL),
(3, '2025-04-18 13:41:13', 'Loss Recording', 'CHKN-THGH-088', 7, 'Chicken', 5.00, 'Handling Damage', NULL),
(4, '2025-04-17 16:41:13', 'Arrival', 'BEEF-RIB-033', 8, 'Beef', 800.00, 'From Processing Unit 1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cold_storages`
--

CREATE TABLE `cold_storages` (
  `id` int(11) NOT NULL,
  `location` varchar(50) NOT NULL,
  `total_capacity` decimal(10,2) NOT NULL,
  `used_capacity` decimal(10,2) NOT NULL,
  `current_temp` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `status` enum('normal','warning','critical') DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cold_storages`
--

INSERT INTO `cold_storages` (`id`, `location`, `total_capacity`, `used_capacity`, `current_temp`, `humidity`, `status`, `created_at`) VALUES
(1, 'Cold Storage A', 5000.00, 3200.00, -10.00, 100.00, 'normal', '2025-04-25 17:54:57'),
(2, 'Cold Storage F', 2753.00, 134.00, 34.00, 24.00, 'critical', '2025-04-29 17:39:26'),
(3, 'Cold storage A', 5000.00, 2000.00, -34.00, 25.00, 'normal', '2025-04-29 17:40:03'),
(4, 'Cold storage C', 5600.00, 3000.00, 2.00, 78.00, 'critical', '2025-04-29 17:40:46'),
(5, 'Cold storage B ', 4500.00, 2300.00, 4.00, 67.00, 'warning', '2025-04-29 17:41:16'),
(6, 'Cold storage G', 6000.00, 2445.00, 8.00, 35.00, 'warning', '2025-04-29 17:41:53'),
(7, 'Cold Storage F', 23434.00, 34.00, 34.00, 43.00, 'critical', '2025-04-29 19:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `environment_logs`
--

CREATE TABLE `environment_logs` (
  `log_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `log_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `temperature` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `environment_logs`
--

INSERT INTO `environment_logs` (`log_id`, `zone_id`, `log_timestamp`, `temperature`, `humidity`) VALUES
(1, 1, '2025-04-18 16:39:13', -18.50, 88.00),
(2, 2, '2025-04-18 16:40:13', -15.00, 90.00),
(3, 3, '2025-04-18 16:38:13', -17.00, 95.00),
(4, 1, '2025-04-18 15:41:13', -18.60, 87.50),
(5, 2, '2025-04-18 15:41:13', -17.50, 89.00),
(6, 3, '2025-04-18 15:41:13', -17.20, 93.00);

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
(106, 'Lamb', '14134', 14.00, '2025-05-03 19:01:00', 'Cold Storage C', '', '2025-04-27 13:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `expiring_products`
--

CREATE TABLE `expiring_products` (
  `id` int(11) NOT NULL,
  `product` varchar(50) NOT NULL,
  `batch` varchar(20) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `expires` datetime NOT NULL,
  `storage` varchar(50) NOT NULL,
  `status` enum('warning','urgent') DEFAULT 'warning',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` int(11) NOT NULL,
  `batch_number` varchar(100) NOT NULL,
  `meat_type` varchar(100) NOT NULL,
  `quantity_kg` decimal(10,2) NOT NULL,
  `processing_date` date DEFAULT NULL,
  `expiration_date` date NOT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `status` enum('In Storage','Shipped','Lost') DEFAULT 'In Storage',
  `entry_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`item_id`, `batch_number`, `meat_type`, `quantity_kg`, `processing_date`, `expiration_date`, `zone_id`, `status`, `entry_timestamp`) VALUES
(1, 'BEEF-GRND-010', 'Beef', 300.00, '2025-03-10', '2025-09-10', 1, 'Shipped', '2025-04-18 16:41:13'),
(2, 'BEEF-GRND-011', 'Beef', 150.00, '2025-03-15', '2025-04-21', 1, 'In Storage', '2025-04-18 16:41:13'),
(3, 'PORK-CUTS-045', 'Pork', 80.00, '2025-03-18', '2025-04-23', 2, 'In Storage', '2025-04-18 16:41:13'),
(4, 'CHKN-BRST-092', 'Chicken', 220.00, '2025-03-20', '2025-04-24', 1, 'In Storage', '2025-04-18 16:41:13'),
(5, 'BEEF-STK-008', 'Beef', 55.00, '2025-03-01', '2025-04-17', 2, 'In Storage', '2025-04-18 16:41:13'),
(6, 'PORK-LOIN-105', 'Pork', 500.00, '2025-04-18', '2025-10-18', 1, 'In Storage', '2025-04-18 16:41:13'),
(7, 'CHKN-THGH-088', 'Chicken', 95.00, '2025-04-10', '2025-07-10', 3, 'In Storage', '2025-04-18 16:41:13'),
(8, 'BEEF-RIB-033', 'Beef', 800.00, '2025-04-17', '2025-10-17', 1, 'In Storage', '2025-04-18 16:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `loss_events`
--

CREATE TABLE `loss_events` (
  `id` int(11) NOT NULL,
  `product` varchar(100) DEFAULT NULL,
  `batch` varchar(50) DEFAULT NULL,
  `quantity` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `value` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loss_events`
--

INSERT INTO `loss_events` (`id`, `product`, `batch`, `quantity`, `date`, `reason`, `value`) VALUES
(12, 'beef', '213313', 13, '2025-04-02', 'rain', 89),
(15, 'beef', '213313', 13, '2025-04-02', 'rain', 97),
(16, 'beef', '213313', 13, '2025-04-02', 'rain', 54),
(29, 'cow', '143', 143, '2025-04-30', 'vwf', 99),
(30, 'lamb', '324', 1243, '2025-04-23', 'cold storage temparature', 130),
(31, 'chicken', '322', 224, '2025-03-15', 'heat', 324),
(32, 'lamb', '23geg', 1234, '2025-02-27', 'rain', 1313),
(33, 'chicken', 'fbfg454', 345, '2025-01-30', 'bad weather', 2355),
(34, 'chicken', 'aghe55', 35, '2024-12-19', 'excessive rain', 252);

-- --------------------------------------------------------

--
-- Table structure for table `sensor_readings`
--

CREATE TABLE `sensor_readings` (
  `id` int(11) NOT NULL,
  `storage_id` int(11) NOT NULL,
  `temperature` float NOT NULL,
  `humidity` float NOT NULL,
  `sensor_status` varchar(50) DEFAULT NULL,
  `recorded_at` datetime NOT NULL,
  `battery_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sensor_readings`
--

INSERT INTO `sensor_readings` (`id`, `storage_id`, `temperature`, `humidity`, `sensor_status`, `recorded_at`, `battery_level`) VALUES
(1, 1, 23.5, 60, 'active', '2025-04-01 10:00:00', 85),
(2, 1, 24, 58, 'active', '2025-04-01 11:00:00', 80),
(3, 1, 22.8, 65, 'active', '2025-04-01 12:00:00', 75);

-- --------------------------------------------------------

--
-- Table structure for table `stockdata`
--

CREATE TABLE `stockdata` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `batch` varchar(100) NOT NULL,
  `quantity` float NOT NULL,
  `supplier` varchar(150) NOT NULL,
  `cost` float NOT NULL,
  `processing_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `location` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Muaaz', 'khan', '2220401@iub.edu.bd', 'admin', '$2y$10$rC4p/0tzF6bofKFocC5X4up1rjZ01/N2KYUpyzVyQOVvElX0hbnyq');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_activity_time` (`activity_timestamp`),
  ADD KEY `idx_activity_type` (`activity_type`);

--
-- Indexes for table `cold_storages`
--
ALTER TABLE `cold_storages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `environment_logs`
--
ALTER TABLE `environment_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_zone_time` (`zone_id`,`log_timestamp`);

--
-- Indexes for table `expiring_inventory`
--
ALTER TABLE `expiring_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expiring_products`
--
ALTER TABLE `expiring_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `batch_number` (`batch_number`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `loss_events`
--
ALTER TABLE `loss_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sensor_readings`
--
ALTER TABLE `sensor_readings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockdata`
--
ALTER TABLE `stockdata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `storage_zones`
--
ALTER TABLE `storage_zones`
  ADD PRIMARY KEY (`zone_id`),
  ADD UNIQUE KEY `zone_name` (`zone_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cold_storages`
--
ALTER TABLE `cold_storages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `environment_logs`
--
ALTER TABLE `environment_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expiring_inventory`
--
ALTER TABLE `expiring_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `expiring_products`
--
ALTER TABLE `expiring_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `loss_events`
--
ALTER TABLE `loss_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `sensor_readings`
--
ALTER TABLE `sensor_readings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stockdata`
--
ALTER TABLE `stockdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storage_zones`
--
ALTER TABLE `storage_zones`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `environment_logs`
--
ALTER TABLE `environment_logs`
  ADD CONSTRAINT `environment_logs_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `storage_zones` (`zone_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `storage_zones` (`zone_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
