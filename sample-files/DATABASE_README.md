# LakbayLokal Database System

## 📋 Table of Contents
1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [Database Design](#database-design)
4. [Installation](#installation)
5. [How the System Works](#how-the-system-works)
6. [Usage Examples](#usage-examples)
7. [Important Concepts](#important-concepts)
8. [Troubleshooting](#troubleshooting)

---

## 🌍 Overview

**LakbayLokal** is a travel booking system for the Philippines. It allows users to:
- Browse travel destinations
- View available hotels and their details
- Select activities and tours
- Make bookings with different payment methods
- Track their bookings

The database is designed for **PHP beginners** using **MySQLi** (no frameworks, no ORM).

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────┐
│   User's Web Browser                │
│   (index.php, destinations.php...)  │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   PHP Application Files             │
│   - booking.php                     │
│   - database_helpers.php            │
│   - config_db.php                   │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   MySQL Database                    │
│   - users                           │
│   - destinations                    │
│   - hotels                          │
│   - bookings                        │
│   - activities                      │
│   - payments                        │
└─────────────────────────────────────┘
```

---

## 📊 Database Design

### 1. **USERS Table**
Stores user account information.

```
┌─────────────────────────────────────────┐
│             USERS                       │
├─────────────────────────────────────────┤
│ id (PK)           → Auto-increment ID   │
│ first_name        → John                │
│ last_name         → Dela Cruz           │
│ email (UNIQUE)    → john@email.com      │
│ password_hash     → Encrypted password  │
│ role              → 'user' or 'admin'   │
│ is_active         → 1 or 0              │
│ created_at        → When registered     │
└─────────────────────────────────────────┘
```

**Example:**
```php
// Create a user
createUser($db, "Juan", "Dela Cruz", "juan@email.com", "password123");

// Get user by email
$user = getUserByEmail($db, "juan@email.com");

// Verify password
if (verifyPassword("password123", $user['password_hash'])) {
    echo "Login successful!";
}
```

---

### 2. **DESTINATIONS Table**
Stores travel destination information.

```
┌──────────────────────────────────────┐
│       DESTINATIONS                   │
├──────────────────────────────────────┤
│ id (PK)         → 1                  │
│ name            → "Baguio City"      │
│ region          → "Luzon"            │
│ description     → Long text...       │
│ price           → 4500               │
│ image_url       → /assets/pic.jpg    │
│ is_active       → 1                  │
└──────────────────────────────────────┘
```

**Example:**
```php
// Get all destinations
$destinations = getAllDestinations($db);
foreach ($destinations as $dest) {
    echo $dest['name'];  // Baguio City, Vigan, etc.
}

// Get specific destination
$baguio = getDestinationById($db, 1);
```

---

### 3. **HOTELS Table**
Stores hotel information linked to destinations.

```
┌───────────────────────────────────────┐
│           HOTELS                      │
├───────────────────────────────────────┤
│ id (PK)          → 1                  │
│ destination_id   → 1 (links to Baguio)│
│ name             → "Sotogrande"       │
│ price_per_night  → 5200               │
│ stars            → 4                  │
│ rating           → 4.6                │
│ review_count     → 312                │
│ location         → "Session Road"     │
│ checkin_time     → "14:00"            │
│ checkout_time    → "11:00"            │
│ is_active        → 1                  │
└───────────────────────────────────────┘
```

**Important:** The `destination_id` field is a **FOREIGN KEY** - it references the DESTINATIONS table. This creates a relationship: hotels BELONG TO destinations.

**Example:**
```php
// Get hotels in Baguio (destination_id = 1)
$hotels = getHotelsByDestination($db, 1);

// Get complete hotel info (with amenities and policies)
$hotel = getHotelDetails($db, 1);

// hotel['amenities'] contains array of amenity items
// hotel['policies'] contains array of hotel policies
```

---

### 4. **HOTEL_AMENITIES Table**
Links amenities to hotels (one hotel can have many amenities).

```
┌──────────────────────────────────────┐
│    HOTEL_AMENITIES                   │
├──────────────────────────────────────┤
│ id (PK)    → 1                       │
│ hotel_id   → 1 (Foreign Key)         │
│ amenity    → "Free WiFi"             │
│                                      │
│ hotel_id   → 1                       │
│ amenity    → "Pool"                  │
│                                      │
│ hotel_id   → 1                       │
│ amenity    → "Restaurant"            │
└──────────────────────────────────────┘
```

This is a **one-to-many relationship**: One hotel has many amenities.

---

### 5. **HOTEL_POLICIES Table**
Stores hotel policies (cancellation, pet policy, etc.)

```
┌──────────────────────────────────────┐
│    HOTEL_POLICIES                    │
├──────────────────────────────────────┤
│ id (PK)    → 1                       │
│ hotel_id   → 1 (Foreign Key)         │
│ policy     → "No pets allowed"       │
│            → "Cancellation: 24hrs"   │
└──────────────────────────────────────┘
```

---

### 6. **ACTIVITIES Table**
Stores activities/tours available in destinations.

```
┌──────────────────────────────────────┐
│       ACTIVITIES                     │
├──────────────────────────────────────┤
│ id (PK)          → 1                 │
│ destination_id   → 1 (to Baguio)     │
│ name             → "Strawberry Picking"│
│ price            → 250               │
│ description      → Long text...      │
│ is_active        → 1                 │
└──────────────────────────────────────┘
```

**Example:**
```php
// Get activities in Baguio
$activities = getActivitiesByDestination($db, 1);
// Returns array of activities with ID, name, and price

// Get specific activity
$strawberry = getActivityById($db, 1);
// Returns: ['id' => 1, 'name' => 'Strawberry Picking', 'price' => 250]
```

---

### 7. **BOOKINGS Table**
The main bookings table - stores all booking records.

```
┌─────────────────────────────────────────────┐
│              BOOKINGS                       │
├─────────────────────────────────────────────┤
│ id (PK)              → Auto-increment       │
│ reference_code       → "BK12345ABC"         │
│ user_id              → 5 (Foreign Key)      │
│ guest_name           → "Juan Dela Cruz"     │
│ guest_email          → "juan@email.com"     │
│ destination_id       → 1 (Foreign Key)      │
│ hotel_id             → 1 (Foreign Key)      │
│ check_in_date        → "2024-06-15"         │
│ check_out_date       → "2024-06-18"         │
│ number_of_guests     → 2                    │
│ number_of_rooms      → 1                    │
│ subtotal_amount      → 15600 (hotel cost)   │
│ activities_total     → 500 (activities)     │
│ tax_amount           → 1932 (12%)           │
│ total_price          → 18032                │
│ payment_method       → "gcash"              │
│ special_requests     → "High floor please"  │
│ status               → "pending"            │
│ created_at           → "2024-06-01 10:30"   │
└─────────────────────────────────────────────┘
```

**How it works:**
1. `reference_code` - Unique booking ID (e.g., BK12345ABC)
2. `user_id` - Links to the USERS table (who made the booking)
3. `destination_id` & `hotel_id` - Links to which place and hotel
4. `check_in_date` & `check_out_date` - Reservation dates
5. Price breakdown - Shows hotel cost, activities, taxes, total
6. `status` - Can be: pending → confirmed → completed or cancelled

**Example:**
```php
// Create a booking
$booking_data = [
    'user_id' => 5,
    'guest_name' => 'Juan Dela Cruz',
    'guest_email' => 'juan@email.com',
    'destination_id' => 1,
    'hotel_id' => 1,
    'check_in_date' => '2024-06-15',
    'check_out_date' => '2024-06-18',
    'number_of_guests' => 2,
    'number_of_rooms' => 1,
    'subtotal_amount' => 15600,
    'activities_total' => 500,
    'tax_amount' => 1932,
    'total_price' => 18032,
    'payment_method' => 'gcash',
    'special_requests' => 'High floor please',
    'activities' => [1, 2, 3]  // Activity IDs
];

$ref_code = createBooking($db, $booking_data);
// Returns: "BK12345ABC"

// Get booking details
$booking = getBookingByReference($db, "BK12345ABC");
```

---

### 8. **BOOKING_ACTIVITIES Table**
Links activities to bookings (which activities did the guest choose?).

This is a **junction table** - it connects BOOKINGS and ACTIVITIES in a many-to-many relationship.

```
┌──────────────────────────────────────┐
│   BOOKING_ACTIVITIES                 │
├──────────────────────────────────────┤
│ id (PK)       → 1                    │
│ booking_id    → 1 (Foreign Key)      │
│ activity_id   → 1 (Foreign Key)      │
│ activity_name → "Strawberry Picking" │
│ activity_price → 250                 │
│                                      │
│ booking_id    → 1                    │
│ activity_id   → 2                    │
│ activity_name → "BenCab Museum"      │
│ activity_price → 200                 │
└──────────────────────────────────────┘
```

**Why?** Because one booking can have multiple activities.

```php
// Add activities to booking
$activities = [1, 2, 3];  // Activity IDs
addActivitiesToBooking($db, 1, $activities);

// Get activities in a booking
$booking_activities = getBookingActivities($db, 1);
// Returns array of activities with prices
```

---

### 9. **PAYMENT_DETAILS Table**
Stores payment information securely.

```
┌────────────────────────────────────────┐
│     PAYMENT_DETAILS                    │
├────────────────────────────────────────┤
│ id (PK)               → 1              │
│ booking_id (UNIQUE)   → 1 (FK)         │
│ payment_method        → "gcash"        │
│ gcash_number          → "09XXXXXXXXX"  │
│ gcash_account_name    → "Juan D Cruz"  │
│ card_holder_name      → "JUAN D CRUZ"  │
│ card_last_four        → "1234"         │
│ card_brand            → "Visa"         │
│ payment_status        → "pending"      │
└────────────────────────────────────────┘
```

---

## 💻 Installation

### Step 1: Create the Database

Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line):

```bash
mysql -u root -p
```

Then run the SQL:

```sql
-- Create database
CREATE DATABASE lakbaylokal;
USE lakbaylokal;

-- Run the schema (copy from lakbaylokal_schema.sql)
-- ... paste all SQL commands ...
```

Or if you have the SQL file:
```bash
mysql -u root -p lakbaylokal < lakbaylokal_schema.sql
```

### Step 2: Configure Database Connection

Edit `config_db.php`:

```php
define('DB_HOST',     'localhost');      // Usually localhost
define('DB_USER',     'root');           // Your MySQL username
define('DB_PASSWORD', 'your_password');  // Your MySQL password
define('DB_NAME',     'lakbaylokal');    // Database name
```

### Step 3: Place Files in Your Project

```
your-project/
├── config/
│   └── db.php              (from config_db.php)
├── includes/
│   └── database_helpers.php (from database_helpers.php)
├── index.php
├── destinations.php
├── hotel.php
└── booking_confirm.php
```

### Step 4: Use in Your PHP Files

```php
<?php
// At the top of your PHP files:
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// Now you can use the helper functions!
$destinations = getAllDestinations($db);
$hotels = getHotelsByDestination($db, 1);
// etc.
?>
```

---

## 🔄 How the System Works

### 1. User Browses Destinations
```php
// destinations.php
$destinations = getAllDestinations($db);
foreach ($destinations as $dest) {
    echo $dest['name'];  // Display each destination
}
```

### 2. User Views Hotels in Destination
```php
// hotel.php?dest=1
$dest_id = $_GET['dest'];
$hotels = getHotelsByDestination($db, $dest_id);
foreach ($hotels as $hotel) {
    echo $hotel['name'];  // Display each hotel
}
```

### 3. User Views Hotel Details
```php
// hotel_detail.php?id=1
$hotel = getHotelDetails($db, 1);
echo $hotel['name'];
echo implode(", ", array_column($hotel['amenities'], 'amenity'));  // Show amenities
```

### 4. User Selects Activities (JavaScript in frontend)
```javascript
// JavaScript collects selected activity IDs
var selectedActivities = [1, 2, 3];  // Activity IDs
```

### 5. User Submits Booking Form
```php
// booking_confirm.php (POST request)
$booking_data = [
    'user_id' => $_SESSION['user_id'],
    'guest_name' => $_POST['guest_name'],
    'guest_email' => $_POST['guest_email'],
    'destination_id' => $_POST['destination_id'],
    'hotel_id' => $_POST['hotel_id'],
    'check_in_date' => $_POST['checkin'],
    'check_out_date' => $_POST['checkout'],
    'number_of_guests' => $_POST['guests'],
    'number_of_rooms' => $_POST['rooms'],
    'subtotal_amount' => $subtotal,
    'activities_total' => $activities_total,
    'tax_amount' => $tax,
    'total_price' => $total,
    'payment_method' => $_POST['payment_method'],
    'special_requests' => $_POST['requests'],
    'activities' => json_decode($_POST['selected_acts'], true)
];

$ref_code = createBooking($db, $booking_data);
// Booking is saved! Return reference code to user
```

### 6. User Makes Payment
```php
// Save payment details
$payment_data = [
    'payment_method' => $_POST['payment_method'],
    'gcash_number' => $_POST['gcash_number'] ?? null,
    'gcash_account_name' => $_POST['gcash_name'] ?? null,
    'card_holder_name' => $_POST['card_holder'] ?? null,
    'card_number' => $_POST['card_number'] ?? null,
    'card_brand' => 'Visa'
];

savePaymentDetails($db, $booking_id, $payment_data);
```

### 7. Admin Views All Bookings
```php
// admin/bookings.php
$bookings = getAllBookings($db);  // Get all
// Or filter by status:
$pending = getAllBookings($db, 'pending');
$confirmed = getAllBookings($db, 'confirmed');
```

---

## 📝 Usage Examples

### Example 1: Create a New User
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if email already exists
    if (emailExists($db, $email)) {
        echo "Email already registered!";
    } else {
        // Create new user
        $user_id = createUser($db, $first_name, $last_name, $email, $password);
        if ($user_id) {
            echo "Registration successful! User ID: " . $user_id;
        } else {
            echo "Registration failed!";
        }
    }
}
?>
```

### Example 2: User Login
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Get user by email
    $user = getUserByEmail($db, $email);
    
    if ($user && verifyPassword($password, $user['password_hash'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        header('Location: index.php');
    } else {
        echo "Invalid email or password!";
    }
}
?>
```

### Example 3: Display Destinations
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

$destinations = getAllDestinations($db);
?>

<div class="destinations-list">
    <?php foreach ($destinations as $dest): ?>
        <div class="destination-card">
            <h3><?= escapeOutput($dest['name']) ?></h3>
            <p><?= escapeOutput($dest['description']) ?></p>
            <p>From ₱<?= number_format($dest['price']) ?></p>
            <a href="destination.php?id=<?= $dest['id'] ?>">View Hotels</a>
        </div>
    <?php endforeach; ?>
</div>
```

### Example 4: Display Hotels with Amenities
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

$dest_id = $_GET['id'];
$hotels = getHotelsByDestination($db, $dest_id);
?>

<?php foreach ($hotels as $hotel): ?>
    <div class="hotel-card">
        <h3><?= escapeOutput($hotel['name']) ?></h3>
        <p>⭐ <?= $hotel['rating'] ?> (<?= $hotel['review_count'] ?> reviews)</p>
        <p>₱<?= number_format($hotel['price_per_night']) ?> per night</p>
        
        <?php
        // Get full details including amenities
        $details = getHotelDetails($db, $hotel['id']);
        ?>
        
        <h4>Amenities:</h4>
        <ul>
            <?php foreach ($details['amenities'] as $amenity): ?>
                <li><?= escapeOutput($amenity['amenity']) ?></li>
            <?php endforeach; ?>
        </ul>
        
        <a href="book.php?hotel=<?= $hotel['id'] ?>">Book Now</a>
    </div>
<?php endforeach; ?>
```

### Example 5: Create a Booking
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $destination_id = (int)$_POST['destination_id'];
    $hotel_id = (int)$_POST['hotel_id'];
    $check_in = $_POST['check_in_date'];
    $check_out = $_POST['check_out_date'];
    $guests = (int)$_POST['guests'];
    $rooms = (int)$_POST['rooms'];
    
    // Get hotel info
    $hotel = getHotelDetails($db, $hotel_id);
    
    // Calculate costs
    $nights = calculateNights($check_in, $check_out);
    $amounts = calculateBookingTotal(
        $hotel['price_per_night'],
        $nights,
        $rooms,
        $_POST['activities_total'] ?? 0
    );
    
    // Parse selected activities
    $activities = json_decode($_POST['selected_acts'], true) ?? [];
    
    // Create booking
    $booking_data = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'guest_name' => $_POST['guest_name'],
        'guest_email' => $_POST['guest_email'],
        'destination_id' => $destination_id,
        'hotel_id' => $hotel_id,
        'check_in_date' => $check_in,
        'check_out_date' => $check_out,
        'number_of_guests' => $guests,
        'number_of_rooms' => $rooms,
        'subtotal_amount' => $amounts['subtotal'],
        'activities_total' => $_POST['activities_total'] ?? 0,
        'tax_amount' => $amounts['tax'],
        'total_price' => $amounts['total'],
        'payment_method' => $_POST['payment_method'],
        'special_requests' => $_POST['requests'] ?? '',
        'activities' => $activities
    ];
    
    $ref_code = createBooking($db, $booking_data);
    
    if ($ref_code) {
        // Success!
        echo "Booking created! Reference: " . escapeOutput($ref_code);
        
        // Save payment details
        $payment_data = [
            'payment_method' => $_POST['payment_method'],
            'gcash_number' => $_POST['gcash_number'] ?? null,
            'gcash_account_name' => $_POST['gcash_name'] ?? null,
        ];
        
        $booking = getBookingByReference($db, $ref_code);
        savePaymentDetails($db, $booking['id'], $payment_data);
    } else {
        echo "Booking failed!";
    }
}
?>
```

---

## 🎓 Important Concepts

### 1. **Prepared Statements (SQL Injection Prevention)**

❌ **NEVER do this:**
```php
$email = $_GET['email'];
$result = $db->query("SELECT * FROM users WHERE email = '" . $email . "'");
// Attacker can input: ' OR '1'='1
```

✅ **ALWAYS do this:**
```php
$email = $_GET['email'];
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);  // "s" = string
$stmt->execute();
$result = $stmt->get_result();
```

### 2. **Foreign Keys (Relationships)**

Tables are linked together:
```
destination (id=1) ←──┐
                       ├── hotel (destination_id=1)
                       │
activity (id=1) ←─────┤
                       │
booking (hotel_id=1, destination_id=1)
                       │
booking_activity (booking_id=1, activity_id=1)
```

### 3. **Password Security**

```php
// Hash when creating
$hash = password_hash($password, PASSWORD_BCRYPT);

// Verify when logging in
if (password_verify($password, $hash)) {
    // Password is correct
}

// NEVER store plain passwords!
```

### 4. **Data Validation**

Always validate user input:
```php
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$rooms = (int)$_POST['rooms'];  // Convert to integer
$check_in = DateTime::createFromFormat('Y-m-d', $_POST['check_in']);

if (!$email || !$rooms || !$check_in) {
    echo "Invalid input!";
}
```

### 5. **Escaping Output**

Always escape when displaying user data:
```php
// ❌ WRONG
echo $user_input;

// ✅ RIGHT
echo escapeOutput($user_input);
// Or use htmlspecialchars()
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

---

## 🐛 Troubleshooting

### Problem: "Database Connection Failed"

**Solution:**
1. Check MySQL is running
2. Verify credentials in `config_db.php`
3. Ensure database `lakbaylokal` exists
4. Check MySQL username/password

```bash
# Test MySQL connection
mysql -h localhost -u root -p
```

### Problem: "Function not found"

**Solution:**
Make sure you included the helpers file:
```php
require_once 'includes/database_helpers.php';
```

### Problem: "Email already exists" but it doesn't

**Solution:**
The email field has UNIQUE constraint. Check existing data:
```sql
SELECT * FROM users WHERE email = 'test@email.com';
```

### Problem: Booking not saving

**Solution:**
Check if all required fields are present:
```php
// Debug - print what you're sending
var_dump($booking_data);
die();

// Then fix missing fields
```

### Problem: Activities not showing in booking

**Solution:**
Activities are saved separately in `booking_activities` table:
```php
// Get activities for a booking
$activities = getBookingActivities($db, $booking_id);

// These should be activity records with prices
foreach ($activities as $act) {
    echo $act['activity_name'];
}
```

---

## 📚 Database Query Examples for Common Tasks

### Get Dashboard Statistics (Admin)

```php
// Total bookings
$result = $db->query("SELECT COUNT(*) as total FROM bookings");
$stats = $result->fetch_assoc();
echo $stats['total'];  // Number of bookings

// Revenue this month
$result = $db->query("
    SELECT SUM(total_price) as revenue 
    FROM bookings 
    WHERE MONTH(created_at) = MONTH(NOW())
");
$data = $result->fetch_assoc();
echo $data['revenue'];  // Total revenue

// Bookings by status
$result = $db->query("
    SELECT status, COUNT(*) as count
    FROM bookings
    GROUP BY status
");
while ($row = $result->fetch_assoc()) {
    echo $row['status'] . ": " . $row['count'];
}
```

### Find Popular Hotels

```php
$result = $db->query("
    SELECT h.id, h.name, COUNT(b.id) as bookings
    FROM hotels h
    LEFT JOIN bookings b ON h.id = b.hotel_id
    GROUP BY h.id
    ORDER BY bookings DESC
    LIMIT 5
");

while ($hotel = $result->fetch_assoc()) {
    echo $hotel['name'] . ": " . $hotel['bookings'] . " bookings";
}
```

---

## 🚀 Next Steps

1. **Install the database** using the SQL schema
2. **Copy the PHP files** to your project
3. **Update credentials** in `config_db.php`
4. **Create login/registration pages** using the helper functions
5. **Build the booking flow** following the examples
6. **Add admin dashboard** to view bookings and statistics
7. **Implement email notifications** for booking confirmations

---

## 📖 Resources

- [PHP MySQLi Documentation](https://www.php.net/manual/en/book.mysqli.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Password Hashing Best Practices](https://www.php.net/manual/en/function.password-hash.php)
- [SQL Injection Prevention](https://owasp.org/www-community/attacks/SQL_Injection)

---

**Created for beginners learning PHP + MySQL database design**

Good luck with your LakbayLokal project! 🏖️
