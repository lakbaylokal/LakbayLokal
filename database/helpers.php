<?php
/**
 * DATABASE HELPER FUNCTIONS FOR LAKBAYLOKAL
 * Replace data.php functions with these database queries
 * 
 * Usage in your PHP files:
 * require_once 'config/db.php';
 * require_once 'database/helpers.php';
 * 
 * $destinations = getDestinations($conn);
 * $hotels = getHotelsByDestination($conn, $destId);
 */

// ============================================================================
// DESTINATIONS
// ============================================================================

/**
 * Get all destinations
 */
function getDestinations($conn) {
    $query = "SELECT id, name, region, description, image_url, created_at FROM destinations ORDER BY name";
    $result = $conn->query($query);
    
    if (!$result) {
        return [];
    }
    
    $destinations = [];
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
    return $destinations;
}

/**
 * Get a single destination by ID
 */
function getDestinationById($conn, $destId) {
    $destId = (int)$destId;
    $query = "SELECT id, name, region, description, image_url FROM destinations WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("i", $destId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

/**
 * Get destination by name
 */
function getDestinationByName($conn, $name) {
    $query = "SELECT id, name, region, description, image_url FROM destinations WHERE name = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

// ============================================================================
// HOTELS
// ============================================================================

/**
 * Get all hotels for a destination
 */
function getHotelsByDestination($conn, $destId) {
    $destId = (int)$destId;
    $query = "
        SELECT 
            h.id, 
            h.destination_id, 
            h.name, 
            h.price_per_night as price, 
            h.stars, 
            h.rating, 
            h.review_count,
            h.location, 
            h.description,
            h.checkin_time,
            h.checkout_time
        FROM hotels h 
        WHERE h.destination_id = ? 
        ORDER BY h.price_per_night ASC
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("i", $destId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    
    $stmt->close();
    return $hotels;
}

/**
 * Get a single hotel with details
 */
function getHotelDetails($conn, $destId, $hotelId) {
    $destId = (int)$destId;
    $hotelId = (int)$hotelId;
    
    $query = "
        SELECT 
            h.id, 
            h.destination_id, 
            h.name, 
            h.price_per_night as price, 
            h.stars, 
            h.rating, 
            h.review_count,
            h.location, 
            h.description,
            h.checkin_time,
            h.checkout_time
        FROM hotels h 
        WHERE h.destination_id = ? AND h.id = ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("ii", $destId, $hotelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hotel = $result->fetch_assoc();
    $stmt->close();
    
    if ($hotel) {
        // Get amenities for this hotel
        $hotel['amenities'] = getHotelAmenities($conn, $hotelId);
        // Get policies for this hotel
        $hotel['policies'] = getHotelPolicies($conn, $hotelId);
    }
    
    return $hotel;
}

// ============================================================================
// HOTEL AMENITIES & POLICIES
// ============================================================================

/**
 * Get amenities for a hotel
 */
function getHotelAmenities($conn, $hotelId) {
    $hotelId = (int)$hotelId;
    $query = "SELECT id, amenity FROM hotel_amenities WHERE hotel_id = ? ORDER BY amenity";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("i", $hotelId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $amenities = [];
    while ($row = $result->fetch_assoc()) {
        $amenities[] = $row['amenity'];
    }
    
    $stmt->close();
    return $amenities;
}

/**
 * Get policies for a hotel
 */
function getHotelPolicies($conn, $hotelId) {
    $hotelId = (int)$hotelId;
    $query = "SELECT id, policy FROM hotel_policies WHERE hotel_id = ? ORDER BY created_at";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("i", $hotelId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $policies = [];
    while ($row = $result->fetch_assoc()) {
        $policies[] = $row['policy'];
    }
    
    $stmt->close();
    return $policies;
}

// ============================================================================
// ACTIVITIES
// ============================================================================

/**
 * Get all activities for a destination
 */
function getActivitiesByDestination($conn, $destId) {
    $destId = (int)$destId;
    $query = "
        SELECT id, destination_id, name, price, description 
        FROM activities 
        WHERE destination_id = ? 
        ORDER BY name
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("i", $destId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    $stmt->close();
    return $activities;
}

/**
 * Get a single activity
 */
function getActivityById($conn, $activityId) {
    $activityId = (int)$activityId;
    $query = "SELECT id, destination_id, name, price, description FROM activities WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("i", $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

// ============================================================================
// BOOKINGS
// ============================================================================

/**
 * Create a new booking
 */
function createBooking($conn, $bookingData) {
    /**
     * Expected $bookingData:
     * [
     *     'reference_code' => 'LBL123456',
     *     'user_id' => 1 (or null),
     *     'guest_name' => 'John Doe',
     *     'guest_email' => 'john@example.com',
     *     'destination_id' => 1,
     *     'hotel_id' => 5,
     *     'check_in_date' => '2024-06-15',
     *     'check_out_date' => '2024-06-18',
     *     'number_of_guests' => 2,
     *     'number_of_rooms' => 1,
     *     'subtotal_amount' => 15600,
     *     'activities_total' => 500,
     *     'tax_amount' => 1605,
     *     'total_price' => 17705,
     *     'payment_method' => 'gcash',
     *     'special_requests' => 'High floor preferred'
     * ]
     */
    
    $query = "
        INSERT INTO bookings (
            reference_code, user_id, guest_name, guest_email,
            destination_id, hotel_id, 
            check_in_date, check_out_date,
            number_of_guests, number_of_rooms,
            subtotal_amount, activities_total, tax_amount, total_price,
            payment_method, special_requests, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return ['success' => false, 'error' => $conn->error];
    }
    
    $stmt->bind_param(
        "sisiiissiiiiiss",
        $bookingData['reference_code'],
        $bookingData['user_id'],
        $bookingData['guest_name'],
        $bookingData['guest_email'],
        $bookingData['destination_id'],
        $bookingData['hotel_id'],
        $bookingData['check_in_date'],
        $bookingData['check_out_date'],
        $bookingData['number_of_guests'],
        $bookingData['number_of_rooms'],
        $bookingData['subtotal_amount'],
        $bookingData['activities_total'],
        $bookingData['tax_amount'],
        $bookingData['total_price'],
        $bookingData['payment_method'],
        $bookingData['special_requests']
    );
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => $stmt->error];
    }
    
    $bookingId = $conn->insert_id;
    $stmt->close();
    
    return ['success' => true, 'booking_id' => $bookingId];
}

/**
 * Add activities to a booking
 */
function addActivityToBooking($conn, $bookingId, $activityId, $activityName, $activityPrice) {
    $bookingId = (int)$bookingId;
    $activityId = (int)$activityId;
    $activityPrice = (int)$activityPrice;
    
    $query = "
        INSERT INTO booking_activities (booking_id, activity_id, activity_name, activity_price)
        VALUES (?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("iisi", $bookingId, $activityId, $activityName, $activityPrice);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Get booking by reference code
 */
function getBookingByReference($conn, $referenceCode) {
    $query = "
        SELECT 
            b.id, b.reference_code, b.guest_name, b.guest_email,
            b.destination_id, b.hotel_id,
            b.check_in_date, b.check_out_date,
            b.number_of_guests, b.number_of_rooms,
            b.subtotal_amount, b.activities_total, b.tax_amount, b.total_price,
            b.payment_method, b.special_requests, b.status, b.created_at,
            d.name as destination_name,
            h.name as hotel_name
        FROM bookings b
        LEFT JOIN destinations d ON b.destination_id = d.id
        LEFT JOIN hotels h ON b.hotel_id = h.id
        WHERE b.reference_code = ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("s", $referenceCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    if ($booking) {
        // Get activities for this booking
        $booking['activities'] = getBookingActivities($conn, $booking['id']);
    }
    
    return $booking;
}

/**
 * Get activities for a booking
 */
function getBookingActivities($conn, $bookingId) {
    $bookingId = (int)$bookingId;
    $query = "
        SELECT ba.id, ba.activity_id, ba.activity_name, ba.activity_price
        FROM booking_activities ba
        WHERE ba.booking_id = ?
        ORDER BY ba.added_at
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    $stmt->close();
    return $activities;
}

// ============================================================================
// USERS/AUTHENTICATION
// ============================================================================

/**
 * Get user by email
 */
function getUserByEmail($conn, $email) {
    $query = "SELECT id, first_name, last_name, email, password_hash, role FROM users WHERE email = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Create a new user
 */
function createUser($conn, $firstName, $lastName, $email, $passwordHash) {
    $query = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return ['success' => false, 'error' => $conn->error];
    }
    
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $passwordHash);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => $stmt->error];
    }
    
    $userId = $conn->insert_id;
    $stmt->close();
    
    return ['success' => true, 'user_id' => $userId];
}

// ============================================================================
// SEARCH & FILTER
// ============================================================================

/**
 * Search hotels by destination and price range
 */
function searchHotels($conn, $destId = null, $minPrice = 0, $maxPrice = 99999, $minStars = 1) {
    $query = "
        SELECT 
            h.id, h.destination_id, h.name, h.price_per_night as price,
            h.stars, h.rating, h.review_count, h.location, h.description,
            d.name as destination_name
        FROM hotels h
        LEFT JOIN destinations d ON h.destination_id = d.id
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if ($destId !== null) {
        $destId = (int)$destId;
        $query .= " AND h.destination_id = ?";
        $params[] = $destId;
        $types .= "i";
    }
    
    $query .= " AND h.price_per_night BETWEEN ? AND ?";
    $params[] = $minPrice;
    $params[] = $maxPrice;
    $types .= "ii";
    
    $query .= " AND h.stars >= ?";
    $params[] = $minStars;
    $types .= "i";
    
    $query .= " ORDER BY h.price_per_night ASC";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    
    $stmt->close();
    return $hotels;
}

/**
 * Generate unique booking reference code
 */
function generateBookingReference($conn) {
    // Format: LBL + YYYYMMDD + random 5 digits
    $prefix = 'LBL';
    $date = date('Ymd');
    
    // Keep generating until we find a unique one
    for ($i = 0; $i < 100; $i++) {
        $random = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $reference = $prefix . $date . $random;
        
        $query = "SELECT id FROM bookings WHERE reference_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $reference);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows === 0) {
            return $reference;
        }
    }
    
    return null; // Fallback (very unlikely)
}

// ============================================================================
// STATISTICS / ADMIN
// ============================================================================

/**
 * Get booking statistics for admin dashboard
 */
function getBookingStats($conn) {
    $query = "
        SELECT
            COUNT(*) as total_bookings,
            SUM(total_price) as total_revenue,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM bookings
    ";
    
    $result = $conn->query($query);
    if (!$result) {
        return [];
    }
    
    return $result->fetch_assoc();
}

/**
 * Get top hotels by bookings
 */
function getTopHotels($conn, $limit = 10) {
    $query = "
        SELECT 
            h.id, h.name, d.name as destination,
            COUNT(b.id) as booking_count,
            SUM(b.total_price) as total_revenue
        FROM hotels h
        LEFT JOIN bookings b ON h.id = b.hotel_id AND b.status != 'cancelled'
        LEFT JOIN destinations d ON h.destination_id = d.id
        GROUP BY h.id, h.name, d.name
        ORDER BY booking_count DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return [];
    }
    
    $limit = (int)$limit;
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    
    $stmt->close();
    return $hotels;
}

?>