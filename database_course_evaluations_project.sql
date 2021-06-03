-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 02, 2018 at 06:33 PM
-- Server version: 5.6.13
-- PHP Version: 5.4.17

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- SET time_zone = "+00:00";


-- /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
-- /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
-- /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
-- /*!40101 SET NAMES utf8 */;

--
-- Database: `database_course_evaluations_project`
--
CREATE DATABASE IF NOT EXISTS `database_course_evaluations_project` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `database_course_evaluations_project`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `netid` varchar(10) NOT NULL,
  `N_number` int(15) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  PRIMARY KEY (`netid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`netid`, `N_number`, `first_name`, `last_name`) VALUES
('admin', 157, 'admin', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `Course_ID` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `room` varchar(50) NOT NULL,
  `teacher_netid` varchar(10) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `mon` int(11) NOT NULL DEFAULT '0',
  `tues` int(11) NOT NULL DEFAULT '0',
  `wed` int(11) NOT NULL DEFAULT '0',
  `thurs` int(11) NOT NULL DEFAULT '0',
  `fri` int(11) NOT NULL DEFAULT '0',
  `sat` int(11) NOT NULL DEFAULT '0',
  `sun` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Course_ID`,`semester`),
  UNIQUE KEY `Course_ID` (`Course_ID`),
  KEY `teacher_netid` (`teacher_netid`),
  KEY `semester` (`semester`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`Course_ID`, `name`, `room`, `teacher_netid`, `semester`, `start_time`, `end_time`, `start_date`, `end_date`, `mon`, `tues`, `wed`, `thurs`, `fri`, `sat`, `sun`) VALUES
('course1', 'test', 'test', 'teacher', 'SPRING 2018', '12:40:00', '23:59:59', '2018-01-31', '2018-05-07', 1, 1, 1, 1, 1, 1, 1),
('course2', 'test', 'test', 'teacher', 'SPRING 2018', '12:40:00', '23:59:59', '2018-01-31', '2018-05-07', 1, 1, 1, 1, 1, 1, 1),
('course3', 'test', 'test', 'teacher1', 'SPRING 2018', '12:40:00', '23:59:59', '2018-01-31', '2018-05-07', 1, 1, 1, 1, 1, 1, 1),
('course4', 'test', 'test', 'vais', 'SPRING 2018', '12:40:00', '23:59:59', '2018-01-31', '2018-05-07', 1, 1, 1, 1, 1, 1, 1),
('CS3083', 'Intro to Databases', '2MTC 9.011', 'vais', 'SPRING 2018', '11:00:00', '12:20:00', '2018-01-23', '2018-05-11', 0, 1, 0, 1, 0, 0, 0),
('test', 'test', 'test', 'vais', 'SPRING 2018', '12:40:00', '23:59:59', '2018-01-31', '2018-05-07', 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_eval`
--

CREATE TABLE IF NOT EXISTS `course_eval` (
  `Eval_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Grade` int(11) NOT NULL,
  `Comment` varchar(1000) NOT NULL,
  `Date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Course_ID` varchar(10) NOT NULL,
  `semester` varchar(50) NOT NULL,
  PRIMARY KEY (`Eval_ID`),
  KEY `Course_ID` (`Course_ID`,`semester`),
  KEY `Course_ID_2` (`Course_ID`,`semester`),
  KEY `semester` (`semester`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=113 ;

--
-- Dumping data for table `course_eval`
--

INSERT INTO `course_eval` (`Eval_ID`, `Grade`, `Comment`, `Date_added`, `Course_ID`, `semester`) VALUES
(57, 2, '', '2018-04-17 14:38:09', 'CS3083', 'SPRING 2018'),
(58, 4, '', '2018-04-17 14:50:56', 'CS3083', 'SPRING 2018'),
(59, 4, 'it''s all good', '2018-04-17 14:52:28', 'CS3083', 'SPRING 2018'),
(60, 0, '', '2018-04-17 15:16:35', 'CS3083', 'SPRING 2018'),
(61, 4, 'it great', '2018-04-17 15:20:39', 'CS3083', 'SPRING 2018'),
(62, 0, '', '2018-04-17 21:08:34', 'CS3083', 'SPRING 2018'),
(63, 0, '', '2018-04-17 21:12:41', 'CS3083', 'SPRING 2018'),
(64, 4, '', '2018-04-17 21:14:29', 'CS3083', 'SPRING 2018'),
(65, 4, '', '2018-04-17 21:19:38', 'CS3083', 'SPRING 2018'),
(66, 4, '', '2018-04-17 21:20:20', 'CS3083', 'SPRING 2018'),
(67, 4, '', '2018-04-17 21:20:56', 'CS3083', 'SPRING 2018'),
(68, 4, '', '2018-04-17 21:22:05', 'CS3083', 'SPRING 2018'),
(69, 4, '', '2018-04-17 21:22:35', 'CS3083', 'SPRING 2018'),
(70, 4, '', '2018-04-17 21:32:17', 'CS3083', 'SPRING 2018'),
(71, 4, 'it''s awesome', '2018-04-17 21:33:04', 'CS3083', 'SPRING 2018'),
(72, 3, '', '2018-04-17 21:38:18', 'CS3083', 'SPRING 2018'),
(73, 4, '', '2018-04-18 16:59:33', 'CS3083', 'SPRING 2018'),
(74, 4, 'asd', '2018-04-18 17:10:28', 'CS3083', 'SPRING 2018'),
(75, 4, '', '2018-04-18 17:44:45', 'CS3083', 'SPRING 2018'),
(76, 4, '', '2018-04-18 17:46:35', 'CS3083', 'SPRING 2018'),
(77, 4, '', '2018-04-18 17:48:05', 'CS3083', 'SPRING 2018'),
(78, 4, '', '2018-04-18 21:30:15', 'CS3083', 'SPRING 2018'),
(79, 4, '', '2018-04-18 21:30:22', 'CS3083', 'SPRING 2018'),
(80, 4, '', '2018-04-19 11:40:30', 'CS3083', 'SPRING 2018'),
(81, 3, '', '2018-04-19 14:36:11', 'CS3083', 'SPRING 2018'),
(82, 1, 'check''s', '2018-04-19 14:51:30', 'CS3083', 'SPRING 2018'),
(83, 4, 'what if\r\nthere was\r\nno project\r\nand everyone\r\ngot A+', '2018-04-19 17:32:45', 'CS3083', 'SPRING 2018'),
(84, 3, '', '2018-04-19 17:34:37', 'CS3083', 'SPRING 2018'),
(95, 3, '', '2018-04-21 14:52:46', 'CS3083', 'SPRING 2018'),
(96, 4, '', '2018-05-31 00:00:00', 'CS3083', 'SPRING 2018'),
(97, 4, '', '2018-01-31 00:00:00', 'CS3083', 'SPRING 2018'),
(98, 2, '', '2018-04-21 20:07:59', 'test', 'SPRING 2018'),
(99, 4, '', '2018-04-21 20:35:30', 'test', 'SPRING 2018'),
(100, 3, 'lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots of text lots o', '2018-04-22 19:03:20', 'CS3083', 'SPRING 2018'),
(101, 4, 'asd', '2018-04-25 22:58:15', 'CS3083', 'SPRING 2018'),
(102, 4, 'not bad', '2018-04-26 11:44:26', 'CS3083', 'SPRING 2018'),
(103, 4, 'testing', '2018-04-27 14:34:47', 'CS3083', 'SPRING 2018'),
(104, 4, '', '2018-04-29 17:19:50', 'CS3083', 'SPRING 2018'),
(106, 4, '', '2018-04-29 17:55:22', 'course4', 'SPRING 2018'),
(107, 3, '', '2018-04-29 17:55:48', 'course2', 'SPRING 2018'),
(108, 3, '', '2018-04-29 17:56:09', 'course3', 'SPRING 2018'),
(109, 1, '', '2018-04-29 17:56:27', 'course1', 'SPRING 2018'),
(110, 1, '', '2018-04-29 17:58:44', 'course1', 'SPRING 2018'),
(111, 3, '', '2018-04-29 17:59:04', 'test', 'SPRING 2018'),
(112, 2, '', '2018-04-29 17:59:46', 'course3', 'SPRING 2018');

-- --------------------------------------------------------

--
-- Table structure for table `course_rosters`
--

CREATE TABLE IF NOT EXISTS `course_rosters` (
  `Course_ID` varchar(20) NOT NULL,
  `student_netid` varchar(10) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `last_added` date NOT NULL,
  PRIMARY KEY (`Course_ID`,`student_netid`,`semester`),
  KEY `student_netid` (`student_netid`),
  KEY `semester` (`semester`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course_rosters`
--

INSERT INTO `course_rosters` (`Course_ID`, `student_netid`, `semester`, `last_added`) VALUES
('course1', 'fk652', 'SPRING 2018', '2018-04-29'),
('course1', 'student', 'SPRING 2018', '2018-04-29'),
('course1', 'student1', 'SPRING 2018', '0000-00-00'),
('course1', 'student10', 'SPRING 2018', '0000-00-00'),
('course2', 'fk652', 'SPRING 2018', '2018-04-29'),
('course2', 'student2', 'SPRING 2018', '0000-00-00'),
('course2', 'student3', 'SPRING 2018', '0000-00-00'),
('course2', 'student7', 'SPRING 2018', '0000-00-00'),
('course2', 'student8', 'SPRING 2018', '0000-00-00'),
('course3', 'fk652', 'SPRING 2018', '2018-04-29'),
('course3', 'student', 'SPRING 2018', '2018-04-29'),
('course3', 'student2', 'SPRING 2018', '0000-00-00'),
('course3', 'student7', 'SPRING 2018', '0000-00-00'),
('course3', 'student8', 'SPRING 2018', '0000-00-00'),
('course3', 'student9', 'SPRING 2018', '0000-00-00'),
('course4', 'fk652', 'SPRING 2018', '2018-04-29'),
('course4', 'student', 'SPRING 2018', '0000-00-00'),
('course4', 'student1', 'SPRING 2018', '0000-00-00'),
('course4', 'student10', 'SPRING 2018', '0000-00-00'),
('course4', 'student2', 'SPRING 2018', '0000-00-00'),
('course4', 'student3', 'SPRING 2018', '0000-00-00'),
('course4', 'student6', 'SPRING 2018', '0000-00-00'),
('CS3083', 'fk652', 'SPRING 2018', '2018-04-29'),
('CS3083', 'student', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student1', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student10', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student2', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student3', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student4', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student5', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student6', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student7', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student8', 'SPRING 2018', '0000-00-00'),
('CS3083', 'student9', 'SPRING 2018', '0000-00-00'),
('test', 'student', 'SPRING 2018', '2018-04-29'),
('test', 'student1', 'SPRING 2018', '0000-00-00'),
('test', 'student2', 'SPRING 2018', '0000-00-00'),
('test', 'student3', 'SPRING 2018', '0000-00-00'),
('test', 'student4', 'SPRING 2018', '0000-00-00'),
('test', 'student5', 'SPRING 2018', '0000-00-00'),
('test', 'student6', 'SPRING 2018', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `login_info`
--

CREATE TABLE IF NOT EXISTS `login_info` (
  `username` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'student',
  `active` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login_info`
--

INSERT INTO `login_info` (`username`, `password`, `user_type`, `active`) VALUES
('admin', '482c811da5d5b4bc6d497ffa98491e38', 'admin', 0),
('fk652', '5f4dcc3b5aa765d61d8327deb882cf99', 'student', 0),
('student', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student1', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student10', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student2', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student3', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student4', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student5', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student6', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student7', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student8', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('student9', '482c811da5d5b4bc6d497ffa98491e38', 'student', 0),
('teacher', '5f4dcc3b5aa765d61d8327deb882cf99', 'teacher', 0),
('teacher1', '5f4dcc3b5aa765d61d8327deb882cf99', 'teacher', 0),
('vais', '5f4dcc3b5aa765d61d8327deb882cf99', 'teacher', 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `netid` varchar(10) NOT NULL,
  `N_number` int(15) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  PRIMARY KEY (`netid`,`N_number`),
  UNIQUE KEY `N_number` (`N_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`netid`, `N_number`, `first_name`, `last_name`) VALUES
('fk652', 12345678, 'Fahim', 'Khan'),
('student', 8, 'john', 'doe'),
('student1', 9, 'john', 'doe'),
('student10', 18, 'john', 'doe'),
('student2', 10, 'jane', 'doe'),
('student3', 11, 'john', 'doe'),
('student4', 12, 'john', 'doe'),
('student5', 13, 'john', 'doe'),
('student6', 14, 'john', 'doe'),
('student7', 15, 'john', 'doe'),
('student8', 16, 'john', 'doe'),
('student9', 17, 'john', 'doe');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE IF NOT EXISTS `teachers` (
  `netid` varchar(10) NOT NULL,
  `N_number` int(15) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  PRIMARY KEY (`netid`,`N_number`),
  UNIQUE KEY `N_number` (`N_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`netid`, `N_number`, `first_name`, `last_name`) VALUES
('teacher', 100, 'john', 'doe'),
('teacher1', 101, 'jane', 'doe'),
('vais', 87654321, 'Joseph', 'Vaisman');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`netid`) REFERENCES `login_info` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_netid`) REFERENCES `teachers` (`netid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_eval`
--
ALTER TABLE `course_eval`
  ADD CONSTRAINT `course_eval_ibfk_2` FOREIGN KEY (`semester`) REFERENCES `courses` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_eval_ibfk_1` FOREIGN KEY (`Course_ID`) REFERENCES `courses` (`Course_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_rosters`
--
ALTER TABLE `course_rosters`
  ADD CONSTRAINT `course_rosters_ibfk_3` FOREIGN KEY (`semester`) REFERENCES `courses` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_rosters_ibfk_1` FOREIGN KEY (`Course_ID`) REFERENCES `courses` (`Course_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_rosters_ibfk_2` FOREIGN KEY (`student_netid`) REFERENCES `students` (`netid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`netid`) REFERENCES `login_info` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`netid`) REFERENCES `login_info` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

-- /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
-- /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
-- /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
