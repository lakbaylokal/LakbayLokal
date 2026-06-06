<?php
require_once 'data.php';
include 'config/db.php';

$pageTitle  = 'LakbayLokal — Explore the Philippines';
$activePage = 'home';
$rootPath   = '';

include 'includes/header.php';
?>

<!-- ======= HOME HERO ======= -->
<?php 
include 'views/home.view.php';
?>

<!-- ======= MY TRIPS SECTION ======= -->
<section class="section py-5" id="mytrips" style="background-color: #f8f9fa;">
  <div class="container px-4 px-md-5">
    
    <div class="section-header mb-4">
      <h2 class="section-title h3 fw-bold text-dark">My <span class="text-primary">Trips</span></h2>
      <p class="text-muted small">Manage and track your venue bookings and itineraries.</p>
    </div>
    <div id="myTripsContent"></div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
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
  
  if (!el) return;

  // Kung walang laman ang bookings, ipakita ang empty state
  if (!bookings.length) {
    el.innerHTML = `
      <div class="text-center py-5 edit-empty-state">
        <div class="empty-icon">🗺️</div>
        <p class="text-muted mb-0">No bookings yet.</p>
        <a href="destinations.php" class="btn btn-sm btn-primary mt-3 px-4 rounded-pill">Explore Destinations →</a>
      </div>
    `;
    return;
  }

  // Kwenkwentahin ang kabuuang nagastos
  const totalSpent = bookings.reduce((sum, b) => sum + (b.total_price || 0), 0);
  const today = new Date();

  // Render Dashboard Stats at Booking Cards
  el.innerHTML = `
    <!-- Stats Dashboard Block -->
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-lg-4">
        <div class="stat-card p-3 border rounded-3 bg-white shadow-sm d-flex align-items-center">
          <div class="stat-icon bg-light text-primary me-3 rounded-3 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
            <i class="bi bi-suitcase-lg fs-4"></i>
          </div>
          <div>
            <div class="text-muted small fw-medium">Total Bookings</div>
            <div class="fs-4 fw-bold text-dark">${bookings.length}</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="stat-card p-3 border rounded-3 bg-white shadow-sm d-flex align-items-center">
          <div class="stat-icon bg-light text-success me-3 rounded-3 d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
            <i class="bi bi-wallet2 fs-4"></i>
          </div>
          <div>
            <div class="text-muted small fw-medium">Total Spent</div>
            <div class="fs-4 fw-bold text-dark">₱${totalSpent.toLocaleString()}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bookings Container -->
    <div class="d-flex flex-column gap-3">
      ${bookings.map(b => {
        // Dynamic status check batay sa petsa ngayon
        const checkinDate = b.checkin ? new Date(b.checkin) : null;
        const isUpcoming = checkinDate && checkinDate >= today.setHours(0,0,0,0);
        
        const statusClass = isUpcoming ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-secondary-subtle text-secondary border-secondary-subtle';
        const statusLabel = isUpcoming ? 'Upcoming' : 'Past Trip';

        return `
          <div class="trip-card p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-3 transition-all">
            <!-- Icon/Emoji Wrapper -->
            <div class="trip-icon-wrapper rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center" 
                 style="background: ${b.gradient || 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'}; width: 56px; height: 56px; font-size: 1.6rem;">
              ${b.emoji || '🏝️'}
            </div>

            <!-- Booking Details -->
            <div class="flex-grow-1 min-w-0">
              <h5 class="mb-1 fw-bold text-dark text-truncate">${b.dest_name}</h5>
              <div class="text-muted small text-truncate mb-1">
                <i class="bi bi-building me-1"></i>${b.hotel_name} 
                <span class="mx-2 text-black-51">·</span> 
                <i class="bi bi-calendar-check me-1"></i>${b.checkin || 'N/A'}
              </div>
              <div class="fw-bold text-primary fs-6">₱${(b.total_price || 0).toLocaleString()}</div>
            </div>

            <!-- Status Badge -->
            <div class="flex-shrink-0 align-self-start align-self-sm-center">
              <span class="badge border rounded-pill px-3 py-2 fw-semibold ${statusClass}" style="font-size: 0.75rem;">
                ${statusLabel}
              </span>
            </div>
          </div>
        `;
      }).join('')}
    </div>
  `;
}

// Patakbuhin ang function
renderMyTrips();
</script>
