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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/hotel.css">
  <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/auth.css">
</head>

<body>

  <!-- ======= BOOTSTRAP NAVBAR ======= -->
  <nav class="navbar navbar-expand-lg fixed-top lbl-navbar">
    <div class="container-fluid px-4 px-md-5">

      <!-- Brand / Logo -->
      <a class="navbar-brand lbl-nav-logo" href="<?= $rootPath ?? '' ?>index.php">
        Lakbay<span>Lokal</span>
      </a>

      <!-- Mobile Toggler -->
      <button class="navbar-toggler lbl-toggler border-0" type="button"
              data-bs-toggle="collapse" data-bs-target="#navbarMain"
              aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="lbl-toggler-bar"></span>
        <span class="lbl-toggler-bar"></span>
        <span class="lbl-toggler-bar"></span>
      </button>

      <!-- Collapsible content -->
      <div class="collapse navbar-collapse" id="navbarMain">

        <!-- Center links -->
        <ul class="navbar-nav mx-auto gap-1">

          <li class="nav-item">
            <a class="nav-link lbl-nav-link <?= ($activePage ?? '') === 'home' ? 'active' : '' ?>"
               href="<?= $rootPath ?? '' ?>index.php">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link lbl-nav-link <?= ($activePage ?? '') === 'destinations' ? 'active' : '' ?>"
               href="<?= $rootPath ?? '' ?>destinations.php">Destinations</a>
          </li>

          <li class="nav-item">
            <a class="nav-link lbl-nav-link <?= ($activePage ?? '') === 'about' ? 'active' : '' ?>"
               href="<?= $rootPath ?? '' ?>index.php#about">About</a>
          </li>

          <li class="nav-item">
            <a class="nav-link lbl-nav-link <?= ($activePage ?? '') === 'mytrips' ? 'active' : '' ?>"
               href="<?= $rootPath ?? '' ?>index.php#mytrips">My Trips</a>
          </li>

          <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link lbl-nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>"
               href="<?= $rootPath ?? '' ?>admin/index.php">Dashboard</a>
          </li>
          <?php endif; ?>

          <!-- Mobile-only auth (inside collapse) -->
          <li class="nav-item d-lg-none mt-2 pt-2" style="border-top: 1px solid var(--border);">
            <span id="navUserNameMobile" class="d-block px-3 py-1 lbl-nav-user-mobile"
                  style="display: <?= $isLoggedIn ? 'block' : 'none'; ?> !important">
              👤 <?= htmlspecialchars($userName) ?>
            </span>
          </li>
          <li class="nav-item d-lg-none" id="mobileLoginItem"
              style="display: <?= $isLoggedIn ? 'none' : 'list-item'; ?>">
            <a class="nav-link lbl-nav-link" href="#"
               onclick="openAuthModal('login'); return false;">Login</a>
          </li>
          <li class="nav-item d-lg-none" id="mobileSignupItem"
              style="display: <?= $isLoggedIn ? 'none' : 'list-item'; ?>">
            <a class="nav-link lbl-nav-link" href="#"
               onclick="openAuthModal('signup'); return false;">Sign Up</a>
          </li>
          <li class="nav-item d-lg-none" id="mobileLogoutItem"
              style="display: <?= $isLoggedIn ? 'list-item' : 'none'; ?>">
            <a class="nav-link lbl-nav-link" href="#"
               onclick="logoutUser(); return false;">Logout</a>
          </li>

        </ul>

        <!-- Right side: auth buttons (desktop only) -->
        <div class="d-none d-lg-flex align-items-center gap-2">
          <span id="navUserName" class="lbl-nav-user"
                style="display: <?= $isLoggedIn ? 'inline-block' : 'none'; ?>">
            👤 <?= htmlspecialchars($userName) ?>
          </span>
          <button id="navLogoutBtn" class="lbl-btn-ghost"
                  onclick="logoutUser()"
                  style="display: <?= $isLoggedIn ? 'inline-flex' : 'none'; ?>">
            Logout
          </button>
          <button id="navLoginBtn" class="lbl-btn-ghost"
                  onclick="openAuthModal('login')"
                  style="display: <?= $isLoggedIn ? 'none' : 'inline-flex'; ?>">
            Login
          </button>
          <button id="navSignupBtn" class="lbl-btn-primary"
                  onclick="openAuthModal('signup')"
                  style="display: <?= $isLoggedIn ? 'none' : 'inline-flex'; ?>">
            Sign Up
          </button>
        </div>

      </div><!-- /collapse -->
    </div><!-- /container -->
  </nav>

  <!-- ======= NAVBAR STYLES ======= -->
  <style>
  .lbl-navbar {
    background: rgba(251, 247, 240, 0.96);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
    height: 68px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .lbl-nav-logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--primary) !important;
    letter-spacing: -0.5px;
    text-decoration: none;
    flex-shrink: 0;
  }
  .lbl-nav-logo span { color: var(--accent); }
  .lbl-nav-logo:hover { color: var(--primary-light) !important; }
  .lbl-nav-link {
    color: var(--muted) !important;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 0.42rem 0.85rem !important;
    border-radius: 50px;
    transition: color 0.2s, background 0.2s;
    white-space: nowrap;
  }
  .lbl-nav-link:hover { color: var(--primary) !important; background: var(--primary-pale); }
  .lbl-nav-link.active { color: var(--primary) !important; font-weight: 600; background: var(--primary-pale); }
  .lbl-nav-user {
    color: var(--accent);
    font-size: 0.85rem;
    font-weight: 700;
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .lbl-nav-user-mobile {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--accent);
  }
  .lbl-btn-ghost {
    background: transparent;
    color: var(--deep);
    border: 1.5px solid var(--border);
    border-radius: 50px;
    padding: 0.46rem 1.1rem;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: 'DM Sans', sans-serif;
    display: inline-flex;
    align-items: center;
  }
  .lbl-btn-ghost:hover { border-color: var(--primary); color: var(--primary); }
  .lbl-btn-primary {
    background: var(--primary);
    color: white !important;
    border: none;
    border-radius: 50px;
    padding: 0.5rem 1.3rem;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    font-family: 'DM Sans', sans-serif;
    display: inline-flex;
    align-items: center;
  }
  .lbl-btn-primary:hover { background: var(--primary-light); transform: scale(1.02); }
  .lbl-toggler { padding: 4px 6px; }
  .lbl-toggler:focus { box-shadow: none; }
  .lbl-toggler-bar {
    display: block;
    width: 22px;
    height: 2px;
    background: var(--deep);
    border-radius: 2px;
    margin: 5px 0;
    transition: all 0.3s;
  }
  @media (max-width: 991px) {
    .lbl-navbar { height: auto; min-height: 68px; }
    .navbar-collapse {
      background: rgba(251,247,240,0.98);
      border-top: 1px solid var(--border);
      padding: 1rem 0.5rem 1.2rem;
    }
    .lbl-nav-link { padding: 0.55rem 1rem !important; border-radius: 10px; }
  }
  /* Override old nav rule so body top padding stays right */
  nav:not(.navbar) { display: none !important; }
  </style>

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
            <small class="validation-error" id="loginEmailError"></small>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" id="loginPassword" placeholder="••••••••" required>
            <small class="validation-error" id="loginPasswordError"></small>
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
              <small class="validation-error" id="signupFNameError"></small>
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" id="signupLName" required>
              <small class="validation-error" id="signupLNameError"></small>
            </div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" id="signupEmail" required>
            <small class="validation-error" id="signupEmailError"></small>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" id="signupPassword" required>
            <small class="validation-error" id="signupPasswordError"></small>
          </div>
          <button class="btn-primary auth-submit" type="submit">Create Account</button>
        </form>
        <div class="auth-switch">Already have an account? <button type="button" onclick="switchAuthTab('login')">Login</button></div>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>