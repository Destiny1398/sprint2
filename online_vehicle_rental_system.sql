-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2024 at 07:20 PM
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
-- Database: `online_vehicle_rental_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_email`, `admin_password`, `admin_name`, `admin_created_at`) VALUES
(1, 'admin@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'admin1', '2024-07-15 17:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `return_location` varchar(255) NOT NULL,
  `pickup_date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `return_date` date NOT NULL,
  `return_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `booking_modified_date` timestamp NULL DEFAULT NULL,
  `booking_cancelled_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `vehicle_id`, `pickup_location`, `return_location`, `pickup_date`, `pickup_time`, `return_date`, `return_time`, `price`, `customer_id`, `booking_date`, `booking_modified_date`, `booking_cancelled_date`) VALUES
(17, 11, 'Oakville', 'Scarborough', '2024-07-18', '12:00:00', '2024-07-19', '02:30:00', 148.50, 4, '2024-07-16 18:39:45', '2024-07-18 02:14:54', NULL),
(18, 11, 'Oakville', 'Toronto', '2024-07-30', '02:30:00', '2024-07-31', '08:00:00', 148.50, 4, '2024-07-16 19:12:03', '2024-07-18 03:46:10', '2024-07-18 04:22:58'),
(19, 12, 'Milton', 'Scarborough', '2024-07-17', '12:00:00', '2024-07-31', '12:00:00', 1120.00, 4, '2024-07-16 19:27:20', NULL, NULL),
(20, 11, 'Oakville', 'Oakville', '2024-07-18', '04:30:00', '2024-07-19', '04:30:00', 99.00, 4, '2024-07-17 02:21:44', NULL, '2024-07-18 17:42:32'),
(21, 11, 'Oakville', 'Oakville', '2024-07-18', '12:00:00', '2024-07-19', '12:00:00', 99.00, 4, '2024-07-17 02:49:46', NULL, '2024-07-18 17:42:24'),
(22, 13, 'Toronto', 'Scarborough', '2024-07-18', '12:00:00', '2024-07-18', '06:30:00', 50.00, 4, '2024-07-17 03:15:52', NULL, '2024-07-18 17:42:19'),
(23, 13, 'Toronto', 'Milton', '2024-07-18', '12:00:00', '2024-07-19', '12:00:00', 100.00, 4, '2024-07-17 03:19:54', NULL, NULL),
(24, 13, 'Toronto', 'Scarborough', '2024-07-19', '09:00:00', '2024-07-23', '08:30:00', 350.00, 4, '2024-07-18 03:31:42', '2024-07-18 03:54:09', '2024-07-18 17:42:11'),
(25, 14, 'Milton', 'Toronto', '2024-07-26', '12:00:00', '2024-07-31', '12:00:00', 500.00, 4, '2024-07-18 05:38:08', NULL, '2024-07-18 17:42:08'),
(26, 14, 'Milton', 'Mississauga', '2024-07-19', '12:00:00', '2024-07-18', '12:00:00', 0.00, 4, '2024-07-19 00:00:35', NULL, NULL),
(27, 21, 'Kitchener', 'Toronto', '2024-07-19', '09:00:00', '2024-07-20', '05:30:00', 74.50, 10, '2024-07-19 17:03:11', '2024-07-19 17:03:45', '2024-07-19 17:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `car_features`
--

CREATE TABLE `car_features` (
  `feature_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `ABS` tinyint(1) DEFAULT NULL,
  `rearview_camera` tinyint(1) DEFAULT NULL,
  `traction_control` tinyint(1) DEFAULT NULL,
  `air_conditioning` tinyint(1) DEFAULT NULL,
  `power_windows_locks` tinyint(1) DEFAULT NULL,
  `keyless_entry` tinyint(1) DEFAULT NULL,
  `cruise_control` tinyint(1) DEFAULT NULL,
  `adjustable_steering` tinyint(1) DEFAULT NULL,
  `bluetooth` tinyint(1) DEFAULT NULL,
  `navigation` tinyint(1) DEFAULT NULL,
  `sunroof` tinyint(1) DEFAULT NULL,
  `heated_seats` tinyint(1) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_features`
--

INSERT INTO `car_features` (`feature_id`, `vehicle_id`, `ABS`, `rearview_camera`, `traction_control`, `air_conditioning`, `power_windows_locks`, `keyless_entry`, `cruise_control`, `adjustable_steering`, `bluetooth`, `navigation`, `sunroof`, `heated_seats`, `admin_id`) VALUES
(2, 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(3, 4, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(4, 11, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(5, 14, 1, 1, 1, 0, 0, 1, 1, 0, 1, 1, 1, 1, 1),
(6, 17, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 1),
(7, 18, 1, 0, 1, 1, 0, 0, 1, 0, 0, 1, 0, 0, 1),
(8, 19, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(9, 20, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(10, 21, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` int(11) NOT NULL,
  `cust_first_name` varchar(255) NOT NULL,
  `cust_last_name` varchar(255) NOT NULL,
  `cust_email` varchar(255) NOT NULL,
  `cust_password` varchar(255) NOT NULL,
  `cust_contact_no` varchar(255) NOT NULL,
  `cust_profile_image` text NOT NULL,
  `cust_license_number` varchar(255) NOT NULL,
  `cust_dob` date NOT NULL,
  `cust_acc_created_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `cust_first_name`, `cust_last_name`, `cust_email`, `cust_password`, `cust_contact_no`, `cust_profile_image`, `cust_license_number`, `cust_dob`, `cust_acc_created_on`) VALUES
(2, 'sai sravan kumar', 'repaka', 'sravan@gmail.com', '0b3f9a5344ae759d85f8484e306618fe', '(888) 888 - 8888', 'uploads/7.jpg', 'AP02920130020023', '1994-02-24', '2024-07-14 18:12:23'),
(4, 'sai sravan kumar', 'repaka', 'sravan123@gmail.com', '$2y$10$nB/6Sv4UwB7LlAT/n/M4Xu7aGziYI46MQz32Zi9I.GBKbJgBn9C1q', '(888) 888 - 8888', 'uploads/car-05.jpg', 'AP02920130020023', '1994-02-24', '2024-07-14 18:12:23'),
(5, 'sravan kumar', 'sharma', 'sharma@gmail.com', '$2y$10$dZiVHP48KQ31js6d8bsqveyZqxNMna7CRr6ezGI629wnsvpnKtXeq', '(289) 999 - 9999', 'uploads/default-profile.png', 'AP02920130020023', '1994-02-24', '2024-07-18 23:31:42'),
(10, 'badal', 'b', 'badal@gmail.com', '$2y$10$VCukMIHumRMjn30fmnH6aeV.hXzlAYpfFkhf9TV4D1Gq43LULhcuC', '(999) 999 - 9999', 'uploads/car-05.jpg', 'AP02920130020023', '1994-02-24', '2024-07-19 16:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `driverlicensedetail`
--

CREATE TABLE `driverlicensedetail` (
  `dl_detail_id` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ValidFrom` date NOT NULL,
  `ValidTo` date NOT NULL,
  `RelativeName` varchar(100) NOT NULL,
  `State` varchar(50) NOT NULL,
  `COVDetails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`COVDetails`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driverlicensedetail`
--

INSERT INTO `driverlicensedetail` (`dl_detail_id`, `CustomerID`, `ValidFrom`, `ValidTo`, `RelativeName`, `State`, `COVDetails`) VALUES
(4, 7, '2024-07-16', '2024-07-04', 'sdkj', 'snd', '\"dsfjk\"'),
(5, 8, '0001-01-01', '0001-01-01', 'dna', 'jsdk', '\"fsjk4\"'),
(6, 9, '0001-01-01', '0001-01-01', 'fhdskj53', 'fsjkddfs', '\"537dskjf\"');

-- --------------------------------------------------------

--
-- Table structure for table `motorcycle_features`
--

CREATE TABLE `motorcycle_features` (
  `feature_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `ABS` tinyint(1) DEFAULT NULL,
  `multiple_riding_modes` tinyint(1) DEFAULT NULL,
  `GPS_navigation` tinyint(1) DEFAULT NULL,
  `bluetooth` tinyint(1) DEFAULT NULL,
  `security_system` tinyint(1) DEFAULT NULL,
  `mobile_phone_mount` tinyint(1) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `motorcycle_features`
--

INSERT INTO `motorcycle_features` (`feature_id`, `vehicle_id`, `ABS`, `multiple_riding_modes`, `GPS_navigation`, `bluetooth`, `security_system`, `mobile_phone_mount`, `admin_id`) VALUES
(1, 6, 1, 1, 0, 0, 0, 0, 1),
(2, 7, 1, 0, 0, 0, 0, 0, 1),
(3, 8, 1, 0, 0, 0, 0, 0, 1),
(4, 9, 0, 0, 0, 0, 0, 0, 1),
(5, 10, 0, 0, 0, 0, 0, 0, 1),
(6, 13, 0, 0, 0, 1, 0, 0, 1),
(7, 24, 1, 1, 1, 0, 0, 0, 1),
(8, 25, 1, 1, 1, 1, 0, 0, 1),
(9, 26, 0, 1, 1, 1, 1, 1, 1),
(10, 28, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pickup_truck_features`
--

CREATE TABLE `pickup_truck_features` (
  `feature_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `air_conditioning` tinyint(1) DEFAULT NULL,
  `four_wheel_drive` tinyint(1) DEFAULT NULL,
  `bed_liner` tinyint(1) DEFAULT NULL,
  `rearview_camera` tinyint(1) DEFAULT NULL,
  `blind_spot_monitoring` tinyint(1) DEFAULT NULL,
  `lane_departure_warning` tinyint(1) DEFAULT NULL,
  `automatic_emergency_braking` tinyint(1) DEFAULT NULL,
  `infotainment_system` tinyint(1) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pickup_truck_features`
--

INSERT INTO `pickup_truck_features` (`feature_id`, `vehicle_id`, `air_conditioning`, `four_wheel_drive`, `bed_liner`, `rearview_camera`, `blind_spot_monitoring`, `lane_departure_warning`, `automatic_emergency_braking`, `infotainment_system`, `admin_id`) VALUES
(2, 5, 1, 1, 0, 0, 0, 0, 0, 0, 1),
(3, 12, 1, 0, 0, 0, 0, 0, 0, 0, 1),
(4, 22, 1, 1, 1, 1, 0, 0, 0, 0, 1),
(5, 23, 0, 0, 0, 1, 1, 1, 1, 1, 1),
(6, 27, 0, 0, 0, 0, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `make` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `car_type` varchar(50) DEFAULT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `airbags` int(11) DEFAULT NULL,
  `doors` int(11) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image_paths` text NOT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `vehicle_type`, `make`, `model`, `year`, `car_type`, `fuel_type`, `transmission`, `airbags`, `doors`, `seats`, `price`, `location`, `image_paths`, `admin_id`, `created_at`) VALUES
(14, 'Car', 'Toyota', 'Corolla', 2020, 'Convertible', 'Gas', 'Automatic', 4, 4, 5, 200.00, 'Milton', '[\"uploads\\/corolla-1.jpg\",\"uploads\\/corolla-2.jpg\",\"uploads\\/corolla-3.jpeg\",\"uploads\\/corolla-4.jpeg\",\"uploads\\/corolla-5.jpg\"]', 1, '2024-07-18 04:49:17'),
(17, 'Car', 'Toyota', 'Camry', 2007, 'Hatchback', 'Gas', 'Automatic', 4, 4, 5, 89.00, 'Oakville', '[\"uploads\\/camry-1.jpg\",\"uploads\\/camry-2.jpeg\",\"uploads\\/camry-3.jpg\",\"uploads\\/camry-4.jpg\",\"uploads\\/camry-5.jpeg\"]', 1, '2024-07-19 04:05:51'),
(18, 'Car', 'Toyota', 'Prius', 1998, 'Compact', 'Hybrid', 'Manual', 5, 4, 5, 90.00, 'Etobicoke', '[\"uploads\\/prius-1.jpeg\",\"uploads\\/prius-2.jpeg\",\"uploads\\/prius-3.jpg\",\"uploads\\/prius-4.jpg\",\"uploads\\/prius-5.jpeg\",\"uploads\\/prius-6.jpeg\"]', 1, '2024-07-19 04:06:52'),
(19, 'Car', 'Honda', 'Civic', 2012, 'Sedan', 'Gas', 'Automatic', 4, 4, 5, 101.00, 'Halton Hills', '[\"uploads\\/civic-1.jpeg\",\"uploads\\/civic-2.webp\",\"uploads\\/civic-3.webp\",\"uploads\\/civic-4.jpeg\",\"uploads\\/civic-5.jpeg\"]', 1, '2024-07-19 04:07:52'),
(20, 'Car', 'BMW', '3 Series', 2008, 'SUV', 'Hybrid', 'Manual', 4, 4, 5, 200.00, 'Mississauga', '[\"uploads\\/BMW 3 Series -1.jpg\",\"uploads\\/BMW-3 Series-2.jpeg\",\"uploads\\/BMW-3 Series-3.jpg\"]', 1, '2024-07-19 04:08:37'),
(21, 'Car', 'Volkswagen', 'Golf', 2013, 'Hatchback', 'Electric', 'Manual', 4, 5, 4, 149.00, 'Kitchener', '[\"uploads\\/golf-1.jpeg\",\"uploads\\/golf-2.jpeg\",\"uploads\\/golf-3.jpg\"]', 1, '2024-07-19 04:09:22'),
(22, 'Pickup Truck', 'Ford', 'F-150', 2008, NULL, 'Hybrid', 'Manual', 5, 4, 5, 88.00, 'Kitchener', '[\"uploads\\/F-150-1.jpeg\",\"uploads\\/F-150-2.jpg\",\"uploads\\/F-150-3.jpg\",\"uploads\\/F-150-4.jpg\",\"uploads\\/F-150-5.png\"]', 1, '2024-07-19 04:09:59'),
(23, 'Pickup Truck', 'Chevrolet', 'Silverado 3500HD', 2023, NULL, 'Hybrid', 'Manual', 1, 5, 4, 99.00, 'Ajax', '[\"uploads\\/5.jpeg\",\"uploads\\/chevrolet_silverado_3500hd-1.jpg\",\"uploads\\/chevrolet_silverado_3500hd-2.jpg\",\"uploads\\/chevrolet_silverado_3500hd-3.jpg\",\"uploads\\/chevrolet_silverado_3500hd-4.jpg\"]', 1, '2024-07-19 04:10:40'),
(24, 'Motorcycle', 'Harley-Davidson', 'Sportster Iron 883', 2024, NULL, 'Electric', NULL, NULL, NULL, NULL, 150.00, 'Burlington', '[\"uploads\\/sportster1.jpeg\",\"uploads\\/sportster2.jpeg\",\"uploads\\/sportster3.jpeg\",\"uploads\\/sportster4.jpeg\"]', 1, '2024-07-19 04:14:41'),
(25, 'Motorcycle', 'Yamaha', 'YZF-R1', 2022, NULL, 'Gas', NULL, NULL, NULL, NULL, 69.00, 'Georgetown', '[\"uploads\\/yfz r1 3.jpg\",\"uploads\\/yzf r1 1jpg.jpg\",\"uploads\\/yzf r1 2.jpg\",\"uploads\\/yzf r1 4.jpg\"]', 1, '2024-07-19 04:15:40'),
(26, 'Motorcycle', 'Suzuki', 'GSX-R1000', 2014, NULL, 'Gas', NULL, NULL, NULL, NULL, 82.00, 'Ajax', '[\"uploads\\/gsxr1000 1.jpg\",\"uploads\\/gsxr1000 2.jpg\",\"uploads\\/gsxr1000 3.jpg\",\"uploads\\/gsxr1000 4.jpg\"]', 1, '2024-07-19 04:20:44'),
(27, 'Pickup Truck', 'Honda', 'Ridgeline', 2008, NULL, 'Hybrid', 'Manual', 5, 5, 9, 95.00, 'Oakville', '[\"uploads\\/HONDA RIDGELINE-1.jpg\",\"uploads\\/HONDA RIDGELINE-2.jpg\",\"uploads\\/HONDA RIDGELINE-3.jpg\",\"uploads\\/HONDA RIDGELINE-4.jpg\",\"uploads\\/HONDA RIDGELINE-5.jpg\"]', 1, '2024-07-19 04:21:35'),
(28, 'Motorcycle', 'Indian Motorcycle', 'Roadmaster', 2020, NULL, 'Gas', NULL, NULL, NULL, NULL, 99.00, 'Milton', '[\"uploads\\/radmaster2.jpg\",\"uploads\\/roadmaster1.jpg\",\"uploads\\/roadmaster3.jpg\",\"uploads\\/roadmaster4.jpg\"]', 1, '2024-07-19 17:05:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `car_features`
--
ALTER TABLE `car_features`
  ADD PRIMARY KEY (`feature_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `driverlicensedetail`
--
ALTER TABLE `driverlicensedetail`
  ADD PRIMARY KEY (`dl_detail_id`),
  ADD KEY `CustomerID` (`CustomerID`);

--
-- Indexes for table `motorcycle_features`
--
ALTER TABLE `motorcycle_features`
  ADD PRIMARY KEY (`feature_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `pickup_truck_features`
--
ALTER TABLE `pickup_truck_features`
  ADD PRIMARY KEY (`feature_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `car_features`
--
ALTER TABLE `car_features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `driverlicensedetail`
--
ALTER TABLE `driverlicensedetail`
  MODIFY `dl_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `motorcycle_features`
--
ALTER TABLE `motorcycle_features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pickup_truck_features`
--
ALTER TABLE `pickup_truck_features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`);

--
-- Constraints for table `car_features`
--
ALTER TABLE `car_features`
  ADD CONSTRAINT `car_features_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `car_features_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `driverlicensedetail`
--
ALTER TABLE `driverlicensedetail`
  ADD CONSTRAINT `driverlicensedetail_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`cust_id`);

--
-- Constraints for table `motorcycle_features`
--
ALTER TABLE `motorcycle_features`
  ADD CONSTRAINT `motorcycle_features_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `motorcycle_features_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `pickup_truck_features`
--
ALTER TABLE `pickup_truck_features`
  ADD CONSTRAINT `pickup_truck_features_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `pickup_truck_features_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
