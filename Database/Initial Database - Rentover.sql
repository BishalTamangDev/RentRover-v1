-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2024 at 12:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentrover`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(10) NOT NULL,
  `middle_name` varchar(10) NOT NULL,
  `last_name` varchar(10) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `register_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `announcement_id` int(11) NOT NULL,
  `whose` varchar(9) NOT NULL DEFAULT 'admin',
  `target` int(11) NOT NULL DEFAULT 0,
  `landlord_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 0,
  `house_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `announcement` varchar(255) NOT NULL,
  `announcement_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_response`
--

CREATE TABLE `announcement_response` (
  `announcement_response_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(9) NOT NULL,
  `response` varchar(255) NOT NULL,
  `acknowledge` int(11) NOT NULL DEFAULT 0,
  `announcement_response_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `application_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `rent_type` varchar(9) NOT NULL DEFAULT '',
  `move_in_date` date NOT NULL,
  `move_out_date` date DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT 0,
  `cancel_count` int(11) NOT NULL DEFAULT 0,
  `apply_count` int(11) NOT NULL DEFAULT 0,
  `application_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_room`
--

CREATE TABLE `custom_room` (
  `custom_room_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `district` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `room_type` int(11) NOT NULL,
  `min_rent` int(11) NOT NULL,
  `max_rent` int(11) NOT NULL,
  `furnishing` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `state` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback_data` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `response_data` varchar(255) DEFAULT NULL,
  `is_responded` tinyint(1) NOT NULL DEFAULT 0,
  `feedback_date` datetime NOT NULL,
  `response_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `house`
--

CREATE TABLE `house` (
  `house_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `house_identity` varchar(100) DEFAULT NULL,
  `district` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `location_coordinate` varchar(150) NOT NULL DEFAULT '0',
  `all_amenities` varchar(255) NOT NULL,
  `general_requirement` text NOT NULL,
  `house_state` int(1) NOT NULL DEFAULT 0,
  `register_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `house_photo`
--

CREATE TABLE `house_photo` (
  `house_photo_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `house_photo` varchar(255) NOT NULL DEFAULT 'blank.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_application`
--

CREATE TABLE `leave_application` (
  `leave_application_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `leave_date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT 0,
  `application_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `whose` varchar(9) NOT NULL,
  `type` varchar(30) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 0,
  `landlord_id` int(11) NOT NULL DEFAULT 0,
  `house_id` int(11) NOT NULL DEFAULT 0,
  `room_id` int(11) NOT NULL DEFAULT 0,
  `feedback_id` int(11) NOT NULL DEFAULT 0,
  `announcement_id` int(11) NOT NULL DEFAULT 0,
  `announcement_response_id` int(11) NOT NULL DEFAULT 0,
  `review_id` int(11) NOT NULL DEFAULT 0,
  `application_id` int(11) DEFAULT 0,
  `leave_application_id` int(11) NOT NULL DEFAULT 0,
  `tenant_voice_id` int(11) NOT NULL DEFAULT 0,
  `tenant_voice_response_id` int(11) NOT NULL DEFAULT 0,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `rent_amount` float NOT NULL,
  `room_type` int(11) NOT NULL,
  `furnishing` int(11) NOT NULL DEFAULT 0,
  `bhk` int(11) NOT NULL,
  `number_of_room` int(11) NOT NULL DEFAULT 0,
  `floor` int(11) NOT NULL DEFAULT 0,
  `amenities` varchar(255) NOT NULL,
  `requirement` varchar(255) DEFAULT NULL,
  `is_acquired` tinyint(1) NOT NULL DEFAULT 0,
  `tenant_id` int(11) NOT NULL,
  `room_state` int(11) NOT NULL DEFAULT 0,
  `register_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_photo`
--

CREATE TABLE `room_photo` (
  `room_photo_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `room_photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_review`
--

CREATE TABLE `room_review` (
  `room_review_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `review_data` varchar(255) DEFAULT NULL,
  `rating` float NOT NULL DEFAULT 0,
  `review_date` datetime NOT NULL,
  `state` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenancy_history`
--

CREATE TABLE `tenancy_history` (
  `tenancy_history_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `move_in_date` date NOT NULL,
  `move_out_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_voice`
--

CREATE TABLE `tenant_voice` (
  `tenant_voice_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `voice` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `issue_solved_date` datetime DEFAULT NULL,
  `issue_state` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_voice_response`
--

CREATE TABLE `tenant_voice_response` (
  `tenant_voice_response_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL DEFAULT 0,
  `tenant_voice_id` int(11) NOT NULL DEFAULT 0,
  `whose` varchar(9) NOT NULL DEFAULT 'landlord',
  `tenant_id` int(11) NOT NULL DEFAULT 0,
  `landlord_id` int(11) NOT NULL DEFAULT 0,
  `response` varchar(255) NOT NULL,
  `response_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(15) NOT NULL,
  `middle_name` varchar(15) NOT NULL DEFAULT 'null',
  `last_name` varchar(15) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `province` int(11) NOT NULL,
  `district` int(11) NOT NULL,
  `isVdc` varchar(13) NOT NULL,
  `area_name` varchar(30) NOT NULL,
  `ward` int(11) NOT NULL,
  `role` varchar(10) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `citizenship_number` varchar(255) NOT NULL,
  `citizenship_front_pic` varchar(255) NOT NULL,
  `citizenship_back_pic` varchar(255) NOT NULL,
  `account_state` int(11) NOT NULL DEFAULT 1,
  `register_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `announcement_response`
--
ALTER TABLE `announcement_response`
  ADD PRIMARY KEY (`announcement_response_id`);

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`application_id`);

--
-- Indexes for table `custom_room`
--
ALTER TABLE `custom_room`
  ADD PRIMARY KEY (`custom_room_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `house`
--
ALTER TABLE `house`
  ADD PRIMARY KEY (`house_id`);

--
-- Indexes for table `house_photo`
--
ALTER TABLE `house_photo`
  ADD PRIMARY KEY (`house_photo_id`);

--
-- Indexes for table `leave_application`
--
ALTER TABLE `leave_application`
  ADD PRIMARY KEY (`leave_application_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `room_photo`
--
ALTER TABLE `room_photo`
  ADD PRIMARY KEY (`room_photo_id`);

--
-- Indexes for table `room_review`
--
ALTER TABLE `room_review`
  ADD PRIMARY KEY (`room_review_id`);

--
-- Indexes for table `tenancy_history`
--
ALTER TABLE `tenancy_history`
  ADD PRIMARY KEY (`tenancy_history_id`);

--
-- Indexes for table `tenant_voice`
--
ALTER TABLE `tenant_voice`
  ADD PRIMARY KEY (`tenant_voice_id`);

--
-- Indexes for table `tenant_voice_response`
--
ALTER TABLE `tenant_voice_response`
  ADD PRIMARY KEY (`tenant_voice_response_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement_response`
--
ALTER TABLE `announcement_response`
  MODIFY `announcement_response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_room`
--
ALTER TABLE `custom_room`
  MODIFY `custom_room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `house`
--
ALTER TABLE `house`
  MODIFY `house_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `house_photo`
--
ALTER TABLE `house_photo`
  MODIFY `house_photo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_application`
--
ALTER TABLE `leave_application`
  MODIFY `leave_application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_photo`
--
ALTER TABLE `room_photo`
  MODIFY `room_photo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_review`
--
ALTER TABLE `room_review`
  MODIFY `room_review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenancy_history`
--
ALTER TABLE `tenancy_history`
  MODIFY `tenancy_history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_voice`
--
ALTER TABLE `tenant_voice`
  MODIFY `tenant_voice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_voice_response`
--
ALTER TABLE `tenant_voice_response`
  MODIFY `tenant_voice_response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
