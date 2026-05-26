# 🇵🇭 LakbayLokal (LakbayPH)

A Philippine travel web application for exploring destinations, browsing hotels, and planning trips through a simple itinerary system.

This project is currently in **early development stage**. Core structure and UI layout are in place, but several features are still under development.

---

## 📌 Project Status

### 🔴 HIGH PRIORITY (Fixes / Core Functionality Issues)
- Hotel click navigation issue (index.php → hotel details page not fully consistent across all hotel cards)
- Destination system not functional yet (no proper filtering/navigation behavior for all pages)
- Search functionality not working

---

### 🟡 MEDIUM PRIORITY (Main Feature Logic / System Behavior)
- Destination behavior update:
  - Clicking a location (e.g., Baguio) should filter and display only related hotels
  - Other hotels should be hidden based on selected destination
- Plan Trip system:
  - Selecting a place should dynamically update available hotels and activities only
- Hotel system improvement:
  - Some hotels may include sample itineraries (lightweight version only if needed)
- Booking system (not yet implemented)
- Payment system (not yet implemented)
- Database integration ongoing (partially implemented for auth module only)

---

### 🟢 LOW PRIORITY (UI / Design / Structural Improvements)
- Hotel page UI issue (hotel.php CSS needs fixing)
  - Use `hotel2.php` design as reference
- Navbar / UI consistency improvements across pages
- Modular file structure already implemented
- Admin dashboard UI placeholder only
- Basic frontend UI (Landing Page, Destinations, Hotels listing)
- Footer and navigation components already separated

