-- ============================================================================
-- LAKBAYLOKAL DATABASE SCHEMA - COMPLETE WITH ALL DESTINATION DATA
-- Travel Booking System (PHP + MySQL)
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS lakbaylokal;
USE lakbaylokal;

-- ============================================================================
-- 1. USERS TABLE
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
-- 2. DESTINATIONS TABLE
-- ============================================================================
CREATE TABLE destinations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  region ENUM('Luzon', 'Visayas', 'Mindanao') NOT NULL,
  description LONGTEXT,
  image_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_region (region)
);

-- ============================================================================
-- 3. HOTELS TABLE
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
-- 8. BOOKING_ACTIVITIES TABLE (Junction table for M:M)
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
-- ============================================================================
CREATE TABLE payment_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL UNIQUE,
  
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
  
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  KEY idx_status (payment_status)
);

-- ============================================================================
-- SAMPLE DATA - ALL 8 DESTINATIONS COMPLETE
-- ============================================================================

-- Insert all destinations
INSERT INTO destinations (name, region, description, image_url) VALUES
('Baguio City', 'Luzon', 'The Summer Capital of the Philippines - Escape to cool mountain air and pine forests', '/assets/pics/Baguio.jpg'),
('Vigan City', 'Luzon', 'UNESCO World Heritage City - Colonial charm and heritage architecture from the Spanish era', '/assets/pics/vigan.jpg'),
('Palawan', 'Luzon', 'Last Frontier of the Philippines - Crystal lagoons, El Nido, and world-class diving', '/assets/pics/palawan.jpg'),
('Cebu City', 'Visayas', 'The Queen City of the South - Beaches, history, and adventure activities', '/assets/pics/Cebu2.jpg'),
('Boracay Island', 'Visayas', 'World-Famous White Sand Beach - Paradise for beach lovers and water sports', '/assets/pics/boracay.jpg'),
('Siargao Island', 'Mindanao', 'Surfing Capital of the Philippines - Laid-back beach vibes and cloud nine waves', '/assets/pics/siargao.jpg'),
('Bukidnon', 'Mindanao', 'Highland adventures - Ziplines, waterfalls, and outdoor thrills', '/assets/pics/bukidno.jpg'),
('Camiguin Island', 'Mindanao', 'Island Born of Fire - Waterfalls, hot springs, and volcanic wonders', '/assets/pics/camiguin.jpg');

-- ============================================================================
-- HOTELS: BAGUIO CITY (id=1)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(1, 'Sotogrande Hotel Baguio', 5200, 4, 4.6, 312, 'Session Road, Baguio City', 'A modern boutique hotel with luxurious rooms and stunning mountain views. Perfect for a relaxing getaway.'),
(1, 'Microtel by Wyndham Baguio', 3600, 3, 4.3, 205, 'Legarda Road, Baguio City', 'Comfortable rooms with reliable service near SM City. Great value for money with excellent amenities.'),
(1, 'Travelite Express Hotel', 2800, 2, 4.1, 148, 'Magsaysay Avenue, Baguio City', 'Budget-friendly option perfect for backpackers and students. Basic but clean rooms with friendly staff.');

-- ============================================================================
-- HOTELS: VIGAN CITY (id=2)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(2, 'Hotel Luna', 5800, 4, 4.8, 421, 'Calle Crisologo, Vigan City', 'Award-winning boutique hotel in a century-old ancestral mansion. Combines heritage with modern comfort.'),
(2, 'Hotel Felicidad Vigan', 4200, 3, 4.4, 186, 'Quirino Boulevard, Vigan City', 'Charming heritage hotel with period-inspired décor and welcoming staff. Great cultural experience.'),
(2, 'Paradores de Vigan', 3500, 3, 4.2, 97, 'Mena Crisologo Street, Vigan City', 'Family-run hotel in a restored colonial building. Perfect for travelers who want authentic heritage experience.');

-- ============================================================================
-- HOTELS: PALAWAN (id=3)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(3, 'Twin Bay Beach Resort', 6500, 4, 4.7, 298, 'Sabang Beach, El Nido, Palawan', 'Beachfront resort with private beach access and stunning lagoon views. Perfect for families and couples.'),
(3, 'Palawan Uno Motel', 2800, 2, 3.9, 142, 'Puerto Princesa, Palawan', 'Budget-friendly beachfront accommodation. Close to underground river and city attractions.'),
(3, 'Pambato Beach House', 4200, 3, 4.3, 167, 'Coron, Palawan', 'Intimate beachfront property with wreck diving nearby. Great for adventurous travelers.');

-- ============================================================================
-- HOTELS: CEBU CITY (id=4)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(4, 'Radisson Blu Cebu', 7500, 5, 4.8, 387, 'Fuente Osmena Circle, Cebu City', 'Luxury 5-star hotel with world-class service and facilities. Best in class dining and spa.'),
(4, 'Waterfront Cebu City Hotel', 5800, 4, 4.5, 256, 'Salinas Drive, Cebu City', 'Premier beachfront hotel with excellent restaurants and meeting facilities.'),
(4, 'Sunlight Hotel Cebu', 3200, 2, 4.0, 189, 'Jones Avenue, Cebu City', 'Economy hotel perfect for business travelers and backpackers. Walking distance to shopping centers.');

-- ============================================================================
-- HOTELS: BORACAY ISLAND (id=5)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(5, 'Fairways & Bluewater Boracay', 8500, 5, 4.9, 456, 'Station 1, Boracay Island', 'Ultra-luxury beachfront resort with world-class amenities. Private beach, fine dining, and spa.'),
(5, 'Calypso Boracay Resort', 6200, 4, 4.6, 334, 'Station 2, Boracay Island', 'Premium beach resort with modern facilities and excellent service. Perfect for water sports.'),
(5, 'Boracay Beach Resort', 3800, 2, 4.2, 201, 'Station 3, Boracay Island', 'Budget beachfront resort with direct beach access. Great for backpackers and budget travelers.');

-- ============================================================================
-- HOTELS: SIARGAO ISLAND (id=6)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(6, 'Siargao Bleu Resort & Spa', 4800, 4, 4.6, 245, 'Cloud Nine, Siargao Island', 'Surf-friendly resort with easy access to Cloud Nine beach. Professional instructors available.'),
(6, 'Generation Boardhouse', 2200, 2, 4.1, 118, 'General Luna, Siargao Island', 'Laid-back hostel-style accommodation. Perfect for surfers and backpackers. Vibrant community atmosphere.');

-- ============================================================================
-- HOTELS: BUKIDNON (id=7)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(7, 'Dahilayan Forest Park Resort', 3500, 3, 4.4, 167, 'Dahilayan, Bukidnon', 'Nature resort with zipline adventures and forest activities. Perfect for thrill-seekers and families.'),
(7, 'Iligan Gateway Hotel', 2400, 2, 4.0, 89, 'Iligan City, Bukidnon', 'Convenient base for exploring Bukidnon attractions. Good access to Maria Cristina Falls.');

-- ============================================================================
-- HOTELS: CAMIGUIN ISLAND (id=8)
-- ============================================================================
INSERT INTO hotels (destination_id, name, price_per_night, stars, rating, review_count, location, description) VALUES
(8, 'Sunken Garden Resort', 5500, 4, 4.7, 289, 'Mambajao, Camiguin Island', 'Beachfront resort with access to waterfalls and hot springs. Excellent for relaxation and adventure.'),
(8, 'Camiguin Blue Water Resort', 3800, 3, 4.3, 156, 'White Island, Camiguin Island', 'Island resort experience with pristine beaches and snorkeling opportunities.');

-- ============================================================================
-- HOTEL AMENITIES
-- ============================================================================

-- Baguio City Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(1, 'Free WiFi'), (1, 'Restaurant'), (1, 'Pool'), (1, 'Gym'), (1, 'Parking'), (1, 'Room Service'), (1, 'Air Conditioning'), (1, 'TV'), (1, 'Minibar'), (1, 'Spa'),
(2, 'Free WiFi'), (2, 'Breakfast'), (2, 'Parking'), (2, 'Laundry Service'), (2, 'Air Conditioning'), (2, '24/7 Front Desk'),
(3, 'Free WiFi'), (3, 'Air Conditioning'), (3, '24/7 Front Desk'), (3, 'Basic Cable TV');

-- Vigan City Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(4, 'Free WiFi'), (4, 'Restaurant'), (4, 'Parking'), (4, 'Spa'), (4, 'Room Service'), (4, 'Air Conditioning'), (4, 'Library'),
(5, 'Free WiFi'), (5, 'Breakfast'), (5, 'Parking'), (5, 'Laundry Service'),
(6, 'Free WiFi'), (6, 'Parking'), (6, 'Basic Cafe');

-- Palawan Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(7, 'Free WiFi'), (7, 'Restaurant'), (7, 'Pool'), (7, 'Diving Equipment'), (7, 'Beach Access'), (7, 'Room Service'),
(8, 'Free WiFi'), (8, 'Air Conditioning'), (8, 'Parking'), (8, 'Beach Access'),
(9, 'Free WiFi'), (9, 'Restaurant'), (9, 'Dive Shop'), (9, 'Snorkeling Gear');

-- Cebu City Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(10, 'Free WiFi'), (10, 'Restaurant'), (10, 'Pool'), (10, 'Gym'), (10, 'Spa'), (10, 'Business Center'), (10, 'Parking'), (10, 'Room Service'),
(11, 'Free WiFi'), (11, 'Restaurant'), (11, 'Pool'), (11, 'Parking'), (11, 'Beach Access'),
(12, 'Free WiFi'), (12, 'Breakfast'), (12, 'Parking');

-- Boracay Island Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(13, 'Free WiFi'), (13, 'Restaurant'), (13, 'Pool'), (13, 'Spa'), (13, 'Beach Access'), (13, 'Room Service'), (13, 'Water Sports'),
(14, 'Free WiFi'), (14, 'Restaurant'), (14, 'Pool'), (14, 'Beach Access'), (14, 'Parking'),
(15, 'Free WiFi'), (15, 'Breakfast'), (15, 'Beach Access'), (15, 'Parking');

-- Siargao Island Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(16, 'Free WiFi'), (16, 'Restaurant'), (16, 'Pool'), (16, 'Surf Equipment'), (16, 'Beach Access'), (16, 'Parking'),
(17, 'Free WiFi'), (17, 'Breakfast'), (17, 'Surf Board Rental'), (17, 'Beach Access');

-- Bukidnon Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(18, 'Free WiFi'), (18, 'Restaurant'), (18, 'Zipline Tours'), (18, 'Parking'), (18, 'Room Service'),
(19, 'Free WiFi'), (19, 'Breakfast'), (19, 'Parking');

-- Camiguin Island Hotels
INSERT INTO hotel_amenities (hotel_id, amenity) VALUES
(20, 'Free WiFi'), (20, 'Restaurant'), (20, 'Pool'), (20, 'Spa'), (20, 'Beach Access'), (20, 'Room Service'),
(21, 'Free WiFi'), (21, 'Restaurant'), (21, 'Snorkeling Gear'), (21, 'Beach Access');

-- ============================================================================
-- ACTIVITIES: BAGUIO CITY
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(1, 'Strawberry Picking at La Trinidad Farm', 250, 'Pick fresh strawberries and enjoy farm-fresh jam at La Trinidad'),
(1, 'BenCab Museum Gallery Tour', 200, 'Explore contemporary art at the prestigious BenCab Museum'),
(1, 'Tree Top Adventure – Camp John Hay', 400, 'Zipline through the pine forests with stunning views'),
(1, 'Igorot Stone Kingdom Exploration', 150, 'Learn about indigenous Igorot heritage and stone carvings');

-- ============================================================================
-- ACTIVITIES: VIGAN CITY
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(2, 'Calesa Ride around Calle Crisologo', 250, 'Experience colonial Vigan via traditional calesa horse-drawn carriage'),
(2, 'Pagburnayan Jar Factory Pottery Making', 300, 'Make your own pottery using traditional techniques'),
(2, 'Vigan Museum / Syquia Mansion Tour', 180, 'Guided tour of historic colonial mansion and museum');

-- ============================================================================
-- ACTIVITIES: PALAWAN
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(3, 'Puerto Princesa Underground River Tour', 600, 'Explore the famous UNESCO World Heritage underground river'),
(3, 'El Nido Island Hopping', 800, 'Visit lagoons, beaches, and islands around El Nido'),
(3, 'Coron Wreck Diving', 900, 'Dive Japanese WWII shipwrecks - world-class diving site'),
(3, 'Palawan Safari Tour', 450, 'Wildlife observation tour in Palawan wildlife sanctuary');

-- ============================================================================
-- ACTIVITIES: CEBU CITY
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(4, 'Whale Shark Encounter in Oslob', 1100, 'Swim with gentle whale sharks - a once-in-a-lifetime experience'),
(4, 'Kawasan Falls Canyoneering', 750, 'Adventure activity including rappelling and swimming'),
(4, 'Mactan Island Beach Resort Day', 400, 'Full day beach and water sports at Mactan Island'),
(4, 'Cebu Historical City Tour', 350, 'Tour of Cebu City historic sites and landmarks');

-- ============================================================================
-- ACTIVITIES: BORACAY ISLAND
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(5, 'Parasailing and Water Sports', 850, 'Parasailing, jet skiing, and other water sports'),
(5, 'Island Hopping Tour', 600, 'Visit nearby islands and snorkel in clear waters'),
(5, 'Sunset Cruise with Dinner', 950, 'Romantic dinner cruise with sunset views'),
(5, 'Coral Garden Snorkeling', 400, 'Snorkel in vibrant coral gardens');

-- ============================================================================
-- ACTIVITIES: SIARGAO ISLAND
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(6, 'Surf Lesson at Cloud Nine Beach', 700, 'Professional surf lessons at famous Cloud Nine break'),
(6, 'Island Hopping and Snorkeling', 600, 'Explore neighboring islands with snorkeling'),
(6, 'Guided Lagoon Tour', 350, 'Scenic boat tour of Siargao lagoons'),
(6, 'Street Fish Market Tour and Cooking Class', 450, 'Learn local fishing traditions and cook local dishes');

-- ============================================================================
-- ACTIVITIES: BUKIDNON
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(7, 'Dahilayan Zipline Adventure', 650, 'Thrilling zipline courses through forest canopy'),
(7, 'Maria Cristina Falls Tour', 300, 'Visit the majestic power-generating waterfalls'),
(7, 'Indigenous Lumad Village Tour', 400, 'Learn about Lumad indigenous culture and traditions'),
(7, 'River Trekking and Canyoning', 550, 'Adventure trekking along rivers and canyons');

-- ============================================================================
-- ACTIVITIES: CAMIGUIN ISLAND
-- ============================================================================
INSERT INTO activities (destination_id, name, price, description) VALUES
(8, 'Soda Spring Bath and White Island Tour', 450, 'Visit natural soda springs and pristine White Island'),
(8, 'Sunken Cemetery Snorkeling', 500, 'Unique snorkeling at the famous sunken cemetery'),
(8, 'Hibok-Hibok Volcano Trek', 400, 'Hike active volcano for panoramic island views'),
(8, 'Hot Spring and Waterfall Combination Tour', 380, 'Relax in natural hot springs and visit waterfalls');

-- ============================================================================
-- SAMPLE USERS
-- ============================================================================
INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Admin', 'User', 'admin@lakbaylokal.com', '$2y$10$Z3Tm5wTFjXBJG8qN4bL2NO9pqkZmWsXxZ3dRlQ0L9VhYzZ4kG1lzS', 'admin'),
('John', 'Doe', 'john@example.com', '$2y$10$1gPWzcwZpMQYEF3Q8xK2Q.8wJ3K8xZ2Q9mN4fG7pR3R3sT5uE2qJ2', 'user'),
('Maria', 'Santos', 'maria@example.com', '$2y$10$x3M8nK9oP2L1Q4R5S6T7U8V9W0X1Y2Z3A4B5C6D7E8F9G0H1I2J3', 'user');

-- ============================================================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================================================
CREATE INDEX idx_booking_status ON bookings(status);
CREATE INDEX idx_booking_created_at ON bookings(created_at);
CREATE INDEX idx_booking_user_id ON bookings(user_id);
CREATE INDEX idx_hotel_destination ON hotels(destination_id);
CREATE INDEX idx_activity_destination ON activities(destination_id);

-- ============================================================================
-- VIEWS FOR REPORTING
-- ============================================================================

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

CREATE VIEW v_hotel_occupancy AS
SELECT 
  h.id,
  h.name,
  h.destination_id,
  COUNT(DISTINCT b.id) AS total_bookings,
  SUM(DATEDIFF(b.check_out_date, b.check_in_date)) AS total_nights,
  SUM(b.total_price) AS total_revenue
FROM hotels h
LEFT JOIN bookings b ON h.id = b.hotel_id AND b.status != 'cancelled'
GROUP BY h.id, h.name, h.destination_id;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================