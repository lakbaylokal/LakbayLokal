# 📦 LakbayLokal Database System - Complete Package

## 📂 Files Included

This package contains everything you need to set up a professional MySQL database for the LakbayLokal travel booking system. All files are ready to use!

### 1. 📊 **lakbaylokal_schema.sql** (13 KB)
**What it is:** Complete MySQL database schema with all tables, relationships, and sample data

**Contains:**
- 9 tables (users, destinations, hotels, activities, bookings, etc.)
- Foreign key relationships
- Proper indexes for performance
- Sample data (8 destinations, 10 hotels, multiple activities)
- 2 useful database views for reporting
- Comments explaining each table

**How to use:**
```bash
# Method 1: phpMyAdmin
1. Create database "lakbaylokal"
2. Go to Import tab
3. Choose this file
4. Click "Go"

# Method 2: Command line
mysql -u root -p lakbaylokal < lakbaylokal_schema.sql

# Method 3: MySQL client
mysql> CREATE DATABASE lakbaylokal;
mysql> USE lakbaylokal;
mysql> SOURCE /path/to/lakbaylokal_schema.sql;
```

---

### 2. ⚙️ **config_db.php** (1.8 KB)
**What it is:** Database connection configuration file

**Contains:**
- Database connection setup
- Error handling
- UTF-8 charset configuration
- Security notes and best practices

**How to use:**
```php
// Update these values with your MySQL credentials
define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASSWORD', 'your_password');
define('DB_NAME',     'lakbaylokal');

// Include in your PHP files
require_once 'config/db.php';

// Now use $db or $connection for queries
$result = $db->query("SELECT * FROM users");
```

**Important:**
- Store this file OUTSIDE the web root if possible
- Add to .gitignore to protect credentials
- Update username/password for production

---

### 3. 🔧 **database_helpers.php** (15 KB)
**What it is:** Collection of ready-to-use PHP functions for database operations

**Contains 20+ functions organized by category:**

#### User Functions:
- `createUser()` - Register new user
- `getUserByEmail()` - Login user
- `verifyPassword()` - Check password
- `emailExists()` - Check if email registered

#### Destination Functions:
- `getAllDestinations()` - List all destinations
- `getDestinationById()` - Get single destination

#### Hotel Functions:
- `getHotelsByDestination()` - List hotels in place
- `getHotelDetails()` - Get hotel with amenities/policies

#### Activity Functions:
- `getActivitiesByDestination()` - Get activities
- `getActivityById()` - Get single activity

#### Booking Functions:
- `createBooking()` - Create new booking
- `getBookingByReference()` - Get booking details
- `getAllBookings()` - Admin: view all bookings
- `updateBookingStatus()` - Change booking status
- `addActivitiesToBooking()` - Link activities to booking
- `getBookingActivities()` - Get activities in booking

#### Payment Functions:
- `savePaymentDetails()` - Save payment info
- `getPaymentDetails()` - Retrieve payment details

#### Utility Functions:
- `calculateNights()` - Calculate stay duration
- `calculateBookingTotal()` - Get total with tax
- `escapeOutput()` - Safely display user data

**How to use:**
```php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

// All functions use prepared statements (SQL injection safe)
$user_id = createUser($db, "Juan", "Dela Cruz", "juan@email.com", "password123");

$user = getUserByEmail($db, "juan@email.com");
if (verifyPassword("password123", $user['password_hash'])) {
    echo "Login successful!";
}

$hotels = getHotelsByDestination($db, 1);
foreach ($hotels as $hotel) {
    echo $hotel['name'];
}
```

---

### 4. 📖 **DATABASE_README.md** (27 KB)
**What it is:** Comprehensive documentation of the entire database system

**Contains:**
1. **System Overview** - What is LakbayLokal
2. **System Architecture** - How components work together
3. **Complete Database Design** - Detailed explanation of each table with examples
4. **Installation Steps** - Step-by-step setup instructions
5. **How the System Works** - User flow from browsing to payment
6. **Usage Examples** - Real PHP code for common tasks
7. **Important Concepts** - SQL injection, foreign keys, password security
8. **Troubleshooting** - Common problems and solutions

**Read this first** to understand the system before implementing.

---

### 5. ⚡ **QUICK_START.md** (12 KB)
**What it is:** Fast 5-minute setup guide to get started immediately

**Contains:**
1. **5-Minute Setup** - Create database and test
2. **Testing Common Operations** - Test code for each function
3. **Database Structure Summary** - Quick visual overview
4. **Security Reminders** - Important do's and don'ts
5. **Useful Database Queries** - Copy-paste SQL for reports
6. **Sample Admin Dashboard** - Example admin page code
7. **Checklist Before Going Live** - Pre-production checklist
8. **Common Issues & Solutions** - Quick fixes

**Best for:** Getting something working quickly, then reading DATABASE_README.md for details.

---

### 6. 💡 **sample_booking_flow.php** (21 KB)
**What it is:** Complete working example of the entire booking flow

**Contains:**
Functions for each step:
1. **Authentication** - Register, login, logout
2. **Browse Destinations** - Display all destinations
3. **Browse Hotels** - Show hotels in a destination
4. **Booking Form** - Complete booking form with price calculator
5. **Confirm Booking** - Process and save booking
6. **Booking Confirmation** - Display confirmation page
7. **View Bookings** - Show user's booking history

**Features:**
- Form validation
- Price calculation with taxes
- Activity selection
- Payment method options
- Session management

**How to use:**
```php
// Copy functions into your application
// Or use as reference for your own implementation

// Example: Display destinations
displayDestinations($db);

// Example: Create booking
$result = handleBookingSubmission($db);
if ($result['success']) {
    displayBookingConfirmation($db, $result['reference_code']);
}
```

---

## 🚀 Getting Started (3 Easy Steps)

### Step 1: Create the Database (2 minutes)
```bash
# Open phpMyAdmin and import lakbaylokal_schema.sql
# OR run in MySQL:
mysql -u root -p lakbaylokal < lakbaylokal_schema.sql
```

### Step 2: Set Up PHP Files (1 minute)
```
your-project/
├── config/
│   └── db.php                    ← Copy config_db.php here
├── includes/
│   └── database_helpers.php      ← Copy this file
└── (your PHP files)
```

### Step 3: Start Coding (1 minute)
```php
<?php
require_once 'config/db.php';
require_once 'includes/database_helpers.php';

$destinations = getAllDestinations($db);
foreach ($destinations as $dest) {
    echo $dest['name'];
}
?>
```

**That's it!** You're ready to build the application.

---

## 📊 Database Tables Overview

```
┌─ Users (for authentication)
│  ├─ first_name, last_name
│  ├─ email (unique)
│  └─ password_hash
│
├─ Destinations (places to visit)
│  ├─ name, region, description
│  ├─ price, image_url
│  │
│  ├─ → Hotels (accommodations)
│  │    ├─ name, price_per_night
│  │    ├─ stars, rating, reviews
│  │    ├─ checkin_time, checkout_time
│  │    ├─ → Hotel_Amenities (pool, WiFi, etc.)
│  │    └─ → Hotel_Policies (no pets, etc.)
│  │
│  └─ → Activities (things to do)
│       ├─ name, price
│       └─ → Booking_Activities (selected activities)
│
└─ Bookings (reservations)
   ├─ reference_code (unique ID like BK12345)
   ├─ guest_name, guest_email
   ├─ check_in_date, check_out_date
   ├─ number_of_guests, number_of_rooms
   ├─ total_price, tax_amount
   ├─ payment_method, status
   └─ → Payment_Details (payment info)
```

---

## 🎯 Common Tasks & How to Do Them

### Display All Destinations
```php
$destinations = getAllDestinations($db);
foreach ($destinations as $dest) {
    echo $dest['name'];
}
```

### Get Hotel with Amenities
```php
$hotel = getHotelDetails($db, $hotel_id);
echo $hotel['name'];
foreach ($hotel['amenities'] as $amenity) {
    echo $amenity['amenity'];
}
```

### Register User
```php
if (createUser($db, $fname, $lname, $email, $password)) {
    echo "Registration successful!";
}
```

### Create Booking
```php
$ref_code = createBooking($db, [
    'guest_name' => 'Juan Dela Cruz',
    'guest_email' => 'juan@email.com',
    'destination_id' => 1,
    'hotel_id' => 1,
    'check_in_date' => '2024-06-15',
    'check_out_date' => '2024-06-18',
    'number_of_guests' => 2,
    'number_of_rooms' => 1,
    'total_price' => 18000,
    'payment_method' => 'gcash',
    'activities' => [1, 2, 3]
]);
```

### View User Bookings
```php
$bookings = getAllBookings($db);
// Or filter by status:
$pending = getAllBookings($db, 'pending');
```

---

## 🔒 Security Best Practices (IMPORTANT!)

### ✅ DO THIS:
```php
// Use prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// Hash passwords
$hash = password_hash($password, PASSWORD_BCRYPT);
if (password_verify($password, $hash)) { ... }

// Escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// Validate input
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
```

### ❌ NEVER DO THIS:
```php
// SQL injection vulnerability
$db->query("SELECT * FROM users WHERE email = '" . $_POST['email'] . "'");

// Storing plain passwords
$db->query("INSERT INTO users (password) VALUES ('" . $_POST['password'] . "')");

// XSS vulnerability
echo $_POST['name'];

// No validation
$count = $_POST['rooms'];  // Could be text!
```

---

## 📋 File Checklist

- [ ] Download all 6 files
- [ ] Read QUICK_START.md (5 minutes)
- [ ] Create database using lakbaylokal_schema.sql
- [ ] Copy config_db.php to config/ folder
- [ ] Copy database_helpers.php to includes/ folder
- [ ] Update database credentials in config_db.php
- [ ] Test with sample_booking_flow.php
- [ ] Read DATABASE_README.md for details
- [ ] Build your application using the helper functions

---

## 🆘 Need Help?

### Common Issues:

**"Database connection failed"**
- Check MySQL is running
- Verify credentials in config_db.php
- Ensure database exists

**"Function not found"**
- Make sure database_helpers.php is included
- Check spelling of function name

**"Duplicate entry error"**
- Email/reference code already exists
- Check database for existing records

**See QUICK_START.md** for more troubleshooting.

---

## 📚 Documentation Order

Read in this order:

1. **This file** (2 min) - Get overview
2. **QUICK_START.md** (5 min) - Set up database
3. **sample_booking_flow.php** (10 min) - See real code
4. **DATABASE_README.md** (20 min) - Understand system deeply

Total: ~40 minutes to understand everything!

---

## 🎓 Learning Path

1. Create database
2. Read QUICK_START.md
3. Run sample test code
4. Copy sample_booking_flow.php functions into your app
5. Customize for your needs
6. Read DATABASE_README.md when you need deeper understanding

---

## 💬 Questions?

Everything is documented in:
- **Quick answers:** QUICK_START.md
- **Detailed explanations:** DATABASE_README.md
- **Working code:** sample_booking_flow.php
- **SQL reference:** lakbaylokal_schema.sql comments

---

## 📝 License & Credits

Created for **LakbayLokal** - A travel booking system for beginner PHP students.

**Designed for:**
- Beginners learning PHP + MySQL
- Students understanding database relationships
- Small projects needing a solid database foundation

No frameworks. No ORM. Just simple, clean, secure MySQL code.

---

**Ready to start? Begin with:** [QUICK_START.md](./QUICK_START.md)

Good luck with your project! 🚀🏖️
