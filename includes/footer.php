<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <h3>Lakbay<span>Lokal</span></h3>
      <p>A hotel booking platform with curated travel itineraries across the Philippines. Plan your perfect trip in minutes.</p>
    </div>
    <div class="footer-col">
      <h4>Destinations</h4>
      <ul>
        <?php foreach (array_slice($destinations, 0, 5) as $d): ?>
          <li onclick="openDest('<?= $d['id'] ?>')"><?= htmlspecialchars($d['name']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li onclick="showPage('home')">Home</li>
        <li onclick="showPage('destinations')">All Destinations</li>
        <li onclick="showPage('about')">About Us</li>
        <li onclick="showPage('dashboard')">My Trips</li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> LakbayLokal. All rights reserved.</span>
    <span>Made with ❤️ for Filipino travelers</span>
  </div>
</footer>

<script>
  const DESTINATIONS = <?= $destinationsJson ?>;
</script>
<script src="assets/script.js"></script>
</body>
</html>