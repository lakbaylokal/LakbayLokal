<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LakbayPH — Discover the Philippines</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="assets/styles.css">
</head>

<body>

  <?php include 'includes/header.php'; ?>
  <!-- ── HERO ── -->
  <section class="hero">
    <div class="hero-badge">🇵🇭 Your Philippine Travel Guide</div>
    <h1>Discover the Beauty<br />of <em>Pilipinas</em></h1>
    <p>Plan your perfect Philippine adventure — from Baguio's cool mountains to Boracay's white sand beaches. Book
      hotels, choose activities, create your itinerary.</p>
    <div class="hero-ctas">
      <a href="#destinations" class="btn-primary">Explore Destinations</a>
      <a href="#itinerary" class="btn-outline-white">Plan My Trip</a>
    </div>
    <div class="hero-stats">
      <div class="stat">
        <div class="stat-num">7</div>
        <div class="stat-label">Destinations</div>
      </div>
      <div class="stat">
        <div class="stat-num">20+</div>
        <div class="stat-label">Hotels</div>
      </div>
      <div class="stat">
        <div class="stat-num">30+</div>
        <div class="stat-label">Activities</div>
      </div>
      <div class="stat">
        <div class="stat-num">3</div>
        <div class="stat-label">Regions</div>
      </div>
    </div>
  </section>

  <?php include 'hotel/search.php'; ?>

  <!-- ── HOW IT WORKS ── -->
  <section class="how-section">
    <div class="section-header">
      <div class="section-tag">Simple Process</div>
      <h2 class="section-title">How <em>LakbayPH</em> Works</h2>
      <p class="section-sub">From destination to doorstep — planning your Philippine trip has never been easier.</p>
    </div>
    <div class="steps-grid">
      <div class="step-card">
        <div class="step-num">1</div>
        <div class="step-icon">🗺️</div>
        <h4>Choose Destination</h4>
        <p>Browse 10 stunning places across Luzon, Visayas & Mindanao</p>
      </div>
      <div class="step-card">
        <div class="step-num">2</div>
        <div class="step-icon">🏨</div>
        <h4>Pick a Hotel</h4>
        <p>Select from 3 curated hotels per destination, all price ranges</p>
      </div>
      <div class="step-card">
        <div class="step-num">3</div>
        <div class="step-icon">🎯</div>
        <h4>Choose Activities</h4>
        <p>Add tourist spots and experiences to your trip</p>
      </div>
      <div class="step-card">
        <div class="step-num">4</div>
        <div class="step-icon">📅</div>
        <h4>Build Itinerary</h4>
        <p>Organize your schedule day by day with our planner</p>
      </div>
      <div class="step-card">
        <div class="step-num">5</div>
        <div class="step-icon">💳</div>
        <h4>Book & Pay</h4>
        <p>Reserve your package and pay securely online</p>
      </div>
    </div>
  </section>

  <!-- ── DESTINATIONS ── -->
  <?php include 'destination/destination.php'; ?>

  <!-- ── HOTELS ── -->
  <?php include 'hotel/hotels.php'; ?>

  <!-- ── ITINERARY BUILDER ── -->
  <section class="builder-section" id="itinerary">
    <div class="section-header">
      <div class="section-tag">Trip Planner</div>
      <h2 class="section-title">Build Your <em>Itinerary</em></h2>
      <p class="section-sub">Choose your destination, hotel, and activities. We'll calculate your estimated budget
        instantly.</p>
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
          <div class="summary-row summary-total"><span>Total Estimated Budget</span><strong id="sumTotal">₱0</strong>
          </div>
        </div>

        <button class="btn-book" onclick="confirmBooking()">Reserve This Package →</button>
      </div>
    </div>
  </section>

  <!-- ── REVIEWS ── -->
  <?php include 'reviews/reviews.php'; ?>

  <?php include 'includes/footer.php'; ?>

  <script src="assets/script.js">
  </script>
</body>

</html>