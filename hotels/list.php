
<?php include '../includes/config.php'; ?>
<?php include '../includes/navbar.php'; ?>
<link rel="stylesheet" href="../assets/styles.css">

<section class="hotels-section" id="hotels">

  <div class="section-header">
    <div class="section-tag">Curated Stays</div>
    <h2 class="section-title" style="color:var(--sand)">
      Featured <em style="color:var(--sun)">Hotels</em>
    </h2>
    <p class="section-sub">
      Hand-picked accommodations for every destination and budget.
    </p>
  </div>

  <div class="hotels-grid">

    <?php
    // TEMP DATA (later DB na to)
    $hotels = [
      [
        "name" => "Hotel Veniz",
        "location" => "Baguio City",
        "stars" => 3,
        "price" => 1800,
        "url" => "https://www.hotelveniz.com"
      ],
      [
        "name" => "The Manor at Camp John Hay",
        "location" => "Baguio City",
        "stars" => 5,
        "price" => 8500,
        "url" => "https://www.campjohnhay.ph/accommodations/the-manor"
      ],
      [
        "name" => "Microtel by Wyndham Baguio",
        "location" => "Baguio City",
        "stars" => 3,
        "price" => 2500,
        "url" => "https://www.wyndhamhotels.com/microtel/baguio-city-philippines"
      ],
      [
        "name" => "Henann Resort Boracay",
        "location" => "Boracay",
        "stars" => 5,
        "price" => 12000,
        "url" => "https://www.henann.com/boracay/henannresort/"
      ],
      [
        "name" => "Fairways & Bluewater",
        "location" => "Boracay",
        "stars" => 4,
        "price" => 6000,
        "url" => "https://www.fairwaysandbluewater.com"
      ]
    ];

    foreach ($hotels as $hotel): ?>
      
      <?php
        // map common location strings to data keys used by the front-end
        $loc = $hotel['location'];
        $key = 'other';
        if (stripos($loc, 'baguio') !== false) $key = 'baguio';
        elseif (stripos($loc, 'boracay') !== false) $key = 'boracay';
        elseif (stripos($loc, 'cebu') !== false) $key = 'cebu';
      ?>

      <div class="hotel-card" data-destination="<?= htmlspecialchars($key) ?>">
        <div class="hotel-dest">📍 <?= $hotel['location'] ?></div>

        <div class="hotel-name">
          <?= $hotel['name'] ?>
        </div>

        <div class="hotel-stars">
          <?= str_repeat("★", $hotel['stars']) . str_repeat("☆", 5 - $hotel['stars']) ?>
        </div>

        <div class="hotel-price">
          ₱<?= number_format($hotel['price']) ?> <span>/ night</span>
        </div>

        <?php if (!empty($hotel['url'])): ?>
          <a href="<?= $hotel['url'] ?>" target="_blank" class="btn-hotel">
            View Hotel →
          </a>
        <?php endif; ?>
      </div>

    <?php endforeach; ?>

  </div>

</section>

<?php include '../includes/footer.php'; ?>