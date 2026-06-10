<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <h3>Lakbay<span>Lokal</span></h3>
      <p>Discover the heartbeat of the Philippines. We combine curated, local-first itineraries with seamless hotel bookings to help you plan, customize, and experience your perfect tropical getaway.</p>
    </div>
    <div class="footer-col">
      <h4>Destinations</h4>
      <ul>
        <?php if (!empty($destinations) && is_array($destinations)): ?>
          <?php foreach (array_slice($destinations, 0, 5) as $d): ?>
            <li><a href="<?= $rootPath ?? '' ?>destinations.php?dest=<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></a></li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="<?= $rootPath ?? '' ?>index.php">Home</a></li>
        <li><a href="<?= $rootPath ?? '' ?>destinations.php">All Destinations</a></li>
        <li><a href="<?= $rootPath ?? '' ?>index.php#about">About Us</a></li>
        <li><a href="<?= $rootPath ?? '' ?>index.php#mytrips">My Trips</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> LakbayLokal. All rights reserved.</span>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmB43xZLNmH8LWbZLjk4bEVPLWEo" crossorigin="anonymous"></script>
<script>window.ROOT_PATH = '<?= $rootPath ?? '' ?>';</script>
<script src="<?= $rootPath ?? '' ?>assets/js/script.js"></script>
</body>
</html>