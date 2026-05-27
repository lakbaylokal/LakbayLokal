<div class="page" id="page-dashboard">
  <div class="dash-header">
    <h1>My Trips Dashboard</h1>
    <p>Manage your bookings and upcoming adventures</p>
  </div>
  <div class="dash-stats">
    <div class="dash-stat"><div class="dash-stat-num" id="statTotal">0</div><div class="dash-stat-label">Total Bookings</div></div>
    <div class="dash-stat"><div class="dash-stat-num" id="statUpcoming">0</div><div class="dash-stat-label">Upcoming Trips</div></div>
    <div class="dash-stat"><div class="dash-stat-num" id="statSpent">₱0</div><div class="dash-stat-label">Total Spent</div></div>
    <div class="dash-stat"><div class="dash-stat-num">5★</div><div class="dash-stat-label">Avg. Rating</div></div>
  </div>
  <div class="dash-content">
    <h2 class="dash-bookings-title">Your Bookings</h2>
    <div id="bookingsList">
      <div style="text-align:center;padding:3rem;color:var(--muted);">
        <div style="font-size:3rem;margin-bottom:1rem;">🗺️</div>
        <p>No bookings yet. <button onclick="showPage('destinations')" style="background:none;border:none;color:var(--primary);font-weight:600;cursor:pointer;font-size:inherit;">Explore destinations →</button></p>
      </div>
    </div>
  </div>
</div>