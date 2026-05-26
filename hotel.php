<?php
// ─── HOTEL DATA ─────────────────────────────────────────────────────────────
// To add a new hotel: just add a new entry here. The page design stays the same.
$hotels = [
  'hotel-veniz' => [
    'name'        => 'Hotel Veniz',
    'location'    => 'Baguio City, Benguet',
    'tagline'     => 'Cozy comfort in the heart of Session Road',
    'description' => 'A charming boutique hotel steps away from Session Road. Known for its warm Cordillera-inspired interiors and friendly staff. The perfect base for exploring Baguio\'s cafés, markets, and highland scenery.',
    'price'       => 1800,
    'stars'       => 3,
    'rating'      => 4.6,
    'reviews'     => 128,
    'gradient'    => 'linear-gradient(135deg, #2d6a4f 0%, #1b4332 100%)',
    'color'       => '#2d6a4f',
    'badge_color' => '#d8f3dc',
    'badge_text'  => '#1b4332',
    'amenities'   => ['Free WiFi', 'Breakfast Included', 'Hot Shower', 'City View', 'Café Onsite', 'Daily Housekeeping'],
    'highlights'  => ['5 min walk to Burnham Park', 'Near Session Road', 'Mountain views from upper floors'],
    'rooms'       => [
      ['type' => 'Standard Room',  'beds' => '1 Double Bed',   'size' => '22 sqm', 'price' => 1800],
      ['type' => 'Deluxe Room',    'beds' => '1 Queen Bed',    'size' => '28 sqm', 'price' => 2200],
      ['type' => 'Family Suite',   'beds' => '2 Twin Beds',    'size' => '36 sqm', 'price' => 3200],
    ],
  ],
  'the-manor' => [
    'name'        => 'The Manor at Camp John Hay',
    'location'    => 'Camp John Hay, Baguio City',
    'tagline'     => 'Luxury nestled in a pine forest retreat',
    'description' => 'The crown jewel of Baguio accommodations. Set within the sprawling Camp John Hay complex, The Manor blends colonial architecture with world-class amenities — from a full spa to golf course access and fine dining.',
    'price'       => 8500,
    'stars'       => 5,
    'rating'      => 4.9,
    'reviews'     => 312,
    'gradient'    => 'linear-gradient(135deg, #3a5a40 0%, #1a2e1c 100%)',
    'color'       => '#3a5a40',
    'badge_color' => '#d8f3dc',
    'badge_text'  => '#1a2e1c',
    'amenities'   => ['Free WiFi', 'Full-Service Spa', 'Golf Course Access', 'Fine Dining Restaurant', 'Heated Pool', 'Concierge', 'Valet Parking', 'Fitness Center'],
    'highlights'  => ['Inside Camp John Hay', 'Pine forest surroundings', 'Closest to Baguio Country Club'],
    'rooms'       => [
      ['type' => 'Deluxe Room',         'beds' => '1 King Bed',     'size' => '35 sqm', 'price' => 8500],
      ['type' => 'Premier Room',        'beds' => '1 King Bed',     'size' => '42 sqm', 'price' => 10500],
      ['type' => 'Junior Suite',        'beds' => '1 King + Sofa',  'size' => '58 sqm', 'price' => 14000],
      ['type' => 'Forest View Suite',   'beds' => '1 King Bed',     'size' => '70 sqm', 'price' => 18000],
    ],
  ],
  'microtel-baguio' => [
    'name'        => 'Microtel by Wyndham Baguio',
    'location'    => 'Harrison Road, Baguio City',
    'tagline'     => 'Reliable, modern stay with mountain views',
    'description' => 'Part of the trusted Wyndham group, Microtel Baguio delivers consistent quality at a mid-range price. Modern rooms, reliable hot water, and a great location for families and solo travelers alike.',
    'price'       => 2500,
    'stars'       => 3,
    'rating'      => 4.4,
    'reviews'     => 95,
    'gradient'    => 'linear-gradient(135deg, #457b9d 0%, #1d3557 100%)',
    'color'       => '#457b9d',
    'badge_color' => '#dbe9f5',
    'badge_text'  => '#1d3557',
    'amenities'   => ['Free WiFi', 'Breakfast Available', 'Parking', 'Air Conditioning', '24hr Front Desk', 'Luggage Storage'],
    'highlights'  => ['Near SM City Baguio', 'Business-friendly rooms', 'Easy access to Mines View Park'],
    'rooms'       => [
      ['type' => 'Standard Queen',  'beds' => '1 Queen Bed',  'size' => '20 sqm', 'price' => 2500],
      ['type' => 'Standard Twin',   'beds' => '2 Twin Beds',  'size' => '20 sqm', 'price' => 2700],
      ['type' => 'Superior Room',   'beds' => '1 King Bed',   'size' => '26 sqm', 'price' => 3200],
    ],
  ],
  'henann-resort' => [
    'name'        => 'Henann Resort Boracay',
    'location'    => 'White Beach, Boracay, Aklan',
    'tagline'     => 'Beachfront luxury on the world\'s best beach',
    'description' => 'Sitting directly on Boracay\'s legendary White Beach, Henann Resort is an icon of Philippine beach hospitality. Multiple infinity pools, an award-winning spa, and that perfect sunset view every single evening.',
    'price'       => 12000,
    'stars'       => 5,
    'rating'      => 4.9,
    'reviews'     => 541,
    'gradient'    => 'linear-gradient(135deg, #0096c7 0%, #023e58 100%)',
    'color'       => '#0096c7',
    'badge_color' => '#caf0f8',
    'badge_text'  => '#023e58',
    'amenities'   => ['Beachfront Access', 'Multiple Infinity Pools', 'Full Spa', 'Restaurant & Bar', 'Free WiFi', 'Concierge', 'Water Sports Desk', 'Kids Club'],
    'highlights'  => ['Directly on White Beach', 'Award-winning spa', 'Famous sunset views from pool'],
    'rooms'       => [
      ['type' => 'Deluxe Room',        'beds' => '1 King Bed',    'size' => '30 sqm', 'price' => 12000],
      ['type' => 'Ocean View Room',    'beds' => '1 King Bed',    'size' => '36 sqm', 'price' => 15000],
      ['type' => 'Beach Front Suite',  'beds' => '1 King + Sofa', 'size' => '55 sqm', 'price' => 22000],
      ['type' => 'Penthouse Suite',    'beds' => '1 King Bed',    'size' => '90 sqm', 'price' => 35000],
    ],
  ],
  'fairways-bluewater' => [
    'name'        => 'Fairways & Bluewater',
    'location'    => 'Newcoast, Boracay, Aklan',
    'tagline'     => 'Serene resort away from the crowd',
    'description' => 'Escape the bustle of White Beach at this tranquil enclave on the quieter Newcoast side of Boracay. Spacious villas with private plunge pools, a championship golf course, and a pristine stretch of beach.',
    'price'       => 6000,
    'stars'       => 4,
    'rating'      => 4.7,
    'reviews'     => 187,
    'gradient'    => 'linear-gradient(135deg, #219ebc 0%, #023047 100%)',
    'color'       => '#219ebc',
    'badge_color' => '#caf0f8',
    'badge_text'  => '#023047',
    'amenities'   => ['Golf Course', 'Private Beach', 'Pool Villas', 'Spa', 'Free WiFi', 'Restaurant', 'Water Sports', 'Bike Rentals'],
    'highlights'  => ['Away from crowded White Beach', 'Championship golf course', 'Spacious villas with plunge pools'],
    'rooms'       => [
      ['type' => 'Garden Room',       'beds' => '1 King Bed',    'size' => '32 sqm', 'price' => 6000],
      ['type' => 'Pool Access Room',  'beds' => '1 King Bed',    'size' => '38 sqm', 'price' => 8500],
      ['type' => 'Garden Villa',      'beds' => '1 King + Sofa', 'size' => '65 sqm', 'price' => 13000],
    ],
  ],
  'boracay-budget-inn' => [
    'name'        => 'Boracay Budget Inn',
    'location'    => 'Station 3, Boracay, Aklan',
    'tagline'     => 'Affordable base near White Beach',
    'description' => 'The smart traveler\'s pick — a clean, no-frills guesthouse just a short walk from White Beach. Spend your budget on parasailing and island hopping, not the hotel room.',
    'price'       => 1200,
    'stars'       => 2,
    'rating'      => 4.1,
    'reviews'     => 63,
    'gradient'    => 'linear-gradient(135deg, #48cae4 0%, #0077b6 100%)',
    'color'       => '#0077b6',
    'badge_color' => '#caf0f8',
    'badge_text'  => '#023e58',
    'amenities'   => ['Free WiFi', 'Fan & AC Rooms', 'Common Area', 'Luggage Storage', '24hr Reception'],
    'highlights'  => ['8-min walk to White Beach', 'Best value on the island', 'Near Station 3 restaurants'],
    'rooms'       => [
      ['type' => 'Fan Room',   'beds' => '1 Double Bed',  'size' => '14 sqm', 'price' => 1200],
      ['type' => 'AC Room',    'beds' => '1 Double Bed',  'size' => '16 sqm', 'price' => 1600],
      ['type' => 'Twin Room',  'beds' => '2 Single Beds', 'size' => '16 sqm', 'price' => 1700],
    ],
  ],
  'radisson-blu-cebu' => [
    'name'        => 'Radisson Blu Cebu',
    'location'    => 'SM City Mall, Cebu City',
    'tagline'     => 'Five-star elegance in the city center',
    'description' => 'Cebu\'s premier luxury address, directly connected to SM City Cebu. Sleek contemporary rooms, a rooftop pool with panoramic city views, a world-class spa, and easy access to everything Cebu has to offer.',
    'price'       => 9000,
    'stars'       => 5,
    'rating'      => 4.8,
    'reviews'     => 289,
    'gradient'    => 'linear-gradient(135deg, #c9a227 0%, #7b5e0e 100%)',
    'color'       => '#c9a227',
    'badge_color' => '#fef3c7',
    'badge_text'  => '#78350f',
    'amenities'   => ['Rooftop Pool', 'Spa & Wellness', 'Gym', 'Signature Restaurant', 'Sky Lounge Bar', 'Free WiFi', 'Concierge', 'Mall Access'],
    'highlights'  => ['Connected to SM City Cebu', 'Rooftop infinity pool', 'Central location for city tours'],
    'rooms'       => [
      ['type' => 'Superior Room',       'beds' => '1 King Bed',    'size' => '32 sqm', 'price' => 9000],
      ['type' => 'Deluxe City View',    'beds' => '1 King Bed',    'size' => '36 sqm', 'price' => 11000],
      ['type' => 'Junior Suite',        'beds' => '1 King + Sofa', 'size' => '52 sqm', 'price' => 16000],
    ],
  ],
  'seda-cebu' => [
    'name'        => 'Seda Ayala Center Cebu',
    'location'    => 'Ayala Center, Cebu City',
    'tagline'     => 'Modern stays above Ayala Business District',
    'description' => 'Directly above Ayala Center Cebu, Seda offers stylish contemporary rooms with stunning city views. A top pick for business travelers and families who want the convenience of shopping, dining, and transport at their doorstep.',
    'price'       => 5500,
    'stars'       => 4,
    'rating'      => 4.7,
    'reviews'     => 214,
    'gradient'    => 'linear-gradient(135deg, #e9c46a 0%, #c77b0a 100%)',
    'color'       => '#c77b0a',
    'badge_color' => '#fef3c7',
    'badge_text'  => '#78350f',
    'amenities'   => ['Pool', 'Gym', 'Restaurant', 'Rooftop Bar', 'Free WiFi', 'Mall Access', 'Business Center'],
    'highlights'  => ['Inside Ayala Business District', 'Stunning city skyline views', 'Steps from Cebu Business Park'],
    'rooms'       => [
      ['type' => 'Deluxe Room',        'beds' => '1 King Bed',    'size' => '30 sqm', 'price' => 5500],
      ['type' => 'Premier Room',       'beds' => '1 King Bed',    'size' => '36 sqm', 'price' => 7000],
      ['type' => 'Suite',              'beds' => '1 King + Sofa', 'size' => '54 sqm', 'price' => 11500],
    ],
  ],
  'harolds-evotel' => [
    'name'        => 'Harolds Evotel Cebu',
    'location'    => 'Gorordo Avenue, Lahug, Cebu City',
    'tagline'     => 'Smart, eco-forward hotel for modern travelers',
    'description' => 'Cebu\'s trendiest mid-range hotel. Harolds Evotel blends smart-room tech with sustainable design — EV charging, energy-saving systems, and a rooftop bar with killer views of the Cebu cityscape.',
    'price'       => 2200,
    'stars'       => 3,
    'rating'      => 4.5,
    'reviews'     => 151,
    'gradient'    => 'linear-gradient(135deg, #f4a261 0%, #c45c0c 100%)',
    'color'       => '#e07020',
    'badge_color' => '#fef3c7',
    'badge_text'  => '#78350f',
    'amenities'   => ['Free WiFi', 'Restaurant', 'Rooftop Bar', 'Smart TV', 'EV Charging', 'Eco-Certified', 'Co-working Space'],
    'highlights'  => ['Trendy rooftop bar', 'Eco-certified property', 'Best mid-range value in Cebu'],
    'rooms'       => [
      ['type' => 'Smart Room',     'beds' => '1 Queen Bed',  'size' => '18 sqm', 'price' => 2200],
      ['type' => 'Deluxe Smart',   'beds' => '1 King Bed',   'size' => '24 sqm', 'price' => 2900],
      ['type' => 'Studio Suite',   'beds' => '1 King Bed',   'size' => '34 sqm', 'price' => 4200],
    ],
  ],
  'mallberry-suites' => [
    'name'        => 'Mallberry Suites',
    'location'    => 'Cagayan de Oro, Gateway to Bukidnon',
    'tagline'     => 'Premier hotel at the heart of CDO',
    'description' => 'The top hotel choice for travelers exploring Bukidnon via Cagayan de Oro. Modern suites, a large outdoor pool, and excellent dining make this a great starting point for Dahilayan tours and highland adventures.',
    'price'       => 3500,
    'stars'       => 4,
    'rating'      => 4.6,
    'reviews'     => 98,
    'gradient'    => 'linear-gradient(135deg, #5c8a60 0%, #2e5232 100%)',
    'color'       => '#5c8a60',
    'badge_color' => '#d1e7d2',
    'badge_text'  => '#1a3a1c',
    'amenities'   => ['Outdoor Pool', 'Gym', 'Restaurant', 'Free WiFi', 'Parking', 'Conference Rooms', 'Airport Shuttle'],
    'highlights'  => ['Gateway to Bukidnon highlands', '1 hour from Dahilayan Park', 'Best hotel in CDO'],
    'rooms'       => [
      ['type' => 'Deluxe Room',    'beds' => '1 King Bed',    'size' => '28 sqm', 'price' => 3500],
      ['type' => 'Junior Suite',   'beds' => '1 King + Sofa', 'size' => '45 sqm', 'price' => 5500],
      ['type' => 'Executive Suite', 'beds' => '1 King Bed',    'size' => '58 sqm', 'price' => 7500],
    ],
  ],
  'dahilayan-forest-park' => [
    'name'        => 'Dahilayan Forest Park Resort',
    'location'    => 'Manolo Fortich, Bukidnon',
    'tagline'     => 'Sleep inside Asia\'s longest zipline park',
    'description' => 'Wake up surrounded by pine forests at 1,500 meters above sea level. This resort sits directly inside Dahilayan Adventure Park — Asia\'s longest zipline is literally steps from your door. For adventure lovers, this is a dream.',
    'price'       => 4200,
    'stars'       => 4,
    'rating'      => 4.7,
    'reviews'     => 143,
    'gradient'    => 'linear-gradient(135deg, #6a994e 0%, #386641 100%)',
    'color'       => '#6a994e',
    'badge_color' => '#d1e7d2',
    'badge_text'  => '#1a3a1c',
    'amenities'   => ['Adventure Park Access', 'Asia\'s Longest Zipline', 'Restaurant', 'Free WiFi', 'Fireplace in Rooms', 'Nature Trails', 'Bonfire Area'],
    'highlights'  => ['Inside Dahilayan Adventure Park', 'Cool 18°C mountain air', 'Stargazing deck on-site'],
    'rooms'       => [
      ['type' => 'Forest Cabin',       'beds' => '2 Single Beds',  'size' => '24 sqm', 'price' => 4200],
      ['type' => 'Deluxe Cabin',       'beds' => '1 Queen Bed',    'size' => '30 sqm', 'price' => 5500],
      ['type' => 'Family Forest Villa', 'beds' => '2 Queen Beds',   'size' => '52 sqm', 'price' => 8500],
    ],
  ],
  'citi-inn-bukidnon' => [
    'name'        => 'Citi Inn Bukidnon',
    'location'    => 'Malaybalay City, Bukidnon',
    'tagline'     => 'Clean, affordable base for highland tours',
    'description' => 'A no-fuss inn in Malaybalay City, the provincial capital of Bukidnon. Clean rooms, all essentials covered, and a friendly team who can arrange day tours to the province\'s top attractions.',
    'price'       => 1500,
    'stars'       => 2,
    'rating'      => 4.2,
    'reviews'     => 47,
    'gradient'    => 'linear-gradient(135deg, #8ab87a 0%, #4a7c59 100%)',
    'color'       => '#4a7c59',
    'badge_color' => '#d1e7d2',
    'badge_text'  => '#1a3a1c',
    'amenities'   => ['Free WiFi', 'Parking', 'Hot Shower', '24hr Front Desk', 'Tour Desk', 'Laundry Service'],
    'highlights'  => ['Heart of Malaybalay City', 'Tour arrangements available', 'Most affordable in Bukidnon'],
    'rooms'       => [
      ['type' => 'Standard Room',  'beds' => '1 Double Bed',  'size' => '16 sqm', 'price' => 1500],
      ['type' => 'Deluxe Room',    'beds' => '1 Queen Bed',   'size' => '20 sqm', 'price' => 2000],
      ['type' => 'Family Room',    'beds' => '2 Twin Beds',   'size' => '26 sqm', 'price' => 2600],
    ],
  ],
];

// ─── GET HOTEL ───────────────────────────────────────────────────────────────
$id = isset($_GET['id']) ? trim($_GET['id']) : '';

$hotel = $hotels[$id] ?? null;

if (!$hotel):
?>
  <div class="not-found">
    <h2>Hotel Not Found</h2>
    <p>The hotel you're looking for doesn't exist or may have been removed.</p>
    <a href="index.php#hotels">← Back to hotels</a>
  </div>
<?php
  exit;
endif;

// ─── HELPERS ─────────────────────────────────────────────────────────────────
function stars(int|float $rating): string
{
  $rating = min(5, max(0, $rating));
  $full = (int) floor($rating);
  return str_repeat('★', $full) . str_repeat('☆', 5 - $full);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($hotel['name']) ?> — Lakbaylokal</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/hotel.css" />
</head>

<body>

  <!-- Back Button -->
  <a class="back-nav" href="index.php#hotels">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <path d="M19 12H5M12 5l-7 7 7 7" />
    </svg>
    Back to Hotels
  </a>

  <!-- ── HERO ── -->
  <section class="hero">
    <div class="hero-texture"></div>
    <div class="hero-content">
      <div class="hero-location">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
          <circle cx="12" cy="9" r="2.5" />
        </svg>
        <?= htmlspecialchars($hotel['location']) ?>
      </div>
      <div class="hero-stars"><?= stars($hotel['stars']) ?></div>
      <h1 class="hero-name"><?= htmlspecialchars($hotel['name']) ?></h1>
      <p class="hero-tagline"><?= htmlspecialchars($hotel['tagline']) ?></p>
    </div>
    <div class="hero-rating">
      <div class="rating-number"><?= $hotel['rating'] ?></div>
      <div class="rating-stars"><?= str_repeat('★', round($hotel['rating'])) ?></div>
      <div class="rating-label"><?= number_format($hotel['reviews']) ?> reviews</div>
    </div>
  </section>

  <!-- ── MAIN ── -->
  <div class="main">

    <!-- LEFT COLUMN -->
    <div class="left-col">

      <!-- About -->
      <div class="about-block">
        <h2 class="section-title">About this hotel</h2>
        <p class="about-text"><?= htmlspecialchars($hotel['description']) ?></p>
        <div class="highlights">
          <?php foreach (($hotel['highlights'] ?? []) as $h): ?>
            <div class="highlight-item">
              <div class="highlight-dot"></div>
              <?= htmlspecialchars($h) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Amenities -->
      <div class="amenities-block">
        <h2 class="section-title">What's included</h2>
        <div class="amenities-grid">
          <?php foreach (($hotel['amenities'] ?? []) as $am): ?>
            <div class="amenity-tag">
              <div class="amenity-check">✓</div>
              <?= htmlspecialchars($am) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Room Selection -->
      <div class="rooms-block">
        <h2 class="section-title">Choose your room</h2>
        <div class="room-list">
          <?php foreach (($hotel['rooms'] ?? []) as $i => $room): ?>
            <div class="room-card" onclick='selectRoom(this, <?= $i ?>, <?= json_encode($room["type"], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$room["price"] ?>)'
              <div class="room-info">
              <div class="room-type"><?= htmlspecialchars($room['type']) ?></div>
              <div class="room-meta">
                <span>🛏 <?= htmlspecialchars($room['beds']) ?></span>
                <span>📐 <?= htmlspecialchars($room['size']) ?></span>
              </div>
              <div class="room-select-badge">Selected ✓</div>
            </div>
            <div class="room-price-col">
              <div class="room-price">₱<?= number_format($room['price']) ?></div>
              <div class="room-per">/ night</div>
            </div>
        </div>
      <?php endforeach; ?>
      </div>
    </div>

    <!-- Sample Reviews -->
    <div class="reviews-block">
      <h2 class="section-title">Guest reviews</h2>
      <div class="reviews-summary">
        <div class="rs-number"><?= $hotel['rating'] ?? 0 ?></div>
        <div>
          <div class="rs-stars"><?= stars($hotel['rating'] ?? 0) ?></div>
          <div style="font-size:.9rem; font-weight:600; color:var(--deep);">
            <?= ($hotel['rating'] ?? 0) >= 4.8 ? 'Exceptional' : (($hotel['rating'] ?? 0) >= 4.5 ? 'Excellent' : 'Very Good') ?>
          </div>
          <div class="rs-count"><?= number_format($hotel['reviews'] ?? 0) ?> verified reviews</div>
        </div>
      </div>
      <!-- Static sample reviews (in real system: from DB) -->
      <div class="review-card">
        <div class="rc-stars">★★★★★</div>
        <p class="rc-text">"Absolutely loved our stay here. The staff were incredibly welcoming, the room was spotless, and the location couldn't have been more convenient for our trip. Would definitely book again!"</p>
        <div class="rc-author">
          <div class="rc-avatar">MR</div>
          <div>
            <div class="rc-name">Maria Reyes</div>
            <div class="rc-date">May 2025</div>
          </div>
        </div>
      </div>
      <div class="review-card">
        <div class="rc-stars">★★★★<?= $hotel['rating'] >= 4.7 ? '★' : '☆' ?></div>
        <p class="rc-text">"Great value for the price. Check-in was smooth, beds were super comfortable, and we slept like logs after a long day of exploring. The breakfast was a nice bonus."</p>
        <div class="rc-author">
          <div class="rc-avatar">JL</div>
          <div>
            <div class="rc-name">James Lim</div>
            <div class="rc-date">April 2025</div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /left-col -->

  <!-- RIGHT: BOOKING CARD -->
  <div>
    <div class="booking-card">
      <div class="booking-header">
        <div class="bh-price">
          ₱<?= number_format($hotel['price']) ?><span> / night</span>
        </div>
        <div class="bh-label">Starting price · taxes not included</div>
        <div class="bh-stars"><?= stars($hotel['stars']) ?> &nbsp; <?= $hotel['rating'] ?>/5</div>
      </div>

      <div class="booking-body">

        <!-- Selected room indicator -->
        <div class="selected-room-pill" id="selectedRoomPill">
          <div class="srp-dot"></div>
          <span id="selectedRoomName">No room selected</span>
        </div>

        <!-- Dates -->
        <div class="bk-dates">
          <div class="bk-field">
            <label>Check-in</label>
            <input type="date" id="checkIn" onchange="calcTotal()" />
          </div>
          <div class="bk-field">
            <label>Check-out</label>
            <input type="date" id="checkOut" onchange="calcTotal()" />
          </div>
        </div>

        <!-- Guests -->
        <div class="bk-field">
          <label>Guests</label>
          <select id="guestCount" onchange="calcTotal()">
            <option value="1">1 Guest</option>
            <option value="2" selected>2 Guests</option>
            <option value="3">3 Guests</option>
            <option value="4">4 Guests</option>
          </select>
        </div>

        <!-- Room picker in sidebar (mirrors left column choices) -->
        <div class="bk-field">
          <label>Room Type</label>
          <select id="roomSelect" onchange="syncRoomFromSelect()">
            <option value="">Select a room...</option>
            <?php foreach ($hotel['rooms'] as $i => $room): ?>
              <option value="<?= $i ?>" data-price="<?= $room['price'] ?>">
                <?= htmlspecialchars($room['type']) ?> — ₱<?= number_format($room['price']) ?>/night
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Price breakdown -->
        <div class="bk-total" id="bkTotal">
          <div class="bk-total-row">
            <span id="totalRoomLine">Room</span>
            <span id="totalRoomCost">—</span>
          </div>
          <div class="bk-total-row">
            <span>Taxes & fees (12%)</span>
            <span id="totalTax">—</span>
          </div>
          <div class="bk-total-row grand">
            <span>Total</span>
            <span id="totalGrand">—</span>
          </div>
        </div>

        <button class="bk-btn" onclick="bookNow()">Reserve Now →</button>
        <p class="bk-note">🔒 You won't be charged yet</p>
      </div>
    </div>
  </div>

  </div><!-- /main -->

  <!-- ── CONFIRMATION MODAL ── -->
  <div class="modal-backdrop" id="confirmModal">
    <div class="modal-box">
      <div class="modal-check">✅</div>
      <div class="modal-title">Booking Confirmed!</div>
      <p class="modal-sub">Your reservation at <strong><?= htmlspecialchars($hotel['name']) ?></strong> has been received. A confirmation will be sent to your email.</p>
      <div class="modal-details" id="modalDetails"></div>
      <button class="modal-close-btn" onclick="document.getElementById('confirmModal').classList.remove('open')">Back to Hotel →</button>
    </div>
  </div>

  <script>
    // ── STATE ──────────────────────────────────────────────────────
    let selectedRoomPrice = <?= $hotel['price'] ?>;
    let selectedRoomIndex = -1;

    const roomData = <?= json_encode(array_values($hotel['rooms'] ?? [])) ?>;

    // ── SET DEFAULT DATES ──────────────────────────────────────────
    (function() {
      const today = new Date();
      const tom = new Date(today);
      tom.setDate(tom.getDate() + 1);
      const atot = new Date(today);
      atot.setDate(atot.getDate() + 3);
      document.getElementById('checkIn').value = tom.toISOString().slice(0, 10);
      document.getElementById('checkOut').value = atot.toISOString().slice(0, 10);
      calcTotal();
    })();

    // ── SELECT ROOM (from left column cards) ──────────────────────
    function selectRoom(card, idx, name, price) {
      document.querySelectorAll('.room-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      selectedRoomIndex = idx;
      selectedRoomPrice = price;

      // update pill
      const pill = document.getElementById('selectedRoomPill');
      document.getElementById('selectedRoomName').textContent = name;
      pill.classList.add('visible');

      // sync sidebar dropdown
      document.getElementById('roomSelect').value = idx;
      calcTotal();
    }

    // ── SYNC ROOM FROM SIDEBAR DROPDOWN ───────────────────────────
    function syncRoomFromSelect() {
      const sel = document.getElementById('roomSelect');
      const idx = parseInt(sel.value);
      if (isNaN(idx)) return;
      const room = roomData[idx];
      selectedRoomIndex = idx;
      selectedRoomPrice = room.price;

      // update pill
      document.getElementById('selectedRoomName').textContent = room.type;
      document.getElementById('selectedRoomPill').classList.add('visible');

      // highlight card
      document.querySelectorAll('.room-card').forEach((c, i) => {
        c.classList.toggle('selected', i === idx);
      });
      calcTotal();
    }

    // ── CALCULATE TOTAL ────────────────────────────────────────────
    function calcTotal() {
      const ci = document.getElementById('checkIn').value;
      const co = document.getElementById('checkOut').value;
      if (!ci || !co) return;

      const nights = Math.max(0, (new Date(co) - new Date(ci)) / 864e5);
      if (nights <= 0) return;

      const base = selectedRoomPrice * nights;
      const tax = Math.round(base * 0.12);
      const total = base + tax;

      document.getElementById('totalRoomLine').textContent =
        `₱${selectedRoomPrice.toLocaleString()} × ${nights} night${nights !== 1 ? 's' : ''}`;
      document.getElementById('totalRoomCost').textContent = `₱${base.toLocaleString()}`;
      document.getElementById('totalTax').textContent = `₱${tax.toLocaleString()}`;
      document.getElementById('totalGrand').textContent = `₱${total.toLocaleString()}`;

      document.getElementById('bkTotal').classList.add('visible');
    }

    // ── BOOK NOW ───────────────────────────────────────────────────
    function bookNow() {
      const ci = document.getElementById('checkIn').value;
      const co = document.getElementById('checkOut').value;
      if (!ci || !co) {
        alert('Please select your check-in and check-out dates.');
        return;
      }
      if (selectedRoomIndex < 0) {
        alert('Please select a room type first.');
        return;
      }

      const nights = Math.max(0, (new Date(co) - new Date(ci)) / 864e5);
      const base = selectedRoomPrice * nights;
      const tax = Math.round(base * 0.12);
      const room = roomData[selectedRoomIndex];
      const guests = document.getElementById('guestCount').value;

      document.getElementById('modalDetails').innerHTML = `
      <div class="md-row"><span>Hotel</span><span><?= htmlspecialchars(addslashes($hotel['name'] ?? '')) ?></span></div>
      <div class="md-row"><span>Room</span><span>${room.type}</span></div>
      <div class="md-row"><span>Check-in</span><span>${ci}</span></div>
      <div class="md-row"><span>Check-out</span><span>${co}</span></div>
      <div class="md-row"><span>Guests</span><span>${guests}</span></div>
      <div class="md-row"><span>Nights</span><span>${nights}</span></div>
      <div class="md-row"><span>Total (incl. tax)</span><span>₱${(base + tax).toLocaleString()}</span></div>
    `;
      document.getElementById('confirmModal').classList.add('open');
    }
  </script>
</body>

</html>