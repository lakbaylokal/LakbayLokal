<div class="page" id="page-detail">
  <button class="back-btn" onclick="goBack()">← Back</button>
  <div class="detail-hero" id="detailHero">
    <div class="detail-hero-content">
      <h1 id="detailTitle">Baguio City</h1>
      <p id="detailSub">Choose your hotel &amp; activities below</p>
    </div>
  </div>

  <div class="detail-layout">
    <div>
      <div class="hotels-section">
        <h2>🏨 Choose a Hotel</h2>
        <div class="hotel-list" id="hotelList"></div>
      </div>
      <div class="activities-section">
        <h2>🎯 Add Activities</h2>
        <div class="activity-list" id="activityList"></div>
      </div>
    </div>

    <div class="booking-sidebar">
      <h3>Your Booking</h3>
      <div id="bookingSummary">
        <div class="booking-line"><span>Destination</span><strong id="bDest">—</strong></div>
        <div class="booking-line"><span>Base Package</span><strong id="bBase">—</strong></div>
        <div class="booking-line"><span>Hotel</span><strong id="bHotel">Not selected</strong></div>
        <div class="booking-line"><span>Activities</span><strong id="bActs">None</strong></div>
        <div class="booking-total"><span>Total</span><span class="total-price" id="bTotal">—</span></div>
      </div>
      <div class="booking-form">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" id="guestName" placeholder="Juan dela Cruz">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="guestEmail" placeholder="juan@email.com">
        </div>
        <div class="form-group">
          <label>Check-in Date</label>
          <input type="date" id="checkinDate">
        </div>
        <div class="form-group">
          <label>Guests</label>
          <select id="guestCount">
            <option>1 Guest</option>
            <option>2 Guests</option>
            <option>3 Guests</option>
            <option>4 Guests</option>
            <option>5+ Guests</option>
          </select>
        </div>
        <button class="book-now-btn" onclick="confirmBooking()">Confirm Booking →</button>
      </div>
    </div>
  </div>
</div> 
