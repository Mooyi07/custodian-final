-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 10:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `status` enum('pending','borrowed','returned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `tbl_cat`
--

CREATE TABLE `tbl_cat` (
  `cat_id` int(11) NOT NULL,
  `cat_desc` varchar(255) NOT NULL
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin', 0, '2025-01-31 09:09:56'),
(2, 'earl', 'earl', 1, '2025-01-31 09:19:12'),
(3, 'user', 'user', 1, '2025-01-31 09:59:01'),
(4, 'admin123', 'admin123', 1, '2025-02-03 05:49:53');

--
-- Indexes for dumped tables
CREATE TABLE position (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(255) NOT NULL
);

--
-- Indexes for table `academic_item`
--
ALTER TABLE `academic_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cleaning_item`
--
ALTER TABLE `cleaning_item`
  ADD PRIMARY KEY (`item_id`);

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
-- Indexes for table `tbl_cat`
--
ALTER TABLE `tbl_cat`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_desc` (`cat_desc`);

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
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow`
--
ALTER TABLE `borrow`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cleaning_item`
--
ALTER TABLE `cleaning_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_equipment`
--
ALTER TABLE `item_equipment`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_item`
--
ALTER TABLE `medical_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_cat`
--
ALTER TABLE `tbl_cat`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow`
--
ALTER TABLE `borrow`
  ADD CONSTRAINT `borrow_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_type VARCHAR(50) NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    item_code VARCHAR(50) NOT NULL,
    item_qty INT NOT NULL,
    item_unit VARCHAR(50) NOT NULL,
    item_brand VARCHAR(100) DEFAULT NULL,
    date_purchase DATE DEFAULT NULL,
    item_location VARCHAR(255) DEFAULT NULL,
    item_life VARCHAR(50) DEFAULT NULL,
    item_remarks TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(50) NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `academic_item`
CHANGE COLUMN `item_desc` `item_type` text DEFAULT NULL;