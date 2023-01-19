-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2016 at 05:19 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eproc`
--

-- --------------------------------------------------------

--
-- Table structure for table `ms_login`
--

CREATE TABLE `ms_login` (
  `id` int(11) NOT NULL,
  `id_user` bigint(20) DEFAULT NULL COMMENT 'FK for id in ms_vendor if type = user, if type = admin then FK for ms_admin',
  `type` varchar(6) DEFAULT NULL COMMENT 'type of user, admin or user',
  `username` varchar(60) DEFAULT NULL COMMENT 'username for login',
  `password` varchar(60) DEFAULT NULL COMMENT 'password for login',
  `entry_stamp` timestamp NULL DEFAULT NULL COMMENT 'data entry timestamp',
  `edit_stamp` timestamp NULL DEFAULT NULL COMMENT 'data edit timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ms_login`
--

INSERT INTO `ms_login` (`id`, `id_user`, `type`, `username`, `password`, `entry_stamp`, `edit_stamp`) VALUES
(1, 1, 'user', 'amathul.basyith@gmail.com', '1234', '2015-10-29 04:44:47', '2015-12-13 15:40:03'),
(2, 2, 'user', 'praditoo@rocketmail.com', '123', '2015-10-29 04:50:20', '2015-11-12 10:35:27'),
(3, 12, 'admin', 'admin1', 'admin1', NULL, NULL),
(4, 14, 'admin', 'log', 'log', NULL, NULL),
(5, 20, 'admin', 'hse', 'hse', NULL, NULL),
(6, 16, 'admin', 'user', 'user', NULL, NULL),
(7, 21, 'admin', 'spv', 'spv', NULL, NULL),
(8, 19, 'admin', 'auction', 'auction', NULL, NULL),
(9, 4, 'user', 'asd@gmail.com', 'gxfkTwVOqK', '2015-10-30 00:27:59', NULL),
(10, 5, 'user', 'nurhanafiyah@gmail.com', '123', '2015-10-30 08:03:02', NULL),
(11, 6, 'user', 'muarifgustiar1@gmail.com', 'MBDGTqSGNQ', '2015-11-02 17:21:06', NULL),
(12, 22, 'admin', 'test', 'test', '2015-11-03 17:02:55', NULL),
(13, 7, 'user', 'bidadari@heaven.com', 'f9lPAntqCb', '2015-11-04 09:19:37', NULL),
(14, 8, 'user', 'muar@gmail.com', '123', '2015-11-04 15:09:28', NULL),
(15, 9, 'user', 'fadlimp@gmail.com', '123', '2015-11-05 03:41:21', '2015-12-08 09:46:49'),
(16, 10, 'user', 'contoh@gmail.com', 'CezRbm3hX', '2015-11-12 09:06:55', NULL),
(18, 12, 'user', 'admin1', 'admin1', '2015-12-01 00:04:46', NULL),
(19, 23, 'admin', 'testing alicia dekodrin2', '1232', '2015-12-01 22:54:05', NULL),
(20, 13, 'user', 'muarifgg@gmail.com', 'PuERctee46', '2015-12-03 21:43:26', NULL),
(21, 14, 'user', 'log', 'log', '2015-12-04 23:34:04', NULL),
(22, 15, 'user', 'fasdasdasd@gmail.com', 'qsSMhWmtWe', '2015-12-04 23:42:37', '2015-12-05 00:00:10'),
(23, 16, 'user', 'dekodr', 'dekodr', '2015-12-07 23:28:22', '2015-12-08 03:22:06'),
(24, 18, 'user', 'muarifgustiar@gmail.com', 'KYuZgOX29A', '2015-12-07 23:35:55', NULL),
(25, 19, 'user', 'auction', 'auction', '2015-12-07 23:50:07', NULL),
(26, 20, 'user', 'hse', 'hse', '2015-12-08 05:16:16', NULL),
(27, 21, 'user', 'spv', 'spv', '2015-12-08 05:22:44', NULL),
(28, 24, 'admin', 'manual buk', '123', '2015-12-10 05:55:07', NULL),
(29, 24, 'user', 'muarif123@gmail.com', 'YE6QtT01K2', '2015-12-10 10:15:57', NULL),
(31, 26, 'user', 'Hanafi', 'hanafi', '2015-12-15 08:40:55', NULL),
(32, 27, 'user', 'alexandro.putra@gmail.com', 'jVt7ID0Jp', '2015-12-15 15:38:24', NULL),
(33, 25, 'admin', 'usersdm', 'SDM', '2015-12-16 22:19:13', NULL),
(34, 28, 'user', 'gelentinowkwk@gmail.com', 'LCSopKb8sT', '2015-12-17 00:02:42', NULL),
(35, 32, 'user', 'metrodata@gmail.com', 'iVN6VA7wd', '2015-12-17 06:04:20', NULL),
(36, 33, 'user', 'muar@b.com', '123', '2015-12-17 06:08:00', NULL),
(37, 26, 'admin', 'Hanafi', 'hanafi', '2015-12-17 15:36:09', NULL),
(38, 27, 'admin', 'pandu', 'pandu', '2015-12-17 15:41:50', NULL),
(39, 28, 'admin', 'hanafi', 'hanafi', '2015-12-17 15:43:48', NULL),
(40, 29, 'admin', 'userlogistik', 'userlogistik', '2015-12-17 16:00:37', NULL),
(41, 30, 'admin', 'user layum', 'layum', '2015-12-17 20:10:07', NULL),
(42, 31, 'admin', 'userlogistik', 'logistik', '2015-12-17 20:13:12', NULL),
(43, 34, 'user', 'nurhanafiyah@gmail.com', 'pa4JXTjRoU', '2016-01-14 15:39:44', '2016-01-19 16:09:39'),
(44, 35, 'user', 'cdf@gmail.com', 'coba', '2016-01-19 16:13:59', NULL),
(45, 36, 'user', 'userkomersial', 'komersial', '2016-02-26 08:56:23', NULL),
(46, 32, 'admin', 'userlayum', 'layum', '2016-02-29 00:36:54', NULL),
(47, 33, 'admin', 'userpemeliharaan', 'pemeliharaan', '2016-02-29 00:38:23', NULL),
(48, 34, 'admin', 'userenjiniring', 'enjiniring', '2016-02-29 00:39:08', NULL),
(49, 35, 'admin', 'usersekper', 'sekper', '2016-02-29 00:40:40', NULL),
(50, 36, 'admin', 'userkomersial', 'komersial', '2016-02-29 00:44:37', NULL),
(51, 37, 'admin', 'useroperasi', 'operasi', '2016-02-29 00:47:09', NULL),
(52, 38, 'admin', 'userkeuangan', 'keuangan', '2016-02-29 00:48:38', NULL),
(53, 39, 'admin', 'userqhsse', 'qhsse', '2016-02-29 00:51:48', NULL),
(54, 40, 'admin', 'user', 'user', '2016-02-29 04:19:18', NULL),
(56, 38, 'user', 'nusantara.regas.test@gmail.com', 'FMWn2S5ejA', '2016-02-29 06:07:02', NULL),
(57, 39, 'user', 'hanafi.tirtayogci@yahoo.com', 'eiX0k6Qz3R', '2016-10-17 03:17:10', NULL),
(58, 40, 'user', 'logistik@nusantararegas.com', 'n3bJgbq0Z9', '2016-10-17 03:21:19', NULL),
(59, 41, 'user', 'nusan@co.com', 'j6Tk4sS3gf', '2016-10-17 04:12:10', NULL),
(60, 42, 'user', 'n@f.com', 'xROJVxBlP', '2016-11-10 09:08:24', NULL),
(61, 43, 'user', 'nh@j.com', 'vXKV7g7uVS', '2016-11-10 10:06:11', NULL),
(63, 45, 'user', 'muarifgustiar@gmail.com', 'GsbBatwbO', '2016-11-27 15:48:47', NULL),
(64, 46, 'user', 'pratama_fadli16@yahoo.com', 'oUH1te5TtU', '2016-11-28 19:39:46', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ms_login`
--
ALTER TABLE `ms_login`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ms_login`
--
ALTER TABLE `ms_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
