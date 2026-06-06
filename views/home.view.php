<!-- ======= HOME HERO ======= -->
<section class="hero">
  <div class="container-fluid px-5">
    <div class="hero-content">
      <h1>Discover the <em>Beauty</em> of the Philippines</h1>
      <p>Handpicked hotel stays paired with unforgettable local activities — all in one seamless booking platform.</p>
      <div class="hero-btns">
        <a href="destinations.php" class="btn btn-primary btn-lg btn-custom">
          Explore Destinations
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#about" class="btn btn-outline btn-lg btn-custom">How It Works</a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num"><?= count($destinations) ?>+</div>
          <div class="stat-label">Destinations</div>
        </div>
        <div class="stat-item">
          <div class="stat-num"><?= array_sum(array_map(fn($d) => count($d['hotels']), $destinations)) ?>+</div>
          <div class="stat-label">Hotels</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">100%</div>
          <div class="stat-label">Local Picks</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======= HOME SEARCH ======= -->
<section class="search-section-wrapper">
  <div class="container-fluid px-5">
    <div class="row g-3 align-items-end">
      
      <div class="col-12 col-md-6 col-lg-3">
        <div class="search-group">
          <label for="homeSearchDest" class="form-label">Destination</label>
          <select id="homeSearchDest" class="form-select">
            <option value="">All Destinations</option>
            <?php foreach ($destinations as $d): ?>
              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="search-group">
          <label for="homeSearchBudget" class="form-label">Budget</label>
          <select id="homeSearchBudget" class="form-select">
            <option value="">Any Budget</option>
            <option value="low">Under ₱5,000</option>
            <option value="mid">₱5,000 – ₱7,500</option>
            <option value="high">Above ₱7,500</option>
          </select>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="search-group">
          <label for="homeCheckin" class="form-label">Check-in Date</label>
          <input type="date" id="homeCheckin" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3 d-flex">
        <button class="search-btn w-100" onclick="doHomeSearch()">
          <i class="bi bi-search"></i>
          <span>Search</span>
        </button>
      </div>

    </div>
  </div>
</section>

<!-- ======= FEATURED DESTINATIONS ======= -->
<section class="section">
  <div class="container-fluid px-5">
    <div class="section-header">
      <h2 class="section-title">Featured <span>Destinations</span></h2>
      <a href="destinations.php" class="see-all">See all →</a>
    </div>
    <div class="row g-4">
      <?php foreach (array_slice($destinations, 0, 3) as $d): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <a href="destinations.php?dest=<?= $d['id'] ?>" class="dest-card">
            <div class="dest-img" style="background: <?= $d['gradient'] ?>;">
              <div class="dest-img-emoji">
                <?= $d['emoji'] ?>
              </div>
              <div class="dest-badge"><?= htmlspecialchars($d['region']) ?></div>
              <div class="dest-price-badge">From ₱<?= number_format($d['price_from']) ?></div>
            </div>
            <div class="dest-body">
              <h3><?= htmlspecialchars($d['name']) ?></h3>
              <div class="dest-meta">📍 <?= htmlspecialchars($d['tagline']) ?></div>
              <div class="dest-activities">
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
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======= WHY LAKBAYLOKAL ======= -->
<section class="section" id="about" style="background:white;">
  <div class="container-fluid px-5">
    <div class="section-header">
      <h2 class="section-title">Why <span>LakbayLokal?</span></h2>
    </div>
    <div class="row g-4 mt-2">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="why-card text-center p-4">
          <div class="why-icon">🗺️</div>
          <h3>All-in-One Planning</h3>
          <p>Hotel + activities bundled in one convenient booking flow.</p>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="why-card text-center p-4">
          <div class="why-icon">🏨</div>
          <h3>Curated Hotels</h3>
          <p>Handpicked accommodations with detailed info, ratings, and policies.</p>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="why-card text-center p-4">
          <div class="why-icon">🤝</div>
          <h3>Local Experiences</h3>
          <p>Authentic activities curated for each destination.</p>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="why-card text-center p-4">
          <div class="why-icon">📱</div>
          <h3>Easy Booking</h3>
          <p>Book in minutes from any device — desktop or mobile.</p>
        </div>
      </div>
    </div>
  </div>
</section>