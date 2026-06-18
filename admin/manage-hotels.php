<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage = 'hotels';
$msg        = '';
$msgType    = 'success';

// ── Upload helper ─────────────────────────────────────────────────────────
function handleImageUpload(string $field, ?string $currentUrl = ''): string {
    if (empty($_FILES[$field]['name'])) return $currentUrl ?? '';
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    $mime    = mime_content_type($_FILES[$field]['tmp_name']);
    if (!in_array($mime, $allowed)) return $currentUrl;
    $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $name = 'hotel_' . uniqid() . '.' . strtolower($ext);
    $dir  = __DIR__ . '/assets/pics/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $name)) {
        return 'assets/pics/' . $name;
    }
    return $currentUrl;
}

// ── CREATE ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $image_url = handleImageUpload('image_file', '');
    $stmt = $pdo->prepare("
        INSERT INTO hotels
            (destination_id, name, image_url, location, description, stars, price,
             rating, reviews_count, checkin_time, checkout_time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        trim($_POST['destination_id']),
        trim($_POST['name']),
        $image_url,
        trim($_POST['location']),
        trim($_POST['description']),
        (int)$_POST['stars'],
        floatval($_POST['price']),
        floatval($_POST['rating']),
        (int)$_POST['reviews_count'],
        trim($_POST['checkin_time']),
        trim($_POST['checkout_time']),
    ]);
    header('Location: manage-hotels.php?msg=' . urlencode('Hotel added successfully.') . '&type=success');
    exit;
}

// ── UPDATE ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id = trim($_POST['id']);
    $row = $pdo->prepare("SELECT image_url FROM hotels WHERE id = ?");
    $row->execute([$id]);
    $existing  = $row->fetchColumn();
    $image_url = handleImageUpload('image_file', $existing);
    $stmt = $pdo->prepare("
        UPDATE hotels
        SET destination_id=?, name=?, image_url=?, location=?, description=?,
            stars=?, price=?, rating=?, reviews_count=?, checkin_time=?, checkout_time=?
        WHERE id=?
    ");
    $stmt->execute([
        trim($_POST['destination_id']),
        trim($_POST['name']),
        $image_url,
        trim($_POST['location']),
        trim($_POST['description']),
        (int)$_POST['stars'],
        floatval($_POST['price']),
        floatval($_POST['rating']),
        (int)$_POST['reviews_count'],
        trim($_POST['checkin_time']),
        trim($_POST['checkout_time']),
        $id,
    ]);
    header('Location: manage-hotels.php?msg=' . urlencode('Hotel updated successfully.') . '&type=success');
    exit;
}

// ── DELETE ────────────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM hotels WHERE id = ?")->execute([trim($_GET['id'])]);
    header('Location: manage-hotels.php?msg=' . urlencode('Hotel deleted.') . '&type=success');
    exit;
}

// ── EDIT FETCH (JSON) ─────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->execute([trim($_GET['id'])]);
    echo json_encode($stmt->fetch() ?: ['error' => 'Not found']);
    exit;
}

if (isset($_GET['msg'])) {
    $msg     = htmlspecialchars($_GET['msg']);
    $msgType = ($_GET['type'] ?? 'success') === 'error' ? 'error' : 'success';
}

// ── FETCH DESTINATIONS for dropdown ──────────────────────────────────────
$destinations_all = $pdo->query("SELECT id, name FROM destinations ORDER BY name")->fetchAll();

// ── FETCH HOTELS ──────────────────────────────────────────────────────────
$search   = trim($_GET['search'] ?? '');
$dest_filter = trim($_GET['destination_id'] ?? '');
$where    = [];
$params   = [];
if ($search !== '') {
    $where[]  = "(h.name LIKE ? OR h.location LIKE ?)";
    $like     = "%$search%";
    $params   = array_merge($params, [$like, $like]);
}
if ($dest_filter > 0) {
    $where[]  = "h.destination_id = ?";
    $params[] = $dest_filter;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("
    SELECT h.*, d.name AS destination_name
    FROM hotels h
    LEFT JOIN destinations d ON h.destination_id = d.id
    $whereSQL
    ORDER BY h.id DESC
");
$stmt->execute($params);
$hotels = $stmt->fetchAll();
$total  = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotels – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left"><h1>🏨 Hotels</h1></div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('M d, Y (D)') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName) ?></div>
    </div>
  </header>

  <div class="adm-body">
    <div class="adm-page-header-row">
      <div class="adm-page-header" style="margin-bottom:0">
        <h2>Hotels</h2>
        <p>Manage accommodation properties linked to each destination.</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('create')">＋ Add Hotel</button>
    </div>

    <?php if ($msg): ?>
    <div class="adm-alert adm-alert-<?= $msgType ?>" style="margin-top:1.25rem">
      <?= $msgType === 'success' ? '✅' : '❌' ?> <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🏨</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Hotels</div>
          <div class="stat-value"><?= $total ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">⭐</div>
        <div class="adm-stat-body">
          <div class="stat-label">Destinations Covered</div>
          <div class="stat-value"><?= count($destinations_all) ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">🔍</div>
        <div class="adm-stat-body">
          <div class="stat-label">Showing Now</div>
          <div class="stat-value"><?= count($hotels) ?></div>
        </div>
      </div>
    </div>

    <!-- FILTERS + GRID -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:320px">
              <span class="search-icon">🔍</span>
              <input type="text" name="search" class="adm-search"
                     placeholder="Search hotels or locations…"
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="destination_id" class="adm-select" onchange="this.form.submit()">
              <option value="">All Destinations</option>
              <?php foreach ($destinations_all as $dd): ?>
              <option value="<?= $dd['id'] ?>" <?= $dest_filter===$dd['id']?'selected':'' ?>><?= htmlspecialchars($dd['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Search</button>
            <?php if ($search || $dest_filter): ?>
            <a href="manage-hotels.php" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <?php if (empty($hotels)): ?>
      <div class="adm-card-body">
        <div class="adm-empty">
          <div class="empty-icon">🏨</div>
          <h4>No hotels found</h4>
          <p><?= $search || $dest_filter ? 'Try adjusting your filters.' : 'Click "Add Hotel" to get started.' ?></p>
        </div>
      </div>
      <?php else: ?>
      <div class="adm-content-grid" style="padding:1.25rem">
        <?php foreach ($hotels as $h): ?>
        <div class="adm-item-card">
          <?php if (!empty($h['image_url'])): ?>
          <img src="<?= htmlspecialchars($h['image_url']) ?>" alt="<?= htmlspecialchars($h['name']) ?>" class="adm-item-card-img">
          <?php else: ?>
          <div class="adm-item-card-img-placeholder"><span>🏨</span></div>
          <?php endif; ?>
          <div class="adm-item-card-body">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem">
              <h4><?= htmlspecialchars($h['name']) ?></h4>
              <span style="font-size:.78rem;white-space:nowrap;color:#D97706;font-weight:700">
                <?= str_repeat('⭐', min((int)$h['stars'], 5)) ?>
              </span>
            </div>
            <p style="font-size:.75rem;color:var(--primary);font-weight:600;margin:.2rem 0">
              📍 <?= htmlspecialchars($h['destination_name'] ?? '—') ?>
            </p>
            <p style="font-size:.75rem;color:var(--muted);margin-bottom:.3rem">
              <?= htmlspecialchars($h['location']) ?>
            </p>
            <p><?= htmlspecialchars(mb_strimwidth($h['description'], 0, 80, '…')) ?></p>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.5rem">
              <strong style="font-size:.9rem;color:var(--deep)">₱<?= number_format($h['price'], 2) ?>/night</strong>
              <span style="font-size:.75rem;color:var(--muted)">⭐ <?= $h['rating'] ?> (<?= number_format($h['reviews_count']) ?>)</span>
            </div>
          </div>
          <div class="adm-item-card-footer">
            <button class="btn btn-outline btn-sm" onclick="editHotel('<?= htmlspecialchars($h['id']) ?>')">✏️ Edit</button>
            <a href="manage-hotels.php?action=delete&id=<?= $h['id'] ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Delete <?= addslashes($h['name']) ?>?')">
              🗑 Delete
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="padding:.8rem 1.2rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border)">
        Showing <?= count($hotels) ?> of <?= $total ?> hotels
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── MODAL ── -->
<div class="adm-modal-bg" id="hotel-modal">
  <div class="adm-modal" style="max-width:720px;width:95%">
    <div class="adm-modal-header">
      <h3 id="modal-title">Add Hotel</h3>
      <button class="panel-close" onclick="closeModal()">✕</button>
    </div>
    <div class="adm-modal-body" style="max-height:72vh;overflow-y:auto">
      <form id="hotel-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="form-action" value="create">
        <input type="hidden" name="id"     id="form-id"     value="">

        <div class="adm-form-grid">
          <!-- Row 1 -->
          <div class="form-group form-span-2">
            <label for="f-dest">Destination *</label>
            <select id="f-dest" name="destination_id" required>
              <option value="">— Select Destination —</option>
              <?php foreach ($destinations_all as $dd): ?>
              <option value="<?= $dd['id'] ?>"><?= htmlspecialchars($dd['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group form-span-2">
            <label for="f-name">Hotel Name *</label>
            <input type="text" id="f-name" name="name" required placeholder="e.g. Henann Crystal Sands">
          </div>
          <!-- Row 2 -->
          <div class="form-group form-span-2">
            <label for="f-location">Location / Address *</label>
            <input type="text" id="f-location" name="location" required placeholder="e.g. Station 1, Boracay Island">
          </div>
          <!-- Row 3 -->
          <div class="form-group">
            <label for="f-stars">Star Rating *</label>
            <select id="f-stars" name="stars" required>
              <option value="">— Stars —</option>
              <?php for ($s=1;$s<=5;$s++): ?>
              <option value="<?= $s ?>"><?= $s ?> Star<?= $s>1?'s':'' ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="f-price">Price per Night (₱) *</label>
            <input type="number" id="f-price" name="price" required min="0" step="0.01" placeholder="3500.00">
          </div>
          <!-- Row 4 -->
          <div class="form-group">
            <label for="f-rating">Guest Rating (0–5)</label>
            <input type="number" id="f-rating" name="rating" min="0" max="5" step="0.1" placeholder="4.5">
          </div>
          <div class="form-group">
            <label for="f-reviews">Reviews Count</label>
            <input type="number" id="f-reviews" name="reviews_count" min="0" placeholder="1200">
          </div>
          <!-- Row 5 -->
          <div class="form-group">
            <label for="f-checkin">Check-in Time</label>
            <input type="text" id="f-checkin" name="checkin_time" placeholder="e.g. 2:00 PM">
          </div>
          <div class="form-group">
            <label for="f-checkout">Check-out Time</label>
            <input type="text" id="f-checkout" name="checkout_time" placeholder="e.g. 12:00 PM">
          </div>
          <!-- Description -->
          <div class="form-group form-span-2">
            <label for="f-desc">Description</label>
            <textarea id="f-desc" name="description" rows="3" placeholder="Brief description of the hotel…"></textarea>
          </div>
          <!-- Image -->
          <div class="form-group form-span-2">
            <label for="f-image">Hotel Image</label>
            <input type="file" id="f-image" name="image_file" accept="image/*">
            <span class="form-hint">JPG, PNG, WEBP. Saved to assets/pics/. Leave blank to keep current image.</span>
            <div id="img-preview" style="margin-top:.5rem"></div>
          </div>
        </div>
      </form>
    </div>
    <div class="adm-modal-footer">
      <button class="btn btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="submitHotelForm()" id="modal-submit-btn">Save Hotel</button>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById('hotel-modal');

function openModal(mode) {
  document.getElementById('hotel-form').reset();
  document.getElementById('img-preview').innerHTML = '';
  document.getElementById('modal-title').textContent      = 'Add Hotel';
  document.getElementById('modal-submit-btn').textContent = 'Save Hotel';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value     = '';
  modal.classList.add('open');
}

function closeModal() { modal.classList.remove('open'); }
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

function submitHotelForm() {
  const form = document.getElementById('hotel-form');
  const submitBtn = document.getElementById('modal-submit-btn');
  
  // Validate
  const name = document.getElementById('f-name').value.trim();
  const location = document.getElementById('f-location').value.trim();
  const destination = document.getElementById('f-dest').value.trim();
  const price = document.getElementById('f-price').value.trim();
  
  if (!name || !location || !destination || !price) {
    alert('Please fill in all required fields: Hotel Name, Location, Destination, and Price.');
    return;
  }
  
  if (isNaN(parseFloat(price)) || parseFloat(price) < 0) {
    alert('Price must be a valid positive number.');
    return;
  }
  
  // Show loading
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ Saving...';
  
  // Submit form
  form.submit();
}

function editHotel(id) {
  document.getElementById('hotel-form').reset();
  document.getElementById('img-preview').innerHTML = '';
  document.getElementById('modal-title').textContent      = 'Edit Hotel';
  document.getElementById('modal-submit-btn').textContent = 'Update Hotel';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value     = id;

  fetch(`manage-hotels.php?action=get&id=${id}`)
    .then(r => {
      if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
      return r.json();
    })
    .then(h => {
      if (h.error) { 
        alert('Could not load hotel: ' + (h.error || 'Unknown error'));
        return; 
      }
      document.getElementById('f-dest').value     = h.destination_id || '';
      document.getElementById('f-name').value     = h.name           || '';
      document.getElementById('f-location').value = h.location       || '';
      document.getElementById('f-stars').value    = h.stars          || '';
      document.getElementById('f-price').value    = h.price          || '';
      document.getElementById('f-rating').value   = h.rating         || '';
      document.getElementById('f-reviews').value  = h.reviews_count  || '';
      document.getElementById('f-checkin').value  = h.checkin_time   || '';
      document.getElementById('f-checkout').value = h.checkout_time  || '';
      document.getElementById('f-desc').value     = h.description    || '';
      if (h.image_url) {
        document.getElementById('img-preview').innerHTML =
          `<img src="${h.image_url}" style="max-height:120px;border-radius:8px;border:1px solid var(--border)" alt="Current">
           <p class="form-hint" style="margin-top:.3rem">Current image shown above.</p>`;
      }
      modal.classList.add('open');
    })
    .catch(e => {
      console.error('Error loading hotel:', e);
      alert('Error loading hotel. Check console for details.');
    });
}

document.getElementById('f-image').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('img-preview').innerHTML =
      `<img src="${e.target.result}" style="max-height:120px;border-radius:8px;border:1px solid var(--border)" alt="Preview">
       <p class="form-hint" style="margin-top:.3rem">New image preview.</p>`;
  };
  reader.readAsDataURL(file);
});
</script>
</body>
</html>