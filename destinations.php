<?php
require_once 'data.php';
include 'includes/amenity-icons.php';

$destId  = $_GET['dest']   ?? '';
$region  = $_GET['region'] ?? '';
$budget  = $_GET['budget'] ?? '';
$sortBy  = $_GET['sort']   ?? 'recommended';
$dest    = $destId ? getDestById($destId) : null;

// Filter destinations
$filteredDests = $destinations;
if ($region) {
  $filteredDests = array_filter($filteredDests, fn($d) => strtolower($d['region']) === strtolower($region));
}
if ($budget === 'low')  $filteredDests = array_filter($filteredDests, fn($d) => $d['price'] < 5000);
if ($budget === 'mid')  $filteredDests = array_filter($filteredDests, fn($d) => $d['price'] >= 5000 && $d['price'] <= 7500);
if ($budget === 'high') $filteredDests = array_filter($filteredDests, fn($d) => $d['price'] > 7500);

$pageTitle  = $dest ? 'Hotels in ' . $dest['name'] . ' — LakbayLokal' : 'All Destinations — LakbayLokal';
$activePage = 'destinations';
$rootPath   = '';
include 'includes/header.php';
?>

<div class="page-wrapper">

  <?php if ($dest): ?>
    <!-- ========== SINGLE DESTINATION: HOTEL LIST ========== -->

    <div class="breadcrumb-wrapper">
      <button class="breadcrumb-back" onclick="window.history.back()" title="Go back to previous page" aria-label="Back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
      </button>
      <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span class="breadcrumb-sep">›</span>
        <a href="destinations.php">Destinations</a>
        <span class="breadcrumb-sep">›</span>
        <span><?= htmlspecialchars($dest['name']) ?></span>
      </div>
    </div>

    <!-- Destination Hero -->
    <div class="hotels-page-hero" style="background: <?= $dest['gradient'] ?>;">
      <div class="hero-overlay"></div>
      <div class="hero-inner">
        <h1><?= $dest['emoji'] ?> Hotels in <?= htmlspecialchars($dest['name']) ?></h1>
        <p><?= htmlspecialchars($dest['tagline']) ?></p>
        <p style="font-size:0.9rem;opacity:0.8;max-width:540px;margin-top:0.4rem;">
          <?= htmlspecialchars($dest['desc']) ?>
        </p>
        <div class="dest-tags">
          <span class="tag">📍 <?= htmlspecialchars($dest['region']) ?></span>
          <span class="tag">🏨 <?= count($dest['hotels']) ?> Hotels</span>
          <span class="tag">💰 From ₱<?= number_format($dest['price_from']) ?>/night</span>
          <span class="tag">🎯 <?= count($dest['acts']) ?> Activities</span>
        </div>
      </div>
    </div>

    <!-- Sort Bar -->
    <div class="dest-filters">
      <span class="filter-label">Sort by:</span>
      <?php $sorts = ['recommended' => 'Recommended', 'price-asc' => 'Price: Low to High', 'price-desc' => 'Price: High to Low', 'rating' => 'Top Rated']; ?>
      <?php foreach ($sorts as $val => $label): ?>
        <a href="?dest=<?= $destId ?>&sort=<?= $val ?>" class="filter-btn <?= $sortBy === $val ? 'active' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Hotel List + Sidebar -->
    <div class="hotels-layout">

      <!-- Sidebar -->
      <aside class="hotels-sidebar">
        <h3>Filters</h3>
        <div class="sidebar-group">
          <label>Star Rating</label>
          <div class="star-filter">
            <?php foreach ([2, 3, 4, 5] as $s): ?>
              <button class="star-btn" onclick="filterStars(<?= $s ?>)"><?= $s ?>★</button>
            <?php endforeach; ?>
            <button class="star-btn" onclick="filterStars(0)" style="font-size:0.75rem;">All</button>
          </div>
        </div>
        <div class="sidebar-group">
          <label>Max Price / Night</label>
          <input type="range" id="priceRange" min="2000" max="20000" step="500" value="20000" oninput="filterPrice(this.value)">
          <div class="price-range-display">
            <span>₱2,000</span>
            <span id="priceDisplay">Up to ₱20,000</span>
          </div>
        </div>
        <div class="sidebar-group">
          <label>Other Destinations</label>
          <select onchange="if(this.value) location.href='?dest='+this.value">
            <option value="">— Switch Destination —</option>
            <?php foreach ($destinations as $d): ?>
              <option value="<?= $d['id'] ?>" <?= $d['id'] === $destId ? 'selected' : '' ?>>
                <?= $d['emoji'] ?> <?= htmlspecialchars($d['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <a href="destinations.php" style="display:block;text-align:center;margin-top:0.5rem;font-size:0.85rem;color:var(--primary);text-decoration:none;">← All Destinations</a>

        <!-- Activities Preview -->
        <div class="sidebar-group" style="margin-top:1.5rem;">
          <label>Available Activities</label>
          <div style="display:flex;flex-direction:column;gap:0.5rem;margin-top:0.25rem;">
            <?php foreach ($dest['acts'] as $act): ?>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;border-bottom:1px dashed var(--border);font-size:0.82rem;">
                <span style="color:var(--deep);"><?= htmlspecialchars($act['name']) ?></span>
                <span style="color:var(--primary);font-weight:700;">₱<?= number_format($act['price']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </aside>

      <!-- Hotel Cards -->
      <div>
        <p style="font-size:0.85rem;color:var(--muted);margin-bottom:1.2rem;">
          Showing <strong><?= count($dest['hotels']) ?></strong> hotels in <strong><?= htmlspecialchars($dest['name']) ?></strong>
        </p>
        <div class="hotel-cards" id="hotelCards">
          <?php
          $hotels = $dest['hotels'];
          if ($sortBy === 'price-asc')  usort($hotels, fn($a, $b) => $a['price'] - $b['price']);
          if ($sortBy === 'price-desc') usort($hotels, fn($a, $b) => $b['price'] - $a['price']);
          if ($sortBy === 'rating')     usort($hotels, fn($a, $b) => $b['rating'] <=> $a['rating']);

          foreach ($hotels as $h):
            $stars = str_repeat('★', $h['stars']) . str_repeat('☆', 5 - $h['stars']);
            $hotelBackground = isset($h['image'])
              ? "linear-gradient(135deg, rgba(0,0,0,0.25), rgba(0,0,0,0.05)), url('{$h['image']}') center/cover no-repeat"
              : $dest['gradient'];
          ?>
            <a href="hotel.php?dest=<?= $destId ?>&id=<?= $h['id'] ?>" class="hotel-card" data-price="<?= $h['price'] ?>" data-stars="<?= $h['stars'] ?>">
              <div class="hotel-card-img" style="background: <?= $hotelBackground ?>;">
                <div class="hotel-card-stars"><?= $stars ?></div>
              </div>
              <div class="hotel-card-body">
                <h3><?= htmlspecialchars($h['name']) ?></h3>
                <div class="hotel-card-location">📍 <?= htmlspecialchars($h['location']) ?></div>
                <div class="hotel-card-desc"><?= htmlspecialchars($h['desc']) ?></div>
                <div class="hotel-card-amenities">
                  <?php foreach (array_slice($h['amenities'], 0, 4) as $am): ?>
                    <span class="amenity-tag"><?= $amenityIcons[$am] ?? '✓' ?> <?= htmlspecialchars($am) ?></span>
                  <?php endforeach; ?>
                  <?php if (count($h['amenities']) > 4): ?>
                    <span class="amenity-tag">+<?= count($h['amenities']) - 4 ?> more</span>
                  <?php endif; ?>
                </div>
                <div class="hotel-card-footer">
                  <div class="hotel-rating">
                    <span class="rating-score"><?= $h['rating'] ?></span>
                    <span class="rating-count"><?= $h['reviews'] ?> reviews</span>
                  </div>
                  <div>
                    <div class="hotel-price">
                      <span class="hotel-price-num">₱<?= number_format($h['price']) ?></span>
                      <span class="hotel-price-label">per night</span>
                    </div>
                    <span class="hotel-card-cta">View Details →</span>
                  </div>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  <?php else: ?>
    <!-- ========== ALL DESTINATIONS LISTING ========== -->

    <div class="dest-page-hero">
      <h1>All Destinations</h1>
      <p>Explore hotels across <?= count($destinations) ?> stunning Philippine destinations</p>
    </div>

    <!-- Filters -->
    <div class="dest-filters">
      <span class="filter-label">Filter:</span>
      <a href="destinations.php" class="filter-btn <?= !$region && !$budget ? 'active' : '' ?>">All</a>
      <?php $regions = array_unique(array_column($destinations, 'region')); ?>
      <?php foreach ($regions as $r): ?>
        <a href="?region=<?= urlencode($r) ?>" class="filter-btn <?= strtolower($region) === strtolower($r) ? 'active' : '' ?>"><?= htmlspecialchars($r) ?></a>
      <?php endforeach; ?>
      <span class="filter-label" style="margin-left:0.5rem;">Budget:</span>
      <a href="?budget=low" class="filter-btn <?= $budget === 'low'  ? 'active' : '' ?>">Under ₱5,000</a>
      <a href="?budget=mid" class="filter-btn <?= $budget === 'mid'  ? 'active' : '' ?>">₱5,000–₱7,500</a>
      <a href="?budget=high" class="filter-btn <?= $budget === 'high' ? 'active' : '' ?>">Above ₱7,500</a>
    </div>

    <div class="dest-list-section">
      <div class="dest-grid">
        <?php foreach ($filteredDests as $d): ?>
          <a href="destinations.php?dest=<?= $d['id'] ?>" class="dest-card">
            <div class="dest-img" style="background: <?= $d['gradient'] ?>;">
              <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:4rem"><?= $d['emoji'] ?></div>
              <div class="dest-badge"><?= htmlspecialchars($d['region']) ?></div>
              <div class="dest-price-badge">From ₱<?= number_format($d['price_from']) ?></div>
            </div>
            <div class="dest-body">
              <h3><?= htmlspecialchars($d['name']) ?></h3>
              <p><?= htmlspecialchars($d['tagline']) ?></p>
              <div class="dest-meta">
                <span class="dest-meta-hotels">🏨 <?= count($d['hotels']) ?> Hotels</span>
                <span>Browse →</span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

  <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

<script>
  function filterStars(n) {
    document.querySelectorAll('.star-btn').forEach(b => b.classList.remove('active'));
    if (event && event.target) event.target.classList.add('active');
    document.querySelectorAll('.hotel-card').forEach(card => {
      card.style.display = (n === 0 || parseInt(card.dataset.stars) >= n) ? 'flex' : 'none';
    });
  }

  function filterPrice(val) {
    document.getElementById('priceDisplay').textContent = 'Up to ₱' + parseInt(val).toLocaleString();
    document.querySelectorAll('.hotel-card').forEach(card => {
      card.style.display = parseInt(card.dataset.price) <= parseInt(val) ? 'flex' : 'none';
    });
  }
</script>