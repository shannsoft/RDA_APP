-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2016 at 03:29 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rdadb`
--
CREATE DATABASE IF NOT EXISTS `rdadb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `rdadb`;

-- --------------------------------------------------------

--
-- Table structure for table `buiding_plan`
--

CREATE TABLE IF NOT EXISTS `buiding_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `regdNo` varchar(20) DEFAULT NULL,
  `file_path` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected','') NOT NULL DEFAULT 'pending',
  `remark` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `asset_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `buiding_plan`
--

INSERT INTO `buiding_plan` (`id`, `user`, `name`, `regdNo`, `file_path`, `status`, `remark`, `date`, `asset_name`) VALUES
(20, 25, NULL, '2134sfesf', 'images/Chrysanthemum.jpg', 'pending', NULL, '0000-00-00', NULL),
(21, 25, NULL, '2134sfesf', 'images/Chrysanthemum.jpg', 'pending', NULL, '0000-00-00', NULL),
(22, 25, NULL, '2134sfesf', 'images/Desert.jpg', 'pending', NULL, '0000-00-00', NULL),
(23, 25, 'asdf', '2134sfesf', 'images/Desert.jpg', 'pending', NULL, '0000-00-00', NULL),
(24, 25, 'asdf', '2134sfesf', 'images/Desert.jpg', 'pending', NULL, '0000-00-00', NULL),
(25, 25, 'dsfasdfsdfsdsdfsf', '2134sfesf', 'images/Koala.jpg', 'pending', NULL, '0000-00-00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `user_type` enum('1','2','3','') NOT NULL DEFAULT '2',
  `user_name` varchar(20) DEFAULT NULL,
  `mobile` text NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `first_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `token` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `id_3` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `user_name`, `mobile`, `password`, `email`, `first_name`, `last_name`, `token`, `status`) VALUES
(17, '1', 'admin', '', 'e10adc3949ba59abbe56e057f20f883e', 'admin@gmail.com', 'Santosh', 'Majhi', '', 1),
(24, '2', 'admin2', '9438753143', '81dc9bdb52d04dc20036dbd8313ed055', 'santoshmajhi99@gmail.com', 'santosh', 'majhi', NULL, 0),
(25, '3', 'user3', '1111111111', 'e10adc3949ba59abbe56e057f20f883e', 'asdf@gmail.com', NULL, NULL, 'kxCdXp7RJf00u1RoAcMeRKmeE9HDOlo8GPwY6yHQq0ZYt816CqRqGP7F2QS2', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buiding_plan`
--
ALTER TABLE `buiding_plan`
  ADD CONSTRAINT `buiding_plan_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
