<!-- Discount Modal -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <span style="font-size: 3rem;">🎉</span>
        <h3 class="mt-3">Mabuhay! Narito ang iyong Welcome Gift!</h3>
        <p class="text-muted">Gamitin ang code na ito para sa bawas-presyo sa iyong unang booking.</p>
        <div class="p-3 my-3 bg-light border rounded">
          <strong style="font-size: 1.5rem; color: var(--primary);" id="promoCodeText">LAKBAYLOKAL10</strong>
        </div>
        <p class="small text-danger">*Minsan lang ito lalabas. I-copy o i-screenshot mo na!</p>
        <button type="button" class="btn btn-hero-primary w-100" data-bs-dismiss="modal">Salamat! I-explore ang Pinas</button>
      </div>
    </div>
  </div>
</div>

<!-- ======= HOME HERO ======= -->
<section class="hero">
  <div class="container-fluid px-5">
    <div class="hero-content">
      <h1>Discover the <em>Beauty</em> of the Philippines</h1>
      <p>Handpicked hotel stays paired with unforgettable local activities — all in one seamless booking platform.</p>
      <div class="hero-btns">
        <a href="destinations.php" class="btn btn-primary btn-lg btn-custom">
          Explore Destinations
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#about" class="btn btn-outline btn-lg btn-custom">How It Works</a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num"><?= count($destinations) ?>+</div>
          <div class="stat-label">Destinations</div>
        </div>
        <div class="stat-item">
          <div class="stat-num"><?= array_sum(array_column($destinations, 'hotel_count')) ?>+</div>
          <div class="stat-label">Hotels</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">100%</div>
          <div class="stat-label">Local Picks</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======= HOME SEARCH ======= -->
<section class="search-section-wrapper">
  <div class="container-fluid px-5">
    <div class="row g-3 align-items-end">
      
      <div class="col-12 col-md-6 col-lg-3">
        <div class="search-group">
          <label for="homeSearchDest" class="form-label">Destination</label>
          <select id="homeSearchDest" class="form-select">
            <option value="">All Destinations</option>
            <?php foreach ($destinations as $d): ?>
              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="search-group">
          <label for="homeSearchBudget" class="form-label">Budget</label>
          <select id="homeSearchBudget" class="form-select">
            <option value="">Any Budget</option>
            <option value="low">Under ₱5,000</option>
            <option value="mid">₱5,000 – ₱7,500</option>
            <option value="high">Above ₱7,500</option>
          </select>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="search-group">
          <label for="homeCheckin" class="form-label">Check-in Date</label>
          <input type="date" id="homeCheckin" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3 d-flex">
        <button class="search-btn w-100" onclick="doHomeSearch()">
          <i class="bi bi-search"></i>
          <span>Search</span>
        </button>
      </div>

    </div>
  </div>
</section>

<!-- ======= FEATURED DESTINATIONS ======= -->
<section class="section">
  <div class="container-fluid px-5">
    <div class="section-header">
      <h2 class="section-title">Featured <span>Destinations</span></h2>
      <a href="destinations.php" class="see-all">See all →</a>
    </div>
    <div class="row g-4">
      <?php foreach (array_slice($destinations, 0, 3) as $d): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <a href="destinations.php?dest=<?= $d['id'] ?>" class="dest-card">
            <div class="dest-img" style="background: <?= $d['gradient'] ?>;">
              <div class="dest-img-emoji">
                <?= $d['emoji'] ?>
              </div>
              <div class="dest-badge"><?= htmlspecialchars($d['region']) ?></div>
              <div class="dest-price-badge">From ₱<?= number_format($d['price_from']) ?></div>
            </div>
            <div class="dest-body">
              <h3><?= htmlspecialchars($d['name']) ?></h3>
              <div class="dest-meta">📍 <?= htmlspecialchars($d['tagline']) ?></div>
              <div class="dest-activities">
                <?php
                  $previewActs = getActivitiesByDest($conn, $d['id']);
                  foreach (array_slice($previewActs, 0, 3) as $a):
                ?>
                  <span class="act-tag"><?= htmlspecialchars($a['name']) ?></span>
                <?php endforeach; ?>
              </div>
              <div class="dest-footer">
                <div class="dest-footer-price">Base: <strong>₱<?= number_format($d['price']) ?></strong></div>
                <span class="book-btn">Book Now</span>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======= AMENITIES / WHY LAKBAYLOKAL ======= -->
<section class="amenities-section" id="about">
  <div class="container-fluid px-4 px-md-5">
    <div class="row align-items-center mb-5">
      <div class="col-lg-5">
        <p class="section-eyebrow">More Than Just a Place to Sleep</p>
        <h2 class="section-title" style="font-size:clamp(1.6rem,3vw,2.4rem); line-height:1.2;">
          Why Choose <span>LakbayLokal?</span>
        </h2>
      </div>
      <div class="col-lg-7 mt-3 mt-lg-0">
        <p class="text-muted" style="font-size:1rem; max-width:520px;">
          Whether you're here to relax, explore, or immerse yourself in local culture — we've thought of every detail to make your Philippine journey seamless and unforgettable.
        </p>
      </div>
    </div>
 
    <div class="row g-3">
      <?php
        $features = [
          ['icon'=>'🗺️', 'num'=>'#01', 'title'=>'All-in-One Planning', 'desc'=>'Hotel + activities bundled in one convenient booking flow. No juggling multiple apps.'],
          ['icon'=>'🏨', 'num'=>'#02', 'title'=>'Curated Hotels', 'desc'=>'Handpicked accommodations with detailed info, ratings, and flexible policies.'],
          ['icon'=>'🤝', 'num'=>'#03', 'title'=>'Local Experiences', 'desc'=>'Authentic activities curated per destination — from island hopping to heritage tours.'],
          ['icon'=>'📱', 'num'=>'#04', 'title'=>'Easy Booking', 'desc'=>'Book in minutes from any device — desktop or mobile, fast and commitment-free.'],
          ['icon'=>'🛎️', 'num'=>'#05', 'title'=>'Concierge-Level Service', 'desc'=>'Enjoy seamless support with thoughtful touches throughout your stay.'],
          ['icon'=>'🌊', 'num'=>'#06', 'title'=>'Beach & Nature Access', 'desc'=>'Sun, sand, and sea — breathtaking natural spots just minutes from your booking.'],
          ['icon'=>'🍜', 'num'=>'#07', 'title'=>'Local Dining Spots', 'desc'=>'Discover top-rated restaurants, cafes, and street food all within walking distance.'],
          ['icon'=>'🧭', 'num'=>'#08', 'title'=>'Explore Like a Local', 'desc'=>'Insider tips and curated guides so you see the Philippines beyond the usual.'],
        ];
        foreach ($features as $f):
      ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="amenity-card">
          <div class="amenity-num"><?= $f['num'] ?></div>
          <div class="amenity-icon"><?= $f['icon'] ?></div>
          <h5 class="amenity-title"><?= $f['title'] ?></h5>
          <p class="amenity-desc"><?= $f['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
 
<!-- ======= BOOKING CTA SECTION ======= -->
<section class="cta-section">
  <div class="container-fluid px-4 px-md-5">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <p class="cta-eyebrow">Secure your dates in just a few clicks</p>
        <h2 class="cta-title">Ready to Book <br>Your <em>Adventure?</em></h2>
        <p class="cta-desc">Check availability, choose your destination, and reserve your perfect Philippine experience instantly. Fast, easy, and commitment-free until you confirm.</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="destinations.php" class="btn btn-hero-primary">
            <i class="bi bi-search me-2"></i>Browse Destinations
          </a>
          <a href="#mytrips" class="btn btn-hero-ghost">
            <i class="bi bi-suitcase me-2"></i>View My Trips
          </a>
        </div>
        <!-- Feature bullets -->
        <div class="cta-features mt-4">
          <div class="cta-feature-item"><i class="bi bi-check-circle-fill text-success me-2"></i>No hidden fees</div>
          <div class="cta-feature-item"><i class="bi bi-check-circle-fill text-success me-2"></i>Instant confirmation</div>
          <div class="cta-feature-item"><i class="bi bi-check-circle-fill text-success me-2"></i>Local expert picks</div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="cta-visual-grid">
          <?php
            $ctaFeatures = [
              ['icon'=>'✈️', 'label'=>'Easy Flights'],
              ['icon'=>'🏖️', 'label'=>'Beach Stays'],
              ['icon'=>'🍽️', 'label'=>'Local Food'],
              ['icon'=>'🗺️', 'label'=>'Guided Tours'],
            ];
            foreach ($ctaFeatures as $cf):
          ?>
          <div class="cta-visual-card">
            <div class="cta-visual-icon"><?= $cf['icon'] ?></div>
            <div class="cta-visual-label"><?= $cf['label'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
 
<!-- ======= INLINE STYLES FOR NEW SECTIONS ======= -->
<style>
/* ── HERO CAROUSEL ── */
.hero-carousel-section { padding-top: 68px; }
.hero-slide { position: relative; }
.hero-slide-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);
  z-index: 1;
}
.hero-slide-emoji-bg {
  position: absolute; right: -2%; bottom: -2%;
  font-size: clamp(12rem, 30vw, 22rem);
  opacity: 0.06;
  line-height: 1;
  pointer-events: none;
  z-index: 1;
  user-select: none;
}
.hero-pill {
  background: rgba(196,96,42,0.2);
  color: var(--primary-light);
  border: 1px solid rgba(196,96,42,0.35);
  padding: 0.35rem 1rem;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}
.hero-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(2.4rem, 5vw, 3.8rem);
  line-height: 1.1;
  color: #fff;
  font-weight: 700;
  margin-bottom: 1rem;
}
.hero-title em { color: var(--primary-light); font-style: italic; }
.hero-subtitle { font-size: 1.05rem; color: rgba(255,255,255,0.75); max-width: 480px; }
 
/* Hero buttons */
.btn-hero-primary {
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.85rem 2rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-family: 'DM Sans', sans-serif;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}
.btn-hero-primary:hover { background: var(--primary-light); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(196,96,42,0.4); color: white; }
.btn-hero-ghost {
  background: rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.9);
  border: 1.5px solid rgba(255,255,255,0.3);
  border-radius: 50px;
  padding: 0.85rem 2rem;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  font-family: 'DM Sans', sans-serif;
  text-decoration: none;
  backdrop-filter: blur(4px);
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-hero-ghost:hover { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.5); color: white; }
 
/* Hero stats */
.hero-stats-row {
  display: flex;
  align-items: center;
  gap: 0;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(255,255,255,0.15);
}
.hero-stat-item { text-align: left; padding-right: 2rem; }
.hero-stat-num { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: var(--primary-light); line-height: 1; }
.hero-stat-label { font-size: 0.78rem; color: rgba(255,255,255,0.6); margin-top: 2px; }
.hero-stat-divider { width: 1px; height: 36px; background: rgba(255,255,255,0.15); margin-right: 2rem; flex-shrink: 0; }
 
/* Hero dest preview cards */
.hero-dest-preview { display: flex; gap: 16px; align-items: flex-start; }
.hero-preview-card {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: var(--radius);
  overflow: hidden;
  width: 175px;
  text-decoration: none;
  color: white;
  transition: transform 0.3s, box-shadow 0.3s;
}
.hero-preview-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.3); }
.hero-preview-card--offset { margin-top: 48px; }
.hero-preview-img {
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
}
.hero-preview-body { padding: 12px; }
.hero-preview-name { font-weight: 700; font-size: 0.9rem; margin-bottom: 2px; }
.hero-preview-region { font-size: 0.75rem; color: rgba(255,255,255,0.65); margin-bottom: 6px; }
.hero-preview-price { font-size: 0.8rem; font-weight: 700; color: var(--primary-light); }
 
/* Carousel controls */
.hero-carousel-btn {
  width: 48px;
  height: 48px;
  background: rgba(255,255,255,0.15) !important;
  border: 1px solid rgba(255,255,255,0.3) !important;
  border-radius: 50% !important;
  backdrop-filter: blur(8px);
  top: 50%;
  transform: translateY(-50%);
  opacity: 1 !important;
  transition: background 0.2s !important;
}
.hero-carousel-btn:hover { background: rgba(255,255,255,0.3) !important; }
.carousel-control-prev.hero-carousel-btn { left: 20px; }
.carousel-control-next.hero-carousel-btn { right: 20px; }
.hero-carousel-arrow {
  font-size: 1.8rem;
  color: white;
  line-height: 1;
  display: block;
}
 
/* Carousel indicators */
.hero-carousel-indicators {
  bottom: 100px !important;
  gap: 8px;
}
.hero-carousel-indicators button {
  width: 28px !important;
  height: 4px !important;
  border-radius: 2px !important;
  background: rgba(255,255,255,0.4) !important;
  border: none !important;
  transition: all 0.3s !important;
}
.hero-carousel-indicators button.active {
  width: 48px !important;
  background: var(--primary-light) !important;
}
 
/* ── FLOATING SEARCH ── */
.hero-search-float {
  position: relative;
  z-index: 10;
  margin-top: -60px;
  padding-bottom: 0;
}
.hero-search-card {
  background: white;
  border-radius: var(--radius);
  padding: 1.5rem 2rem;
  box-shadow: 0 12px 40px rgba(26,18,8,0.15);
  border: 1px solid var(--border);
}
 
/* ── DESTINATION CARDS V2 ── */
.dest-card-v2 { position: relative; }
.dest-card-num {
  position: absolute;
  top: 12px;
  right: 12px;
  font-family: 'Playfair Display', serif;
  font-size: 0.85rem;
  font-weight: 700;
  color: rgba(255,255,255,0.5);
  z-index: 3;
  letter-spacing: 1px;
}
.dest-hover-overlay {
  position: absolute;
  inset: 0;
  background: rgba(196,96,42,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
  border-radius: 0;
}
.dest-card:hover .dest-hover-overlay { opacity: 1; }
.dest-hover-cta {
  color: white;
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 0.5px;
}
.dest-img-emoji {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 4rem;
  z-index: 1;
}
 
/* ── MINI CAROUSEL ── */
.dest-mini-track-wrapper { cursor: grab; user-select: none; }
.dest-mini-track-wrapper:active { cursor: grabbing; }
.dest-mini-track { transition: transform 0.4s cubic-bezier(0.25,1,0.5,1); }
.dest-mini-card {
  background: white;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  overflow: hidden;
  width: 200px;
  transition: all 0.3s;
  color: var(--deep);
}
.dest-mini-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-hover); border-color: var(--primary); color: var(--deep); }
.dest-mini-img {
  height: 110px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.dest-mini-body { padding: 0.85rem; }
.dest-mini-name { font-weight: 700; font-size: 0.9rem; margin-bottom: 2px; }
.dest-mini-region { font-size: 0.75rem; color: var(--muted); margin-bottom: 6px; }
.dest-mini-price { font-size: 0.85rem; font-weight: 700; color: var(--primary); }
.dest-mini-btn {
  width: 36px; height: 36px;
  border-radius: 50%;
  border: 1.5px solid var(--border);
  background: white;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
  color: var(--deep);
}
.dest-mini-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-pale); }
 
/* ── AMENITIES SECTION ── */
.amenities-section {
  background: var(--cream);
  padding: 5rem 0;
}
.section-eyebrow {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 0.5rem;
}
.amenity-card {
  background: white;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.5rem;
  height: 100%;
  position: relative;
  transition: all 0.3s;
  overflow: hidden;
}
.amenity-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-hover);
  border-color: var(--primary);
}
.amenity-num {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: rgba(26,18,8,0.06);
  line-height: 1;
}
.amenity-icon { font-size: 2.5rem; margin-bottom: 0.85rem; display: block; }
.amenity-title { font-weight: 700; font-size: 0.95rem; color: var(--deep); margin-bottom: 0.5rem; }
.amenity-desc { font-size: 0.8rem; color: var(--muted); line-height: 1.5; margin: 0; }
 
/* ── CTA SECTION ── */
.cta-section {
  background: var(--deep);
  padding: 5rem 0;
  position: relative;
  overflow: hidden;
}
.cta-section::before {
  content: '';
  position: absolute;
  top: -200px; right: -200px;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(196,96,42,0.12) 0%, transparent 70%);
  border-radius: 50%;
}
.cta-eyebrow { font-size: 0.8rem; font-weight: 700; color: var(--primary-light); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.75rem; }
.cta-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(2rem, 4vw, 3rem);
  color: white;
  font-weight: 700;
  line-height: 1.15;
  margin-bottom: 1rem;
}
.cta-title em { color: var(--primary-light); font-style: italic; }
.cta-desc { color: rgba(255,255,255,0.65); font-size: 1rem; max-width: 440px; line-height: 1.7; }
.cta-features { display: flex; flex-direction: column; gap: 0.5rem; }
.cta-feature-item { color: rgba(255,255,255,0.8); font-size: 0.9rem; }
 
.cta-visual-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.cta-visual-card {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: var(--radius);
  padding: 2rem 1.5rem;
  text-align: center;
  transition: all 0.3s;
  backdrop-filter: blur(4px);
}
.cta-visual-card:hover {
  background: rgba(255,255,255,0.1);
  border-color: rgba(196,96,42,0.4);
  transform: translateY(-4px);
}
.cta-visual-icon { font-size: 2.8rem; display: block; margin-bottom: 0.75rem; }
.cta-visual-label { color: rgba(255,255,255,0.75); font-size: 0.9rem; font-weight: 600; }
 
/* ── OVERRIDE: section-header for new eyebrow layout ── */
.section-header { display: flex; align-items: flex-end; justify-content: space-between; }
 
@media (max-width: 768px) {
  .hero-search-float { margin-top: -20px; }
  .hero-carousel-indicators { bottom: 80px !important; }
  .cta-visual-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
  .cta-visual-card { padding: 1.2rem 1rem; }
  .cta-visual-icon { font-size: 2rem; }
}
@media (max-width: 576px) {
  .hero-carousel-btn { display: none !important; }
}
</style>
 
<script>
// ── Mini carousel drag/scroll ──
(function () {
  const track = document.getElementById('destMiniTrack');
  const prevBtn = document.getElementById('miniPrev');
  const nextBtn = document.getElementById('miniNext');
  if (!track) return;
 
  const cardWidth = 216; // 200px + 16px gap
  let currentOffset = 0;
 
  function getMaxOffset() {
    const wrapper = track.parentElement;
    return Math.max(0, track.scrollWidth - wrapper.offsetWidth);
  }
 
  function slideTo(offset) {
    currentOffset = Math.max(0, Math.min(offset, getMaxOffset()));
    track.style.transform = `translateX(-${currentOffset}px)`;
  }
 
  if (nextBtn) nextBtn.addEventListener('click', () => slideTo(currentOffset + cardWidth * 2));
  if (prevBtn) prevBtn.addEventListener('click', () => slideTo(currentOffset - cardWidth * 2));
 
  // Drag support
  let isDragging = false, startX = 0, startOffset = 0;
  track.addEventListener('mousedown', e => { isDragging = true; startX = e.clientX; startOffset = currentOffset; });
  document.addEventListener('mousemove', e => {
    if (!isDragging) return;
    slideTo(startOffset - (e.clientX - startX));
  });
  document.addEventListener('mouseup', () => isDragging = false);
})();

document.addEventListener("DOMContentLoaded", function () {

  const today = new Date().toDateString();
  const lastShown = localStorage.getItem("discountShownDate");

  // I-check kung hindi pa siya naipapakita today
  if (lastShown !== today) {

    setTimeout(function () {
      var myModal = new bootstrap.Modal(document.getElementById('discountModal'));
      myModal.show();

      // i-save yung date today
      localStorage.setItem("discountShownDate", today);
    }, 3000);

  }
});
</script>