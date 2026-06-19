-- ============================================================================
-- DATABASE: LAKBAYLOKAL (UPDATED VERSION)
-- ============================================================================

DROP DATABASE IF EXISTS `lakbaylokal`;
CREATE DATABASE IF NOT EXISTS `lakbaylokal`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `lakbaylokal`;

-- ============================================================================
-- TABLE: destinations
-- ============================================================================
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
);

-- ============================================================================
-- TABLE: hotels
-- ============================================================================
CREATE TABLE `hotels` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`destination_id` INT NOT NULL,
`name` VARCHAR(150) NOT NULL,
`image_url` VARCHAR(255),
`location` VARCHAR(200),
`description` TEXT,
`stars` TINYINT DEFAULT 0,
`price` INT DEFAULT 0,
`rating` DECIMAL(3,1) DEFAULT 0.0,
`reviews_count` INT DEFAULT 0,
`checkin_time` TIME,
`checkout_time` TIME,
`archived` TINYINT(1) NOT NULL DEFAULT 0,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE
);

-- ============================================================================
-- TABLE: hotel_amenities
-- ============================================================================
CREATE TABLE `hotel_amenities` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`hotel_id` INT NOT NULL,
`amenity_name` VARCHAR(100) NOT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE CASCADE,
KEY `idx_hotel` (`hotel_id`)
);

-- ============================================================================
-- TABLE: hotel_policies
-- ============================================================================
CREATE TABLE `hotel_policies` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`hotel_id` INT NOT NULL,
`policy` VARCHAR(255) NOT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE CASCADE,
KEY `idx_hotel` (`hotel_id`)
);

-- ============================================================================
-- TABLE: activities
-- ============================================================================
CREATE TABLE `activities` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`destination_id` INT NOT NULL,
`name` VARCHAR(150) NOT NULL,
`price` INT DEFAULT 0,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE,
KEY `idx_destination` (`destination_id`)
);

-- ============================================================================
-- TABLE: users
-- ============================================================================
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
);

-- ============================================================================
-- TABLE: bookings
-- ============================================================================
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

`status` ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
`archived` TINYINT(1) NOT NULL DEFAULT 0,

`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE RESTRICT,
FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE RESTRICT
);

-- ============================================================================
-- TABLE: booking_activities
-- ============================================================================
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
);

-- ============================================================================
-- TABLE: payment_details
-- ============================================================================
CREATE TABLE `payment_details` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`booking_id` INT NOT NULL UNIQUE,

`gcash_number` VARCHAR(20),
`gcash_account_name` VARCHAR(120),
`card_holder_name` VARCHAR(120),
`card_last_four` VARCHAR(4),
`card_brand` VARCHAR(20),

`payment_status` ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
`payment_reference` VARCHAR(100),

`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
);
