# LakbayLokal Database - Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Create the Database (2 minutes)

**Option A: Using phpMyAdmin**

1. Open `http://localhost/phpmyadmin`
2. Click "New" on the left
3. Database name: `lakbaylokal`
4. Click "Create"
5. Click the new database
6. Click "Import" tab
7. Choose `lakbaylokal_schema.sql` file
8. Click "Go"

**Option B: Using Command Line**

```bash
# Open MySQL
mysql -u root -p

# Create and import
CREATE DATABASE lakbaylokal;
USE lakbaylokal;
SOURCE /path/to/lakbaylokal_schema.sql;

# Verify
SHOW TABLES;
```

### Step 2: Copy PHP Files (1 minute)

Copy these files to your project:
```
your-project/
├── config/
│   └── db.php                    ← Copy config_db.php here
├── includes/
│   └── database_helpers.php      ← Copy this file here
└── (your other files)
```

### Step 3: Update Database Credentials (1 minute)

Edit `config/db.php`:

```php
define('DB_HOST',     'localhost');      // Your host
define('DB_USER',     'root');           // Your username
define('DB_PASSWORD', 'password123');    // Your password (if any)
define('DB_NAME',     'lakbaylokal');
```

### Step 4: Test Connection (1 minute)

Create `test_db.php`:

```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// Test 1: Get all destinations
$destinations = getAllDestinations($db);
echo "<h2>Destinations:</h2>";
echo "Found " . count($destinations) . " destinations<br>";
foreach ($destinations as $dest) {
    echo "- " . $dest['name'] . "<br>";
}

// Test 2: Get hotels for first destination
$hotels = getHotelsByDestination($db, 1);
echo "<h2>Hotels in " . $destinations[0]['name'] . ":</h2>";
echo "Found " . count($hotels) . " hotels<br>";
foreach ($hotels as $hotel) {
    echo "- " . $hotel['name'] . " (₱" . number_format($hotel['price_per_night']) . "/night)<br>";
}

// Test 3: Get activities
$activities = getActivitiesByDestination($db, 1);
echo "<h2>Activities:</h2>";
echo "Found " . count($activities) . " activities<br>";
foreach ($activities as $activity) {
    echo "- " . $activity['name'] . " (₱" . $activity['price'] . ")<br>";
}

echo "<h3 style='color: green;'>✓ Database is working!</h3>";
?>
```

Visit `http://localhost/your-project/test_db.php` in your browser.

---

## 🧪 Testing Common Operations

### Test 1: Register a User

Create `register_test.php`:

```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// Create a test user
$user_id = createUser($db, "Test", "User", "test@example.com", "password123");

if ($user_id) {
    echo "✓ User created with ID: " . $user_id;
} else {
    echo "✗ Failed to create user";
}
?>
```

### Test 2: Login a User

```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// Get user
$user = getUserByEmail($db, "test@example.com");

if ($user) {
    echo "User found: " . $user['first_name'] . " " . $user['last_name'] . "<br>";
    
    // Verify password
    if (verifyPassword("password123", $user['password_hash'])) {
        echo "✓ Password is correct!";
    } else {
        echo "✗ Password is wrong!";
    }
} else {
    echo "✗ User not found";
}
?>
```

### Test 3: Create a Booking

```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// Calculate booking details
$nights = calculateNights("2024-06-15", "2024-06-18");  // 3 nights
$price_per_night = 5200;
$rooms = 1;
$activities_total = 450;

$amounts = calculateBookingTotal($price_per_night, $nights, $rooms, $activities_total);

echo "Booking Calculation:<br>";
echo "Nights: " . $nights . "<br>";
echo "Price per night: ₱" . number_format($price_per_night) . "<br>";
echo "Rooms: " . $rooms . "<br>";
echo "Subtotal: ₱" . number_format($amounts['subtotal']) . "<br>";
echo "Tax (12%): ₱" . number_format($amounts['tax']) . "<br>";
echo "Total: ₱" . number_format($amounts['total']) . "<br>";

// Create booking
$booking_data = [
    'user_id' => 1,
    'guest_name' => 'Juan Dela Cruz',
    'guest_email' => 'juan@example.com',
    'destination_id' => 1,
    'hotel_id' => 1,
    'check_in_date' => '2024-06-15',
    'check_out_date' => '2024-06-18',
    'number_of_guests' => 2,
    'number_of_rooms' => 1,
    'subtotal_amount' => $amounts['subtotal'],
    'activities_total' => $activities_total,
    'tax_amount' => $amounts['tax'],
    'total_price' => $amounts['total'],
    'payment_method' => 'gcash',
    'special_requests' => 'High floor please',
    'activities' => [1, 2]  // Activity IDs
];

$ref_code = createBooking($db, $booking_data);

if ($ref_code) {
    echo "<br><br>✓ Booking created!<br>";
    echo "Reference Code: <strong>" . $ref_code . "</strong>";
} else {
    echo "✗ Failed to create booking";
}
?>
```

---

## 📊 Database Structure Summary

```
USERS
├─ id, first_name, last_name, email, password_hash, role
│
DESTINATIONS
├─ id, name, region, description, price, image_url
│
HOTELS (linked to destinations)
├─ id, destination_id, name, price_per_night, stars, rating
├─ HOTEL_AMENITIES (many amenities per hotel)
├─ HOTEL_POLICIES (many policies per hotel)
│
ACTIVITIES (linked to destinations)
├─ id, destination_id, name, price
│
BOOKINGS (the main booking table)
├─ id, reference_code, user_id, guest_name, guest_email
├─ destination_id, hotel_id
├─ check_in_date, check_out_date
├─ number_of_guests, number_of_rooms
├─ subtotal_amount, activities_total, tax_amount, total_price
├─ payment_method, status
├─ BOOKING_ACTIVITIES (link activities to bookings)
│   └─ booking_id, activity_id, activity_name, activity_price
│
PAYMENT_DETAILS
└─ id, booking_id, payment_method, [payment-specific fields]
```

---

## 🔐 Important Security Reminders

### 1. Never commit passwords
```bash
# Add to .gitignore
config/db.php
```

### 2. Use prepared statements
```php
// ✓ Good
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

// ✗ Bad
$db->query("SELECT * FROM users WHERE email = '" . $email . "'");
```

### 3. Always hash passwords
```php
// ✓ Good
$hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

// ✗ Bad
$db->query("INSERT INTO users (password) VALUES ('" . $_POST['password'] . "')");
```

### 4. Escape output
```php
// ✓ Good
echo escapeOutput($user_input);

// ✗ Bad
echo $user_input;
```

### 5. Validate input
```php
// ✓ Good
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) exit("Invalid email");

// ✗ Bad
$email = $_POST['email'];
```

---

## 🛠️ Useful Database Queries

### View all bookings with status
```sql
SELECT reference_code, guest_name, guest_email, total_price, status, created_at
FROM bookings
ORDER BY created_at DESC;
```

### View pending bookings
```sql
SELECT * FROM bookings WHERE status = 'pending';
```

### Get hotel with all amenities and policies
```sql
SELECT 
    h.*,
    GROUP_CONCAT(DISTINCT ha.amenity) as amenities,
    GROUP_CONCAT(DISTINCT hp.policy) as policies
FROM hotels h
LEFT JOIN hotel_amenities ha ON h.id = ha.hotel_id
LEFT JOIN hotel_policies hp ON h.id = hp.hotel_id
WHERE h.id = 1
GROUP BY h.id;
```

### Get booking details with hotel and destination names
```sql
SELECT 
    b.*,
    d.name as destination_name,
    h.name as hotel_name,
    h.price_per_night
FROM bookings b
LEFT JOIN destinations d ON b.destination_id = d.id
LEFT JOIN hotels h ON b.hotel_id = h.id
WHERE b.reference_code = 'BK12345ABC';
```

### Revenue by destination
```sql
SELECT 
    d.name,
    COUNT(b.id) as total_bookings,
    SUM(b.total_price) as total_revenue
FROM destinations d
LEFT JOIN bookings b ON d.id = b.destination_id
GROUP BY d.id
ORDER BY total_revenue DESC;
```

### Most popular hotels
```sql
SELECT 
    h.name,
    d.name as destination,
    COUNT(b.id) as bookings,
    AVG(h.rating) as avg_rating
FROM hotels h
LEFT JOIN destinations d ON h.destination_id = d.id
LEFT JOIN bookings b ON h.id = b.hotel_id
GROUP BY h.id
ORDER BY bookings DESC
LIMIT 10;
```

---

## 📱 Sample Admin Dashboard Code

```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';
?>

<h1>Admin Dashboard</h1>

<!-- Pending Bookings -->
<h2>Pending Bookings</h2>
<?php
$pending = getAllBookings($db, 'pending');
?>
<table>
    <tr>
        <th>Reference</th>
        <th>Guest Name</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php foreach ($pending as $booking): ?>
        <tr>
            <td><?= escapeOutput($booking['reference_code']) ?></td>
            <td><?= escapeOutput($booking['guest_name']) ?></td>
            <td>₱<?= number_format($booking['total_price']) ?></td>
            <td><?= $booking['created_at'] ?></td>
            <td>
                <button onclick="confirmBooking('<?= $booking['reference_code'] ?>')">
                    Confirm
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Revenue Stats -->
<h2>This Month's Revenue</h2>
<?php
$result = $db->query("
    SELECT SUM(total_price) as revenue, COUNT(*) as bookings
    FROM bookings
    WHERE MONTH(created_at) = MONTH(NOW()) AND status != 'cancelled'
");
$stats = $result->fetch_assoc();
?>
<p>Total Bookings: <strong><?= $stats['bookings'] ?></strong></p>
<p>Total Revenue: <strong>₱<?= number_format($stats['revenue']) ?></strong></p>
```

---

## ✅ Checklist Before Going Live

- [ ] Database created and populated
- [ ] config/db.php updated with correct credentials
- [ ] database_helpers.php included in necessary files
- [ ] All database queries use prepared statements
- [ ] Passwords are hashed with password_hash()
- [ ] User input is validated before database operations
- [ ] User output is escaped with htmlspecialchars()
- [ ] config/db.php is added to .gitignore
- [ ] Foreign keys are working correctly
- [ ] Indexes are created for performance
- [ ] Test all CRUD operations work
- [ ] Admin can view all bookings
- [ ] Users can make and track bookings

---

## 🆘 Common Issues & Solutions

### Issue: "Column 'email' cannot be null"
**Solution:** Email is required. Check form validation.

### Issue: "Duplicate entry for key 'email'"
**Solution:** Email already exists in database. Check for typos or existing account.

### Issue: "Foreign key constraint fails"
**Solution:** Trying to book a hotel/destination that doesn't exist. Verify IDs are correct.

### Issue: Prepared statement not closing
**Solution:** Always call `$stmt->close()` after executing.

### Issue: MySQLi error but no error message
**Solution:** Add error checking:
```php
if ($db->connect_error) {
    die("Connection Error: " . $db->connect_error);
}

if (!$stmt->execute()) {
    die("Execute Error: " . $stmt->error);
}
```

---

## 🎓 Learning Path

1. **Understand the database structure** (read DATABASE_README.md)
2. **Test basic operations** (run test files)
3. **Create registration page** (use createUser function)
4. **Create login page** (use getUserByEmail + verifyPassword)
5. **Display destinations** (use getAllDestinations function)
6. **Display hotels** (use getHotelsByDestination function)
7. **Create booking form** (use createBooking function)
8. **Create admin dashboard** (use getAllBookings function)
9. **Add payment integration** (use savePaymentDetails function)

---

**You're ready to build LakbayLokal!** 🚀

For detailed documentation, see `DATABASE_README.md`
