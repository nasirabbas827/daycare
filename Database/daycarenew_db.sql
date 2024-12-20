-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2024 at 11:30 AM
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
-- Database: `daycarenew_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `activity_date` date DEFAULT NULL,
  `activity_description` text DEFAULT NULL,
  `photos_videos` varchar(255) DEFAULT NULL,
  `playtime` text DEFAULT NULL,
  `learning_lesson` text DEFAULT NULL,
  `meals` text DEFAULT NULL,
  `naps` text DEFAULT NULL,
  `other_activities` text DEFAULT NULL,
  `logged_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `child_id`, `activity_date`, `activity_description`, `photos_videos`, `playtime`, `learning_lesson`, `meals`, `naps`, `other_activities`, `logged_by`) VALUES
(1, 1, '2024-12-20', 'dafd', 'uploads/201_3.png', 'dfe', 'eafd', 'fesdsdsdsd', 'efa', 'feas', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `check_in_time` datetime DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `date` date DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `child_id`, `check_in_time`, `check_out_time`, `date`, `staff_id`) VALUES
(1, 1, '2024-12-20 11:29:00', '2024-12-20 16:29:00', '2024-12-20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_notifications`
--

CREATE TABLE `attendance_notifications` (
  `notification_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `notification_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_notifications`
--

INSERT INTO `attendance_notifications` (`notification_id`, `child_id`, `message`, `notification_time`) VALUES
(1, 1, 'Attendance marked for Shahbaz  on 2024-12-20 from 2024-12-20T11:29 to 2024-12-20T16:29', '2024-12-20 11:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `child_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age_group` varchar(50) NOT NULL,
  `allergies` text DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`child_id`, `parent_id`, `name`, `date_of_birth`, `age_group`, `allergies`, `special_needs`, `created_at`, `updated_at`) VALUES
(1, 4, 'Shahbaz ', '2024-12-28', 'Preschool', 'none', 'yes', '2024-12-20 06:03:03', '2024-12-20 06:05:01');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `attendance_days` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('Paid','Unpaid') DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `child_id`, `attendance_days`, `total_amount`, `due_date`, `status`) VALUES
(2, 1, 23, 3000.00, '2024-12-20', 'Paid'),
(4, 1, 22, 2000.00, '2024-12-21', 'Paid'),
(5, 1, 22, 22.00, '2024-12-24', 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `meal_requests`
--

CREATE TABLE `meal_requests` (
  `request_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `meal_type` varchar(50) DEFAULT NULL,
  `meal_time` time DEFAULT NULL,
  `meal_details` text DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_requests`
--

INSERT INTO `meal_requests` (`request_id`, `child_id`, `meal_type`, `meal_time`, `meal_details`, `request_date`, `status`) VALUES
(1, 1, 'dase', '12:52:00', 'dfewad', '2024-12-20 12:52:32', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `meal_schedule`
--

CREATE TABLE `meal_schedule` (
  `meal_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `meal_type` enum('daily','weekly') NOT NULL,
  `meal_time` enum('Breakfast','Lunch','Snack') NOT NULL,
  `meal_details` text DEFAULT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_schedule`
--

INSERT INTO `meal_schedule` (`meal_id`, `date`, `meal_type`, `meal_time`, `meal_details`, `created_by`) VALUES
(2, '2024-12-20', 'daily', 'Breakfast', 'saddds', 1);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text DEFAULT NULL,
  `sent_datetime` datetime DEFAULT current_timestamp(),
  `reply_text` text DEFAULT NULL,
  `reply_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message_text`, `sent_datetime`, `reply_text`, `reply_datetime`) VALUES
(4, 3, 0, 'dfes', '2024-12-20 15:06:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `message`, `notification_type`, `created_at`) VALUES
(2, 'dfesd', 'Event', '2024-12-20 07:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `invoice_id`, `amount`, `payment_date`, `payment_method`, `transaction_image`) VALUES
(1, 2, 3000.00, '2024-12-20', 'Card', 'transactions/411-2.png'),
(2, 4, 2000.00, '2025-01-01', 'Online Transfer', 'transactions/504-1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(50) NOT NULL DEFAULT 'Staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `email`, `password`, `phone`, `address`, `created_at`, `updated_at`, `role`) VALUES
(1, 'NASIR ABBAS', 'nasiryt.827@gmail.com', '123', '3176526827', 'Street jeff xxxx\r\nAp04', '2024-12-20 05:54:27', '2024-12-20 07:10:56', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `age`, `full_name`, `bio`) VALUES
(3, 'nasir', '$2y$10$a/SgsGBswoo1YfNrJd./p.Jqey.MDYC.QoLsyJGas3H5cdesUPvjq', 'nasiryt.827@gmail.com', '9853', 23, '', NULL),
(4, 'parent', '$2y$10$ASNRwSWzI.GLXrol3u.JBuZccNpXfuX3okVTounFkm05hw0k.mDgG', 'parent@gmail.com', '4512', 23, '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `logged_by` (`logged_by`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `attendance_notifications`
--
ALTER TABLE `attendance_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`child_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `meal_requests`
--
ALTER TABLE `meal_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `meal_schedule`
--
ALTER TABLE `meal_schedule`
  ADD PRIMARY KEY (`meal_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance_notifications`
--
ALTER TABLE `attendance_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `child_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meal_requests`
--
ALTER TABLE `meal_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `meal_schedule`
--
ALTER TABLE `meal_schedule`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`child_id`),
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`logged_by`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`child_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `attendance_notifications`
--
ALTER TABLE `attendance_notifications`
  ADD CONSTRAINT `attendance_notifications_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`child_id`);

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`child_id`);

--
-- Constraints for table `meal_requests`
--
ALTER TABLE `meal_requests`
  ADD CONSTRAINT `meal_requests_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`child_id`);

--
-- Constraints for table `meal_schedule`
--
ALTER TABLE `meal_schedule`
  ADD CONSTRAINT `meal_schedule_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
