<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage = 'activities';
$msg        = '';
$msgType    = 'success';

// ── CREATE ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $destination_id = trim($_POST['destination_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($destination_id === '' || $name === '' || $price <= 0) {
        header('Location: manage-activities.php?msg=' . urlencode('Please select a destination and enter a valid activity name and price.') . '&type=error');
        exit;
    }

    $destCheck = $pdo->prepare("SELECT COUNT(*) FROM destinations WHERE id = ?");
    $destCheck->execute([$destination_id]);
    if ($destCheck->fetchColumn() == 0) {
        header('Location: manage-activities.php?msg=' . urlencode('Selected destination does not exist.') . '&type=error');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO activities (destination_id, name, price) VALUES (?, ?, ?)");
    $stmt->execute([
        $destination_id,
        $name,
        $price,
    ]);
    header('Location: manage-activities.php?msg=' . urlencode('Activity added successfully.') . '&type=success');
    exit;
}

// ── UPDATE ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $destination_id = trim($_POST['destination_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0 || $destination_id === '' || $name === '' || $price <= 0) {
        header('Location: manage-activities.php?msg=' . urlencode('Please select a destination and enter a valid activity name and price.') . '&type=error');
        exit;
    }

    $destCheck = $pdo->prepare("SELECT COUNT(*) FROM destinations WHERE id = ?");
    $destCheck->execute([$destination_id]);
    if ($destCheck->fetchColumn() == 0) {
        header('Location: manage-activities.php?msg=' . urlencode('Selected destination does not exist.') . '&type=error');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE activities SET destination_id=?, name=?, price=? WHERE id=?");
    $stmt->execute([
        $destination_id,
        $name,
        $price,
        $id,
    ]);
    header('Location: manage-activities.php?msg=' . urlencode('Activity updated successfully.') . '&type=success');
    exit;
}

// ── DELETE ────────────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM activities WHERE id = ?")->execute([(int)$_GET['id']]);
    header('Location: manage-activities.php?msg=' . urlencode('Activity deleted.') . '&type=success');
    exit;
}

// ── GET (JSON) ────────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM activities WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    echo json_encode($stmt->fetch() ?: ['error' => 'Not found']);
    exit;
}

if (isset($_GET['msg'])) {
    $msg     = htmlspecialchars($_GET['msg']);
    $msgType = ($_GET['type'] ?? 'success') === 'error' ? 'error' : 'success';
}

// ── DESTINATIONS (for dropdown) ───────────────────────────────────────────
$destinations_all = $pdo->query("SELECT id, name FROM destinations ORDER BY name")->fetchAll();
$dest_map = array_column($destinations_all, 'name', 'id');

// ── FETCH ACTIVITIES ──────────────────────────────────────────────────────
$search      = trim($_GET['search'] ?? '');
$dest_filter = trim($_GET['destination_id'] ?? '');
$where  = [];
$params = [];
if ($search !== '') {
    $where[]  = "a.name LIKE ?";
    $params[] = "%$search%";
}
if ($dest_filter !== '') {
    $where[]  = "a.destination_id = ?";
    $params[] = $dest_filter;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("
    SELECT a.*, d.name AS destination_name
    FROM activities a
    LEFT JOIN destinations d ON a.destination_id = d.id
    $whereSQL
    ORDER BY a.destination_id, a.name
");
$stmt->execute($params);
$activities = $stmt->fetchAll();

$total         = $pdo->query("SELECT COUNT(*) FROM activities")->fetchColumn();
$avg_price     = $pdo->query("SELECT AVG(price) FROM activities")->fetchColumn();
$dest_with_act = $pdo->query("SELECT COUNT(DISTINCT destination_id) FROM activities")->fetchColumn();

// ── GROUP by destination for display ─────────────────────────────────────
$grouped = [];
foreach ($activities as $a) {
    $grouped[$a['destination_id']]['label']  = $a['destination_name'] ?? 'Unknown';
    $grouped[$a['destination_id']]['items'][] = $a;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activities – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left"><h1>🎯 Activities</h1></div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('M d, Y (D)') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName) ?></div>
    </div>
  </header>

  <div class="adm-body">
    <div class="adm-page-header-row">
      <div class="adm-page-header" style="margin-bottom:0">
        <h2>Activities</h2>
        <p>Manage add-on activities linked to each destination.</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('create')">＋ Add Activity</button>
    </div>

    <?php if ($msg): ?>
    <div class="adm-alert adm-alert-<?= $msgType ?>" style="margin-top:1.25rem">
      <?= $msgType === 'success' ? '✅' : '❌' ?> <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🎯</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Activities</div>
          <div class="stat-value"><?= $total ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">🗺️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Destinations w/ Activities</div>
          <div class="stat-value"><?= $dest_with_act ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">💰</div>
        <div class="adm-stat-body">
          <div class="stat-label">Avg. Activity Price</div>
          <div class="stat-value" style="font-size:1.4rem">₱<?= number_format((float)$avg_price, 0) ?></div>
        </div>
      </div>
    </div>

    <!-- FILTERS + TABLE -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:320px">
              <span class="search-icon">🔍</span>
              <input type="text" name="search" class="adm-search"
                     placeholder="Search activities…"
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
            <a href="manage-activities.php" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <?php if (empty($activities)): ?>
      <div class="adm-card-body">
        <div class="adm-empty">
          <div class="empty-icon">🎯</div>
          <h4>No activities found</h4>
          <p><?= $search || $dest_filter ? 'Try adjusting your filters.' : 'Click "Add Activity" to get started.' ?></p>
        </div>
      </div>

      <?php elseif ($search || $dest_filter): ?>
      <!-- FLAT TABLE when filtered -->
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Activity Name</th>
              <th>Destination</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($activities as $a): ?>
          <tr>
            <td style="color:var(--muted);font-size:.82rem"><?= $a['id'] ?></td>
            <td><span class="cell-main"><?= htmlspecialchars($a['name']) ?></span></td>
            <td>
              <span style="background:var(--primary-pale);color:var(--primary);padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:600">
                <?= htmlspecialchars($a['destination_name'] ?? '—') ?>
              </span>
            </td>
            <td><strong>₱<?= number_format($a['price'], 2) ?></strong></td>
            <td>
              <div style="display:flex;gap:.5rem">
                <button class="btn btn-outline btn-sm" onclick="editActivity(<?= $a['id'] ?>)">✏️ Edit</button>
                <a href="manage-activities.php?action=delete&id=<?= $a['id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this activity?')">🗑 Delete</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php else: ?>
      <!-- GROUPED by destination -->
      <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1.5rem">
        <?php foreach ($grouped as $destId => $group): ?>
        <div>
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;padding-bottom:.5rem;border-bottom:2px solid var(--primary-pale)">
            <span style="font-size:1rem">🗺️</span>
            <h4 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:var(--deep)"><?= htmlspecialchars($group['label']) ?></h4>
            <span style="background:var(--primary-pale);color:var(--primary);border-radius:20px;padding:.15rem .6rem;font-size:.72rem;font-weight:600">
              <?= count($group['items']) ?> activit<?= count($group['items'])!==1?'ies':'y' ?>
            </span>
          </div>
          <div class="adm-table-wrap">
            <table class="adm-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Activity Name</th>
                  <th>Price per Person</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($group['items'] as $a): ?>
              <tr>
                <td style="color:var(--muted);font-size:.82rem"><?= $a['id'] ?></td>
                <td><span class="cell-main"><?= htmlspecialchars($a['name']) ?></span></td>
                <td><strong>₱<?= number_format($a['price'], 2) ?></strong></td>
                <td>
                  <div style="display:flex;gap:.5rem">
                    <button class="btn btn-outline btn-sm" onclick="editActivity(<?= $a['id'] ?>)">✏️ Edit</button>
                    <a href="manage-activities.php?action=delete&id=<?= $a['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete \'<?= addslashes($a['name']) ?>\'?')">🗑 Delete</a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($activities)): ?>
      <div style="padding:.8rem 1.2rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border)">
        Showing <?= count($activities) ?> of <?= $total ?> activities
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── MODAL ── -->
<div class="adm-modal-bg" id="act-modal">
  <div class="adm-modal" style="max-width:500px;width:95%">
    <div class="adm-modal-header">
      <h3 id="modal-title">Add Activity</h3>
      <button class="panel-close" onclick="closeModal()">✕</button>
    </div>
    <div class="adm-modal-body">
      <form id="act-form" method="POST">
        <input type="hidden" name="action" id="form-action" value="create">
        <input type="hidden" name="id"     id="form-id"     value="">

        <div class="adm-form-grid cols-1" style="gap:1rem">
          <div class="form-group">
            <label for="f-dest">Destination *</label>
            <select id="f-dest" name="destination_id" required>
              <option value="">— Select Destination —</option>
              <?php foreach ($destinations_all as $dd): ?>
              <option value="<?= $dd['id'] ?>"><?= htmlspecialchars($dd['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="f-name">Activity Name *</label>
            <input type="text" id="f-name" name="name" required
                   placeholder="e.g. Island Hopping Tour, ATV Ride, Snorkeling">
          </div>
          <div class="form-group">
            <label for="f-price">Price per Person (₱) *</label>
            <input type="number" id="f-price" name="price" required min="0" step="0.01" placeholder="800.00">
            <span class="form-hint">This is an add-on cost shown during booking checkout.</span>
          </div>
        </div>
      </form>
    </div>
    <div class="adm-modal-footer">
      <button class="btn btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="document.getElementById('act-form').submit()" id="modal-submit-btn">Save Activity</button>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById('act-modal');

function openModal(mode) {
  document.getElementById('act-form').reset();
  document.getElementById('modal-title').textContent      = 'Add Activity';
  document.getElementById('modal-submit-btn').textContent = 'Save Activity';
  document.getElementById('form-action').value = 'create';
  document.getElementById('form-id').value     = '';
  modal.classList.add('open');
}

function closeModal() { modal.classList.remove('open'); }
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

function editActivity(id) {
  document.getElementById('modal-title').textContent      = 'Edit Activity';
  document.getElementById('modal-submit-btn').textContent = 'Update Activity';
  document.getElementById('form-action').value = 'update';
  document.getElementById('form-id').value     = id;

  fetch(`manage-activities.php?action=get&id=${id}`)
    .then(r => r.json())
    .then(a => {
      if (a.error) { alert('Could not load activity.'); return; }
      document.getElementById('f-dest').value  = a.destination_id || '';
      document.getElementById('f-name').value  = a.name           || '';
      document.getElementById('f-price').value = a.price          || '';
      modal.classList.add('open');
    });
}
</script>
</body>
</html>