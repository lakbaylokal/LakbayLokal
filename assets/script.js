// script.js — LakbayLokal frontend logic
// Expects DESTINATIONS to be injected as a global variable from index.php

let currentDest = null;
let selectedHotel = null;
let checkedActivities = new Set();
let bookings = [];
let pendingBooking = null;
let selectedPaymentMethod = null;
let prevPage = (document.querySelector('.page.active')?.id?.replace('page-', '')) || 'destinations';

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
        <div class="dest-">
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

/* ── CONFIRM BOOKING ── */
function confirmBooking() {
  const name  = document.getElementById('guestName').value.trim();
  const email = document.getElementById('guestEmail').value.trim();
  const date  = document.getElementById('checkinDate').value;

  if (!name || !email)   { showToast('⚠️ Please fill in your name and email.'); return; }
  if (!selectedHotel)    { showToast('⚠️ Please select a hotel.');               return; }
  if (!date)             { showToast('⚠️ Please pick a check-in date.');          return; }

  const actTotal = [...checkedActivities].reduce((s, k) => s + parseInt(k.split('__')[1]), 0);
  const total    = currentDest.price + actTotal;

  pendingBooking = {
    dest: currentDest.name,
    hotel: selectedHotel,
    date,
    name,
    email,
    total,
    status: 'upcoming',
    emoji: currentDest.emoji,
    gradient: currentDest.gradient,
  };

  renderPaymentDetails();
  showPage('payment');
}

function renderPaymentDetails() {
  if (!pendingBooking) return;
  selectedPaymentMethod = null;
  document.getElementById('payDest').textContent  = pendingBooking.dest;
  document.getElementById('payHotel').textContent = pendingBooking.hotel;
  document.getElementById('payDate').textContent  = new Date(pendingBooking.date).toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });
  document.getElementById('payName').textContent  = pendingBooking.name;
  document.getElementById('payTotal').textContent = '₱' + pendingBooking.total.toLocaleString();
  document.getElementById('cardNumber').value = '';
  document.getElementById('cardExpiry').value = '';
  document.getElementById('cardCvv').value = '';
  document.getElementById('gcashMobile').value = '';
  document.getElementById('gcashRef').value = '';
  document.getElementById('cardPaymentFields').style.display = 'none';
  document.getElementById('gcashPaymentFields').style.display = 'none';
  document.getElementById('method-gcash').classList.remove('selected');
  document.getElementById('method-card').classList.remove('selected');
  const payBtn = document.getElementById('payNowBtn');
  payBtn.disabled = false;
  payBtn.textContent = 'Pay Now';
}

function selectPaymentMethod(method) {
  selectedPaymentMethod = method;
  document.getElementById('method-gcash').classList.toggle('selected', method === 'gcash');
  document.getElementById('method-card').classList.toggle('selected', method === 'card');
  document.getElementById('gcashPaymentFields').style.display = method === 'gcash' ? 'flex' : 'none';
  document.getElementById('cardPaymentFields').style.display = method === 'card' ? 'flex' : 'none';
  const payBtn = document.getElementById('payNowBtn');
  payBtn.textContent = method === 'gcash' ? 'Pay with GCash' : 'Pay with Card';
}

function formatGcashMobile(input) {
  const digits = input.value.replace(/\D/g, '').slice(0, 10);
  const parts = [];
  if (digits.length > 0) parts.push(digits.substring(0, Math.min(3, digits.length)));
  if (digits.length > 3) parts.push(digits.substring(3, Math.min(6, digits.length)));
  if (digits.length > 6) parts.push(digits.substring(6, 10));
  input.value = parts.join(' ');
}

function formatCardNumber(input) {
  const digits = input.value.replace(/\D/g, '').slice(0, 16);
  const groups = [];
  for (let i = 0; i < digits.length; i += 4) {
    groups.push(digits.substring(i, i + 4));
  }
  input.value = groups.join(' ');
}

function processPayment() {
  if (!selectedPaymentMethod) {
    showToast('⚠️ Please select a payment method.');
    return;
  }

  if (selectedPaymentMethod === 'card') {
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const cardExpiry = document.getElementById('cardExpiry').value.trim();
    const cardCvv    = document.getElementById('cardCvv').value.trim();

    if (!cardNumber || !cardExpiry || !cardCvv) {
      showToast('⚠️ Please enter your card details.');
      return;
    }

    pendingBooking.paymentInfo = {
      method: 'Card',
      label: `Card ending ${cardNumber.replace(/\s+/g, '').slice(-4)}`,
      details: cardExpiry,
    };
  }

  if (selectedPaymentMethod === 'gcash') {
    const gcashMobile = document.getElementById('gcashMobile').value.trim();
    const gcashRef = document.getElementById('gcashRef').value.trim();

    if (!gcashMobile || !gcashRef) {
      showToast('⚠️ Please enter your GCash mobile and reference.');
      return;
    }

    pendingBooking.paymentInfo = {
      method: 'GCash',
      label: `GCash ${gcashMobile}`,
      details: gcashRef,
    };
  }

  const payBtn = document.getElementById('payNowBtn');
  payBtn.disabled = true;
  payBtn.textContent = 'Processing…';

  setTimeout(() => {
    finalizeBooking();
  }, 1400);
}

function finalizeBooking() {
  if (!pendingBooking) return;

  pendingBooking.paymentMethod = selectedPaymentMethod || 'Card';
  bookings.unshift(pendingBooking);

  document.getElementById('cfDest').textContent  = pendingBooking.dest;
  document.getElementById('cfHotel').textContent = pendingBooking.hotel;
  document.getElementById('cfDate').textContent  = new Date(pendingBooking.date).toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });
  document.getElementById('cfName').textContent  = pendingBooking.name;
  document.getElementById('cfMethod').textContent = pendingBooking.paymentInfo?.method || pendingBooking.paymentMethod;
  document.getElementById('cfTotal').textContent = '₱' + pendingBooking.total.toLocaleString();

  if (pendingBooking.paymentInfo?.details) {
    document.getElementById('cfPaymentRef').textContent = pendingBooking.paymentInfo.details;
    document.getElementById('cfPaymentRefRow').style.display = 'flex';
  } else {
    document.getElementById('cfPaymentRefRow').style.display = 'none';
  }

  pendingBooking = null;
  showPage('confirm');
  showToast('Payment successful! 🎉');
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

/* ── INIT ── */
renderFeatured();
renderAll();
// Ensure the navbar reflects the currently visible page on initial load
function syncNavWithPage() {
  const activePageEl = document.querySelector('.page.active');
  if (!activePageEl) return;
  const pageName = activePageEl.id.replace('page-', '');
  document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
  const navEl = document.getElementById('nav-' + pageName);
  if (navEl) navEl.classList.add('active');
}

syncNavWithPage();
