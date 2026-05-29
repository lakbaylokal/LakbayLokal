<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LakbayLokal</title>
</head>
<body>
  <div class="page active" id="page-home">
    <section class="hero">
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
    </div>
  </section>

  <!-- SEARCH -->
  <div class="search-section">
    <div class="search-group">
      <label>Destination</label>
      <select id="homeSearchDest">
        <option value="">All Destinations</option>
        <?php
          if (!isset($destinations) || !is_array($destinations)) {
            $destinations = [];
          }
          foreach ($destinations as $d):
        ?>
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
  </div>

  <!-- FEATURED DESTINATIONS -->
  <section class="section">
    <div class="section-header">
      <h2 class="section-title">Featured <span>Destinations</span></h2>
      <button class="see-all" onclick="showPage('destinations')">See all →</button>
    </div>
    <div class="dest-grid" id="featuredGrid"></div>
  </section>

  <!-- WHY SECTION -->
  <section class="section" style="background:white;padding-top:3rem;padding-bottom:3rem;">
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
</div>