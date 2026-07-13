-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2023 at 02:12 PM
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
-- Database: `event_mgt`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_booking`
--

CREATE TABLE `tbl_booking` (
  `b_id` int(4) NOT NULL,
  `b_uid` int(4) NOT NULL,
  `b_type` varchar(20) NOT NULL,
  `b_package` varchar(20) NOT NULL,
  `b_date` date NOT NULL,
  `b_venue` varchar(50) NOT NULL,
  `b_price` double NOT NULL,
  `b_msg` varchar(500) NOT NULL,
  `b_state` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_booking`
--

INSERT INTO `tbl_booking` (`b_id`, `b_uid`, `b_type`, `b_package`, `b_date`, `b_venue`, `b_price`, `b_msg`, `b_state`) VALUES
(18, 9, 'BIRTHDAY', 'SILVER', '2023-12-14', 'the basil park', 5000, 'user1', 'Finished'),
(19, 9, 'WEDDING', 'SILVER', '2023-12-23', 'sarovar portico', 200000, 'hello', 'Upcoming'),
(20, 9, 'BIRTHDAY', 'SILVER', '2023-12-15', 'nilambag palace', 5000, 'hello', 'Cancelled'),
(26, 9, 'WEDDING', 'GOLD', '2023-12-21', 'the basil park', 500000, 'hello', 'Upcoming'),
(27, 10, 'WEDDING', 'GOLD', '2023-12-28', 'sarovar portico', 500000, 'trujykhj', 'Upcoming');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `First_Name` char(15) NOT NULL,
  `Last_Name` char(15) NOT NULL,
  `Email_ID` varchar(50) NOT NULL,
  `MobileNo` char(10) NOT NULL,
  `PWD` varchar(15) NOT NULL,
  `ID` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`First_Name`, `Last_Name`, `Email_ID`, `MobileNo`, `PWD`, `ID`) VALUES
('Admin', 'Administrator', 'admin@gmail.com', '9898989898', 'admin', 1),
('user1', 'user', 'user1@gmail.com', '8978897889', 'user1', 9),
('rakesh', 'patel', 'rachitagandhi870@gmail.com', '7383060497', '123456', 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_booking`
--
ALTER TABLE `tbl_booking`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_booking`
--
ALTER TABLE `tbl_booking`
  MODIFY `b_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
