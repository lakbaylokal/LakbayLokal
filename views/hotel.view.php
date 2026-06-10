<div class="page-wrapper">

  <!-- Breadcrumb -->
  <div class="breadcrumb-wrapper px-5 py-3">
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

  <!-- Main Two-Column Layout (Bootstrap) -->
  <section class="py-5">
    <div class="container-fluid px-5">
      <div class="row g-5">

        <!-- LEFT COLUMN: Hotel Info (col-lg-8) -->
        <div class="col-12 col-lg-8">

          <!-- About Section -->
          <div class="hotel-info-section mb-5">
            <h2 class="mb-3">About This Hotel</h2>
            <p class="hotel-desc-text"><?= htmlspecialchars($hotel['desc']) ?></p>
          </div>

          <!-- Amenities Section -->
          <div class="hotel-info-section mb-5">
            <h2 class="mb-4">Amenities &amp; Facilities</h2>
            <div class="row g-3">
              <?php foreach ($hotel['amenities'] as $am): ?>
                <div class="col-12 col-sm-6 col-md-4">
                  <div class="amenity-item p-3 rounded" style="background: white; border: 1px solid var(--border); text-align: center;">
                    <div class="amenity-icon" style="font-size: 1.5rem; margin-bottom: 0.5rem;">
                      <?= $amenityIcons[$am] ?? '✓' ?>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--deep); font-weight: 500;">
                      <?= htmlspecialchars($am) ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Check-in / Check-out -->
          <div class="hotel-info-section mb-5">
            <h2 class="mb-4">Check-in &amp; Check-out</h2>
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="checkinout-box p-4 rounded" style="background: var(--cream); border: 1px solid var(--border);">
                  <div class="cio-label small" style="color: var(--muted); margin-bottom: 0.5rem;">Check-in from</div>
                  <div class="cio-time fw-bold" style="font-size: 1.1rem; color: var(--primary);">
                    <?= $hotel['checkin'] ?>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="checkinout-box p-4 rounded" style="background: var(--cream); border: 1px solid var(--border);">
                  <div class="cio-label small" style="color: var(--muted); margin-bottom: 0.5rem;">Check-out before</div>
                  <div class="cio-time fw-bold" style="font-size: 1.1rem; color: var(--primary);">
                    <?= $hotel['checkout'] ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Activities Section -->
          <div class="hotel-info-section mb-5" id="activitiesSection">
            <h2 class="mb-3">🎯 Activities in <?= htmlspecialchars($dest['name']) ?></h2>
            <p style="color:var(--muted);font-size:0.88rem;margin-bottom:1.2rem;">Select activities to add to your booking. Prices will be reflected in your total.</p>
            <div id="activityList" class="d-flex flex-column gap-2">
              <?php foreach ($dest['acts'] as $i => $act): ?>
                <div class="activity-item p-3 rounded d-flex justify-content-between align-items-center cursor-pointer"
                  id="act-<?= $i ?>"
                  data-name="<?= htmlspecialchars($act['name']) ?>"
                  data-price="<?= $act['price'] ?>"
                  onclick="toggleActivity(<?= $i ?>)"
                  style="background: white; border: 1px solid var(--border); cursor: pointer; transition: all 0.2s;">
                  <div>
                    <div class="activity-name fw-500"><?= htmlspecialchars($act['name']) ?></div>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    <span class="activity-price fw-bold" style="color: var(--primary);">₱<?= number_format($act['price']) ?></span>
                    <div class="activity-check" style="width: 20px; height: 20px; border: 2px solid var(--border); border-radius: 4px;"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Policies Section -->
          <div class="hotel-info-section mb-5">
            <h2 class="mb-3">Property Policies</h2>
            <ul class="policy-list ps-4">
              <?php foreach ($hotel['policies'] as $p): ?>
                <li class="mb-2" style="color: var(--muted);"><?= htmlspecialchars($p) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Other Hotels -->
          <div class="hotel-info-section">
            <h2 class="mb-3">Other Hotels in <?= htmlspecialchars($dest['name']) ?></h2>
            <div class="d-flex flex-column gap-2">
              <?php foreach ($dest['hotels'] as $h):
                if ($h['id'] === $hotelId) continue; ?>
                <a href="hotel.php?dest=<?= $destId ?>&id=<?= $h['id'] ?>"
                  class="d-flex justify-content-between align-items-center p-3 rounded text-decoration-none"
                  style="background: var(--cream); border: 1px solid var(--border); color: inherit; transition: all 0.2s;"
                  onmouseover="this.style.borderColor='var(--primary)'"
                  onmouseout="this.style.borderColor='var(--border)'">
                  <div>
                    <div class="fw-600" style="font-size: 0.9rem;"><?= htmlspecialchars($h['name']) ?></div>
                    <small style="color: var(--muted);">⭐ <?= $h['rating'] ?> · <?= str_repeat('★', $h['stars']) ?></small>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold" style="color: var(--primary); font-size: 0.95rem;">₱<?= number_format($h['price']) ?></div>
                    <small style="color: var(--muted);">per night</small>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

        </div><!-- /Left Column -->

        <!-- RIGHT COLUMN: Booking Form (col-lg-4) -->
        <div class="col-12 col-lg-4">
          <div class="booking-form-card position-sticky p-4 rounded"
            style="background: white; border: 1px solid var(--border); box-shadow: var(--shadow); top: 90px;">

            <h3 class="mb-3">Book This Hotel</h3>

            <div class="booking-price-preview mb-4">
              <span class="price-big" style="font-size: 2rem; color: var(--primary); font-weight: 700;">
                ₱<?= number_format($hotel['price']) ?>
              </span>
              <span class="price-unit" style="color: var(--muted); font-size: 0.9rem;">/ night</span>
            </div>

            <form action="booking_confirm.php" method="POST" id="bookingForm" onsubmit="return prepareSubmit()">
              <input type="hidden" name="dest_id" value="<?= htmlspecialchars($destId) ?>">
              <input type="hidden" name="hotel_id" value="<?= htmlspecialchars($hotelId) ?>">
              <input type="hidden" name="dest_name" value="<?= htmlspecialchars($dest['name']) ?>">
              <input type="hidden" name="hotel_name" value="<?= htmlspecialchars($hotel['name']) ?>">
              <input type="hidden" name="price_per_night" value="<?= $hotel['price'] ?>">
              <input type="hidden" name="dest_gradient" value="<?= htmlspecialchars($dest['gradient']) ?>">
              <input type="hidden" name="dest_emoji" value="<?= $dest['emoji'] ?>">
              <input type="hidden" name="selected_acts" id="selectedActsInput" value="">
              <input type="hidden" name="discount_amount" id="discountAmountInput" value="0">
              <input type="hidden" name="discount_percent" id="discountPercentInput" value="0">

              <!-- Full Name -->
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="guest_name" class="form-control" id="guestNameInput" placeholder="Juan dela Cruz" required>
                <small class="validation-error" id="guestNameInputError"></small>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="guest_email" class="form-control" placeholder="juan@email.com" required>
              </div>

              <!-- Check-in & Check-out Dates -->
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label">Check-in</label>
                  <input type="date" name="checkin" class="form-control" id="checkinInput"
                    min="<?= date('Y-m-d') ?>" required onchange="calcTotal()">
                  <small class="validation-error" id="checkinInputError"></small>
                </div>
                <div class="col-6">
                  <label class="form-label">Check-out</label>
                  <input type="date" name="checkout" class="form-control" id="checkoutInput"
                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required onchange="calcTotal()">
                  <small class="validation-error" id="checkoutInputError"></small>
                </div>
              </div>

              <!-- Guests & Rooms -->
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label">Guests</label>
                  <select name="guests" class="form-select">
                    <option value="1">1 Guest</option>
                    <option value="2" selected>2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4 Guests</option>
                    <option value="5">5+ Guests</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label">Rooms</label>
                  <select name="rooms" class="form-select" id="roomsInput" onchange="calcTotal()">
                    <option value="1">1 Room</option>
                    <option value="2">2 Rooms</option>
                    <option value="3">3 Rooms</option>
                  </select>
                </div>
              </div>

              <!-- Special Requests -->
              <div class="mb-3">
                <label class="form-label">Special Requests</label>
                <input type="text" name="requests" class="form-control" placeholder="e.g. early check-in, high floor...">
              </div>

              <!-- Payment Method -->
              <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select" id="paymentMethodSelect" required onchange="showPaymentFields()">
                  <option value="">Select Payment Method</option>
                  <option value="gcash">GCash</option>
                  <option value="credit_card">Credit Card</option>
                  <option value="debit_card">Debit Card</option>
                </select>
                <small class="validation-error" id="paymentMethodSelectError"></small>
              </div>

              <!-- Discount Code -->
              <div class="mb-3">
                <label class="form-label">Discount Code (optional)</label>
                <input type="text" name="discount_code" id="discountCode"
                  class="form-control"
                  placeholder="Enter promo code"
                  oninput="applyDiscount()">
              </div>

              <!-- GCash Fields -->
              <div id="gcashFields" style="display:none;">
                <div class="mb-3">
                  <label class="form-label">GCash Mobile Number</label>
                  <input type="tel" name="gcash_number" class="form-control" id="gcashNumber"
                    placeholder="09XX XXX XXXX"
                    pattern="^(09|\+639)\d{9}$"
                    maxlength="13">
                  <small class="form-text">Format: 09XXXXXXXXX</small>
                  <small class="validation-error" id="gcashNumberError"></small>
                </div>
                <div class="mb-3">
                  <label class="form-label">Account Name</label>
                  <input type="text" name="gcash_name" class="form-control" id="gcashName"
                    placeholder="Name registered on GCash">
                  <small class="validation-error" id="gcashNameError"></small>
                </div>
              </div>

              <!-- Credit/Debit Card Fields -->
              <div id="cardFields" style="display:none;">
                <div class="mb-3">
                  <label class="form-label">Cardholder Name</label>
                  <input type="text" name="card_holder" class="form-control" id="cardHolder"
                    placeholder="As printed on the card"
                    autocomplete="cc-name">
                  <small class="validation-error" id="cardHolderError"></small>
                </div>
                <div class="mb-3">
                  <label class="form-label">Card Number</label>
                  <input type="text" name="card_number" class="form-control" id="cardNumber"
                    placeholder="XXXX XXXX XXXX XXXX"
                    maxlength="19"
                    autocomplete="cc-number"
                    oninput="formatCardNumber(this)">
                  <small class="validation-error" id="cardNumberError"></small>
                </div>
                <div class="row g-2 mb-3">
                  <div class="col-6">
                    <label class="form-label">Expiry</label>
                    <input type="text" name="card_expiry" class="form-control" id="cardExpiry"
                      placeholder="MM / YY"
                      maxlength="7"
                      autocomplete="cc-exp"
                      oninput="formatExpiry(this)">
                    <small class="validation-error" id="cardExpiryError"></small>
                  </div>
                  <div class="col-6">
                    <label class="form-label">CVV</label>
                    <input type="password" name="card_cvv" class="form-control" id="cardCvv"
                      placeholder="•••"
                      maxlength="4"
                      autocomplete="cc-csc"
                      oninput="this.value=this.value.replace(/\D/g,'')">
                    <small class="validation-error" id="cardCvvError"></small>
                  </div>
                </div>
              </div>

              <!-- Price Breakdown -->
              <div class="booking-summary-breakdown p-3 rounded mb-4" style="background: var(--primary-pale); border: 1px solid var(--border);">
                <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom: 1px solid var(--border);">
                  <span style="font-size: 0.85rem;">Price per night</span>
                  <strong>₱<?= number_format($hotel['price']) ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span style="font-size: 0.85rem;">Nights</span>
                  <strong id="nightsDisplay">—</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span style="font-size: 0.85rem;">Rooms</span>
                  <strong id="roomsDisplay">1</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" id="actsRow" style="display:none;">
                  <span style="font-size: 0.85rem;">Activities</span>
                  <strong id="actsDisplay">—</strong>
                </div>
                <div class="d-flex justify-content-between mb-2" id="discountRow" style="display:none;">
                  <span style="font-size: 0.85rem; color: #27ae60;">Discount</span>
                  <strong id="discountDisplay" style="color: #27ae60;">—</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span style="font-size: 0.85rem;">Taxes &amp; Fees (12%)</span>
                  <strong id="taxDisplay">—</strong>
                </div>
                <div class="d-flex justify-content-between fw-bold" style="font-size: 0.95rem; color: var(--primary);">
                  <span>Total</span>
                  <strong id="totalDisplay">—</strong>
                </div>
              </div>

              <input type="hidden" name="total_price" id="totalInput" value="0">
              <input type="hidden" name="nights" id="nightsInput" value="0">
              <input type="hidden" name="acts_total" id="actsTotalInput" value="0">

              <!-- Submit Button -->
              <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                Confirm Booking →
              </button>
            </form>

            <p class="text-center" style="font-size:0.75rem;color:var(--muted);">
              🔒 Secure booking · Free cancellation within 24hrs
            </p>

          </div>
        </div><!-- /Right Column -->

      </div>
    </div>
  </section>

</div><!-- /page-wrapper -->