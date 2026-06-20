<?php
require_once 'config/db.php';
require_once 'database/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch all destinations from DB
$destinations = getAllDestinations($conn);

// ── AUTOMATIC SESSION DETECTOR ──
// Titingnan natin kung may kahit anong login indicator sa session mo (user_id, user, o username)
$currentUserId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? $_SESSION['username'] ?? null; 

$userBookings = [];
if ($currentUserId) {
    if (function_exists('getUserBookings')) {
        $userBookings = getUserBookings($conn, $currentUserId);
    } else {
        // Fallback: Kunin sa kasalukuyang session history para sa naka-log in na user
        $userBookings = array_values($_SESSION['receipt_history'] ?? []);
    }
}
// ────────────────────────────────

$pageTitle  = 'LakbayLokal — Explore the Philippines';
$activePage = 'home';
$rootPath   = '';

include 'includes/header.php';
?>

<!-- Custom Premium Styles for My Trips -->
<style>
  .ll-trips-section {
    background-color: var(--cream);
    padding: 4rem 0;
    font-family: 'DM Sans', sans-serif;
  }
  .ll-stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: var(--shadow-sm);
  }
  .ll-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--primary-pale);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }
  .ll-trip-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .ll-trip-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
  }
  .ll-emoji-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 -4px 0 rgba(0,0,0,0.1);
  }
  .ll-badge-status {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .ll-badge-upcoming {
    background: var(--primary-pale);
    color: var(--primary);
    border: 1px solid var(--primary-light);
  }
  .ll-badge-past {
    background: #f1f3f5;
    color: #6c757d;
    border: 1px solid #dee2e6;
  }
  .ll-meta-item {
    font-size: 0.88rem;
    color: var(--muted);
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }
</style>

<!-- ======= HOME HERO ======= -->
<?php include 'views/home.view.php'; ?>

<!-- ======= MY TRIPS SECTION ======= -->
<section class="ll-trips-section" id="mytrips">
  <div class="container px-4 px-md-5">

    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 style="font-family: 'Playfair Display', serif; color: var(--deep); font-weight: 700;" class="h3 mb-1">
          My <span style="color: var(--primary);">Trips</span>
        </h2>
        <p class="text-muted small mb-0">Manage and track your venue bookings and itineraries.</p>
      </div>
    </div>

    <div id="myTripsContent">
      <?php if (!$currentUserId): ?>
        <!-- SIMPLE NOT SIGNED IN STATE -->
        <div class="text-center py-4 ll-stat-card">
          <p class="text-muted mb-2 small">Please sign in to view your dynamic bookings.</p>
          <a href="login.php" class="btn btn-sm text-white px-4 rounded-pill" style="background: var(--primary); font-weight: 600; font-size: 0.8rem;">Sign In</a>
        </div>

      <?php elseif (empty($userBookings)): ?>
        <!-- EMPTY BOOKINGS STATE -->
        <div class="text-center py-5 ll-stat-card">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🗺️</div>
          <p class="text-muted mb-0 small fw-medium">No bookings found for your account.</p>
          <a href="destinations.php" class="btn btn-sm text-white mt-3 px-4 rounded-pill" style="background: var(--accent); font-weight: 600;">Explore Destinations →</a>
        </div>

      <?php else: 
        $totalBookings = count($userBookings);
        $totalSpent = array_reduce($userBookings, function($sum, $b) {
            return $sum + (($b['total_price'] ?? $b['total'] ?? 0));
        }, 0);
      ?>
        <!-- STATS BAR -->
        <div class="row g-3 mb-4">
          <div class="col-md-6 col-lg-4">
            <div class="ll-stat-card d-flex align-items-center">
              <div class="ll-stat-icon me-3">
                <i class="bi bi-suitcase-lg-fill"></i>
              </div>
              <div>
                <div class="text-muted small fw-medium">Total Bookings</div>
                <div class="fs-4 fw-bold" style="color: var(--deep);"><?= $totalBookings ?></div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="ll-stat-card d-flex align-items-center">
              <div class="ll-stat-icon me-3" style="background: #eef9f2; color: #2e6b4f;">
                <i class="bi bi-wallet2"></i>
              </div>
              <div>
                <div class="text-muted small fw-medium">Total Spent</div>
                <div class="fs-4 fw-bold" style="color: var(--deep);">₱<?= number_format($totalSpent) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- BOOKINGS LISTING -->
        <div class="d-flex flex-column gap-3">
          <?php foreach ($userBookings as $b): 
            $refId = $b['ref'] ?? $b['booking_ref'] ?? 'N/A';
            $hotelName = $b['hotel_name'] ?? 'Unknown Accommodation';
            $destName = $b['dest_name'] ?? 'Local Destination';
            $checkin = $b['checkin'] ?? $b['checkin_fmt'] ?? null;
            $checkout = $b['checkout'] ?? $b['checkout_fmt'] ?? 'N/A';
            $rooms = $b['rooms'] ?? 1;
            $nights = $b['nights'] ?? 1;
            $price = $b['total_price'] ?? $b['total'] ?? 0;
            
            $isUpcoming = true;
            if ($checkin) {
                $isUpcoming = (strtotime($checkin) >= strtotime('today'));
            }
          ?>
            <div class="ll-trip-card d-flex flex-column flex-md-row align-items-md-center gap-3">
              <div class="ll-emoji-wrapper flex-shrink-0" style="background: <?= $b['gradient'] ?? 'linear-gradient(135deg, #1e4c2b, #437c53)' ?>; color: #fff;">
                <?= $b['emoji'] ?? '🏝️' ?>
              </div>
              
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                  <h5 class="mb-0 fw-bold text-truncate" style="color: var(--deep); max-width: 250px;"><?= htmlspecialchars($destName) ?></h5>
                  <span class="text-muted small" style="font-family: monospace; background: var(--cream); padding: 2px 8px; border-radius: 4px; border: 1px solid var(--border);">
                    ID: <?= htmlspecialchars($refId) ?>
                  </span>
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-x-3 gap-y-1 mb-2" style="column-gap: 15px !important;">
                  <span class="ll-meta-item"><i class="bi bi-building"></i> <strong><?= htmlspecialchars($hotelName) ?></strong></span>
                  <span class="ll-meta-item"><i class="bi bi-moon-stars"></i> <?= $nights ?> night<?= $nights != 1 ? 's' : '' ?></span>
                  <span class="ll-meta-item"><i class="bi bi-door-open"></i> <?= $rooms ?> room<?= $rooms != 1 ? 's' : '' ?></span>
                </div>

                <div class="text-muted" style="font-size: 0.85rem;">
                  <i class="bi bi-calendar-range me-1"></i>
                  <strong>In:</strong> <?= htmlspecialchars($checkin ?? 'N/A') ?> 
                  <span class="mx-1">·</span> 
                  <strong>Out:</strong> <?= htmlspecialchars($checkout) ?>
                </div>
              </div>

              <div class="d-flex flex-row flex-md-column justify-content-between align-items-center align-items-md-end gap-2 flex-shrink-0 pt-2 pt-md-0 border-top border-md-top-0" style="min-width: 120px;">
                <span class="ll-badge-status <?= $isUpcoming ? 'll-badge-upcoming' : 'll-badge-past' ?>">
                  <?= $isUpcoming ? 'Upcoming' : 'Past Trip' ?>
                </span>
                <div class="fw-bold fs-5" style="color: var(--primary);">₱<?= number_format($price) ?></div>
                <a href="receipt.php?ref=<?= urlencode($refId) ?>" class="text-decoration-none small fw-bold text-accent">View Receipt →</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
// FORCE CLEAR OLD JS STORAGE (Para hindi na mag-conflict)
sessionStorage.removeItem('lbl_bookings');

window.DESTINATIONS = <?= json_encode($destinations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function doHomeSearch() {
  const dest   = document.getElementById('homeSearchDest').value;
  const budget = document.getElementById('homeSearchBudget').value;
  let url = 'destinations.php?';
  if (dest)   url += 'dest='   + encodeURIComponent(dest)   + '&';
  if (budget) url += 'budget=' + encodeURIComponent(budget);
  window.location.href = url;
}
</script>
