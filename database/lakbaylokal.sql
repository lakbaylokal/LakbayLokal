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
  `id` VARCHAR(50) PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `region` VARCHAR(100) NOT NULL,
  `emoji` VARCHAR(10),
  `tagline` VARCHAR(255),
  `description` TEXT,
  `price` INT DEFAULT 0,
  `price_from` INT DEFAULT 0,
  `image_url` VARCHAR(255),
  `gradient_bg` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: hotels
-- ============================================================================

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id` VARCHAR(100) PRIMARY KEY,
  `destination_id` VARCHAR(50) NOT NULL,
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
  `hotel_id` VARCHAR(100) NOT NULL,
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
  `hotel_id` VARCHAR(100) NOT NULL,
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
  `destination_id` VARCHAR(50) NOT NULL,
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
  `Mname` VARCHAR(100),
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
  `destination_id` VARCHAR(50) NOT NULL,
  `hotel_id` VARCHAR(100) NOT NULL,
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

INSERT INTO `users` (`FName`, `LName`, `Mname`, `Email`, `Password`, `role`) VALUES
('Admin', 'User', 'Test', 'admin@lakbaylokal.com', '$2y$10$6xXQLki/XEq43lanGNMsfee9QF4LmsOKg4GL00FTAnciMe.DzbTv6', 'admin'),
('Test', 'User', 'Sample', 'user@test.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'user'),
('Juan', 'Dela Cruz', 'Sample', 'juan@example.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'user');

-- ============================================================================
-- DATA: Destinations (Eksaktong sunod sa data.php)
-- ============================================================================

INSERT INTO `destinations` (`id`, `name`, `region`, `emoji`, `tagline`, `description`, `price`, `price_from`, `gradient_bg`) VALUES
('baguio', 'Baguio City', 'Luzon', '', 'The Summer Capital of the Philippines', 'Escape to the cool mountain air of Baguio City and discover pine forests, strawberry farms, and rich Igorot culture.', 4500, 2800, 'linear-gradient(135deg, rgba(109,177,197,0.75), rgba(0,0,0,0.22)), url(\'assets/pics/Baguio.jpg\') center/cover no-repeat'),
('vigan', 'Vigan City', 'Luzon', '', 'UNESCO World Heritage City', 'Walk the cobblestone streets of a colonial-era city frozen in time, with kalesas, ancestral houses, and Ilocano cuisine.', 6500, 3500, 'linear-gradient(135deg, rgba(0,0,0,0.34), rgba(0,0,0,0.05)), url(\'assets/pics/vigan.jpg\') center/cover no-repeat'),
('palawan', 'Palawan', 'Luzon', '', 'Last Frontier of the Philippines', 'Crystal lagoons, secret beaches, WWII shipwrecks, and the world-famous Underground River await in paradise Palawan.', 8500, 6500, 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.1)), url(\'assets/pics/palawan.jpg\') center/cover no-repeat'),
('cebu', 'Cebu City', 'Visayas', '', 'The Queen City of the South', 'From canyoneering in Kawasan Falls to swimming with whale sharks in Oslob — Cebu offers it all.', 5500, 3200, 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(\'assets/pics/Cebu2.jpg\') center/cover no-repeat'),
('boracay', 'Boracay Island', 'Visayas', '', 'World-Famous White Sand Beach', 'Powdery white sand, turquoise waters, and endless island adventures — Boracay is the Philippines\' crown jewel.', 7500, 5500, 'linear-gradient(135deg, rgba(0,0,0,0.22), rgba(0,0,0,0.08)), url(\'assets/pics/boracay.jpg\') center/cover no-repeat'),
('siargao', 'Siargao Island', 'Mindanao', '', 'Surfing Capital of the Philippines', 'Ride the world-famous Cloud 9 waves, hop between pristine islands, and soak in Siargao\'s laid-back surf culture.', 6800, 3700, 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(\'assets/pics/siargao.jpg\') center/cover no-repeat'),
('bukidnon', 'Bukidnon', 'Mindanao', '', 'Thrills at Dahilayan Adventure Park', 'Experience the longest zipline in Asia, ATV rides through highland meadows, and the cool breeze of Mindanao\'s highlands.', 4500, 2100, 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.06)), url(\'assets/pics/bukidno.jpg\') center/cover no-repeat'),
('camiguin', 'Camiguin Island', 'Mindanao', '', 'Island Born of Fire', 'This tiny island has more volcanoes per square kilometer than anywhere on Earth, plus stunning waterfalls and turquoise springs.', 5800, 3500, 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(\'assets/pics/camiguin.jpg\') center/cover no-repeat');

-- ============================================================================
-- DATA: Hotels (Eksaktong sunod sa data.php kasama ang mga naitamang IDs at Pics)
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
-- Baguio
('sotogrande-baguio', 'baguio', 'Sotogrande Hotel Baguio', 'assets/pics/sotogrande.jpg', 'Session Road, Baguio City', 'A modern boutique hotel nestled in the heart of Baguio, offering luxurious rooms with stunning mountain views and world-class amenities.', 4, 5200, 4.6, 312, '14:00:00', '11:00:00'),
('microtel-baguio', 'baguio', 'Microtel by Wyndham Baguio', 'assets/pics/microtel3.jpg', 'Legarda Road, Baguio City', 'Comfortable and affordable rooms with reliable service, conveniently located near SM City Baguio and key tourist spots.', 3, 3600, 4.3, 205, '14:00:00', '12:00:00'),
('travelite-baguio', 'baguio', 'Travelite Express Hotel', 'assets/pics/travellite2.jpg', 'Magsaysay Avenue, Baguio City', 'A budget-friendly option with clean, cozy rooms ideal for solo travelers and backpackers exploring the City of Pines.', 2, 2800, 4.1, 148, '13:00:00', '11:00:00'),
-- Vigan
('hotel-luna-vigan', 'vigan', 'Hotel Luna', 'assets/pics/hotelluna2.jpg', 'Crisologo St., Vigan City', 'An award-winning boutique hotel housed in a century-old ancestral mansion, blending heritage architecture with modern luxury on the famous Calle Crisologo.', 4, 5800, 4.8, 421, '14:00:00', '11:00:00'),
('hotel-felicidad', 'vigan', 'Hotel Felicidad Vigan', 'assets/pics/hotelfelicidad2.jpg', 'Quirino Blvd, Vigan City', 'A charming heritage hotel offering comfortable rooms with period-inspired décor, located steps away from Vigan\'s famous plazas and churches.', 3, 4200, 4.4, 186, '14:00:00', '12:00:00'),
('paradores-vigan', 'vigan', 'Paradores de Vigan', 'assets/pics/paradores de.jpg', 'Mena Crisologo St., Vigan City', 'Experience the warmth of Ilocano hospitality in this cozy, family-run hotel set in a restored colonial building within the heritage zone.', 3, 3500, 4.2, 97, '13:00:00', '11:00:00'),
-- Palawan
('seda-lio', 'palawan', 'Seda Lio – El Nido', 'assets/pics/sedalio2.jpg', 'Lio Tourism Estate, El Nido, Palawan', 'A stunning eco-luxury resort set on the shores of El Nido\'s Lio Beach, offering sustainable luxury with panoramic views of the Bacuit Archipelago.', 5, 12500, 4.9, 534, '15:00:00', '12:00:00'),
('hue-hotels-pp', 'palawan', 'Hue Hotels – Puerto Princesa', 'assets/pics/huehotel2.jpg', 'Rizal Avenue, Puerto Princesa City', 'A vibrant, design-forward hotel in the heart of Puerto Princesa, offering colorful rooms, an excellent restaurant, and easy access to the city\'s attractions.', 4, 7200, 4.6, 298, '14:00:00', '12:00:00'),
('two-seasons-coron', 'palawan', 'Two Seasons Coron Island Resort', 'assets/pics/two seasons.jpg', 'Malaroyroy Peninsula, Coron, Palawan', 'Perched on a private peninsula overlooking the famous Coron Bay, this overwater resort offers an intimate island experience with world-class diving access.', 4, 9800, 4.7, 187, '14:00:00', '11:00:00'),
-- Cebu (Inayos mula radisson-cebu/sunburst para tumugma sa data.php)
('radisson-blu-cebu', 'cebu', 'Radisson Blu Cebu', 'assets/pics/radisson.jpg', 'Sergio Osmeña Blvd, Cebu City', 'A five-star landmark in the heart of Cebu City, offering elegant rooms, multiple dining venues, a rooftop pool, and a world-class spa.', 5, 9500, 4.7, 689, '15:00:00', '12:00:00'),
('quest-hotel-cebu', 'cebu', 'Quest Hotel Cebu', 'assets/pics/questhotel.jpg', 'Archbishop Reyes Ave, Cebu City', 'A contemporary hotel known for its vibrant social spaces, spectacular pool area, and easy access to Cebu\'s business and leisure districts.', 4, 5500, 4.4, 412, '14:00:00', '12:00:00'),
('bayfront-cebu', 'cebu', 'Bayfront Hotel Cebu', 'assets/pics/bayfront.jpg', 'Manalili St, Cebu City', 'A reliable mid-range option in Cebu City offering clean, comfortable rooms with great city views and close proximity to Carbon Market and Colon Street.', 3, 3200, 4.2, 253, '14:00:00', '11:00:00'),
-- Boracay (Inayos mula shangri-la/henann para tumugma sa assets/pics/boracay.jpg)
('henann-crystal', 'boracay', 'Henann Crystal Sands Resort', 'assets/pics/boracay.jpg', 'Station 1, White Beach, Boracay', 'An iconic beachfront resort on the finest stretch of White Beach, offering breathtaking sunsets, multiple pools, and world-class island dining.', 5, 11800, 4.8, 876, '14:00:00', '12:00:00'),
('fairways-boracay', 'boracay', 'Fairways and Bluewater Boracay', 'assets/pics/boracay.jpg', 'Newcoast, Boracay Island', 'Boracay\'s only golf resort, offering sprawling tropical villas, championship fairways, and direct access to a pristine private beach.', 5, 14500, 4.9, 432, '14:00:00', '12:00:00'),
('la-sirena-boracay', 'boracay', 'La Sirena Resort', 'assets/pics/boracay.jpg', 'Station 3, White Beach, Boracay', 'A cozy, native-style boutique beachfront resort offering a peaceful escape on the quieter side of Boracay\'s world-famous shore.', 3, 5500, 4.3, 198, '13:00:00', '11:00:00'),
-- Siargao (Inayos para tumugma sa assets/pics/siargao.jpg)
('nay-palad-siargao', 'siargao', 'Nay Palad Hideaway', 'assets/pics/siargao.jpg', 'Pacifico, Siargao Island', 'An ultra-exclusive eco-luxury hideaway on Siargao\'s wild northern shore, offering just 10 private beachfront cottages with personalized butler service.', 5, 18000, 4.9, 156, '15:00:00', '12:00:00'),
('kalinaw-siargao', 'siargao', 'Kalinaw Resort', 'assets/pics/siargao.jpg', 'General Luna, Siargao Island', 'A tranquil boutique resort tucked among coconut palms near Cloud 9, offering stunning lagoon views and a genuine island atmosphere.', 4, 7500, 4.7, 289, '14:00:00', '12:00:00'),
('cloud9-resort-siargao', 'siargao', 'Cloud 9 Surf Resort', 'assets/pics/siargao.jpg', 'Cloud 9, General Luna, Siargao', 'The ultimate surfer\'s lodge located directly in front of the world-famous Cloud 9 boardwalk, featuring rustic rooms and an iconic view.', 3, 3700, 4.5, 342, '13:00:00', '11:00:00'),
-- Bukidnon
('dahilayan-resort', 'bukidnon', 'Dahilayan Forest Park Resort', 'assets/pics/bukidno.jpg', 'Dahilayan, Manolo Fortich, Bukidnon', 'Stay right at the adventure park and wake up to misty mountain views. Direct access to all zipline and extreme adventure facilities.', 4, 4500, 4.5, 312, '14:00:00', '11:00:00'),
('ultrawinds-resort', 'bukidnon', 'Ultrawinds Mountain Resort', 'assets/pics/bukidno.jpg', 'Manolo Fortich, Bukidnon', 'A scenic highland resort with comfortable accommodations and stunning views of Bukidnon\'s pine-covered mountains.', 3, 3200, 4.3, 198, '13:00:00', '11:00:00'),
('secret-haven-bukidnon', 'bukidnon', 'Secret Haven Private Resort', 'assets/pics/bukidno.jpg', 'Bukidnon Province', 'An intimate private resort surrounded by nature, perfect for group getaways with exclusive use of the property.', 3, 2100, 4.1, 145, '13:00:00', '11:00:00'),
-- Camiguin
('bintana-paraiso', 'camiguin', 'Bintana sa Paraiso', 'assets/pics/camiguin.jpg', 'Mambajao, Camiguin Island', 'A picturesque resort with sea-facing cottages and direct beach access, offering a genuine island paradise experience in Camiguin.', 4, 5800, 4.7, 203, '14:00:00', '11:00:00'),
('paras-beach', 'camiguin', 'Paras Beach Resort', 'assets/pics/camiguin.jpg', 'Guinsiliban, Camiguin Island', 'A beloved beachside resort known for its warm hospitality, well-kept grounds, and stunning views of the island\'s volcanic peaks.', 3, 4200, 4.4, 178, '14:00:00', '12:00:00'),
('balai-baibai', 'camiguin', 'Balai sa Baibai', 'assets/pics/camiguin.jpg', 'Mambajao, Camiguin Island', 'A cozy family-run beachside inn offering affordable comfort and genuine Camiguinon hospitality just steps from the beach.', 3, 3500, 4.2, 134, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotel Amenities (Eksaktong sunod sa data.php)
-- ============================================================================

INSERT INTO `hotel_amenities` (`hotel_id`, `amenity_name`) VALUES
-- Sotogrande Hotel Baguio
('sotogrande-baguio', 'Free WiFi'), ('sotogrande-baguio', 'Restaurant'), ('sotogrande-baguio', 'Pool'), ('sotogrande-baguio', 'Gym'), ('sotogrande-baguio', 'Parking'), ('sotogrande-baguio', 'Room Service'),
-- Microtel Baguio
('microtel-baguio', 'Free WiFi'), ('microtel-baguio', 'Breakfast'), ('microtel-baguio', 'Parking'), ('microtel-baguio', 'Room Service'), ('microtel-baguio', 'Laundry'),
-- Travelite Express Hotel
('travelite-baguio', 'Free WiFi'), ('travelite-baguio', 'Air Conditioning'), ('travelite-baguio', '24/7 Front Desk'),
-- Hotel Luna
('hotel-luna-vigan', 'Free WiFi'), ('hotel-luna-vigan', 'Rooftop Pool'), ('hotel-luna-vigan', 'Art Gallery'), ('hotel-luna-vigan', 'Restaurant'), ('hotel-luna-vigan', 'Room Service'), ('hotel-luna-vigan', 'Concierge'),
-- Hotel Felicidad Vigan
('hotel-felicidad', 'Free WiFi'), ('hotel-felicidad', 'Breakfast Included'), ('hotel-felicidad', 'Parking'), ('hotel-felicidad', 'Tour Desk'),
-- Paradores de Vigan
('paradores-vigan', 'Free WiFi'), ('paradores-vigan', 'Courtyard Garden'), ('paradores-vigan', 'Bike Rental'), ('paradores-vigan', 'Breakfast'),
-- Seda Lio
('seda-lio', 'Infinity Pool'), ('seda-lio', 'Beach Access'), ('seda-lio', 'Spa'), ('seda-lio', 'Restaurant'), ('seda-lio', 'Water Sports'), ('seda-lio', 'Free WiFi'),
-- Hue Hotels
('hue-hotels-pp', 'Pool'), ('hue-hotels-pp', 'Restaurant'), ('hue-hotels-pp', 'Free WiFi'), ('hue-hotels-pp', 'Gym'), ('hue-hotels-pp', 'Airport Shuttle'), ('hue-hotels-pp', 'Kids Club'),
-- Two Seasons Coron
('two-seasons-coron', 'Overwater Villas'), ('two-seasons-coron', 'Dive Center'), ('two-seasons-coron', 'Kayaking'), ('two-seasons-coron', 'Restaurant'), ('two-seasons-coron', 'Snorkeling Gear'), ('two-seasons-coron', 'Free WiFi'),
-- Radisson Blu Cebu
('radisson-blu-cebu', 'Rooftop Pool'), ('radisson-blu-cebu', 'Spa'), ('radisson-blu-cebu', 'Multiple Restaurants'), ('radisson-blu-cebu', 'Free WiFi'), ('radisson-blu-cebu', 'Business Center'), ('radisson-blu-cebu', 'Gym'),
-- Quest Hotel Cebu
('quest-hotel-cebu', 'Pool'), ('quest-hotel-cebu', 'Free WiFi'), ('quest-hotel-cebu', 'Restaurant'), ('quest-hotel-cebu', 'Gym'), ('quest-hotel-cebu', 'Parking'), ('quest-hotel-cebu', 'Event Halls'),
-- Bayfront Hotel Cebu
('bayfront-cebu', 'Free WiFi'), ('bayfront-cebu', 'Air Conditioning'), ('bayfront-cebu', 'Restaurant'), ('bayfront-cebu', '24/7 Front Desk'), ('bayfront-cebu', 'Laundry Service'),
-- Henann Crystal Sands
('henann-crystal', 'Beachfront'), ('henann-crystal', '3 Pools'), ('henann-crystal', 'Water Sports'), ('henann-crystal', 'Multiple Restaurants'), ('henann-crystal', 'Spa'), ('henann-crystal', 'Free WiFi'),
-- Fairways and Bluewater
('fairways-boracay', 'Golf Course'), ('fairways-boracay', 'Private Beach'), ('fairways-boracay', 'Spa'), ('fairways-boracay', 'Multiple Pools'), ('fairways-boracay', 'Tennis'), ('fairways-boracay', 'Free WiFi'),
-- La Sirena Resort
('la-sirena-boracay', 'Beachfront Dining'), ('la-sirena-boracay', 'Free WiFi'), ('la-sirena-boracay', 'Eco-Friendly Rooms'), ('la-sirena-boracay', 'Bar & Lounge'),
-- Nay Palad Hideaway
('nay-palad-siargao', 'Private Beach'), ('nay-palad-siargao', 'Infinity Pool'), ('nay-palad-siargao', 'Organic Restaurant'), ('nay-palad-siargao', 'Surfing Access'), ('nay-palad-siargao', 'Spa'), ('nay-palad-siargao', 'Butler Service'),
-- Kalinaw Resort
('kalinaw-siargao', 'Lagoon Views'), ('kalinaw-siargao', 'Pool'), ('kalinaw-siargao', 'Restaurant'), ('kalinaw-siargao', 'Surf Board Rental'), ('kalinaw-siargao', 'Free WiFi'), ('kalinaw-siargao', 'Bicycle Rental'),
-- Cloud 9 Surf Resort
('cloud9-resort-siargao', 'Beachfront Boardwalk Access'), ('cloud9-resort-siargao', 'Surf School'), ('cloud9-resort-siargao', 'Restaurant'), ('cloud9-resort-siargao', 'Free WiFi'), ('cloud9-resort-siargao', 'Hammocks'),
-- Dahilayan Forest Park Resort
('dahilayan-resort', 'Free WiFi'), ('dahilayan-resort', 'Restaurant'), ('dahilayan-resort', 'Adventure Park Access'), ('dahilayan-resort', 'Parking'), ('dahilayan-resort', 'Room Service'),
-- Ultrawinds Mountain Resort
('ultrawinds-resort', 'Free WiFi'), ('ultrawinds-resort', 'Restaurant'), ('ultrawinds-resort', 'Mountain View'), ('ultrawinds-resort', 'Parking'), ('ultrawinds-resort', 'Bonfire Area'),
-- Secret Haven Private Resort
('secret-haven-bukidnon', 'Private Pool'), ('secret-haven-bukidnon', 'Kitchen Facilities'), ('secret-haven-bukidnon', 'Free WiFi'), ('secret-haven-bukidnon', 'BBQ Pit'),
-- Bintana sa Paraiso
('bintana-paraiso', 'Beach Access'), ('bintana-paraiso', 'Restaurant'), ('bintana-paraiso', 'Free WiFi'), ('bintana-paraiso', 'Snorkeling Gear'), ('bintana-paraiso', 'Tour Desk'), ('bintana-paraiso', 'Kayaking'),
-- Paras Beach Resort
('paras-beach', 'Pool'), ('paras-beach', 'Restaurant'), ('paras-beach', 'Free WiFi'), ('paras-beach', 'Dive Shop'), ('paras-beach', 'Function Room'),
-- Balai sa Baibai
('balai-baibai', 'Free WiFi'), ('balai-baibai', 'Beach Access'), ('balai-baibai', 'Breakfast Available'), ('balai-baibai', 'Communal Kitchen'), ('balai-baibai', 'Hammocks');

-- ============================================================================
-- DATA: Hotel Policies (Eksaktong sunod sa data.php)
-- ============================================================================

INSERT INTO `hotel_policies` (`hotel_id`, `policy`) VALUES
-- Sotogrande Baguio
('sotogrande-baguio', 'Government-issued ID required'), ('sotogrande-baguio', 'No pets allowed'), ('sotogrande-baguio', 'Extra person charges apply'),
-- Microtel Baguio
('microtel-baguio', 'Check-in age: 18+'), ('microtel-baguio', 'Cash or card accepted'), ('microtel-baguio', 'Cancellation: 24hrs prior'),
-- Travelite Baguio
('travelite-baguio', 'No smoking in rooms'), ('travelite-baguio', 'Quiet hours: 10PM–7AM'),
-- Hotel Luna
('hotel-luna-vigan', 'Government-issued ID required'), ('hotel-luna-vigan', 'No pets allowed'), ('hotel-luna-vigan', 'Cancellation: 48hrs prior'),
-- Hotel Felicidad
('hotel-felicidad', 'Breakfast served 6AM–9AM'), ('hotel-felicidad', 'Extra bed available on request'),
-- Paradores de Vigan
('paradores-vigan', 'No pets'), ('paradores-vigan', 'Cash payment preferred'), ('paradores-vigan', 'Early check-in subject to availability'),
-- Seda Lio
('seda-lio', 'Adults only: 18+'), ('seda-lio', 'No outside food/drinks'), ('seda-lio', 'Strictly no smoking'),
-- Hue Hotels
('hue-hotels-pp', 'Children under 12 stay free'), ('hue-hotels-pp', 'Credit cards accepted'), ('hue-hotels-pp', 'Tour desk available'),
-- Two Seasons Coron
('two-seasons-coron', 'Minimum stay: 2 nights'), ('two-seasons-coron', 'Dive packages available'), ('two-seasons-coron', 'Airport transfer included'),
-- Radisson Blu Cebu
('radisson-blu-cebu', 'Government-issued ID required'), ('radisson-blu-cebu', 'Check-in age: 18+'), ('radisson-blu-cebu', 'Pets not allowed'),
-- Quest Hotel Cebu
('quest-hotel-cebu', 'Children welcome'), ('quest-hotel-cebu', 'Cancellation: 24hrs prior'), ('quest-hotel-cebu', 'Airport shuttle available'),
-- Bayfront Hotel Cebu
('bayfront-cebu', 'No pets'), ('bayfront-cebu', 'Cash or card'), ('bayfront-cebu', 'Quiet hours: 11PM'),
-- Henann Crystal Sands
('henann-crystal', 'No day-tour guests'), ('henann-crystal', 'Minimum age for alcohol: 18+'), ('henann-crystal', 'Beach access 24/7'),
-- Fairways and Bluewater
('fairways-boracay', 'Smart casual dress code in golf course'), ('fairways-boracay', 'Private beach strictly for guests'),
-- La Sirena Resort
('la-sirena-boracay', 'Eco-friendly policy: Eco-bricks encourage'), ('la-sirena-boracay', 'No plastics on beachfront'),
-- Nay Palad Hideaway
('nay-palad-siargao', 'Adults only: 12+'), ('nay-palad-siargao', 'All-inclusive available'), ('nay-palad-siargao', 'Advance booking required'),
-- Kalinaw Resort
('kalinaw-siargao', 'No loud music after 10PM'), ('kalinaw-siargao', 'Surfboard storage available'),
-- Cloud 9 Surf Resort
('cloud9-resort-siargao', 'Surfing instructors available on-site'), ('cloud9-resort-siargao', 'Cash only for rentals'),
-- Dahilayan Forest Park Resort
('dahilayan-resort', 'Government-issued ID required'), ('dahilayan-resort', 'No pets'), ('dahilayan-resort', 'Height restrictions apply for some activities'),
-- Ultrawinds Mountain Resort
('ultrawinds-resort', 'Cash payment preferred'), ('ultrawinds-resort', 'Group rates available'), ('ultrawinds-resort', 'Quiet hours: 10PM'),
-- Secret Haven Private Resort
('secret-haven-bukidnon', 'Security deposit required upon check-in'), ('secret-haven-bukidnon', 'Event hosting subject to separate fee'),
-- Bintana sa Paraiso
('bintana-paraiso', 'Government-issued ID required'), ('bintana-paraiso', 'Cash only for on-site bar'),
-- Paras Beach Resort
('paras-beach', 'No pets allowed'), ('paras-beach', 'Pool hours: 7AM–10PM'),
-- Balai sa Baibai
('balai-baibai', 'Cash only'), ('balai-baibai', 'Breakfast not included (available for fee)'), ('balai-baibai', 'No smoking in rooms');

-- ============================================================================
-- DATA: Activities (Eksaktong sunod sa data.php)
-- ============================================================================

INSERT INTO `activities` (`destination_id`, `name`, `price`) VALUES
-- Baguio
('baguio', 'Strawberry Picking at La Trinidad Farm', 250),
('baguio', 'BenCab Museum Gallery Tour', 200),
('baguio', 'Tree Top Adventure – Camp John Hay', 400),
('baguio', 'Igorot Stone Kingdom Exploration', 150),
-- Vigan
('vigan', 'Calesa Ride around Calle Crisologo', 250),
('vigan', 'Pagburnayan Jar Factory Pottery Making', 300),
('vigan', 'Vigan Museum / Syquia Mansion Tour', 180),
-- Palawan
('palawan', 'El Nido Tour A – Lagoons & Islands', 1200),
('palawan', 'Puerto Princesa Underground River Tour', 2750),
('palawan', 'Coron Shipwreck & Snorkeling Tour', 1600),
('palawan', 'Wildlife Safari at Calauit Sanctuary', 2500),
-- Cebu
('cebu', 'Kawasan Falls Canyoneering', 1500),
('cebu', 'Temple of Leah Tour', 100),
('cebu', 'Oslob Whale Shark Watching', 500),
-- Boracay
('boracay', 'Island Hopping with Buffet Lunch', 1000),
('boracay', 'Helmet Diving Experience', 800),
('boracay', 'Parasailing at Sunset', 1500),
-- Siargao
('siargao', 'Tri-Island Hopping (Naked, Daku, Guyam)', 1200),
('siargao', 'Sohoton Cove & Jellyfish Sanctuary Tour', 2000),
('siargao', 'Basic Surf Lesson (1 Hour with Instructor)', 500),
-- Bukidnon
('bukidnon', 'Dual Zipline Adventure', 600),
('bukidnon', 'ATV Forest Trail Ride', 1200),
('bukidnon', 'Anicycle Air Bike Experience', 350),
-- Camiguin
('camiguin', 'Island Hopping', 2500),
('camiguin', 'Waterfalls Tour', 3500),
('camiguin', 'Scuba Diving', 2800);

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


-- Idagdag ang archived column sa bookings (Dito madalas may count sa dashboard gaya ng SELECT COUNT(*) FROM bookings WHERE archived = 0)
ALTER TABLE `bookings` 
ADD COLUMN `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

-- Idagdag din sa hotels para kung sakaling i-filter ng admin CRUD
ALTER TABLE `hotels` 
ADD COLUMN `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `checkout_time`;

-- Idagdag din sa destinations para safe ang buong admin panel
ALTER TABLE `destinations` 
ADD COLUMN `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `gradient_bg`;

-- Idagdag din sa activities para hindi lumabas sa users kapag archived
ALTER TABLE `activities`
ADD COLUMN `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `price`;
