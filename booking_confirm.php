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
$gcashNumber  = $_POST['gcash_number'] ?? '';
$gcashName    = $_POST['gcash_name'] ?? '';
$cardHolder   = $_POST['card_holder'] ?? '';
$cardNumber   = $_POST['card_number'] ?? '';
$cardExpiry   = $_POST['card_expiry'] ?? '';
$cardCvv      = $_POST['card_cvv'] ?? '';

function isValidName($name) {
  return preg_match("/^[\\p{L}]+(?:[ '\\-][\\p{L}]+)*$/u", trim($name));
}

function isValidGcashNumber($number) {
  $normalized = preg_replace('/\\s+/', '', $number);
  return preg_match('/^(09\\d{9}|\\+639\\d{9})$/', $normalized);
}

function validateExpiry($expiry) {
  $normalized = preg_replace('/\\s+/', '', $expiry);
  if (!preg_match('/^\\d{2}\\/\\d{2}$/', $normalized)) {
    return 'Please enter a valid expiry date (MM/YY).';
  }

  [$month, $year] = array_map('intval', explode('/', $normalized));
  if ($month < 1 || $month > 12) {
    return 'Please enter a valid expiry date (MM/YY).';
  }

  $expiryDate = DateTime::createFromFormat('!Y-m-d', (2000 + $year) . '-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '-01');
  $currentMonth = new DateTime('first day of this month');
  if ($expiryDate < $currentMonth) {
    return 'Card is already expired.';
  }

  return '';
}

function validationRedirect($destId, $hotelId, $message) {
  header('Location: hotel.php?dest=' . urlencode($destId) . '&id=' . urlencode($hotelId) . '&error=' . urlencode($message));
  exit;
}

// Validate
if (!$guestName || !$guestEmail || !$checkin || !$checkout || !$paymentMethod) {
  validationRedirect($destId, $hotelId, 'Please complete all required fields.');
}

if (!isValidName($guestName)) {
  validationRedirect($destId, $hotelId, 'Name must contain letters only.');
}

if (strtotime($checkout) <= strtotime($checkin)) {
  validationRedirect($destId, $hotelId, 'Check-out date must be after check-in date.');
}

if ($paymentMethod === 'gcash') {
  if (!isValidGcashNumber($gcashNumber)) {
    validationRedirect($destId, $hotelId, 'Please enter a valid GCash number (e.g., 09XXXXXXXXX).');
  }
  if (!isValidName($gcashName)) {
    validationRedirect($destId, $hotelId, 'GCash account name must contain letters only.');
  }
}

if ($paymentMethod === 'credit_card' || $paymentMethod === 'debit_card') {
  if (!isValidName($cardHolder)) {
    validationRedirect($destId, $hotelId, 'Name must contain letters only.');
  }
  if (!preg_match('/^\\d{16}$/', preg_replace('/\\s+/', '', $cardNumber))) {
    validationRedirect($destId, $hotelId, 'Card number must be 16 digits.');
  }
  $expiryError = validateExpiry($cardExpiry);
  if ($expiryError) {
    validationRedirect($destId, $hotelId, $expiryError);
  }
  if (!preg_match('/^\\d{3,4}$/', $cardCvv)) {
    validationRedirect($destId, $hotelId, 'Invalid CVV.');
  }
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
