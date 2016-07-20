-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2016 at 07:57 AM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rda_123`
--

-- --------------------------------------------------------

--
-- Table structure for table `buiding_plan`
--

CREATE TABLE `buiding_plan` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `regdNo` varchar(20) DEFAULT NULL,
  `file_no` varchar(50) NOT NULL,
  `file_path` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected','') NOT NULL DEFAULT 'pending',
  `verifier_id` int(11) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `asset_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `buiding_plan`
--

INSERT INTO `buiding_plan` (`id`, `user`, `name`, `regdNo`, `file_no`, `file_path`, `status`, `verifier_id`, `remark`, `date`, `asset_name`) VALUES
(43, 29, 'Ashok Kumar Agrawal', '938', '', 'buildingPlan/Permission-letter-of-Building-Plan.pdf', 'approved', 30, 'asdfasdf', '2016-04-04', NULL),
(44, 29, 'Bandana Nayak', '943', '', 'buildingPlan/Permission-letter-of-Building-Plan-2.pdf', 'approved', 30, '', '2016-04-04', NULL),
(45, 31, 'Test Edit', '123', '', 'buildingPlan/doc20160409200026.pdf', 'rejected', 30, 'Test edit', '2016-06-27', NULL),
(46, 29, 'Sujata', '123', '', 'buildingPlan/Lawsuit Terms and Conditions.pdf', 'pending', NULL, NULL, '2016-06-28', NULL),
(47, 29, 'Raj Kumar sahau', '456', '', 'buildingPlan/3822531_TenderDocumentType_5590589.pdf', 'pending', NULL, NULL, '2016-07-04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `details` text NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender`
--

CREATE TABLE `tender` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `file_path` text NOT NULL,
  `description` text NOT NULL,
  `user` int(11) NOT NULL,
  `verifier_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `user_type` enum('1','2','3','') NOT NULL DEFAULT '2',
  `user_name` varchar(20) DEFAULT NULL,
  `mobile` text NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `first_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `token` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `user_name`, `mobile`, `password`, `email`, `first_name`, `last_name`, `token`, `status`) VALUES
(28, '1', 'Admin', '8446753743', 'e10adc3949ba59abbe56e057f20f883e', 'admin@gmail.com', 'Rourkela', 'Admin', '', 1),
(29, '3', 'santoshRDA', '9438753143', 'e10adc3949ba59abbe56e057f20f883e', 'santosh@gmail.com', 'Krishna', 'Majhi', '', 1),
(30, '2', 'biswalRDA', '8895368590', 'e10adc3949ba59abbe56e057f20f883e', 'biswal@gmail.com', 'Rajashree', 'Biswal', '', 1),
(31, '3', 'amareshRDA', '8895368590', 'e10adc3949ba59abbe56e057f20f883e', 'amaresh11@gmail.com', 'Amaresh', 'Nayak', 'mqKgA674xTLDaqnhXLcQRwWGnNPDq9y3wHnMC3Y8agBZaYXG3eg3PFerWl6H', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buiding_plan`
--
ALTER TABLE `buiding_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tender`
--
ALTER TABLE `tender`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD KEY `id` (`id`),
  ADD KEY `id_2` (`id`),
  ADD KEY `id_3` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buiding_plan`
--
ALTER TABLE `buiding_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tender`
--
ALTER TABLE `tender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
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
