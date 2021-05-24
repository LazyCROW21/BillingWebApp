-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2020 at 02:58 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `item`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill_detail`
--

CREATE TABLE `bill_detail` (
  `bill_id` int(11) NOT NULL,
  `cust_name` varchar(32) DEFAULT NULL,
  `cust_phone` varchar(10) DEFAULT NULL,
  `cust_choice` varchar(12) DEFAULT NULL,
  `ready_date` date DEFAULT NULL,
  `ready_time` time DEFAULT NULL,
  `order_date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted` tinyint(4) DEFAULT 0,
  `discount` float DEFAULT NULL,
  `delivery_charge` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bill_detail`
--

INSERT INTO `bill_detail` (`bill_id`, `cust_name`, `cust_phone`, `cust_choice`, `ready_date`, `ready_time`, `order_date_time`, `accepted`, `discount`, `delivery_charge`) VALUES
(15, 'Hardik Kardam', '7567496109', 'pick_up', '2020-06-24', '21:47:00', '2020-06-24 16:18:03', 1, 0, 10),
(16, 'Hardik Kardam', '7567496109', 'pick_up', '2020-06-25', '05:26:00', '2020-06-25 14:30:48', 1, 2, 10),
(17, 'Hardik', '7567496109', 'pick_up', '2020-06-25', '21:02:00', '2020-06-25 15:33:27', 1, 10, 20),
(18, NULL, NULL, NULL, NULL, NULL, '2020-06-26 09:40:45', 1, 0, 0),
(19, NULL, NULL, NULL, NULL, NULL, '2020-06-26 11:15:13', 0, NULL, NULL),
(20, NULL, NULL, NULL, NULL, NULL, '2020-06-26 11:15:17', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_item_list`
--

CREATE TABLE `bill_item_list` (
  `bill_item_id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `selling_price` float DEFAULT NULL,
  `item_qty` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bill_item_list`
--

INSERT INTO `bill_item_list` (`bill_item_id`, `bill_id`, `item_id`, `selling_price`, `item_qty`) VALUES
(7, 15, 79, 10, 5),
(8, 16, 78, 10, 1),
(9, 16, 80, 25, 2),
(10, 16, 83, 10, 5),
(11, 17, 82, 10, 5),
(12, 17, 83, 10, 2),
(13, 17, 86, 10, 6),
(14, 17, 87, 10, 5),
(19, 18, 80, 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(32) NOT NULL,
  `customer_phone` varchar(10) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_gst` varchar(15) DEFAULT NULL,
  `customer_addr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_phone`, `customer_email`, `customer_gst`, `customer_addr`) VALUES
(24, 'Hardik Kardam', '7801846413', 'sasukeuchiha51047@gmail.com', NULL, 'Hello kitty'),
(25, 'Yash Noob', '7567496109', 'yashkardam@yahoo.co.in', '1234567890ABCDE', 'kahi  toh rehta hai pata nahi kaha rehta hai but rehta hai yeh paka hai ');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(32) NOT NULL,
  `Item_price` float NOT NULL,
  `GST` float DEFAULT 0,
  `item_stock` float NOT NULL,
  `deleted` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `Item_price`, `GST`, `item_stock`, `deleted`) VALUES
(76, 'ramen', 10, 9, 0, 0),
(77, 'ઘાણી', 1, 1, 1000, 0),
(78, 'Maggi', 10, 0, 9999, 0),
(79, 'Lays Tomato', 10, 12, 95, 0),
(80, 'Pepsi', 25, 18, 117, 0),
(81, 'Lays Salt', 10, 12, 102, 0),
(82, 'Lays Onion', 10, 12, 120, 0),
(83, 'Ice Cream', 10, 18, 43, 0),
(84, 'Coca Cola', 25, 18, 160, 0),
(85, '5Star', 5, 6, 100, 0),
(86, 'Dairy Milk', 10, 12, 117, 0),
(87, 'ParleG', 10, 0, 116, 0),
(88, 'Cookies', 20, 12, 132, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill_detail`
--
ALTER TABLE `bill_detail`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `bill_item_list`
--
ALTER TABLE `bill_item_list`
  ADD PRIMARY KEY (`bill_item_id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_phone` (`customer_phone`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bill_detail`
--
ALTER TABLE `bill_detail`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `bill_item_list`
--
ALTER TABLE `bill_item_list`
  MODIFY `bill_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill_item_list`
--
ALTER TABLE `bill_item_list`
  ADD CONSTRAINT `bill_item_list_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill_detail` (`bill_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
