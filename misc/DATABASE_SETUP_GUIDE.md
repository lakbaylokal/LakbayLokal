# LakbayLokal Database Setup Guide

## 📦 What You Have

This package contains everything needed to convert your PHP-based data structure into a fully functional MySQL database for XAMPP:

1. **lakbaylokal.sql** - Complete SQL database dump with all tables, data, and relationships
2. **data-db.php** - Database-connected PHP file that replaces your hardcoded `data.php`
3. **This README** - Setup and integration instructions


---
## 🚀 QUICK START (5 minutes)

### Step 1: Import the Database into XAMPP

1. **Open phpMyAdmin**
   - In your browser, go to: `http://localhost/phpmyadmin`
   - Or open XAMPP Control Panel and click "Admin" next to MySQL

2. **Import the SQL File**
   - Click on the **Import** tab (top menu)
   - Click **Choose File** and select `lakbaylokal.sql`
   - Click **Import** button at the bottom
   - You should see: "Import has been successfully finished"

3. **Verify the Database**
   - In the left sidebar, you should now see the `lakbaylokal` database
   - Click it to view the tables:
     - `destinations` (10 destinations)
     - `hotels` (27 hotels)
     - `hotel_amenities` (100+ amenities)
     - `hotel_policies` (50+ policies)
     - `activities` (40+ activities)

---

## 📝 Step 2: Update Your PHP Files

### Option A: Using the Database-Connected File (Recommended)

1. **Copy `data-db.php` to your project**
   - Place it in the same directory as your current `data.php`

2. **Update your files to use the database**
   - In `destinations.php`, replace:
     ```php
     require_once 'data.php';
     ```
     With:
     ```php
     require_once 'data-db.php';
     ```

   - Do the same in any other files that use `require_once 'data.php'`

3. **Test it**
   - Visit `http://localhost/your-project/destinations.php`
   - You should see all destinations and hotels from the database

### Option B: Keep Using data.php (No Changes Needed)

If you prefer not to change your code yet, you can continue using your hardcoded `data.php` while the database is prepared for later use.

---

## 🔧 Step 3: Verify Database Connection

If you get a database connection error, check:

1. **XAMPP is Running**
   - Open XAMPP Control Panel
   - Make sure "MySQL" is running (green status)

2. **Connection Details in `data-db.php`**
   - Edit `data-db.php` and verify these lines:
   ```php
   $db_host = 'localhost';    // Your MySQL host
   $db_user = 'root';         // Your MySQL username
   $db_pass = '';             // Your MySQL password (usually empty in XAMPP)
   $db_name = 'lakbaylokal';  // Database name
   ```

3. **If you changed MySQL credentials**
   - Update the values above to match your XAMPP setup
   - Default XAMPP credentials are usually:
     - Host: `localhost`
     - User: `root`
     - Password: `` (empty)

---

## 📊 Database Schema Overview

### Tables

#### `destinations`
Stores all destination information
```
- id (PRIMARY KEY)
- name
- region
- emoji
- tagline
- description
- price
- price_from
- image_url
- gradient_bg
- created_at, updated_at
```

#### `hotels`
Stores all hotel information
```
- id (PRIMARY KEY)
- destination_id (FOREIGN KEY)
- name
- image_url
- location
- description
- stars
- price
- rating
- reviews_count
- checkin_time
- checkout_time
- created_at, updated_at
```

#### `hotel_amenities`
Stores amenities for each hotel (one-to-many)
```
- id (PRIMARY KEY)
- hotel_id (FOREIGN KEY)
- amenity_name
```

#### `hotel_policies`
Stores policies for each hotel (one-to-many)
```
- id (PRIMARY KEY)
- hotel_id (FOREIGN KEY)
- policy
```

#### `activities`
Stores activities for each destination
```
- id (PRIMARY KEY)
- destination_id (FOREIGN KEY)
- name
- price
```

---

## 🔍 Common Issues & Solutions

### Issue: "Database Connection Failed"
**Solution:**
- Ensure MySQL is running in XAMPP Control Panel
- Check if port 3306 is not blocked
- Verify credentials in `data-db.php`

### Issue: "Table doesn't exist"
**Solution:**
- Re-import the SQL file in phpMyAdmin
- Clear any partial imports by dropping the database first

### Issue: "PHP shows blank page"
**Solution:**
- Check PHP error logs: `error_log` file in your project
- Ensure `data-db.php` is in the correct location
- Verify file permissions (readable by Apache)

### Issue: "Characters not displaying correctly (Tagalog text)"
**Solution:**
- The SQL dump uses UTF-8MB4 by default
- Ensure your HTML has: `<meta charset="UTF-8">`
- Your database should already be set to UTF-8MB4

---

## 💾 Data Management

### Adding New Data via SQL

To add new destinations, hotels, or activities, you can use INSERT statements:

```sql
-- Add a new destination
INSERT INTO destinations 
(id, name, region, emoji, tagline, description, price, price_from) 
VALUES 
('davao', 'Davao City', 'Mindanao', '🍌', 'Fruit Basket of the Philippines', '...description...', 5000, 2500);

-- Add a hotel
INSERT INTO hotels 
(id, destination_id, name, location, description, stars, price, rating, reviews_count, checkin_time, checkout_time) 
VALUES 
('example-hotel', 'davao', 'Example Hotel', 'Downtown Davao', 'A great hotel', 4, 4500, 4.5, 250, '14:00:00', '11:00:00');

-- Add amenities
INSERT INTO hotel_amenities (hotel_id, amenity_name) 
VALUES 
('example-hotel', 'Free WiFi'),
('example-hotel', 'Restaurant');

-- Add activities
INSERT INTO activities (destination_id, name, price) 
VALUES 
('davao', 'Fruit Tour', 500);
```

Use phpMyAdmin's SQL tab to execute these queries.

---

## 🎯 Next Steps

### After Setup

1. **Test all features**
   - Navigate through destinations
   - Click on hotels
   - Filter by price and rating
   - Check if activities display correctly

2. **Update booking system**
   - Once database is working, update `hotel.php` and `booking_confirm.php` if needed
   - They may need to query the database for activities instead of PHP arrays

3. **Add admin panel** (Optional future enhancement)
   - Create an admin page to add/edit destinations, hotels, and activities
   - No longer need to edit SQL files manually

---

## 📞 Support Notes

- **XAMPP localhost:** `http://localhost`
- **phpMyAdmin:** `http://localhost/phpmyadmin`
- **Database location:** Usually at `C:\xampp\mysql\data\lakbaylokal` (Windows)
- **Database backup:** Regularly export your database via phpMyAdmin's Export tab

---

## ✅ Checklist

- [ ] XAMPP MySQL is running
- [ ] `lakbaylokal.sql` imported successfully
- [ ] `data-db.php` placed in project directory
- [ ] Updated include statements in PHP files
- [ ] Database connection verified
- [ ] Destinations page loads without errors
- [ ] Hotels display with correct data
- [ ] Activities show correct prices
- [ ] No PHP warnings or errors

---

**Created:** 2026-06-03  
**Database Name:** lakbaylokal  
**Total Records:** 10 destinations, 27 hotels, 100+ amenities, 40+ activities

Enjoy your database-powered LakbayLokal! 🇵🇭
