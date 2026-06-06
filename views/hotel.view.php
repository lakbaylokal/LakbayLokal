<?php
require_once 'data.php';
?>

<div class="page-wrapper">
  <div class="container py-4">

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
      <button type="button" class="btn btn-link text-decoration-none text-primary px-0 d-flex align-items-center gap-2" onclick="window.history.back()" title="Go back to previous page" aria-label="Back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        <span class="d-none d-sm-inline">Back</span>
      </button>

      <nav aria-label="breadcrumb" class="w-100">
        <ol class="breadcrumb mb-0 bg-transparent px-0">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="destinations.php">Destinations</a></li>
          <li class="breadcrumb-item"><a href="destinations.php?dest=<?= $destId ?>"><?= htmlspecialchars($dest['name']) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($hotel['name']) ?></li>
        </ol>
      </nav>
    </div>

    <div class="hotel-detail-hero rounded-4 overflow-hidden mb-4" style="background: <?= $hotelBackground ?>;">
      <div class="hotel-detail-hero-overlay"></div>
      <div class="hotel-detail-hero-content p-4 p-md-5">
        <div class="hotel-stars-big"><?= $stars ?></div>
        <h1><?= htmlspecialchars($hotel['name']) ?></h1>
        <div class="hotel-loc">
          📍 <?= htmlspecialchars($hotel['location']) ?>
          <span class="hotel-rating-pill">⭐ <?= $hotel['rating'] ?> (<?= $hotel['reviews'] ?> reviews)</span>
        </div>
      </div>
    </div>

    <div class="row gx-4 gy-4">
      <div class="col-12 col-lg-8">

        <div class="card mb-4">
          <div class="card-body">
            <h2 class="h4 card-title">About This Hotel</h2>
            <p class="text-muted mb-0"><?= htmlspecialchars($hotel['desc']) ?></p>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h2 class="h4 card-title">Amenities &amp; Facilities</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mt-3">
              <?php foreach ($hotel['amenities'] as $am): ?>
                <div class="col">
                  <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-2">
                    <span class="amenity-icon"><?= $amenityIcons[$am] ?? '✓' ?></span>
                    <span><?= htmlspecialchars($am) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h2 class="h4 card-title">Check-in &amp; Check-out</h2>
            <div class="row g-3 mt-3">
              <div class="col-12 col-md-6">
                <div class="bg-light rounded-3 p-3 text-center">
                  <div class="text-uppercase text-muted small mb-1">Check-in from</div>
                  <div class="fw-bold"><?= $hotel['checkin'] ?></div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="bg-light rounded-3 p-3 text-center">
                  <div class="text-uppercase text-muted small mb-1">Check-out before</div>
                  <div class="fw-bold"><?= $hotel['checkout'] ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4" id="activitiesSection">
          <div class="card-body">
            <h2 class="h4 card-title">🎯 Activities in <?= htmlspecialchars($dest['name']) ?></h2>
            <p class="text-muted small mb-3">Select activities to add to your booking. Prices will be reflected in your total.</p>
            <div class="row row-cols-1 row-cols-sm-2 g-3" id="activityList">
              <?php foreach ($dest['acts'] as $i => $act): ?>
                <div class="col">
                  <div class="activity-item border rounded-3 p-3 h-100 d-flex justify-content-between align-items-center" id="act-<?= $i ?>"
                       data-name="<?= htmlspecialchars($act['name']) ?>"
                       data-price="<?= $act['price'] ?>"
                       onclick="toggleActivity(<?= $i ?>)">
                    <div class="flex-grow-1">
                      <div class="activity-name fw-semibold"><?= htmlspecialchars($act['name']) ?></div>
                    </div>
                    <div class="text-end">
                      <div class="activity-price text-primary fw-bold">₱<?= number_format($act['price']) ?></div>
                      <div class="activity-check mt-1"></div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h2 class="h4 card-title">Property Policies</h2>
            <ul class="list-unstyled mb-0 mt-3">
              <?php foreach ($hotel['policies'] as $p): ?>
                <li class="py-2 border-bottom border-200"><?= htmlspecialchars($p) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h2 class="h4 card-title">Other Hotels in <?= htmlspecialchars($dest['name']) ?></h2>
            <div class="list-group list-group-flush mt-3">
              <?php foreach ($dest['hotels'] as $h):
                if ($h['id'] === $hotelId) continue; ?>
                <a href="hotel.php?dest=<?= $destId ?>&id=<?= $h['id'] ?>"
                   class="list-group-item list-group-item-action rounded-3 mb-2 d-flex justify-content-between align-items-center"
                   style="background: var(--cream); border-color: var(--border);">
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($h['name']) ?></div>
                    <div class="small text-muted">⭐ <?= $h['rating'] ?> · <?= str_repeat('★', $h['stars']) ?></div>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold text-primary">₱<?= number_format($h['price']) ?></div>
                    <div class="small text-muted">per night</div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>

      <div class="col-12 col-lg-4">
        <div class="card booking-form-card shadow-sm border-0">
          <div class="card-body">
            <h3 class="h5 mb-3">Book This Hotel</h3>
            <div class="d-flex align-items-baseline gap-2 mb-4 pb-3 border-bottom">
              <span class="fs-2 fw-bold">₱<?= number_format($hotel['price']) ?></span>
              <small class="text-muted">/ night</small>
            </div>

            <form action="booking_confirm.php" method="POST" id="bookingForm">
              <input type="hidden" name="dest_id" value="<?= htmlspecialchars($destId) ?>">
              <input type="hidden" name="hotel_id" value="<?= htmlspecialchars($hotelId) ?>">
              <input type="hidden" name="dest_name" value="<?= htmlspecialchars($dest['name']) ?>">
              <input type="hidden" name="hotel_name" value="<?= htmlspecialchars($hotel['name']) ?>">
              <input type="hidden" name="price_per_night" value="<?= $hotel['price'] ?>">
              <input type="hidden" name="dest_gradient" value="<?= htmlspecialchars($dest['gradient']) ?>">
              <input type="hidden" name="dest_emoji" value="<?= $dest['emoji'] ?>">
              <input type="hidden" name="selected_acts" id="selectedActsInput" value="">

              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="guest_name" placeholder="Juan dela Cruz" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="guest_email" placeholder="juan@email.com" required>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Check-in Date</label>
                  <input type="date" class="form-control" name="checkin" id="checkinInput" min="<?= date('Y-m-d') ?>" required onchange="calcTotal()">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Check-out Date</label>
                  <input type="date" class="form-control" name="checkout" id="checkoutInput" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required onchange="calcTotal()">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Guests</label>
                  <select class="form-select" name="guests">
                    <option value="1">1 Guest</option>
                    <option value="2" selected>2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4 Guests</option>
                    <option value="5">5+ Guests</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Rooms</label>
                  <select class="form-select" name="rooms" id="roomsInput" onchange="calcTotal()">
                    <option value="1">1 Room</option>
                    <option value="2">2 Rooms</option>
                    <option value="3">3 Rooms</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Special Requests (optional)</label>
                <input type="text" class="form-control" name="requests" placeholder="e.g. early check-in, high floor...">
              </div>

              <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select class="form-select" name="payment_method" id="paymentMethodSelect" required onchange="showPaymentFields()">
                  <option value="">Select Payment Method</option>
                  <option value="gcash">GCash</option>
                  <option value="credit_card">Credit Card</option>
                  <option value="debit_card">Debit Card</option>
                </select>
              </div>

              <div id="gcashFields" style="display:none;">
                <div class="mb-3">
                  <label class="form-label">GCash Mobile Number</label>
                  <input type="tel" class="form-control" name="gcash_number" id="gcashNumber" placeholder="09XX XXX XXXX" pattern="^(09|\+639)\d{9}$" maxlength="13">
                  <div class="form-text">Format: 09XXXXXXXXX</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Account Name</label>
                  <input type="text" class="form-control" name="gcash_name" id="gcashName" placeholder="Name registered on GCash">
                </div>
              </div>

              <div id="cardFields" style="display:none;">
                <div class="mb-3">
                  <label class="form-label">Cardholder Name</label>
                  <input type="text" class="form-control" name="card_holder" id="cardHolder" placeholder="As printed on the card" autocomplete="cc-name">
                </div>
                <div class="mb-3">
                  <label class="form-label">Card Number</label>
                  <input type="text" class="form-control" name="card_number" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" autocomplete="cc-number" oninput="formatCardNumber(this)">
                </div>
                <div class="row g-3">
                  <div class="col-6">
                    <label class="form-label">Expiry Date</label>
                    <input type="text" class="form-control" name="card_expiry" id="cardExpiry" placeholder="MM / YY" maxlength="7" autocomplete="cc-exp" oninput="formatExpiry(this)">
                  </div>
                  <div class="col-6">
                    <label class="form-label">CVV</label>
                    <input type="password" class="form-control" name="card_cvv" id="cardCvv" placeholder="•••" maxlength="4" autocomplete="cc-csc" oninput="this.value=this.value.replace(/\D/g,'')">
                  </div>
                </div>
              </div>

              <div class="booking-summary-breakdown">
                <div class="breakdown-row"><span>Price per night</span><strong>₱<?= number_format($hotel['price']) ?></strong></div>
                <div class="breakdown-row"><span>Nights</span><strong id="nightsDisplay">—</strong></div>
                <div class="breakdown-row"><span>Rooms</span><strong id="roomsDisplay">1</strong></div>
                <div class="breakdown-row" id="actsRow" style="display:none;"><span>Activities</span><strong id="actsDisplay">—</strong></div>
                <div class="breakdown-row"><span>Taxes &amp; Fees (12%)</span><strong id="taxDisplay">—</strong></div>
                <div class="breakdown-row" style="font-weight:700;font-size:0.95rem;border-bottom:none;"><span>Total</span><strong id="totalDisplay" style="color:var(--primary);">—</strong></div>
              </div>

              <input type="hidden" name="total_price" id="totalInput" value="0">
              <input type="hidden" name="nights" id="nightsInput" value="0">
              <input type="hidden" name="acts_total" id="actsTotalInput" value="0">

              <button type="submit" class="btn btn-primary w-100 book-now-btn" onclick="return prepareSubmit()">Confirm Booking →</button>
            </form>

            <p class="text-center text-muted small mt-3 mb-0">🔒 Secure booking · Free cancellation within 24hrs</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
