-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 11, 2025 lúc 04:58 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `user_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `month_year` date NOT NULL,
  `status` enum('available','in_use','expired') DEFAULT 'available',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `month_year`, `status`, `start_date`, `end_date`) VALUES
(1, 'Account1_Jan2024', '2024-01-01', 'available', NULL, NULL),
(2, 'Account2_Jan2024', '2024-01-01', 'available', NULL, NULL),
(3, 'Account3_Jan2024', '2024-01-01', 'available', NULL, NULL),
(4, 'Account4_Jan2024', '2024-01-01', 'available', NULL, NULL),
(5, 'Account5_Jan2024', '2024-01-01', 'available', NULL, NULL),
(6, 'Account1_Feb2024', '2024-02-01', 'available', NULL, NULL),
(7, 'Account2_Feb2024', '2024-02-01', 'available', NULL, NULL),
(8, 'Account3_Feb2024', '2024-02-01', 'available', NULL, NULL),
(9, 'Account4_Feb2024', '2024-02-01', 'available', NULL, NULL),
(10, 'Account5_Feb2024', '2024-02-01', 'available', NULL, NULL),
(11, 'accuot1', '2025-02-01', 'available', NULL, NULL),
(12, 'accuot1', '2025-04-01', 'available', NULL, NULL),
(13, 'accuot1', '2025-02-01', 'available', NULL, NULL),
(14, 'accuot1', '2025-02-01', 'available', NULL, NULL),
(15, 'accuot1', '2025-01-01', 'in_use', NULL, NULL),
(16, 'a', '2025-01-01', 'available', '2025-01-11', '2025-02-11'),
(17, 'àdsfd', '2025-01-01', 'available', '2025-01-12', '2025-02-12'),
(18, 'ádfdf', '2025-01-01', 'available', '2025-01-11', '2025-02-11'),
(19, 'accuot1dfg', '2025-01-01', 'available', '2025-01-11', '2025-02-11'),
(20, 'admin1', '2025-01-01', 'available', '2025-01-17', '2025-02-17'),
(21, 'accuot2', '2025-02-01', 'available', '2025-02-08', '2025-03-08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `subscription_plan` varchar(50) NOT NULL,
  `account` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('còn hiệu lực','hết hiệu lực') NOT NULL,
  `email` varchar(100) NOT NULL,
  `facebook_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `subscription_plan`, `account`, `start_date`, `end_date`, `status`, `email`, `facebook_link`) VALUES
(2, 'admin', '1 tháng', 'accuot1', '2025-01-11', '2025-02-11', 'còn hiệu lực', 'kiendtph49182@gmail.com', 'https://www.facebook.com/hai.trung.hoang.3112');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
