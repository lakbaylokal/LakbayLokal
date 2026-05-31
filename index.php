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
    <span class="hero-tag">🇵🇭 Your Philippine Journey Starts Here</span>
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
<div class="search-section">
  <div class="search-group">
    <label>Destination</label>
    <select id="homeSearchDest">
      <option value="">All Destinations</option>
      <?php foreach ($destinations as $d): ?>
        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="search-group">
    <label>Budget</label>
    <select id="homeSearchBudget">
      <option value="">Any Budget</option>
      <option value="low">Under ₱5,000</option>
      <option value="mid">₱5,000 – ₱7,500</option>
      <option value="high">Above ₱7,500</option>
    </select>
  </div>
  <div class="search-group">
    <label>Check-in Date</label>
    <input type="date" id="homeCheckin" min="<?= date('Y-m-d') ?>">
  </div>
  <button class="search-btn" onclick="doHomeSearch()">🔍 Search</button>
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

<script>
// Inject PHP data for JS use
window.DESTINATIONS = <?= json_encode($destinations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function doHomeSearch() {
  const dest   = document.getElementById('homeSearchDest').value;
  const budget = document.getElementById('homeSearchBudget').value;
  let url = 'destinations.php?';
  if (dest)   url += 'dest=' + encodeURIComponent(dest) + '&';
  if (budget) url += 'budget=' + encodeURIComponent(budget);
  window.location.href = url;
}

// Render My Trips from sessionStorage
function renderMyTrips() {
  const bookings = JSON.parse(sessionStorage.getItem('lbl_bookings') || '[]');
  const el = document.getElementById('myTripsContent');
  if (!bookings.length) return;
  el.innerHTML = '<div class="dash-stats" style="margin-bottom:1.5rem;">' +
    '<div class="dash-stat"><div class="dash-stat-num">' + bookings.length + '</div><div class="dash-stat-label">Total Bookings</div></div>' +
    '<div class="dash-stat"><div class="dash-stat-num">₱' + bookings.reduce((s,b)=>s+(b.total_price||0),0).toLocaleString() + '</div><div class="dash-stat-label">Total Spent</div></div>' +
    '</div>' +
    '<div style="display:flex;flex-direction:column;gap:1rem;">' +
    bookings.map(b => `
      <div class="booking-card">
        <div class="booking-dest-icon" style="background:${b.gradient||'var(--primary)'};display:flex;align-items:center;justify-content:center;font-size:1.8rem;width:60px;height:60px;border-radius:12px;flex-shrink:0;">${b.emoji||'🏝️'}</div>
        <div class="booking-card-info" style="flex:1;">
          <h4 style="font-weight:700;margin-bottom:4px;">${b.dest_name}</h4>
          <p style="font-size:0.85rem;color:var(--muted);">${b.hotel_name} · Check-in: ${b.checkin}</p>
          <p style="font-weight:700;color:var(--primary);margin-top:4px;">₱${(b.total_price||0).toLocaleString()}</p>
        </div>
        <span class="booking-status status-upcoming">Upcoming</span>
      </div>`).join('') +
    '</div>';
}

renderMyTrips();
</script>
