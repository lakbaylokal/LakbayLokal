<?php
require_once 'data.php';
include 'config/db.php';

$pageTitle  = 'LakbayLokal — Explore the Philippines';
$activePage = 'home';
$rootPath   = '';

include 'includes/header.php';
?>

<!-- ======= HOME HERO ======= -->
<section class="hero">
  <div class="hero-content">
    <h1>Discover the <em>Beauty</em> of the Philippines</h1>
    <p>Handpicked hotel stays paired with unforgettable local activities — all in one seamless booking platform.</p>
    <div class="hero-btns">
      <a href="destinations.php" class="btn-primary">
        Explore Destinations
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
      <a href="#about" class="btn-outline">How It Works</a>
    </div>
    <div class="hero-stats">
      <div class="stat-item"><div class="stat-num"><?= count($destinations) ?>+</div><div class="stat-label">Destinations</div></div>
      <div class="stat-item"><div class="stat-num"><?= array_sum(array_map(fn($d) => count($d['hotels']), $destinations)) ?>+</div><div class="stat-label">Hotels</div></div>
      <div class="stat-item"><div class="stat-num">100%</div><div class="stat-label">Local Picks</div></div>
    </div>
  </div>
</section>

<!-- ======= HOME SEARCH ======= -->
<div class="search-section-wrapper">
  <div class="row g-3 align-items-end"> 
    
    <div class="col-12 col-md-4 col-lg-3">
      <div class="search-group">
        <label for="homeSearchDest">Destination</label>
        <select id="homeSearchDest" class="form-select">
          <option value="">All Destinations</option>
          <?php foreach ($destinations as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="col-12 col-md-4 col-lg-3">
      <div class="search-group">
        <label for="homeSearchBudget">Budget</label>
        <select id="homeSearchBudget" class="form-select">
          <option value="">Any Budget</option>
          <option value="low">Under ₱5,000</option>
          <option value="mid">₱5,000 – ₱7,500</option>
          <option value="high">Above ₱7,500</option>
        </select>
      </div>
    </div>
    <div class="col-12 col-md-4 col-lg-3">
      <div class="search-group">
        <label for="homeCheckin">Check-in Date</label>
        <input type="date" id="homeCheckin" class="form-control" min="<?= date('Y-m-d') ?>">
      </div>
    </div>
    <div class="col-12 col-lg-3">
      <button class="search-btn w-100" onclick="doHomeSearch()">
        <i class="bi bi-search"></i>
        <span>Search</span>
      </button>
    </div>
  </div>
</div>

<!-- ======= FEATURED DESTINATIONS ======= -->
<section class="section">
  <div class="section-header">
    <h2 class="section-title">Featured <span>Destinations</span></h2>
    <a href="destinations.php" class="see-all">See all →</a>
  </div>
  <div class="dest-grid">
    <?php foreach (array_slice($destinations, 0, 3) as $d): ?>
      <a href="destinations.php?dest=<?= $d['id'] ?>" class="dest-card">
        <div class="dest-img" style="background: <?= $d['gradient'] ?>;">
          <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:4rem"><?= $d['emoji'] ?></div>
          <div class="dest-badge"><?= htmlspecialchars($d['region']) ?></div>
          <div class="dest-price-badge">From ₱<?= number_format($d['price_from']) ?></div>
        </div>
        <div class="dest-body">
          <h3><?= htmlspecialchars($d['name']) ?></h3>
          <div class="dest-meta">📍 <?= htmlspecialchars($d['tagline']) ?></div>
          <div class="dest-activities" style="margin:0.5rem 0;">
            <?php foreach ($d['activities'] as $a): ?>
              <span class="act-tag"><?= htmlspecialchars($a) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="dest-footer">
            <div class="dest-footer-price">Base: <strong>₱<?= number_format($d['price']) ?></strong></div>
            <span class="book-btn">Book Now</span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ======= WHY LAKBAYLOKAL ======= -->
<section class="section" id="about" style="background:white;padding-top:3rem;padding-bottom:3rem;">
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
      <div style="font-size:2.5rem;margin-bottom:1rem;">🏨</div>
      <h3 style="font-weight:700;margin-bottom:0.5rem;">Curated Hotels</h3>
      <p style="font-size:0.88rem;color:var(--muted);">Handpicked accommodations with detailed info, ratings, and policies.</p>
    </div>
    <div style="text-align:center;padding:1.5rem;">
      <div style="font-size:2.5rem;margin-bottom:1rem;">🤝</div>
      <h3 style="font-weight:700;margin-bottom:0.5rem;">Local Experiences</h3>
      <p style="font-size:0.88rem;color:var(--muted);">Authentic activities curated for each destination.</p>
    </div>
    <div style="text-align:center;padding:1.5rem;">
      <div style="font-size:2.5rem;margin-bottom:1rem;">📱</div>
      <h3 style="font-weight:700;margin-bottom:0.5rem;">Easy Booking</h3>
      <p style="font-size:0.88rem;color:var(--muted);">Book in minutes from any device — desktop or mobile.</p>
    </div>
  </div>
</section>

<!-- ======= MY TRIPS (session-based) ======= -->
<section class="section" id="mytrips">
  <div class="section-header">
    <h2 class="section-title">My <span>Trips</span></h2>
  </div>
  <div id="myTripsContent">
    <div style="text-align:center;padding:3rem;color:var(--muted);">
      <div style="font-size:3rem;margin-bottom:1rem;">🗺️</div>
      <p>No bookings yet. <a href="destinations.php" style="color:var(--primary);font-weight:600;">Explore destinations →</a></p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>