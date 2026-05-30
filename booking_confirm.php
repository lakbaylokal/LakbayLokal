<?php
require_once 'data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
  
}

// Sanitize inputs
$destId       = htmlspecialchars($_POST['dest_id']         ?? '');
$hotelId      = htmlspecialchars($_POST['hotel_id']        ?? '');
$destName     = htmlspecialchars($_POST['dest_name']       ?? '');
$hotelName    = htmlspecialchars($_POST['hotel_name']      ?? '');
$guestName    = htmlspecialchars($_POST['guest_name']      ?? '');
$guestEmail   = htmlspecialchars($_POST['guest_email']     ?? '');
$checkin      = htmlspecialchars($_POST['checkin']         ?? '');
$checkout     = htmlspecialchars($_POST['checkout']        ?? '');
$guests       = htmlspecialchars($_POST['guests']          ?? '2');
$rooms        = htmlspecialchars($_POST['rooms']           ?? '1');
$requests     = htmlspecialchars($_POST['requests']        ?? '');
$paymentMethod = htmlspecialchars($_POST['payment_method'] ?? '');
$totalPrice   = (int)($_POST['total_price']                ?? 0);
$nights       = (int)($_POST['nights']                     ?? 0);
$pricePerNight= (int)($_POST['price_per_night']            ?? 0);
$actsTotal    = (int)($_POST['acts_total']                 ?? 0);
$destGradient = htmlspecialchars($_POST['dest_gradient']   ?? '');
$destEmoji    = htmlspecialchars($_POST['dest_emoji']      ?? '🏝️');
$selectedActsRaw = $_POST['selected_acts'] ?? '[]';

// Validate
if (!$guestName || !$guestEmail || !$checkin || !$checkout || !$paymentMethod) {
  header('Location: hotel.php?dest=' . urlencode($destId) . '&id=' . urlencode($hotelId));
  exit;
}

// Parse activities
$selectedActs = [];
$decoded = json_decode($selectedActsRaw, true);
if (is_array($decoded)) {
  $selectedActs = $decoded;
}

// Generate reference
$ref = 'LBL-' . strtoupper(substr(md5($guestEmail . $checkin . microtime()), 0, 8));

// Format dates
$checkinFmt  = date('F j, Y', strtotime($checkin));
$checkoutFmt = date('F j, Y', strtotime($checkout));

// Format payment method for display
$paymentMethodDisplay = [
  'gcash' => '💳 GCash',
  'credit_card' => '💳 Credit Card',
  'debit_card' => '💳 Debit Card'
][$paymentMethod] ?? $paymentMethod;

// Compute breakdown (server-side for display)
$hotelSubtotal = $pricePerNight * $nights * (int)$rooms;
$subtotal      = $hotelSubtotal + $actsTotal;
$tax           = round($subtotal * 0.12);
$displayTotal  = $totalPrice > 0 ? $totalPrice : ($subtotal + $tax);

$pageTitle  = 'Booking Confirmed! — LakbayLokal';
$activePage = '';
$rootPath   = '';
include 'includes/header.php';
?>

<div class="page-wrapper">
  <div class="confirm-page">
    <div class="confirm-card" style="max-width:580px;">

      <div class="confirm-icon">🎉</div>
      <p>Your reservation at <strong><?= $hotelName ?></strong> has been submitted. A confirmation will be sent to <strong><?= $guestEmail ?></strong>.</p>

      <div class="confirm-ref">
        Booking Reference: <strong><?= $ref ?></strong>
      </div>

      <div class="confirm-details">
        <div class="confirm-detail-row"><span>Guest Name</span><strong><?= $guestName ?></strong></div>
        <div class="confirm-detail-row"><span>Destination</span><strong><?= $destName ?></strong></div>
        <div class="confirm-detail-row"><span>Hotel</span><strong><?= $hotelName ?></strong></div>
        <div class="confirm-detail-row"><span>Check-in</span><strong><?= $checkinFmt ?></strong></div>
        <div class="confirm-detail-row"><span>Check-out</span><strong><?= $checkoutFmt ?></strong></div>
        <div class="confirm-detail-row"><span>Duration</span><strong><?= $nights ?> night<?= $nights != 1 ? 's' : '' ?></strong></div>
        <div class="confirm-detail-row"><span>Rooms / Guests</span><strong><?= $rooms ?> room · <?= $guests ?></strong></div>
        <div class="confirm-detail-row"><span>Payment Method</span><strong><?= $paymentMethodDisplay ?></strong></div>

        <form action="components/payment.php" method="POST">
          <input type="hidden" name="dest_id" value="<?= $destId ?>">
          <input type="hidden" name="hotel_id" value="<?= $hotelId ?>">
          <input type="hidden" name="total_price" value="<?= $displayTotal ?>">
          <input type="hidden" name="guest_name" value="<?= $guestName ?>">
          <input type="hidden" name="guest_email" value="<?= $guestEmail ?>">
          <input type="hidden" name="checkin" value="<?= $checkin ?>">
          <input type="hidden" name="payment_method" value="<?= $paymentMethod ?>">
          <input type="hidden" name="checkout" value="<?= $checkout ?>">
        </form>

        <?php if (!empty($selectedActs)): ?>
        <div class="confirm-detail-row" style="flex-direction:column;align-items:flex-start;gap:0.3rem;">
          <span>Selected Activities</span>
          <?php foreach ($selectedActs as $act): ?>
            <div style="display:flex;justify-content:space-between;width:100%;font-size:0.85rem;">
              <span style="color:var(--deep);"><?= htmlspecialchars($act['name'] ?? '') ?></span>
              <strong>₱<?= number_format($act['price'] ?? 0) ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($requests): ?>
        <div class="confirm-detail-row"><span>Special Requests</span><strong><?= $requests ?></strong></div>
        <?php endif; ?>

        <!-- Price Breakdown -->
        <div class="confirm-detail-row" style="margin-top:0.5rem;padding-top:0.5rem;border-top:2px solid var(--border);">
          <span>Hotel (<?= $nights ?> nights × <?= $rooms ?> room<?= $rooms > 1 ? 's' : '' ?>)</span>
          <strong>₱<?= number_format($hotelSubtotal) ?></strong>
        </div>
        <?php if ($actsTotal > 0): ?>
        <div class="confirm-detail-row">
          <span>Activities Total</span>
          <strong>₱<?= number_format($actsTotal) ?></strong>
        </div>
        <?php endif; ?>
        <div class="confirm-detail-row">
          <span>Taxes &amp; Fees (12%)</span>
          <strong>₱<?= number_format($tax) ?></strong>
        </div>
        <div class="confirm-detail-row" style="font-size:1rem;font-weight:700;border-bottom:none;">
          <span><strong>Total Paid</strong></span>
          <strong style="color:var(--primary);font-size:1.15rem;">₱<?= number_format($displayTotal) ?></strong>
        </div>
      </div>

      <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;margin-top:0.5rem;">
        <a href="hotel.php?dest=<?= $destId ?>&id=<?= $hotelId ?>"
           style="background:var(--cream);border:1.5px solid var(--border);color:var(--deep);border-radius:50px;padding:0.7rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;transition:all 0.2s;"
           onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
          ← Back to Hotel
        </a>
        <a href="destinations.php?dest=<?= $destId ?>"
           style="background:var(--primary);color:white;border-radius:50px;padding:0.7rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">
          Explore More Hotels
        </a>
        <a href="destinations.php"
           style="background:var(--accent);color:white;border-radius:50px;padding:0.7rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">
          All Destinations
        </a>
      </div>

      <p style="margin-top:1.5rem;font-size:0.78rem;color:var(--muted);">
        📧 Confirmation sent · 🔒 Free cancellation within 24 hours
      </p>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Save booking to sessionStorage so My Trips on home page shows it
(function() {
  const booking = {
    ref:        '<?= $ref ?>',
    dest_id:    '<?= $destId ?>',
    dest_name:  '<?= addslashes($destName) ?>',
    hotel_name: '<?= addslashes($hotelName) ?>',
    checkin:    '<?= $checkinFmt ?>',
    checkout:   '<?= $checkoutFmt ?>',
    nights:     <?= $nights ?>,
    guests:     '<?= addslashes($guests) ?>',
    total_price: <?= $displayTotal ?>,
    gradient:   '<?= addslashes($destGradient) ?>',
    emoji:      '<?= $destEmoji ?>',
  };
  const bookings = JSON.parse(sessionStorage.getItem('lbl_bookings') || '[]');
  bookings.unshift(booking);
  sessionStorage.setItem('lbl_bookings', JSON.stringify(bookings));
})();
</script>
