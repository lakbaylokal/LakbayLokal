<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LakbayLokal — Explore the Philippines</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
  <div class="nav-logo" onclick="showPage('home')">Lakbay<span>Lokal</span></div>
  <ul class="nav-links">
    <li><a onclick="showPage('home')" id="nav-home" class="active">Home</a></li>
    <li><a onclick="showPage('destinations')" id="nav-destinations">Destinations</a></li>
    <li><a onclick="showPage('about')" id="nav-about">About</a></li>
    <li><a onclick="showPage('dashboard')" id="nav-dashboard">My Trips</a></li>
  </ul>
  <button class="nav-cta" onclick="showPage('destinations')">Book Now</button>
  <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a onclick="showPage('home');closeMenu()">Home</a>
  <a onclick="showPage('destinations');closeMenu()">Destinations</a>
  <a onclick="showPage('about');closeMenu()">About</a>
  <a onclick="showPage('dashboard');closeMenu()">My Trips</a>
</div>

<div class="toast" id="toast">Booking confirmed! 🎉</div>