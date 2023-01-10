-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2023 at 09:59 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `patient_infosys`
--

-- --------------------------------------------------------

--
-- Table structure for table `waste`
--

CREATE TABLE `waste` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `waste`
--

INSERT INTO `waste` (`id`, `item_id`, `amount`, `date_created`) VALUES
(2, 15, 2, '2023-01-09'),
(3, 1, 2, '2023-01-10'),
(5, 15, 10, '2023-01-10'),
(6, 4, 10, '2023-01-10'),
(7, 6, 10, '2023-01-10'),
(8, 7, 10, '2023-01-10'),
(9, 9, 10, '2023-01-10'),
(10, 16, 10, '2023-01-10'),
(11, 14, 10, '2023-01-10'),
(12, 13, 10, '2023-01-10'),
(14, 10, 45, '2023-01-10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `waste`
--
ALTER TABLE `waste`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `waste`
--
ALTER TABLE `waste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;