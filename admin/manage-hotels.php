<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage    = 'hotels';
$msg           = '';
$msgType       = 'success';
$show_archived = isset($_GET['show_archived']) && $_GET['show_archived'] === '1';

$schemaCheck = $pdo->query("SHOW COLUMNS FROM hotels LIKE 'archived'")->fetch();
if (!$schemaCheck) {
    $pdo->exec("ALTER TABLE hotels ADD COLUMN archived TINYINT(1) NOT NULL DEFAULT 0");
}

function handleImageUpload(string $field, ?string $currentUrl = ''): string {
    if (empty($_FILES[$field]['name'])) return $currentUrl ?? '';
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    $mime    = mime_content_type($_FILES[$field]['tmp_name']);
    if (!in_array($mime, $allowed)) return $currentUrl;
    $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $name = 'hotel_' . uniqid() . '.' . strtolower($ext);
    $dir  = dirname(__DIR__) . '/assets/pics/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $name)) return 'assets/pics/' . $name;
    return $currentUrl;
}

function resolveAdminImageSrc(?string $imageUrl): string {
    $imageUrl = trim((string)($imageUrl ?? ''));
    if ($imageUrl === '') return '';

    $imageUrl = str_replace('\\', '/', $imageUrl);
    if (preg_match('/^(https?:)?\/\//i', $imageUrl) || str_starts_with($imageUrl, 'data:') || str_starts_with($imageUrl, '/')) {
        return $imageUrl;
    }

    if (is_file(__DIR__ . '/' . $imageUrl)) return $imageUrl;
    if (is_file(dirname(__DIR__) . '/' . $imageUrl)) return '../' . $imageUrl;

    return $imageUrl;
}

// Helper para i-save ang relational tags (Amenities & Policies)
function saveHotelRelations($pdo, $hotelId, $amenities, $policies) {
    // 1. I-clear ang lumang entries
    $stmt1 = $pdo->prepare("DELETE FROM hotel_amenities WHERE hotel_id = ?");
    $stmt1->execute([$hotelId]);
    $stmt2 = $pdo->prepare("DELETE FROM hotel_policies WHERE hotel_id = ?");
    $stmt2->execute([$hotelId]);

    // 2. Ipasok ang mga bagong Amenities
    if (!empty($amenities)) {
        $stmt = $pdo->prepare("INSERT INTO hotel_amenities (hotel_id, amenity_name) VALUES (?, ?)");
        foreach ($amenities as $amenity) {
            $amenity = trim($amenity);
            if ($amenity !== '') $stmt->execute([$hotelId, $amenity]);
        }
    }

    // 3. Ipasok ang mga bagong Policies
    if (!empty($policies)) {
        $stmt = $pdo->prepare("INSERT INTO hotel_policies (hotel_id, policy) VALUES (?, ?)");
        foreach ($policies as $policy) {
            $policy = trim($policy);
            if ($policy !== '') $stmt->execute([$hotelId, $policy]);
        }
    }
}

// ── CRUD ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $hotel_id = 'hotel_' . uniqid(); // Gumawa ng unique custom string ID
    $image_url = handleImageUpload('image_file', '');
    
    $stmt = $pdo->prepare("INSERT INTO hotels (id,destination_id,name,image_url,location,description,stars,price,rating,reviews_count,checkin_time,checkout_time) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$hotel_id,trim($_POST['destination_id']),trim($_POST['name']),$image_url,trim($_POST['location']),trim($_POST['description']),(int)$_POST['stars'],floatval($_POST['price']),floatval($_POST['rating']),(int)$_POST['reviews_count'],trim($_POST['checkin_time']),trim($_POST['checkout_time'])]);
    
    // Kunin ang arrays galing sa dynamic tagging inputs
    $amenities = $_POST['amenities'] ?? [];
    $policies = $_POST['policies'] ?? [];
    saveHotelRelations($pdo, $hotel_id, $amenities, $policies);

    header('Location: manage-hotels.php?msg='.urlencode('Hotel added successfully.').'&type=success'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id = trim($_POST['id']);
    $row = $pdo->prepare("SELECT image_url FROM hotels WHERE id=?"); $row->execute([$id]);
    $image_url = handleImageUpload('image_file', $row->fetchColumn());
    
    $stmt = $pdo->prepare("UPDATE hotels SET destination_id=?,name=?,image_url=?,location=?,description=?,stars=?,price=?,rating=?,reviews_count=?,checkin_time=?,checkout_time=? WHERE id=?");
    $stmt->execute([trim($_POST['destination_id']),trim($_POST['name']),$image_url,trim($_POST['location']),trim($_POST['description']),(int)$_POST['stars'],floatval($_POST['price']),floatval($_POST['rating']),(int)$_POST['reviews_count'],trim($_POST['checkin_time']),trim($_POST['checkout_time']),$id]);
    
    // Kunin at i-update ang relasyon ng tags
    $amenities = $_POST['amenities'] ?? [];
    $policies = $_POST['policies'] ?? [];
    saveHotelRelations($pdo, $id, $amenities, $policies);

    header('Location: manage-hotels.php?msg='.urlencode('Hotel updated.').'&type=success'); exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE hotels SET archived=1 WHERE id=?")->execute([trim($_GET['id'])]);
    header('Location: manage-hotels.php?msg='.urlencode('Hotel archived.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'unarchive' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE hotels SET archived=0 WHERE id=?")->execute([trim($_GET['id'])]);
    header('Location: manage-hotels.php?show_archived=1&msg='.urlencode('Hotel restored.').'&type=success'); exit;
}

// API endpoint para sa AJAX load details ng modal edit
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id=?"); $stmt->execute([trim($_GET['id'])]);
    $hotel = $stmt->fetch();
    if ($hotel) {
        $hotel['image_src'] = resolveAdminImageSrc($hotel['image_url'] ?? '');
        
        // Isama ang mga Amenities
        $amStmt = $pdo->prepare("SELECT amenity_name FROM hotel_amenities WHERE hotel_id = ?");
        $amStmt->execute([$hotel['id']]);
        $hotel['amenities'] = $amStmt->fetchAll(PDO::FETCH_COLUMN);

        // Isama ang mga Policies
        $polStmt = $pdo->prepare("SELECT policy FROM hotel_policies WHERE hotel_id = ?");
        $polStmt->execute([$hotel['id']]);
        $hotel['policies'] = $polStmt->fetchAll(PDO::FETCH_COLUMN);
    }
    echo json_encode($hotel ?: ['error'=>'Not found']); exit;
}

if (isset($_GET['msg'])) { $msg = htmlspecialchars($_GET['msg']); $msgType = ($_GET['type']??'success')==='error'?'error':'success'; }

// ── Fetch ──────────────────────────────────────────────────────────────────
$destinations_all = $pdo->query("SELECT id,name FROM destinations ORDER BY name")->fetchAll();
$search      = trim($_GET['search'] ?? '');
$dest_filter = trim($_GET['destination_id'] ?? '');
$where = []; $params = [];
$where[] = $show_archived ? 'h.archived=1' : 'h.archived=0';
if ($search !== '') { $where[] = "(h.name LIKE ? OR h.location LIKE ?)"; $like="%$search%"; $params=array_merge($params,[$like,$like]); }
if ($dest_filter !== '') { $where[] = "h.destination_id=?"; $params[] = $dest_filter; }
$whereSQL = 'WHERE '.implode(' AND ',$where);
$stmt = $pdo->prepare("SELECT h.*, d.name AS destination_name FROM hotels h LEFT JOIN destinations d ON h.destination_id=d.id $whereSQL ORDER BY h.id DESC");
$stmt->execute($params);
$hotels         = $stmt->fetchAll();
$total          = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$active_count   = $pdo->query("SELECT COUNT(*) FROM hotels WHERE archived=0")->fetchColumn();
$archived_count = $pdo->query("SELECT COUNT(*) FROM hotels WHERE archived=1")->fetchColumn();
$avg_price      = $pdo->query("SELECT AVG(price) FROM hotels WHERE archived=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotels – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  
  <style>
    /* Estilo para sa Dynamic Tag Generator Controls */
    .tag-input-container {
        border: 1px solid var(--border, #ccc);
        border-radius: 6px;
        padding: 0.5rem;
        background: #fff;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .tag-field-row {
        display: flex;
        gap: 0.5rem;
    }
    .tag-field-row input {
        flex: 1;
        margin-bottom: 0 !important;
    }
    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.25rem;
    }
    .tag-item {
        background: var(--bg-tint, #f1f5f9);
        color: var(--deep, #1e293b);
        border: 1px solid var(--border, #cbd5e1);
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .tag-item .remove-tag-btn {
        color: #ef4444;
        cursor: pointer;
        font-weight: bold;
        border: none;
        background: transparent;
        padding: 0;
        font-size: 0.85rem;
    }
    .tag-item .remove-tag-btn:hover {
        color: #b91c1c;
    }
  </style>
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
      <?= $msgType==='success'?'✅':'❌' ?> <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🏨</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Hotels</div>
          <div class="stat-value"><?= $total ?></div>
          <div class="stat-sub">All-time</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">✅</div>
        <div class="adm-stat-body">
          <div class="stat-label">Active Hotels</div>
          <div class="stat-value"><?= $active_count ?></div>
          <div class="stat-sub">Currently listed</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">💰</div>
        <div class="adm-stat-body">
          <div class="stat-label">Avg. Price / Night</div>
          <div class="stat-value">₱<?= $avg_price ? number_format($avg_price,0) : '—' ?></div>
          <div class="stat-sub">Active hotels</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">🗄️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Archived Hotels</div>
          <div class="stat-value"><?= $archived_count ?></div>
          <div class="stat-sub">Hidden from users</div>
        </div>
      </div>
    </div>

    <!-- FILTERS + GRID -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:300px">
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
            <?php if ($show_archived): ?><input type="hidden" name="show_archived" value="1"><?php endif; ?>
            <button type="submit" class="btn btn-outline btn-sm">Search</button>
            <?php if ($search || $dest_filter): ?>
            <a href="manage-hotels.php<?= $show_archived?'?show_archived=1':'' ?>" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
        <div style="display:flex;gap:.5rem">
          <?php if ($show_archived): ?>
          <a href="manage-hotels.php" class="btn btn-outline btn-sm">📋 Show Active</a>
          <?php else: ?>
          <a href="manage-hotels.php?show_archived=1" class="btn btn-ghost btn-sm">🗄️ Archived (<?= $archived_count ?>)</a>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($show_archived): ?>
      <div style="padding:.6rem 1.5rem;background:var(--gold-pale);border-bottom:1px solid #FDE68A;font-size:.83rem;color:#92400E;display:flex;align-items:center;gap:.5rem">
        🗄️ Viewing archived hotels — these are hidden from users.
      </div>
      <?php endif; ?>

      <?php if (empty($hotels)): ?>
      <div class="adm-card-body">
        <div class="adm-empty">
          <div class="empty-icon">🏨</div>
          <h4>No hotels found</h4>
          <p><?= ($search || $dest_filter) ? 'Try adjusting your filters.' : 'Click "Add Hotel" to get started.' ?></p>
        </div>
      </div>
      <?php else: ?>
      <div class="adm-content-grid" style="padding:1.25rem">
        <?php foreach ($hotels as $h):
          $imageSrc = resolveAdminImageSrc($h['image_url'] ?? '');
        ?>
        <div class="adm-item-card">
          <?php if ($imageSrc !== ''): ?>
          <img src="<?= htmlspecialchars($imageSrc) ?>" alt="<?= htmlspecialchars($h['name']) ?>" class="adm-item-card-img">
          <?php else: ?>
          <div class="adm-item-card-img-placeholder"><span>🏨</span></div>
          <?php endif; ?>

          <div class="adm-item-card-body">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem;margin-bottom:.2rem">
              <h4 style="font-size:.95rem;font-weight:700;color:var(--deep)"><?= htmlspecialchars($h['name']) ?></h4>
              <span class="stars" style="flex-shrink:0;font-size:.75rem"><?= str_repeat('⭐', min((int)$h['stars'],5)) ?></span>
            </div>
            <p style="font-size:.73rem;color:var(--primary);font-weight:600;margin-bottom:.2rem">
              📍 <?= htmlspecialchars($h['destination_name'] ?? '—') ?>
            </p>
            <p style="font-size:.75rem;color:var(--muted);margin-bottom:.3rem"><?= htmlspecialchars($h['location']) ?></p>
            <p style="font-size:.8rem;color:var(--deep);line-height:1.5"><?= htmlspecialchars(mb_strimwidth($h['description'], 0, 75, '…')) ?></p>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.5rem">
              <strong style="font-size:.9rem;color:var(--deep)">₱<?= number_format($h['price'],2) ?>/night</strong>
              <span style="font-size:.73rem;color:var(--muted)">⭐ <?= $h['rating'] ?> <span style="opacity:.6">(<?= number_format($h['reviews_count']) ?>)</span></span>
            </div>
            <?php if ($h['checkin_time'] || $h['checkout_time']): ?>
            <div style="font-size:.72rem;color:var(--muted);margin-top:.3rem;display:flex;gap:.75rem">
              <?php if ($h['checkin_time']): ?><span>🔑 In: <?= htmlspecialchars($h['checkin_time']) ?></span><?php endif; ?>
              <?php if ($h['checkout_time']): ?><span>🚪 Out: <?= htmlspecialchars($h['checkout_time']) ?></span><?php endif; ?>
            </div>
            <?php endif; ?>
          </div>

          <div class="adm-item-card-footer">
            <button class="btn btn-outline btn-sm" onclick="editHotel('<?= htmlspecialchars($h['id']) ?>')">✏️ Edit</button>
            <?php if ($show_archived): ?>
            <a href="manage-hotels.php?action=unarchive&id=<?= $h['id'] ?>"
               class="btn btn-primary btn-sm"
               onclick="return confirm('Restore <?= addslashes($h['name']) ?>?')">↩️ Restore</a>
            <?php else: ?>
            <a href="manage-hotels.php?action=delete&id=<?= $h['id'] ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Archive «<?= addslashes($h['name']) ?>»?')">🗄️ Archive</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="padding:.75rem 1.5rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
        <span>Showing <strong><?= count($hotels) ?></strong> of <strong><?= $total ?></strong> hotels</span>
        <?php if ($show_archived): ?><a href="manage-hotels.php" class="btn btn-ghost btn-sm" style="font-size:.78rem">← Back to Active</a><?php endif; ?>
      </div>
      <?php endif; ?>
    </div><!-- /adm-card -->

  </div>
</div>

<!-- ══════════════════════════════════ MODAL ══════════════════════════════════ -->
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

          <div class="form-group form-span-2">
            <label for="f-dest">Destination <span style="color:var(--primary)">*</span></label>
            <select id="f-dest" name="destination_id" required>
              <option value="">— Select Destination —</option>
              <?php foreach ($destinations_all as $dd): ?>
              <option value="<?= $dd['id'] ?>"><?= htmlspecialchars($dd['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group form-span-2">
            <label for="f-name">Hotel Name <span style="color:var(--primary)">*</span></label>
            <input type="text" id="f-name" name="name" required placeholder="e.g. Henann Crystal Sands">
          </div>

          <div class="form-group form-span-2">
            <label for="f-location">Location / Address <span style="color:var(--primary)">*</span></label>
            <input type="text" id="f-location" name="location" required placeholder="e.g. Station 1, Boracay Island, Malay, Aklan">
          </div>

          <div class="form-group">
            <label for="f-stars">Star Rating <span style="color:var(--primary)">*</span></label>
            <select id="f-stars" name="stars" required>
              <option value="">— Stars —</option>
              <?php for ($s=1;$s<=5;$s++): ?>
              <option value="<?= $s ?>"><?= $s ?> Star<?= $s>1?'s':'' ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="f-price">Price per Night (₱) <span style="color:var(--primary)">*</span></label>
            <input type="number" id="f-price" name="price" required min="0" step="0.01" placeholder="3500.00">
          </div>

          <div class="form-group">
            <label for="f-rating">Guest Rating (0–5)</label>
            <input type="number" id="f-rating" name="rating" min="0" max="5" step="0.1" placeholder="4.5">
          </div>
          <div class="form-group">
            <label for="f-reviews">Reviews Count</label>
            <input type="number" id="f-reviews" name="reviews_count" min="0" placeholder="1200">
          </div>

          <div class="form-group">
            <label for="f-checkin">Check-in Time</label>
            <input type="text" id="f-checkin" name="checkin_time" placeholder="e.g. 2:00 PM">
          </div>
          <div class="form-group">
            <label for="f-checkout">Check-out Time</label>
            <input type="text" id="f-checkout" name="checkout_time" placeholder="e.g. 12:00 PM">
          </div>

          <!-- DYNAMIC AMENITIES INCLUSION -->
          <div class="form-group form-span-2">
            <label>Hotel Amenities</label>
            <div class="tag-input-container">
              <div class="tag-field-row">
                <input type="text" id="amenity-input" placeholder="Type an amenity (e.g. Free Swimming Pool, Rooftop Bar)">
                <button type="button" class="btn btn-outline btn-sm" onclick="addTag('amenity')">＋ Add</button>
              </div>
              <div id="amenity-list" class="tag-list"></div>
            </div>
          </div>

          <!-- DYNAMIC POLICIES INCLUSION -->
          <div class="form-group form-span-2">
            <label>Hotel Policies</label>
            <div class="tag-input-container">
              <div class="tag-field-row">
                <input type="text" id="policy-input" placeholder="Type a policy (e.g. No pets allowed, 100% Non-Smoking)">
                <button type="button" class="btn btn-outline btn-sm" onclick="addTag('policy')">＋ Add</button>
              </div>
              <div id="policy-list" class="tag-list"></div>
            </div>
          </div>

          <div class="form-group form-span-2">
            <label for="f-desc">Description</label>
            <textarea id="f-desc" name="description" rows="3" placeholder="Brief description of the hotel, amenities, and what makes it special…"></textarea>
          </div>

          <div class="form-group form-span-2">
            <label for="f-image">Hotel Image</label>
            <input type="file" id="f-image" name="image_file" accept="image/*">
            <span class="form-hint">JPG, PNG, or WEBP. Saved to assets/pics/. Leave blank to keep current image when editing.</span>
            <div id="img-preview" style="margin-top:.6rem"></div>
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

// Buksan ang modal at i-reset ang listahan ng custom tags
function openModal(mode) {
  document.getElementById('hotel-form').reset();
  document.getElementById('img-preview').innerHTML = '';
  document.getElementById('amenity-list').innerHTML = '';
  document.getElementById('policy-list').innerHTML = '';
  document.getElementById('modal-title').textContent      = 'Add Hotel';
  document.getElementById('modal-submit-btn').textContent = 'Save Hotel';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value     = '';
  modal.classList.add('open');
}

function closeModal() { modal.classList.remove('open'); }
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

// ── TAGS UTILITIES ENGINE ──────────────────────────────────────────────────
// Uri: 'amenity' o 'policy'
function addTag(type, value = null) {
  const inputEl = document.getElementById(`${type}-input`);
  const listEl = document.getElementById(`${type}-list`);
  const textValue = value ? value.trim() : inputEl.value.trim();

  if (textValue === "") return;

  // Iwasan ang duplicate entries sa listahan
  const existingInputs = listEl.querySelectorAll(`input[name="${type}s[]"]`);
  for (let input of existingInputs) {
    if (input.value.toLowerCase() === textValue.toLowerCase()) {
      if(!value) inputEl.value = "";
      return;
    }
  }

  // Gumawa ng Tag element framework
  const tagItem = document.createElement('div');
  tagItem.className = 'tag-item';
  tagItem.innerHTML = `
    <span>${escapeHTML(textValue)}</span>
    <input type="hidden" name="${type}s[]" value="${escapeHTML(textValue)}">
    <button type="button" class="remove-tag-btn" onclick="this.parentElement.remove()">✕</button>
  `;

  listEl.appendChild(tagItem);
  if (!value) inputEl.value = ""; // Linisin ang input box kapag tapos na
}

// Suporta para sa enter key triggering sa input elements
document.getElementById('amenity-input').addEventListener('keydown', e => { if(e.key === 'Enter') { e.preventDefault(); addTag('amenity'); } });
document.getElementById('policy-input').addEventListener('keydown', e => { if(e.key === 'Enter') { e.preventDefault(); addTag('policy'); } });

function escapeHTML(str) {
  return str.replace(/[&<>'"]/g, 
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}

function submitHotelForm() {
  const name   = document.getElementById('f-name').value.trim();
  const loc    = document.getElementById('f-location').value.trim();
  const dest   = document.getElementById('f-dest').value.trim();
  const price  = document.getElementById('f-price').value.trim();
  const stars  = document.getElementById('f-stars').value.trim();
  if (!name || !loc || !dest || !price || !stars) { alert('Please fill in all required fields.'); return; }
  if (isNaN(parseFloat(price)) || parseFloat(price) < 0) { alert('Price must be a valid positive number.'); return; }
  const btn = document.getElementById('modal-submit-btn');
  btn.disabled = true; btn.textContent = '⏳ Saving…';
  document.getElementById('hotel-form').submit();
}

function editHotel(id) {
  document.getElementById('hotel-form').reset();
  document.getElementById('img-preview').innerHTML = '';
  document.getElementById('amenity-list').innerHTML = '';
  document.getElementById('policy-list').innerHTML = '';
  
  document.getElementById('modal-title').textContent      = 'Edit Hotel';
  document.getElementById('modal-submit-btn').textContent = 'Update Hotel';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value     = id;

  fetch(`manage-hotels.php?action=get&id=${encodeURIComponent(id)}`)
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(h => {
      if (h.error) { alert('Could not load hotel: ' + h.error); return; }
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
      
      // I-populate ang mga Amenity Tags galing database gamit ang JS loop
      if (h.amenities && Array.isArray(h.amenities)) {
        h.amenities.forEach(amenity => addTag('amenity', amenity));
      }

      // I-populate ang mga Policy Tags galing database gamit ang JS loop
      if (h.policies && Array.isArray(h.policies)) {
        h.policies.forEach(policy => addTag('policy', policy));
      }

      if (h.image_src || h.image_url) {
        document.getElementById('img-preview').innerHTML =
          `<img src="${h.image_src || h.image_url}" style="max-height:120px;border-radius:8px;border:1px solid var(--border)" alt="Current">
           <p class="form-hint" style="margin-top:.3rem">Current image — upload a new file to replace it.</p>`;
      }
      modal.classList.add('open');
    })
    .catch(e => { console.error(e); alert('Error loading hotel details.'); });
}

document.getElementById('f-image').addEventListener('change', function() {
  const file = this.files[0]; if (!file) return;
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