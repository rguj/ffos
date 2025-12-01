-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 08:55 AM
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
-- Database: `ffos`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_bundle` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `code`, `category_id`, `is_bundle`, `name`, `price`, `image_path`, `is_active`) VALUES
(1, 'BIGMAC', NULL, 0, 'Big Mac Meal', 150.00, NULL, 0),
(2, 'MCCHKN', NULL, 0, 'McChicken Meal', 140.00, NULL, 0),
(3, 'FRIESL', NULL, 0, 'Large Fries', 60.00, NULL, 0),
(4, 'COKEL', NULL, 0, 'Large Coke', 45.00, NULL, 0),
(5, 'wfwqfwq', 1, 0, 'safasf', 51581.00, 'uploads/prod_692cfa68962f31.90440074.jpg', 0),
(6, 'dsada', NULL, 1, 'jkanbjksak', 815.00, NULL, 0),
(7, 'sada', NULL, 1, 'dasd', 51881.00, NULL, 0),
(8, 'ww', 1, 0, 'sda', 242.00, NULL, 0),
(9, 'llp', NULL, 1, 'wet', 105.00, NULL, 0),
(10, 'tew', NULL, 1, 'tew', 340.00, NULL, 0),
(11, 'hhh', NULL, 1, 'n', 45.00, NULL, 0),
(12, 'wwrt', 2, 0, 'efawefg', 325325.00, NULL, 0),
(27, 'BRG001', 1, 0, 'Classic Cheeseburger', 79.00, 'images/burger1.jpg', 1),
(28, 'BRG002', 1, 0, 'Beef BBQ Burger', 99.00, 'images/burger2.jpg', 1),
(29, 'BRG003', 1, 0, 'Double Patty Stack', 129.00, 'images/burger3.jpg', 1),
(30, 'CHK001', 2, 0, '1-PC Crispy Chicken', 89.00, 'images/chicken1.jpg', 1),
(31, 'CHK002', 2, 0, '2-PC Chicken with Rice', 149.00, 'images/chicken2.jpg', 1),
(32, 'SDE001', 3, 0, 'Regular Fries', 39.00, 'images/fries1.jpg', 1),
(33, 'SDE002', 3, 0, 'Large Fries', 59.00, 'images/fries2.jpg', 1),
(34, 'SDE003', 3, 0, 'Butter Corn Cup', 35.00, 'images/corn1.jpg', 1),
(35, 'DRK001', 4, 0, 'Iced Tea', 35.00, 'images/icedtea.jpg', 1),
(36, 'DRK002', 4, 0, 'Soft Drink (Soda)', 30.00, 'images/soda.jpg', 1),
(37, 'DRK003', 4, 0, 'Bottled Water', 25.00, 'images/water.jpg', 1),
(38, 'DST001', 5, 0, 'Vanilla Sundae', 45.00, 'images/sundae1.jpg', 1),
(39, 'DST002', 5, 0, 'Choco Sundae', 55.00, 'images/sundae2.jpg', 1),
(40, 'DST003', 5, 0, 'Apple Pie', 49.00, 'images/pie1.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `teller_terminal_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_received` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('UNPAID','PAID','IN_PROCESS','READY_FOR_CLAIM','COMPLETED','CANCELLED','CLAIMED') NOT NULL DEFAULT 'UNPAID',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `display_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `terminal_id`, `teller_terminal_id`, `total_amount`, `cash_received`, `change_amount`, `status`, `paid_at`, `created_at`, `updated_at`, `display_number`) VALUES
(1, NULL, NULL, 51881.00, 0.00, 0.00, 'CANCELLED', '2025-11-30 22:11:18', '2025-12-01 04:19:51', '2025-12-01 05:40:12', NULL),
(2, NULL, NULL, 150.00, 0.00, 0.00, 'CANCELLED', '2025-11-30 22:35:29', '2025-11-30 22:08:28', '2025-12-01 05:40:14', 1),
(3, NULL, NULL, 150.00, 0.00, 0.00, 'CANCELLED', '2025-11-30 22:35:54', '2025-11-30 22:28:19', '2025-12-01 05:40:17', 2),
(4, NULL, NULL, 195.00, 0.00, 0.00, 'CANCELLED', '2025-11-30 22:40:47', '2025-11-30 22:40:39', '2025-12-01 05:45:51', 3),
(5, 1, 2, 210.00, 0.00, 0.00, 'CANCELLED', '2025-11-30 23:00:38', '2025-11-30 22:46:16', '2025-12-01 06:16:05', 4),
(6, 1, 2, 965.00, 1000.00, 35.00, '', '2025-11-30 23:16:28', '2025-11-30 23:16:23', '2025-11-30 23:38:58', 5),
(7, 1, 2, 105.00, 200.00, 95.00, '', '2025-11-30 23:46:24', '2025-11-30 23:44:58', '2025-11-30 23:47:00', 6),
(8, 1, 2, 60.00, 100.00, 40.00, 'CLAIMED', '2025-12-01 00:24:48', '2025-12-01 00:24:28', '2025-12-01 00:30:31', 7),
(9, 1, NULL, 242.00, 0.00, 0.00, 'UNPAID', NULL, '2025-12-01 00:38:28', '2025-12-01 00:38:28', 8),
(10, 1, NULL, 965.00, 0.00, 0.00, 'UNPAID', NULL, '2025-12-01 00:40:44', '2025-12-01 00:40:44', 9),
(11, 1, NULL, 340.00, 0.00, 0.00, 'UNPAID', NULL, '2025-12-01 00:41:01', '2025-12-01 00:41:01', 10);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `source` enum('CUSTOMER','TELLER','','') NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `source`, `subtotal`) VALUES
(4, 1, 1, 1, 150.00, 'CUSTOMER', 0.00),
(5, 1, 1, 1, 150.00, 'CUSTOMER', 0.00),
(6, 1, 5, 1, 51581.00, 'TELLER', 0.00),
(8, 2, 1, 1, 150.00, 'CUSTOMER', 0.00),
(9, 3, 1, 1, 150.00, 'CUSTOMER', 0.00),
(12, 4, 1, 1, 150.00, 'CUSTOMER', 0.00),
(13, 4, 4, 1, 45.00, 'CUSTOMER', 0.00),
(16, 5, 1, 1, 150.00, 'CUSTOMER', 0.00),
(17, 5, 3, 1, 60.00, 'CUSTOMER', 0.00),
(20, 6, 1, 1, 150.00, 'CUSTOMER', 0.00),
(21, 6, 6, 1, 815.00, 'CUSTOMER', 0.00),
(24, 7, 3, 1, 60.00, 'CUSTOMER', 0.00),
(25, 7, 4, 1, 45.00, 'CUSTOMER', 0.00),
(27, 8, 3, 1, 60.00, 'CUSTOMER', 0.00),
(28, 9, 8, 1, 242.00, 'CUSTOMER', 0.00),
(29, 10, 1, 1, 150.00, 'CUSTOMER', 0.00),
(30, 10, 6, 1, 815.00, 'CUSTOMER', 0.00),
(31, 11, 10, 1, 340.00, 'CUSTOMER', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Burgers', '2025-12-01 01:38:45'),
(2, 'Chicken', '2025-12-01 01:38:45'),
(3, 'Fries & Sides', '2025-12-01 01:38:45'),
(4, 'Drinks', '2025-12-01 01:38:45'),
(5, 'Desserts', '2025-12-01 02:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `terminals`
--

CREATE TABLE `terminals` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('CUSTOMER','TELLER','KITCHEN','CLAIM') NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `pin_code` char(6) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terminals`
--

INSERT INTO `terminals` (`id`, `name`, `type`, `employee_name`, `pin_code`, `is_active`, `created_at`) VALUES
(1, 'KIOSK1', 'CUSTOMER', 'KIOSK1', '013259', 1, '2025-12-01 01:24:52'),
(2, 'TELLER1', 'TELLER', 'TELLER1', '405479', 1, '2025-12-01 04:20:38'),
(3, 'KITCHEN1', 'KITCHEN', 'KITCHEN1', '336714', 1, '2025-12-01 06:17:58'),
(4, 'CLAIMING1', 'CLAIM', 'CLAIMING1', '888086', 1, '2025-12-01 06:23:18'),
(5, 'CLAIMING1', 'CLAIM', 'CLAIMING1', '747586', 1, '2025-12-01 06:24:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_menu_items_category` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `terminal_id` (`terminal_id`),
  ADD KEY `teller_terminal_id` (`teller_terminal_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terminals`
--
ALTER TABLE `terminals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `terminals`
--
ALTER TABLE `terminals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_menu_items_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`terminal_id`) REFERENCES `terminals` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`teller_terminal_id`) REFERENCES `terminals` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
