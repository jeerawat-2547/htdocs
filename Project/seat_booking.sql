-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2025 at 08:48 PM
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
-- Database: `seat_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$a2Pv5w2x2LGNEW6UOP7lFOmMcDeleyvXbxD6Cjm7FM7x8aIsmWZ5y');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `queue_number` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `student_id`, `course_id`, `queue_number`, `status`, `created_at`, `reason`) VALUES
(1, 7, 1, 1, 'completed', '2025-07-31 17:26:15', NULL),
(2, 7, 2, 2, 'completed', '2025-07-31 18:05:31', NULL),
(3, 8, 1, 3, 'completed', '2025-07-31 18:06:58', NULL),
(4, 8, 2, 3, 'completed', '2025-07-31 18:06:58', NULL),
(5, 9, 2, 4, 'completed', '2025-07-31 18:43:16', NULL),
(6, 10, 17, 5, 'completed', '2025-07-31 18:55:29', NULL),
(7, 10, 10, 5, 'completed', '2025-07-31 18:55:29', NULL),
(8, 10, 2, 5, 'completed', '2025-07-31 18:55:29', NULL),
(9, 10, 9, 5, 'completed', '2025-07-31 18:55:29', NULL),
(10, 10, 14, 5, 'completed', '2025-07-31 18:55:29', NULL),
(11, 11, 15, 6, 'completed', '2025-07-31 19:54:21', NULL),
(12, 11, 11, 6, 'completed', '2025-07-31 19:54:21', NULL),
(13, 11, 2, 6, 'completed', '2025-07-31 19:54:21', NULL),
(14, 12, 1, 7, 'rejected', '2025-07-31 20:06:46', NULL),
(15, 17, 16, 8, 'rejected', '2025-07-31 20:48:39', NULL),
(16, 18, 14, 9, 'completed', '2025-08-04 10:25:53', NULL),
(17, 19, 14, 10, 'rejected', '2025-08-04 11:35:20', NULL),
(18, 6, 11, 11, 'pending', '2025-08-04 16:11:16', NULL),
(19, 6, 17, 11, 'pending', '2025-08-04 16:11:16', NULL),
(20, 6, 2, 11, 'pending', '2025-08-04 16:11:16', NULL),
(21, 4, 16, 12, 'completed', '2025-08-04 18:29:26', NULL),
(22, 4, 14, 12, 'completed', '2025-08-04 18:29:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `group_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `name`, `group_name`) VALUES
(1, '0905405', 'Aduanced Financial Manasment', '2'),
(2, '0905405', 'Aduanced Financial Manasment', '2'),
(3, '0105201', 'English Communication Skills 1', '1'),
(4, '0106102', 'Writing for Communication', '1'),
(5, '0106105', 'Oratory', '1'),
(6, '0106107', 'Writing for Communication', '1'),
(7, '0106202', 'Oratory', '1'),
(8, '0106203', 'Contemporary Literary Reading', '1'),
(9, '0106204', 'Aesthetics in Thai Literary Works', '1'),
(10, '0106205', 'Oratory', '1'),
(11, '0904324', 'Security Systems for E-Commerce', '1'),
(12, '0904325', 'Finance and Banking for E-Commerce', '1'),
(13, '0904326', 'E-Commerce Law', '1'),
(14, '0904411', 'Business Information Technology Project Management', '1'),
(15, '0904412', 'Advanced Business Information Systems Analysis and Design', '1'),
(16, '0904413', 'Special Topics in Business Information Technology', '1'),
(17, '0904421', 'Electronic Business Management', '1');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `queued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queue_status`
--

CREATE TABLE `queue_status` (
  `id` int(11) NOT NULL,
  `current_queue` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `bookings_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `other_reason` text DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `department_code` varchar(20) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `faculty` varchar(255) DEFAULT NULL,
  `major` varchar(255) DEFAULT NULL,
  `degree_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `bookings_id`, `reason`, `other_reason`, `gpa`, `department_code`, `status`, `created_at`, `updated_at`, `faculty`, `major`, `degree_status`) VALUES
(6, 3, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.88, 'BC651', 'pending', '2025-07-31 18:06:58', '2025-07-31 18:06:58', 'คณะการบัญชีและการจัดการ', 'International Business', 'Regular System'),
(7, 4, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.88, 'BC651', 'pending', '2025-07-31 18:06:58', '2025-07-31 18:06:58', 'คณะการบัญชีและการจัดการ', 'International Business', 'Regular System'),
(8, 5, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.55, 'AC661', 'pending', '2025-07-31 18:43:16', '2025-07-31 18:43:16', 'คณะการบัญชีและการจัดการ', 'Marketing', 'Regular System'),
(9, 6, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.25, 'BC651', 'pending', '2025-07-31 18:55:29', '2025-07-31 18:55:29', 'คณะการบัญชีและการจัดการ', 'Digital Business and Information Systems', 'Regular System'),
(10, 7, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.25, 'BC651', 'pending', '2025-07-31 18:55:29', '2025-07-31 18:55:29', 'คณะการบัญชีและการจัดการ', 'Digital Business and Information Systems', 'Regular System'),
(11, 8, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.25, 'BC651', 'pending', '2025-07-31 18:55:29', '2025-07-31 18:55:29', 'คณะการบัญชีและการจัดการ', 'Digital Business and Information Systems', 'Regular System'),
(12, 9, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.25, 'BC651', 'pending', '2025-07-31 18:55:29', '2025-07-31 18:55:29', 'คณะการบัญชีและการจัดการ', 'Digital Business and Information Systems', 'Regular System'),
(13, 10, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.25, 'BC651', 'pending', '2025-07-31 18:55:29', '2025-07-31 18:55:29', 'คณะการบัญชีและการจัดการ', 'Digital Business and Information Systems', 'Regular System'),
(14, 11, 'เคย W', '', 2.55, 'BC651', 'pending', '2025-07-31 19:54:21', '2025-07-31 19:54:21', 'คณะการบัญชีและการจัดการ', 'Marketing', 'Regular System'),
(15, 12, 'เคย W', '', 2.55, 'BC651', 'pending', '2025-07-31 19:54:21', '2025-07-31 19:54:21', 'คณะการบัญชีและการจัดการ', 'Marketing', 'Regular System'),
(16, 13, 'เคย W', '', 2.55, 'BC651', 'pending', '2025-07-31 19:54:21', '2025-07-31 19:54:21', 'คณะการบัญชีและการจัดการ', 'Marketing', 'Regular System'),
(17, 14, 'ติดเวลาเรียนซ้ำ', '', 2.55, 'BC651', 'pending', '2025-07-31 20:06:46', '2025-07-31 20:06:46', 'คณะการบัญชีและการจัดการ', 'Financial Management', 'Regular System'),
(18, 15, 'เคย W', '', 3.22, 'BC651', 'pending', '2025-07-31 20:48:39', '2025-07-31 20:48:39', 'คณะการบัญชีและการจัดการ', 'Modern Management', 'Regular System'),
(19, 16, 'ติดเวลาเรียนซ้ำ', '', 2.85, 'AC661', 'pending', '2025-08-04 10:25:53', '2025-08-04 10:25:53', 'คณะการบัญชีและการจัดการ', 'Marketing', 'Special Program Transfer'),
(20, 17, 'เก็บรายวิชาเรียนไม่ตามแผน', '', 2.88, 'AC661', 'pending', '2025-08-04 11:35:20', '2025-08-04 11:35:20', 'คณะการบัญชีและการจัดการ', 'Marketing', 'Regular System'),
(21, 18, 'ลงทะเบียนไม่ทัน', '', 2.55, 'BC651', 'pending', '2025-08-04 16:11:16', '2025-08-04 16:11:16', 'คณะการบัญชีและการจัดการ', 'Modern Entrepreneurship', 'Regular System'),
(22, 19, 'ลงทะเบียนไม่ทัน', '', 2.55, 'BC651', 'pending', '2025-08-04 16:11:16', '2025-08-04 16:11:16', 'คณะการบัญชีและการจัดการ', 'Modern Entrepreneurship', 'Regular System'),
(23, 20, 'ลงทะเบียนไม่ทัน', '', 2.55, 'BC651', 'pending', '2025-08-04 16:11:16', '2025-08-04 16:11:16', 'คณะการบัญชีและการจัดการ', 'Modern Entrepreneurship', 'Regular System'),
(24, 21, 'ลงแก้ F', '', 2.88, 'BC651', 'pending', '2025-08-04 18:29:26', '2025-08-04 18:29:26', 'คณะการบัญชีและการจัดการ', 'Business Computer Science', 'Special Program Transfer'),
(25, 22, 'ลงแก้ F', '', 2.88, 'BC651', 'pending', '2025-08-04 18:29:26', '2025-08-04 18:29:26', 'คณะการบัญชีและการจัดการ', 'Business Computer Science', 'Special Program Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

CREATE TABLE `slides` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `order_no` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `image_path`, `alt_text`, `link_url`, `order_no`, `created_at`) VALUES
(1, 'uploads/slide_6890daa494179_pngtree-full-hand-painted-watercolor-freehand-landscape-background-paintedwatercolorlandscapeartistic-conceptionsimplecountryantiquitychinese-styleartistic-image_82987.jpg', 'ผนึกเทพบัลลังก์ราชันย์', 'https://th.pikbest.com/backgrounds/qianku-blue-spring-ancient-style-bridge-long-bridge-landscape-background_1910425.html', 1, '2025-08-04 11:55:03'),
(2, 'slider/68909fd04faed_8.jpg', 'ปรมาจารย์ลัทธิมาร', 'https://www.sanook.com/movie/171679/', 2, '2025-08-04 11:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_code` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_code`, `name`, `phone`, `major`) VALUES
(1, '65010912513', 'แพร', '0623901032', 'Business Computer Science'),
(2, '65010912511', 'แพร', '0623901333', 'Financial Management'),
(3, '65010912511', 'xx', '0623901333', 'International Business'),
(4, '66012010007', 'แบมบี้', '1230425555', 'Business Computer Science'),
(5, '65010912511', 'แพร', '0986517108', 'Marketing'),
(6, '66010002100', 'เทพ', '0623901333', 'Modern Entrepreneurship'),
(7, '65010912511', 'แพร', '0986517108', 'Digital Business and Information Systems'),
(8, '65010912513', 'แบมบี้', '0986517209', 'Financial Management'),
(9, '65010912513', 'แพร', '0623901333', 'Marketing'),
(10, '65010912511', 'แพร', '0986517209', 'Modern Entrepreneurship'),
(11, '65010912523', 'แบมบี้', '1230425555', 'Marketing'),
(12, '65010912513', 'เทพภู', '1230425555', 'Financial Management'),
(13, '66010002100', 'แพร', '0623901333', 'Marketing'),
(14, '66010002100', 'แพร', '0623901333', 'Marketing'),
(15, '66010002100', 'แพร', '0623901333', 'Marketing'),
(16, '65010912513', 'เทพภู', '0623901333', 'Marketing'),
(17, '65010912513', 'เทพภู', '0623901333', 'Modern Management'),
(18, '65010912511', 'เทพภู', '0986517209', 'Marketing'),
(19, '65010912511', 'xx', '0623901333', 'Marketing');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_course_id` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue_status`
--
ALTER TABLE `queue_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_id` (`bookings_id`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queue_status`
--
ALTER TABLE `queue_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`bookings_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
