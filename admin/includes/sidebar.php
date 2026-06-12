<?php
// admin/includes/sidebar.php — Shared Sidebar Navigation
// $activePage must be set before including this file
$activePage = $activePage ?? '';
?>
<aside class="adm-sidebar">
  <div class="adm-sidebar-brand">
    <span class="brand-logo">✈️</span>
    <div>
      <div class="brand-name">Lakbay<span>Lokal</span></div>
      <div class="brand-sub">Admin Panel</div>
    </div>
  </div>

  <nav class="adm-nav">
    <div class="adm-nav-group">
      <div class="adm-nav-label">Overview</div>
      <a href="index.php" class="adm-nav-link <?= $activePage==='dashboard'?'active':'' ?>">
        <span class="nav-icon">📊</span> Dashboard
      </a>
    </div>

    <div class="adm-nav-group">
      <div class="adm-nav-label">Management</div>
      <a href="manage-bookings.php" class="adm-nav-link <?= $activePage==='bookings'?'active':'' ?>">
        <span class="nav-icon">🧾</span> Bookings
      </a>
      <a href="manage-bookings.php?view=calendar" class="adm-nav-link <?= $activePage==='calendar'?'active':'' ?>">
        <span class="nav-icon">📅</span> Calendar View
      </a>
      <a href="manage-users.php" class="adm-nav-link <?= $activePage==='users'?'active':'' ?>">
        <span class="nav-icon">👥</span> Users
      </a>
    </div>

    <div class="adm-nav-group">
      <div class="adm-nav-label">Content</div>
      <a href="manage-destinations.php" class="adm-nav-link <?= $activePage==='destinations'?'active':'' ?>">
        <span class="nav-icon">🗺️</span> Destinations
      </a>
      <a href="manage-hotels.php" class="adm-nav-link <?= $activePage==='hotels'?'active':'' ?>">
        <span class="nav-icon">🏨</span> Hotels
      </a>
      <a href="manage-activities.php" class="adm-nav-link <?= $activePage==='activities'?'active':'' ?>">
        <span class="nav-icon">🎯</span> Activities
      </a>
    </div>
  </nav>

  <div class="adm-sidebar-footer">
    <a href="../index.php" class="adm-exit-link">🚪 Exit Admin</a>
  </div>
</aside>