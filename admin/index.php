<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LakbayLokal | Admin Panel</title>
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
  <div class="admin-sidebar">
    <div class="sidebar-brand">
      <h3>Lakbay<span>Lokal</span></h3>
    </div>
    <ul class="sidebar-menu">
      <li><a href="?page=dashboard" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">📊 Dashboard</a></li>
      <li><a href="?page=destinations" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'destinations') ? 'active' : ''; ?>">🗺️ Destinations</a></li>
      <li><a href="?page=hotels" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'hotels') ? 'active' : ''; ?>">🏨 Hotels</a></li>
      <li><a href="?page=itineraries" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'itineraries') ? 'active' : ''; ?>">🎯 Itineraries</a></li>
      <li><a href="?page=bookings" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'bookings') ? 'active' : ''; ?>">🧾 Bookings List</a></li>
      <li style="margin-top: auto;"><a href="../index.php" style="color: #E8905D;">🚪 Exit Admin</a></li>
    </ul>
  </div>

  <main class="main-content">
    <div class="page" id="page-admin">
      
      <?php
      // 1. Alamin kung anong page ang pinindot. Kung wala, dashboard ang default.
      $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

      // 2. Dito magpapalit-palit ang view depende sa variable na $page
      switch ($page) {
          
          case 'dashboard':
              ?>
              <div class="dash-header">
                <h1>Admin Dashboard</h1>
                <p>Welcome back! Here's an overview of the platform's performance.</p>
              </div>

              <div class="dash-stats">
                <div class="dash-stat">
                  <div class="dash-stat-num" id="adminTotalBookings">0</div>
                  <div class="dash-stat-label">Global Bookings</div>
                </div>
                <div class="dash-stat">
                  <div class="dash-stat-num" id="adminTotalRevenue">₱0</div>
                  <div class="dash-stat-label">Total Revenue</div>
                </div>
                <div class="dash-stat">
                  <div class="dash-stat-num">12</div>
                  <div class="dash-stat-label">Active Destinations</div>
                </div>
              </div>

              <div class="dash-content">
                <h2 class="dash-bookings-title">Recent Updates</h2>
                <div id="adminBookingsList">
                  <div style="text-align:center;padding:3rem;color:var(--muted);">
                    <p>Dashboard analytics loading...</p>
                  </div>
                </div>
              </div>
              <?php
              break;

          case 'destinations':
              ?>
              <div class="dash-header">
                <h1>🗺️ Manage Destinations</h1>
                <p>Add, edit, or delete platform travel spots.</p>
              </div>
              
              <div class="dash-content">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                    <h2 class="dash-bookings-title">All Travel Packages</h2>
                    <button style="background: var(--primary); color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer;">+ Add Destination</button>
                </div>
                
                <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem; text-align: center; color: var(--muted);">
                    <p>No destinations found in database. Start adding now!</p>
                </div>
              </div>
              <?php
              break;

          case 'hotels':
              ?>
              <div class="dash-header">
                <h1>🏨 Partner Hotels</h1>
                <p>Manage accommodation options for your destinations.</p>
              </div>
              <div class="dash-content">
                <p style="color: var(--muted);">Hotel lists loading...</p>
              </div>
              <?php
              break;

          case 'itineraries':
              ?>
              <div class="dash-header">
                <h1>🎯 Destination Activities</h1>
                <p>Configure addon tours and travel paths for users.</p>
              </div>
              <div class="dash-content">
                <p style="color: var(--muted);">Itineraries layout loading...</p>
              </div>
              <?php
              break;

          case 'bookings':
              ?>
              <div class="dash-header">
                <h1>🧾 Master Booking Records</h1>
                <p>Review, cancel, or approve customer travel bookings globally.</p>
              </div>
              <div class="dash-content">
                <div id="adminBookingsList">
                  <div style="text-align:center;padding:3rem;color:var(--muted);">
                    <p>Loading transaction records from database...</p>
                  </div>
                </div>
              </div>
              <?php
              break;
      }
      ?>

    </div>
  </main>
</body>
</html>