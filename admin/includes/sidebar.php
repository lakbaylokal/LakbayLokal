<?php
// admin/includes/sidebar.php — Shared Sidebar Navigation
// $activePage must be set before including this file
$activePage = $activePage ?? '';
?>
<!-- <header>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
</header> -->
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
        <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
      </a>
    </div>

    <div class="adm-nav-group">
      <div class="adm-nav-label">Management</div>
      <a href="manage-bookings.php" class="adm-nav-link <?= $activePage==='bookings'?'active':'' ?>">
        <i class="bi bi-receipt nav-icon"></i> Bookings
      </a>
      <a href="manage-bookings.php?view=calendar" class="adm-nav-link <?= $activePage==='calendar'?'active':'' ?>">
        <i class="bi bi-calendar nav-icon"></i> Calendar View
      </a>
      <a href="manage-users.php" class="adm-nav-link <?= $activePage==='users'?'active':'' ?>">
        <i class="bi bi-people nav-icon"></i> Users
      </a>
    </div>

    <div class="adm-nav-group">
      <div class="adm-nav-label">Content</div>
      <a href="manage-destinations.php" class="adm-nav-link <?= $activePage==='destinations'?'active':'' ?>">
        <i class="bi bi-globe nav-icon"></i> Destinations
      </a>
      <a href="manage-hotels.php" class="adm-nav-link <?= $activePage==='hotels'?'active':'' ?>">
        <i class="bi bi-building nav-icon"></i> Hotels
      </a>
      <a href="manage-activities.php" class="adm-nav-link <?= $activePage==='activities'?'active':'' ?>">
        <i class="bi bi-star nav-icon"></i> Activities
      </a>
    </div>
  </nav>

  <div class="adm-sidebar-footer">
    <a href="../index.php" class="adm-exit-link"><i class="bi bi-box-arrow-right"></i> Exit Admin</a>
  </div>
</aside>