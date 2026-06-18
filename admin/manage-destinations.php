<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage    = 'destinations';
$msg           = '';
$msgType       = 'success';
$show_archived = isset($_GET['show_archived']) && $_GET['show_archived'] === '1';

// Ensure archiving column exists
$schemaCheck = $pdo->query("SHOW COLUMNS FROM destinations LIKE 'archived'")->fetch();
if (!$schemaCheck) {
    $pdo->exec("ALTER TABLE destinations ADD COLUMN archived TINYINT(1) NOT NULL DEFAULT 0");
}

// ── Upload helper ─────────────────────────────────────────────────────────
function handleImageUpload(string $field, ?string $currentUrl = ''): string {
    if (empty($_FILES[$field]['name'])) return $currentUrl ?? '';
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    $mime    = mime_content_type($_FILES[$field]['tmp_name']);
    if (!in_array($mime, $allowed)) return $currentUrl;
    $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $name = 'dest_' . uniqid() . '.' . strtolower($ext);
    $dir  = __DIR__ . '/assets/pics/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $name)) {
        return 'assets/pics/' . $name;
    }
    return $currentUrl;
}

function resolveDestinationImageSrc(array $d): string {
    $imageUrl = trim((string)($d['image_url'] ?? ''));
    
    if ($imageUrl === '' && !empty($d['gradient_bg'])) {
        // FIXED REGEX: Inayos ang character class para sa single at double quotes
        if (preg_match('/url\((["\']?)(.*?)\1\)/', (string)$d['gradient_bg'], $m)) {
            $imageUrl = trim($m[2]);
        }
    }
    
    if ($imageUrl === '') return '';
    
    $imageUrl = str_replace('\\', '/', $imageUrl);
    
    if (preg_match('/^(https?:)?\/\//i', $imageUrl) || str_starts_with($imageUrl, 'data:') || str_starts_with($imageUrl, '/')) {
        return $imageUrl;
    }
    
    if (is_file(__DIR__ . '/' . $imageUrl)) return $imageUrl;
    
    if (is_file(dirname(__DIR__) . '/' . $imageUrl)) return '../' . $imageUrl;
    
    return $imageUrl;
}

// ── CRUD ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $image_url = handleImageUpload('image_file', '');
    $stmt = $pdo->prepare("INSERT INTO destinations (name,region,tagline,description,price,price_from,image_url,gradient_bg) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([trim($_POST['name']),trim($_POST['region']),trim($_POST['tagline']),trim($_POST['description']),floatval($_POST['price']),trim($_POST['price_from']),$image_url,trim($_POST['gradient_bg'])]);
    header('Location: manage-destinations.php?msg='.urlencode('Destination added successfully.').'&type=success'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id = trim($_POST['id']);
    $row = $pdo->prepare("SELECT image_url FROM destinations WHERE id=?"); $row->execute([$id]);
    $image_url = handleImageUpload('image_file', $row->fetchColumn());
    $stmt = $pdo->prepare("UPDATE destinations SET name=?,region=?,tagline=?,description=?,price=?,price_from=?,image_url=?,gradient_bg=? WHERE id=?");
    $stmt->execute([trim($_POST['name']),trim($_POST['region']),trim($_POST['tagline']),trim($_POST['description']),floatval($_POST['price']),trim($_POST['price_from']),$image_url,trim($_POST['gradient_bg']),$id]);
    header('Location: manage-destinations.php?msg='.urlencode('Destination updated.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE destinations SET archived=1 WHERE id=?")->execute([trim($_GET['id'])]);
    header('Location: manage-destinations.php?msg='.urlencode('Destination archived.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'unarchive' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE destinations SET archived=0 WHERE id=?")->execute([trim($_GET['id'])]);
    header('Location: manage-destinations.php?show_archived=1&msg='.urlencode('Destination restored.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id=?"); $stmt->execute([trim($_GET['id'])]);
    $d = $stmt->fetch();
    if ($d) $d['image_src'] = resolveDestinationImageSrc($d);
    echo json_encode($d ?: ['error'=>'Not found']); exit;
}

if (isset($_GET['msg'])) { $msg = htmlspecialchars($_GET['msg']); $msgType = ($_GET['type']??'success')==='error'?'error':'success'; }

// ── Fetch data ─────────────────────────────────────────────────────────────
$search  = trim($_GET['search'] ?? '');
$params  = [];
$whereSQL = $show_archived ? 'WHERE archived=1' : 'WHERE archived=0';
if ($search !== '') { $whereSQL .= ' AND (name LIKE ? OR region LIKE ?)'; $like="%$search%"; $params=[$like,$like]; }
$stmt = $pdo->prepare("SELECT * FROM destinations $whereSQL ORDER BY id DESC"); $stmt->execute($params);
$destinations   = $stmt->fetchAll();
$total          = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
$active_count   = $pdo->query("SELECT COUNT(*) FROM destinations WHERE archived=0")->fetchColumn();
$archived_count = $pdo->query("SELECT COUNT(*) FROM destinations WHERE archived=1")->fetchColumn();
$regions_count  = $pdo->query("SELECT COUNT(DISTINCT region) FROM destinations WHERE archived=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destinations – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left"><h1>🗺️ Destinations</h1></div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('M d, Y (D)') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName) ?></div>
    </div>
  </header>

  <div class="adm-body">

    <!-- PAGE HEADER -->
    <div class="adm-page-header-row">
      <div class="adm-page-header" style="margin-bottom:0">
        <h2>Destinations</h2>
        <p>Manage local travel destinations listed on LakbayLokal.</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('create')">＋ Add Destination</button>
    </div>

    <?php if ($msg): ?>
    <div class="adm-alert adm-alert-<?= $msgType ?>" style="margin-top:1.25rem">
      <?= $msgType==='success'?'✅':'❌' ?> <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🗺️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Destinations</div>
          <div class="stat-value"><?= $total ?></div>
          <div class="stat-sub">All-time</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">✅</div>
        <div class="adm-stat-body">
          <div class="stat-label">Active Listings</div>
          <div class="stat-value"><?= $active_count ?></div>
          <div class="stat-sub">Currently live</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">📍</div>
        <div class="adm-stat-body">
          <div class="stat-label">Regions Covered</div>
          <div class="stat-value"><?= $regions_count ?></div>
          <div class="stat-sub">Unique regions</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">🗄️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Archived</div>
          <div class="stat-value"><?= $archived_count ?></div>
          <div class="stat-sub">Hidden from users</div>
        </div>
      </div>
    </div>

    <!-- CARD WITH SEARCH + GRID -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:380px">
              <span class="search-icon">🔍</span>
              <input type="text" name="search" class="adm-search"
                     placeholder="Search destinations or regions…"
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <?php if ($show_archived): ?>
            <input type="hidden" name="show_archived" value="1">
            <?php endif; ?>
            <button type="submit" class="btn btn-outline btn-sm">Search</button>
            <?php if ($search): ?>
            <a href="manage-destinations.php<?= $show_archived?'?show_archived=1':'' ?>" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
        <div style="display:flex;gap:.5rem">
          <?php if ($show_archived): ?>
          <a href="manage-destinations.php" class="btn btn-outline btn-sm">📋 Show Active</a>
          <?php else: ?>
          <a href="manage-destinations.php?show_archived=1" class="btn btn-ghost btn-sm">🗄️ Archived (<?= $archived_count ?>)</a>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($show_archived): ?>
      <div style="padding:.6rem 1.5rem;background:var(--gold-pale);border-bottom:1px solid #FDE68A;font-size:.83rem;color:#92400E;display:flex;align-items:center;gap:.5rem">
        🗄️ Viewing archived destinations — these are hidden from users.
      </div>
      <?php endif; ?>

      <?php if (empty($destinations)): ?>
      <div class="adm-card-body">
        <div class="adm-empty">
          <div class="empty-icon">🗺️</div>
          <h4>No destinations found</h4>
          <p><?= $search ? 'Try a different search term.' : ($show_archived ? 'No archived destinations.' : 'Click "Add Destination" to get started.') ?></p>
        </div>
      </div>
      <?php else: ?>
      <div class="adm-content-grid" style="padding:1.25rem">
        <?php foreach ($destinations as $d):
          $imageSrc = resolveDestinationImageSrc($d);
        ?>
        <div class="adm-item-card">
          <?php if ($imageSrc !== ''): ?>
          <img src="<?= htmlspecialchars($imageSrc) ?>" alt="<?= htmlspecialchars($d['name']) ?>" class="adm-item-card-img">
          <?php else: ?>
          <div class="adm-item-card-img-placeholder" style="background:<?= htmlspecialchars($d['gradient_bg'] ?: 'linear-gradient(135deg,#FFF3E6,#FFE4CC)') ?>">
            <span style="font-size:2.5rem"><?= htmlspecialchars($d['emoji'] ?: '🗺️') ?></span>
          </div>
          <?php endif; ?>

          <div class="adm-item-card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;margin-bottom:.3rem">
              <h4 style="font-size:.95rem;font-weight:700;color:var(--deep)"><?= htmlspecialchars($d['name']) ?></h4>
              <?php if ($show_archived): ?>
              <span class="badge badge-cancelled" style="flex-shrink:0">Archived</span>
              <?php endif; ?>
            </div>
            <p style="font-size:.73rem;color:var(--primary);font-weight:600;margin-bottom:.25rem;display:flex;align-items:center;gap:.25rem">
              📍 <?= htmlspecialchars($d['region']) ?>
            </p>
            <p style="font-size:.8rem;color:var(--muted);line-height:1.5;margin-bottom:.4rem">
              <?= htmlspecialchars(mb_strimwidth($d['tagline'] ?: $d['description'], 0, 80, '…')) ?>
            </p>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.25rem">
              <strong style="font-size:.92rem;color:var(--deep)">₱<?= number_format($d['price'], 2) ?></strong>
              <?php if ($d['price_from']): ?>
              <span style="font-size:.72rem;color:var(--muted)"><?= htmlspecialchars($d['price_from']) ?></span>
              <?php endif; ?>
            </div>
          </div>

          <div class="adm-item-card-footer">
            <button class="btn btn-outline btn-sm" onclick="editDestination('<?= htmlspecialchars($d['id']) ?>')">✏️ Edit</button>
            <?php if ($show_archived): ?>
            <a href="manage-destinations.php?action=unarchive&id=<?= $d['id'] ?>"
               class="btn btn-primary btn-sm"
               onclick="return confirm('Restore <?= addslashes($d['name']) ?>?')">↩️ Restore</a>
            <?php else: ?>
            <a href="manage-destinations.php?action=delete&id=<?= $d['id'] ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Archive «<?= addslashes($d['name']) ?>»? It will be hidden from users.')">🗄️ Archive</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div style="padding:.75rem 1.5rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
        <span>Showing <strong><?= count($destinations) ?></strong> of <strong><?= $total ?></strong> destinations</span>
        <?php if ($show_archived): ?>
        <a href="manage-destinations.php" class="btn btn-ghost btn-sm" style="font-size:.78rem">← Back to Active</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div><!-- /adm-card -->

  </div><!-- /adm-body -->
</div><!-- /adm-main -->

<!-- ══════════════════════════════════ MODAL ══════════════════════════════════ -->
<div class="adm-modal-bg" id="dest-modal">
  <div class="adm-modal" style="max-width:680px;width:95%">
    <div class="adm-modal-header">
      <h3 id="modal-title">Add Destination</h3>
      <button class="panel-close" onclick="closeModal()">✕</button>
    </div>

    <div class="adm-modal-body" style="max-height:72vh;overflow-y:auto">
      <form id="dest-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="form-action" value="create">
        <input type="hidden" name="id"     id="form-id"     value="">

        <div class="adm-form-grid">
          <!-- Row 1 -->
          <div class="form-group">
            <label for="f-name">Destination Name <span style="color:var(--primary)">*</span></label>
            <input type="text" id="f-name" name="name" required placeholder="e.g. Boracay Island">
          </div>
          <div class="form-group">
            <label for="f-region">Region <span style="color:var(--primary)">*</span></label>
            <input type="text" id="f-region" name="region" required placeholder="e.g. Western Visayas">
          </div>

          <!-- Row 2 -->
          <div class="form-group">
            <label for="f-price">Base Price (₱) <span style="color:var(--primary)">*</span></label>
            <input type="number" id="f-price" name="price" required min="0" step="0.01" placeholder="5500.00">
          </div>
          <div class="form-group">
            <label for="f-price-from">Price Label</label>
            <input type="text" id="f-price-from" name="price_from" placeholder="e.g. per person, per night">
          </div>

          <!-- Row 3 -->
          <div class="form-group">
            <label for="f-gradient">Gradient BG (CSS)</label>
            <input type="text" id="f-gradient" name="gradient_bg" placeholder="linear-gradient(135deg,#FFF3E6,#FFE4CC)">
            <span class="form-hint">Used as card background when no image is set.</span>
          </div>
          <div class="form-group">
            <label for="f-emoji">Emoji Icon</label>
            <input type="text" id="f-emoji" name="emoji" placeholder="e.g. 🏖️">
            <span class="form-hint">Shown on card when no image is set.</span>
          </div>

          <!-- Row 4 -->
          <div class="form-group form-span-2">
            <label for="f-tagline">Tagline</label>
            <input type="text" id="f-tagline" name="tagline" placeholder="Short catchy line shown on the destination card">
          </div>

          <!-- Row 5 -->
          <div class="form-group form-span-2">
            <label for="f-desc">Full Description</label>
            <textarea id="f-desc" name="description" rows="3" placeholder="Detailed description of the destination…"></textarea>
          </div>

          <!-- Row 6 -->
          <div class="form-group form-span-2">
            <label for="f-image">Destination Image</label>
            <input type="file" id="f-image" name="image_file" accept="image/*">
            <span class="form-hint">JPG, PNG, or WEBP. Saved to assets/pics/. Leave blank to keep current image when editing.</span>
            <div id="img-preview" style="margin-top:.6rem"></div>
          </div>
        </div>
      </form>
    </div>

    <div class="adm-modal-footer">
      <button class="btn btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="submitDestinationForm()" id="modal-submit-btn">Save Destination</button>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById('dest-modal');

function openModal(mode) {
  document.getElementById('dest-form').reset();
  document.getElementById('img-preview').innerHTML = '';
  document.getElementById('modal-title').textContent      = 'Add Destination';
  document.getElementById('modal-submit-btn').textContent = 'Save Destination';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value     = '';
  modal.classList.add('open');
}

function closeModal() { modal.classList.remove('open'); }
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

function submitDestinationForm() {
  const name   = document.getElementById('f-name').value.trim();
  const region = document.getElementById('f-region').value.trim();
  const price  = document.getElementById('f-price').value.trim();
  if (!name || !region || !price) { alert('Please fill in all required fields: Name, Region, and Price.'); return; }
  if (isNaN(parseFloat(price)) || parseFloat(price) < 0) { alert('Price must be a valid positive number.'); return; }
  const btn = document.getElementById('modal-submit-btn');
  btn.disabled = true; btn.textContent = '⏳ Saving…';
  document.getElementById('dest-form').submit();
}

function editDestination(id) {
  document.getElementById('dest-form').reset();
  document.getElementById('img-preview').innerHTML = '';
  document.getElementById('modal-title').textContent      = 'Edit Destination';
  document.getElementById('modal-submit-btn').textContent = 'Update Destination';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value     = id;

  fetch(`manage-destinations.php?action=get&id=${encodeURIComponent(id)}`)
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(d => {
      if (d.error) { alert('Could not load destination: ' + d.error); return; }
      document.getElementById('f-name').value       = d.name        || '';
      document.getElementById('f-region').value     = d.region      || '';
      document.getElementById('f-price').value      = d.price       || '';
      document.getElementById('f-price-from').value = d.price_from  || '';
      document.getElementById('f-gradient').value   = d.gradient_bg || '';
      document.getElementById('f-emoji').value      = d.emoji       || '';
      document.getElementById('f-tagline').value    = d.tagline     || '';
      document.getElementById('f-desc').value       = d.description || '';
      if (d.image_src || d.image_url) {
        document.getElementById('img-preview').innerHTML =
          `<img src="${d.image_src || d.image_url}" style="max-height:120px;border-radius:8px;border:1px solid var(--border)" alt="Current image">
           <p class="form-hint" style="margin-top:.3rem">Current image — upload a new file to replace it.</p>`;
      }
      modal.classList.add('open');
    })
    .catch(e => { console.error(e); alert('Error loading destination details.'); });
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