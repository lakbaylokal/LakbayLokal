<?php
require_once 'data.php';
?>

<div class="page-wrapper">

  <!-- Breadcrumb -->
  <div class="breadcrumb-wrapper">
    <button class="breadcrumb-back" onclick="window.history.back()" title="Go back to previous page" aria-label="Back">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
    </button>
    <div class="breadcrumb">
      <a href="index.php">Home</a>
      <span class="breadcrumb-sep">›</span>
      <a href="destinations.php">Destinations</a>
      <span class="breadcrumb-sep">›</span>
      <a href="destinations.php?dest=<?= $destId ?>"><?= htmlspecialchars($dest['name']) ?></a>
      <span class="breadcrumb-sep">›</span>
      <span><?= htmlspecialchars($hotel['name']) ?></span>
    </div>
  </div>

  <!-- Hotel Hero -->
  <div class="hotel-detail-hero" style="background: <?= $hotelBackground ?>;">
    <div class="hotel-detail-hero-overlay"></div>
    <div class="hotel-detail-hero-content">
      <div class="hotel-stars-big"><?= $stars ?></div>
      <h1><?= htmlspecialchars($hotel['name']) ?></h1>
      <div class="hotel-loc">
        📍 <?= htmlspecialchars($hotel['location']) ?>
        <span class="hotel-rating-pill">⭐ <?= $hotel['rating'] ?> (<?= $hotel['reviews'] ?> reviews)</span>
      </div>
    </div>
  </div>

  <!-- Main Two-Column Layout -->
  <div class="hotel-detail-layout">

    <!-- LEFT: Hotel Info + Activities -->
    <div>

      <!-- About -->
      <div class="hotel-info-section">
        <h2>About This Hotel</h2>
        <p class="hotel-desc-text"><?= htmlspecialchars($hotel['desc']) ?></p>
      </div>

      <!-- Amenities -->
      <div class="hotel-info-section">
        <h2>Amenities &amp; Facilities</h2>
        <div class="amenities-grid">
          <?php foreach ($hotel['amenities'] as $am): ?>
            <div class="amenity-item">
              <span class="amenity-icon"><?= $amenityIcons[$am] ?? '✓' ?></span>
              <span><?= htmlspecialchars($am) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Check-in / Check-out -->
      <div class="hotel-info-section">
        <h2>Check-in &amp; Check-out</h2>
        <div class="checkinout-grid">
          <div class="checkinout-box">
            <div class="cio-label">Check-in from</div>
            <div class="cio-time"><?= $hotel['checkin'] ?></div>
          </div>
          <div class="checkinout-box">
            <div class="cio-label">Check-out before</div>
            <div class="cio-time"><?= $hotel['checkout'] ?></div>
          </div>
        </div>
      </div>

      <!-- Activities -->
      <div class="hotel-info-section" id="activitiesSection">
        <h2>🎯 Activities in <?= htmlspecialchars($dest['name']) ?></h2>
        <p style="color:var(--muted);font-size:0.88rem;margin-bottom:1.2rem;">Select activities to add to your booking. Prices will be reflected in your total.</p>
        <div id="activityList">
          <?php foreach ($dest['acts'] as $i => $act): ?>
            <div class="activity-item" id="act-<?= $i ?>"
                 data-name="<?= htmlspecialchars($act['name']) ?>"
                 data-price="<?= $act['price'] ?>"
                 onclick="toggleActivity(<?= $i ?>)">
              <div>
                <div class="activity-name"><?= htmlspecialchars($act['name']) ?></div>
              </div>
              <div style="display:flex;align-items:center;gap:12px;">
                <span class="activity-price">₱<?= number_format($act['price']) ?></span>
                <div class="activity-check"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Policies -->
      <div class="hotel-info-section">
        <h2>Property Policies</h2>
        <ul class="policy-list">
          <?php foreach ($hotel['policies'] as $p): ?>
            <li><?= htmlspecialchars($p) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Other Hotels -->
      <div class="hotel-info-section">
        <h2>Other Hotels in <?= htmlspecialchars($dest['name']) ?></h2>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
          <?php foreach ($dest['hotels'] as $h):
            if ($h['id'] === $hotelId) continue; ?>
            <a href="hotel.php?dest=<?= $destId ?>&id=<?= $h['id'] ?>"
               style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1rem;background:var(--cream);border-radius:var(--radius-sm);text-decoration:none;color:inherit;border:1px solid var(--border);transition:all 0.2s;"
               onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
              <div>
                <div style="font-weight:600;font-size:0.9rem;"><?= htmlspecialchars($h['name']) ?></div>
                <div style="font-size:0.78rem;color:var(--muted);">⭐ <?= $h['rating'] ?> · <?= str_repeat('★', $h['stars']) ?></div>
              </div>
              <div style="text-align:right;">
                <div style="font-weight:700;color:var(--primary);font-size:0.95rem;">₱<?= number_format($h['price']) ?></div>
                <div style="font-size:0.72rem;color:var(--muted);">per night</div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /left column -->

    <!-- RIGHT: Booking Form -->
    <div>
      <div class="booking-form-card" id="bookingCard">
        <h3>Book This Hotel</h3>
        <div class="booking-price-preview">
          <span class="price-big">₱<?= number_format($hotel['price']) ?></span>
          <span class="price-unit">/ night</span>
        </div>

        <form action="booking_confirm.php" method="POST" id="bookingForm">
          <input type="hidden" name="dest_id"        value="<?= htmlspecialchars($destId) ?>">
          <input type="hidden" name="hotel_id"       value="<?= htmlspecialchars($hotelId) ?>">
          <input type="hidden" name="dest_name"      value="<?= htmlspecialchars($dest['name']) ?>">
          <input type="hidden" name="hotel_name"     value="<?= htmlspecialchars($hotel['name']) ?>">
          <input type="hidden" name="price_per_night" value="<?= $hotel['price'] ?>">
          <input type="hidden" name="dest_gradient"  value="<?= htmlspecialchars($dest['gradient']) ?>">
          <input type="hidden" name="dest_emoji"     value="<?= $dest['emoji'] ?>">
          <input type="hidden" name="selected_acts"  id="selectedActsInput" value="">

          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="guest_name" placeholder="Juan dela Cruz" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="guest_email" placeholder="juan@email.com" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Check-in Date</label>
              <input type="date" name="checkin" id="checkinInput" min="<?= date('Y-m-d') ?>" required onchange="calcTotal()">
            </div>
            <div class="form-group">
              <label>Check-out Date</label>
              <input type="date" name="checkout" id="checkoutInput" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required onchange="calcTotal()">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Guests</label>
              <select name="guests">
                <option value="1">1 Guest</option>
                <option value="2" selected>2 Guests</option>
                <option value="3">3 Guests</option>
                <option value="4">4 Guests</option>
                <option value="5">5+ Guests</option>
              </select>
            </div>
            <div class="form-group">
              <label>Rooms</label>
              <select name="rooms" id="roomsInput" onchange="calcTotal()">
                <option value="1">1 Room</option>
                <option value="2">2 Rooms</option>
                <option value="3">3 Rooms</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Special Requests (optional)</label>
            <input type="text" name="requests" placeholder="e.g. early check-in, high floor...">
          </div>
          
          <div class="form-group">
            <label>Payment Method</label>
            <select name="payment_method" id="paymentMethodSelect" required onchange="showPaymentFields()">
              <option value="">Select Payment Method</option>
              <option value="gcash">GCash</option>
              <option value="credit_card">Credit Card</option>
              <option value="debit_card">Debit Card</option>
            </select>
          </div>

          <!-- GCash Fields -->
          <div id="gcashFields" style="display:none;">
            <div class="form-group">
              <label>GCash Mobile Number</label>
              <input type="tel" name="gcash_number" id="gcashNumber"
                     placeholder="09XX XXX XXXX"
                     pattern="^(09|\+639)\d{9}$"
                     maxlength="13">
              <small style="color:var(--muted);font-size:0.75rem;">Format: 09XXXXXXXXX</small>
            </div>
            <div class="form-group">
              <label>Account Name</label>
              <input type="text" name="gcash_name" id="gcashName"
                     placeholder="Name registered on GCash">
            </div>
          </div>

          <!-- Credit / Debit Card Fields -->
          <div id="cardFields" style="display:none;">
            <div class="form-group">
              <label>Cardholder Name</label>
              <input type="text" name="card_holder" id="cardHolder"
                     placeholder="As printed on the card"
                     autocomplete="cc-name">
            </div>
            <div class="form-group">
              <label>Card Number</label>
              <input type="text" name="card_number" id="cardNumber"
                     placeholder="XXXX XXXX XXXX XXXX"
                     maxlength="19"
                     autocomplete="cc-number"
                     oninput="formatCardNumber(this)">
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Expiry Date</label>
                <input type="text" name="card_expiry" id="cardExpiry"
                       placeholder="MM / YY"
                       maxlength="7"
                       autocomplete="cc-exp"
                       oninput="formatExpiry(this)">
              </div>
              <div class="form-group">
                <label>CVV</label>
                <input type="password" name="card_cvv" id="cardCvv"
                       placeholder="•••"
                       maxlength="4"
                       autocomplete="cc-csc"
                       oninput="this.value=this.value.replace(/\D/g,'')">
              </div>
            </div>
          </div>

          <!-- Price Breakdown -->
          <div class="booking-summary-breakdown">
            <div class="breakdown-row"><span>Price per night</span><strong>₱<?= number_format($hotel['price']) ?></strong></div>
            <div class="breakdown-row"><span>Nights</span><strong id="nightsDisplay">—</strong></div>
            <div class="breakdown-row"><span>Rooms</span><strong id="roomsDisplay">1</strong></div>
            <div class="breakdown-row" id="actsRow" style="display:none;"><span>Activities</span><strong id="actsDisplay">—</strong></div>
            <div class="breakdown-row"><span>Taxes &amp; Fees (12%)</span><strong id="taxDisplay">—</strong></div>
            <div class="breakdown-row" style="font-weight:700;font-size:0.95rem;border-bottom:none;"><span>Total</span><strong id="totalDisplay" style="color:var(--primary);">—</strong></div>
          </div>
          <input type="hidden" name="total_price" id="totalInput" value="0">
          <input type="hidden" name="nights"      id="nightsInput" value="0">
          <input type="hidden" name="acts_total"  id="actsTotalInput" value="0">

          <button type="submit" class="book-now-btn" onclick="return prepareSubmit()">
            Confirm Booking →
          </button>
        </form>

        <p style="font-size:0.75rem;color:var(--muted);text-align:center;margin-top:0.8rem;">
          🔒 Secure booking · Free cancellation within 24hrs
        </p>
      </div>
    </div>

  </div><!-- /hotel-detail-layout -->

</div><!-- /page-wrapper -->