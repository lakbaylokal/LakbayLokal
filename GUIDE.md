# 🔧 Step-by-Step Database Integration Guide

## How to Connect Your Existing Files to the Database

This guide shows you **exactly what to change** in your files to use the database instead of mock data.

---

## 📝 Quick Summary of Changes

**Before (Mock Data):**
```php
$destinations = [
  ['id' => 'baguio', 'name' => 'Baguio City', ...],
  ...
];
```

**After (Database):**
```php
$destinations = getAllDestinations($db);
```

That's it! Everything else works the same.

---

## 🚀 Step 1: Update `config/db.php`

### Create the file structure first:
```
your-project/
├── config/
│   └── db.php              ← CREATE THIS FILE
├── includes/
│   ├── database_helpers.php ← COPY THIS FILE
│   ├── header.php          ← You already have this
│   └── footer.php          ← You already have this
├── index.php               ← MODIFY
├── destinations.php        ← MODIFY
├── hotel.php               ← MODIFY
└── booking_confirm.php     ← MODIFY
```

### `config/db.php` - COPY AS IS
Just copy the entire `config_db.php` file and rename it to `db.php`.

Update only the credentials:
```php
<?php
define('DB_HOST',     'localhost');
define('DB_USER',     'root');           // YOUR MYSQL USERNAME
define('DB_PASSWORD', '');               // YOUR MYSQL PASSWORD
define('DB_NAME',     'lakbaylokal');
// ... rest of file stays same
?>
```

---

## 🚀 Step 2: Add Database Helpers

### Copy `includes/database_helpers.php` as is
This file contains all the functions you need. No changes required!

---

## 🚀 Step 3: Update `index.php` (Homepage)

### BEFORE (Current Code)
```php
<?php
require_once 'data.php';  // ← REMOVE THIS

$pageTitle = 'LakbayLokal';
$activePage = 'home';
$rootPath = '';
include 'includes/header.php';
?>

<!-- Your HTML code here -->

<?php include 'includes/footer.php'; ?>
```

### AFTER (With Database)
```php
<?php
// ADD THESE TWO LINES at the top
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
// REMOVE: require_once 'data.php';

$pageTitle = 'LakbayLokal';
$activePage = 'home';
$rootPath = '';
include 'includes/header.php';
?>

<!-- Your HTML code here (stays the same) -->

<?php include 'includes/footer.php'; ?>
```

**That's the only change needed!**

---

## 🚀 Step 4: Update `destinations.php`

This file displays all destinations. Here's how to modify it:

### BEFORE (Current Code)
```php
<?php
require_once 'data.php';

// ... page setup code ...
include 'includes/header.php';

// Display destinations
foreach ($destinations as $d): ?>
    <div class="destination-card">
        <h3><?= htmlspecialchars($d['name']) ?></h3>
        <!-- ... -->
    </div>
<?php endforeach;
```

### AFTER (With Database)
```php
<?php
// ADD THESE TWO LINES
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
// REMOVE: require_once 'data.php';

// ... page setup code ...
include 'includes/header.php';

// GET DATA FROM DATABASE INSTEAD OF MOCK DATA
$destinations = getAllDestinations($db);  // ← CHANGE THIS LINE ONLY

// Display destinations (code stays the same!)
foreach ($destinations as $d): ?>
    <div class="destination-card">
        <h3><?= htmlspecialchars($d['name']) ?></h3>
        <!-- ... -->
    </div>
<?php endforeach;
```

**What changed:**
- Line 1-2: Add database includes
- Line X: Replace mock data with `getAllDestinations($db)`
- Everything else: **Stays exactly the same**

---

## 🚀 Step 5: Update `hotel.php`

This is where it gets interesting. This file currently gets data from `data.php`. Change it to use the database.

### BEFORE (Current Code)
```php
<?php
require_once 'data.php';

$destId  = $_GET['dest'] ?? '';
$hotelId = $_GET['id']   ?? '';

// Getting from mock data
$dest    = getDestById($destId);
$hotel   = getHotelById($destId, $hotelId);

if (!$dest || !$hotel) {
  header('Location: destinations.php');
  exit;
}

// ... rest of file
```

### AFTER (With Database)

**The beauty:** Your `getDestById()` and `getHotelById()` functions ARE ALREADY in `database_helpers.php`! 

But they work with database IDs (numbers), not string IDs like 'baguio'.

So make this change:

```php
<?php
// ADD THESE TWO LINES
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
// REMOVE: require_once 'data.php';

// CHANGE THIS:
$destId  = $_GET['dest'] ?? '';  // Gets string like 'baguio'
$hotelId = $_GET['id']   ?? '';

// UPDATE TO THIS:
$destId  = (int)$_GET['dest'] ?? 0;  // Convert to number (database ID)
$hotelId = (int)$_GET['id']   ?? 0;

// Now use database functions (these work the same as before!)
$dest    = getDestinationById($db, $destId);
$hotel   = getHotelDetails($db, $hotelId);

if (!$dest || !$hotel) {
  header('Location: destinations.php');
  exit;
}

// ... rest of file stays EXACTLY THE SAME
```

**What changed:**
- Add database includes
- Convert ID from string to integer: `(int)$_GET['dest']`
- Use database function names: `getDestinationById()` instead of `getDestById()`
- **Everything else: stays the same**

### Getting Activities in hotel.php

**BEFORE:**
```php
<?php
// In the JavaScript section, you loop through activities
<?php foreach ($dest['acts'] as $i => $act): ?>
```

**AFTER:**
```php
<?php
// Get activities from database instead
$activities = getActivitiesByDestination($db, $destId);

// Then in JavaScript, loop through them
<?php foreach ($activities as $i => $activity): ?>
    <div class="activity-item" id="act-<?= $i ?>"
         data-name="<?= htmlspecialchars($activity['name']) ?>"
         data-price="<?= $activity['price'] ?>">
```

---

## 🚀 Step 6: Update `booking_confirm.php`

This file handles the booking form submission. Major changes here.

### BEFORE (Current Code)
```php
<?php
require_once 'data.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Process form data
  $booking_ref = 'BK' . strtoupper(substr(uniqid(), -8));
  // Save to... nowhere (mock system)
  
  header('Location: booking_success.php?ref=' . $booking_ref);
}
?>
```

### AFTER (With Database)

```php
<?php
// ADD THESE LINES
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
// REMOVE: require_once 'data.php';

session_start();  // ADD THIS (needed for user tracking)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data
  $guest_name = trim($_POST['guest_name']);
  $guest_email = filter_var($_POST['guest_email'], FILTER_VALIDATE_EMAIL);
  $destination_id = (int)$_POST['dest_id'];
  $hotel_id = (int)$_POST['hotel_id'];
  $check_in = $_POST['checkin'];
  $check_out = $_POST['checkout'];
  $guests = (int)$_POST['guests'];
  $rooms = (int)$_POST['rooms'];
  
  // Validate email
  if (!$guest_email) {
    die('Invalid email');
  }
  
  // Parse selected activities (from JSON)
  $selected_acts = json_decode($_POST['selected_acts'] ?? '[]', true);
  $activity_ids = [];
  $activities_total = 0;
  
  foreach ($selected_acts as $act) {
    $activity_ids[] = (int)$act['id'];
    $activities_total += (int)$act['price'];
  }
  
  // Calculate prices
  $nights = calculateNights($check_in, $check_out);
  $price_per_night = (int)$_POST['price_per_night'];
  $room_count = (int)$_POST['rooms'];
  
  $amounts = calculateBookingTotal($price_per_night, $nights, $room_count, $activities_total);
  
  // Create booking in database
  $booking_data = [
    'user_id' => $_SESSION['user_id'] ?? null,
    'guest_name' => $guest_name,
    'guest_email' => $guest_email,
    'destination_id' => $destination_id,
    'hotel_id' => $hotel_id,
    'check_in_date' => $check_in,
    'check_out_date' => $check_out,
    'number_of_guests' => $guests,
    'number_of_rooms' => $rooms,
    'subtotal_amount' => $amounts['subtotal'],
    'activities_total' => $activities_total,
    'tax_amount' => $amounts['tax'],
    'total_price' => $amounts['total'],
    'payment_method' => $_POST['payment_method'],
    'special_requests' => $_POST['requests'] ?? '',
    'activities' => $activity_ids
  ];
  
  // Save to database
  $ref_code = createBooking($db, $booking_data);
  
  if ($ref_code) {
    // Success! Redirect to confirmation
    header('Location: booking_success.php?ref=' . urlencode($ref_code));
    exit;
  } else {
    die('Booking failed. Please try again.');
  }
}
?>
```

---

## ✅ Complete File Template

Here's a complete minimal template you can copy:

### `index.php` (Simplified)
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
session_start();

$pageTitle = 'LakbayLokal - Travel Booking';
$activePage = 'home';
$rootPath = '';
include 'includes/header.php';

// Get destinations from database
$destinations = getAllDestinations($db);
?>

<div class="destinations-container">
  <?php foreach ($destinations as $dest): ?>
    <div class="destination-card">
      <h3><?= htmlspecialchars($dest['name']) ?></h3>
      <p><?= htmlspecialchars(substr($dest['description'], 0, 100)) ?>...</p>
      <p>₱<?= number_format($dest['price']) ?></p>
      <a href="destinations.php?dest=<?= $dest['id'] ?>">View Hotels →</a>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
```

### `destinations.php` (Show Hotels)
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

$destId = (int)($_GET['dest'] ?? 0);
$destination = getDestinationById($db, $destId);

if (!$destination) {
  header('Location: index.php');
  exit;
}

$pageTitle = $destination['name'] . ' - Hotels';
$activePage = 'destinations';
include 'includes/header.php';

$hotels = getHotelsByDestination($db, $destId);
?>

<h1><?= htmlspecialchars($destination['name']) ?></h1>

<div class="hotels-container">
  <?php foreach ($hotels as $hotel): ?>
    <div class="hotel-card">
      <h3><?= htmlspecialchars($hotel['name']) ?></h3>
      <p>⭐ <?= $hotel['rating'] ?> (<?= $hotel['review_count'] ?> reviews)</p>
      <p>₱<?= number_format($hotel['price_per_night']) ?>/night</p>
      <a href="hotel.php?dest=<?= $destId ?>&id=<?= $hotel['id'] ?>">View Details →</a>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
```

### `hotel.php` (Hotel Details & Book)
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
session_start();

$destId = (int)($_GET['dest'] ?? 0);
$hotelId = (int)($_GET['id'] ?? 0);

$destination = getDestinationById($db, $destId);
$hotel = getHotelDetails($db, $hotelId);

if (!$destination || !$hotel) {
  header('Location: index.php');
  exit;
}

$pageTitle = $hotel['name'];
$activePage = 'hotels';
include 'includes/header.php';

$activities = getActivitiesByDestination($db, $destId);
$amenityIcons = [
  'Free WiFi' => '📶', 'Pool' => '🏊', 'Restaurant' => '🍽️',
  'Gym' => '💪', 'Parking' => '🅿️', 'Breakfast' => '🍳',
  // ... more icons
];
?>

<h1><?= htmlspecialchars($hotel['name']) ?></h1>
<p>₱<?= number_format($hotel['price_per_night']) ?>/night</p>

<h2>Amenities:</h2>
<div class="amenities">
  <?php foreach ($hotel['amenities'] as $amenity): ?>
    <span><?= $amenityIcons[$amenity['amenity']] ?? '✓' ?> <?= htmlspecialchars($amenity['amenity']) ?></span>
  <?php endforeach; ?>
</div>

<h2>Activities in <?= htmlspecialchars($destination['name']) ?>:</h2>
<form method="POST" action="booking_confirm.php" id="bookingForm">
  <!-- Hidden inputs for form data -->
  <input type="hidden" name="dest_id" value="<?= $destId ?>">
  <input type="hidden" name="hotel_id" value="<?= $hotelId ?>">
  <input type="hidden" name="price_per_night" value="<?= $hotel['price_per_night'] ?>">
  
  <!-- Guest info -->
  <label>Name: <input type="text" name="guest_name" required></label>
  <label>Email: <input type="email" name="guest_email" required></label>
  
  <!-- Dates -->
  <label>Check-in: <input type="date" name="checkin" required></label>
  <label>Check-out: <input type="date" name="checkout" required></label>
  
  <!-- Guests and rooms -->
  <label>Guests: <input type="number" name="guests" value="1" required></label>
  <label>Rooms: <input type="number" name="rooms" value="1" required></label>
  
  <!-- Activities -->
  <h3>Select Activities:</h3>
  <?php foreach ($activities as $activity): ?>
    <label>
      <input type="checkbox" name="activities[]" value="<?= $activity['id'] ?>">
      <?= htmlspecialchars($activity['name']) ?> - ₱<?= number_format($activity['price']) ?>
    </label>
  <?php endforeach; ?>
  
  <!-- Payment -->
  <label>Payment Method:
    <select name="payment_method" required>
      <option value="">Select...</option>
      <option value="gcash">GCash</option>
      <option value="credit_card">Credit Card</option>
      <option value="debit_card">Debit Card</option>
    </select>
  </label>
  
  <!-- Special requests -->
  <label>Special Requests: <textarea name="requests"></textarea></label>
  
  <!-- Hidden total -->
  <input type="hidden" name="selected_acts" id="selectedActs" value="[]">
  
  <button type="submit">Book Now</button>
</form>

<?php include 'includes/footer.php'; ?>
```

### `booking_confirm.php` (Process Booking)
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

// Get form data
$guest_name = trim($_POST['guest_name'] ?? '');
$guest_email = filter_var($_POST['guest_email'] ?? '', FILTER_VALIDATE_EMAIL);
$dest_id = (int)($_POST['dest_id'] ?? 0);
$hotel_id = (int)($_POST['hotel_id'] ?? 0);
$check_in = $_POST['checkin'] ?? '';
$check_out = $_POST['checkout'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);
$rooms = (int)($_POST['rooms'] ?? 1);
$payment_method = $_POST['payment_method'] ?? '';
$requests = trim($_POST['requests'] ?? '');

// Validate
if (!$guest_name || !$guest_email || !$dest_id || !$hotel_id) {
  die('Missing required fields');
}

// Get hotel for price
$hotel = getHotelDetails($db, $hotel_id);
if (!$hotel) {
  die('Hotel not found');
}

// Calculate
$nights = calculateNights($check_in, $check_out);
$amounts = calculateBookingTotal($hotel['price_per_night'], $nights, $rooms, 0);

// Parse activities
$selected_acts = json_decode($_POST['selected_acts'] ?? '[]', true);
$activity_ids = array_column($selected_acts, 'id');

// Create booking
$booking_data = [
  'user_id' => $_SESSION['user_id'] ?? null,
  'guest_name' => $guest_name,
  'guest_email' => $guest_email,
  'destination_id' => $dest_id,
  'hotel_id' => $hotel_id,
  'check_in_date' => $check_in,
  'check_out_date' => $check_out,
  'number_of_guests' => $guests,
  'number_of_rooms' => $rooms,
  'subtotal_amount' => $amounts['subtotal'],
  'activities_total' => 0,
  'tax_amount' => $amounts['tax'],
  'total_price' => $amounts['total'],
  'payment_method' => $payment_method,
  'special_requests' => $requests,
  'activities' => $activity_ids
];

$ref_code = createBooking($db, $booking_data);

if ($ref_code) {
  // Save payment details
  $payment_data = [
    'payment_method' => $payment_method,
    'gcash_number' => $_POST['gcash_number'] ?? null,
    'gcash_name' => $_POST['gcash_name'] ?? null,
    'card_holder' => $_POST['card_holder'] ?? null,
    'card_number' => $_POST['card_number'] ?? null,
  ];
  
  $booking = getBookingByReference($db, $ref_code);
  if ($booking) {
    savePaymentDetails($db, $booking['id'], $payment_data);
  }
  
  // Show confirmation
  ?>
  <h1>✓ Booking Successful!</h1>
  <p>Reference: <strong><?= htmlspecialchars($ref_code) ?></strong></p>
  <p>Guest: <?= htmlspecialchars($guest_name) ?></p>
  <p>Total: ₱<?= number_format($booking_data['total_price']) ?></p>
  <a href="index.php">Back to Home</a>
  <?php
} else {
  echo "Booking failed. Please try again.";
}
?>
```

---

## 🎯 Summary of Changes

| File | Change | Why |
|------|--------|-----|
| All files | Add `require_once 'config/db.php'` | Connect to database |
| All files | Add `require_once 'includes/database_helpers.php'` | Load helper functions |
| All files | Remove `require_once 'data.php'` | Stop using mock data |
| destinations.php | `$destinations = getAllDestinations($db)` | Get real data |
| hotel.php | `$dest = getDestinationById($db, $destId)` | Query database |
| hotel.php | `$hotel = getHotelDetails($db, $hotelId)` | Get hotel + amenities |
| booking_confirm.php | Use `createBooking($db, $data)` | Save booking to DB |

---

## ✅ Testing Checklist

After making changes:

- [ ] Database created with `lakbaylokal_schema.sql`
- [ ] `config/db.php` has correct credentials
- [ ] `includes/database_helpers.php` copied
- [ ] `index.php` shows destinations from database
- [ ] `destinations.php?dest=1` shows hotels
- [ ] `hotel.php?dest=1&id=1` shows hotel details
- [ ] Can submit booking form
- [ ] Booking appears in database (check with phpMyAdmin)

---

## 🐛 If Something Doesn't Work

**Error: "Undefined function getAllDestinations()"**
- Solution: Make sure you have `require_once 'includes/database_helpers.php'`

**Error: "Connection failed"**
- Solution: Check credentials in `config/db.php`

**Bookings not saving**
- Solution: Check database exists with `mysql -u root lakbaylokal`

**Activities not showing**
- Solution: Use `getActivitiesByDestination($db, $destId)` instead of `$dest['acts']`

---

**Start with Step 1 and work through each step. You've got this!** 💪