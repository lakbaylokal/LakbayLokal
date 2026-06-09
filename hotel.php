<?php
require_once 'data.php';
require_once 'config/db.php';
require_once 'database/helpers.php';

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
  let currentDiscount = 0; // Store current discount percentage
  let currentDiscountCode = ''; // Store the code that was applied
  const bookingNamePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:[ '\-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/;

  function setBookingError(inputId, message) {
    const error = document.getElementById(inputId + 'Error');
    if (!error) return;
    error.textContent = message || '';
    error.classList.toggle('show', Boolean(message));
  }

  function clearBookingErrors() {
    document.querySelectorAll('#bookingForm .validation-error').forEach(el => {
      el.textContent = '';
      el.classList.remove('show');
    });
  }

  // ── Activity toggle ──
  function toggleActivity(i) {
    const el = document.getElementById('act-' + i);
    const name = el.dataset.name;
    const price = parseInt(el.dataset.price);

    if (selectedActivities[i]) {
      delete selectedActivities[i];
      el.classList.remove('checked');
    } else {
      selectedActivities[i] = {
        name,
        price
      };
      el.classList.add('checked');
    }
    calcTotal();
  }

  function applyDiscount() {
    const code = document.getElementById('discountCode').value.trim().toUpperCase();

    if (code === "LAKBAYLOCAL10") {
      currentDiscount = 0.10; // 10%
      currentDiscountCode = code;
    } else {
      currentDiscount = 0;
      currentDiscountCode = '';
    }

    calcTotal();
  }

  // ── Calculate total ──
  function calcTotal() {
    const checkin = document.getElementById('checkinInput').value;
    const checkout = document.getElementById('checkoutInput').value;
    const rooms = parseInt(document.getElementById('roomsInput').value) || 1;
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
        const hotel = PRICE_PER_NIGHT * nights * rooms;
        const subtotal = hotel + actsTotal;

        const discountAmount = Math.round(subtotal * currentDiscount);
        const subtotalAfterDiscount = subtotal - discountAmount;

        const tax = Math.round(subtotalAfterDiscount * 0.12);
        const total = subtotalAfterDiscount + tax;

        document.getElementById('nightsDisplay').textContent = nights + (nights === 1 ? ' night' : ' nights');
        document.getElementById('roomsDisplay').textContent = rooms + (rooms === 1 ? ' room' : ' rooms');
        
        // Display discount if applied
        if (discountAmount > 0) {
          document.getElementById('discountRow').style.display = 'flex';
          document.getElementById('discountDisplay').textContent = '-₱' + discountAmount.toLocaleString() + ' (' + Math.round(currentDiscount * 100) + '%)';
        } else {
          document.getElementById('discountRow').style.display = 'none';
          document.getElementById('discountDisplay').textContent = '';
        }
        
        document.getElementById('taxDisplay').textContent = '₱' + tax.toLocaleString();
        document.getElementById('totalDisplay').textContent = '₱' + total.toLocaleString();
        document.getElementById('totalInput').value = total;
        document.getElementById('nightsInput').value = nights;
        
        // Send discount to server
        document.getElementById('discountAmountInput').value = discountAmount;
        document.getElementById('discountPercentInput').value = currentDiscount;
        
        return;
      }
    }
    document.getElementById('nightsDisplay').textContent = '—';
    document.getElementById('taxDisplay').textContent = '—';
    document.getElementById('totalDisplay').textContent = '—';
    document.getElementById('discountRow').style.display = 'none';
  }

  // ── Payment method: show / hide fields ──
  function showPaymentFields() {
    const method = document.getElementById('paymentMethodSelect').value;

    const gcashFields = document.getElementById('gcashFields');
    const cardFields = document.getElementById('cardFields');

    // Hide all first, clear required
    gcashFields.style.display = 'none';
    cardFields.style.display = 'none';
    setRequired(['gcashNumber', 'gcashName'], false);
    setRequired(['cardHolder', 'cardNumber', 'cardExpiry', 'cardCvv'], false);
    ['gcashNumber', 'gcashName', 'cardHolder', 'cardNumber', 'cardExpiry', 'cardCvv'].forEach(id => setBookingError(id, ''));

    if (method === 'gcash') {
      gcashFields.style.display = 'block';
      setRequired(['gcashNumber', 'gcashName'], true);
    } else if (method === 'credit_card' || method === 'debit_card') {
      cardFields.style.display = 'block';
      setRequired(['cardHolder', 'cardNumber', 'cardExpiry', 'cardCvv'], true);
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
    if (v.length >= 3) v = v.substring(0, 2) + ' / ' + v.substring(2);
    el.value = v;
  }

  // ── Validate payment details before submit ──
  function validatePayment() {
    const method = document.getElementById('paymentMethodSelect').value;
    if (!method) {
      setBookingError('paymentMethodSelect', 'Please select a payment method.');
      return false;
    }
    setBookingError('paymentMethodSelect', '');

    if (method === 'gcash') {
      const num = document.getElementById('gcashNumber').value.replace(/\s/g, '');
      const name = document.getElementById('gcashName').value.trim();
      if (!num || !/^(09\d{9}|\+639\d{9})$/.test(num)) {
        setBookingError('gcashNumber', 'Please enter a valid GCash number (e.g., 09XXXXXXXXX).');
        return false;
      }
      setBookingError('gcashNumber', '');
      if (!bookingNamePattern.test(name)) {
        setBookingError('gcashName', 'GCash account name must contain letters only.');
        return false;
      }
      setBookingError('gcashName', '');
    }

    if (method === 'credit_card' || method === 'debit_card') {
      const holder = document.getElementById('cardHolder').value.trim();
      const num = document.getElementById('cardNumber').value.replace(/\s/g, '');
      const expiry = document.getElementById('cardExpiry').value.replace(/\s/g, '');
      const cvv = document.getElementById('cardCvv').value.trim();

      if (!bookingNamePattern.test(holder)) {
        setBookingError('cardHolder', 'Name must contain letters only.');
        return false;
      }
      setBookingError('cardHolder', '');
      if (!/^\d{16}$/.test(num)) {
        setBookingError('cardNumber', 'Card number must be 16 digits.');
        return false;
      }
      setBookingError('cardNumber', '');
      if (!/^\d{2}\/\d{2}$/.test(expiry)) {
        setBookingError('cardExpiry', 'Please enter a valid expiry date (MM/YY).');
        return false;
      }
      // Check expiry not in the past
      const [mm, yy] = expiry.split('/').map(Number);
      if (mm < 1 || mm > 12) {
        setBookingError('cardExpiry', 'Please enter a valid expiry date (MM/YY).');
        return false;
      }
      const now = new Date();
      const expDate = new Date(2000 + yy, mm - 1, 1);
      if (expDate < new Date(now.getFullYear(), now.getMonth(), 1)) {
        setBookingError('cardExpiry', 'Card is already expired.');
        return false;
      }
      setBookingError('cardExpiry', '');
      if (!/^\d{3,4}$/.test(cvv)) {
        setBookingError('cardCvv', 'Invalid CVV.');
        return false;
      }
      setBookingError('cardCvv', '');
    }
    return true;
  }

  // ── Pre-submit: validate & pack activities ──
  function prepareSubmit() {
    clearBookingErrors();
    const guestName = document.getElementById('guestNameInput').value.trim();
    const checkin = document.getElementById('checkinInput').value;
    const checkout = document.getElementById('checkoutInput').value;
    if (!bookingNamePattern.test(guestName)) {
      setBookingError('guestNameInput', 'Name must contain letters only.');
      return false;
    }
    if (!checkin || !checkout) {
      if (!checkin) setBookingError('checkinInput', 'Please select a check-in date.');
      if (!checkout) setBookingError('checkoutInput', 'Please select a check-out date.');
      return false;
    }
    const nights = Math.round((new Date(checkout) - new Date(checkin)) / 86400000);
    if (nights < 1) {
      setBookingError('checkoutInput', 'Check-out date must be after check-in date.');
      return false;
    }
    if (!validatePayment()) return false;
    // Pack selected activities as JSON string for PHP
    document.getElementById('selectedActsInput').value = JSON.stringify(Object.values(selectedActivities));
    return true;
  }

  document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('bookingForm');
    if (!bookingForm) return;

    const serverError = new URLSearchParams(window.location.search).get('error');
    if (serverError && typeof showToast === 'function') {
      showToast(serverError);
    }

    const fieldValidators = {
      guestNameInput: value => bookingNamePattern.test(value.trim()) ? '' : 'Name must contain letters only.',
      gcashNumber: value => /^(09\d{9}|\+639\d{9})$/.test(value.replace(/\s/g, '')) ? '' : 'Please enter a valid GCash number (e.g., 09XXXXXXXXX).',
      gcashName: value => bookingNamePattern.test(value.trim()) ? '' : 'GCash account name must contain letters only.',
      cardHolder: value => bookingNamePattern.test(value.trim()) ? '' : 'Name must contain letters only.',
      cardNumber: value => /^\d{16}$/.test(value.replace(/\s/g, '')) ? '' : 'Card number must be 16 digits.',
      cardCvv: value => /^\d{3,4}$/.test(value.trim()) ? '' : 'Invalid CVV.'
    };

    Object.keys(fieldValidators).forEach(id => {
      const input = document.getElementById(id);
      if (!input) return;
      input.addEventListener('input', function() {
        setBookingError(id, fieldValidators[id](input.value));
      });
    });

    document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
      const expiry = e.target.value.replace(/\s/g, '');
      let message = '';
      if (expiry.length === 5) {
        if (!/^\d{2}\/\d{2}$/.test(expiry)) {
          message = 'Please enter a valid expiry date (MM/YY).';
        } else {
          const [mm, yy] = expiry.split('/').map(Number);
          const now = new Date();
          const expDate = new Date(2000 + yy, mm - 1, 1);
          if (mm < 1 || mm > 12) message = 'Please enter a valid expiry date (MM/YY).';
          else if (expDate < new Date(now.getFullYear(), now.getMonth(), 1)) message = 'Card is already expired.';
        }
      }
      setBookingError('cardExpiry', message);
    });

    ['checkinInput', 'checkoutInput'].forEach(id => {
      document.getElementById(id)?.addEventListener('change', function() {
        const checkin = document.getElementById('checkinInput').value;
        const checkout = document.getElementById('checkoutInput').value;
        setBookingError('checkoutInput', checkin && checkout && new Date(checkout) <= new Date(checkin) ? 'Check-out date must be after check-in date.' : '');
      });
    });
  });
</script>