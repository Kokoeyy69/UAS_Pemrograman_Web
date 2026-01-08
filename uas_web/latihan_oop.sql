-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 19, 2025 at 10:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `latihan_oop`
--

-- --------------------------------------------------------

--
-- Table structure for table `artikel`
--

CREATE TABLE `artikel` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artikel`
--

INSERT INTO `artikel` (`id`, `judul`, `slug`, `isi`, `gambar`, `tanggal`, `created_at`) VALUES
(1, 'Belajar PHP OOP', 'belajar-php-oop', 'Ini adalah contoh artikel mengenai OOP di PHP.', 'https://santrikoding.com/storage/posts/1a7097d5-3bd7-4eaf-806e-88a628d5820d.webp', '2025-12-19 05:30:31', '2025-12-09 11:31:48'),
(2, 'Framework Modular', 'framework-modular', 'Contoh modular routing menggunakan OOP.', 'https://www.jagatreview.com/wp-content/uploads/2021/02/Framework-Laptop.jpg', '2025-12-19 05:30:31', '2025-12-09 11:31:48'),
(3, 'Routing PHP', 'routing-php', 'Implementasi routing sederhana dengan .htaccess.', 'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b', '2025-12-19 05:30:31', '2025-12-09 11:31:48'),
(12, 'HTML', 'html', 'HTML Dasar', 'https://www.malasngoding.com/wp-content/uploads/2015/12/c.png', '2025-12-19 05:30:31', '2025-12-18 21:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `otp` int(6) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `foto`, `nama`, `email`, `no_hp`, `telepon`, `role`, `otp`, `is_active`, `gambar`) VALUES
(1, 'admin', '$2y$10$Xeo5qtFdMaUxijMlHbuqPOa8tyO8Va8C.fuzVbu4wt/6OPnbcc.mm', NULL, 'Administrator', 'admin@mail.com', '', NULL, 'admin', NULL, 1, NULL),
(3, 'user', '$2y$10$o0gLYBqdpKPLp8RgZKH5ION/IPDbugedg30ZCvRtf/TFbKKelxoJq', NULL, 'user', 'user@example.com', '', NULL, 'user', NULL, 1, NULL),
(4, 'user 2', '$2y$10$HE/zJsnvxm3TlJN0eIaKc.D7oe3OAymb2nCAKEvJYwwpqiHow/UoC', NULL, 'user 2', 'user2@example.com', NULL, NULL, 'user', NULL, 1, NULL),
(8, 'user 3', '$2y$10$HXXTnMn90yKEZ/EWFOZmCOqY38.evb5jLUhHLa8aEoHyWUrZEY2YS', NULL, 'user 3', 'user3@example.com', NULL, NULL, 'user', 141791, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
