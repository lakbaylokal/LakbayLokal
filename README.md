# LakbayLokal

A premium, minimalist Philippine travel web application designed as a Single Page Application (SPA). It allows users to explore curated local destinations, customize hotel stays, and build trip itineraries in one seamless, unified flow.

This project is currently in its **core UI & frontend logic stage**, utilizing a modular vanilla PHP and JavaScript architecture.

---

## 📂 Project Structure

The application follows a clean, modular structure separating the customer experience from the administrative panel:

```text
lakbaylokal/ (ROOT FOLDER)
├── admin/               # Administrative Control Panel (CRUD)
│   ├── assets/          # Admin-specific styles & scripts
│   │   └── style.css    # Premium Admin Dashboard Custom Theme
│   └── index.php        # Dynamic Admin Dashboard (via PHP Switch-Case)
├── assets/              # Global Frontend Assets
│   ├── script.js        # Core SPA Navigation & Interactive Booking Logic
│   └── style.css        # Premium Earth-toned UI Theme
├── components/          # Customer-facing Views (SPA Components)
│   ├── about.php
│   ├── confirm.php      # Dynamic Booking Receipt/Success Screen
│   ├── dashboard.php    # User's "My Trips" Dashboard & History
│   ├── destinations.php # Complete Travel Packages Grid
│   ├── detail.php       # Interactive Booking Workspace (Hotel & Activity Selection)
│   └── home.php         # Landing Page with Hero and Search Modules
├── includes/            # Global UI Layout Blocks
│   ├── header.php
│   └── footer.php
├── data.php             # Central Mock Data Store (Destinations, Hotels, Itineraries)
└── index.php            # Master Application Constructor (SPA Router)
```
---

## 📌 Project Status & Development Roadmap

### 🔴 PHASE 1: HIGH PRIORITY (Authentication & Core Feature Flows - No Database)
- **Session-less Authentication UI:** Implement Frontend login, logout, and sign-up interfaces (including a dedicated route/toggle gateway for the Admin account).
- **Booking to Payment Flow:** Connect the `Confirm Booking →` button inside the `detail.php` sidebar to trigger a Simulated Payment Screen before officially routing to `confirm.php` (Receipt).
- **Search Engine Fix:** Activate `doSearch()` inside `home.php` to filter destination cards based on chosen location, budget bracket, and check-in date criteria.

---

### 🟡 PHASE 2: MEDIUM PRIORITY (Database Integration & SQL Execution)
- **Relational Database Migration:** Move from static `data.php` arrays to a relational MySQL/SQLite database structure mapping out Destinations, Hotels, Activities, Users, and Bookings.
- **SQL-Driven Customer Operations:** Connect user inputs (Sign-up, Bookings, Search) to perform `INSERT INTO` and `SELECT FROM` statements dynamically.
- **Persistent Booking Logs:** Ensure that successful checkouts write a permanent record to the database, carrying complete customer info, chosen hotels, dynamic dates, and calculated totals.

---

### 🟢 PHASE 3: LOW PRIORITY (Internal Database UI / Admin CRUD Panel)
- **Internal Database UI (Admin Panel):** Build a stylized administrative interface to act as a direct window to the database (Visual Tables for non-technical admins).
- **Admin CRUD Control Suite:** Empower the admin account to run SQL-backed forms for:
  - **Create:** Add fresh Destinations, Hotels, and Itinerary packages.
  - **Read:** View, filter, and track live database transaction logs.
  - **Update:** Edit existing holiday costs, titles, and item availability.
  - **Delete:** Safely wipe obsolete packages or records from the tables.
- **Admin Booking Search Engine:** Integrate an internal search and filter bar inside the Admin Booking list, executing dynamic `LIKE %criteria%` SQL statements to locate specific user names, emails, or booking codes instantly.
