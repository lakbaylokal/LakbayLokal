<?php include_once __DIR__ . '/config.php'; ?>

<nav>
  <div class="nav-logo">Lakbay<span>PH</span></div>

  <ul class="nav-links">

    <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
    <li><a href="<?= BASE_URL ?>/hotels/list.php">Hotels</a></li>
     <li><a href="<?= BASE_URL ?>/destinations.php">Destinations</a></li>
    <li><a href="<?= BASE_URL ?>/itinerary.php">Plan Trip</a></li>

    <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
    <li><a href="<?php echo BASE_URL; ?>/pages/destinations.php">Destinations</a></li>
    <li><a href="<?php echo BASE_URL; ?>/hotels/list.php">Hotels</a></li>
    <li><a href="<?php echo BASE_URL; ?>/pages/itinerary.php">Plan Trip</a></li>

  </ul>

  <div class="nav-actions">

    <?php if (!isset($_SESSION['user'])): ?>
      
      <!-- GUEST -->
      <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn-nav">Log In</a>
      <a href="<?php echo BASE_URL; ?>/auth/signup.php" class="btn-nav filled">Sign Up</a>

    <?php else: ?>

      <!-- LOGGED IN USER -->
      <span style="color:white; margin-right:10px;">
        👤 <?php echo htmlspecialchars($_SESSION['user']); ?>
      </span>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a class="btn-nav" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin</a>
      <?php endif; ?>

      <a class="btn-nav filled" href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a>

    <?php endif; ?>

  </div>
</nav>