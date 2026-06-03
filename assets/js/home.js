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

