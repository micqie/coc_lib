-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 04:15 AM
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
-- Database: `libdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `lib_logs`
--

CREATE TABLE `lib_logs` (
  `log_id` int(11) NOT NULL,
  `user_schoolId` varchar(255) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `log_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lib_logs`
--

INSERT INTO `lib_logs` (`log_id`, `user_schoolId`, `time_in`, `time_out`, `log_date`) VALUES
(14, 'Jane Smith', '06:07:58', NULL, '0000-00-00'),
(18, '02-2122-020202', '06:10:50', NULL, '0000-00-00'),
(21, '02-2122-020202', '21:03:22', NULL, '0000-00-00'),
(22, '02-2122-020202', '21:03:27', NULL, '0000-00-00'),
(23, '02-2122-020202', '21:08:46', NULL, '0000-00-00'),
(24, '02-2122-020202', '21:09:42', NULL, '0000-00-00'),
(25, '02-2122-020202', '21:09:47', NULL, '0000-00-00'),
(38, 'cooldown test', '01:38:39', '01:39:47', '2025-03-11'),
(39, 'cooldown test', '01:43:18', '01:44:23', '2025-03-11'),
(40, 'cooldown test', '01:46:35', '01:49:43', '2025-03-11'),
(41, 'user', '01:49:32', '01:50:44', '2025-03-11'),
(42, 'user', '01:50:49', '01:56:43', '2025-03-11'),
(46, 'latest', '11:09:59', '11:11:11', '2025-03-11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lib_logs`
--
ALTER TABLE `lib_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lib_logs`
--
ALTER TABLE `lib_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
