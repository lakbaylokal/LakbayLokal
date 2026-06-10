<?php
require_once 'data.php';
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

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
$discountAmount = (int)($_POST['discount_amount']          ?? 0);
$discountPercent = (float)($_POST['discount_percent']      ?? 0);
$destGradient = htmlspecialchars($_POST['dest_gradient']   ?? '');
$destEmoji    = htmlspecialchars($_POST['dest_emoji']      ?? '🏝️');
$selectedActsRaw = $_POST['selected_acts'] ?? '[]';
$discountCode = htmlspecialchars($_POST['discount_code'] ?? '');
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

function detectCardBrand($number) {
  $digits = preg_replace('/\D/', '', $number);
  if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $digits)) {
    return 'Visa';
  }
  if (preg_match('/^5[1-5][0-9]{14}$/', $digits)) {
    return 'Mastercard';
  }
  if (preg_match('/^3[47][0-9]{13}$/', $digits)) {
    return 'American Express';
  }
  if (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $digits)) {
    return 'Discover';
  }
  return 'Card';
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

// Validate discount code
$validDiscountCodes = ['LAKBAYLOKAL10' => 0.10];
$appliedDiscount = 0;
if (!empty($discountCode)) {
  $codeUpper = strtoupper(trim($discountCode));
  if (isset($validDiscountCodes[$codeUpper])) {
    $appliedDiscount = $validDiscountCodes[$codeUpper];
  }
}

// Server-side recalculation for security
$hotelSubtotal = $pricePerNight * $nights * (int)$rooms;
$activityTotal = $actsTotal;
$subtotal = $hotelSubtotal + $activityTotal;

// Apply validated discount
$serverDiscountAmount = round($subtotal * $appliedDiscount);
$subtotalAfterDiscount = $subtotal - $serverDiscountAmount;
$tax = round($subtotalAfterDiscount * 0.12);
$serverTotal = $subtotalAfterDiscount + $tax;

// Validate that client-calculated total matches server calculation (within rounding)
if (abs($serverTotal - $totalPrice) > 5) {
  validationRedirect($destId, $hotelId, 'Price calculation mismatch. Please try again.');
}

// Use server-calculated values for security
$finalDiscountAmount = $serverDiscountAmount;
$finalTotal = $serverTotal;

// Parse activities
$selectedActs = [];
$decoded = json_decode($selectedActsRaw, true);
if (is_array($decoded)) {
  $selectedActs = $decoded;
}

// Generate reference
$ref = 'LBL-' . strtoupper(substr(md5($guestEmail . $checkin . microtime()), 0, 8));

// Save booking and payment details to database
$bookingId = null;
$bookingStmt = $conn->prepare(
  "INSERT INTO bookings (
      reference_code, user_id, guest_name, guest_email,
      destination_id, hotel_id, checkin_date, checkout_date,
      number_of_guests, number_of_rooms, subtotal,
      activities_total, tax_amount, total_price,
      payment_method, special_requests, status
    ) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')"
);
if ($bookingStmt) {
  $guestNameClean = trim($guestName);
  $guestEmailClean = trim($guestEmail);
  $requestsClean = trim($requests);
  $paymentMethodClean = trim($paymentMethod);
  $guestCount = (int)$guests;
  $roomCount = (int)$rooms;

  $bookingStmt->bind_param(
    str_repeat('s', 7) . str_repeat('i', 6) . str_repeat('s', 2),
    $ref,
    $guestNameClean,
    $guestEmailClean,
    $destId,
    $hotelId,
    $checkin,
    $checkout,
    $guestCount,
    $roomCount,
    $hotelSubtotal,
    $activityTotal,
    $tax,
    $finalTotal,
    $paymentMethodClean,
    $requestsClean
  );

  if ($bookingStmt->execute()) {
    $bookingId = $conn->insert_id;
  }
  $bookingStmt->close();
}

if ($bookingId) {
  $paymentReference = 'PAY-' . strtoupper(substr(md5($ref . microtime()), 0, 10));
  $paymentStatus = 'completed';

  if ($paymentMethod === 'gcash') {
    $gcashNumberClean = preg_replace('/\D/', '', $gcashNumber);
    $gcashNameClean = trim($gcashName);
    $paymentStmt = $conn->prepare(
      "INSERT INTO payment_details (
          booking_id, gcash_number, gcash_account_name,
          payment_status, payment_reference
        ) VALUES (?, ?, ?, ?, ?)"
    );
    if ($paymentStmt) {
      $paymentStmt->bind_param(
        'issss',
        $bookingId,
        $gcashNumberClean,
        $gcashNameClean,
        $paymentStatus,
        $paymentReference
      );
      $paymentStmt->execute();
      $paymentStmt->close();
    }
  } else {
    $cardHolderClean = trim($cardHolder);
    $cardLastFour = substr(preg_replace('/\D/', '', $cardNumber), -4);
    $cardBrand = detectCardBrand($cardNumber);
    $paymentStmt = $conn->prepare(
      "INSERT INTO payment_details (
          booking_id, card_holder_name, card_last_four, card_brand,
          payment_status, payment_reference
        ) VALUES (?, ?, ?, ?, ?, ?)"
    );
    if ($paymentStmt) {
      $paymentStmt->bind_param(
        'isssss',
        $bookingId,
        $cardHolderClean,
        $cardLastFour,
        $cardBrand,
        $paymentStatus,
        $paymentReference
      );
      $paymentStmt->execute();
      $paymentStmt->close();
    }
  }
}


// Format dates
$checkinFmt  = date('F j, Y', strtotime($checkin));
$checkoutFmt = date('F j, Y', strtotime($checkout));

// Format payment method for display
$paymentMethodDisplay = [
  'gcash' => 'GCash',
  'credit_card' => 'Credit Card',
  'debit_card' => 'Debit Card'
][$paymentMethod] ?? $paymentMethod;
$paymentMethodDisplay = preg_replace('/[^\x20-\x7E]/', '', $paymentMethodDisplay);

// Prepare breakdown variables for view
$activityTotal = $actsTotal;

// Store receipt data in session so separate receipt page can render it
$receiptData = [
  'ref' => $ref,
  'dest_id' => $destId,
  'hotel_id' => $hotelId,
  'dest_name' => $destName,
  'hotel_name' => $hotelName,
  'guest_name' => $guestName,
  'guest_email' => $guestEmail,
  'checkin' => $checkin,
  'checkout' => $checkout,
  'checkin_fmt' => $checkinFmt,
  'checkout_fmt' => $checkoutFmt,
  'payment_method' => $paymentMethod,
  'payment_method_display' => $paymentMethodDisplay,
  'nights' => $nights,
  'rooms' => $rooms,
  'guests' => $guests,
  'special_requests' => $requests,
  'hotel_subtotal' => $hotelSubtotal,
  'activity_total' => $activityTotal,
  'discount_amount' => $finalDiscountAmount,
  'discount_percent' => $appliedDiscount,
  'tax' => $tax,
  'total' => $finalTotal,
  'selected_activities' => $selectedActs,
  'created_at' => date('F j, Y \a\t g:i A'),
];

$_SESSION['receipt_history'][$ref] = $receiptData;
$_SESSION['latest_receipt'] = $ref;

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
    total_price: <?= $finalTotal ?>,
    gradient:   '<?= addslashes($destGradient) ?>',
    emoji:      '<?= $destEmoji ?>',
  };
  const bookings = JSON.parse(sessionStorage.getItem('lbl_bookings') || '[]');
  bookings.unshift(booking);
  sessionStorage.setItem('lbl_bookings', JSON.stringify(bookings));
})();
</script>
