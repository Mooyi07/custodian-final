-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 04:33 AM
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
-- Database: `custodian_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_item`
--

CREATE TABLE `academic_item` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `item_qty` int(11) DEFAULT 0,
  `item_unit` varchar(50) DEFAULT NULL,
  `item_brand` varchar(255) DEFAULT NULL,
  `date_purchase` date DEFAULT NULL,
  `item_location` varchar(255) DEFAULT NULL,
  `item_life` int(11) DEFAULT NULL,
  `item_remarks` text DEFAULT NULL,
  `equipment_type` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_item`
--

INSERT INTO `academic_item` (`item_id`, `item_name`, `item_code`, `item_qty`, `item_unit`, `item_brand`, `date_purchase`, `item_location`, `item_life`, `item_remarks`, `equipment_type`) VALUES
(1, 'Bond Paper', '0', 4, 'ream', 'Hard Copy', '2025-02-23', 'Property Custodian Office', 0, '', NULL),
(2, 'Bond Paper', '0', 10, 'ream', 'Hard Copy', '2025-02-23', 'Property Custodian Office', 0, '', NULL),
(3, 'Pen', '0', 5, 'boxes', 'Pilot', '2025-02-27', 'Property Custodian Office', 0, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `borrow`
--

CREATE TABLE `borrow` (
  `borrow_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `category` enum('equipment','medical','academic','cleaning') NOT NULL,
  `quantity` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow`
--

INSERT INTO `borrow` (`borrow_id`, `userId`, `item_id`, `category`, `quantity`, `borrow_date`, `expected_return_date`, `status`) VALUES
(1, 2, 1, 'academic', 1, '2025-02-23', '2025-02-23', 'reject'),
(2, 2, 1, 'academic', 2, '2025-02-24', '2025-02-24', 'returned'),
(3, 2, 1, 'cleaning', 3, '2025-02-26', '2025-03-05', 'approve'),
(4, 2, 1, 'cleaning', 1, '2025-02-26', '2025-03-05', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `cleaning_item`
--

CREATE TABLE `cleaning_item` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `item_qty` int(11) DEFAULT 0,
  `item_unit` varchar(50) DEFAULT NULL,
  `item_brand` varchar(255) DEFAULT NULL,
  `date_purchase` date DEFAULT NULL,
  `item_location` varchar(255) DEFAULT NULL,
  `item_life` int(11) DEFAULT NULL,
  `item_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cleaning_item`
--

INSERT INTO `cleaning_item` (`item_id`, `item_name`, `item_code`, `item_qty`, `item_unit`, `item_brand`, `date_purchase`, `item_location`, `item_life`, `item_remarks`) VALUES
(1, 'Bleach', '0', 1, 'liters', 'BrandX', '2025-02-24', 'property custodian office', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_qty` int(11) NOT NULL,
  `item_unit` varchar(50) NOT NULL,
  `item_brand` varchar(100) DEFAULT NULL,
  `date_purchase` date DEFAULT NULL,
  `item_location` varchar(255) DEFAULT NULL,
  `item_life` varchar(50) DEFAULT NULL,
  `item_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_equipment`
--

CREATE TABLE `item_equipment` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `item_qty` int(11) DEFAULT 0,
  `item_unit` varchar(50) DEFAULT NULL,
  `item_brand` varchar(255) DEFAULT NULL,
  `date_purchase` date DEFAULT NULL,
  `item_location` varchar(255) DEFAULT NULL,
  `item_life` int(11) DEFAULT NULL,
  `item_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_equipment`
--

INSERT INTO `item_equipment` (`item_id`, `item_name`, `item_code`, `item_qty`, `item_unit`, `item_brand`, `date_purchase`, `item_location`, `item_life`, `item_remarks`) VALUES
(1, 'calculator', '0', 5, 'pcs', 'casio', '2025-02-28', 'Green box beside my table', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `medical_item`
--

CREATE TABLE `medical_item` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `item_qty` int(11) DEFAULT 0,
  `item_unit` varchar(50) DEFAULT NULL,
  `item_brand` varchar(255) DEFAULT NULL,
  `date_purchase` date DEFAULT NULL,
  `item_location` varchar(255) DEFAULT NULL,
  `item_life` int(11) DEFAULT NULL,
  `item_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `id` int(11) NOT NULL,
  `position_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cat`
--

CREATE TABLE `tbl_cat` (
  `cat_id` int(11) NOT NULL,
  `cat_desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `password`, `role`, `created_at`, `position`) VALUES
(1, 'admin', 'admin', 0, '2025-01-31 09:09:56', NULL),
(2, 'earl', 'earl', 1, '2025-01-31 09:19:12', 'Security Guard'),
(3, 'user', 'user', 1, '2025-01-31 09:59:01', NULL),
(4, 'admin123', 'admin123', 1, '2025-02-03 05:49:53', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_item`
--
ALTER TABLE `academic_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`borrow_id`);

--
-- Indexes for table `cleaning_item`
--
ALTER TABLE `cleaning_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_equipment`
--
ALTER TABLE `item_equipment`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `medical_item`
--
ALTER TABLE `medical_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_cat`
--
ALTER TABLE `tbl_cat`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_desc` (`cat_desc`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_item`
--
ALTER TABLE `academic_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cleaning_item`
--
ALTER TABLE `cleaning_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_equipment`
--
ALTER TABLE `item_equipment`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `medical_item`
--
ALTER TABLE `medical_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_cat`
--
ALTER TABLE `tbl_cat`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
