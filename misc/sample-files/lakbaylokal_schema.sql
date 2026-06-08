-- ============================================================================
-- LAKBAYLOKAL DATABASE SCHEMA
-- Travel Booking System (PHP + MySQL)
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS lakbaylokal;
USE lakbaylokal;

-- ============================================================================
-- 1. USERS TABLE
-- Store user account information
-- ============================================================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_email (email),
  KEY idx_role (role)
);

-- ============================================================================
-- 2. DESTINATIONS/PLACES TABLE
-- Store travel destination information
-- ============================================================================
CREATE TABLE destinations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  region ENUM('Luzon', 'Visayas', 'Mindanao') NOT NULL,
  description LONGTEXT,
  price INT DEFAULT 0,
  image_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_region (region)
);

-- ============================================================================
-- 3. HOTELS TABLE
-- Store hotel information linked to destinations
-- ============================================================================
CREATE TABLE hotels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  destination_id INT NOT NULL,
  name VARCHAR(180) NOT NULL,
  price_per_night INT NOT NULL,
  stars INT DEFAULT 3,
  rating DECIMAL(3, 1) DEFAULT 0.0,
  review_count INT DEFAULT 0,
  location VARCHAR(255),
  description LONGTEXT,
  checkin_time TIME DEFAULT '14:00:00',
  checkout_time TIME DEFAULT '11:00:00',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
  KEY idx_destination (destination_id),
  KEY idx_price (price_per_night)
);

-- ============================================================================
-- 4. HOTEL_AMENITIES TABLE
-- Store amenities/facilities for each hotel
-- ============================================================================
CREATE TABLE hotel_amenities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hotel_id INT NOT NULL,
  amenity VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
  KEY idx_hotel (hotel_id),
  UNIQUE KEY unique_amenity (hotel_id, amenity)
);

-- ============================================================================
-- 5. HOTEL_POLICIES TABLE
-- Store hotel policies/rules
-- ============================================================================
CREATE TABLE hotel_policies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hotel_id INT NOT NULL,
  policy TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
  KEY idx_hotel (hotel_id)
);

-- ============================================================================
-- 6. ACTIVITIES TABLE
-- Store activities/tours available in each destination
-- ============================================================================
CREATE TABLE activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  destination_id INT NOT NULL,
  name VARCHAR(200) NOT NULL,
  price INT NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
  KEY idx_destination (destination_id),
  KEY idx_price (price)
);

-- ============================================================================
-- 7. BOOKINGS TABLE
-- Store all booking records
-- ============================================================================
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(20) NOT NULL UNIQUE,
  user_id INT,
  guest_name VARCHAR(120) NOT NULL,
  guest_email VARCHAR(180) NOT NULL,
  
  destination_id INT NOT NULL,
  hotel_id INT NOT NULL,
  
  check_in_date DATE NOT NULL,
  check_out_date DATE NOT NULL,
  number_of_guests INT NOT NULL DEFAULT 1,
  number_of_rooms INT NOT NULL DEFAULT 1,
  
  subtotal_amount INT DEFAULT 0,
  activities_total INT DEFAULT 0,
  tax_amount INT DEFAULT 0,
  total_price INT NOT NULL,
  
  payment_method ENUM('gcash', 'credit_card', 'debit_card') NOT NULL,
  special_requests TEXT,
  
  status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE RESTRICT,
  FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE RESTRICT,
  
  KEY idx_reference (reference_code),
  KEY idx_user (user_id),
  KEY idx_email (guest_email),
  KEY idx_status (status),
  KEY idx_created (created_at)
);

-- ============================================================================
-- 8. BOOKING_ACTIVITIES TABLE
-- Junction table: links activities to bookings (many-to-many)
-- ============================================================================
CREATE TABLE booking_activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  activity_id INT NOT NULL,
  activity_name VARCHAR(200) NOT NULL,
  activity_price INT NOT NULL,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE RESTRICT,
  
  KEY idx_booking (booking_id),
  KEY idx_activity (activity_id),
  UNIQUE KEY unique_booking_activity (booking_id, activity_id)
);

-- ============================================================================
-- 9. PAYMENT_DETAILS TABLE
-- Store payment information (optional, for security reasons)
-- ============================================================================
CREATE TABLE payment_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL UNIQUE,
  
  payment_method ENUM('gcash', 'credit_card', 'debit_card') NOT NULL,
  
  -- GCash fields
  gcash_number VARCHAR(20),
  gcash_account_name VARCHAR(120),
  
  -- Card fields (masked for security)
  card_holder_name VARCHAR(120),
  card_last_four VARCHAR(4),
  card_brand VARCHAR(20),
  
  payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
  payment_reference VARCHAR(100),
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  KEY idx_status (payment_status)
);

-- ============================================================================
-- SAMPLE DATA INSERTION
-- ============================================================================

-- Insert sample destinations
INSERT INTO destinations (name, region, description, price) VALUES
('Baguio City', 'Luzon', 'The Summer Capital of the Philippines - Escape to cool mountain air', 4500),
('Vigan City', 'Luzon', 'UNESCO World Heritage City - Colonial charm and heritage', 6500),
('Palawan', 'Luzon', 'Last Frontier of the Philippines - Crystal lagoons and white sand beaches', 8500),
('Cebu City', 'Visayas', 'The Queen City of the South - Beaches, history, and adventure', 5500),
('Boracay Island', 'Visayas', 'World-Famous White Sand Beach - Paradise on earth', 7500),
('Siargao Island', 'Mindanao', 'Surfing Capital of the Philippines - Laid-back beach vibes', 3700),
('Bukidnon', 'Mindanao', 'Highland adventures - Ziplines and outdoor thrills', 2100),
('Camiguin Island', 'Mindanao', 'Island Born of Fire - Waterfalls and volcanic wonders', 7500);

-- Insert sample hotels for Baguio
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(1, 'Sotogrande Hotel Baguio', 5200, 4, 4.6, 312, 'Session Road, Baguio City', 'A modern boutique hotel with luxurious rooms and mountain views'),
(1, 'Microtel by Wyndham Baguio', 3600, 3, 4.3, 205, 'Legarda Road, Baguio City', 'Comfortable rooms with reliable service near SM City'),
(1, 'Travelite Express Hotel', 2800, 2, 4.1, 148, 'Magsaysay Avenue, Baguio City', 'Budget-friendly option perfect for backpackers');

-- Insert sample hotels for Vigan
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(2, 'Hotel Luna', 5800, 4, 4.8, 421, 'Crisologo St., Vigan City', 'Award-winning boutique hotel in a century-old ancestral mansion'),
(2, 'Hotel Felicidad Vigan', 4200, 3, 4.4, 186, 'Quirino Blvd, Vigan City', 'Charming heritage hotel with period-inspired décor'),
(2, 'Paradores de Vigan', 3500, 3, 4.2, 97, 'Mena Crisologo St., Vigan City', 'Family-run hotel in a restored colonial building');

-- Insert sample amenities for hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(1, 'Free WiFi'),
(1, 'Restaurant'),
(1, 'Pool'),
(1, 'Gym'),
(1, 'Parking'),
(1, 'Room Service'),
(2, 'Free WiFi'),
(2, 'Breakfast'),
(2, 'Parking'),
(2, 'Laundry Service'),
(3, 'Free WiFi'),
(3, 'Air Conditioning'),
(3, '24/7 Front Desk');

-- Insert sample activities for Baguio
INSERT INTO activities (destination_id, name, price) VALUES
(1, 'Strawberry Picking at La Trinidad Farm', 250),
(1, 'BenCab Museum Gallery Tour', 200),
(1, 'Tree Top Adventure – Camp John Hay', 400),
(1, 'Igorot Stone Kingdom Exploration', 150);

-- Insert sample activities for Vigan
INSERT INTO activities (destination_id, name, price) VALUES
(2, 'Calesa Ride around Calle Crisologo', 250),
(2, 'Pagburnayan Jar Factory Pottery Making', 300),
(2, 'Vigan Museum / Syquia Mansion Tour', 180);

-- Insert sample admin user (password: admin123 - hashed with password_hash())
INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Admin', 'User', 'admin@lakbaylokal.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36MM6FSm', 'admin');

-- ============================================================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================================================
CREATE INDEX idx_booking_status ON bookings(status);
CREATE INDEX idx_booking_created_at ON bookings(created_at);
CREATE INDEX idx_booking_user_id ON bookings(user_id);
CREATE INDEX idx_hotel_destination ON hotels(destination_id);
CREATE INDEX idx_activity_destination ON activities(destination_id);

-- ============================================================================
-- VIEWS (Optional - for easier reporting)
-- ============================================================================

-- View: Get all bookings with destination and hotel details
CREATE VIEW v_bookings_detail AS
SELECT 
  b.id,
  b.reference_code,
  b.guest_name,
  b.guest_email,
  d.name AS destination_name,
  h.name AS hotel_name,
  b.check_in_date,
  b.check_out_date,
  b.number_of_guests,
  b.number_of_rooms,
  b.total_price,
  b.status,
  b.created_at
FROM bookings b
LEFT JOIN destinations d ON b.destination_id = d.id
LEFT JOIN hotels h ON b.hotel_id = h.id;

-- View: Hotel occupancy summary
CREATE VIEW v_hotel_occupancy AS
SELECT 
  h.id,
  h.name,
  COUNT(DISTINCT b.id) AS total_bookings,
  SUM(DATEDIFF(b.check_out_date, b.check_in_date)) AS total_nights,
  SUM(b.total_price) AS revenue
FROM hotels h
LEFT JOIN bookings b ON h.id = b.hotel_id AND b.status != 'cancelled'
GROUP BY h.id, h.name;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
