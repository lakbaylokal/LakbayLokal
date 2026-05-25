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
            <option value="vigan">Vigan City</option>
            <option value="palawan">Palawan</option>
            <option value="boracay">Boracay</option>
            <option value="cebu">Cebu City</option>
            <option value="bukidnon">Bukidnon</option>
            <option value="davao">Siargao Island</option>

          </select>
          </select>
          </select>
        </div>
        <div class="form-group">
          <label>Hotel</label>
          <select id="planHotel" onchange="updateSummary()">
            <option value="">Select hotel...</option>
            <option value="sotogrande">Sotogrande Hotel Baguio</option>
            <option value="the_mansion">The Mansion Baguio</option>
            <option value="travelite">Travelite Express Hotel</option>
            <option value="felicidad">Hotel Felicidad Vigan</option>
            <option value="paradores">Paradores de Vigan</option>
            <option value="luna">Hotel Luna Vigan</option>
            <option value="el_nido">Seda Lio (El Nido)</option>
            <option value="hue">Hue Hotels and Resorts (Puerto Princesa)</option>
            <option value="two_seasons">Two Seasons Coron Island Resort</option>


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
          <div class="check-icon"></div> Strawberry Picking at La Trinidad Farm
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 200)">
          <div class="check-icon"></div> BenCab Museum Gallery Tour
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 300)">
          <div class="check-icon"></div> Tree Top Adventure (Camp John Hay)
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 500)">
          <div class="check-icon"></div> Igorot Stone Kingdom Exploration 
        </div>
        <div class="activity-check" onclick="toggleActivity(this, 400)">
          <div class="check-icon"></div> Calesa Ride around Calle CrisolgoN
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