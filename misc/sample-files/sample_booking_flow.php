<?php
/**
 * Complete Booking Flow Example
 * LakbayLokal - Travel Booking System
 * 
 * This file shows the complete flow from browsing to booking confirmation.
 * Copy and adapt this code to your actual application.
 */

require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// Start session for user data
session_start();

// ============================================================================
// STEP 1: AUTHENTICATION CHECK
// ============================================================================

/**
 * Check if user is logged in
 * Redirect to login if not
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Register new user
 * POST: first_name, last_name, email, password, password_confirm
 */
function handleRegistration($db) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        return ['success' => false, 'message' => 'Invalid request'];
    }
    
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validate input
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if ($password !== $password_confirm) {
        return ['success' => false, 'message' => 'Passwords do not match'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Validate email
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    // Check if email exists
    if (emailExists($db, $email)) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Create user
    $user_id = createUser($db, $first_name, $last_name, $email, $password);
    
    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        $_SESSION['user_email'] = $email;
        
        return ['success' => true, 'message' => 'Registration successful'];
    }
    
    return ['success' => false, 'message' => 'Registration failed. Please try again'];
}

/**
 * Login user
 * POST: email, password
 */
function handleLogin($db) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        return ['success' => false, 'message' => 'Invalid request'];
    }
    
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (!$email || empty($password)) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Get user
    $user = getUserByEmail($db, $email);
    
    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    // Verify password
    if (!verifyPassword($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid password'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];
    
    return ['success' => true, 'message' => 'Login successful'];
}

/**
 * Logout user
 */
function handleLogout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

// ============================================================================
// STEP 2: BROWSE DESTINATIONS
// ============================================================================

/**
 * Display all destinations
 */
function displayDestinations($db) {
    $destinations = getAllDestinations($db);
    
    echo '<div class="destinations-grid">';
    
    foreach ($destinations as $dest) {
        echo '<div class="destination-card">';
        echo '<img src="' . escapeOutput($dest['image_url']) . '" alt="' . escapeOutput($dest['name']) . '">';
        echo '<h3>' . escapeOutput($dest['name']) . '</h3>';
        echo '<p class="region">📍 ' . escapeOutput($dest['region']) . '</p>';
        echo '<p class="description">' . escapeOutput(substr($dest['description'], 0, 100)) . '...</p>';
        echo '<p class="price">From ₱' . number_format($dest['price']) . '</p>';
        echo '<a href="hotels.php?dest=' . $dest['id'] . '" class="btn-view">View Hotels →</a>';
        echo '</div>';
    }
    
    echo '</div>';
}

// ============================================================================
// STEP 3: BROWSE HOTELS IN DESTINATION
// ============================================================================

/**
 * Display hotels for a destination
 */
function displayHotels($db, $destination_id) {
    // Validate destination exists
    $destination = getDestinationById($db, $destination_id);
    if (!$destination) {
        echo '<p>Destination not found</p>';
        return;
    }
    
    echo '<h1>Hotels in ' . escapeOutput($destination['name']) . '</h1>';
    
    $hotels = getHotelsByDestination($db, $destination_id);
    
    if (empty($hotels)) {
        echo '<p>No hotels available</p>';
        return;
    }
    
    echo '<div class="hotels-list">';
    
    foreach ($hotels as $hotel) {
        echo '<div class="hotel-card">';
        echo '<h3>' . escapeOutput($hotel['name']) . '</h3>';
        echo '<p class="rating">⭐ ' . $hotel['rating'] . '/5 (' . $hotel['review_count'] . ' reviews)</p>';
        echo '<p class="location">📍 ' . escapeOutput($hotel['location']) . '</p>';
        echo '<p class="price">₱' . number_format($hotel['price_per_night']) . '/night</p>';
        echo '<p class="times">';
        echo 'Check-in: ' . date('g:i A', strtotime($hotel['checkin_time'])) . ' | ';
        echo 'Check-out: ' . date('g:i A', strtotime($hotel['checkout_time']));
        echo '</p>';
        echo '<p class="description">' . escapeOutput(substr($hotel['description'], 0, 150)) . '...</p>';
        echo '<a href="book.php?dest=' . $destination_id . '&hotel=' . $hotel['id'] . '" class="btn-book">Book Now →</a>';
        echo '</div>';
    }
    
    echo '</div>';
}

// ============================================================================
// STEP 4: BOOKING PAGE
// ============================================================================

/**
 * Display booking form
 */
function displayBookingForm($db, $destination_id, $hotel_id) {
    // Get destination and hotel
    $destination = getDestinationById($db, $destination_id);
    $hotel = getHotelDetails($db, $hotel_id);
    
    if (!$destination || !$hotel) {
        echo '<p>Invalid destination or hotel</p>';
        return;
    }
    
    // Get activities
    $activities = getActivitiesByDestination($db, $destination_id);
    
    echo '<h1>Book ' . escapeOutput($hotel['name']) . '</h1>';
    echo '<h2>' . escapeOutput($destination['name']) . '</h2>';
    
    echo '<form method="POST" action="confirm_booking.php" id="bookingForm">';
    echo '<input type="hidden" name="destination_id" value="' . $destination_id . '">';
    echo '<input type="hidden" name="hotel_id" value="' . $hotel_id . '">';
    echo '<input type="hidden" name="hotel_price" value="' . $hotel['price_per_night'] . '">';
    
    // Guest info
    echo '<fieldset><legend>Guest Information</legend>';
    
    if (isset($_SESSION['user_id'])) {
        echo '<p>Logged in as: ' . escapeOutput($_SESSION['user_name']) . '</p>';
        echo '<input type="hidden" name="user_id" value="' . $_SESSION['user_id'] . '">';
        echo '<input type="hidden" name="guest_name" value="' . escapeOutput($_SESSION['user_name']) . '">';
        echo '<input type="hidden" name="guest_email" value="' . escapeOutput($_SESSION['user_email']) . '">';
    } else {
        echo '<label>Full Name: <input type="text" name="guest_name" required></label>';
        echo '<label>Email: <input type="email" name="guest_email" required></label>';
    }
    
    echo '</fieldset>';
    
    // Dates
    echo '<fieldset><legend>Dates</legend>';
    echo '<label>Check-in: <input type="date" name="check_in" required></label>';
    echo '<label>Check-out: <input type="date" name="check_out" required></label>';
    echo '</fieldset>';
    
    // Guests and rooms
    echo '<fieldset><legend>Party Details</legend>';
    echo '<label>Guests: <select name="guests">';
    for ($i = 1; $i <= 5; $i++) {
        echo '<option value="' . $i . '">' . $i . '</option>';
    }
    echo '</select></label>';
    
    echo '<label>Rooms: <select name="rooms">';
    for ($i = 1; $i <= 3; $i++) {
        echo '<option value="' . $i . '">' . $i . '</option>';
    }
    echo '</select></label>';
    echo '</fieldset>';
    
    // Activities
    if (!empty($activities)) {
        echo '<fieldset><legend>Add Activities</legend>';
        echo '<p>Select activities to add to your booking:</p>';
        
        foreach ($activities as $activity) {
            echo '<label>';
            echo '<input type="checkbox" name="activities[]" value="' . $activity['id'] . '">';
            echo escapeOutput($activity['name']) . ' - ₱' . number_format($activity['price']);
            echo '</label><br>';
        }
        
        echo '</fieldset>';
    }
    
    // Special requests
    echo '<fieldset><legend>Special Requests</legend>';
    echo '<label>Any special requests?</label>';
    echo '<textarea name="special_requests" rows="3" placeholder="e.g., high floor, early check-in..."></textarea>';
    echo '</fieldset>';
    
    // Payment method
    echo '<fieldset><legend>Payment Method</legend>';
    echo '<label><input type="radio" name="payment_method" value="gcash" required> GCash</label><br>';
    echo '<label><input type="radio" name="payment_method" value="credit_card"> Credit Card</label><br>';
    echo '<label><input type="radio" name="payment_method" value="debit_card"> Debit Card</label>';
    echo '</fieldset>';
    
    // Price summary
    echo '<div class="price-summary">';
    echo '<h3>Price Summary</h3>';
    echo '<p>Hotel: ₱<span id="hotelCost">0</span></p>';
    echo '<p>Activities: ₱<span id="activitiesCost">0</span></p>';
    echo '<p>Subtotal: ₱<span id="subtotal">0</span></p>';
    echo '<p>Tax (12%): ₱<span id="tax">0</span></p>';
    echo '<h4>Total: ₱<span id="total" style="color: #e74c3c;">0</span></h4>';
    echo '<input type="hidden" name="total_price" id="totalInput" value="0">';
    echo '<input type="hidden" name="activities_total" id="activitiesTotalInput" value="0">';
    echo '<input type="hidden" name="tax_amount" id="taxInput" value="0">';
    echo '<input type="hidden" name="subtotal_amount" id="subtotalInput" value="0">';
    echo '</div>';
    
    echo '<button type="submit" class="btn-submit">Proceed to Payment →</button>';
    echo '</form>';
    
    // JavaScript for price calculation
    echo '<script>
    const hotelPrice = ' . $hotel['price_per_night'] . ';
    
    function calculatePrice() {
        // Calculate nights
        const checkin = new Date(document.querySelector("input[name=check_in]").value);
        const checkout = new Date(document.querySelector("input[name=check_out]").value);
        const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
        
        if (nights <= 0) {
            document.getElementById("hotelCost").textContent = "0";
            document.getElementById("total").textContent = "0";
            return;
        }
        
        // Get rooms
        const rooms = parseInt(document.querySelector("input[name=rooms]").value || 1);
        
        // Calculate hotel cost
        const hotelCost = hotelPrice * nights * rooms;
        document.getElementById("hotelCost").textContent = hotelCost.toLocaleString();
        
        // Calculate activities cost
        const selectedActivities = document.querySelectorAll("input[name=\"activities[]\"]]:checked");
        let activitiesCost = 0;
        selectedActivities.forEach(activity => {
            const row = activity.parentElement;
            const priceText = row.textContent.match(/₱([0-9,]+)/);
            if (priceText) {
                activitiesCost += parseInt(priceText[1].replace(/,/g, ""));
            }
        });
        document.getElementById("activitiesCost").textContent = activitiesCost.toLocaleString();
        
        // Calculate totals
        const subtotal = hotelCost + activitiesCost;
        const tax = Math.round(subtotal * 0.12);
        const total = subtotal + tax;
        
        document.getElementById("subtotal").textContent = subtotal.toLocaleString();
        document.getElementById("tax").textContent = tax.toLocaleString();
        document.getElementById("total").textContent = total.toLocaleString();
        
        document.getElementById("subtotalInput").value = subtotal;
        document.getElementById("activitiesTotalInput").value = activitiesCost;
        document.getElementById("taxInput").value = tax;
        document.getElementById("totalInput").value = total;
    }
    
    // Recalculate on change
    document.querySelector("input[name=check_in]").addEventListener("change", calculatePrice);
    document.querySelector("input[name=check_out]").addEventListener("change", calculatePrice);
    document.querySelector("input[name=rooms]").addEventListener("change", calculatePrice);
    document.querySelectorAll("input[name=\"activities[]\"]").forEach(cb => {
        cb.addEventListener("change", calculatePrice);
    });
    </script>';
}

// ============================================================================
// STEP 5: CONFIRM BOOKING
// ============================================================================

/**
 * Process booking confirmation
 */
function handleBookingSubmission($db) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        return ['success' => false, 'message' => 'Invalid request'];
    }
    
    // Validate required fields
    $required = ['destination_id', 'hotel_id', 'guest_name', 'guest_email', 'check_in', 'check_out', 
                 'number_of_guests', 'number_of_rooms', 'payment_method', 'total_price'];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            return ['success' => false, 'message' => 'Missing required field: ' . $field];
        }
    }
    
    // Get activities from checkbox array
    $activities = [];
    if (isset($_POST['activities']) && is_array($_POST['activities'])) {
        $activities = array_map('intval', $_POST['activities']);
    }
    
    // Prepare booking data
    $booking_data = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'guest_name' => trim($_POST['guest_name']),
        'guest_email' => filter_var($_POST['guest_email'], FILTER_VALIDATE_EMAIL),
        'destination_id' => (int)$_POST['destination_id'],
        'hotel_id' => (int)$_POST['hotel_id'],
        'check_in_date' => $_POST['check_in'],
        'check_out_date' => $_POST['check_out'],
        'number_of_guests' => (int)$_POST['number_of_guests'],
        'number_of_rooms' => (int)$_POST['number_of_rooms'],
        'subtotal_amount' => (int)$_POST['subtotal_amount'],
        'activities_total' => (int)$_POST['activities_total'],
        'tax_amount' => (int)$_POST['tax_amount'],
        'total_price' => (int)$_POST['total_price'],
        'payment_method' => $_POST['payment_method'],
        'special_requests' => trim($_POST['special_requests'] ?? ''),
        'activities' => $activities
    ];
    
    // Validate email
    if (!$booking_data['guest_email']) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    // Create booking
    $ref_code = createBooking($db, $booking_data);
    
    if (!$ref_code) {
        return ['success' => false, 'message' => 'Failed to create booking'];
    }
    
    // Save payment details
    $payment_data = [
        'payment_method' => $_POST['payment_method'],
        'gcash_number' => $_POST['gcash_number'] ?? null,
        'gcash_account_name' => $_POST['gcash_name'] ?? null,
        'card_holder_name' => $_POST['card_holder'] ?? null,
        'card_number' => $_POST['card_number'] ?? null,
        'card_brand' => $_POST['card_brand'] ?? null
    ];
    
    $booking = getBookingByReference($db, $ref_code);
    if ($booking) {
        savePaymentDetails($db, $booking['id'], $payment_data);
    }
    
    return [
        'success' => true,
        'message' => 'Booking successful!',
        'reference_code' => $ref_code
    ];
}

/**
 * Display booking confirmation
 */
function displayBookingConfirmation($db, $reference_code) {
    $booking = getBookingByReference($db, $reference_code);
    
    if (!$booking) {
        echo '<p>Booking not found</p>';
        return;
    }
    
    $destination = getDestinationById($db, $booking['destination_id']);
    $hotel = getHotelDetails($db, $booking['hotel_id']);
    $activities = getBookingActivities($db, $booking['id']);
    
    echo '<div class="confirmation-box">';
    echo '<h1>✓ Booking Confirmed!</h1>';
    echo '<p>Reference Code: <strong style="font-size: 1.5em;color: #27ae60;">' . escapeOutput($booking['reference_code']) . '</strong></p>';
    
    echo '<h2>Booking Details</h2>';
    echo '<p><strong>Guest:</strong> ' . escapeOutput($booking['guest_name']) . '</p>';
    echo '<p><strong>Email:</strong> ' . escapeOutput($booking['guest_email']) . '</p>';
    echo '<p><strong>Destination:</strong> ' . escapeOutput($destination['name']) . '</p>';
    echo '<p><strong>Hotel:</strong> ' . escapeOutput($hotel['name']) . '</p>';
    echo '<p><strong>Check-in:</strong> ' . date('M d, Y', strtotime($booking['check_in_date'])) . '</p>';
    echo '<p><strong>Check-out:</strong> ' . date('M d, Y', strtotime($booking['check_out_date'])) . '</p>';
    echo '<p><strong>Guests:</strong> ' . $booking['number_of_guests'] . '</p>';
    echo '<p><strong>Rooms:</strong> ' . $booking['number_of_rooms'] . '</p>';
    
    if (!empty($activities)) {
        echo '<h3>Activities</h3>';
        echo '<ul>';
        foreach ($activities as $activity) {
            echo '<li>' . escapeOutput($activity['activity_name']) . ' - ₱' . number_format($activity['activity_price']) . '</li>';
        }
        echo '</ul>';
    }
    
    echo '<h2>Price Summary</h2>';
    echo '<p>Subtotal: <strong>₱' . number_format($booking['subtotal_amount']) . '</strong></p>';
    echo '<p>Tax: <strong>₱' . number_format($booking['tax_amount']) . '</strong></p>';
    echo '<h3>Total: <strong style="color: #27ae60;">₱' . number_format($booking['total_price']) . '</strong></h3>';
    
    echo '<p style="margin-top: 2rem;">Status: <strong>' . ucfirst($booking['status']) . '</strong></p>';
    echo '<p>Confirmation email sent to: ' . escapeOutput($booking['guest_email']) . '</p>';
    
    echo '<a href="index.php" class="btn-home">Back to Home</a>';
    echo '</div>';
}

// ============================================================================
// STEP 6: VIEW BOOKING STATUS
// ============================================================================

/**
 * Display user's bookings
 */
function displayUserBookings($db, $user_id) {
    $result = $db->prepare("
        SELECT 
            id,
            reference_code,
            guest_name,
            destination_id,
            hotel_id,
            check_in_date,
            check_out_date,
            total_price,
            status,
            created_at
        FROM bookings
        WHERE user_id = ? OR guest_email = ?
        ORDER BY created_at DESC
    ");
    
    // Get user email
    $user = $db->query("SELECT email FROM users WHERE id = " . (int)$user_id)->fetch_assoc();
    $email = $user['email'] ?? '';
    
    $result->bind_param("is", $user_id, $email);
    $result->execute();
    $bookings = $result->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (empty($bookings)) {
        echo '<p>No bookings found</p>';
        return;
    }
    
    echo '<h1>My Bookings</h1>';
    echo '<div class="bookings-table">';
    echo '<table>';
    echo '<tr><th>Reference</th><th>Destination</th><th>Check-in</th><th>Total</th><th>Status</th><th>Action</th></tr>';
    
    foreach ($bookings as $booking) {
        $dest = getDestinationById($db, $booking['destination_id']);
        echo '<tr>';
        echo '<td>' . escapeOutput($booking['reference_code']) . '</td>';
        echo '<td>' . escapeOutput($dest['name'] ?? 'N/A') . '</td>';
        echo '<td>' . date('M d, Y', strtotime($booking['check_in_date'])) . '</td>';
        echo '<td>₱' . number_format($booking['total_price']) . '</td>';
        echo '<td><span class="status-' . $booking['status'] . '">' . ucfirst($booking['status']) . '</span></td>';
        echo '<td><a href="booking-details.php?ref=' . escapeOutput($booking['reference_code']) . '">View</a></td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</div>';
}

?>
