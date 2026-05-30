<?php
// index.php — LakbayLokal main entry point
require_once 'data.php';

// Encode PHP destination data for use in JavaScript
$destinationsJson = json_encode($destinations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

include_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LakbayLokal — Explore the Philippines</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/auth.css">
</head>
<body>


<!-- ======= HOME PAGE ======= -->
<?php include 'components/home.php'; ?>

<!-- ======= DESTINATIONS PAGE ======= -->
<?php include 'components/destinations.php'; ?>

<!-- ======= DESTINATION DETAIL PAGE ======= -->
<?php include 'components/detail.php'; ?>

<!-- ======= PAYMENT PAGE ======= -->
<?php include 'components/payment.php'; ?>

<!-- ======= CONFIRMATION PAGE ======= -->
<?php include 'components/confirm.php'; ?>

<!-- ======= LOGIN PAGE ======= -->
<?php include 'components/login.php'; ?>

<!-- ======= SIGNUP PAGE ======= -->
<?php include 'components/signup.php'; ?>

<!-- ======= DASHBOARD PAGE ======= -->
<?php include 'components/dashboard.php'; ?>

<!-- ======= ABOUT PAGE ======= -->
<?php include 'components/about.php'; ?>

<!-- FOOTER -->
<?php
// 2. Isabit ang Footer at Scripts
include_once 'includes/footer.php';
?>

<!-- Inject PHP destination data into JS -->
<script>
  const DESTINATIONS = <?= $destinationsJson ?>;
</script>
<script src="assets/script.js"></script>
<script src="assets/auth.js"></script>
</body>
</html>
