-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 05, 2026 at 08:22 PM
-- Server version: 5.7.44-48
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `isaaccra_clientsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiry_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `trip_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `travelers` int(11) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`inquiry_id`, `trip_id`, `trip_type`, `full_name`, `travelers`, `submitted_at`) VALUES
(1, 1, 'adventure', 'Jane Smith', 2, '2026-03-01 10:30:00'),
(2, 3, 'relaxation', 'Carlos Rivera', 2, '2026-03-05 14:15:00'),
(3, 4, 'family', 'Amy Johnson', 4, '2026-03-10 09:45:00'),
(4, 5, 'adventure', 'Michael Chen', 3, '2026-03-12 16:20:00'),
(5, 7, 'relaxation', 'Sarah Patel', 2, '2026-03-15 11:05:00'),
(6, 2, 'cultural', 'David Thompson', 2, '2026-03-18 13:40:00'),
(7, 8, 'family', 'Maria Rodriguez', 5, '2026-03-22 08:55:00'),
(8, 9, 'adventure', 'Emma Wilson', 2, '2026-03-25 19:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `trip_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `trip_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `price_per_person` decimal(10,2) NOT NULL,
  `max_travelers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `trip_name`, `trip_type`, `description`, `price_per_person`, `max_travelers`) VALUES
(1, 'Caribbean Paradise Escape', 'adventure', 'Experience pristine beaches, crystal-clear waters, snorkeling, and vibrant island culture in Jamaica and Aruba.', 1299.99, 20),
(2, 'European Grand Tour', 'cultural', 'Immerse yourself in centuries of history, art, and culture across Italy, France, and Greece with expert local guides.', 2799.99, 16),
(3, 'Romantic Santorini Getaway', 'relaxation', 'Stunning sunsets, white-washed villages, and luxury overwater-style experiences in the Greek islands.', 3199.99, 12),
(4, 'Family Costa Rica Adventure', 'family', 'Zip-lining, rainforest hikes, wildlife encounters, and family-friendly beaches in beautiful Costa Rica.', 1899.99, 24),
(5, 'Warrior Spirit', 'family', 'Go and be a warrior!', 1144.99, 13),
(6, 'Tuscany Wine & Culture Escape', 'cultural', 'Explore charming hill towns, taste world-class wines, and enjoy authentic Italian cuisine in Tuscany.', 2199.99, 14),
(7, 'Romantic Honeymoon', 'relaxation', 'Relax with your boo!', 2699.99, 2),
(8, 'Disney World Family Magic', 'family', 'Experience the magic of Walt Disney World with park tickets, character dining, and unforgettable family memories.', 1599.99, 25),
(9, 'Iceland Northern Lights Explorer', 'adventure', 'Chase the Northern Lights, soak in geothermal hot springs, and explore dramatic volcanic landscapes.', 2899.99, 15),
(10, 'Paris Romantic Honeymoon', 'relaxation', 'The City of Light awaits with Eiffel Tower views, Seine River cruises, and intimate boutique hotels.', 2599.99, 12),
(15, 'French Riviera', 'relaxation', 'jrj coem and see this s', 1122.99, 1),
(16, 'French Riviera', 'relaxation', 'jrj coem and see this s', 1122.99, 1),
(17, 'Mount Everest', 'adventure', 'Climb the tallest building in the world', 1133.99, 13),
(18, 'Dubai', 'relaxation', 'Come  and see the most modern city in the world!', 2133.99, 14);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiry_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
