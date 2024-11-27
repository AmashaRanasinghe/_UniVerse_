-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2024 at 07:04 PM
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
-- Database: `universe`
--

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

CREATE TABLE `badge` (
  `badge_id` int(11) NOT NULL,
  `badge_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE `collection` (
  `collection_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `notes` int(11) DEFAULT NULL,
  `videos` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `content_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `content_type` enum('Note','Video','Post') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `status` enum('Pending Approval','Approved','Rejected') NOT NULL DEFAULT 'Pending Approval',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`content_id`, `student_id`, `content_type`, `title`, `description`, `file_url`, `status`, `created_at`, `updated_at`) VALUES
(6, 4, 'Post', 'a', 'a', '6741f75556c229.75306595.PNG', 'Pending Approval', '2024-11-23 15:40:05', '2024-11-23 15:40:05'),
(7, 4, 'Post', 'What is this?', 'What is this?', '6741fe84b6da94.67266147.PNG', 'Pending Approval', '2024-11-23 16:10:44', '2024-11-23 16:10:44'),
(8, 4, 'Note', 'java', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '../../uploads/6743299f28455.pdf', 'Pending Approval', '2024-11-24 13:26:55', '2024-11-24 13:26:55');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_area` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`mentor_id`, `student_id`, `subject_area`, `status`, `price`) VALUES
(1, 4, 'html ', 'Inactive', 1000.00),
(2, 4, 'front-end development', 'Inactive', 5000.00),
(3, 4, 'Back end development', 'Active', 12000.00),
(4, 5, 'Combined Maths Advanced', 'Active', 6000.00),
(5, 5, 'Combined Maths Advanced', 'Inactive', 6000.00),
(6, 5, 'Physics', 'Active', 500.00),
(7, 5, 'Chemistry Advanced', 'Inactive', 9000.00);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `report_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Under Review','Resolved') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secondary_administrator`
--

CREATE TABLE `secondary_administrator` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_type` enum('Content Approver','Content Moderator') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `year_of_study` int(11) DEFAULT NULL,
  `degree_program` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `badges` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `user_id`, `university`, `year_of_study`, `degree_program`, `bio`, `profile_picture`, `badges`) VALUES
(1, 1, 'ICBT', 2, 'Data Science', 'Hey there!', '../uploads/_332f854c-410b-4ab8-94f1-d692b37a7549.jpeg', NULL),
(2, 2, 'Esoft', 2, 'Information Technology', '-', '../uploads/_71d65e9d-2550-4e38-ac84-f823cd0b54a9.jpeg', NULL),
(3, 4, 'SLIIT', 3, 'Information Technology', '-', '../uploads/_4f6c76c6-03e7-48b7-a332-e426917b26c6.jpeg', NULL),
(4, 6, 'ICBT', 2, 'Software Engineer', 'HND Under graduate', '../uploads/my pic.PNG', NULL),
(5, 7, 'University of Sri Jayawardhanapura', 2, 'Applied Science', 'Physical under graduate', '../uploads/wp1929442.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `study_group`
--

CREATE TABLE `study_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `fields_of_interest` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`members`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Student','Primary Administrator','Secondary Administrator') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `created_at`, `updated_at`, `firstname`, `lastname`) VALUES
(1, 'ama@gmail.com', '$2y$10$CnWYKUbt0U.oGTflhuCwsOl2tJDjVmZDBPR/jAb9n4LXWWFm.AlRW', 'Student', '2024-11-23 09:33:08', '2024-11-23 09:33:08', 'Amasha', 'Ranasinghe'),
(2, 'hunter@gmail.com', '$2y$10$RB8RazUX6ShLjqiPy/HZhuGAMedJl./r3c6Ogte0fX/D14ZtlMuCG', 'Student', '2024-11-23 09:34:32', '2024-11-23 09:34:32', 'Hunter', 'Fernando'),
(3, 'admin1@gmail.com', 'admin1@123', 'Primary Administrator', '2024-11-23 09:35:13', '2024-11-23 09:35:13', 'Ash', 'Shey'),
(4, 'rowdy@gmail.com', '$2y$10$nV9veZnao67AHerOcXBsduXd5qlYm72qn7TYTyjut3NuQ9tt8rwU2', 'Student', '2024-11-23 09:36:19', '2024-11-23 09:36:19', 'Rowdy', 'Jayawardhane'),
(5, 'admin2@gmail.com', 'admin2@123', 'Primary Administrator', '2024-11-23 09:36:56', '2024-11-23 09:36:56', 'Shane', 'Saff'),
(6, 'rishithagcrlf@gmail.com', '$2y$10$tP.CoL.pyUWPYJ5669c3l.rt5mysY2ZZhsA0GzdTxMT8QqO0kmt/q', 'Student', '2024-11-23 10:24:25', '2024-11-23 10:24:25', 'Rishii', 'Fernando'),
(7, 'chanu@gmail.com', '$2y$10$MA0es7CzQ7APZ48o2GhBqeGNB85iZSqdHVOYgnd0AQ1v2lmz.nZte', 'Student', '2024-11-24 15:46:52', '2024-11-24 15:46:52', 'Chanu', 'Perera');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badge`
--
ALTER TABLE `badge`
  ADD PRIMARY KEY (`badge_id`);

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`collection_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `notes` (`notes`),
  ADD KEY `videos` (`videos`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`mentor_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `secondary_administrator`
--
ALTER TABLE `secondary_administrator`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `badges` (`badges`);

--
-- Indexes for table `study_group`
--
ALTER TABLE `study_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badge`
--
ALTER TABLE `badge`
  MODIFY `badge_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `mentor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `secondary_administrator`
--
ALTER TABLE `secondary_administrator`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `study_group`
--
ALTER TABLE `study_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `collection`
--
ALTER TABLE `collection`
  ADD CONSTRAINT `collection_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collection_ibfk_2` FOREIGN KEY (`notes`) REFERENCES `content` (`content_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collection_ibfk_3` FOREIGN KEY (`videos`) REFERENCES `content` (`content_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`content_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`content_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `mentors`
--
ALTER TABLE `mentors`
  ADD CONSTRAINT `mentors_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`content_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `secondary_administrator`
--
ALTER TABLE `secondary_administrator`
  ADD CONSTRAINT `secondary_administrator_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`badges`) REFERENCES `badge` (`badge_id`) ON DELETE CASCADE;

--
-- Constraints for table `study_group`
--
ALTER TABLE `study_group`
  ADD CONSTRAINT `study_group_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
