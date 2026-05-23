<div class="search-section">
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
