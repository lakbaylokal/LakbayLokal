<?php
/**
 * Database Helper Functions - CRUD Operations
 * LakbayLokal - Travel Booking System
 * 
 * Simple mysqli wrapper functions for beginners
 * All functions use prepared statements for security
 */

/**
 * ============================================================================
 * USER FUNCTIONS
 * ============================================================================
 */

/**
 * Create a new user account
 * @param mysqli $db
 * @param string $first_name
 * @param string $last_name
 * @param string $email
 * @param string $password (plain text - will be hashed)
 * @return bool|int User ID on success, false on failure
 */
function createUser($db, $first_name, $last_name, $email, $password) {
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $db->prepare("
        INSERT INTO users (first_name, last_name, email, password_hash, role)
        VALUES (?, ?, ?, ?, 'user')
    ");
    
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $password_hash);
    
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

/**
 * Get user by email
 * @param mysqli $db
 * @param string $email
 * @return array|null User data or null if not found
 */
function getUserByEmail($db, $email) {
    $stmt = $db->prepare("SELECT id, first_name, last_name, email, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Verify user password
 * @param string $password Plain text password
 * @param string $password_hash Hashed password from database
 * @return bool
 */
function verifyPassword($password, $password_hash) {
    return password_verify($password, $password_hash);
}

/**
 * ============================================================================
 * DESTINATION FUNCTIONS
 * ============================================================================
 */

/**
 * Get all active destinations
 * @param mysqli $db
 * @return array Array of destinations
 */
function getAllDestinations($db) {
    $result = $db->query("
        SELECT id, name, region, description, price, image_url
        FROM destinations
        WHERE is_active = 1
        ORDER BY name ASC
    ");
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get destination by ID
 * @param mysqli $db
 * @param int $destination_id
 * @return array|null Destination data or null
 */
function getDestinationById($db, $destination_id) {
    $stmt = $db->prepare("
        SELECT id, name, region, description, price, image_url
        FROM destinations
        WHERE id = ? AND is_active = 1
    ");
    
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

/**
 * ============================================================================
 * HOTEL FUNCTIONS
 * ============================================================================
 */

/**
 * Get all hotels for a destination
 * @param mysqli $db
 * @param int $destination_id
 * @return array Array of hotels
 */
function getHotelsByDestination($db, $destination_id) {
    $stmt = $db->prepare("
        SELECT id, name, price_per_night, stars, rating, review_count, 
               location, description, checkin_time, checkout_time
        FROM hotels
        WHERE destination_id = ? AND is_active = 1
        ORDER BY rating DESC
    ");
    
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get hotel details including amenities and policies
 * @param mysqli $db
 * @param int $hotel_id
 * @return array|null Complete hotel data
 */
function getHotelDetails($db, $hotel_id) {
    // Get basic hotel info
    $stmt = $db->prepare("
        SELECT id, destination_id, name, price_per_night, stars, rating,
               review_count, location, description, checkin_time, checkout_time
        FROM hotels
        WHERE id = ? AND is_active = 1
    ");
    
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel = $stmt->get_result()->fetch_assoc();
    
    if (!$hotel) {
        return null;
    }
    
    // Get amenities
    $stmt = $db->prepare("SELECT amenity FROM hotel_amenities WHERE hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel['amenities'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get policies
    $stmt = $db->prepare("SELECT policy FROM hotel_policies WHERE hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel['policies'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return $hotel;
}

/**
 * ============================================================================
 * ACTIVITY FUNCTIONS
 * ============================================================================
 */

/**
 * Get all activities for a destination
 * @param mysqli $db
 * @param int $destination_id
 * @return array Array of activities
 */
function getActivitiesByDestination($db, $destination_id) {
    $stmt = $db->prepare("
        SELECT id, name, price, description
        FROM activities
        WHERE destination_id = ? AND is_active = 1
        ORDER BY name ASC
    ");
    
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get activity by ID
 * @param mysqli $db
 * @param int $activity_id
 * @return array|null Activity data
 */
function getActivityById($db, $activity_id) {
    $stmt = $db->prepare("
        SELECT id, destination_id, name, price, description
        FROM activities
        WHERE id = ? AND is_active = 1
    ");
    
    $stmt->bind_param("i", $activity_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

/**
 * ============================================================================
 * BOOKING FUNCTIONS
 * ============================================================================
 */

/**
 * Create a new booking
 * @param mysqli $db
 * @param array $booking_data Array with booking information
 * @return string|false Reference code on success, false on failure
 */
function createBooking($db, $booking_data) {
    // Generate unique reference code
    $reference_code = 'BK' . strtoupper(substr(uniqid(), -8));
    
    $stmt = $db->prepare("
        INSERT INTO bookings (
            reference_code,
            user_id,
            guest_name,
            guest_email,
            destination_id,
            hotel_id,
            check_in_date,
            check_out_date,
            number_of_guests,
            number_of_rooms,
            subtotal_amount,
            activities_total,
            tax_amount,
            total_price,
            payment_method,
            special_requests,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->bind_param(
        "sissiissiiiiiss",
        $reference_code,
        $booking_data['user_id'],
        $booking_data['guest_name'],
        $booking_data['guest_email'],
        $booking_data['destination_id'],
        $booking_data['hotel_id'],
        $booking_data['check_in_date'],
        $booking_data['check_out_date'],
        $booking_data['number_of_guests'],
        $booking_data['number_of_rooms'],
        $booking_data['subtotal_amount'],
        $booking_data['activities_total'],
        $booking_data['tax_amount'],
        $booking_data['total_price'],
        $booking_data['payment_method'],
        $booking_data['special_requests']
    );
    
    if ($stmt->execute()) {
        $booking_id = $db->insert_id;
        
        // Add selected activities to booking
        if (!empty($booking_data['activities']) && is_array($booking_data['activities'])) {
            addActivitiesToBooking($db, $booking_id, $booking_data['activities']);
        }
        
        return $reference_code;
    }
    
    return false;
}

/**
 * Get booking by reference code
 * @param mysqli $db
 * @param string $reference_code
 * @return array|null Booking data
 */
function getBookingByReference($db, $reference_code) {
    $stmt = $db->prepare("
        SELECT 
            id,
            reference_code,
            guest_name,
            guest_email,
            destination_id,
            hotel_id,
            check_in_date,
            check_out_date,
            number_of_guests,
            number_of_rooms,
            total_price,
            payment_method,
            status,
            created_at
        FROM bookings
        WHERE reference_code = ?
    ");
    
    $stmt->bind_param("s", $reference_code);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get all bookings (for admin)
 * @param mysqli $db
 * @param string $status (optional) Filter by status
 * @return array Array of bookings
 */
function getAllBookings($db, $status = null) {
    if ($status) {
        $stmt = $db->prepare("
            SELECT 
                id,
                reference_code,
                guest_name,
                guest_email,
                total_price,
                status,
                created_at
            FROM bookings
            WHERE status = ?
            ORDER BY created_at DESC
        ");
        
        $stmt->bind_param("s", $status);
    } else {
        $stmt = $db->prepare("
            SELECT 
                id,
                reference_code,
                guest_name,
                guest_email,
                total_price,
                status,
                created_at
            FROM bookings
            ORDER BY created_at DESC
        ");
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Update booking status
 * @param mysqli $db
 * @param int $booking_id
 * @param string $status New status
 * @return bool
 */
function updateBookingStatus($db, $booking_id, $status) {
    $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    return $stmt->execute();
}

/**
 * Add activities to a booking
 * @param mysqli $db
 * @param int $booking_id
 * @param array $activities Array of activity IDs
 * @return bool
 */
function addActivitiesToBooking($db, $booking_id, $activities) {
    $success = true;
    
    foreach ($activities as $activity_id) {
        // Get activity details
        $activity = getActivityById($db, $activity_id);
        if (!$activity) continue;
        
        $stmt = $db->prepare("
            INSERT INTO booking_activities (booking_id, activity_id, activity_name, activity_price)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->bind_param("iisi", $booking_id, $activity['id'], $activity['name'], $activity['price']);
        
        if (!$stmt->execute()) {
            $success = false;
        }
    }
    
    return $success;
}

/**
 * Get activities for a booking
 * @param mysqli $db
 * @param int $booking_id
 * @return array Array of activities in booking
 */
function getBookingActivities($db, $booking_id) {
    $stmt = $db->prepare("
        SELECT activity_id, activity_name, activity_price
        FROM booking_activities
        WHERE booking_id = ?
    ");
    
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * ============================================================================
 * PAYMENT FUNCTIONS
 * ============================================================================
 */

/**
 * Save payment details
 * @param mysqli $db
 * @param int $booking_id
 * @param array $payment_data
 * @return bool
 */
function savePaymentDetails($db, $booking_id, $payment_data) {
    $stmt = $db->prepare("
        INSERT INTO payment_details (
            booking_id,
            payment_method,
            gcash_number,
            gcash_account_name,
            card_holder_name,
            card_last_four,
            card_brand,
            payment_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    // Extract last 4 digits from card
    $card_last_four = isset($payment_data['card_number']) ? 
                      substr($payment_data['card_number'], -4) : 
                      null;
    
    $stmt->bind_param(
        "isssss",
        $booking_id,
        $payment_data['payment_method'],
        $payment_data['gcash_number'] ?? null,
        $payment_data['gcash_account_name'] ?? null,
        $payment_data['card_holder_name'] ?? null,
        $card_last_four,
        $payment_data['card_brand'] ?? null
    );
    
    return $stmt->execute();
}

/**
 * Get payment details for a booking
 * @param mysqli $db
 * @param int $booking_id
 * @return array|null Payment data
 */
function getPaymentDetails($db, $booking_id) {
    $stmt = $db->prepare("
        SELECT *
        FROM payment_details
        WHERE booking_id = ?
    ");
    
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

/**
 * ============================================================================
 * UTILITY FUNCTIONS
 * ============================================================================
 */

/**
 * Calculate nights between two dates
 * @param string $check_in Date in format YYYY-MM-DD
 * @param string $check_out Date in format YYYY-MM-DD
 * @return int Number of nights
 */
function calculateNights($check_in, $check_out) {
    $checkin = new DateTime($check_in);
    $checkout = new DateTime($check_out);
    $interval = $checkin->diff($checkout);
    return $interval->days;
}

/**
 * Calculate total booking amount
 * @param int $price_per_night
 * @param int $nights
 * @param int $rooms
 * @param int $activities_total
 * @return array Array with subtotal, tax, and total
 */
function calculateBookingTotal($price_per_night, $nights, $rooms, $activities_total = 0) {
    $subtotal = ($price_per_night * $nights * $rooms) + $activities_total;
    $tax = round($subtotal * 0.12); // 12% tax
    $total = $subtotal + $tax;
    
    return [
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $total
    ];
}

/**
 * Check if email exists in database
 * @param mysqli $db
 * @param string $email
 * @return bool
 */
function emailExists($db, $email) {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

/**
 * Escape user input for safe display
 * @param string $input
 * @return string Escaped output
 */
function escapeOutput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

?>
