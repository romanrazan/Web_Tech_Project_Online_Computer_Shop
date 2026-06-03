-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 08:31 PM
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
-- Database: `computer_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `category_id`, `created_at`) VALUES
(1, 'MSI', 1, '2026-05-12 21:47:16'),
(2, 'DELL', 1, '2026-05-12 21:47:22'),
(3, 'CORSAIR', 5, '2026-05-12 23:10:50'),
(4, 'LG', 1, '2026-05-12 23:12:57'),
(5, 'LENOVO', 5, '2026-05-16 15:40:30'),
(6, 'SAMSUNG', 4, '2026-05-16 15:43:36'),
(7, 'BENQ', 1, '2026-05-16 15:46:19'),
(8, 'WD', 8, '2026-05-16 15:55:31'),
(9, 'SEAGATE', 9, '2026-05-16 15:55:40'),
(10, 'RAPOO', 10, '2026-05-16 15:55:57'),
(11, 'HUAWEI ', 11, '2026-05-16 15:56:15'),
(12, 'LOGITECH', 12, '2026-05-16 15:56:29'),
(13, 'PS ', 13, '2026-05-16 15:59:20'),
(14, 'GIGABYTE', 14, '2026-05-16 15:59:45'),
(15, 'DEEPCOOL', 15, '2026-05-16 15:59:56'),
(16, 'PENDRIVE', 14, '2026-05-16 16:06:48'),
(17, 'Microlab', 12, '2026-05-16 19:46:27'),
(18, 'APPLE', 67, '2026-05-16 20:12:36'),
(19, 'FENTECH', 18, '2026-05-16 20:19:38'),
(20, 'RYZEN', 20, '2026-05-17 08:51:27'),
(21, 'v500', 8, '2026-05-17 18:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `created_at`) VALUES
(1, 'Monitor', NULL, '2026-05-12 21:45:38'),
(2, 'Laptop', NULL, '2026-05-12 21:45:46'),
(4, 'HDD', NULL, '2026-05-12 21:46:07'),
(5, 'MOUSE', NULL, '2026-05-12 23:10:07'),
(7, 'SSD', NULL, '2026-05-16 15:43:56'),
(8, 'KEYBOARD', NULL, '2026-05-16 15:51:52'),
(9, 'HEADPHONE', NULL, '2026-05-16 15:57:18'),
(10, 'PLAY STATION', NULL, '2026-05-16 15:57:27'),
(11, 'RAM', NULL, '2026-05-16 15:58:05'),
(12, 'MOTHERBOARD', NULL, '2026-05-16 15:58:18'),
(13, 'POWER SUPPLY', NULL, '2026-05-16 15:58:30'),
(14, 'PENDRIVE', NULL, '2026-05-16 15:58:44'),
(17, 'Sound Box', NULL, '2026-05-16 19:45:22'),
(18, 'JOYSTICK', NULL, '2026-05-16 20:18:58'),
(19, 'CPU', NULL, '2026-05-17 08:51:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash_on_delivery','online_wallet') DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `status`, `order_date`) VALUES
(1, 3, 536900.00, 'cash_on_delivery', 'accepted', '2026-05-17 18:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 28, 1, 519900.00),
(2, 1, 30, 1, 17000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manufacturer_review` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `manufacturer_review`, `price`, `category_id`, `brand_id`, `image_path`, `stock`, `created_at`) VALUES
(3, 'Dell Laptop', 'Gaming laptop', 'High performance', 75000.00, 2, 2, '1778945920_dell-inspiron-16-plus-7610.jpg', 155, '2026-05-12 22:43:22'),
(4, 'CORSAIR MOUSE', 'GAMING MOUSE', 'GOOD', 2600.00, 5, 3, '1778773401_mouse4.jpg', 12, '2026-05-12 23:11:54'),
(5, 'LG MONITOR', 'LG 22 INCH MONITOR', 'BEST', 15000.00, 1, 4, '1778773412_monitor1.jpg', 55, '2026-05-12 23:13:40'),
(6, 'MSI LAPTOP', 'gaming laptop', 'good', 99999.00, 2, 1, '1778773534_laptop2.jpg', 11, '2026-05-14 15:45:34'),
(7, 'LENOVO LEGION', 'LENOVO LAPTOP', 'BEST', 150000.00, 2, 5, '1778946094_laptop5.jpg', 45, '2026-05-16 15:41:34'),
(8, 'SAMSUNG SSD', '1TB', 'BEST', 12000.00, 7, 6, '1778946275_ssd1.jpg', 199, '2026-05-16 15:44:35'),
(9, 'LENOVO', 'IDEAPAD SLIM 3', 'BEST', 90000.00, 2, 5, '1778946340_laptop3.jpg', 55, '2026-05-16 15:45:40'),
(10, 'BENQ MONITOR', '24 INCH', 'BEST', 18000.00, 1, 7, '1778946418_monitor4.jpg', 65, '2026-05-16 15:46:58'),
(11, 'KEYBOARD', 'MECHANICAL', 'GOOD', 4000.00, 8, 12, '1778946693_keyboard3.jpg', 111, '2026-05-16 15:51:33'),
(12, 'MSI MOTHERBOARD', 'Z270', 'BEST', 13000.00, 12, 1, '1778947527_91DxRE3U3-L._AC_SL400_.jpg', 53, '2026-05-16 16:05:27'),
(13, 'PENDRIVE', 'ADATA', 'GOOD', 600.00, 14, 16, '1778947579_adata-uv128-128gb-pendrive-1-500x500.jpg', 333, '2026-05-16 16:06:19'),
(14, 'CORSAIR RAM', '3200 MHZ,8GB', 'BEST', 8000.00, 11, 3, '1778947684_corsair-vengeance-lpx-4gb-ram-1-600x315w.jpg', 500, '2026-05-16 16:08:04'),
(15, 'DEEPCOOL', 'POWER SUPPLY', 'BEST', 6000.00, 13, 15, '1778947742_deepcool-pn650m-atx31-650w-power-supply-3-500x500.jpg', 333, '2026-05-16 16:09:02'),
(16, 'GIGABYTE', 'MOTHERBOARD', 'BEST', 11000.00, 12, 14, '1778947787_gigabyte-b650m-gaming-wifi-am5-matx-motherboard-500x500.png', 222, '2026-05-16 16:09:47'),
(17, 'HDD', 'WD 1 TB', 'GOOD', 4500.00, 4, 8, '1778947823_hdd1.jpg', 444, '2026-05-16 16:10:23'),
(18, 'WD', '2TB', 'BEST', 9000.00, 4, 8, '1778947858_hdd3.jpg', 44, '2026-05-16 16:10:58'),
(19, 'SEAGATE', '1TB', 'BEST', 5000.00, 4, 9, '1778947892_hdd2.jpg', 334, '2026-05-16 16:11:32'),
(20, 'PS5', 'PLAYER EDITION', 'BEST', 129000.00, 10, 13, '1778947945_Sony PS5 Slim 825 GB-500x500.jpg', 22, '2026-05-16 16:12:25'),
(21, 'PS4', 'PS4', 'GOOD', 40000.00, 10, 13, '1778947979_PS4-Console-wDS4.jpg', 22, '2026-05-16 16:12:59'),
(22, 'CORSAIR HEADPHONE', 'GAMING', 'BEST', 7000.00, 9, 3, '1778948023_headphone3.jpg', 222, '2026-05-16 16:13:43'),
(23, 'RAPOO KEYBOARD', 'GAMING', 'BEST', 4000.00, 8, 10, '1778948386_keyboard2.jpg', 555, '2026-05-16 16:19:46'),
(24, 'HUAWEI MONITOR', '24 INCH', 'BEST', 20000.00, 1, 11, '1778948445_huawei.jpg', 111, '2026-05-16 16:20:45'),
(25, 'Microlab M-590 2.1 Speaker', 'sound speaker.3 in 1', 'GOOD', 6999.00, 17, 17, '1778960960_m590-1-500x500-500x500.jpg', 56, '2026-05-16 19:49:20'),
(27, 'LogiTech Headphone', 'Zone-300-White', 'ONE of the best', 2090.00, 9, 12, '1778962129_Logitech-ZONE-300-White.jpg', 7, '2026-05-16 20:08:49'),
(28, 'MacBook M4 Pro', '16/512GB\r\nColor-Ash\r\n', 'best', 519900.00, 2, 18, '1778962645_img-MacBook-Pro-Retina-16-Inch-24323-scaled-1250x1250.jpg', 4, '2026-05-16 20:17:25'),
(29, 'FENTECH JOYSTICK', 'Model:WGP13S \r\nCOLOR:ALL COLOR', 'AVERAGE', 3599.00, 18, 19, '1778962967_WGP13S-GABUNGAN.jpg', 39, '2026-05-16 20:22:47'),
(30, 'AMD RYZEN CPU', 'RYZEN 5 5600X', 'BEST', 17000.00, 19, 20, '1779007989_AMD-Ryzen-5-5600X-6-core-12.jpg', 99, '2026-05-17 08:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reviewer_name` varchar(100) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `remember_token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `profile_picture`, `created_at`, `password`, `profile_pic`, `remember_token`) VALUES
(1, 'numan', 'numan@gmail.com', NULL, 'customer', NULL, '2026-05-14 13:46:19', '$2y$10$QzDce0eC7rLU9MN7MLjKLe5EdkPC09mxvPpUX5DQOS9rLMhFbZwfe', NULL, '$2y$10$4DjvrqasiCyq/z0H9fhxheUPxF7ffVo4K2KdKuT.7gIHB7TBsjaYa'),
(2, 'NUMAN', 'numan1@gmail.com', '$2y$10$Kxm3EYPg748DonJ80vPhjuHN07zgGyfRQJ8rdhi0dKB8//l7Gf2Iy', 'admin', NULL, '2026-05-17 16:35:44', NULL, NULL, NULL),
(3, 'numan', 'numan2@gmail.com', '$2y$10$A2XiBF/HxG/2J6tSRyyNOOnbcPcwUKwyMwhKsJPoEV4b4g3vFrF9u', 'customer', NULL, '2026-05-17 18:11:02', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
