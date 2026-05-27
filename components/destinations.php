<div class="page" id="page-destinations">
  <section class="section">
    <div class="section-header">
      <h2 class="section-title">All <span>Destinations</span></h2>
    </div>

    <!-- FILTER BAR -->
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:2rem;">
      <button class="filter-btn active" onclick="filterDest('all',this)"        style="background:var(--primary);color:white;border:none;border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">All</button>
      <button class="filter-btn"        onclick="filterDest('low',this)"         style="background:white;color:var(--muted);border:1.5px solid var(--border);border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">Under ₱5,000</button>
      <button class="filter-btn"        onclick="filterDest('mid',this)"         style="background:white;color:var(--muted);border:1.5px solid var(--border);border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">₱5,000–₱7,500</button>
      <button class="filter-btn"        onclick="filterDest('high',this)"        style="background:white;color:var(--muted);border:1.5px solid var(--border);border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">Above ₱7,500</button>
      <button class="filter-btn"        onclick="filterDest('luzon',this)"       style="background:white;color:var(--muted);border:1.5px solid var(--border);border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">Luzon</button>
      <button class="filter-btn"        onclick="filterDest('visayas',this)"     style="background:white;color:var(--muted);border:1.5px solid var(--border);border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">Visayas</button>
      <button class="filter-btn"        onclick="filterDest('mindanao',this)"    style="background:white;color:var(--muted);border:1.5px solid var(--border);border-radius:50px;padding:0.45rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">Mindanao</button>
    </div>

    <div class="dest-grid" id="allDestGrid"></div>
  </section>
</div>