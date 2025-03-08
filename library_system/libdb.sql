-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 02:22 PM
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
  `time_out` varchar(255) DEFAULT NULL,
  `log_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lib_logs`
--

INSERT INTO `lib_logs` (`log_id`, `user_schoolId`, `time_in`, `time_out`, `log_date`) VALUES
(14, 'Jane Smith', '06:07:58', NULL, ''),
(18, '02-2122-020202', '06:10:50', NULL, ''),
(21, '02-2122-020202', '21:03:22', NULL, ''),
(22, '02-2122-020202', '21:03:27', NULL, ''),
(23, '02-2122-020202', '21:08:46', NULL, ''),
(24, '02-2122-020202', '21:09:42', NULL, ''),
(25, '02-2122-020202', '21:09:47', NULL, '');

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
