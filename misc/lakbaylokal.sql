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
-- DATA: Users (Sample data - passwords are hashed with bcrypt)
-- ============================================================================

INSERT INTO `users` (`FName`, `LName`, `Mname`, `Email`, `Password`, `role`) VALUES
('Admin', 'User', 'Test', 'admin@lakbaylokal.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'admin'),
('Test', 'User', 'Sample', 'user@test.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'user'),
('Juan', 'Dela Cruz', 'Sample', 'juan@example.com', '$2y$10$YQv8XqPjKsKYlM.F4zZ6K.AW8l7G.5Xz0P5KpS8r9Q0Q0Q0Q0Q0Qq', 'user');

-- ============================================================================
-- DATA: Destinations
-- ============================================================================

INSERT INTO `destinations` (`id`, `name`, `region`, `emoji`, `tagline`, `description`, `price`, `price_from`, `gradient_bg`) VALUES
('baguio', 'Baguio City', 'Luzon', '❄️', 'The Summer Capital of the Philippines', 'Escape to the cool mountain air of Baguio City and discover pine forests, strawberry farms, and rich Igorot culture.', 4500, 2800, 'linear-gradient(135deg, rgba(109,177,197,0.75), rgba(0,0,0,0.22)), url(\'assets/pics/Baguio.jpg\') center/cover no-repeat'),
('vigan', 'Vigan City', 'Luzon', '🏛️', 'UNESCO World Heritage City', 'Walk the cobblestone streets of a colonial-era city frozen in time, with kalesas, ancestral houses, and Ilocano cuisine.', 6500, 3500, 'linear-gradient(135deg, rgba(0,0,0,0.34), rgba(0,0,0,0.05)), url(\'assets/pics/vigan.jpg\') center/cover no-repeat'),
('palawan', 'Palawan', 'Luzon', '🏝️', 'Last Frontier of the Philippines', 'Crystal lagoons, secret beaches, WWII shipwrecks, and the world-famous Underground River await in paradise Palawan.', 8500, 6500, 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.1)), url(\'assets/pics/palawan.jpg\') center/cover no-repeat'),
('tagaytay', 'Tagaytay', 'Luzon', '🌋', 'Cool Highlands with a View', 'Perched 600m above sea level, Tagaytay offers cool breezes, stunning volcano vistas, and charming cafes overlooking Taal Lake and Volcano.', 4800, 2500, 'linear-gradient(135deg, rgba(0,0,0,0.30), rgba(0,0,0,0.08)), url(\'assets/pics/tagaytay.jpg\') center/cover no-repeat'),
('batangas', 'Batangas – Matabungkay', 'Luzon', '🏖️', 'Beach Escape Just Hours from Manila', 'A laid-back coastal retreat famous for long stretches of white sand, watersports, and fresh seafood restaurants perfect for weekend getaways.', 3500, 1800, 'linear-gradient(135deg, rgba(0,0,0,0.26), rgba(0,0,0,0.06)), url(\'assets/pics/batangas.jpg\') center/cover no-repeat'),
('cebu', 'Cebu City', 'Visayas', '🐚', 'Queen City of the South', 'A vibrant island blend of colonial heritage, world-class beaches, world-class diving spots, and modern urban convenience.', 5200, 2800, 'linear-gradient(135deg, rgba(0,0,0,0.30), rgba(0,0,0,0.08)), url(\'assets/pics/cebu.jpg\') center/cover no-repeat'),
('boracay', 'Boracay', 'Visayas', '🌅', 'World\'s Best Beach', 'Pristine white-sand shores, electric nightlife, crystal-clear waters perfect for diving, and endless island vibes on this famous tropical paradise.', 7000, 4500, 'linear-gradient(135deg, rgba(0,0,0,0.25), rgba(0,0,0,0.05)), url(\'assets/pics/boracay.jpg\') center/cover no-repeat'),
('siargao', 'Siargao Island', 'Mindanao', '🏄', 'Surfing Paradise', 'Home to Cloud 9 breaks and turquoise lagoons, Siargao is a haven for surfers, beach lovers, and those seeking tropical island tranquility.', 5500, 3000, 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.10)), url(\'assets/pics/siargao2.jpg\') center/cover no-repeat'),
('bukidnon', 'Bukidnon', 'Mindanao', '🎢', 'Thrills at Dahilayan Adventure Park', 'Experience the longest zipline in Asia, ATV rides through highland meadows, and the cool breeze of Mindanao\'s highlands.', 2100, 2100, 'linear-gradient(135deg, rgba(0,0,0,0.28), rgba(0,0,0,0.06)), url(\'assets/pics/bukidno.jpg\') center/cover no-repeat'),
('camiguin', 'Camiguin Island', 'Mindanao', '🌋', 'Island Born of Fire', 'This tiny island has more volcanoes per square kilometer than anywhere on Earth, plus stunning waterfalls and turquoise springs.', 7500, 3500, 'linear-gradient(135deg, rgba(0,0,0,0.32), rgba(0,0,0,0.08)), url(\'assets/pics/camiguin.jpg\') center/cover no-repeat');

-- ============================================================================
-- DATA: Hotels - Baguio
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('sotogrande-baguio', 'baguio', 'Sotogrande Hotel Baguio', 'assets/pics/sotogrande.jpg', 'Session Road, Baguio City', 'A modern boutique hotel nestled in the heart of Baguio, offering luxurious rooms with stunning mountain views and world-class amenities.', 4, 5200, 4.6, 312, '14:00:00', '11:00:00'),
('microtel-baguio', 'baguio', 'Microtel by Wyndham Baguio', 'assets/pics/microtel3.jpg', 'Legarda Road, Baguio City', 'Comfortable and affordable rooms with reliable service, conveniently located near SM City Baguio and key tourist spots.', 3, 3600, 4.3, 205, '14:00:00', '12:00:00'),
('travelite-baguio', 'baguio', 'Travelite Express Hotel', 'assets/pics/travellite2.jpg', 'Magsaysay Avenue, Baguio City', 'A budget-friendly option with clean, cozy rooms ideal for solo travelers and backpackers exploring the City of Pines.', 2, 2800, 4.1, 148, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Vigan
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('hotel-luna-vigan', 'vigan', 'Hotel Luna', 'assets/pics/hotelluna2.jpg', 'Crisologo St., Vigan City', 'An award-winning boutique hotel housed in a century-old ancestral mansion, blending heritage architecture with modern luxury on the famous Calle Crisologo.', 4, 5800, 4.8, 421, '14:00:00', '11:00:00'),
('hotel-felicidad', 'vigan', 'Hotel Felicidad Vigan', 'assets/pics/hotelfelicidad2.jpg', 'Quirino Blvd, Vigan City', 'A charming heritage hotel offering comfortable rooms with period-inspired décor, located steps away from Vigan\'s famous plazas and churches.', 3, 4200, 4.4, 186, '14:00:00', '12:00:00'),
('paradores-vigan', 'vigan', 'Paradores de Vigan', 'assets/pics/paradores de.jpg', 'Mena Crisologo St., Vigan City', 'Experience the warmth of Ilocano hospitality in this cozy, family-run hotel set in a restored colonial building within the heritage zone.', 3, 3500, 4.2, 97, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Palawan
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('seda-lio', 'palawan', 'Seda Lio – El Nido', 'assets/pics/sedalio2.jpg', 'Lio Tourism Estate, El Nido, Palawan', 'A stunning eco-luxury resort set on the shores of El Nido\'s Lio Beach, offering sustainable luxury with panoramic views of the Bacuit Archipelago.', 5, 12500, 4.9, 534, '15:00:00', '12:00:00'),
('hue-hotels-pp', 'palawan', 'Hue Hotels – Puerto Princesa', 'assets/pics/huehotel2.jpg', 'Rizal Avenue, Puerto Princesa City', 'A vibrant, design-forward hotel in the heart of Puerto Princesa, offering colorful rooms, an excellent restaurant, and easy access to the city\'s attractions.', 4, 7200, 4.6, 298, '14:00:00', '12:00:00'),
('two-seasons-coron', 'palawan', 'Two Seasons Coron Island Resort', 'assets/pics/two seasons.jpg', 'Malaroyroy Peninsula, Coron, Palawan', 'Perched on a private peninsula overlooking the famous Coron Bay, this overwater resort offers an intimate island experience with world-class diving access.', 4, 9800, 4.7, 187, '14:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Tagaytay
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('mahogany-market', 'tagaytay', 'Mahogany Market Hotel', 'assets/pics/mahogany.jpg', 'Maginhawa St., Tagaytay', 'A charming hillside boutique hotel in the heart of Tagaytay, with stunning Taal Lake views and a farm-to-table restaurant.', 3, 3800, 4.4, 267, '14:00:00', '11:00:00'),
('picnic-grove-tagaytay', 'tagaytay', 'Picnic Grove Tagaytay', 'assets/pics/picnic.jpg', 'Picnic Grove Rd., Tagaytay', 'A scenic resort with cozy cottages, camping sites, and full garden views, famous for its bonfire nights and garden restaurant.', 3, 2500, 4.3, 189, '13:00:00', '11:00:00'),
('people-park-hotel', 'tagaytay', 'People\'s Park Hotel', 'assets/pics/peoplespark.jpg', 'Park Rd., Tagaytay', 'A family-friendly hilltop resort offering affordable comfort with panoramic volcano views and multiple dining options.', 2, 2100, 4.0, 145, '12:00:00', '10:00:00');

-- ============================================================================
-- DATA: Hotels - Batangas
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('matabungkay-beach-resort', 'batangas', 'Matabungkay Beach Resort', 'assets/pics/matabungkay.jpg', 'Matabungkay, Nasugbu, Batangas', 'A long-established beachfront resort with direct sand access, water sports facilities, and a relaxed tropical atmosphere.', 3, 2800, 4.2, 234, '14:00:00', '11:00:00'),
('eagle-point', 'batangas', 'Eagle Point Beach Resort', 'assets/pics/eaglepoint.jpg', 'Nasugbu, Batangas', 'A scenic clifftop resort with dramatic views of Manila Bay, private beach coves, and water sports activities.', 3, 3200, 4.4, 156, '14:00:00', '12:00:00'),
('royal-sunset-beach', 'batangas', 'Royal Sunset Beach Resort', 'assets/pics/royalsunset.jpg', 'Matabungkay, Batangas', 'A budget-friendly beachside inn perfect for weekend escapes, with rooms facing the sea and a popular beachfront restaurant.', 2, 1800, 4.1, 124, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Cebu
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('radisson-cebu', 'cebu', 'Radisson Blu Cebu', 'assets/pics/radisson.jpg', 'Sergio Osmeña Blvd, Cebu City', 'A luxury hotel in the heart of Cebu with modern rooms, rooftop pools, and sophisticated dining options.', 4, 6500, 4.7, 389, '15:00:00', '12:00:00'),
('jpark-island', 'cebu', 'J Park Island Resort', 'assets/pics/jpark.jpg', 'Lapu-Lapu, Cebu', 'An exciting beachfront resort on Mactan Island with water sports, multiple pools, and family-friendly entertainment.', 4, 5500, 4.5, 267, '14:00:00', '11:00:00'),
('sunburst-resort-cebu', 'cebu', 'Sunburst Resort Cebu', 'assets/pics/sunburst.jpg', 'Lapu-Lapu, Mactan, Cebu', 'A budget-conscious beachfront resort offering comfortable rooms and easy access to diving and island activities.', 3, 2800, 4.0, 98, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Boracay
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('shangri-la-boracay', 'boracay', 'Shangri-La\'s Boracay', 'assets/pics/shangrila.jpg', 'White Beach, Boracay Island', 'An ultra-luxury beachfront resort on the famous White Beach with world-class facilities and exclusive service.', 5, 15800, 4.9, 612, '15:00:00', '12:00:00'),
('henann-resort', 'boracay', 'Henann Evolution', 'assets/pics/henann.jpg', 'Station 2, Boracay Island', 'A premium all-inclusive resort right on White Beach with water sports, infinity pools, and vibrant nightlife nearby.', 4, 8900, 4.6, 445, '14:00:00', '11:00:00'),
('la-sirena-boracay', 'boracay', 'La Sirena Resort', 'assets/pics/lasirena.jpg', 'Balabag, Boracay Island', 'A charming beachfront boutique hotel offering intimate accommodations with direct access to White Beach.', 3, 5200, 4.3, 287, '14:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Siargao
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('cloud9-resort-siargao', 'siargao', 'Cloud 9 Reef Club', 'assets/pics/cloud9.jpg', 'General Luna, Siargao Island', 'A popular surf resort with beachfront access to Cloud 9 break, offering surfboard rentals and a vibrant backpacker vibe.', 4, 6200, 4.6, 334, '14:00:00', '11:00:00'),
('siargao-bleu-resort', 'siargao', 'Siargao Bleu Resort', 'assets/pics/siargao_bleu.jpg', 'General Luna, Siargao Island', 'A tranquil boutique resort tucked among coconut palms near Cloud 9, offering stunning lagoon views and a genuine island atmosphere.', 4, 5800, 4.5, 267, '14:00:00', '11:00:00'),
('villa-cali-siargao', 'siargao', 'Villa Cali', 'assets/pics/siargao.jpg', 'General Luna, Siargao Island', 'A laid-back villa perfect for surf enthusiasts and budget travelers, with cozy rooms, a social common area, and walking distance to Cloud 9 breaks.', 3, 3700, 4.3, 198, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Bukidnon
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('dahilayan-resort', 'bukidnon', 'Dahilayan Forest Park Resort', 'assets/pics/bukidno.jpg', 'Dahilayan, Manolo Fortich, Bukidnon', 'Stay right at the adventure park and wake up to misty mountain views. Direct access to all zipline and extreme adventure facilities.', 4, 4500, 4.5, 312, '14:00:00', '11:00:00'),
('ultrawinds-resort', 'bukidnon', 'Ultrawinds Mountain Resort', 'assets/pics/bukidno.jpg', 'Manolo Fortich, Bukidnon', 'A scenic highland resort with comfortable accommodations and stunning views of Bukidnon\'s pine-covered mountains.', 3, 3200, 4.3, 198, '13:00:00', '11:00:00'),
('secret-haven-bukidnon', 'bukidnon', 'Secret Haven Private Resort', 'assets/pics/bukidno.jpg', 'Bukidnon Province', 'An intimate private resort surrounded by nature, perfect for group getaways with exclusive use of the property.', 3, 2100, 4.1, 145, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotels - Camiguin
-- ============================================================================

INSERT INTO `hotels` (`id`, `destination_id`, `name`, `image_url`, `location`, `description`, `stars`, `price`, `rating`, `reviews_count`, `checkin_time`, `checkout_time`) VALUES
('bintana-paraiso', 'camiguin', 'Bintana sa Paraiso', 'assets/pics/camiguin.jpg', 'Mambajao, Camiguin Island', 'A picturesque resort with sea-facing cottages and direct beach access, offering a genuine island paradise experience in Camiguin.', 4, 5800, 4.7, 203, '14:00:00', '11:00:00'),
('paras-beach', 'camiguin', 'Paras Beach Resort', 'assets/pics/camiguin.jpg', 'Guinsiliban, Camiguin Island', 'A beloved beachside resort known for its warm hospitality, well-kept grounds, and stunning views of the island\'s volcanic peaks.', 3, 4200, 4.4, 178, '14:00:00', '12:00:00'),
('balai-baibai', 'camiguin', 'Balai sa Baibai', 'assets/pics/camiguin.jpg', 'Mambajao, Camiguin Island', 'A cozy family-run beachside inn offering affordable comfort and genuine Camiguinon hospitality just steps from the beach.', 3, 3500, 4.2, 134, '13:00:00', '11:00:00');

-- ============================================================================
-- DATA: Hotel Amenities
-- ============================================================================

INSERT INTO `hotel_amenities` (`hotel_id`, `amenity_name`) VALUES
('sotogrande-baguio', 'Free WiFi'),
('sotogrande-baguio', 'Restaurant'),
('sotogrande-baguio', 'Pool'),
('sotogrande-baguio', 'Gym'),
('sotogrande-baguio', 'Parking'),
('sotogrande-baguio', 'Room Service'),

('microtel-baguio', 'Free WiFi'),
('microtel-baguio', 'Breakfast'),
('microtel-baguio', 'Parking'),
('microtel-baguio', 'Room Service'),
('microtel-baguio', 'Laundry'),

('travelite-baguio', 'Free WiFi'),
('travelite-baguio', 'Air Conditioning'),
('travelite-baguio', '24/7 Front Desk'),

('hotel-luna-vigan', 'Free WiFi'),
('hotel-luna-vigan', 'Rooftop Pool'),
('hotel-luna-vigan', 'Art Gallery'),
('hotel-luna-vigan', 'Restaurant'),
('hotel-luna-vigan', 'Room Service'),
('hotel-luna-vigan', 'Concierge'),

('hotel-felicidad', 'Free WiFi'),
('hotel-felicidad', 'Breakfast Included'),
('hotel-felicidad', 'Parking'),
('hotel-felicidad', 'Tour Desk'),

('paradores-vigan', 'Free WiFi'),
('paradores-vigan', 'Courtyard Garden'),
('paradores-vigan', 'Bike Rental'),
('paradores-vigan', 'Breakfast'),

('seda-lio', 'Infinity Pool'),
('seda-lio', 'Beach Access'),
('seda-lio', 'Spa'),
('seda-lio', 'Restaurant'),
('seda-lio', 'Water Sports'),
('seda-lio', 'Free WiFi'),

('hue-hotels-pp', 'Pool'),
('hue-hotels-pp', 'Restaurant'),
('hue-hotels-pp', 'Free WiFi'),
('hue-hotels-pp', 'Gym'),
('hue-hotels-pp', 'Airport Shuttle'),
('hue-hotels-pp', 'Kids Club'),

('two-seasons-coron', 'Overwater Villas'),
('two-seasons-coron', 'Restaurant'),
('two-seasons-coron', 'Dive Center'),
('two-seasons-coron', 'Free WiFi'),
('two-seasons-coron', 'Beach Access'),

('mahogany-market', 'Free WiFi'),
('mahogany-market', 'Restaurant'),
('mahogany-market', 'Mountain View'),
('mahogany-market', 'Parking'),

('picnic-grove-tagaytay', 'Free WiFi'),
('picnic-grove-tagaytay', 'Bonfire Area'),
('picnic-grove-tagaytay', 'Restaurant'),
('picnic-grove-tagaytay', 'BBQ Area'),

('people-park-hotel', 'Free WiFi'),
('people-park-hotel', 'Restaurant'),
('people-park-hotel', 'Mountain View'),
('people-park-hotel', 'Parking'),

('matabungkay-beach-resort', 'Beach Access'),
('matabungkay-beach-resort', 'Restaurant'),
('matabungkay-beach-resort', 'Free WiFi'),
('matabungkay-beach-resort', 'Water Sports'),

('eagle-point', 'Beach Access'),
('eagle-point', 'Restaurant'),
('eagle-point', 'Free WiFi'),
('eagle-point', 'Water Sports'),

('royal-sunset-beach', 'Beach Access'),
('royal-sunset-beach', 'Restaurant'),
('royal-sunset-beach', 'Free WiFi'),

('radisson-cebu', 'Pool'),
('radisson-cebu', 'Restaurant'),
('radisson-cebu', 'Free WiFi'),
('radisson-cebu', 'Gym'),
('radisson-cebu', 'Business Center'),

('jpark-island', 'Beach Access'),
('jpark-island', 'Multiple Pools'),
('jpark-island', 'Restaurant'),
('jpark-island', 'Free WiFi'),
('jpark-island', 'Water Sports'),

('sunburst-resort-cebu', 'Beach Access'),
('sunburst-resort-cebu', 'Restaurant'),
('sunburst-resort-cebu', 'Free WiFi'),
('sunburst-resort-cebu', 'Gym'),

('shangri-la-boracay', 'Infinity Pool'),
('shangri-la-boracay', 'Beach Access'),
('shangri-la-boracay', 'Spa'),
('shangri-la-boracay', 'Multiple Restaurants'),

('henann-resort', 'Beach Access'),
('henann-resort', '3 Pools'),
('henann-resort', 'Restaurant'),
('henann-resort', 'Free WiFi'),
('henann-resort', 'Water Sports'),

('la-sirena-boracay', 'Beach Access'),
('la-sirena-boracay', 'Restaurant'),
('la-sirena-boracay', 'Free WiFi'),
('la-sirena-boracay', 'Pool'),

('cloud9-resort-siargao', 'Beach Access'),
('cloud9-resort-siargao', 'Restaurant'),
('cloud9-resort-siargao', 'Free WiFi'),
('cloud9-resort-siargao', 'Surf Board Rental'),

('siargao-bleu-resort', 'Lagoon Views'),
('siargao-bleu-resort', 'Pool'),
('siargao-bleu-resort', 'Restaurant'),
('siargao-bleu-resort', 'Surf Board Rental'),
('siargao-bleu-resort', 'Free WiFi'),

('villa-cali-siargao', 'Free WiFi'),
('villa-cali-siargao', 'Surfboard Storage'),
('villa-cali-siargao', 'Common Area'),
('villa-cali-siargao', 'Hammock Garden'),
('villa-cali-siargao', 'Breakfast Available'),

('dahilayan-resort', 'Free WiFi'),
('dahilayan-resort', 'Restaurant'),
('dahilayan-resort', 'Adventure Park Access'),
('dahilayan-resort', 'Parking'),
('dahilayan-resort', 'Room Service'),

('ultrawinds-resort', 'Free WiFi'),
('ultrawinds-resort', 'Restaurant'),
('ultrawinds-resort', 'Mountain View'),
('ultrawinds-resort', 'Parking'),
('ultrawinds-resort', 'Bonfire Area'),

('secret-haven-bukidnon', 'Pool'),
('secret-haven-bukidnon', 'Free WiFi'),
('secret-haven-bukidnon', 'BBQ Area'),
('secret-haven-bukidnon', 'Exclusive Use'),
('secret-haven-bukidnon', 'Kitchen Facilities'),

('bintana-paraiso', 'Beach Access'),
('bintana-paraiso', 'Restaurant'),
('bintana-paraiso', 'Free WiFi'),
('bintana-paraiso', 'Snorkeling Gear'),
('bintana-paraiso', 'Tour Desk'),
('bintana-paraiso', 'Kayaking'),

('paras-beach', 'Beachfront'),
('paras-beach', 'Restaurant'),
('paras-beach', 'Free WiFi'),
('paras-beach', 'Pool'),
('paras-beach', 'Outdoor Bar'),

('balai-baibai', 'Free WiFi'),
('balai-baibai', 'Beach Access'),
('balai-baibai', 'Breakfast Available'),
('balai-baibai', 'Communal Kitchen'),
('balai-baibai', 'Hammocks');

-- ============================================================================
-- DATA: Hotel Policies
-- ============================================================================

INSERT INTO `hotel_policies` (`hotel_id`, `policy`) VALUES
('sotogrande-baguio', 'Government-issued ID required'),
('sotogrande-baguio', 'No pets allowed'),
('sotogrande-baguio', 'Extra person charges apply'),

('microtel-baguio', 'Check-in age: 18+'),
('microtel-baguio', 'Cash or card accepted'),
('microtel-baguio', 'Cancellation: 24hrs prior'),

('travelite-baguio', 'No smoking in rooms'),
('travelite-baguio', 'Quiet hours: 10PM–7AM'),

('hotel-luna-vigan', 'Government-issued ID required'),
('hotel-luna-vigan', 'No pets allowed'),
('hotel-luna-vigan', 'Cancellation: 48hrs prior'),

('hotel-felicidad', 'Breakfast served 6AM–9AM'),
('hotel-felicidad', 'Extra bed available on request'),

('paradores-vigan', 'No pets'),
('paradores-vigan', 'Cash payment preferred'),
('paradores-vigan', 'Early check-in subject to availability'),

('seda-lio', 'Adults only: 18+'),
('seda-lio', 'No outside food/drinks'),
('seda-lio', 'Strictly no smoking'),

('hue-hotels-pp', 'Children under 12 stay free'),
('hue-hotels-pp', 'Credit cards accepted'),
('hue-hotels-pp', 'Tour desk available'),

('two-seasons-coron', 'Government-issued ID required'),
('two-seasons-coron', 'Dive packages available'),
('two-seasons-coron', 'Marine protection policy'),

('mahogany-market', 'Government-issued ID required'),
('mahogany-market', 'No pets allowed'),
('mahogany-market', 'Parking available'),

('picnic-grove-tagaytay', 'Bonfire policy: No glass containers'),
('picnic-grove-tagaytay', 'Camping sites available'),

('people-park-hotel', 'Family-friendly'),
('people-park-hotel', 'Group rates available'),

('matabungkay-beach-resort', 'Beach activities available'),
('matabungkay-beach-resort', 'Equipment rental on-site'),

('eagle-point', 'Water sports safety briefing required'),
('eagle-point', 'Private beach coves'),

('royal-sunset-beach', 'Budget-friendly option'),
('royal-sunset-beach', 'Beachfront restaurant'),

('radisson-cebu', 'Business center available'),
('radisson-cebu', 'Concierge service 24/7'),

('jpark-island', 'Family packages available'),
('jpark-island', 'Water sports included'),

('sunburst-resort-cebu', 'Diving packages available'),
('sunburst-resort-cebu', 'Budget-friendly'),

('shangri-la-boracay', 'Luxury service standards'),
('shangri-la-boracay', 'Concierge available'),

('henann-resort', 'All-inclusive packages available'),
('henann-resort', 'Entertainment nightly'),

('la-sirena-boracay', 'Intimate boutique experience'),
('la-sirena-boracay', 'Beachfront dining'),

('cloud9-resort-siargao', 'Surfboard rental included'),
('cloud9-resort-siargao', 'Backpacker friendly'),

('siargao-bleu-resort', 'Minimum 2-night stay on weekends'),
('siargao-bleu-resort', 'No children under 10'),
('siargao-bleu-resort', 'Cashless payment only'),

('villa-cali-siargao', 'Cash only'),
('villa-cali-siargao', 'No pets'),
('villa-cali-siargao', 'Communal areas shared'),

('dahilayan-resort', 'Government-issued ID required'),
('dahilayan-resort', 'No pets'),
('dahilayan-resort', 'Height restrictions apply for some activities'),

('ultrawinds-resort', 'Cash payment preferred'),
('ultrawinds-resort', 'Group rates available'),
('ultrawinds-resort', 'Quiet hours: 10PM'),

('secret-haven-bukidnon', 'Private booking required'),
('secret-haven-bukidnon', 'Outside food allowed'),
('secret-haven-bukidnon', 'No overnight pets'),

('bintana-paraiso', 'Government-issued ID required'),
('bintana-paraiso', 'Cash or card accepted'),
('bintana-paraiso', 'Dive packages available'),

('paras-beach', 'No pets'),
('paras-beach', 'Early check-in on request'),
('paras-beach', 'Cash payment preferred'),

('balai-baibai', 'Cash only'),
('balai-baibai', 'Breakfast not included (available for fee)'),
('balai-baibai', 'No smoking in rooms');

-- ============================================================================
-- DATA: Activities - Baguio
-- ============================================================================

INSERT INTO `activities` (`destination_id`, `name`, `price`) VALUES
('baguio', 'Strawberry Picking at La Trinidad Farm', 250),
('baguio', 'BenCab Museum Gallery Tour', 200),
('baguio', 'Tree Top Adventure – Camp John Hay', 400),
('baguio', 'Igorot Stone Kingdom Exploration', 150),

('vigan', 'Calesa Ride around Calle Crisologo', 250),
('vigan', 'Pagburnayan Jar Factory Pottery Making', 300),
('vigan', 'Vigan Museum / Syquia Mansion Tour', 180),

('palawan', 'Underground River Tour', 1800),
('palawan', 'Island Hopping – Bacuit Archipelago', 2800),
('palawan', 'Scuba Diving Certification', 12000),
('palawan', 'El Nido Lagoon Tour', 1500),

('tagaytay', 'Horseback Riding with Lake Views', 800),
('tagaytay', 'Taal Volcano Trek', 1200),
('tagaytay', 'Zip-line Adventure', 1500),
('tagaytay', 'ATV Tour of Highlands', 1000),

('batangas', 'Scuba Diving at Anilao', 2200),
('batangas', 'Island Hopping Tour', 1500),
('batangas', 'Snorkeling Trip', 800),

('cebu', 'Whale Shark Encounter – Oslob', 1500),
('cebu', 'Balicasag Island Diving', 2500),
('cebu', 'Mactan Island Snorkeling', 900),

('boracay', 'Parasailing', 1200),
('boracay', 'Island Hopping Cruise', 1800),
('boracay', 'Windsurfing Lesson', 1500),
('boracay', 'Sunset Catamaran Cruise', 2000),

('siargao', 'Island Hopping', 2500),
('siargao', 'Basic Surf Lesson', 700),
('siargao', 'Motorbike Rental (per day)', 500),

('bukidnon', 'ATV – Dahilayan Adventure Park', 850),
('bukidnon', '840m Zipline – Dahilayan Adventure Park', 500),
('bukidnon', 'DropZone – Dahilayan Adventure Park', 500),
('bukidnon', 'ZipKart – Dahilayan Adventure Park', 250),

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

-- ============================================================================
-- DONE! Database 'lakbaylokal' is ready to use
-- ============================================================================
