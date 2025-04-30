-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 17, 2025 at 11:02 AM
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
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(100) NOT NULL,
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
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ProductSeller Table
CREATE TABLE IF NOT EXISTS ProductSeller (
    seller_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100),
    address TEXT,
    phone_number VARCHAR(20),
    seller_type VARCHAR(50)
);

-- ProductType Table
CREATE TABLE IF NOT EXISTS ProductType (
    type_name VARCHAR(50) PRIMARY KEY,
    description TEXT
);

-- ProductGrade Table
CREATE TABLE IF NOT EXISTS ProductGrade (
    grade_id VARCHAR(20) PRIMARY KEY,
    grade VARCHAR(10),
    nutrition_value TEXT,
    quality_description TEXT,
    optimum_temperature FLOAT,
    optimum_humidity FLOAT
);

-- AgroFarm Table
CREATE TABLE IF NOT EXISTS AgroFarm (
    farm_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100),
    location TEXT,
    owner_id VARCHAR(20)
);

-- MeatProductBatch Table
CREATE TABLE IF NOT EXISTS MeatProductBatch (
    batch_id VARCHAR(20) PRIMARY KEY,
    meat_type VARCHAR(50),
    grade_id VARCHAR(20),
    farm_id VARCHAR(20),
    price DECIMAL(10,2),
    stock INT,
    manufacturing_date DATE,
    expiry_date DATE,
    batch_no VARCHAR(50),
    slaughtering_date DATE,
    FOREIGN KEY (meat_type) REFERENCES ProductType(type_name),
    FOREIGN KEY (grade_id) REFERENCES ProductGrade(grade_id),
    FOREIGN KEY (farm_id) REFERENCES AgroFarm(farm_id)
);

-- PlaceOrder Table
CREATE TABLE IF NOT EXISTS PlaceOrder (
    seller_id VARCHAR(20),
    coldstorage_id VARCHAR(20),
    PRIMARY KEY (seller_id, coldstorage_id),
    FOREIGN KEY (seller_id) REFERENCES ProductSeller(seller_id)
);

-- Order Table
CREATE TABLE IF NOT EXISTS `Order` (
    order_id VARCHAR(20) PRIMARY KEY,
    customer_id VARCHAR(20),
    order_quantity INT,
    status VARCHAR(50),
    shipping_address TEXT,
    amount DECIMAL(10,2),
    tracking_info TEXT,
    order_date DATE
);

-- Delivery Table
CREATE TABLE IF NOT EXISTS Delivery (
    delivery_id VARCHAR(20) PRIMARY KEY,
    batch_id VARCHAR(20),
    date DATE,
    delivery_type VARCHAR(50),
    FOREIGN KEY (batch_id) REFERENCES MeatProductBatch(batch_id)
);
-- LossRecord Table
CREATE TABLE IF NOT EXISTS LossRecord (
    loss_id VARCHAR(20) PRIMARY KEY,
    batch_id VARCHAR(20),
    stage VARCHAR(50),
    cause TEXT,
    date DATE,
    quantity_lost INT,
    remarks TEXT,
    FOREIGN KEY (batch_id) REFERENCES MeatProductBatch(batch_id)
);

-- PreventiveAction Table
CREATE TABLE IF NOT EXISTS PreventiveAction (
    action_id VARCHAR(20) PRIMARY KEY,
    loss_id VARCHAR(20),
    action_taken TEXT,
    responsible_person VARCHAR(100),
    date DATE,
    effectiveness_rating INT,
    FOREIGN KEY (loss_id) REFERENCES LossRecord(loss_id)
);

-- ColdStorage Table
CREATE TABLE IF NOT EXISTS ColdStorage (
    coldstorage_id VARCHAR(20) PRIMARY KEY,
    location TEXT,
    capacity INT,
    temperature_range VARCHAR(50),
    humidity_level FLOAT,
    product_status VARCHAR(50)
);

-- Sensor Table
CREATE TABLE IF NOT EXISTS Sensor (
    sensor_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100),
    installation_date DATE,
    validation VARCHAR(50)
);

-- SensorData Table
CREATE TABLE IF NOT EXISTS SensorData (
    data_id VARCHAR(20) PRIMARY KEY,
    sensor_id VARCHAR(20),
    timestamp DATETIME,
    sensor_reading TEXT,
    FOREIGN KEY (sensor_id) REFERENCES Sensor(sensor_id)
);

-- Transport Table
CREATE TABLE IF NOT EXISTS Transport (
    transport_id VARCHAR(20) PRIMARY KEY,
    vehicle_id VARCHAR(50),
    route_details TEXT,
    delivery_status VARCHAR(50)
);
