-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2025 at 11:36 PM
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
(101, 'Cold Storage A', 1000.00, 600.00, 3.50, 65.00, 'normal', '2025-05-06 02:00:00', 2001),
(102, 'Cold Storage B', 950.00, 450.00, 2.00, 70.00, 'normal', '2025-05-06 02:10:00', 2002),
(103, 'Cold Storage C', 1200.00, 1100.00, -1.00, 60.00, '', '2025-05-06 02:20:00', 2003),
(104, 'Cold Storage D', 800.00, 300.00, 4.00, 68.00, 'normal', '2025-05-06 02:30:00', 2004),
(105, 'Cold Storage E', 1100.00, 1090.00, -5.00, 55.00, 'critical', '2025-05-06 02:40:00', 2005),
(106, 'Cold Storage F', 980.00, 500.00, 1.50, 63.00, 'normal', '2025-05-06 02:50:00', 2006),
(107, 'Cold Storage G', 1050.00, 750.00, 3.00, 67.00, 'normal', '2025-05-06 03:00:00', 2007);

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
-- Table structure for table `loss_auditor_record_loss`
--

CREATE TABLE `loss_auditor_record_loss` (
  `auditor_id` int(11) NOT NULL,
  `transport_id` int(11) NOT NULL,
  `loss_description` varchar(1111) NOT NULL
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
(35, '2025-05-04 23:38:00', 'saaaaaaaaaaaaaaaaaaa', 'Processing', 'Beef', 543.00, 'ddd', '', '2025-05-05 17:38:33');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(50) NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `type` varchar(30) DEFAULT NULL,
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
-- Table structure for table `preventive_measures`
--

CREATE TABLE `preventive_measures` (
  `loss_id` int(11) DEFAULT NULL,
  `measure_description` varchar(1000) DEFAULT NULL,
  `implementation_date` date DEFAULT NULL
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
(101, 3, 65, 'Sensor A'),
(102, 2, 70, 'Sensor B'),
(103, -1, 60, 'Sensor C'),
(104, 4, 68, 'Sensor D'),
(105, -5, 55, 'Sensor E'),
(106, 1, 63, 'Sensor F'),
(107, 3, 67, 'Sensor G'),
(2001, 3, 65, 'Sensor A'),
(2002, 2, 70, 'Sensor B'),
(2003, -1, 60, 'Sensor C'),
(2004, 4, 68, 'Sensor D'),
(2005, -5, 55, 'Sensor E'),
(2006, 1, 63, 'Sensor F'),
(2007, 3, 67, 'Sensor G');

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
  `coldstorage_id` int(11) DEFAULT NULL,
  `farm_manager_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockdata`
--

INSERT INTO `stockdata` (`batch_id`, `type`, `quantity`, `supplier`, `cost`, `processing_date`, `expiration_Date`, `location`, `date_added`, `coldstorage_id`, `farm_manager_id`) VALUES
('10001', 'Lamb', 120.00, 'Supplier D', 6.00, '2025-05-06', '2025-06-30', 'Cold Storage D', '2025-05-06 20:52:37', 104, NULL),
('10002', 'Lamb', 75.00, 'Supplier E', 4.50, '2025-05-06', '2025-06-20', 'Cold Storage E', '2025-05-06 20:52:37', 105, NULL),
('10003', 'Lamb', 200.00, 'Supplier F', 7.20, '2025-05-06', '2025-07-10', 'Cold Storage F', '2025-05-06 20:52:37', 106, NULL),
('10004', 'Lamb', 95.00, 'Supplier G', 5.50, '2025-05-06', '2025-07-01', 'Cold Storage G', '2025-05-06 20:52:37', 107, NULL),
('2261', 'Beef', 44.00, 'Supplier A', 4.00, '2025-05-03', '2025-06-02', 'Cold Storage B', '2025-05-03 21:40:00', 102, NULL),
('2572', 'Chicken', 555.00, 'Samiul', 5.00, '2025-05-05', '2025-06-04', 'Cold Storage A', '2025-05-05 17:32:39', 101, NULL),
('3948', 'Beef', 22.00, 'Sam', 2.00, '2025-04-23', '2025-05-23', 'Cold Storage B', '2025-04-23 06:15:50', 102, NULL),
('4261', 'Chicken', 8.00, 'Supplier B', 2.00, '2025-04-23', '2025-05-23', 'Cold Storage B', '2025-04-23 06:16:11', 102, NULL),
('5148', 'Beef', 500.00, 'Sam', 5.00, '2025-04-29', '2025-05-29', 'Cold Storage B', '2025-04-29 16:30:13', 102, NULL),
('7342', 'Beef', 2.00, 'Supplier C', 3.00, '2025-05-05', '2025-06-05', 'Cold Storage C', '2025-05-05 10:13:08', 103, NULL),
('9091', 'Beef', 22.00, 'Supplier B', 2.00, '2025-04-29', '2025-05-29', 'Cold Storage B', '2025-04-29 12:24:56', 102, NULL);

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
(222, 'Beef', 22.00, 'Bashundhara', 'Dhanmondi', '3', 102),
(333, 'Chicken', 3.00, 'Noakhali', 'Cumilla', '532', 101),
(342, 'Chicken', 45.00, 'Rajshahi', 'Bogura', '4532', 102),
(532, 'Beef', 33.00, 'Dhaka', 'Ctg', '324233', 103);

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
  ADD KEY `fk_coldstorage_id` (`coldstorage_id`),
  ADD KEY `agro_fk` (`farm_manager_id`);

--
-- Indexes for table `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`transport_id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD KEY `sensor_fk` (`sensor_id`);

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
  MODIFY `coldstorage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `expiring_inventory`
--
ALTER TABLE `expiring_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `loststock`
--
ALTER TABLE `loststock`
  MODIFY `loss_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  ADD CONSTRAINT `agro_fk` FOREIGN KEY (`farm_manager_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_coldstorage_id` FOREIGN KEY (`coldstorage_id`) REFERENCES `cold_storages` (`coldstorage_id`);

--
-- Constraints for table `transport`
--
ALTER TABLE `transport`
  ADD CONSTRAINT `sensor_fk` FOREIGN KEY (`sensor_id`) REFERENCES `sensor` (`sensor_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
