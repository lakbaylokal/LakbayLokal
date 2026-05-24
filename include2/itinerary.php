<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

</body>
</html>
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