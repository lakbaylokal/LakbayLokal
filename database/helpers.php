<?php
/**
 * database/helpers.php — LakbayLokal DB Helper Functions
 * All functions use the real SQL schema columns.
 * Replaces data.php array lookups entirely.
 */

// ============================================================================
// MEDIA
// ============================================================================

function resolvePublicImageUrl(?string $imageUrl): string {
    $imageUrl = trim((string)($imageUrl ?? ''));
    if ($imageUrl === '') return '';

    $imageUrl = str_replace('\\', '/', $imageUrl);
    if (preg_match('/^(https?:)?\/\//i', $imageUrl) || str_starts_with($imageUrl, 'data:')) {
        return $imageUrl;
    }

    $rootDir = dirname(__DIR__);
    if (str_starts_with($imageUrl, '/')) {
        if (is_file($rootDir . $imageUrl)) {
            return $imageUrl;
        }
        return '';
    }

    if (is_file($rootDir . '/' . $imageUrl)) {
        return $imageUrl;
    }

    if (is_file($rootDir . '/admin/' . $imageUrl)) {
        return 'admin/' . $imageUrl;
    }

    return '';
}

// ============================================================================
// DESTINATIONS
// ============================================================================

/**
 * Get all destinations (with hotel count and min price injected).
 * Returns array shaped like the old $destinations array so views work unchanged.
 */
function getAllDestinations($conn) {
    $sql = "
        SELECT
            d.id, d.name, d.region, d.emoji, d.tagline,
            d.description AS `desc`,
            d.price, d.price_from, d.image_url AS image,
            d.gradient_bg AS gradient,
            COUNT(DISTINCT h.id)   AS hotel_count,
            COUNT(DISTINCT a.id)   AS acts_count
        FROM destinations d
        LEFT JOIN hotels h
            ON h.destination_id = d.id AND h.archived = 0
        LEFT JOIN activities a
            ON a.destination_id = d.id AND a.archived = 0
        WHERE d.archived = 0
        GROUP BY d.id
        ORDER BY d.name
    ";
    $result = $conn->query($sql);
    if (!$result) return [];

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        // Keep a flat array; views access count via hotel_count / acts_count
        $row['image'] = resolvePublicImageUrl($row['image'] ?? '');
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Get a single destination by string ID (e.g. 'baguio').
 * Includes nested hotels and acts arrays to match the old data.php shape.
 */
function getDestById($conn, string $id): ?array {
    $stmt = $conn->prepare("
        SELECT id, name, region, emoji, tagline,
               description AS `desc`,
               price, price_from, image_url AS image,
               gradient_bg AS gradient
        FROM destinations
        WHERE id = ? AND archived = 0
    ");
    if (!$stmt) return null;
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $dest = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$dest) return null;
    $dest['image'] = resolvePublicImageUrl($dest['image'] ?? '');

    $dest['hotels'] = getHotelsByDest($conn, $id);
    $dest['acts']   = getActivitiesByDest($conn, $id);
    return $dest;
}

// ============================================================================
// HOTELS
// ============================================================================

/**
 * Get all hotels for a destination, with amenities and policies nested.
 */
function getHotelsByDest($conn, string $destId): array {
    $stmt = $conn->prepare("
        SELECT id, destination_id, name, image_url AS image,
               location, description AS `desc`,
               stars, price, rating, reviews_count AS reviews,
               checkin_time  AS checkin,
               checkout_time AS checkout
        FROM hotels
        WHERE destination_id = ? AND archived = 0
        ORDER BY price ASC
    ");
    if (!$stmt) return [];
    $stmt->bind_param('s', $destId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hotels = [];
    while ($h = $result->fetch_assoc()) {
        $h['image'] = resolvePublicImageUrl($h['image'] ?? '');
        $h['amenities'] = getHotelAmenities($conn, $h['id']);
        $h['policies']  = getHotelPolicies($conn, $h['id']);
        $hotels[] = $h;
    }
    $stmt->close();
    return $hotels;
}

/**
 * Get a single hotel by destination ID + hotel ID.
 * Shape matches what hotel.php / hotel.view.php expect.
 */
function getHotelById($conn, string $destId, string $hotelId): ?array {
    $stmt = $conn->prepare("
        SELECT id, destination_id, name, image_url AS image,
               location, description AS `desc`,
               stars, price, rating, reviews_count AS reviews,
               checkin_time  AS checkin,
               checkout_time AS checkout
        FROM hotels
        WHERE destination_id = ? AND id = ? AND archived = 0
    ");
    if (!$stmt) return null;
    $stmt->bind_param('ss', $destId, $hotelId);
    $stmt->execute();
    $hotel = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$hotel) return null;
    $hotel['image'] = resolvePublicImageUrl($hotel['image'] ?? '');

    $hotel['amenities'] = getHotelAmenities($conn, $hotel['id']);
    $hotel['policies']  = getHotelPolicies($conn, $hotel['id']);
    return $hotel;
}

// ============================================================================
// AMENITIES & POLICIES
// ============================================================================

function getHotelAmenities($conn, string $hotelId): array {
    $stmt = $conn->prepare(
        "SELECT amenity_name FROM hotel_amenities WHERE hotel_id = ? ORDER BY id"
    );
    if (!$stmt) return [];
    $stmt->bind_param('s', $hotelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row['amenity_name'];
    }
    $stmt->close();
    return $list;
}

function getHotelPolicies($conn, string $hotelId): array {
    $stmt = $conn->prepare(
        "SELECT policy FROM hotel_policies WHERE hotel_id = ? ORDER BY id"
    );
    if (!$stmt) return [];
    $stmt->bind_param('s', $hotelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row['policy'];
    }
    $stmt->close();
    return $list;
}

// ============================================================================
// ACTIVITIES
// ============================================================================

function getActivitiesByDest($conn, string $destId): array {
    $stmt = $conn->prepare(
        "SELECT id, name, price FROM activities WHERE destination_id = ? AND archived = 0 ORDER BY id"
    );
    if (!$stmt) return [];
    $stmt->bind_param('s', $destId);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
    $stmt->close();
    return $list;
}

function getActivityById($conn, int $activityId): ?array {
    $stmt = $conn->prepare(
        "SELECT id, destination_id, name, price FROM activities WHERE id = ? AND archived = 0"
    );
    if (!$stmt) return null;
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

// ============================================================================
// BOOKINGS
// ============================================================================

/**
 * Generate a unique LBL reference code.
 */
function generateBookingReference($conn): string {
    do {
        $code = 'LBL' . date('Ymd') . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT id FROM bookings WHERE reference_code = ?");
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $code;
}

/**
 * Insert a booking row. Returns ['success'=>bool, 'booking_id'=>int, 'error'=>string].
 */
function createBooking($conn, array $d): array {
    $stmt = $conn->prepare("
        INSERT INTO bookings
            (reference_code, user_id, guest_name, guest_email,
             destination_id, hotel_id,
             checkin_date, checkout_date,
             number_of_guests, number_of_rooms,
             subtotal, activities_total, tax_amount, total_price,
             payment_method, special_requests, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')
    ");
    if (!$stmt) return ['success' => false, 'error' => $conn->error];

    $stmt->bind_param(
        'sissssssiiiiisss',
        $d['reference_code'],
        $d['user_id'],
        $d['guest_name'],
        $d['guest_email'],
        $d['destination_id'],
        $d['hotel_id'],
        $d['checkin_date'],
        $d['checkout_date'],
        $d['number_of_guests'],
        $d['number_of_rooms'],
        $d['subtotal'],
        $d['activities_total'],
        $d['tax_amount'],
        $d['total_price'],
        $d['payment_method'],
        $d['special_requests']
    );

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        return ['success' => false, 'error' => $err];
    }
    $id = $conn->insert_id;
    $stmt->close();
    return ['success' => true, 'booking_id' => $id];
}

/**
 * Insert a single activity line into booking_activities.
 */
function addBookingActivity($conn, int $bookingId, int $activityId, string $name, int $price): bool {
    $stmt = $conn->prepare("
        INSERT INTO booking_activities (booking_id, activity_id, activity_name, activity_price)
        VALUES (?,?,?,?)
    ");
    if (!$stmt) return false;
    $stmt->bind_param('iisi', $bookingId, $activityId, $name, $price);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Fetch a booking by reference code (joined with destination & hotel names).
 */
function getBookingByReference($conn, string $ref): ?array {
    $stmt = $conn->prepare("
        SELECT b.*,
               d.name AS dest_name,
               h.name AS hotel_name
        FROM bookings b
        LEFT JOIN destinations d ON d.id = b.destination_id
        LEFT JOIN hotels       h ON h.id = b.hotel_id
        WHERE b.reference_code = ?
    ");
    if (!$stmt) return null;
    $stmt->bind_param('s', $ref);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$booking) return null;

    // Attach activity lines
    $stmt2 = $conn->prepare(
        "SELECT * FROM booking_activities WHERE booking_id = ?"
    );
    $stmt2->bind_param('i', $booking['id']);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $acts = [];
    while ($row = $res->fetch_assoc()) $acts[] = $row;
    $stmt2->close();
    $booking['activities'] = $acts;

    return $booking;
}

// ============================================================================
// USERS
// ============================================================================

function getUserByEmail($conn, string $email): ?array {
    $stmt = $conn->prepare(
        "SELECT id, FName, LName, Mname, Email, Password, role FROM users WHERE Email = ?"
    );
    if (!$stmt) return null;
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

// ============================================================================
// ADMIN / STATS
// ============================================================================

function getBookingStats($conn): array {
    $result = $conn->query("
        SELECT
            COUNT(*) AS total_bookings,
            COALESCE(SUM(total_price), 0) AS total_revenue,
            SUM(status = 'confirmed') AS confirmed,
            SUM(status = 'pending')   AS pending,
            SUM(status = 'cancelled') AS cancelled
        FROM bookings
    ");
    return $result ? $result->fetch_assoc() : [];
}