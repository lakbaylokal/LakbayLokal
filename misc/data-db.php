<?php
/**
 * data-db.php — LakbayLokal Database-Connected Data Layer
 * Replaces hardcoded data.php with dynamic database queries
 * 
 * SETUP INSTRUCTIONS:
 * 1. Import lakbaylokal.sql into your MySQL/XAMPP database
 * 2. Update the database connection details below (host, user, password)
 * 3. Replace require_once 'data.php' with require_once 'data-db.php' in your pages
 */

// ============================================================================
// DATABASE CONNECTION
// ============================================================================

$db_host = 'localhost';      // XAMPP default
$db_user = 'root';           // XAMPP default
$db_pass = '';               // XAMPP default (no password)
$db_name = 'lakbaylokal';    // Database name

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die('Database Connection Failed: ' . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// ============================================================================
// HELPER: Build Destinations Array from Database
// ============================================================================

function buildDestinationsArray($conn) {
    $destinations = [];
    
    $query = "SELECT * FROM destinations ORDER BY name ASC";
    $result = $conn->query($query);
    
    if (!$result) {
        die("Query Error: " . $conn->error);
    }
    
    while ($dest = $result->fetch_assoc()) {
        $dest_id = $dest['id'];
        
        // Fetch hotels for this destination
        $hotels_query = "SELECT * FROM hotels WHERE destination_id = ? ORDER BY rating DESC";
        $hotels_stmt = $conn->prepare($hotels_query);
        $hotels_stmt->bind_param("s", $dest_id);
        $hotels_stmt->execute();
        $hotels_result = $hotels_stmt->get_result();
        
        $hotels = [];
        while ($hotel = $hotels_result->fetch_assoc()) {
            $hotel_id = $hotel['id'];
            
            // Fetch amenities for this hotel
            $amenities_query = "SELECT amenity_name FROM hotel_amenities WHERE hotel_id = ?";
            $amenities_stmt = $conn->prepare($amenities_query);
            $amenities_stmt->bind_param("s", $hotel_id);
            $amenities_stmt->execute();
            $amenities_result = $amenities_stmt->get_result();
            
            $amenities = [];
            while ($amenity = $amenities_result->fetch_assoc()) {
                $amenities[] = $amenity['amenity_name'];
            }
            $amenities_stmt->close();
            
            // Fetch policies for this hotel
            $policies_query = "SELECT policy FROM hotel_policies WHERE hotel_id = ?";
            $policies_stmt = $conn->prepare($policies_query);
            $policies_stmt->bind_param("s", $hotel_id);
            $policies_stmt->execute();
            $policies_result = $policies_stmt->get_result();
            
            $policies = [];
            while ($policy = $policies_result->fetch_assoc()) {
                $policies[] = $policy['policy'];
            }
            $policies_stmt->close();
            
            // Add to hotels array
            $hotels[] = [
                'id' => $hotel['id'],
                'name' => $hotel['name'],
                'image' => $hotel['image_url'],
                'stars' => (int)$hotel['stars'],
                'price' => (int)$hotel['price'],
                'rating' => (float)$hotel['rating'],
                'reviews' => (int)$hotel['reviews_count'],
                'location' => $hotel['location'],
                'desc' => $hotel['description'],
                'amenities' => $amenities,
                'checkin' => $hotel['checkin_time'],
                'checkout' => $hotel['checkout_time'],
                'policies' => $policies,
            ];
        }
        $hotels_stmt->close();
        
        // Fetch activities for this destination
        $activities_query = "SELECT * FROM activities WHERE destination_id = ?";
        $activities_stmt = $conn->prepare($activities_query);
        $activities_stmt->bind_param("s", $dest_id);
        $activities_stmt->execute();
        $activities_result = $activities_stmt->get_result();
        
        $activities = [];
        while ($activity = $activities_result->fetch_assoc()) {
            $activities[] = [
                'name' => $activity['name'],
                'price' => (int)$activity['price'],
            ];
        }
        $activities_stmt->close();
        
        // Add to destinations array
        $destinations[] = [
            'id' => $dest['id'],
            'name' => $dest['name'],
            'region' => $dest['region'],
            'price' => (int)$dest['price'],
            'emoji' => $dest['emoji'],
            'gradient' => $dest['gradient_bg'],
            'tagline' => $dest['tagline'],
            'desc' => $dest['description'],
            'activities' => ['placeholder'],  // Legacy support
            'price_from' => (int)$dest['price_from'],
            'hotels' => $hotels,
            'acts' => $activities,
        ];
    }
    
    return $destinations;
}

// ============================================================================
// BUILD MAIN DATA STRUCTURES
// ============================================================================

$destinations = buildDestinationsArray($conn);

// ============================================================================
// HELPER FUNCTIONS (from original data.php)
// ============================================================================

function getDestById(string $id): ?array {
    global $destinations;
    foreach ($destinations as $d) {
        if ($d['id'] === $id) return $d;
    }
    return null;
}

function getHotelById(string $destId, string $hotelId): ?array {
    $dest = getDestById($destId);
    if (!$dest) return null;
    foreach ($dest['hotels'] as $h) {
        if ($h['id'] === $hotelId) return $h;
    }
    return null;
}

// ============================================================================
// DATABASE ACCESS FUNCTION (for direct queries if needed)
// ============================================================================

function getDBConnection() {
    global $conn;
    return $conn;
}

?>
