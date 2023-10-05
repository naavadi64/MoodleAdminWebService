-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 05, 2023 at 08:19 AM
-- Server version: 8.0.34-0ubuntu0.22.04.1
-- PHP Version: 8.1.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moodleadminwebservice`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_assign`
--

CREATE TABLE `t_assign` (
  `assignid` int NOT NULL,
  `userid` int NOT NULL,
  `roleid` int NOT NULL,
  `unitid` int NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `contract` varchar(128) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `t_course`
--

CREATE TABLE `t_course` (
  `courseid` int NOT NULL,
  `coursename` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `coursedesc` varchar(4096) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_course_enrollment`
--

CREATE TABLE `t_course_enrollment` (
  `userid` int NOT NULL,
  `courseid` int NOT NULL,
  `lastaccess` datetime DEFAULT NULL,
  `progress` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `t_event_log`
--

CREATE TABLE `t_event_log` (
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event` varchar(128) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `t_event_log`
--

INSERT INTO `t_event_log` (`time`, `event`) VALUES
('2023-09-24 06:54:22', 'Database initialization');

-- --------------------------------------------------------

--
-- Table structure for table `t_role`
--

CREATE TABLE `t_role` (
  `roleid` int NOT NULL,
  `rolename` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `roledesc` varchar(256) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_unit`
--

CREATE TABLE `t_unit` (
  `unitid` int NOT NULL,
  `unitname` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `unitdesc` varchar(256) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hierarchy` tinyint NOT NULL DEFAULT '0',
  `parent_unit` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_user`
--

CREATE TABLE `t_user` (
  `userid` int NOT NULL,
  `username` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `userpass` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `userfname` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `userlname` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `wsadmin` tinyint NOT NULL DEFAULT '0',
  `lastaccess` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_assign`
--
ALTER TABLE `t_assign`
  ADD PRIMARY KEY (`assignid`),
  ADD KEY `fk_assign_from_role` (`roleid`),
  ADD KEY `fk_assign_from_unit` (`unitid`),
  ADD KEY `fk_assign_from_user` (`userid`);

--
-- Indexes for table `t_course`
--
ALTER TABLE `t_course`
  ADD PRIMARY KEY (`courseid`);

--
-- Indexes for table `t_course_enrollment`
--
ALTER TABLE `t_course_enrollment`
  ADD KEY `fk_enrollment_from_user` (`userid`),
  ADD KEY `fk_enrollement_from_course` (`courseid`);

--
-- Indexes for table `t_role`
--
ALTER TABLE `t_role`
  ADD PRIMARY KEY (`roleid`);

--
-- Indexes for table `t_unit`
--
ALTER TABLE `t_unit`
  ADD PRIMARY KEY (`unitid`);

--
-- Indexes for table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_assign`
--
ALTER TABLE `t_assign`
  MODIFY `assignid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_assign`
--
ALTER TABLE `t_assign`
  ADD CONSTRAINT `fk_assign_from_role` FOREIGN KEY (`roleid`) REFERENCES `t_role` (`roleid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_assign_from_unit` FOREIGN KEY (`unitid`) REFERENCES `t_unit` (`unitid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_assign_from_user` FOREIGN KEY (`userid`) REFERENCES `t_user` (`userid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `t_course_enrollment`
--
ALTER TABLE `t_course_enrollment`
  ADD CONSTRAINT `fk_enrollement_from_course` FOREIGN KEY (`courseid`) REFERENCES `t_course` (`courseid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_enrollment_from_user` FOREIGN KEY (`userid`) REFERENCES `t_user` (`userid`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
