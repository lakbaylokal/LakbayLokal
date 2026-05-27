<?php
// index.php — LakbayLokal main entry point
require_once 'data.php';

// Encode PHP destination data for use in JavaScript
$destinationsJson = json_encode($destinations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

include_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LakbayLokal — Explore the Philippines</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- NAV
<nav>
  <div class="nav-logo" onclick="showPage('home')">Lakbay<span>Lokal</span></div>
  <ul class="nav-links">
    <li><a onclick="showPage('home')" id="nav-home" class="active">Home</a></li>
    <li><a onclick="showPage('destinations')" id="nav-destinations">Destinations</a></li>
    <li><a onclick="showPage('about')" id="nav-about">About</a></li>
    <li><a onclick="showPage('dashboard')" id="nav-dashboard">My Trips</a></li>
  </ul>
  <button class="nav-cta" onclick="showPage('destinations')">Book Now</button>
  <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>
<div class="mobile-menu" id="mobileMenu">
  <a onclick="showPage('home');closeMenu()">Home</a>
  <a onclick="showPage('destinations');closeMenu()">Destinations</a>
  <a onclick="showPage('about');closeMenu()">About</a>
  <a onclick="showPage('dashboard');closeMenu()">My Trips</a>
</div>

<!-- TOAST -->
<!-- <div class="toast" id="toast">Booking confirmed! 🎉</div> -->

<!-- ======= HOME PAGE ======= -->
<!-- <div class="page active" id="page-home"> -->
  <!-- <section class="hero">
    <div class="hero-content">
      <span class="hero-tag">🇵🇭 Your Philippine Journey Starts Here</span>
      <h1>Discover the <em>Beauty</em> of the Philippines</h1>
      <p>Handpicked hotel stays paired with unforgettable local activities — all in one seamless booking platform.</p>
      <div class="hero-btns">
        <button class="btn-primary" onclick="showPage('destinations')">
          Explore Destinations
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
        <button class="btn-outline" onclick="showPage('about')">How It Works</button>
      </div>
      <div class="hero-stats">
        <div class="stat-item"><div class="stat-num">8+</div><div class="stat-label">Destinations</div></div>
        <div class="stat-item"><div class="stat-num">30+</div><div class="stat-label">Activities</div></div>
        <div class="stat-item"><div class="stat-num">100%</div><div class="stat-label">Local Picks</div></div>
      </div>
    </div> -->
<!-- 
    <div class="hero-visual">
      <div class="hero-card">
        <div style="height:130px;background:linear-gradient(135deg,#8ECAC0,#2E6B4F);display:flex;align-items:center;justify-content:center;font-size:3.5rem;">🏄</div>
        <div class="hero-card-body">
          <h4>Siargao Island</h4>
          <p>Surf &amp; Island Hop</p>
          <div class="hero-card-price">from ₱3,700</div>
        </div>
      </div>
      <div class="hero-card" style="margin-top:0;align-self:flex-end">
        <div style="height:130px;background:linear-gradient(135deg,#F5D5B0,#C4602A);display:flex;align-items:center;justify-content:center;font-size:3.5rem;">🏔️</div>
        <div class="hero-card-body">
          <h4>Baguio City</h4>
          <p>Cool mountain escape</p>
          <div class="hero-card-price">from ₱4,500</div>
        </div>
      </div>
    </div>
  </section> -->

  <!-- SEARCH -->
  <!-- <div class="search-section">
    <div class="search-group">
      <label>Destination</label>
      <select id="homeSearchDest">
        <option value="">All Destinations</option>
        <?php foreach ($destinations as $d): ?>
          <option><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="search-group">
      <label>Budget</label>
      <select id="homeSearchBudget">
        <option value="">Any Budget</option>
        <option value="low">Under ₱5,000</option>
        <option value="mid">₱5,000 – ₱7,000</option>
        <option value="high">Above ₱7,000</option>
      </select>
    </div>
    <div class="search-group">
      <label>Check-in Date</label>
      <input type="date" id="homeCheckin">
    </div>
    <button class="search-btn" onclick="doSearch()">🔍 Search Packages</button>
  </div> -->

  <!-- FEATURED DESTINATIONS -->
  <!-- <section class="section">
    <div class="section-header">
      <h2 class="section-title">Featured <span>Destinations</span></h2>
      <button class="see-all" onclick="showPage('destinations')">See all →</button>
    </div>
    <div class="dest-grid" id="featuredGrid"></div>
  </section> -->

  <!-- WHY SECTION -->
  <!-- <section class="section" style="background:white;padding-top:3rem;padding-bottom:3rem;">
    <div class="section-header">
      <h2 class="section-title">Why <span>LakbayLokal?</span></h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem;">
      <div style="text-align:center;padding:1.5rem;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">🗺️</div>
        <h3 style="font-weight:700;margin-bottom:0.5rem;">All-in-One Planning</h3>
        <p style="font-size:0.88rem;color:var(--muted);">Hotel + activities bundled in one convenient booking flow.</p>
      </div>
      <div style="text-align:center;padding:1.5rem;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">🤝</div>
        <h3 style="font-weight:700;margin-bottom:0.5rem;">Curated Local Picks</h3>
        <p style="font-size:0.88rem;color:var(--muted);">Handpicked accommodations and authentic experiences.</p>
      </div>
      <div style="text-align:center;padding:1.5rem;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">📱</div>
        <h3 style="font-weight:700;margin-bottom:0.5rem;">Easy Booking</h3>
        <p style="font-size:0.88rem;color:var(--muted);">Book in minutes from any device — desktop or mobile.</p>
      </div>
      <div style="text-align:center;padding:1.5rem;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">💳</div>
        <h3 style="font-weight:700;margin-bottom:0.5rem;">Secure Payments</h3>
        <p style="font-size:0.88rem;color:var(--muted);">Safe online transactions with instant confirmation.</p>
      </div>
    </div>
  </section>
</div> -->

<?php include 'components/home.php'; ?>

<!-- ======= DESTINATIONS PAGE ======= -->
<?php include 'components/destinations.php'; ?>



<!-- ======= DESTINATION DETAIL PAGE ======= -->
<?php include 'components/detail.php'; ?>

<!-- ======= CONFIRMATION PAGE ======= -->
<?php include 'components/confirm.php'; ?>

<!-- ======= DASHBOARD PAGE ======= -->
<?php include 'components/dashboard.php'; ?>

<!-- ======= ABOUT PAGE ======= -->
<?php include 'components/about.php'; ?>

<<<<<<< HEAD
<!-- FOOTER -->
<?php
// 2. Isabit ang Footer at Scripts
include_once 'includes/footer.php';
?>

<!-- Inject PHP destination data into JS -->
<script>
  const DESTINATIONS = <?= $destinationsJson ?>;
=======
    <!-- BAGUIO HOTELS -->
    <div class="hotel-card" data-destination="baguio">
      <div class="hotel-dest">📍 Baguio City</div>
      <div class="hotel-name">Hotel Veniz</div>
      <div class="hotel-stars">★★★☆☆</div>
      <div class="hotel-price">₱1,800 <span>/ night</span></div>
      <a href="hotel.php?id=hotel-veniz" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card" data-destination="baguio">
      <div class="hotel-dest">📍 Baguio City</div>
      <div class="hotel-name">The Manor at Camp John Hay</div>
      <div class="hotel-stars">★★★★★</div>
      <div class="hotel-price">₱8,500 <span>/ night</span></div>
      <a href="hotel.php?id=the-manor" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card" data-destination="baguio">
      <div class="hotel-dest">📍 Baguio City</div>
      <div class="hotel-name">Microtel by Wyndham Baguio</div>
      <div class="hotel-stars">★★★☆☆</div>
      <div class="hotel-price">₱2,500 <span>/ night</span></div>
      <a href="hotel.php?id=microtel-baguio" class="btn-hotel">View Hotel →</a>
    </div>

    <!-- BORACAY HOTELS -->
    <div class="hotel-card" data-destination="boracay">
      <div class="hotel-dest">📍 Boracay</div>
      <div class="hotel-name">Henann Resort Boracay</div>
      <div class="hotel-stars">★★★★★</div>
      <div class="hotel-price">₱12,000 <span>/ night</span></div>
      <a href="hotel.php?id=henann-resort" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card" data-destination="boracay">
      <div class="hotel-dest">📍 Boracay</div>
      <div class="hotel-name">Fairways & Bluewater</div>
      <div class="hotel-stars">★★★★☆</div>
      <div class="hotel-price">₱6,000 <span>/ night</span></div>
      <a href="hotel.php?id=fairways-bluewater" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card" data-destination="boracay">
      <div class="hotel-dest">📍 Boracay</div>
      <div class="hotel-name">Boracay Budget Inn</div>
      <div class="hotel-stars">★★☆☆☆</div>
      <div class="hotel-price">₱1,200 <span>/ night</span></div>
      <a href="hotel.php?id=boracay-budget-inn" class="btn-hotel">View Hotel →</a>
    </div>

    <!-- CEBU HOTELS -->
    <div class="hotel-card" data-destination="cebu">
      <div class="hotel-dest">📍 Cebu City</div>
      <div class="hotel-name">Radisson Blu Cebu</div>
      <div class="hotel-stars">★★★★★</div>
      <div class="hotel-price">₱9,000 <span>/ night</span></div>
      <a href="hotel.php?id=radisson-blu-cebu" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card" data-destination="cebu">
      <div class="hotel-dest">📍 Cebu City</div>
      <div class="hotel-name">Seda Ayala Center Cebu</div>
      <div class="hotel-stars">★★★★☆</div>
      <div class="hotel-price">₱5,500 <span>/ night</span></div>
      <a href="hotel.php?id=seda-cebu" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card" data-destination="cebu">
      <div class="hotel-dest">📍 Cebu City</div>
      <div class="hotel-name">Harolds Evotel Cebu</div>
      <div class="hotel-stars">★★★☆☆</div>
      <div class="hotel-price">₱2,200 <span>/ night</span></div>
      <a href="hotel.php?id=harolds-evotel" class="btn-hotel">View Hotel →</a>
    </div>

  </div>
</section>

<?php include 'includes/itinerary.php'; ?>

<!-- ── REVIEWS ── -->
<section class="reviews-section" id="reviews">
  <div class="section-header">
    <div class="section-tag">Traveler Stories</div>
    <h2 class="section-title">What Travelers <em>Say</em></h2>
  </div>
  <div class="reviews-grid">
    <div class="review-card">
      <div class="review-stars">★★★★★</div>
      <p class="review-text">"Baguio was absolutely magical! The strawberry farm tour was my favorite — fresh picks and amazing views. Lakbaylokal made the whole booking so easy."</p>
      <div class="review-author">
        <div class="review-avatar" style="background:var(--teal)">MR</div>
        <div class="review-info"><strong>Maria Reyes</strong><span>Baguio City Trip · May 2025</span></div>
      </div>
    </div>
    <div class="review-card">
      <div class="review-stars">★★★★★</div>
      <p class="review-text">"Boracay's White Beach is everything they say and more. Parasailing was a highlight! The itinerary builder helped me maximize every single day."</p>
      <div class="review-author">
        <div class="review-avatar" style="background:var(--ocean)">JC</div>
        <div class="review-info"><strong>Juan Carlos</strong><span>Boracay Trip · March 2025</span></div>
      </div>
    </div>
    <div class="review-card">
      <div class="review-stars">★★★★☆</div>
      <p class="review-text">"Cebu canyoneering was a life experience. Loved how the platform showed me all activities nearby the hotel I picked. Super convenient!"</p>
      <div class="review-author">
        <div class="review-avatar" style="background:var(--coral)">AL</div>
        <div class="review-info"><strong>Ana Lim</strong><span>Cebu Trip · April 2025</span></div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="assets/script.js">
>>>>>>> 4f0fdfbef158114ac38cdd496c925c5d2650c1c2
</script>
<script src="assets/script.js"></script>
</body>
</html>
