<<<<<<< HEAD
// script.js — LakbayLokal frontend logic
// Expects DESTINATIONS to be injected as a global variable from index.php

let currentDest = null;
let selectedHotel = null;
let checkedActivities = new Set();
let bookings = [];
let prevPage = 'destinations';

/* ── PAGE NAVIGATION ── */
function showPage(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById('page-' + page).classList.add('active');
  document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
  const navEl = document.getElementById('nav-' + page);
  if (navEl) navEl.classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
  if (page === 'dashboard') renderDashboard();
}

/* ── MOBILE MENU ── */
function toggleMenu() {
  document.getElementById('mobileMenu').classList.toggle('open');
}
function closeMenu() {
  document.getElementById('mobileMenu').classList.remove('open');
}

/* ── RENDER DESTINATION CARD ── */
function renderDestCard(d) {
  return `
    <div class="dest-card" onclick="openDest('${d.id}')">
      <div class="dest-img" style="background:${d.gradient}">
        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:4rem">${d.emoji}</div>
        <div class="dest-badge">${d.region.charAt(0).toUpperCase() + d.region.slice(1)}</div>
        <div class="dest-price-badge">from ₱${d.price.toLocaleString()}</div>
      </div>
      <div class="dest-body">
        <h3>${d.name}</h3>
        <div class="dest-meta">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            <circle cx="12" cy="9" r="2.5"/>
          </svg>
          ${d.tagline}
        </div>
        <div class="dest-activities">
          ${d.activities.map(a => `<span class="act-tag">${a}</span>`).join('')}
        </div>
        <div class="dest-footer">
          <div class="dest-footer-price">Base: <strong>₱${d.price.toLocaleString()}</strong></div>
          <button class="book-btn" onclick="event.stopPropagation();openDest('${d.id}')">Book Now</button>
        </div>
      </div>
    </div>`;
}

/* ── RENDER GRIDS ── */
function renderFeatured() {
  const featured = DESTINATIONS.slice(0, 3);
  document.getElementById('featuredGrid').innerHTML = featured.map(renderDestCard).join('');
}

function renderAll(list) {
  document.getElementById('allDestGrid').innerHTML = (list || DESTINATIONS).map(renderDestCard).join('');
}

/* ── FILTER DESTINATIONS ── */
function filterDest(type, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => {
    b.style.background = 'white';
    b.style.color = 'var(--muted)';
    b.style.borderColor = 'var(--border)';
  });
  btn.style.background = 'var(--primary)';
  btn.style.color = 'white';
  btn.style.borderColor = 'var(--primary)';

  let filtered;
  if (type === 'all')          filtered = DESTINATIONS;
  else if (type === 'low')     filtered = DESTINATIONS.filter(d => d.price < 5000);
  else if (type === 'mid')     filtered = DESTINATIONS.filter(d => d.price >= 5000 && d.price <= 7500);
  else if (type === 'high')    filtered = DESTINATIONS.filter(d => d.price > 7500);
  else                         filtered = DESTINATIONS.filter(d => d.region === type);
  renderAll(filtered);
}

/* ── HOME SEARCH ── */
function doSearch() {
  const dest   = document.getElementById('homeSearchDest').value.toLowerCase();
  const budget = document.getElementById('homeSearchBudget').value;
  let filtered = DESTINATIONS;
  if (dest)              filtered = filtered.filter(d => d.name.toLowerCase().includes(dest));
  if (budget === 'low')  filtered = filtered.filter(d => d.price < 5000);
  else if (budget === 'mid')  filtered = filtered.filter(d => d.price >= 5000 && d.price <= 7000);
  else if (budget === 'high') filtered = filtered.filter(d => d.price > 7000);
  showPage('destinations');
  setTimeout(() => { document.getElementById('allDestGrid').innerHTML = filtered.map(renderDestCard).join(''); }, 100);
}

/* ── OPEN DESTINATION DETAIL ── */
function openDest(id) {
  const d = DESTINATIONS.find(x => x.id === id);
  currentDest = d;
  selectedHotel = null;
  checkedActivities = new Set();

  document.getElementById('detailTitle').textContent = d.name;
  document.getElementById('detailSub').textContent   = d.tagline;
  document.getElementById('detailHero').style.background = d.gradient;

  document.getElementById('hotelList').innerHTML = d.hotels.map((h, i) => `
    <div class="hotel-item" id="hotel-${i}" onclick="selectHotel(${i},'${h.name.replace(/'/g, "\\'")}')">
      <div>
        <div class="hotel-name">${h.name}</div>
        <a class="hotel-link" href="${h.url}" target="_blank" onclick="event.stopPropagation()">View Hotel Website ↗</a>
      </div>
      <div class="hotel-radio"></div>
    </div>`).join('');

  document.getElementById('activityList').innerHTML = d.acts.map((a, i) => `
    <div class="activity-item" id="act-${i}" onclick="toggleAct(${i},'${a.name.replace(/'/g, "\\'")}',${a.price})">
      <div>
        <div class="activity-name">${a.name}</div>
      </div>
      <div style="display:flex;align-items:center;gap:12px">
        <span class="activity-price">₱${a.price.toLocaleString()}</span>
        <div class="activity-check"></div>
      </div>
    </div>`).join('');

  updateBookingSummary();
  prevPage = document.querySelector('.page.active')?.id?.replace('page-', '') || 'destinations';
  showPage('detail');
}

/* ── HOTEL & ACTIVITY SELECTION ── */
function selectHotel(i, name) {
  selectedHotel = name;
  document.querySelectorAll('.hotel-item').forEach(el => el.classList.remove('selected'));
  document.getElementById('hotel-' + i).classList.add('selected');
  updateBookingSummary();
}

function toggleAct(i, name, price) {
  const el  = document.getElementById('act-' + i);
  const key = name + '__' + price;
  if (checkedActivities.has(key)) {
    checkedActivities.delete(key);
    el.classList.remove('checked');
  } else {
    checkedActivities.add(key);
    el.classList.add('checked');
  }
  updateBookingSummary();
}

/* ── BOOKING SUMMARY ── */
function updateBookingSummary() {
  if (!currentDest) return;
  document.getElementById('bDest').textContent  = currentDest.name;
  document.getElementById('bBase').textContent  = '₱' + currentDest.price.toLocaleString();
  document.getElementById('bHotel').textContent = selectedHotel || 'Not selected';
  const actList = [...checkedActivities];
  document.getElementById('bActs').textContent  = actList.length ? actList.length + ' selected' : 'None';
  const actTotal = actList.reduce((sum, k) => sum + parseInt(k.split('__')[1]), 0);
  const total    = currentDest.price + actTotal;
  document.getElementById('bTotal').textContent = '₱' + total.toLocaleString();
}
=======
  console && console.debug && console.debug('assets/script.js loaded');

  /* ── GLOBAL STATE ── */
  let selectedActivities = {};

  /* ── MODAL ── */
  function openModal(tab) {
    const authModal = document.getElementById('authModal');
    if (!authModal) return;
    authModal.classList.add('open');
    switchTab(tab);
  }
  function closeModal() {
    const authModal = document.getElementById('authModal');
    if (!authModal) return;
    authModal.classList.remove('open');
  }
  // Modal event hookup is attached on DOMContentLoaded to avoid null refs

  function switchTab(tab) {
    const modalTabs = document.querySelectorAll('.modal-tab');
    modalTabs.forEach((t,i) => t.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register')));
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    if (loginForm) loginForm.classList.toggle('active', tab==='login');
    if (registerForm) registerForm.classList.toggle('active', tab==='register');
  }
>>>>>>> 4f0fdfbef158114ac38cdd496c925c5d2650c1c2

/* ── CONFIRM BOOKING ── */
function confirmBooking() {
  const name  = document.getElementById('guestName').value.trim();
  const email = document.getElementById('guestEmail').value.trim();
  const date  = document.getElementById('checkinDate').value;

  if (!name || !email)   { showToast('⚠️ Please fill in your name and email.'); return; }
  if (!selectedHotel)    { showToast('⚠️ Please select a hotel.');               return; }
  if (!date)             { showToast('⚠️ Please pick a check-in date.');          return; }

<<<<<<< HEAD
  const actTotal = [...checkedActivities].reduce((s, k) => s + parseInt(k.split('__')[1]), 0);
  const total    = currentDest.price + actTotal;

  const booking = {
    dest: currentDest.name,
    hotel: selectedHotel,
    date, name, email, total,
    status: 'upcoming',
    emoji: currentDest.emoji,
    gradient: currentDest.gradient,
=======
  /* ── SELECT DEST ── */
  function selectDest(name) {
    const map = { 'Baguio City':'baguio', 'Boracay':'boracay', 'Cebu City':'cebu', 'Bukidnon':'bukidnon', 'Vigan':'vigan', 'Davao City':'davao', 'Camiguin':'camiguin' };
    const val = map[name];
    if (val) {
      document.getElementById('planDest').value = val;
      updateHotels();
      filterHotelsByKey(val);
    } else {
      // If we don't have a mapped key, still try to filter by name text
      document.querySelectorAll('.hotel-card').forEach(card => {
        const destEl = card.querySelector('.hotel-dest');
        if (!destEl) return;
        const txt = destEl.textContent || destEl.innerText || '';
        card.style.display = txt.toLowerCase().includes(name.toLowerCase()) ? '' : 'none';
      });
    }
    document.getElementById('itinerary').scrollIntoView({behavior:'smooth'});
  }

  /* ── HOTEL & ACTIVITY DATA ── */
  const destData = {
    baguio: {
      hotels: [
        {name:'Hotel Veniz', price:1800},
        {name:'Microtel by Wyndham Baguio', price:8500},
        {name:'Travelite Express Hotel', price:2500}
      ], 
      activities: ['Strawberry Picking at La Trinidad Farm ₱250','BenCab Museum Gallery Tour ₱200','Tree Top Adventure (Camp John Hay) ₱400','Igorot Stone Kingdom Exploration ₱150',],
      actPrices: [0,200,300,500,400]
    },
    boracay: {
      hotels: [
        {name:'Henann Resort Boracay', price:12000},
        {name:'Fairways & Bluewater', price:6000},
        {name:'La Carmela de Boracay Resort Hotel', price:1200}
      ],
      activities: ['Island Hopping ₱800','Parasailing Activity ₱2,000','Helmet Diving ₱700','ATV Ride ₱600'],
      actPrices: [800,1500,1200,600,900]
    },
    cebu: {
      hotels: [
        {name:'Quest Hotel Cebu', price:9000},
        {name:'Radisson Blu Cebu', price:5500},
        {name:'Bayfront Hotel Cebu', price:2200}
      ],
      activities: ['Kawasan Falls Canyoneering  ₱1,500','Temple of Leah Tour  ₱100','Oslob Whale Shark Watching  ₱500'],
      actPrices: [1500,100,500]
    },
    bukidnon: {
      hotels: [
        {name:'Dahilayan Forest Park Resort', price:3500},
        {name:'Ultrawinds Mountain Resort', price:4200},
        {name:'Secret Haven Private Resort', price:1500}
      ],
      activities: ['ATV (Dahilayan Adventure Park) ₱850','840m Zipline (Dahilayan Adventure park) ₱500','DropZone (Dahilayan Adventure park) ₱500','ZipKart  (Dahilayan Adventure park) ₱250'],
      actPrices: [850,500,500,250,400]
    },
    vigan: {
      hotels: [
        {name:'Hotel Felicidad Vigan', price:3200},
        {name:'Paradores de Vigan', price:2800},
        {name:'Hotel Luna', price:2600}
      ],
      activities: ['Calesa Ride around Calle Crisologo (₱250)','Pagburnayan Jar Factory Pottery Making ₱300','Vigan Museum / Syquia Mansion Tour ₱180'],
      actPrices: [250,300,150,200,0]
    },
    palawan: {
      hotels: [
        {name:'Seda Lio (El Nido) ', price:6000},
        {name:'Hue Hotels and Resorts', price:4200},
        {name:'Two Seasons Coron Island Resort', price:8500}
      ],
      activities: ['El Nido Tour A (Lagoons & Islands) ₱1,200','Puerto Princesa Underground River Tour ₱2,750','Coron Ultimate Shipwreck & Snorkeling Tour ₱1,600', 'Wildlife Safari Tour at Calauit Sanctuary ₱2,500'],
      actPrices: [1200,2750,1600,2500,800]
    },
    siargao: {
      hotels: [
        {name:'Villa Cali', price:7500},
        {name:'Nay Palad Hideawa', price:3800},
        {name:'Kalinaw Resort', price:2800}
      ],
      activities: ['Island Hopping ₱2,000','Basic Surfing Lesson ₱700','Motorbike Rental ₱500','Sugba Lagoon Tour ₱1,200'],
      actPrices: [2000,700,500,1200]
    }
>>>>>>> 4f0fdfbef158114ac38cdd496c925c5d2650c1c2
  };
  bookings.unshift(booking);

<<<<<<< HEAD
  document.getElementById('cfDest').textContent  = booking.dest;
  document.getElementById('cfHotel').textContent = booking.hotel;
  document.getElementById('cfDate').textContent  = new Date(date).toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });
  document.getElementById('cfName').textContent  = booking.name;
  document.getElementById('cfTotal').textContent = '₱' + booking.total.toLocaleString();

  showPage('confirm');
  showToast('Booking confirmed! 🎉');
}

/* ── DASHBOARD ── */
function renderDashboard() {
  document.getElementById('statTotal').textContent    = bookings.length;
  document.getElementById('statUpcoming').textContent = bookings.filter(b => b.status === 'upcoming').length;
  const spent = bookings.reduce((s, b) => s + b.total, 0);
  document.getElementById('statSpent').textContent    = '₱' + spent.toLocaleString();

  if (bookings.length === 0) {
    document.getElementById('bookingsList').innerHTML = `
      <div style="text-align:center;padding:3rem;color:var(--muted);">
        <div style="font-size:3rem;margin-bottom:1rem;">🗺️</div>
        <p>No bookings yet. <button onclick="showPage('destinations')" style="background:none;border:none;color:var(--primary);font-weight:600;cursor:pointer;font-size:inherit;">Explore destinations →</button></p>
      </div>`;
    return;
=======
  const destNames = {baguio:'Baguio City',boracay:'Boracay',cebu:'Cebu City',bukidnon:'Bukidnon',vigan:'Vigan City',palawan:'Palawan',siargao:'Siargao Island',davao:'Davao City',camiguin:'Camiguin'};

  function updateHotels() {
    try {
    const dest = document.getElementById('planDest').value;
    const hotelSel = document.getElementById('planHotel');
    const actGrid = document.getElementById('activitiesGrid');
    console && console.debug && console.debug('updateHotels called, dest=', dest, 'hotelSel=', !!hotelSel, 'actGrid=', !!actGrid);
    if (!hotelSel) { console && console.error && console.error('planHotel select not found'); return; }
    hotelSel.innerHTML = '<option value="">Select hotel...</option>';
    selectedActivities = {};
    if (!dest) { actGrid.innerHTML = ''; filterHotelsByKey(''); return; }
    // dest may be a short key like 'baguio' or a full name like 'Baguio City'.
    let d = destData[dest];
    let useKey = dest;
    if (!d) {
      // try to resolve the key from destNames mapping
      for (const k in destNames) {
        const nm = (destNames[k] || '').toLowerCase();
        const val = (dest || '').toLowerCase();
        if (!nm) continue;
        if (val === k.toLowerCase() || val === nm || nm.includes(val) || val.includes(nm)) {
          d = destData[k];
          useKey = k;
          break;
        }
      }
    }
    if (!d) {
      // No data for this destination — clear activities and filter hotels by key/text
      console && console.debug && console.debug('No destData for', dest, 'resolved useKey=', useKey);
      actGrid.innerHTML = '';
      filterHotelsByKey(dest);
      updateSummary();
      return;
    }
    d.hotels.forEach((h,i) => {
      hotelSel.innerHTML += `<option value="${h.price}">${h.name} — ₱${h.price.toLocaleString()}/night</option>`;
    });
    console && console.debug && console.debug('Populated', d.hotels.length, 'hotels into planHotel');
    actGrid.innerHTML = d.activities.map((a,i) =>
      `<div class="activity-check" onclick="toggleActivity(this,${d.actPrices[i]})"><div class="check-icon"></div> ${a}</div>`
    ).join('');
    // Filter the hotels grid to show only hotels for this destination
    filterHotelsByKey(useKey || dest);
    updateSummary();
    } catch (err) {
      console && console.error && console.error('updateHotels error', err);
    }
  }

  function filterHotelsByKey(key) {
    const cards = document.querySelectorAll('.hotel-card');
    if (!key) { cards.forEach(c => c.style.display = ''); return; }
    const name = destNames[key] || '';
    cards.forEach(card => {
      // Prefer a data attribute if present for robust matching
      const dataDest = (card.dataset && card.dataset.destination) ? card.dataset.destination.toLowerCase() : '';
      if (dataDest) {
        card.style.display = (dataDest === key.toLowerCase() || dataDest.includes(key.toLowerCase())) ? '' : 'none';
        return;
      }
      const destEl = card.querySelector('.hotel-dest');
      if (!destEl) { card.style.display = ''; return; }
      const txt = destEl.textContent || destEl.innerText || '';
      card.style.display = txt.toLowerCase().includes(name.toLowerCase()) ? '' : 'none';
    });
>>>>>>> 4f0fdfbef158114ac38cdd496c925c5d2650c1c2
  }
  document.getElementById('bookingsList').innerHTML = bookings.map(b => `
    <div class="booking-card">
      <div class="booking-dest-icon" style="background:${b.gradient};display:flex;align-items:center;justify-content:center;font-size:1.8rem">${b.emoji}</div>
      <div class="booking-card-info">
        <h4>${b.dest}</h4>
        <p>${b.hotel} · ${new Date(b.date).toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' })}</p>
        <p style="font-weight:700;color:var(--primary);margin-top:4px">₱${b.total.toLocaleString()}</p>
      </div>
      <span class="booking-status status-${b.status}">${b.status.charAt(0).toUpperCase() + b.status.slice(1)}</span>
    </div>`).join('');
}

/* ── UTILITIES ── */
function goBack() { showPage(prevPage); }

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

<<<<<<< HEAD
/* ── INIT ── */
renderFeatured();
renderAll();
=======
  function confirmBooking() {
    const dest = document.getElementById('planDest').value;
    if (!dest) { alert('Please select a destination first!'); return; }
    alert('✅ Booking confirmed! (This would redirect to the checkout/payment page in the full system.)');
  }

  // Ensure the hotels and filters initialize if a destination is already selected on page load
  window.addEventListener('DOMContentLoaded', function() {
    try {
      const sel = document.getElementById('planDest');
      // Attach modal click handler here to ensure element exists
      const authModalElement = document.getElementById('authModal');
      if (authModalElement) {
        authModalElement.addEventListener('click', function(e) { if (e.target === this) closeModal(); });
      }
      if (sel && sel.value) {
        updateHotels();
      } else {
        // If no selection, ensure hotel cards are visible
        filterHotelsByKey('');
      }
    } catch (e) {
      console && console.warn && console.warn('Itinerary init error:', e);
    }
  });
>>>>>>> 4f0fdfbef158114ac38cdd496c925c5d2650c1c2
