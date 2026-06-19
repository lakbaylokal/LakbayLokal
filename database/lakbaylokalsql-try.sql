-- ============================================================================
-- LakbayLokal Database SQL Dump
-- For use with XAMPP/phpMyAdmin
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- DATABASE CREATION
-- ============================================================================

DROP DATABASE IF EXISTS `lakbaylokal`;
CREATE DATABASE IF NOT EXISTS `lakbaylokal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lakbaylokal`;

-- ============================================================================
-- TABLE: destinations
-- ============================================================================

DROP TABLE IF EXISTS `destinations`;
CREATE TABLE `destinations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `region` VARCHAR(100) NOT NULL,
  `emoji` VARCHAR(10),
  `tagline` VARCHAR(255),
  `description` TEXT,
  `price` INT DEFAULT 0,
  `price_from` INT DEFAULT 0,
  `image_url` VARCHAR(255),
  `archived` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: hotels
-- ============================================================================

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `destination_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `image_url` VARCHAR(255),
  `location` VARCHAR(200),
  `description` TEXT,
  `stars` INT DEFAULT 0,
  `price` INT DEFAULT 0,
  `rating` DECIMAL(3,1) DEFAULT 0.0,
  `reviews_count` INT DEFAULT 0,
  `checkin_time` TIME,
  `checkout_time` TIME,
  `archived` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: hotel_amenities
-- ============================================================================

DROP TABLE IF EXISTS `hotel_amenities`;
CREATE TABLE `hotel_amenities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `hotel_id` INT NOT NULL,
  `amenity_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE CASCADE,
  KEY `idx_hotel` (`hotel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: hotel_policies
-- ============================================================================

DROP TABLE IF EXISTS `hotel_policies`;
CREATE TABLE `hotel_policies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `hotel_id` INT NOT NULL,
  `policy` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE CASCADE,
  KEY `idx_hotel` (`hotel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: activities
-- ============================================================================

DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `destination_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `price` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE,
  KEY `idx_destination` (`destination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: users
-- ============================================================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `FName` VARCHAR(100) NOT NULL,
  `LName` VARCHAR(100) NOT NULL,
  `Email` VARCHAR(150) NOT NULL UNIQUE,
  `Password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: bookings
-- ============================================================================

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference_code` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT NULL,
  `guest_name` VARCHAR(150) NOT NULL,
  `guest_email` VARCHAR(180) NOT NULL,
  `destination_id` INT NOT NULL,
  `hotel_id` INT NOT NULL,
  `checkin_date` DATE NOT NULL,
  `checkout_date` DATE NOT NULL,
  `number_of_guests` INT NOT NULL DEFAULT 1,
  `number_of_rooms` INT NOT NULL DEFAULT 1,
  `subtotal` INT DEFAULT 0,
  `activities_total` INT DEFAULT 0,
  `tax_amount` INT DEFAULT 0,
  `total_price` INT NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `special_requests` TEXT,
  `status` VARCHAR(50) DEFAULT 'pending',
  `archived` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE RESTRICT,
  KEY `idx_user` (`user_id`),
  KEY `idx_destination` (`destination_id`),
  KEY `idx_hotel` (`hotel_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: booking_activities
-- ============================================================================

DROP TABLE IF EXISTS `booking_activities`;
CREATE TABLE `booking_activities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `activity_id` INT NOT NULL,
  `activity_name` VARCHAR(150) NOT NULL,
  `activity_price` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`activity_id`) REFERENCES `activities`(`id`) ON DELETE RESTRICT,
  KEY `idx_booking` (`booking_id`),
  KEY `idx_activity` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: payment_details
-- ============================================================================

DROP TABLE IF EXISTS `payment_details`;
CREATE TABLE `payment_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL UNIQUE,
  `gcash_number` VARCHAR(20),
  `gcash_account_name` VARCHAR(120),
  `card_holder_name` VARCHAR(120),
  `card_last_four` VARCHAR(4),
  `card_brand` VARCHAR(20),
  `payment_status` VARCHAR(50) DEFAULT 'pending',
  `payment_reference` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  KEY `idx_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATA: Users (Sample data - passwords are hashed with bcrypt)
-- ============================================================================

INSERT INTO `users` (`FName`, `LName`, `Email`, `Password`, `role`) VALUES
('Admin', 'User', 'admin@lakbaylokal.com', '$2y$10$6xXQLki/XEq43lanGNMsfee9QF4LmsOKg4GL00FTAnciMe.DzbTv6', 'admin'),
('Test', 'User', 'user@test.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'user'),
('Juan', 'Dela Cruz', 'juan@example.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'user');

-- ============================================================================
-- DATA: Destinations
-- id: 1=baguio, 2=vigan, 3=palawan, 4=cebu, 5=boracay, 6=siargao, 7=bukidnon, 8=camiguin
-- ============================================================================

INSERT INTO `destinations` (`name`, `region`, `emoji`, `tagline`, `description`, `price`, `price_from`) VALUES
('Baguio City', 'Luzon', '', 'The Summer Capital of the Philippines', 'Escape to the cool mountain air of Baguio City and discover pine forests, strawberry farms, and rich Igorot culture.', 4500, 2800),
('Vigan City', 'Luzon', '', 'UNESCO World Heritage City', 'Walk the cobblestone streets of a colonial-era city frozen in time, with kalesas, ancestral houses, and Ilocano cuisine.', 6500, 3500),
('Palawan', 'Luzon', '', 'Last Frontier of the Philippines', 'Crystal lagoons, secret beaches, WWII shipwrecks, and the world-famous Underground River await in paradise Palawan.', 8500, 6500),
('Cebu City', 'Visayas', '', 'The Queen City of the South', 'From canyoneering in Kawasan Falls to swimming with whale sharks in Oslob — Cebu offers it all.', 5500, 3200),
('Boracay Island', 'Visayas', '', 'World-Famous White Sand Beach', 'Powdery white sand, turquoise waters, and endless island adventures — Boracay is the Philippines\' crown jewel.', 7500, 5500),
('Siargao Island', 'Mindanao', '', 'Surfing Capital of the Philippines', 'Ride the world-famous Cloud 9 waves, hop between pristine islands, and soak in Siargao\'s laid-back surf culture.', 6800, 3700),
('Bukidnon', 'Mindanao', '', 'Thrills at Dahilayan Adventure Park', 'Experience the longest zipline in Asia, ATV rides through highland meadows, and the cool breeze of Mindanao\'s highlands.', 4500, 2100),
('Camiguin Island', 'Mindanao', '', 'Island Born of Fire', 'This tiny island has more volcanoes per square kilometer than anywhere on Earth, plus stunning waterfalls and turquoise springs.', 5800, 3500);

-- ============================================================================
-- DATA: Hotels
-- destination_id: 1=baguio, 2=vigan, 3=palawan, 4=cebu, 5=boracay, 6=siargao, 7=bukidnon, 8=camiguin
-- hotel id order: 1-3 baguio, 4-6 vigan, 7-9 palawan, 10-12 cebu, 13-15 boracay, 16-18 siargao, 19-21 bukidnon, 22-24 camiguin
-- ============================================================================

INSERT INTO `hotels` (`destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
-- Baguio (destination_id=1)
(1, 'Sotogrande Hotel Baguio', 'assets/pics/sotogrande.jpg', 'Session Road, Baguio City', 'A modern boutique hotel nestled in the heart of Baguio, offering luxurious rooms with stunning mountain views and world-class amenities.', 4, 5200, 4.6, 312, '14:00:00', '11:00:00'),
(1, 'Microtel by Wyndham Baguio', 'assets/pics/microtel3.jpg', 'Legarda Road, Baguio City', 'Comfortable and affordable rooms with reliable service, conveniently located near SM City Baguio and key tourist spots.', 3, 3600, 4.3, 205, '14:00:00', '12:00:00'),
(1, 'Travelite Express Hotel', 'assets/pics/travellite2.jpg', 'Magsaysay Avenue, Baguio City', 'A budget-friendly option with clean, cozy rooms ideal for solo travelers and backpackers exploring the City of Pines.', 2, 2800, 4.1, 148, '13:00:00', '11:00:00'),
-- Vigan (destination_id=2)
(2, 'Hotel Luna', 'assets/pics/hotelluna2.jpg', 'Crisologo St., Vigan City', 'An award-winning boutique hotel housed in a century-old ancestral mansion, blending heritage architecture with modern luxury on the famous Calle Crisologo.', 4, 5800, 4.8, 421, '14:00:00', '11:00:00'),
(2, 'Hotel Felicidad Vigan', 'assets/pics/hotelfelicidad2.jpg', 'Quirino Blvd, Vigan City', 'A charming heritage hotel offering comfortable rooms with period-inspired décor, located steps away from Vigan\'s famous plazas and churches.', 3, 4200, 4.4, 186, '14:00:00', '12:00:00'),
(2, 'Paradores de Vigan', 'assets/pics/paradores de.jpg', 'Mena Crisologo St., Vigan City', 'Experience the warmth of Ilocano hospitality in this cozy, family-run hotel set in a restored colonial building within the heritage zone.', 3, 3500, 4.2, 97, '13:00:00', '11:00:00'),
-- Palawan (destination_id=3)
(3, 'Seda Lio – El Nido', 'assets/pics/sedalio2.jpg', 'Lio Tourism Estate, El Nido, Palawan', 'A stunning eco-luxury resort set on the shores of El Nido\'s Lio Beach, offering sustainable luxury with panoramic views of the Bacuit Archipelago.', 5, 12500, 4.9, 534, '15:00:00', '12:00:00'),
(3, 'Hue Hotels – Puerto Princesa', 'assets/pics/huehotel2.jpg', 'Rizal Avenue, Puerto Princesa City', 'A vibrant, design-forward hotel in the heart of Puerto Princesa, offering colorful rooms, an excellent restaurant, and easy access to the city\'s attractions.', 4, 7200, 4.6, 298, '14:00:00', '12:00:00'),
(3, 'Two Seasons Coron Island Resort', 'assets/pics/two seasons.jpg', 'Malaroyroy Peninsula, Coron, Palawan', 'Perched on a private peninsula overlooking the famous Coron Bay, this overwater resort offers an intimate island experience with world-class diving access.', 4, 9800, 4.7, 187, '14:00:00', '11:00:00'),
-- Cebu (destination_id=4)
(4, 'Radisson Blu Cebu', 'assets/pics/radisson.jpg', 'Sergio Osmeña Blvd, Cebu City', 'A five-star landmark in the heart of Cebu City, offering elegant rooms, multiple dining venues, a rooftop pool, and a world-class spa.', 5, 9500, 4.7, 689, '15:00:00', '12:00:00'),
(4, 'Quest Hotel Cebu', 'assets/pics/questhotel.jpg', 'Archbishop Reyes Ave, Cebu City', 'A contemporary hotel known for its vibrant social spaces, spectacular pool area, and easy access to Cebu\'s business and leisure districts.', 4, 5500, 4.4, 412, '14:00:00', '12:00:00'),
(4, 'Bayfront Hotel Cebu', 'assets/pics/bayfront.jpg', 'Manalili St, Cebu City', 'A reliable mid-range option in Cebu City offering clean, comfortable rooms with great city views and close proximity to Carbon Market and Colon Street.', 3, 3200, 4.2, 253, '14:00:00', '11:00:00'),
-- Boracay (destination_id=5)
(5, 'Henann Crystal Sands Resort', 'assets/pics/boracay.jpg', 'Station 1, White Beach, Boracay', 'An iconic beachfront resort on the finest stretch of White Beach, offering breathtaking sunsets, multiple pools, and world-class island dining.', 5, 11800, 4.8, 876, '14:00:00', '12:00:00'),
(5, 'Fairways and Bluewater Boracay', 'assets/pics/boracay.jpg', 'Newcoast, Boracay Island', 'Boracay\'s only golf resort, offering sprawling tropical villas, championship fairways, and direct access to a pristine private beach.', 5, 14500, 4.9, 432, '14:00:00', '12:00:00'),
(5, 'La Sirena Resort', 'assets/pics/boracay.jpg', 'Station 3, White Beach, Boracay', 'A cozy, native-style boutique beachfront resort offering a peaceful escape on the quieter side of Boracay\'s world-famous shore.', 3, 5500, 4.3, 198, '13:00:00', '11:00:00'),
-- Siargao (destination_id=6)
(6, 'Nay Palad Hideaway', 'assets/pics/siargao.jpg', 'Pacifico, Siargao Island', 'An ultra-exclusive eco-luxury hideaway on Siargao\'s wild northern shore, offering just 10 private beachfront cottages with personalized butler service.', 5, 18000, 4.9, 156, '15:00:00', '12:00:00'),
(6, 'Kalinaw Resort', 'assets/pics/siargao.jpg', 'General Luna, Siargao Island', 'A tranquil boutique resort tucked among coconut palms near Cloud 9, offering stunning lagoon views and a genuine island atmosphere.', 4, 7500, 4.7, 289, '14:00:00', '12:00:00'),
(6, 'Cloud 9 Surf Resort', 'assets/pics/siargao.jpg', 'Cloud 9, General Luna, Siargao', 'The ultimate surfer\'s lodge located directly in front of the world-famous Cloud 9 boardwalk, featuring rustic rooms and an iconic view.', 3, 3700, 4.5, 342, '13:00:00', '11:00:00'),
-- Bukidnon (destination_id=7)
(7, 'Dahilayan Forest Park Resort', 'assets/pics/bukidno.jpg', 'Dahilayan, Manolo Fortich, Bukidnon', 'Stay right at the adventure park and wake up to misty mountain views. Direct access to all zipline and extreme adventure facilities.', 4, 4500, 4.5, 312, '14:00:00', '11:00:00'),
(7, 'Ultrawinds Mountain Resort', 'assets/pics/bukidno.jpg', 'Manolo Fortich, Bukidnon', 'A scenic highland resort with comfortable accommodations and stunning views of Bukidnon\'s pine-covered mountains.', 3, 3200, 4.3, 198, '13:00:00', '11:00:00'),
(7, 'Secret Haven Private Resort', 'assets/pics/bukidno.jpg', 'Bukidnon Province', 'An intimate private resort surrounded by nature, perfect for group getaways with exclusive use of the property.', 3, 2100, 4.1, 145, '13:00:00', '11:00:00'),
-- Camiguin (destination_id=8)
(8, 'Bintana sa Paraiso', 'assets/pics/camiguin.jpg', 'Mambajao, Camiguin Island', 'A picturesque resort with sea-facing cottages and direct beach access, offering a genuine island paradise experience in Camiguin.', 4, 5800, 4.7, 203, '14:00:00', '11:00:00'),
(8, 'Paras Beach Resort', 'assets/pics/camiguin.jpg', 'Guinsiliban, Camiguin Island', 'A beloved beachside resort known for its warm hospitality, well-kept grounds, and stunning views of the island\'s volcanic peaks.', 3, 4200, 4.4, 178, '14:00:00', '12:00:00'),
(8, 'Balai sa Baibai', 'assets/pics/camiguin.jpg', 'Mambajao, Camiguin Island', 'A cozy family-run beachside inn offering affordable comfort and genuine Camiguinon hospitality just steps from the beach.', 3, 3500, 4.2, 134, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotel Amenities
-- hotel_id: 1=sotogrande-baguio, 2=microtel-baguio, 3=travelite-baguio,
--           4=hotel-luna, 5=hotel-felicidad, 6=paradores-vigan,
--           7=seda-lio, 8=hue-hotels-pp, 9=two-seasons-coron,
--           10=radisson-blu-cebu, 11=quest-hotel-cebu, 12=bayfront-cebu,
--           13=henann-crystal, 14=fairways-boracay, 15=la-sirena-boracay,
--           16=nay-palad-siargao, 17=kalinaw-siargao, 18=cloud9-resort-siargao,
--           19=dahilayan-resort, 20=ultrawinds-resort, 21=secret-haven-bukidnon,
--           22=bintana-paraiso, 23=paras-beach, 24=balai-baibai
-- ============================================================================

INSERT INTO `hotel_amenities` (`hotel_id`, `amenity_name`) VALUES
-- Sotogrande Hotel Baguio (id=1)
(1, 'Free WiFi'), (1, 'Restaurant'), (1, 'Pool'), (1, 'Gym'), (1, 'Parking'), (1, 'Room Service'),
-- Microtel Baguio (id=2)
(2, 'Free WiFi'), (2, 'Breakfast'), (2, 'Parking'), (2, 'Room Service'), (2, 'Laundry'),
-- Travelite Express Hotel (id=3)
(3, 'Free WiFi'), (3, 'Air Conditioning'), (3, '24/7 Front Desk'),
-- Hotel Luna (id=4)
(4, 'Free WiFi'), (4, 'Rooftop Pool'), (4, 'Art Gallery'), (4, 'Restaurant'), (4, 'Room Service'), (4, 'Concierge'),
-- Hotel Felicidad Vigan (id=5)
(5, 'Free WiFi'), (5, 'Breakfast Included'), (5, 'Parking'), (5, 'Tour Desk'),
-- Paradores de Vigan (id=6)
(6, 'Free WiFi'), (6, 'Courtyard Garden'), (6, 'Bike Rental'), (6, 'Breakfast'),
-- Seda Lio (id=7)
(7, 'Infinity Pool'), (7, 'Beach Access'), (7, 'Spa'), (7, 'Restaurant'), (7, 'Water Sports'), (7, 'Free WiFi'),
-- Hue Hotels (id=8)
(8, 'Pool'), (8, 'Restaurant'), (8, 'Free WiFi'), (8, 'Gym'), (8, 'Airport Shuttle'), (8, 'Kids Club'),
-- Two Seasons Coron (id=9)
(9, 'Overwater Villas'), (9, 'Dive Center'), (9, 'Kayaking'), (9, 'Restaurant'), (9, 'Snorkeling Gear'), (9, 'Free WiFi'),
-- Radisson Blu Cebu (id=10)
(10, 'Rooftop Pool'), (10, 'Spa'), (10, 'Multiple Restaurants'), (10, 'Free WiFi'), (10, 'Business Center'), (10, 'Gym'),
-- Quest Hotel Cebu (id=11)
(11, 'Pool'), (11, 'Free WiFi'), (11, 'Restaurant'), (11, 'Gym'), (11, 'Parking'), (11, 'Event Halls'),
-- Bayfront Hotel Cebu (id=12)
(12, 'Free WiFi'), (12, 'Air Conditioning'), (12, 'Restaurant'), (12, '24/7 Front Desk'), (12, 'Laundry Service'),
-- Henann Crystal Sands (id=13)
(13, 'Beachfront'), (13, '3 Pools'), (13, 'Water Sports'), (13, 'Multiple Restaurants'), (13, 'Spa'), (13, 'Free WiFi'),
-- Fairways and Bluewater (id=14)
(14, 'Golf Course'), (14, 'Private Beach'), (14, 'Spa'), (14, 'Multiple Pools'), (14, 'Tennis'), (14, 'Free WiFi'),
-- La Sirena Resort (id=15)
(15, 'Beachfront Dining'), (15, 'Free WiFi'), (15, 'Eco-Friendly Rooms'), (15, 'Bar & Lounge'),
-- Nay Palad Hideaway (id=16)
(16, 'Private Beach'), (16, 'Infinity Pool'), (16, 'Organic Restaurant'), (16, 'Surfing Access'), (16, 'Spa'), (16, 'Butler Service'),
-- Kalinaw Resort (id=17)
(17, 'Lagoon Views'), (17, 'Pool'), (17, 'Restaurant'), (17, 'Surf Board Rental'), (17, 'Free WiFi'), (17, 'Bicycle Rental'),
-- Cloud 9 Surf Resort (id=18)
(18, 'Beachfront Boardwalk Access'), (18, 'Surf School'), (18, 'Restaurant'), (18, 'Free WiFi'), (18, 'Hammocks'),
-- Dahilayan Forest Park Resort (id=19)
(19, 'Free WiFi'), (19, 'Restaurant'), (19, 'Adventure Park Access'), (19, 'Parking'), (19, 'Room Service'),
-- Ultrawinds Mountain Resort (id=20)
(20, 'Free WiFi'), (20, 'Restaurant'), (20, 'Mountain View'), (20, 'Parking'), (20, 'Bonfire Area'),
-- Secret Haven Private Resort (id=21)
(21, 'Private Pool'), (21, 'Kitchen Facilities'), (21, 'Free WiFi'), (21, 'BBQ Pit'),
-- Bintana sa Paraiso (id=22)
(22, 'Beach Access'), (22, 'Restaurant'), (22, 'Free WiFi'), (22, 'Snorkeling Gear'), (22, 'Tour Desk'), (22, 'Kayaking'),
-- Paras Beach Resort (id=23)
(23, 'Pool'), (23, 'Restaurant'), (23, 'Free WiFi'), (23, 'Dive Shop'), (23, 'Function Room'),
-- Balai sa Baibai (id=24)
(24, 'Free WiFi'), (24, 'Beach Access'), (24, 'Breakfast Available'), (24, 'Communal Kitchen'), (24, 'Hammocks');

-- ============================================================================
-- DATA: Hotel Policies
-- ============================================================================

INSERT INTO `hotel_policies` (`hotel_id`, `policy`) VALUES
-- Sotogrande Baguio (id=1)
(1, 'Government-issued ID required'), (1, 'No pets allowed'), (1, 'Extra person charges apply'),
-- Microtel Baguio (id=2)
(2, 'Check-in age: 18+'), (2, 'Cash or card accepted'), (2, 'Cancellation: 24hrs prior'),
-- Travelite Baguio (id=3)
(3, 'No smoking in rooms'), (3, 'Quiet hours: 10PM–7AM'),
-- Hotel Luna (id=4)
(4, 'Government-issued ID required'), (4, 'No pets allowed'), (4, 'Cancellation: 48hrs prior'),
-- Hotel Felicidad (id=5)
(5, 'Breakfast served 6AM–9AM'), (5, 'Extra bed available on request'),
-- Paradores de Vigan (id=6)
(6, 'No pets'), (6, 'Cash payment preferred'), (6, 'Early check-in subject to availability'),
-- Seda Lio (id=7)
(7, 'Adults only: 18+'), (7, 'No outside food/drinks'), (7, 'Strictly no smoking'),
-- Hue Hotels (id=8)
(8, 'Children under 12 stay free'), (8, 'Credit cards accepted'), (8, 'Tour desk available'),
-- Two Seasons Coron (id=9)
(9, 'Minimum stay: 2 nights'), (9, 'Dive packages available'), (9, 'Airport transfer included'),
-- Radisson Blu Cebu (id=10)
(10, 'Government-issued ID required'), (10, 'Check-in age: 18+'), (10, 'Pets not allowed'),
-- Quest Hotel Cebu (id=11)
(11, 'Children welcome'), (11, 'Cancellation: 24hrs prior'), (11, 'Airport shuttle available'),
-- Bayfront Hotel Cebu (id=12)
(12, 'No pets'), (12, 'Cash or card'), (12, 'Quiet hours: 11PM'),
-- Henann Crystal Sands (id=13)
(13, 'No day-tour guests'), (13, 'Minimum age for alcohol: 18+'), (13, 'Beach access 24/7'),
-- Fairways and Bluewater (id=14)
(14, 'Smart casual dress code in golf course'), (14, 'Private beach strictly for guests'),
-- La Sirena Resort (id=15)
(15, 'Eco-friendly policy: Eco-bricks encourage'), (15, 'No plastics on beachfront'),
-- Nay Palad Hideaway (id=16)
(16, 'Adults only: 12+'), (16, 'All-inclusive available'), (16, 'Advance booking required'),
-- Kalinaw Resort (id=17)
(17, 'No loud music after 10PM'), (17, 'Surfboard storage available'),
-- Cloud 9 Surf Resort (id=18)
(18, 'Surfing instructors available on-site'), (18, 'Cash only for rentals'),
-- Dahilayan Forest Park Resort (id=19)
(19, 'Government-issued ID required'), (19, 'No pets'), (19, 'Height restrictions apply for some activities'),
-- Ultrawinds Mountain Resort (id=20)
(20, 'Cash payment preferred'), (20, 'Group rates available'), (20, 'Quiet hours: 10PM'),
-- Secret Haven Private Resort (id=21)
(21, 'Security deposit required upon check-in'), (21, 'Event hosting subject to separate fee'),
-- Bintana sa Paraiso (id=22)
(22, 'Government-issued ID required'), (22, 'Cash only for on-site bar'),
-- Paras Beach Resort (id=23)
(23, 'No pets allowed'), (23, 'Pool hours: 7AM–10PM'),
-- Balai sa Baibai (id=24)
(24, 'Cash only'), (24, 'Breakfast not included (available for fee)'), (24, 'No smoking in rooms');

-- ============================================================================
-- DATA: Activities
-- destination_id: 1=baguio, 2=vigan, 3=palawan, 4=cebu, 5=boracay, 6=siargao, 7=bukidnon, 8=camiguin
-- ============================================================================

INSERT INTO `activities` (`destination_id`, `name`, `price`) VALUES
-- Baguio (destination_id=1)
(1, 'Strawberry Picking at La Trinidad Farm', 250),
(1, 'BenCab Museum Gallery Tour', 200),
(1, 'Tree Top Adventure – Camp John Hay', 400),
(1, 'Igorot Stone Kingdom Exploration', 150),
-- Vigan (destination_id=2)
(2, 'Calesa Ride around Calle Crisologo', 250),
(2, 'Pagburnayan Jar Factory Pottery Making', 300),
(2, 'Vigan Museum / Syquia Mansion Tour', 180),
-- Palawan (destination_id=3)
(3, 'El Nido Tour A – Lagoons & Islands', 1200),
(3, 'Puerto Princesa Underground River Tour', 2750),
(3, 'Coron Shipwreck & Snorkeling Tour', 1600),
(3, 'Wildlife Safari at Calauit Sanctuary', 2500),
-- Cebu (destination_id=4)
(4, 'Kawasan Falls Canyoneering', 1500),
(4, 'Temple of Leah Tour', 100),
(4, 'Oslob Whale Shark Watching', 500),
-- Boracay (destination_id=5)
(5, 'Island Hopping with Buffet Lunch', 1000),
(5, 'Helmet Diving Experience', 800),
(5, 'Parasailing at Sunset', 1500),
-- Siargao (destination_id=6)
(6, 'Tri-Island Hopping (Naked, Daku, Guyam)', 1200),
(6, 'Sohoton Cove & Jellyfish Sanctuary Tour', 2000),
(6, 'Basic Surf Lesson (1 Hour with Instructor)', 500),
-- Bukidnon (destination_id=7)
(7, 'Dual Zipline Adventure', 600),
(7, 'ATV Forest Trail Ride', 1200),
(7, 'Anicycle Air Bike Experience', 350),
-- Camiguin (destination_id=8)
(8, 'Island Hopping', 2500),
(8, 'Waterfalls Tour', 3500),
(8, 'Scuba Diving', 2800);

-- ============================================================================
-- INDEXES
-- ============================================================================

CREATE INDEX `idx_hotels_destination` ON `hotels` (`destination_id`);
CREATE INDEX `idx_amenities_hotel` ON `hotel_amenities` (`hotel_id`);
CREATE INDEX `idx_policies_hotel` ON `hotel_policies` (`hotel_id`);
CREATE INDEX `idx_activities_destination` ON `activities` (`destination_id`);

-- ============================================================================
-- COMPLETION
-- ============================================================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;