-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 05:52 PM
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
-- Database: `user_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `equipment` text NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_timeslots`
--

CREATE TABLE `room_timeslots` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_booked` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `profile_picture`, `first_name`, `last_name`) VALUES
(1, '20198028@stu.uob.edu.bh', '$2y$10$LKK2zccKXvbTRIfJ3663Ve5tKzYzdRfbSI7IjkzkYm9lF.jHklZJ6', 'uploads/GJv49FTNLGeKMT5aVMidSdsnXyu4KyQ8w4wrYoc8_nBfitgROIDlSSFzEROlDEo_IrSMh6a3FynN72pOj6zj19LwZo6z90dqoGAO=e365-pa-nu-s0.webp', '', ''),
(2, '20201234@stu.uob.edu.bh', '$2y$10$huDr7RudUb5co0BgyXuXIOEVYp1z7x2IPCFrcfilGnrfOk5zsy4ru', NULL, '', ''),
(3, '20198027@stu.uob.edu.bh', '$2y$10$YBvVSyABOa069FXXJeLvMuxJum43BPIvEfrbaCo4XGrrreeJmnkyy', NULL, 'muhanna', 'jamal'),
(4, '20198021@stu.uob.edu.bh', '$2y$10$4.a3A0laDpp5PGwHwiQe6.1SWxzvH2eqnjLiUQgOv0rdeZ0fOoFhO', NULL, 'muhanna', 'jamal'),
(5, '12345678@stu.uob.edu.bh', '$2y$10$M.xA71l3SXbijHJyz2/6de9l38FLVPwdkc92oYtfSNOFJIt9y.m1G', NULL, 'muhanna', 'ahmed'),
(6, '87654321@stu.uob.edu.bh', '$2y$10$2ognlAZ3SgsxjCvUXR6ASeUc.Xmx2dZVY520CfSnYzALrZ/RPBlMi', NULL, 'muhanna', 'noor'),
(7, '11114444@stu.uob.edu.bh', '$2y$10$kcg2tzOsCyJJbuE7tsK1feH7tOWoQWBUxz/ojACqVJpdjmrb/qSsq', 'uploads/Screenshot 2024-11-30 153318.png', 'muhanna', 'ahmed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_timeslots`
--
ALTER TABLE `room_timeslots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eamil` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_timeslots`
--
ALTER TABLE `room_timeslots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `room_timeslots`
--
ALTER TABLE `room_timeslots`
  ADD CONSTRAINT `room_timeslots_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
