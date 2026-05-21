  /* ── MODAL ── */
  function openModal(tab) {
    document.getElementById('authModal').classList.add('open');
    switchTab(tab);
  }
  function closeModal() { document.getElementById('authModal').classList.remove('open'); }
  document.getElementById('authModal').addEventListener('click', function(e) { if(e.target===this) closeModal(); });

  function switchTab(tab) {
    document.querySelectorAll('.modal-tab').forEach((t,i) => t.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register')));
    document.getElementById('loginForm').classList.toggle('active', tab==='login');
    document.getElementById('registerForm').classList.toggle('active', tab==='register');
  }

  /* ── DESTINATION FILTER ── */
  function filterDest(region, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.dest-card').forEach(card => {
      card.style.display = (region === 'all' || card.dataset.region === region) ? '' : 'none';
    });
  }

  /* ── SCROLL TO DEST ── */
  function scrollToDestinations() { document.getElementById('destinations').scrollIntoView({behavior:'smooth'}); }

  /* ── SELECT DEST ── */
  function selectDest(name) {
    const map = { 'Baguio City':'baguio', 'Boracay':'boracay', 'Cebu City':'cebu', 'Bukidnon':'bukidnon' };
    const val = map[name];
    if (val) {
      document.getElementById('planDest').value = val;
      updateHotels();
    }
    document.getElementById('itinerary').scrollIntoView({behavior:'smooth'});
  }

  /* ── HOTEL & ACTIVITY DATA ── */
  const destData = {
    baguio: {
      hotels: [
        {name:'Hotel Veniz', price:1800},
        {name:'The Manor at Camp John Hay', price:8500},
        {name:'Microtel by Wyndham', price:2500}
      ],
      activities: ['Burnham Park (Free)','Strawberry Farm (₱200)','Café Hopping (₱300)','Hiking/Trekking (₱500)','Botanical Garden (₱400)'],
      actPrices: [0,200,300,500,400]
    },
    boracay: {
      hotels: [
        {name:'Henann Resort Boracay', price:12000},
        {name:'Fairways & Bluewater', price:6000},
        {name:'Boracay Budget Inn', price:1200}
      ],
      activities: ['Island Hopping (₱800)','Parasailing (₱1500)','Helmet Diving (₱1200)','ATV Ride (₱600)','Sunset Sailing (₱900)'],
      actPrices: [800,1500,1200,600,900]
    },
    cebu: {
      hotels: [
        {name:'Radisson Blu Cebu', price:9000},
        {name:'Seda Ayala Center Cebu', price:5500},
        {name:'Harolds Evotel Cebu', price:2200}
      ],
      activities: ['Whale Shark Watching (₱1000)','Canyoneering (₱800)','Magellan\'s Cross (Free)','Temple of Leah (₱150)','Sinulog Museum (₱200)'],
      actPrices: [1000,800,0,150,200]
    },
    bukidnon: {
      hotels: [
        {name:'Mallberry Suites', price:3500},
        {name:'Dahilayan Forest Park', price:4200},
        {name:'Citi Inn Bukidnon', price:1500}
      ],
      activities: ['Dahilayan Adventure Park (₱600)','Del Monte Pineapple Plantation (₱300)','Monastery of Transfiguration (Free)','Kampo Juan (₱200)','Impalutao Highland Resort (₱400)'],
      actPrices: [600,300,0,200,400]
    }
  };

  let selectedActivities = {};

  function updateHotels() {
    const dest = document.getElementById('planDest').value;
    const hotelSel = document.getElementById('planHotel');
    const actGrid = document.getElementById('activitiesGrid');
    hotelSel.innerHTML = '<option value="">Select hotel...</option>';
    selectedActivities = {};
    if (!dest) { actGrid.innerHTML = ''; return; }
    const d = destData[dest];
    d.hotels.forEach((h,i) => {
      hotelSel.innerHTML += `<option value="${h.price}">${h.name} — ₱${h.price.toLocaleString()}/night</option>`;
    });
    actGrid.innerHTML = d.activities.map((a,i) =>
      `<div class="activity-check" onclick="toggleActivity(this,${d.actPrices[i]})"><div class="check-icon"></div> ${a}</div>`
    ).join('');
    updateSummary();
  }

  function toggleActivity(el, price) {
    el.classList.toggle('checked');
    const key = el.textContent.trim();
    if (el.classList.contains('checked')) selectedActivities[key] = price;
    else delete selectedActivities[key];
    updateSummary();
  }

  function updateSummary() {
    const dest = document.getElementById('planDest');
    const hotel = document.getElementById('planHotel');
    const ci = document.getElementById('planCheckIn').value;
    const co = document.getElementById('planCheckOut').value;
    if (!dest.value || !hotel.value || !ci || !co) {
      document.getElementById('builderSummary').classList.remove('show');
      return;
    }
    const nights = Math.max(0, (new Date(co)-new Date(ci))/(1000*60*60*24));
    const hotelCost = parseInt(hotel.value) * nights;
    const actCost = Object.values(selectedActivities).reduce((a,b)=>a+b,0);
    const total = hotelCost + actCost;
    const destNames = {baguio:'Baguio City',boracay:'Boracay',cebu:'Cebu City',bukidnon:'Bukidnon'};
    document.getElementById('sumDest').textContent = destNames[dest.value] || '—';
    document.getElementById('sumHotel').textContent = hotel.options[hotel.selectedIndex].text.split('—')[0].trim();
    document.getElementById('sumDates').textContent = `${ci} → ${co}`;
    document.getElementById('sumNights').textContent = nights + (nights===1?' night':' nights');
    document.getElementById('sumHotelCost').textContent = '₱' + hotelCost.toLocaleString();
    document.getElementById('sumActCost').textContent = '₱' + actCost.toLocaleString();
    document.getElementById('sumTotal').textContent = '₱' + total.toLocaleString();
    document.getElementById('builderSummary').classList.add('show');
  }

  function confirmBooking() {
    const dest = document.getElementById('planDest').value;
    if (!dest) { alert('Please select a destination first!'); return; }
    alert('✅ Booking confirmed! (This would redirect to the checkout/payment page in the full system.)');
  }