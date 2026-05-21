<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LakbayPH — Discover the Philippines</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/styles.css">
  
</head>
<body>

<!-- ── NAVBAR ── -->
<nav>
  <div class="nav-logo">Lakbay<span>PH</span></div>
  <ul class="nav-links">
    <li><a href="#destinations">Destinations</a></li>
    <li><a href="#hotels">Hotels</a></li>
    <li><a href="#itinerary">Plan Trip</a></li>
    <li><a href="#reviews">Reviews</a></li>
  </ul>
  <div class="nav-actions">
    <button class="btn-nav" onclick="openModal('login')">Log In</button>
    <button class="btn-nav filled" onclick="openModal('register')">Sign Up</button>
  </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
  <div class="hero-badge">🇵🇭 Your Philippine Travel Guide</div>
  <h1>Discover the Beauty<br/>of <em>Pilipinas</em></h1>
  <p>Plan your perfect Philippine adventure — from Baguio's cool mountains to Boracay's white sand beaches. Book hotels, choose activities, create your itinerary.</p>
  <div class="hero-ctas">
    <a href="#destinations" class="btn-primary">Explore Destinations</a>
    <a href="#itinerary" class="btn-outline-white">Plan My Trip</a>
  </div>
  <div class="hero-stats">
    <div class="stat"><div class="stat-num">10</div><div class="stat-label">Destinations</div></div>
    <div class="stat"><div class="stat-num">30+</div><div class="stat-label">Hotels</div></div>
    <div class="stat"><div class="stat-num">50+</div><div class="stat-label">Activities</div></div>
    <div class="stat"><div class="stat-num">3</div><div class="stat-label">Regions</div></div>
  </div>
</section>

<!-- ── SEARCH ── -->
<div class="search-section">
  <div class="search-bar">
    <div class="search-field">
      <label>Destination</label>
      <select id="searchDest">
        <option value="">Where do you want to go?</option>
        <optgroup label="🌿 Luzon">
          <option>Baguio City</option>
          <option>Palawan</option>
          <option>Vigan</option>
        </optgroup>
        <optgroup label="🌊 Visayas">
          <option>Boracay</option>
          <option>Cebu City</option>
          <option>Siargao</option>
          <option>Bohol</option>
        </optgroup>
        <optgroup label="🏔️ Mindanao">
          <option>Bukidnon</option>
          <option>Davao City</option>
          <option>Camiguin</option>
        </optgroup>
      </select>
    </div>
    <div class="search-divider"></div>
    <div class="search-field">
      <label>Check In</label>
      <input type="date" id="checkIn" />
    </div>
    <div class="search-divider"></div>
    <div class="search-field">
      <label>Check Out</label>
      <input type="date" id="checkOut" />
    </div>
    <div class="search-divider"></div>
    <div class="search-field">
      <label>Travelers</label>
      <select>
        <option>1 Person</option>
        <option>2 People</option>
        <option>3-5 People</option>
        <option>6+ People</option>
      </select>
    </div>
    <button class="btn-search" onclick="scrollToDestinations()">🔍 Search</button>
  </div>
</div>

<!-- ── HOW IT WORKS ── -->
<section class="how-section">
  <div class="section-header">
    <div class="section-tag">Simple Process</div>
    <h2 class="section-title">How <em>LakbayPH</em> Works</h2>
    <p class="section-sub">From destination to doorstep — planning your Philippine trip has never been easier.</p>
  </div>
  <div class="steps-grid">
    <div class="step-card"><div class="step-num">1</div><div class="step-icon">🗺️</div><h4>Choose Destination</h4><p>Browse 10 stunning places across Luzon, Visayas & Mindanao</p></div>
    <div class="step-card"><div class="step-num">2</div><div class="step-icon">🏨</div><h4>Pick a Hotel</h4><p>Select from 3 curated hotels per destination, all price ranges</p></div>
    <div class="step-card"><div class="step-num">3</div><div class="step-icon">🎯</div><h4>Choose Activities</h4><p>Add tourist spots and experiences to your trip</p></div>
    <div class="step-card"><div class="step-num">4</div><div class="step-icon">📅</div><h4>Build Itinerary</h4><p>Organize your schedule day by day with our planner</p></div>
    <div class="step-card"><div class="step-num">5</div><div class="step-icon">💳</div><h4>Book & Pay</h4><p>Reserve your package and pay securely online</p></div>
  </div>
</section>

<!-- ── DESTINATIONS ── -->
<section class="dest-section" id="destinations">
  <div class="section-header">
    <div class="section-tag">10 Amazing Places</div>
    <h2 class="section-title">Explore <em>Destinations</em></h2>
    <p class="section-sub">From highland retreats to tropical paradise — something for every kind of traveler.</p>
  </div>
  <div class="region-tabs">
    <button class="tab-btn active" onclick="filterDest('all', this)">All Regions</button>
    <button class="tab-btn" onclick="filterDest('luzon', this)">🌿 Luzon</button>
    <button class="tab-btn" onclick="filterDest('visayas', this)">🌊 Visayas</button>
    <button class="tab-btn" onclick="filterDest('mindanao', this)">🏔️ Mindanao</button>
  </div>
  <div class="dest-grid" id="destGrid">

    <!-- LUZON -->
    <div class="dest-card" data-region="luzon">
      <div class="dest-img">
        <div class="dest-img-bg bg-baguio"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Luzon</div>
        <div class="dest-img-info"><h3>Baguio City</h3><span>Benguet, CAR</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🌲 Highlands</span><span class="tag">🍓 Strawberries</span><span class="tag">☕ Cafés</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8 <small style="color:#999;font-weight:400">(234)</small></div><button class="btn-explore" onclick="selectDest('Baguio City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="luzon">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#7b6b43,#4a3728)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Luzon</div>
        <div class="dest-img-info"><h3>Vigan City</h3><span>Ilocos Sur</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏛️ Heritage</span><span class="tag">🛺 Kalesa</span><span class="tag">🍶 Empanada</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.7</div><button class="btn-explore" onclick="selectDest('Vigan City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="luzon">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#006994,#01406b)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Luzon</div>
        <div class="dest-img-info"><h3>Palawan</h3><span>MIMAROPA</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏝️ Islands</span><span class="tag">🤿 Diving</span><span class="tag">🦅 Wildlife</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.9</div><button class="btn-explore" onclick="selectDest('Palawan')">Explore →</button></div>
      </div>
    </div>

    <!-- VISAYAS -->
    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg bg-boracay"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Boracay</h3><span>Aklan</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏖️ White Beach</span><span class="tag">🪂 Parasailing</span><span class="tag">🏄 Water Sports</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.9 <small style="color:#999;font-weight:400">(512)</small></div><button class="btn-explore" onclick="selectDest('Boracay')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg bg-cebu"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Cebu City</h3><span>Cebu</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">⛪ History</span><span class="tag">🦈 Whale Sharks</span><span class="tag">🌊 Canyoneering</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8</div><button class="btn-explore" onclick="selectDest('Cebu City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#1a6b8a,#0d3d54)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Siargao</h3><span>Surigao del Norte</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏄 Surfing</span><span class="tag">🌴 Lagoons</span><span class="tag">🐚 Snorkeling</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8</div><button class="btn-explore" onclick="selectDest('Siargao')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#4a8c6f,#2c6e49)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Bohol</h3><span>Bohol</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">⛰️ Chocolate Hills</span><span class="tag">🦎 Tarsier</span><span class="tag">⛵ River Cruise</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.7</div><button class="btn-explore" onclick="selectDest('Bohol')">Explore →</button></div>
      </div>
    </div>

    <!-- MINDANAO -->
    <div class="dest-card" data-region="mindanao">
      <div class="dest-img">
        <div class="dest-img-bg bg-bukidnon"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Mindanao</div>
        <div class="dest-img-info"><h3>Bukidnon</h3><span>Mindanao</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🌾 Highlands</span><span class="tag">🏕️ Camping</span><span class="tag">🌺 Festivals</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.6</div><button class="btn-explore" onclick="selectDest('Bukidnon')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="mindanao">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#5c8a60,#2e5232)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Mindanao</div>
        <div class="dest-img-info"><h3>Davao City</h3><span>Davao del Sur</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🦅 Eagles</span><span class="tag">🍌 Fruits</span><span class="tag">🌋 Mt. Apo</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.7</div><button class="btn-explore" onclick="selectDest('Davao City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="mindanao">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#0077b6,#023e58)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Mindanao</div>
        <div class="dest-img-info"><h3>Camiguin</h3><span>Camiguin</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🌋 Volcanoes</span><span class="tag">♨️ Hot Springs</span><span class="tag">⛪ Ruins</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8</div><button class="btn-explore" onclick="selectDest('Camiguin')">Explore →</button></div>
      </div>
    </div>

  </div>
</section>

<!-- ── HOTELS ── -->
<section class="hotels-section" id="hotels">
  <div class="section-header">
    <div class="section-tag">Curated Stays</div>
    <h2 class="section-title" style="color:var(--sand)">Featured <em style="color:var(--sun)">Hotels</em></h2>
    <p class="section-sub">Hand-picked accommodations for every destination and budget.</p>
  </div>
  <div class="hotels-grid">

    <!-- BAGUIO HOTELS -->
    <div class="hotel-card">
      <div class="hotel-dest">📍 Baguio City</div>
      <div class="hotel-name">Hotel Veniz</div>
      <div class="hotel-stars">★★★☆☆</div>
      <div class="hotel-price">₱1,800 <span>/ night</span></div>
      <a href="https://www.hotelveniz.com" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card">
      <div class="hotel-dest">📍 Baguio City</div>
      <div class="hotel-name">The Manor at Camp John Hay</div>
      <div class="hotel-stars">★★★★★</div>
      <div class="hotel-price">₱8,500 <span>/ night</span></div>
      <a href="https://www.campjohnhay.ph/accommodations/the-manor" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card">
      <div class="hotel-dest">📍 Baguio City</div>
      <div class="hotel-name">Microtel by Wyndham Baguio</div>
      <div class="hotel-stars">★★★☆☆</div>
      <div class="hotel-price">₱2,500 <span>/ night</span></div>
      <a href="https://www.wyndhamhotels.com/microtel/baguio-city-philippines" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>

    <!-- BORACAY HOTELS -->
    <div class="hotel-card">
      <div class="hotel-dest">📍 Boracay</div>
      <div class="hotel-name">Henann Resort Boracay</div>
      <div class="hotel-stars">★★★★★</div>
      <div class="hotel-price">₱12,000 <span>/ night</span></div>
      <a href="https://www.henann.com/boracay/henannresort/" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card">
      <div class="hotel-dest">📍 Boracay</div>
      <div class="hotel-name">Fairways & Bluewater</div>
      <div class="hotel-stars">★★★★☆</div>
      <div class="hotel-price">₱6,000 <span>/ night</span></div>
      <a href="https://www.fairwaysandbluewater.com" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card">
      <div class="hotel-dest">📍 Boracay</div>
      <div class="hotel-name">Boracay Budget Inn</div>
      <div class="hotel-stars">★★☆☆☆</div>
      <div class="hotel-price">₱1,200 <span>/ night</span></div>
      <a class="btn-hotel" style="cursor:pointer">View Hotel →</a>
    </div>

    <!-- CEBU HOTELS -->
    <div class="hotel-card">
      <div class="hotel-dest">📍 Cebu City</div>
      <div class="hotel-name">Radisson Blu Cebu</div>
      <div class="hotel-stars">★★★★★</div>
      <div class="hotel-price">₱9,000 <span>/ night</span></div>
      <a href="https://www.radissonhotels.com/cebu" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card">
      <div class="hotel-dest">📍 Cebu City</div>
      <div class="hotel-name">Seda Ayala Center Cebu</div>
      <div class="hotel-stars">★★★★☆</div>
      <div class="hotel-price">₱5,500 <span>/ night</span></div>
      <a href="https://www.sedahotels.com/cebu" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>
    <div class="hotel-card">
      <div class="hotel-dest">📍 Cebu City</div>
      <div class="hotel-name">Harolds Evotel Cebu</div>
      <div class="hotel-stars">★★★☆☆</div>
      <div class="hotel-price">₱2,200 <span>/ night</span></div>
      <a href="https://haroldshotelcebu.com" target="_blank" class="btn-hotel">View Hotel →</a>
    </div>

  </div>
</section>

<!-- ── ITINERARY BUILDER ── -->
<section class="builder-section" id="itinerary">
  <div class="section-header">
    <div class="section-tag">Trip Planner</div>
    <h2 class="section-title">Build Your <em>Itinerary</em></h2>
    <p class="section-sub">Choose your destination, hotel, and activities. We'll calculate your estimated budget instantly.</p>
  </div>
  <div class="builder-wrapper">
    <div class="builder-header">
      <h3>📋 Trip Planner</h3>
      <p>Fill in the details below to generate your personalized trip summary</p>
    </div>
    <div class="builder-body">
      <div class="form-grid">
        <div class="form-group">
          <label>Destination</label>
          <select id="planDest" onchange="updateHotels()">
            <option value="">Select destination...</option>
            <option value="baguio">Baguio City</option>
            <option value="boracay">Boracay</option>
            <option value="cebu">Cebu City</option>
            <option value="bukidnon">Bukidnon</option>
          </select>
        </div>
        <div class="form-group">
          <label>Hotel</label>
          <select id="planHotel" onchange="updateSummary()">
            <option value="">Select hotel...</option>
          </select>
        </div>
        <div class="form-group">
          <label>Check-in Date</label>
          <input type="date" id="planCheckIn" onchange="updateSummary()" />
        </div>
        <div class="form-group">
          <label>Check-out Date</label>
          <input type="date" id="planCheckOut" onchange="updateSummary()" />
        </div>
      </div>

      <div class="activities-label">Choose Activities</div>
      <div class="activities-grid" id="activitiesGrid">
        <div class="activity-check" onclick="toggleActivity(this, 0)">
          <div class="check-icon"></div> Burnham Park
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 200)">
          <div class="check-icon"></div> Strawberry Farm
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 300)">
          <div class="check-icon"></div> Café Hopping
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 500)">
          <div class="check-icon"></div> Hiking / Trekking
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 400)">
          <div class="check-icon"></div> Botanical Garden
        </div>
      </div>

      <div class="builder-summary" id="builderSummary">
        <div class="summary-title">📄 Trip Summary</div>
        <div class="summary-row"><span>Destination</span><strong id="sumDest">—</strong></div>
        <div class="summary-row"><span>Hotel</span><strong id="sumHotel">—</strong></div>
        <div class="summary-row"><span>Travel Dates</span><strong id="sumDates">—</strong></div>
        <div class="summary-row"><span>Nights</span><strong id="sumNights">—</strong></div>
        <div class="summary-row"><span>Hotel Cost</span><strong id="sumHotelCost">₱0</strong></div>
        <div class="summary-row"><span>Activity Fees</span><strong id="sumActCost">₱0</strong></div>
        <div class="summary-row summary-total"><span>Total Estimated Budget</span><strong id="sumTotal">₱0</strong></div>
      </div>

      <button class="btn-book" onclick="confirmBooking()">Reserve This Package →</button>
    </div>
  </div>
</section>

<!-- ── REVIEWS ── -->
<section class="reviews-section" id="reviews">
  <div class="section-header">
    <div class="section-tag">Traveler Stories</div>
    <h2 class="section-title">What Travelers <em>Say</em></h2>
  </div>
  <div class="reviews-grid">
    <div class="review-card">
      <div class="review-stars">★★★★★</div>
      <p class="review-text">"Baguio was absolutely magical! The strawberry farm tour was my favorite — fresh picks and amazing views. LakbayPH made the whole booking so easy."</p>
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

<!-- ── ADMIN TEASER ── -->
<section class="admin-section">
  <h2>Admin Dashboard</h2>
  <p>Manage destinations, hotels, bookings, and reviews from one central dashboard.</p>
  <div class="admin-cards">
    <div class="admin-card"><strong>10</strong>Destinations</div>
    <div class="admin-card"><strong>30+</strong>Hotels</div>
    <div class="admin-card"><strong>📊</strong>Booking Reports</div>
    <div class="admin-card"><strong>⭐</strong>Reviews</div>
    <div class="admin-card"><strong>👥</strong>Users</div>
  </div>
  <button class="btn-primary" onclick="openModal('login')">Access Admin Panel →</button>
</section>

<!-- ── FOOTER ── -->
<footer>
  <p><strong>LakbayPH</strong> &mdash; Your Philippine Travel Companion &nbsp;|&nbsp; 🇵🇭 Built with love for Filipino travel &nbsp;|&nbsp; © 2025</p>
</footer>

<!-- ── LOGIN / REGISTER MODAL ── -->
<div class="modal-overlay" id="authModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <h2>Welcome to LakbayPH</h2>
    <div class="modal-tabs">
      <div class="modal-tab active" onclick="switchTab('login')">Log In</div>
      <div class="modal-tab" onclick="switchTab('register')">Register</div>
    </div>
    <div class="modal-form active" id="loginForm">
      <input type="email" placeholder="Email address" />
      <input type="password" placeholder="Password" />
      <button class="btn-modal">Log In</button>
    </div>
    <div class="modal-form" id="registerForm">
      <input type="text" placeholder="Full name" />
      <input type="email" placeholder="Email address" />
      <input type="password" placeholder="Password" />
      <input type="password" placeholder="Confirm password" />
      <button class="btn-modal">Create Account</button>
    </div>
  </div>
</div>

<script src="assets/script.js">
</script>
</body>
</html>