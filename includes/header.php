<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$isLoggedIn = isset($_SESSION['user']);
$userName = $_SESSION['user']['name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'LakbayLokal — Explore the Philippines') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/hotel.css">
  <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/auth.css">
</head>

<body>

  <nav>
    <div class="nav-logo" onclick="location.href='<?= $rootPath ?? '' ?>index.php'">Lakbay<span>Lokal</span></div>
    <ul class="nav-links">

      <li><a href="<?= $rootPath ?? '' ?>index.php"
          class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">
          Home
        </a></li>

      <li><a href="<?= $rootPath ?? '' ?>destinations.php"
          class="<?= ($activePage ?? '') === 'destinations' ? 'active' : '' ?>">
          Destinations
        </a></li>

      <li><a href="<?= $rootPath ?? '' ?>index.php#about"
          class="<?= ($activePage ?? '') === 'about' ? 'active' : '' ?>">
          About
        </a></li>

      <li><a href="<?= $rootPath ?? '' ?>index.php#mytrips"
          class="<?= ($activePage ?? '') === 'mytrips' ? 'active' : '' ?>">
          My Trips
        </a></li>

      <!-- 🔥 ADMIN ONLY DASHBOARD -->
      <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin'): ?>
        <li>
          <a href="<?= $rootPath ?? '' ?>admin/index.php"
            class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
            Dashboard
          </a>
        </li>
      <?php endif; ?>

    </ul>
    <div class="nav-actions">

      <span id="navUserName" class="nav-user" style="display: <?= $isLoggedIn ? 'block' : 'none'; ?>">
        👤 <?= htmlspecialchars($userName) ?>
      </span>
      <button id="navLogoutBtn" class="nav-ghost" onclick="logoutUser()" style="display: <?= $isLoggedIn ? 'block' : 'none'; ?>">
        Logout
      </button>
      <button id="navLoginBtn" class="nav-ghost" onclick="openAuthModal('login')" style="display: <?= $isLoggedIn ? 'none' : 'block'; ?>">
        Login
      </button>
      <button id="navSignupBtn" class="nav-cta" onclick="openAuthModal('signup')" style="display: <?= $isLoggedIn ? 'none' : 'block'; ?>">
        Sign Up
      </button>

    </div>
    <button class="hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </nav>

  <div class="mobile-menu" id="mobileMenu">

    <a href="index.php">Home</a>
    <a href="destinations.php">Destinations</a>
    <a href="index.php#about">About</a>
    <a href="index.php#mytrips">My Trips</a>

    <!-- Mobile menu buttons; JavaScript controls visibility -->
    <a id="mobileLogoutLink" onclick="logoutUser()" style="display: <?= $isLoggedIn ? 'block' : 'none'; ?>">Logout</a>
    <a id="mobileLoginLink" onclick="openAuthModal('login')" style="display: <?= $isLoggedIn ? 'none' : 'block'; ?>">Login</a>
    <a id="mobileSignupLink" onclick="openAuthModal('signup')" style="display: <?= $isLoggedIn ? 'none' : 'block'; ?>">Sign Up</a>

  </div>

  <!-- Auth Modal -->
  <div class="auth-modal-overlay" id="authModal" onclick="if(event.target===this) closeAuthModal()">
    <div class="auth-modal">
      <!-- Login Tab -->
      <div class="auth-tab" id="authLogin">
        <button class="auth-modal-close" onclick="closeAuthModal()">✕</button>
        <div class="auth-kicker">Welcome back</div>
        <h2>Login to LakbayLokal</h2>
        <form class="auth-form" onsubmit="handleLogin(event)" novalidate>
          <div class="form-group">
            <label>Email</label>
            <input type="email" id="loginEmail" placeholder="juan@email.com" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" id="loginPassword" placeholder="••••••••" required>
          </div>
          <button class="btn-primary auth-submit" type="submit">Login</button>
        </form>
        <div class="auth-switch">No account yet? <button type="button" onclick="switchAuthTab('signup')">Create one</button></div>
      </div>
      <!-- Signup Tab -->
      <div class="auth-tab" id="authSignup" style="display:none;">
        <button class="auth-modal-close" onclick="closeAuthModal()">✕</button>
        <div class="auth-kicker">Create account</div>
        <h2>Sign Up</h2>
        <form class="auth-form" onsubmit="handleSignup(event)" novalidate>
          <div class="auth-grid">
            <div class="form-group">
              <label>First Name</label>
              <input type="text" id="signupFName" required>
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" id="signupLName" required>
            </div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" id="signupEmail" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" id="signupPassword" required>
          </div>
          <button class="btn-primary auth-submit" type="submit">Create Account</button>
        </form>
        <div class="auth-switch">Already have an account? <button type="button" onclick="switchAuthTab('login')">Login</button></div>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>