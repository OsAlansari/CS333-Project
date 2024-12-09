-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 07, 2024 at 04:40 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_management`
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
  `room_type` enum('ClassRoom','Lab') NOT NULL,
  `location` enum('IS','CS','CE','OpenLab') NOT NULL,
  `capacity` int(11) NOT NULL,
  `equipment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Rooms`
--

INSERT INTO `Rooms` (`room_id`, `room_name`, `room_type`, `location`, `capacity`, `equipment`) VALUES
(1, 'S40-021', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(2, 'S40-2088', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(3, 'S40-032', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(4, 'S40-023', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, iMac for attendees'),
(5, 'S40-030', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(6, 'S40-028', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(7, 'S40-029', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(8, 'S40-1006', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(9, 'S40-1008', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(10, 'S40-1010', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(11, 'S40-1011', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(12, 'S40-1012', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(13, 'S40-1002', 'Lab', 'OpenLab', 100, 'Projector, PC for presenter, PC for attendees'),
(14, 'S40-2001', 'Lab', 'OpenLab', 75, 'Projector, PC for presenter, PC for attendees'),
(15, 'S40-2015', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(16, 'S40-2005', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(17, 'S40-2013', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(18, 'S40-2012', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(19, 'S40-2008', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(20, 'S40-2010', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(21, 'S40-2011', 'ClassRoom', 'IS', 50, 'Projector, PC for presenter, Whiteboard'),
(22, 'S40-2015', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(23, 'S40-1014', 'Lab', 'IS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(24, 'S40-049', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(25, 'S40-051', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(26, 'S40-060', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(27, 'S40-058', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(28, 'S40-056', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(29, 'S40-057', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(30, 'S40-1043', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(31, 'S40-10052', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(32, 'S40-1045', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(33, 'S40-1050', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(34, 'S40-1047', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(35, 'S40-1048', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(36, 'S40-2043', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(37, 'S40-2053', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(38, 'S40-2045', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(39, 'S40-2051', 'Lab', 'CS', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(40, 'S40-2046', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(41, 'S40-2050', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(42, 'S40-2048', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(43, 'S40-2049', 'ClassRoom', 'CS', 50, 'Projector, PC for presenter, Whiteboard'),
(44, 'S40-077', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(45, 'S40-079', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(46, 'S40-088', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(47, 'S40-086', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(48, 'S40-084', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(49, 'S40-085', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(50, 'S40-1081', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(51, 'S40-1089', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(52, 'S40-1083', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(53, 'S40-1087', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(54, 'S40-1086', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(55, 'S40-1085', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(56, 'S40-2081', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(57, 'S40-2089', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(58, 'S40-2083', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(59, 'S40-2091', 'Lab', 'CE', 30, 'Projector, PC for presenter, Whiteboard, PC for attendees'),
(60, 'S40-2084', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(61, 'S40-2086', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard'),
(62, 'S40-2087', 'ClassRoom', 'CE', 50, 'Projector, PC for presenter, Whiteboard');

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
  `user_type` enum('Student','Staff','Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `profile_picture`, `first_name`, `last_name`, `user_type`) VALUES
(0, 'Admin', '$2y$10$zTewDPwnjeSkxK6kcxJ/1OWR2Ko3faqIBM4nOHCsC3/YA.gm6v8mm', '../Css/Admin_Icon.png', 'Admin', 'Admin', 'Admin'),
(1, '20194952@stu.uob.edu.bh', '$2y$10$csoJLLH2gfbUtqZF.U8m5u04cLpBUptWAeS5V5U/1QzoV.fOWG1Jm', NULL, 'Mohammed', 'Hussain', 'Student'),
(2, 'Mohammed@uob.edu.bh', '$2y$10$oUszDjv.iohamTXkZi5np.GuC9uRkkcdHUEBnf991E/Y/APg98y66', NULL, 'Mohammed', 'Hussain', 'Staff'),
(3, '20198028@stu.uob.edu.bh', '$2y$10$eGk/IQdLWZc6wOv/0Bx7N.ehBFNsV3QqfTz.b7FH5kjpjecs/Eh6K', NULL, 'Muhanna', 'Ahmed', 'Student'),
(4, 'Muhanna@uob.edu.bh', '$2y$10$lnmDUgQ.fAuXPz/cWlNuyespbmDO5SoOOKrScDQnbjhHq01qtV7Im', NULL, 'Muhanna', 'Ahmed', 'Staff'),
(5, '20200567@stu.uob.edu.bh', '$2y$10$V6PSxmiC7LEECabEhZNgLeqwjO0Y27a13NnaSefxj/o7G1kSu6MOG', NULL, 'Zainab', 'Abdali', 'Student'),
(6, 'Zainab@uob.edu.bh', '$2y$10$Vkrk/KGCdZs9Z2r9KDQFquJSCaKL.BEWjIguZ1miNsgAnLJC2CPbe', NULL, 'Zainab', 'Abdali', 'Staff'),
(7, '202103778@stu.uob.edu.bh', '$2y$10$tIWIJakjfCOPXUgihSQzOey6VQUdPL39Xkwv8K0JiSyBBzCWz43NG', NULL, 'Osama', 'Alansari', 'Student'),
(8, 'Osama@uob.edu.bh', '$2y$10$iE3a9zRichTzjJgeiclRROspO99lIvKx5ALlzieuxjmHTdVyw1A8u', NULL, 'Osama', 'Alansari', 'Staff'),
(9, '20192186@stu.uob.edu.bh', '$2y$10$Mtn7qU6FyzuZr7jO5Vf3ie4/cdpOrU0s.pKG1m7j505p0/j2wPVuq', NULL, 'Omar', 'Hany', 'Student'),
(10, 'Omar@uob.edu.bh', '$2y$10$iZBg7ijpo1M0WVlqQJm1QOFPlsuXSTuSEzyruBln/SRjnbhhrrv/6', NULL, 'Omar', 'Hany', 'Staff');

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `Rooms`
--
ALTER TABLE `Rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
