<?php
require_once 'data.php';

$destId  = $_GET['dest'] ?? '';
$hotelId = $_GET['id'] ?? '';

$dest  = getDestById($destId);
$hotel = getHotelById($destId, $hotelId);

if (!$dest || !$hotel) {
  header('Location: destinations.php');
  exit;
}

$pageTitle  = $hotel['name'] . ' — LakbayLokal';
$activePage = 'destinations';
$rootPath   = '';

$stars = str_repeat('★', $hotel['stars']) . str_repeat('☆', 5 - $hotel['stars']);

$hotelBackground = isset($hotel['image'])
  ? "linear-gradient(to top, rgba(26,18,8,0.75) 0%, transparent 55%), url('{$hotel['image']}') center/cover no-repeat"
  : $dest['gradient'];

include 'includes/header.php';
include 'includes/amenity-icons.php';

include 'views/hotel.view.php';

include 'includes/footer.php';
?>

<script>
  const PRICE_PER_NIGHT = <?= $hotel['price'] ?>;
let selectedActivities = {}; // { index: { name, price } }

// ── Activity toggle ──
function toggleActivity(i) {
  const el    = document.getElementById('act-' + i);
  const name  = el.dataset.name;
  const price = parseInt(el.dataset.price);

  if (selectedActivities[i]) {
    delete selectedActivities[i];
    el.classList.remove('checked');
  } else {
    selectedActivities[i] = { name, price };
    el.classList.add('checked');
  }
  calcTotal();
}

// ── Calculate total ──
function calcTotal() {
  const checkin  = document.getElementById('checkinInput').value;
  const checkout = document.getElementById('checkoutInput').value;
  const rooms    = parseInt(document.getElementById('roomsInput').value) || 1;
  const actsTotal = Object.values(selectedActivities).reduce((s, a) => s + a.price, 0);

  // Update acts row
  const actKeys = Object.values(selectedActivities);
  if (actKeys.length > 0) {
    document.getElementById('actsRow').style.display = 'flex';
    document.getElementById('actsDisplay').textContent = actKeys.length + ' selected (+₱' + actsTotal.toLocaleString() + ')';
  } else {
    document.getElementById('actsRow').style.display = 'none';
  }

  document.getElementById('actsTotalInput').value = actsTotal;

  if (checkin && checkout) {
    const nights = Math.round((new Date(checkout) - new Date(checkin)) / 86400000);
    if (nights > 0) {
      const hotel    = PRICE_PER_NIGHT * nights * rooms;
      const subtotal = hotel + actsTotal;
      const tax      = Math.round(subtotal * 0.12);
      const total    = subtotal + tax;

      document.getElementById('nightsDisplay').textContent = nights + (nights === 1 ? ' night' : ' nights');
      document.getElementById('roomsDisplay').textContent  = rooms + (rooms === 1 ? ' room' : ' rooms');
      document.getElementById('taxDisplay').textContent    = '₱' + tax.toLocaleString();
      document.getElementById('totalDisplay').textContent  = '₱' + total.toLocaleString();
      document.getElementById('totalInput').value          = total;
      document.getElementById('nightsInput').value         = nights;
      return;
    }
  }
  document.getElementById('nightsDisplay').textContent = '—';
  document.getElementById('taxDisplay').textContent    = '—';
  document.getElementById('totalDisplay').textContent  = '—';
}

// ── Payment method: show / hide fields ──
function showPaymentFields() {
  const method = document.getElementById('paymentMethodSelect').value;

  const gcashFields = document.getElementById('gcashFields');
  const cardFields  = document.getElementById('cardFields');

  // Hide all first, clear required
  gcashFields.style.display = 'none';
  cardFields.style.display  = 'none';
  setRequired(['gcashNumber','gcashName'], false);
  setRequired(['cardHolder','cardNumber','cardExpiry','cardCvv'], false);

  if (method === 'gcash') {
    gcashFields.style.display = 'block';
    setRequired(['gcashNumber','gcashName'], true);
  } else if (method === 'credit_card' || method === 'debit_card') {
    cardFields.style.display = 'block';
    setRequired(['cardHolder','cardNumber','cardExpiry','cardCvv'], true);
  }
}

function setRequired(ids, req) {
  ids.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.required = req;
  });
}

// ── Card number formatter: XXXX XXXX XXXX XXXX ──
function formatCardNumber(el) {
  let v = el.value.replace(/\D/g, '').substring(0, 16);
  el.value = v.match(/.{1,4}/g)?.join(' ') ?? v;
}

// ── Expiry formatter: MM / YY ──
function formatExpiry(el) {
  let v = el.value.replace(/\D/g, '').substring(0, 4);
  if (v.length >= 3) v = v.substring(0,2) + ' / ' + v.substring(2);
  el.value = v;
}

// ── Validate payment details before submit ──
function validatePayment() {
  const method = document.getElementById('paymentMethodSelect').value;
  if (!method) {
    alert('Pakipili ng payment method.');
    return false;
  }

  if (method === 'gcash') {
    const num  = document.getElementById('gcashNumber').value.replace(/\s/g,'');
    const name = document.getElementById('gcashName').value.trim();
    if (!num || !/^(09|\+639)\d{9}$/.test(num)) {
      alert('Pakilagay ng tamang GCash number (e.g. 09XXXXXXXXX).');
      return false;
    }
    if (!name) {
      alert('Pakilagay ng account name sa GCash.');
      return false;
    }
  }

  if (method === 'credit_card' || method === 'debit_card') {
    const holder = document.getElementById('cardHolder').value.trim();
    const num    = document.getElementById('cardNumber').value.replace(/\s/g,'');
    const expiry = document.getElementById('cardExpiry').value.replace(/\s/g,'');
    const cvv    = document.getElementById('cardCvv').value.trim();

    if (!holder) {
      alert('Pakilagay ng pangalan na nasa card.');
      return false;
    }
    if (!/^\d{16}$/.test(num)) {
      alert('Ang card number ay dapat 16 digits.');
      return false;
    }
    if (!/^\d{2}\/\d{2}$/.test(expiry)) {
      alert('Pakilagay ng tamang expiry date (MM/YY).');
      return false;
    }
    // Check expiry not in the past
    const [mm, yy] = expiry.split('/').map(Number);
    const now = new Date();
    const expDate = new Date(2000 + yy, mm - 1, 1);
    if (expDate < new Date(now.getFullYear(), now.getMonth(), 1)) {
      alert('Expired na ang iyong card.');
      return false;
    }
    if (!/^\d{3,4}$/.test(cvv)) {
      alert('Ang CVV ay dapat 3 o 4 digits.');
      return false;
    }
  }
  return true;
}

// ── Pre-submit: validate & pack activities ──
function prepareSubmit() {
  const checkin  = document.getElementById('checkinInput').value;
  const checkout = document.getElementById('checkoutInput').value;
  if (!checkin || !checkout) {
    alert('Pakipili ng check-in at check-out dates.');
    return false;
  }
  const nights = Math.round((new Date(checkout) - new Date(checkin)) / 86400000);
  if (nights < 1) {
    alert('Ang check-out date ay dapat pagkatapos ng check-in.');
    return false;
  }
  if (!validatePayment()) return false;
  // Pack selected activities as JSON string for PHP
  document.getElementById('selectedActsInput').value = JSON.stringify(Object.values(selectedActivities));
  return true;
}

</script>