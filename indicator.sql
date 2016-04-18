-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2016 at 10:00 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `indicator`
--

-- --------------------------------------------------------

--
-- Table structure for table `indicators`
--

CREATE TABLE `indicators` (
  `id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `indicators`
--

INSERT INTO `indicators` (`id`, `name`, `type`, `value`, `parent`, `hidden`) VALUES
(1, '_filename', 'file-name', 'name|', 18, 0),
(2, '_fileregex', 'file-regex', 'name regex|', 18, 0),
(3, '_filehash', 'file-hash', 'sha1|f1l3h4sh|', 17, 0),
(4, '_processname', 'process-name', 'name|', 0, 0),
(5, '_processregex', 'process-regex', 'name regex|', 0, 0),
(6, '_processhash', 'process-hash', 'sha1|3x3cu74bl3h4sh|', 0, 0),
(7, '_mutexname', 'mutex-name', 'name|', 0, 0),
(8, '_networkip', 'network-ip', 'ip.a.d.dr|', 0, 0),
(9, '_networkname', 'network-name', 'www.domain.name|', 0, 0),
(10, '_networkregex', 'network-regex', '.*\\.domain\\.name|', 0, 0),
(11, '_dns', 'dns', 'dns entry|', 0, 0),
(12, '_certdom', 'cert-dom', 'domain name|', 0, 0),
(13, '_certca', 'cert-ca', 'ca name|', 0, 0),
(14, '_regname', 'reg-name', 'key name|', 0, 0),
(15, '_regregex', 'reg-regex', 'key [name]+|', 0, 0),
(16, '_regexact', 'reg-exact', 'registry path|key name|value|', 0, 0),
(17, '_and', 'and', NULL, 0, 0),
(18, '_or', 'or', NULL, 17, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `org` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `device` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `setname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `ioc_id` int(11) NOT NULL,
  `result` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sets`
--

CREATE TABLE `sets` (
  `id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `ioc_id` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sets`
--

INSERT INTO `sets` (`id`, `name`, `ioc_id`, `hidden`) VALUES
(1, 'format', 17, 0),
(2, 'format', 13, 0),
(3, 'format', 12, 0),
(4, 'format', 11, 0),
(5, 'format', 7, 0),
(6, 'format', 8, 0),
(7, 'format', 9, 0),
(8, 'format', 10, 0),
(9, 'format', 6, 0),
(10, 'format', 4, 0),
(11, 'format', 5, 0),
(12, 'format', 16, 0),
(13, 'format', 14, 0),
(14, 'format', 15, 0);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `values_count` int(11) NOT NULL,
  `values_desc` varchar(500) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `type`, `values_count`, `values_desc`) VALUES
(1, 'file-name', 1, 'File name|'),
(2, 'file-regex', 1, 'File name regex|'),
(3, 'file-hash', 2, 'Hash type|File hash|'),
(4, 'process-name', 1, 'Process name|'),
(5, 'process-regex', 1, 'Process name regex|'),
(6, 'process-hash', 2, 'Hash type|Executable hash|'),
(7, 'mutex-name', 1, 'Mutex name|'),
(8, 'network-ip', 1, 'IP address|'),
(9, 'network-name', 1, 'Domain name|'),
(10, 'network-regex', 1, 'Domain name regex|'),
(11, 'dns', 1, 'DNS entry|'),
(12, 'cert-dom', 1, 'Domain name|'),
(13, 'cert-ca', 1, 'CA name|'),
(14, 'reg-name', 1, 'Key name|'),
(15, 'reg-regex', 1, 'Key name regex|'),
(16, 'reg-exact', 3, 'Path|Key name|Value|'),
(17, 'and', 0, ''),
(18, 'or', 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `indicators`
--
ALTER TABLE `indicators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sets`
--
ALTER TABLE `sets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `indicators`
--
ALTER TABLE `indicators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `sets`
--
ALTER TABLE `sets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
