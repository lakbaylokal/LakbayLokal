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
include 'views/booking-confirm.view.php';
include 'includes/footer.php'; 
?>

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
