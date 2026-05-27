<section class="dest-section" id="destinations">
  <div class="section-header">
    <div class="section-tag">10 Amazing Places</div>
    <h2 class="section-title">Explore <em>Destinations</em></h2>
    <p class="section-sub">From highland retreats to tropical paradise — something for every kind of traveler.</p>
  </div>
  <div class="region-tabs">
    <button class="tab-btn active" onclick="filterDest('all', this)">All Regions</button>
    <button class="tab-btn" onclick="filterDest('luzon', this)">🌿 Luzon</button>
    <button class="tab-btn" onclick="filterDest('visayas', this)">🌊 Visayas</button>
    <button class="tab-btn" onclick="filterDest('mindanao', this)">🏔️ Mindanao</button>
  </div>
  <div class="dest-grid" id="destGrid">

    <!-- LUZON -->
    <div class="dest-card" data-region="luzon">
      <div class="dest-img">
        <div class="dest-img-bg bg-baguio"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Luzon</div>
        <div class="dest-img-info"><h3>Baguio City</h3><span>Benguet, CAR</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🌲 Highlands</span><span class="tag">🍓 Strawberries</span><span class="tag">☕ Cafés</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8 <small style="color:#999;font-weight:400">(234)</small></div><button class="btn-explore" onclick="selectDest('Baguio City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="luzon">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#7b6b43,#4a3728)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Luzon</div>
        <div class="dest-img-info"><h3>Vigan City</h3><span>Ilocos Sur</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏛️ Heritage</span><span class="tag">🛺 Kalesa</span><span class="tag">🍶 Empanada</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.7</div><button class="btn-explore" onclick="selectDest('Vigan City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="luzon">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#006994,#01406b)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Luzon</div>
        <div class="dest-img-info"><h3>Palawan</h3><span>MIMAROPA</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏝️ Islands</span><span class="tag">🤿 Diving</span><span class="tag">🦅 Wildlife</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.9</div><button class="btn-explore" onclick="selectDest('Palawan')">Explore →</button></div>
      </div>
    </div>

    <!-- VISAYAS -->
    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg bg-boracay"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Boracay</h3><span>Aklan</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏖️ White Beach</span><span class="tag">🪂 Parasailing</span><span class="tag">🏄 Water Sports</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.9 <small style="color:#999;font-weight:400">(512)</small></div><button class="btn-explore" onclick="selectDest('Boracay')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg bg-cebu"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Cebu City</h3><span>Cebu</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">⛪ History</span><span class="tag">🦈 Whale Sharks</span><span class="tag">🌊 Canyoneering</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8</div><button class="btn-explore" onclick="selectDest('Cebu City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#1a6b8a,#0d3d54)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Siargao</h3><span>Surigao del Norte</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🏄 Surfing</span><span class="tag">🌴 Lagoons</span><span class="tag">🐚 Snorkeling</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8</div><button class="btn-explore" onclick="selectDest('Siargao')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="visayas">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#4a8c6f,#2c6e49)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Visayas</div>
        <div class="dest-img-info"><h3>Bohol</h3><span>Bohol</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">⛰️ Chocolate Hills</span><span class="tag">🦎 Tarsier</span><span class="tag">⛵ River Cruise</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.7</div><button class="btn-explore" onclick="selectDest('Bohol')">Explore →</button></div>
      </div>
    </div>

    <!-- MINDANAO -->
    <div class="dest-card" data-region="mindanao">
      <div class="dest-img">
        <div class="dest-img-bg bg-bukidnon"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Mindanao</div>
        <div class="dest-img-info"><h3>Bukidnon</h3><span>Mindanao</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🌾 Highlands</span><span class="tag">🏕️ Camping</span><span class="tag">🌺 Festivals</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.6</div><button class="btn-explore" onclick="selectDest('Bukidnon')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="mindanao">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#5c8a60,#2e5232)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Mindanao</div>
        <div class="dest-img-info"><h3>Davao City</h3><span>Davao del Sur</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🦅 Eagles</span><span class="tag">🍌 Fruits</span><span class="tag">🌋 Mt. Apo</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.7</div><button class="btn-explore" onclick="selectDest('Davao City')">Explore →</button></div>
      </div>
    </div>

    <div class="dest-card" data-region="mindanao">
      <div class="dest-img">
        <div class="dest-img-bg" style="background:linear-gradient(135deg,#0077b6,#023e58)"></div>
        <div class="dest-img-overlay"></div>
        <div class="dest-region-badge">Mindanao</div>
        <div class="dest-img-info"><h3>Camiguin</h3><span>Camiguin</span></div>
      </div>
      <div class="dest-body">
        <div class="dest-tags"><span class="tag">🌋 Volcanoes</span><span class="tag">♨️ Hot Springs</span><span class="tag">⛪ Ruins</span></div>
        <div class="dest-meta"><div class="dest-rating">⭐ 4.8</div><button class="btn-explore" onclick="selectDest('Camiguin')">Explore →</button></div>
      </div>
    </div>

  </div>
</section>
