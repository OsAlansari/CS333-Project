-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 03, 2024 at 03:22 PM
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
-- Database: `ProjectDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Bookings`
--

CREATE TABLE `Bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `Bookings`
--
DELIMITER $$
CREATE TRIGGER `check_booking_time_before_insert` BEFORE INSERT ON `Bookings` FOR EACH ROW BEGIN
    IF NEW.start_time >= NEW.end_time THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'start_time must be less than end_time';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_booking_time_before_update` BEFORE UPDATE ON `Bookings` FOR EACH ROW BEGIN
    IF NEW.start_time >= NEW.end_time THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'start_time must be less than end_time';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Rooms`
--

CREATE TABLE `Rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `room_type` enum('ClassRoom','MeetingRomm','Lab') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Rooms`
--

INSERT INTO `Rooms` (`room_id`, `room_name`, `room_type`) VALUES
(1, 'S40-021', 'Lab'),
(2, 'S40-2088', 'ClassRoom'),
(3, 'S40-032', 'Lab'),
(4, 'S40-023', 'Lab'),
(5, 'S40-030', 'Lab'),
(6, 'S40-028', 'ClassRoom'),
(7, 'S40-029', 'ClassRoom'),
(8, 'S40-1006', 'Lab'),
(9, 'S40-1008', 'Lab'),
(10, 'S40-1010', 'ClassRoom'),
(11, 'S40-1011', 'ClassRoom'),
(12, 'S40-1012', 'Lab'),
(13, 'S40-1002', 'ClassRoom'),
(14, 'S40-2001', 'ClassRoom'),
(15, 'S40-2015', 'Lab'),
(16, 'S40-2005', 'Lab'),
(17, 'S40-1013', 'Lab'),
(18, 'S40-2012', 'ClassRoom'),
(19, 'S40-2008', 'ClassRoom'),
(20, 'S40-2010', 'ClassRoom'),
(21, 'S40-2011', 'ClassRoom'),
(22, 'S40-2015', 'Lab'),
(23, 'S40-1014', 'Lab'),
(24, 'S40-049', 'Lab'),
(25, 'S40-051', 'Lab'),
(26, 'S40-060', 'Lab'),
(27, 'S40-058', 'Lab'),
(28, 'S40-056', 'ClassRoom'),
(29, 'S40-057', 'ClassRoom'),
(30, 'S40-1043', 'Lab'),
(31, 'S40-10052', 'Lab'),
(32, 'S40-1045', 'Lab'),
(33, 'S40-1050', 'Lab'),
(34, 'S40-1047', 'ClassRoom'),
(35, 'S40-1048', 'ClassRoom'),
(36, 'S40-2043', 'Lab'),
(37, 'S40-2053', 'Lab'),
(38, 'S40-2045', 'Lab'),
(39, 'S40-2051', 'Lab'),
(40, 'S40-2046', 'ClassRoom'),
(41, 'S40-2050', 'ClassRoom'),
(42, 'S40-2048', 'ClassRoom'),
(43, 'S40-2049', 'ClassRoom'),
(44, 'S40-077', 'Lab'),
(45, 'S40-079', 'Lab'),
(46, 'S40-088', 'Lab'),
(47, 'S40-086', 'Lab'),
(48, 'S40-084', 'ClassRoom'),
(49, 'S40-085', 'ClassRoom'),
(50, 'S40-1081', 'Lab'),
(51, 'S40-1089', 'Lab'),
(52, 'S40-1083', 'Lab'),
(53, 'S40-1087', 'Lab'),
(54, 'S40-1086', 'ClassRoom'),
(55, 'S40-1085', 'ClassRoom'),
(56, 'S40-2081', 'Lab'),
(57, 'S40-2089', 'Lab'),
(58, 'S40-2083', 'Lab'),
(59, 'S40-2091', 'Lab'),
(60, 'S40-2084', 'ClassRoom'),
(61, 'S40-2086', 'ClassRoom'),
(62, 'S40-2087', 'ClassRoom');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `user_type` ENUM('Student', 'Staff', 'Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `profile_picture`, `first_name`, `last_name`, `user_type`) VALUES
(0, 'Admin', '$2y$10$zTewDPwnjeSkxK6kcxJ/1OWR2Ko3faqIBM4nOHCsC3/YA.gm6v8mm', NULL, 'Admin', 'Admin', 'Admin'),
(1, '20198028@stu.uob.edu.bh', '$2y$10$LKK2zccKXvbTRIfJ3663Ve5tKzYzdRfbSI7IjkzkYm9lF.jHklZJ6', 'uploads/GJv49FTNLGeKMT5aVMidSdsnXyu4KyQ8w4wrYoc8_nBfitgROIDlSSFzEROlDEo_IrSMh6a3FynN72pOj6zj19LwZo6z90dqoGAO=e365-pa-nu-s0.webp', '', '', 'Student'),
(2, '20201234@stu.uob.edu.bh', '$2y$10$huDr7RudUb5co0BgyXuXIOEVYp1z7x2IPCFrcfilGnrfOk5zsy4ru', NULL, '', '', 'Student'),
(3, '20198027@stu.uob.edu.bh', '$2y$10$YBvVSyABOa069FXXJeLvMuxJum43BPIvEfrbaCo4XGrrreeJmnkyy', NULL, 'muhanna', 'jamal', 'Student'),
(4, '20198021@stu.uob.edu.bh', '$2y$10$4.a3A0laDpp5PGwHwiQe6.1SWxzvH2eqnjLiUQgOv0rdeZ0fOoFhO', NULL, 'muhanna', 'jamal', 'Student'),
(5, '12345678@stu.uob.edu.bh', '$2y$10$M.xA71l3SXbijHJyz2/6de9l38FLVPwdkc92oYtfSNOFJIt9y.m1G', NULL, 'muhanna', 'ahmed', 'Student'),
(6, '87654321@stu.uob.edu.bh', '$2y$10$2ognlAZ3SgsxjCvUXR6ASeUc.Xmx2dZVY520CfSnYzALrZ/RPBlMi', NULL, 'muhanna', 'noor', 'Student'),
(7, '11114444@stu.uob.edu.bh', '$2y$10$kcg2tzOsCyJJbuE7tsK1feH7tOWoQWBUxz/ojACqVJpdjmrb/qSsq', 'uploads/Screenshot 2024-11-30 153318.png', 'muhanna', 'ahmed', 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `Rooms`
--
ALTER TABLE `Rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Bookings`
--
ALTER TABLE `Bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Rooms`
--
ALTER TABLE `Rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `Rooms` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
