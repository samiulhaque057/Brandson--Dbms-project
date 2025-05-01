-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 12:11 PM
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
-- Table structure for table `loststock`
--

CREATE TABLE `loststock` (
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

INSERT INTO `loststock` ( `date_time`, `facility`, `stage`, `product_type`, `quantity_lost`, `loss_reason`, `evidence`, `created_at`) VALUES
(1, '2025-04-11 04:04:00', 'ssss', 'Slaughter', 'Beef', 444.00, 'err', '', '2025-04-30 10:06:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loststock`


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
