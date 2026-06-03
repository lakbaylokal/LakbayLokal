# LakbayLokal — Complete Project Analysis & Refactoring Guide

> **Prepared after full codebase review** of all PHP files, CSS, JS, SQL, and misc guides.  
> Read top to bottom — each section builds on the previous one.

---

## TABLE OF CONTENTS

1. [Current State Summary](#1-current-state-summary)
2. [All Errors, Bad Practices & Structural Issues](#2-all-errors-bad-practices--structural-issues)
3. [Clean Folder Structure](#3-clean-folder-structure)
4. [Complete Database Schema](#4-complete-database-schema)
5. [Migration from data.php to MySQL](#5-migration-from-dataphp-to-mysql)
6. [Proper Separation: UI / Logic / Database](#6-proper-separation-ui--logic--database)
7. [Admin CRUD System Design](#7-admin-crud-system-design)
8. [Step-by-Step Implementation Order](#8-step-by-step-implementation-order)

---

## 1. CURRENT STATE SUMMARY

Here is what your project looks like right now, honestly assessed:

| Area | Status |
|------|--------|
| UI / Frontend | ✅ Good — clean Bootstrap + custom CSS, responsive |
| Data layer | ⚠️ All hardcoded in `data.php` — no real database used |
| Authentication | ❌ Fake — hardcoded credentials in `api_auth.php`, users stored in `$_SESSION` |
| Bookings | ❌ Not saved anywhere — only stored in browser `sessionStorage` |
| Admin panel | ❌ Shell only — no real data, all placeholders |
| Config | ⚠️ `config/db.php` exists but is never actually used for data |
| Security | ❌ Several serious vulnerabilities (detailed below) |
| Folder structure | ⚠️ Messy — business logic mixed into views, orphaned files |
| Misc folder | ⚠️ Has good reference SQL/guides but they are disconnected from live code |

The good news: your UI is solid and the data structure in `data.php` is well-organized. The migration path is clear.

---

## 2. ALL ERRORS, BAD PRACTICES & STRUCTURAL ISSUES

### 🔴 CRITICAL (Fix first — these are security holes)

---

**Issue C1: Hardcoded admin credentials in plain text**

File: `api_auth.php`, lines ~35–50

```php
// CURRENT (DANGEROUS):
if ($email === 'admin@lakbaylokal.com' && $password === 'admin123') {
```

This is visible to anyone who reads your source code. If your repo is ever public or leaked, your admin account is instantly compromised.

**Fix:** Store hashed passwords in the database. Use `password_verify()` to check them.

---

**Issue C2: Users stored in `$_SESSION` — data lost on every server restart**

File: `api_auth.php`, lines ~18–30

```php
// CURRENT (WRONG):
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [ ... ]; // in-memory fake database
}
```

Every time the PHP session expires or the server restarts, all registered users disappear. This is not a real user system.

**Fix:** Insert users into the `users` table in MySQL on signup. Query MySQL on login.

---

**Issue C3: Booking data is never saved to a database**

File: `booking_confirm.php`, bottom JavaScript

```js
// CURRENT (WRONG):
sessionStorage.setItem('lbl_bookings', JSON.stringify(bookings));
```

Bookings only exist in the user's browser tab. Close the tab → booking is gone. Admin cannot see any bookings. No payment record exists. This is the single biggest gap between your current state and a working system.

**Fix:** On form POST to `booking_confirm.php`, INSERT a row into the `bookings` table in MySQL before rendering the confirmation page.

---

**Issue C4: No CSRF protection on forms**

Files: `hotel.php` (booking form), `api_auth.php`

Any malicious website can silently submit your booking form on behalf of a logged-in user.

**Fix:** Generate a CSRF token in the session when rendering the form, include it as a hidden input, and verify it on POST.

```php
// In form rendering:
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// Hidden input:
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
// In POST handler:
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) die('Invalid request');
```

---

**Issue C5: Sensitive payment data passed through POST fields**

File: `hotel.php` (booking form), `booking_confirm.php`

Card numbers, CVVs, and GCash numbers are sent as plain POST fields and echoed into hidden inputs. **Never store or forward raw card details through your own server.** This violates PCI-DSS.

**Fix for your project scope:** For a school project, it is acceptable to simulate payment. Store only the *payment method type* (gcash/credit_card) in the database — never the card number or CVV. Add a note that a real system would use a payment gateway like PayMongo or Stripe.

---

### 🟠 SERIOUS (Fix before connecting the database)

---

**Issue S1: `config/db.php` is included in `index.php` but the connection is never used**

File: `index.php`, line 2

```php
include 'config/db.php'; // Creates $conn...
// ...but $conn is never used anywhere in index.php
```

The DB connection is opened for nothing. Meanwhile, `data.php` is used for all actual data. This creates confusion about which source is authoritative.

**Fix:** Remove `include 'config/db.php'` from `index.php` for now. Add it back only when you start replacing `data.php` calls with database queries.

---

**Issue S2: `filterDestinations()` is duplicated**

File: `destinations.php` (around line 8–13) AND `includes/destination_functions.php`

The same filtering logic is written twice. `destinations.php` has an inline version AND imports the function from `includes/destination_functions.php`. Only one is actually used — the PHP `include` version. The inline code at the top of the file does nothing and is confusing.

**Fix:** Remove the inline filter from `destinations.php`. Use only the function from `destination_functions.php`.

---

**Issue S3: `booking_confirm.php` receives `total_price` from the user via POST**

File: `booking_confirm.php`, line ~23

```php
$totalPrice = (int)($_POST['total_price'] ?? 0);
```

A user can open DevTools, change the hidden `total_price` field to `1`, and book a ₱12,500 hotel for ₱1.

**Fix:** Never trust price data from the client. Always recalculate the total price on the server using data from your own database:

```php
// In booking_confirm.php — CORRECT approach:
$hotel = getHotelFromDB($hotelId);      // query your database
$nights = calculateNights($checkin, $checkout);
$total = ($hotel['price'] * $nights * $rooms) + $actsTotal;
$tax = round($total * 0.12);
$finalTotal = $total + $tax;
```

---

**Issue S4: `htmlspecialchars()` used for sanitization, not just output escaping**

File: `booking_confirm.php`, lines 8–20

```php
$destId = htmlspecialchars($_POST['dest_id'] ?? '');
```

`htmlspecialchars()` is for **displaying** data safely in HTML. It is NOT an input sanitization function. For IDs that get used in database queries, you need parameterized queries (prepared statements), not string escaping.

---

**Issue S5: Admin panel has NO actual data or CRUD operations**

File: `admin/index.php`

Every section of the admin panel just shows placeholder text like "Hotel lists loading..." or "No destinations found in database." The admin cannot add, edit, or delete anything. It is a UI shell with no backend.

---

**Issue S6: `components/dashboard.php` references `showPage()` — a function that doesn't exist**

File: `components/dashboard.php`, line ~12

```js
onclick="showPage('destinations')"
```

There is no `showPage()` function defined anywhere in the codebase. This will throw a JavaScript error silently.

---

**Issue S7: `booking_confirm.php` renders a `<form>` that posts to `components/payment.php` — which doesn't exist**

File: `booking_confirm.php`, around line ~70

```php
<form action="components/payment.php" method="POST">
```

`components/payment.php` does not exist in the project. Submitting that form leads to a 404 error.

---

**Issue S8: `admin/index.php` redirects to `../login.php` which doesn't exist**

File: `admin/index.php`, line 4

```php
header("Location: ../login.php");
```

There is no `login.php` file. This redirect leads to a 404.

---

### 🟡 BAD PRACTICES (Fix during refactoring)

---

**Issue B1: Inline JavaScript and CSS scattered throughout PHP files**

File: `hotel.php` — has 200+ lines of JavaScript embedded at the bottom.

This makes code harder to maintain. JavaScript should be in `/assets/js/` files.

---

**Issue B2: Inline styles used heavily throughout templates**

Files: `destinations.php`, `hotel.php`, `booking_confirm.php`

```php
style="display:flex;justify-content:space-between;padding:0.9rem 1rem;..."
```

These should be CSS classes. Inline styles cannot be overridden cleanly and are hard to maintain.

---

**Issue B3: Business logic inside view files**

Files: `destinations.php`, `hotel.php`

Sorting hotels (`usort()`), filtering destinations, and computing prices are all done directly in view files. These should be in separate function files.

---

**Issue B4: No `.gitignore` properly excluding `config/db.php`**

If a student pushes to GitHub, the `config/db.php` with the database password is visible to the public. Your `.gitignore` should include `config/db.php` and instead provide a `config/db.example.php` template.

---

**Issue B5: `data.php` helper functions use `global $destinations`**

File: `data.php`, bottom functions

```php
function getDestById(string $id): ?array {
  global $destinations; // bad practice
```

Using `global` is discouraged. Pass the array as a parameter instead, or better — replace with a database query function.

---

**Issue B6: String IDs used as primary keys in the planned schema**

File: `misc/lakbaylokal.sql`

```sql
`id` VARCHAR(50) PRIMARY KEY  -- for destinations
`id` VARCHAR(100) PRIMARY KEY -- for hotels
```

String PKs like `'baguio'` or `'sotogrande-baguio'` are acceptable for now but can cause JOIN performance issues at scale. Consider using `INT AUTO_INCREMENT` as the real PK and keeping the string as a `slug` column.

---

**Issue B7: `misc/` folder contains production-relevant files (SQL schema, guides) but is disorganized**

The `misc/` folder has `lakbaylokal.sql`, `MIGRATION_GUIDE.md`, `GUIDE.md`, and a whole `sample-files/` subfolder with duplicated/conflicting schema versions. There are two SQL files (`lakbaylokal.sql` and `sample-files/lakbaylokal_schema.sql`) with different table structures. This is confusing — you need one authoritative schema.

---

## 3. CLEAN FOLDER STRUCTURE

Here is the recommended structure for your refactored project. This follows the MVC-inspired pattern without requiring a full framework.

```
LakbayLokal/
│
├── config/
│   ├── db.php                  ← Database connection (KEEP OUT OF GIT)
│   └── db.example.php          ← Template with empty values (commit this)
│
├── includes/                   ← Reusable PHP components (no business logic)
│   ├── header.php
│   ├── footer.php
│   └── amenity-icons.php
│
├── views/                      ← Pure display files (HTML + minimal PHP echo)
│   ├── home.view.php
│   ├── destinations.view.php
│   ├── hotel.view.php
│   ├── booking-confirm.view.php
│   └── error.view.php
│
├── models/                     ← Database query functions only
│   ├── DestinationModel.php
│   ├── HotelModel.php
│   ├── BookingModel.php
│   ├── UserModel.php
│   └── DiscountModel.php
│
├── services/                   ← Business logic (calculations, validation)
│   ├── BookingService.php
│   ├── AuthService.php
│   └── PriceService.php
│
├── admin/
│   ├── index.php               ← Admin router/layout
│   ├── assets/
│   │   └── style.css
│   └── pages/
│       ├── dashboard.php
│       ├── destinations.php    ← CRUD for destinations
│       ├── hotels.php          ← CRUD for hotels
│       ├── activities.php      ← CRUD for activities
│       ├── bookings.php        ← View/manage bookings
│       └── discounts.php       ← CRUD for discount codes
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── hotel.css
│   │   └── auth.css
│   ├── js/
│   │   ├── home.js
│   │   ├── booking.js          ← Move hotel.php JS here
│   │   ├── destinations.js
│   │   └── auth.js
│   └── pics/                   ← All images
│
├── database/
│   ├── schema.sql              ← The ONE authoritative schema
│   └── seed.sql                ← INSERT statements from data.php
│
├── index.php                   ← Home controller/router
├── destinations.php            ← Destinations controller
├── hotel.php                   ← Hotel detail controller
├── booking_confirm.php         ← Booking POST handler
├── api_auth.php                ← Auth API endpoint
├── .gitignore                  ← Must include config/db.php
└── README.md
```

**Key changes from current structure:**
- `data.php` → replaced by `models/` + `database/seed.sql`
- `misc/` → moved useful files into `database/`; the rest deleted
- `components/dashboard.php` → moved to `views/` or `admin/pages/`
- JavaScript from `hotel.php` → moved to `assets/js/booking.js`

---

## 4. COMPLETE DATABASE SCHEMA

This is the single authoritative schema. It covers all current features plus bookings, payments, discount codes, and admin.

### 4.1 Users Table

```sql
CREATE TABLE users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    first_name   VARCHAR(100) NOT NULL,
    last_name    VARCHAR(100) NOT NULL,
    middle_name  VARCHAR(100),
    email        VARCHAR(150) NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,           -- bcrypt hash, NEVER plain text
    role         ENUM('user', 'admin') DEFAULT 'user',
    is_active    TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 4.2 Destinations Table

```sql
CREATE TABLE destinations (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    slug         VARCHAR(50) NOT NULL UNIQUE,     -- 'baguio', 'palawan', etc.
    name         VARCHAR(100) NOT NULL,
    region       ENUM('Luzon', 'Visayas', 'Mindanao') NOT NULL,
    emoji        VARCHAR(10),
    tagline      VARCHAR(255),
    description  TEXT,
    price        INT DEFAULT 0,                   -- base/display price
    price_from   INT DEFAULT 0,                   -- cheapest hotel price
    image_path   VARCHAR(255),
    gradient_bg  VARCHAR(500),
    is_active    TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 4.3 Hotels Table

```sql
CREATE TABLE hotels (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(100) NOT NULL UNIQUE,  -- 'sotogrande-baguio'
    destination_id  INT NOT NULL,
    name            VARCHAR(150) NOT NULL,
    image_path      VARCHAR(255),
    location        VARCHAR(200),
    description     TEXT,
    stars           TINYINT DEFAULT 0,
    price_per_night INT DEFAULT 0,
    rating          DECIMAL(3,1) DEFAULT 0.0,
    reviews_count   INT DEFAULT 0,
    checkin_time    TIME,
    checkout_time   TIME,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    INDEX idx_destination (destination_id)
);
```

### 4.4 Hotel Amenities Table

```sql
CREATE TABLE hotel_amenities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id    INT NOT NULL,
    amenity     VARCHAR(100) NOT NULL,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    INDEX idx_hotel (hotel_id)
);
```

### 4.5 Hotel Policies Table

```sql
CREATE TABLE hotel_policies (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id    INT NOT NULL,
    policy      VARCHAR(255) NOT NULL,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    INDEX idx_hotel (hotel_id)
);
```

### 4.6 Activities Table

```sql
CREATE TABLE activities (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    destination_id  INT NOT NULL,
    name            VARCHAR(200) NOT NULL,
    price           INT DEFAULT 0,
    description     TEXT,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    INDEX idx_destination (destination_id)
);
```

### 4.7 Discount Codes Table

```sql
CREATE TABLE discount_codes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(50) NOT NULL UNIQUE,    -- 'SUMMER20', 'LAKBAY10'
    type            ENUM('percent', 'fixed') NOT NULL,
    value           DECIMAL(10,2) NOT NULL,         -- 20 = 20% off, or ₱500 off
    min_spend       INT DEFAULT 0,                  -- minimum booking total to qualify
    max_uses        INT DEFAULT NULL,               -- NULL = unlimited
    uses_count      INT DEFAULT 0,                  -- how many times used so far
    valid_from      DATE,
    valid_until     DATE,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4.8 Bookings Table

```sql
CREATE TABLE bookings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    reference       VARCHAR(20) NOT NULL UNIQUE,   -- 'LBL-A1B2C3D4'
    user_id         INT,                            -- NULL if guest (not logged in)
    hotel_id        INT NOT NULL,
    destination_id  INT NOT NULL,
    guest_name      VARCHAR(200) NOT NULL,
    guest_email     VARCHAR(150) NOT NULL,
    checkin_date    DATE NOT NULL,
    checkout_date   DATE NOT NULL,
    nights          INT NOT NULL,
    rooms           TINYINT DEFAULT 1,
    guests_count    TINYINT DEFAULT 1,
    special_requests TEXT,
    hotel_subtotal  INT NOT NULL,                  -- price * nights * rooms
    activities_total INT DEFAULT 0,
    discount_amount INT DEFAULT 0,
    tax_amount      INT NOT NULL,
    total_price     INT NOT NULL,                  -- final amount
    discount_code_id INT DEFAULT NULL,
    status          ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)         REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (hotel_id)        REFERENCES hotels(id),
    FOREIGN KEY (destination_id)  REFERENCES destinations(id),
    FOREIGN KEY (discount_code_id) REFERENCES discount_codes(id) ON DELETE SET NULL,
    INDEX idx_user    (user_id),
    INDEX idx_hotel   (hotel_id),
    INDEX idx_status  (status)
);
```

### 4.9 Booking Activities Table (which activities were selected per booking)

```sql
CREATE TABLE booking_activities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    booking_id  INT NOT NULL,
    activity_id INT NOT NULL,
    price_paid  INT NOT NULL,               -- snapshot of price at time of booking
    FOREIGN KEY (booking_id)  REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id),
    INDEX idx_booking (booking_id)
);
```

### 4.10 Payments Table

```sql
CREATE TABLE payments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT NOT NULL UNIQUE,           -- one payment per booking
    method          ENUM('gcash','credit_card','debit_card','cash') NOT NULL,
    status          ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    amount          INT NOT NULL,
    transaction_ref VARCHAR(100),                  -- reference from payment gateway
    paid_at         TIMESTAMP,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
```

### Entity Relationship Summary

```
users ──────────────────────────── bookings (one user → many bookings)
destinations ───────────────────── hotels (one dest → many hotels)
destinations ───────────────────── activities (one dest → many activities)
hotels ─────────────────────────── bookings (one hotel → many bookings)
bookings ───────────────────────── payments (one booking → one payment)
bookings ───────────────────────── booking_activities (one booking → many activities)
discount_codes ─────────────────── bookings (one code → many bookings)
```

---

## 5. MIGRATION FROM data.php TO MYSQL — STEP BY STEP

### Step 1: Set up the database

Open phpMyAdmin (via XAMPP) and run the schema from Section 4. Or create a single file `database/schema.sql` with all the CREATE TABLE statements and import it.

### Step 2: Convert data.php into a seed file

Create `database/seed.sql`. For each destination in `data.php`, write INSERT statements:

```sql
-- Destinations
INSERT INTO destinations (slug, name, region, emoji, tagline, description, price, price_from, image_path)
VALUES 
  ('baguio', 'Baguio City', 'Luzon', '🌲', 'The Summer Capital of the Philippines', 
   'Escape to the cool mountain air...', 4500, 2800, 'assets/pics/Baguio.jpg'),
  ('vigan', 'Vigan City', 'Luzon', '🏛️', 'UNESCO World Heritage City',
   'Walk the cobblestone streets...', 6500, 3500, 'assets/pics/vigan.jpg');
   -- ... (all 8 destinations)

-- Hotels (use the destination INT id from the previous INSERT)
INSERT INTO hotels (slug, destination_id, name, image_path, location, description, stars, price_per_night, rating, reviews_count, checkin_time, checkout_time)
VALUES
  ('sotogrande-baguio', 1, 'Sotogrande Hotel Baguio', 'assets/pics/sotogrande.jpg',
   'Session Road, Baguio City', 'A modern boutique hotel...', 4, 5200, 4.6, 312, '14:00', '11:00');
  -- ... (all hotels)
```

You can also write a one-time PHP migration script `database/migrate.php` that reads `data.php` and runs INSERTs automatically — this avoids writing SQL by hand.

### Step 3: Create the Model files

Create `models/DestinationModel.php`:

```php
<?php
// models/DestinationModel.php

function getAllDestinations(mysqli $conn): array {
    $result = $conn->query("SELECT * FROM destinations WHERE is_active = 1 ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getDestinationBySlug(mysqli $conn, string $slug): ?array {
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE slug = ? AND is_active = 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}
```

Create `models/HotelModel.php`:

```php
<?php
// models/HotelModel.php

function getHotelsByDestination(mysqli $conn, int $destinationId): array {
    $stmt = $conn->prepare("
        SELECT h.*, GROUP_CONCAT(a.amenity) as amenities
        FROM hotels h
        LEFT JOIN hotel_amenities a ON h.id = a.hotel_id
        WHERE h.destination_id = ? AND h.is_active = 1
        GROUP BY h.id
    ");
    $stmt->bind_param("i", $destinationId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getHotelBySlug(mysqli $conn, string $slug): ?array {
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}
```

### Step 4: Update your page files to use the database

In `destinations.php`, replace:

```php
// OLD:
require_once 'data.php';
$dest = getDestById($destId);

// NEW:
require_once 'config/db.php';
require_once 'models/DestinationModel.php';
require_once 'models/HotelModel.php';

$dest = getDestinationBySlug($conn, $destId);
$hotels = $dest ? getHotelsByDestination($conn, $dest['id']) : [];
```

### Step 5: Save bookings to the database

In `booking_confirm.php`, after validating inputs, INSERT the booking:

```php
require_once 'models/BookingModel.php';

// Recalculate price server-side (never trust client)
$hotel = getHotelBySlug($conn, $hotelId);
$nights = (strtotime($checkout) - strtotime($checkin)) / 86400;
$hotelSubtotal = $hotel['price_per_night'] * $nights * $rooms;
$subtotal = $hotelSubtotal + $actsTotal;
$tax = round($subtotal * 0.12);
$total = $subtotal + $tax;

$ref = 'LBL-' . strtoupper(substr(md5(uniqid()), 0, 8));

$bookingId = createBooking($conn, [
    'reference'     => $ref,
    'user_id'       => $_SESSION['user']['id'] ?? null,
    'hotel_id'      => $hotel['id'],
    'guest_name'    => $guestName,
    'guest_email'   => $guestEmail,
    'checkin_date'  => $checkin,
    'checkout_date' => $checkout,
    'nights'        => $nights,
    'rooms'         => $rooms,
    'total_price'   => $total,
    // ... etc
]);
```

### Step 6: Remove data.php (final step, only after all pages use the DB)

Once every page loads data from MySQL, delete `data.php`. Until then, you can keep it as a fallback during transition.

---

## 6. PROPER SEPARATION: UI / LOGIC / DATABASE

The pattern to follow is **Controller → Service → Model → View**:

```
HTTP Request
     ↓
[Controller] e.g. hotel.php
  - Gets input from $_GET / $_POST
  - Calls Service or Model functions
  - Passes data to View
     ↓
[Model] e.g. models/HotelModel.php
  - Only talks to the database
  - Returns plain PHP arrays
     ↓
[Service] e.g. services/PriceService.php
  - Business logic: calculates totals, validates dates
  - Does NOT touch the database or HTML
     ↓
[View] e.g. views/hotel.view.php
  - Only displays data passed to it
  - No database queries
  - No business calculations
```

### Concrete example: hotel.php refactored

**hotel.php (Controller)**
```php
<?php
require_once 'config/db.php';
require_once 'models/HotelModel.php';
require_once 'models/DestinationModel.php';
require_once 'services/PriceService.php';

$destSlug  = $_GET['dest'] ?? '';
$hotelSlug = $_GET['id']   ?? '';

$dest  = getDestinationBySlug($conn, $destSlug);
$hotel = getHotelBySlug($conn, $hotelSlug);

if (!$dest || !$hotel) {
    header('Location: destinations.php');
    exit;
}

$activities   = getActivitiesByDestination($conn, $dest['id']);
$otherHotels  = getHotelsByDestination($conn, $dest['id']);
$priceBreakdown = null; // computed later by JS or on form submit

$pageTitle  = $hotel['name'] . ' — LakbayLokal';
$activePage = 'destinations';

include 'includes/header.php';
include 'views/hotel.view.php';
include 'includes/footer.php';
```

**views/hotel.view.php (View — display only)**
```php
<!-- Just HTML with echoed variables, no logic -->
<h1><?= htmlspecialchars($hotel['name']) ?></h1>
<p><?= htmlspecialchars($hotel['description']) ?></p>
```

**services/PriceService.php (Business Logic)**
```php
<?php
function calculateBookingTotal(int $pricePerNight, int $nights, int $rooms, int $activitiesTotal): array {
    $hotelSubtotal = $pricePerNight * $nights * $rooms;
    $subtotal = $hotelSubtotal + $activitiesTotal;
    $tax = round($subtotal * 0.12);
    return [
        'hotel_subtotal' => $hotelSubtotal,
        'activities'     => $activitiesTotal,
        'tax'            => $tax,
        'total'          => $subtotal + $tax,
    ];
}
```

**models/HotelModel.php (Database)**
```php
<?php
function getHotelBySlug(mysqli $conn, string $slug): ?array {
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}
```

---

## 7. ADMIN CRUD SYSTEM DESIGN

Your current admin panel (`admin/index.php`) is a shell. Here is how to make each section functional, using PHP only (no separate C# backend needed).

### Admin Authentication

Keep the `session_start()` + role check you already have. Just fix the redirect target:

```php
// admin/index.php — top of file
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /index.php"); // redirect to home, not missing login.php
    exit;
}
```

### Destinations CRUD — `admin/pages/destinations.php`

```php
<?php
require_once '../../config/db.php';
require_once '../../models/DestinationModel.php';

// Handle CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
    $stmt = $conn->prepare("INSERT INTO destinations (slug, name, region, tagline, description, price, price_from) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssii", $_POST['slug'], $_POST['name'], $_POST['region'], $_POST['tagline'], $_POST['description'], $_POST['price'], $_POST['price_from']);
    $stmt->execute();
    header("Location: ?page=destinations&msg=created");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM destinations WHERE id = $id"); // use prepared stmt in production
    header("Location: ?page=destinations");
    exit;
}

// READ (display all)
$destinations = getAllDestinations($conn);
?>

<!-- HTML Table with Edit / Delete buttons -->
<table>
  <thead><tr><th>Name</th><th>Region</th><th>Price</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($destinations as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['name']) ?></td>
        <td><?= htmlspecialchars($d['region']) ?></td>
        <td>₱<?= number_format($d['price']) ?></td>
        <td>
          <a href="?page=destinations&edit=<?= $d['id'] ?>">Edit</a>
          <a href="?page=destinations&delete=<?= $d['id'] ?>" onclick="return confirm('Delete this destination?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Add form -->
<form method="POST">
  <input type="hidden" name="action" value="create">
  <input name="slug" placeholder="baguio" required>
  <input name="name" placeholder="Baguio City" required>
  <!-- ... other fields ... -->
  <button type="submit">Add Destination</button>
</form>
```

Apply the same CREATE / READ / UPDATE / DELETE pattern to:
- `admin/pages/hotels.php`
- `admin/pages/activities.php`
- `admin/pages/discounts.php`

### Bookings Admin Page — `admin/pages/bookings.php`

For bookings, admin should only be able to **view** and **change status** (confirm/cancel) — not create them.

```php
// Change booking status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_status') {
    $id = (int)$_POST['booking_id'];
    $status = in_array($_POST['status'], ['pending','confirmed','cancelled','completed'])
        ? $_POST['status'] : 'pending';
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: ?page=bookings");
    exit;
}

// Read all bookings with hotel and destination names
$result = $conn->query("
    SELECT b.*, h.name as hotel_name, d.name as dest_name, p.status as payment_status
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.id
    JOIN destinations d ON b.destination_id = d.id
    LEFT JOIN payments p ON b.id = p.booking_id
    ORDER BY b.created_at DESC
");
```

### Admin Dashboard Stats

Replace the hardcoded "12 Active Destinations" with real queries:

```php
$totalBookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
$totalRevenue  = $conn->query("SELECT SUM(total_price) FROM bookings WHERE status = 'confirmed'")->fetch_row()[0];
$totalDestinations = $conn->query("SELECT COUNT(*) FROM destinations WHERE is_active = 1")->fetch_row()[0];
```

---

## 8. STEP-BY-STEP IMPLEMENTATION ORDER

Follow this order strictly — each step builds on the previous. Do NOT skip ahead.

---

### Phase 1 — Foundation (Day 1) ✅ Do this first

**Step 1.1 — Fix the folder structure**
- Create `models/`, `views/`, `services/`, `database/` folders
- Move `misc/lakbaylokal.sql` → `database/schema.sql` (use the one that has all tables from Section 4)
- Move inline JS from `hotel.php` → `assets/js/booking.js`
- Add `config/db.php` to `.gitignore`

**Step 1.2 — Set up the database**
- Open XAMPP → phpMyAdmin
- Import `database/schema.sql`
- Verify all tables were created

**Step 1.3 — Seed the database**
- Write `database/seed.php` (or `seed.sql`) with all 8 destinations, ~24 hotels, and all activities from `data.php`
- Run it once — data is now in MySQL

**Step 1.4 — Test the connection**
- Verify `config/db.php` connects without errors
- Write a quick test: `$conn->query("SELECT COUNT(*) FROM destinations")` and echo the result

---

### Phase 2 — Replace data.php with the database (Day 1–2)

**Step 2.1 — Create Model files**
- `models/DestinationModel.php` — `getAllDestinations()`, `getDestinationBySlug()`
- `models/HotelModel.php` — `getHotelsByDestination()`, `getHotelBySlug()`
- `models/ActivityModel.php` — `getActivitiesByDestination()`

**Step 2.2 — Update destinations.php**
- Replace `require_once 'data.php'` with Model calls
- Test that the destination grid and hotel list still render correctly

**Step 2.3 — Update hotel.php**
- Replace data.php calls with Model calls
- Move the 200 lines of JavaScript to `assets/js/booking.js`
- Test that hotel details, activities, and the booking form all work

**Step 2.4 — Update index.php**
- Remove `include 'config/db.php'` (it was unused)
- Replace data.php calls with Model calls for hero stats and featured destinations

---

### Phase 3 — Fix Authentication (Day 2)

**Step 3.1 — Create `models/UserModel.php`**

```php
function createUser($conn, $firstName, $lastName, $email, $passwordHash): bool { ... }
function getUserByEmail($conn, $email): ?array { ... }
```

**Step 3.2 — Fix `api_auth.php` handleSignup()**
- INSERT new user into the `users` table
- Hash password with `password_hash($password, PASSWORD_BCRYPT)`

**Step 3.3 — Fix `api_auth.php` handleLogin()**
- Query `users` table by email
- Verify password with `password_verify($password, $row['password'])`
- Remove the hardcoded credential blocks

**Step 3.4 — Create admin user in the database**
```sql
INSERT INTO users (first_name, last_name, email, password, role)
VALUES ('Admin', 'LakbayLokal', 'admin@lakbaylokal.com', 
        '$2y$10$...', -- bcrypt hash of your chosen admin password
        'admin');
```

---

### Phase 4 — Fix Booking System (Day 2–3) ⭐ Most Important

**Step 4.1 — Create `models/BookingModel.php`**

```php
function createBooking($conn, array $data): int {
    $stmt = $conn->prepare("INSERT INTO bookings (reference, user_id, hotel_id, destination_id, guest_name, guest_email, checkin_date, checkout_date, nights, rooms, guests_count, hotel_subtotal, activities_total, tax_amount, total_price, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')");
    // bind all params
    $stmt->execute();
    return $conn->insert_id;
}
```

**Step 4.2 — Create `models/PaymentModel.php`**

```php
function createPayment($conn, int $bookingId, string $method, int $amount): bool {
    $stmt = $conn->prepare("INSERT INTO payments (booking_id, method, status, amount) VALUES (?, ?, 'pending', ?)");
    $stmt->bind_param("isi", $bookingId, $method, $amount);
    return $stmt->execute();
}
```

**Step 4.3 — Fix `booking_confirm.php`**
- Add CSRF token verification
- Recalculate `$total` on the server (never trust POST price)
- Call `createBooking()` and `createPayment()`
- Remove the `sessionStorage` JavaScript (bookings are now in the database)
- Display confirmation using data from the database, not POST variables

**Step 4.4 — Create `booking_activities` records**
- Parse `selected_acts` JSON from POST
- For each activity, INSERT into `booking_activities`

---

### Phase 5 — Admin CRUD (Day 3–4)

**Step 5.1 — Fix admin authentication redirect**
- Change `header("Location: ../login.php")` → `header("Location: /index.php")`

**Step 5.2 — Admin Dashboard**
- Replace all hardcoded numbers with real SQL COUNT/SUM queries

**Step 5.3 — Destinations CRUD**
- Build the destinations management table + add/edit/delete forms

**Step 5.4 — Hotels CRUD**
- Build hotel management (linked to destinations via destination_id)

**Step 5.5 — Bookings View**
- Show all bookings with guest info, hotel, status, total
- Add status change buttons (confirm / cancel)

**Step 5.6 — Activities CRUD**
- Add/edit/delete activities per destination

---

### Phase 6 — Discount Codes (Day 4)

**Step 6.1 — Add discount code input to booking form**
```html
<input type="text" name="discount_code" placeholder="Enter promo code">
<button type="button" onclick="applyDiscount()">Apply</button>
```

**Step 6.2 — Create discount validation endpoint**

```php
// api_discount.php
$code = $_POST['code'] ?? '';
$stmt = $conn->prepare("SELECT * FROM discount_codes WHERE code = ? AND is_active = 1 AND (valid_until IS NULL OR valid_until >= CURDATE())");
$stmt->bind_param("s", $code);
$stmt->execute();
$discount = $stmt->get_result()->fetch_assoc();

if ($discount) {
    echo json_encode(['valid' => true, 'type' => $discount['type'], 'value' => $discount['value']]);
} else {
    echo json_encode(['valid' => false, 'message' => 'Invalid or expired code']);
}
```

**Step 6.3 — Apply discount in booking total calculation**
- On server side in `booking_confirm.php`, look up the discount code and apply it before saving the booking

**Step 6.4 — Admin Discounts CRUD**
- Add/edit/delete discount codes from `admin/pages/discounts.php`
- Track `uses_count` (increment on every use)

---

### Phase 7 — My Trips Page (Day 4–5)

Replace the current `sessionStorage` approach with a real database query:

```php
// In a new my-trips.php page (or AJAX endpoint):
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$userId = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT b.*, h.name as hotel_name, d.name as dest_name FROM bookings b JOIN hotels h ON b.hotel_id = h.id JOIN destinations d ON b.destination_id = d.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($bookings);
```

---

### Quick Priority Summary

| Priority | What | Impact |
|----------|------|--------|
| 1 | Set up database + seed data | Removes data.php dependency |
| 2 | Fix booking_confirm.php to save to DB | Core feature works |
| 3 | Fix authentication to use DB | Real users, real sessions |
| 4 | Fix admin redirect + show real data | Admin usable |
| 5 | Admin CRUD for destinations/hotels | Content manageable |
| 6 | Discount codes | Bonus feature |
| 7 | My Trips from DB | Polish |

---

## FINAL CHECKLIST BEFORE SUBMISSION

- [ ] All bookings saved to `bookings` table in MySQL
- [ ] Prices recalculated on the server in `booking_confirm.php`
- [ ] Passwords hashed with `password_hash()` in the database
- [ ] No hardcoded credentials in `api_auth.php`
- [ ] `config/db.php` is in `.gitignore`
- [ ] Admin panel shows real data from MySQL
- [ ] Admin can CRUD destinations, hotels, activities
- [ ] CSRF tokens on all forms
- [ ] No raw card details stored or forwarded
- [ ] `components/payment.php` either created or the dead form removed
- [ ] `login.php` created or admin redirect fixed
- [ ] All JavaScript moved out of `hotel.php` into `assets/js/booking.js`

---

*Generated by analysis of the full LakbayLokal codebase — June 2026*
