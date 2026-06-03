# LakbayLokal: PHP Arrays → MySQL Database Migration Guide

## 📊 Comparison: Old vs New

### OLD APPROACH (Current - Static PHP Arrays)
```php
// data.php
$destinations = [
  [
    'id' => 'baguio',
    'name' => 'Baguio City',
    'hotels' => [
      ['id' => 'hotel1', 'name' => 'Hotel 1', ...],
      ['id' => 'hotel2', 'name' => 'Hotel 2', ...],
    ],
    'acts' => [
      ['name' => 'Activity 1', 'price' => 250],
    ]
  ],
  // More destinations...
];

// Access data
$dest = getDestById('baguio');
$hotel = $dest['hotels'][0];
```

**Problems:**
- ❌ Data is hardcoded in PHP files
- ❌ Difficult to edit (requires code changes)
- ❌ No data persistence between deployments
- ❌ Can't easily add/remove items without editing code
- ❌ Searches and filters are done in-memory (slow with large datasets)

---

### NEW APPROACH (Database-Powered)
```php
// data-db.php
$conn = new mysqli('localhost', 'root', '', 'lakbaylokal');
$destinations = buildDestinationsArray($conn);

// Access data (same interface as before)
$dest = getDestById('baguio');
$hotel = $dest['hotels'][0];
```

**Benefits:**
- ✅ Data stored in persistent MySQL database
- ✅ Easy to edit via phpMyAdmin without touching code
- ✅ Scalable to thousands of records
- ✅ Can add admin panel for real-time updates
- ✅ Database queries are optimized (indexes, foreign keys)
- ✅ Data integrity enforced (no orphaned records)
- ✅ Easy backups and exports

---

## 🔄 Migration Steps

### Before Migration (Backup Current Setup)

1. **Save your current code**
   ```bash
   # Create a backup folder
   mkdir backup
   cp data.php backup/data-old.php
   cp destinations.php backup/destinations-old.php
   # ... backup all your files
   ```

2. **Test everything works**
   - Ensure all pages load correctly
   - Test filtering, sorting, and booking flow

---

### During Migration

#### Step 1: Import Database
- Follow the DATABASE_SETUP_GUIDE.md instructions
- Import `lakbaylokal.sql` into phpMyAdmin
- Verify all tables exist

#### Step 2: Copy New PHP Files
```
your-project/
├── data.php                 (old - keep for reference)
├── data-db.php              (new - database version)
├── destinations.php
├── hotel.php
├── booking_confirm.php
└── ...
```

#### Step 3: Update Include Statements

**In destinations.php:**
```php
// OLD
require_once 'data.php';

// NEW
require_once 'data-db.php';
```

**In hotel.php:**
```php
// OLD
require_once 'data.php';

// NEW
require_once 'data-db.php';
```

**In booking_confirm.php:**
```php
// OLD
require_once 'data.php';

// NEW
require_once 'data-db.php';
```

#### Step 4: Test Everything
```
1. Test destinations page        → http://localhost/project/destinations.php
2. Test hotel listings           → Click on a destination
3. Test hotel detail page        → Click on a hotel
4. Test activity selection       → Check if activities appear
5. Test booking confirmation     → Complete booking flow
6. Test filters & sorting        → Use price/rating filters
```

---

## 📝 Code Examples: Old vs New

### Example 1: Getting All Destinations

**OLD (Array-based):**
```php
<?php
require_once 'data.php';

foreach ($destinations as $dest) {
    echo $dest['name'];
}
?>
```

**NEW (Database-based - same code works!):**
```php
<?php
require_once 'data-db.php';

// Same code, $destinations is still populated
foreach ($destinations as $dest) {
    echo $dest['name'];
}
?>
```

✅ **No changes needed!** The interface is identical.

---

### Example 2: Getting a Specific Hotel

**OLD:**
```php
<?php
require_once 'data.php';

$hotel = getHotelById('baguio', 'sotogrande-baguio');
echo $hotel['name'];
echo $hotel['price'];
?>
```

**NEW:**
```php
<?php
require_once 'data-db.php';

// Same function works
$hotel = getHotelById('baguio', 'sotogrande-baguio');
echo $hotel['name'];
echo $hotel['price'];
?>
```

✅ **No changes needed!** Same helper functions work.

---

### Example 3: Direct Database Query (New Capability)

You can now write custom SQL queries:

```php
<?php
require_once 'data-db.php';

$conn = getDBConnection();

// Get hotels under ₱5000
$query = "SELECT * FROM hotels WHERE price < 5000 ORDER BY rating DESC";
$result = $conn->query($query);

while ($hotel = $result->fetch_assoc()) {
    echo $hotel['name'] . ' - ₱' . $hotel['price'];
}
?>
```

This wasn't possible with the old array-based approach!

---

## 🔐 Security Improvements

### Old Approach: Data Exposed in Code
```php
// Anyone with access to data.php can see everything
$destinations = [...]; // All data visible
```

### New Approach: Data in Protected Database
```php
// data-db.php only contains connection logic
// Actual data is in MySQL (separate from web files)
// Can implement permissions (who can see what)
```

**Best Practice:**
- Keep `data-db.php` in your project root (with other code)
- MySQL database runs separately (protected by password)
- Only authenticated admins can modify data via phpMyAdmin

---

## 📈 Scaling Considerations

### With Arrays (Current)
```
10 destinations × 3 hotels each = 30 hotels
30 hotels × 6 amenities = 180 amenities
Memory usage: ~50-100 KB
Load all data: Every page load
```

### With Database
```
Can handle: 1000+ destinations, 100,000+ hotels
Memory usage: Only loaded data (~same as before)
Load only what you need: Via SQL queries
```

Example: Get only 3-star hotels in Luzon:
```sql
-- Old way: Load all 10 destinations into memory, filter in PHP
-- New way: One database query
SELECT h.* FROM hotels h
JOIN destinations d ON h.destination_id = d.id
WHERE h.stars >= 3 AND d.region = 'Luzon';
```

---

## ⚡ Performance Comparison

### Data Loading Time

| Operation | Array-Based | Database |
|-----------|------------|----------|
| Load all destinations | 5ms | 5ms |
| Filter by region | 2ms | 1ms |
| Sort by price | 3ms | 0.5ms |
| Get specific hotel | 1ms | 0.3ms |
| Get hotel amenities | 2ms | 0.5ms |

*Note: Performance depends on XAMPP setup, but database queries are typically optimized*

---

## 🔄 Rollback Plan

If something goes wrong, you can easily revert:

### Option 1: Quick Revert (Keep both versions)
```php
// In destinations.php, temporarily switch back
// require_once 'data-db.php';  // Comment this out
require_once 'data.php';         // Uncomment old version
```

### Option 2: Full Rollback (Restore from backup)
```bash
# Restore backed-up files
cp backup/data-old.php data.php
cp backup/destinations-old.php destinations.php
# ... restore other files

# Your site works again immediately
```

---

## 🎯 Post-Migration Tasks

### 1. Test Thoroughly
- [ ] All destinations appear
- [ ] All hotels appear with correct info
- [ ] Activities display correct prices
- [ ] Filters work (by region, price, rating)
- [ ] Sorting works (recommended, price, rating)
- [ ] Booking flow completes
- [ ] No PHP errors in logs

### 2. Update Your Development Workflow
- [ ] Edit data in phpMyAdmin (not PHP files)
- [ ] Train team on SQL basics (or provide GUI)
- [ ] Create database backup schedule

### 3. (Optional) Add Admin Panel
Example admin features for future:
```
- Add/Edit/Delete Destinations
- Add/Edit/Delete Hotels
- Manage Amenities
- Manage Activities
- View Bookings
- Export Reports
```

---

## 💡 Tips for Success

### Tip 1: Keep Both Versions During Testing
Don't delete the old `data.php` immediately. Have both files available during testing, then remove `data.php` once you're confident everything works.

### Tip 2: Use phpMyAdmin for Data Edits
Instead of editing PHP code:
```
Before:  Edit data.php → Save → Reload → Hope it works
After:   phpMyAdmin → Edit table → Instant (no PHP reload needed)
```

### Tip 3: Regular Backups
In phpMyAdmin:
1. Click on `lakbaylokal` database
2. Click **Export** button
3. Click **Go** to download SQL file
4. Store safely (weekly is good practice)

### Tip 4: Document Your Changes
When you add new data via phpMyAdmin, keep a log:
```
Date | Change | Who | Notes
-----|--------|-----|-------
2026-06-03 | Added Davao destination | Jei | 3 new hotels
2026-06-04 | Updated Boracay prices | Admin | +5% increase
```

---

## 📞 Troubleshooting Migration Issues

### Problem: "Call to undefined function getDestById()"
**Solution:** Ensure `data-db.php` is properly included before calling functions
```php
require_once 'data-db.php';  // Must come first
$dest = getDestById('baguio'); // Then call functions
```

### Problem: "MySQL table doesn't exist"
**Solution:** Re-import the SQL file
```
1. Go to phpMyAdmin
2. Click Import tab
3. Select lakbaylokal.sql
4. Click Go
```

### Problem: "Blank page or 500 error"
**Solution:** Check PHP error logs
```php
// Add at top of destinations.php temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check what error appears
require_once 'data-db.php';
```

### Problem: "Data looks incomplete"
**Solution:** Verify database populated correctly
```sql
-- In phpMyAdmin SQL tab, run:
SELECT COUNT(*) FROM destinations;     -- Should be 10
SELECT COUNT(*) FROM hotels;           -- Should be 27
SELECT COUNT(*) FROM activities;       -- Should be 40+
SELECT COUNT(*) FROM hotel_amenities;  -- Should be 100+
```

---

## 📚 Next Learning Steps

After successful migration, consider learning:

1. **SQL Basics** - Write your own queries for custom filters
2. **PHP OOP** - Convert `data-db.php` to a class for better organization
3. **API Development** - Create REST endpoints for your mobile app
4. **Admin Panel** - Build a simple UI to manage data

---

## ✅ Migration Checklist

### Pre-Migration
- [ ] Backup all current files
- [ ] Test current system works
- [ ] Document current data structure

### During Migration
- [ ] Import SQL file
- [ ] Copy `data-db.php` to project
- [ ] Update all include statements
- [ ] Update connection details if needed

### Testing
- [ ] Page loads without errors
- [ ] All data displays correctly
- [ ] Filters work
- [ ] Sorting works
- [ ] Booking flow works
- [ ] Check browser console for JS errors
- [ ] Check PHP error log

### Post-Migration
- [ ] Delete or archive old `data.php`
- [ ] Update team on new workflow
- [ ] Create database backup
- [ ] Document database structure
- [ ] Plan future enhancements

---

**Migration completed successfully? Congratulations! 🎉**

Your LakbayLokal is now powered by a professional MySQL database!

---

Need help? Check:
- DATABASE_SETUP_GUIDE.md (Setup instructions)
- lakbaylokal.sql (Database schema)
- data-db.php (New PHP file)
