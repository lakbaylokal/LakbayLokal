<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage    = 'activities';
$msg           = '';
$msgType       = 'success';
$show_archived = isset($_GET['show_archived']) && $_GET['show_archived'] === '1';

$schemaCheck = $pdo->query("SHOW COLUMNS FROM activities LIKE 'archived'")->fetch();
if (!$schemaCheck) {
    $pdo->exec("ALTER TABLE activities ADD COLUMN archived TINYINT(1) NOT NULL DEFAULT 0");
}

// ── CRUD ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $destination_id = trim($_POST['destination_id'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    if (!$destination_id || !$name || $price <= 0) {
        header('Location: manage-activities.php?msg='.urlencode('Please select a destination and enter a valid activity name and price.').'&type=error'); exit;
    }
    $stmt = $pdo->prepare("INSERT INTO activities (destination_id,name,price) VALUES (?,?,?)");
    $stmt->execute([$destination_id, $name, $price]);
    header('Location: manage-activities.php?msg='.urlencode('Activity added successfully.').'&type=success'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $destination_id = trim($_POST['destination_id'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $id    = (int)($_POST['id'] ?? 0);
    if (!$id || !$destination_id || !$name || $price <= 0) {
        header('Location: manage-activities.php?msg='.urlencode('Please fill in all required fields.').'&type=error'); exit;
    }
    $pdo->prepare("UPDATE activities SET destination_id=?,name=?,price=? WHERE id=?")->execute([$destination_id,$name,$price,$id]);
    header('Location: manage-activities.php?msg='.urlencode('Activity updated.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE activities SET archived=1 WHERE id=?")->execute([(int)$_GET['id']]);
    header('Location: manage-activities.php?msg='.urlencode('Activity archived.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'unarchive' && isset($_GET['id'])) {
    $pdo->prepare("UPDATE activities SET archived=0 WHERE id=?")->execute([(int)$_GET['id']]);
    header('Location: manage-activities.php?show_archived=1&msg='.urlencode('Activity restored.').'&type=success'); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT * FROM activities WHERE id=?"); $stmt->execute([(int)$_GET['id']]);
    echo json_encode($stmt->fetch() ?: ['error'=>'Not found']); exit;
}

if (isset($_GET['msg'])) { $msg = htmlspecialchars($_GET['msg']); $msgType = ($_GET['type']??'success')==='error'?'error':'success'; }

// ── Fetch ──────────────────────────────────────────────────────────────────
$destinations_all = $pdo->query("SELECT id,name FROM destinations ORDER BY name")->fetchAll();
$search      = trim($_GET['search'] ?? '');
$dest_filter = trim($_GET['destination_id'] ?? '');
$where  = []; $params = [];
$where[] = $show_archived ? "a.archived=1" : "a.archived=0";
if ($search !== '') { $where[] = "a.name LIKE ?"; $params[] = "%$search%"; }
if ($dest_filter !== '') { $where[] = "a.destination_id=?"; $params[] = $dest_filter; }
$whereSQL = 'WHERE '.implode(' AND ',$where);
$stmt = $pdo->prepare("SELECT a.*, d.name AS destination_name FROM activities a LEFT JOIN destinations d ON a.destination_id=d.id $whereSQL ORDER BY a.destination_id, a.name");
$stmt->execute($params);
$activities     = $stmt->fetchAll();
$total          = $pdo->query("SELECT COUNT(*) FROM activities")->fetchColumn();
$active_count   = $pdo->query("SELECT COUNT(*) FROM activities WHERE archived=0")->fetchColumn();
$archived_count = $pdo->query("SELECT COUNT(*) FROM activities WHERE archived=1")->fetchColumn();
$dest_count     = $pdo->query("SELECT COUNT(DISTINCT destination_id) FROM activities WHERE archived=0")->fetchColumn();
$avg_price      = $pdo->query("SELECT AVG(price) FROM activities WHERE archived=0")->fetchColumn();

// Group by destination (only when not filtered)
$grouped = [];
foreach ($activities as $a) {
    $grouped[$a['destination_id']]['label']   = $a['destination_name'] ?? 'Unknown';
    $grouped[$a['destination_id']]['items'][] = $a;
}
$isFiltered = ($search !== '' || $dest_filter !== '');
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
      <?= $msgType==='success'?'✅':'❌' ?> <?= $msg ?>
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
        <div class="adm-stat-icon stat-icon-accent">✅</div>
        <div class="adm-stat-body">
          <div class="stat-label">Active Activities</div>
          <div class="stat-value"><?= $active_count ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">💰</div>
        <div class="adm-stat-body">
          <div class="stat-label">Avg. Price / Person</div>
          <div class="stat-value">₱<?= $avg_price ? number_format($avg_price,0) : '—' ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">🗄️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Archived Activities</div>
          <div class="stat-value"><?= $archived_count ?></div>
        </div>
      </div>
    </div>

    <!-- FILTERS -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:300px">
              <span class="search-icon">🔍</span>
              <input type="text" name="search" class="adm-search"
                     placeholder="Search activities…"
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="destination_id" class="adm-select">
              <option value="">All Destinations</option>
              <?php foreach ($destinations_all as $dd): ?>
              <option value="<?= $dd['id'] ?>" <?= $dest_filter===$dd['id']?'selected':'' ?>><?= htmlspecialchars($dd['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if ($show_archived): ?><input type="hidden" name="show_archived" value="1"><?php endif; ?>
            <button type="submit" class="btn btn-outline btn-sm">Search</button>
            <?php if ($search || $dest_filter): ?>
            <a href="manage-activities.php<?= $show_archived?'?show_archived=1':'' ?>" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
        <div style="display:flex;gap:.5rem">
          <?php if ($show_archived): ?>
          <a href="manage-activities.php" class="btn btn-outline btn-sm">📋 Show Active</a>
          <?php else: ?>
          <a href="manage-activities.php?show_archived=1" class="btn btn-ghost btn-sm">🗄️ Archived (<?= $archived_count ?>)</a>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($show_archived): ?>
      <div style="padding:.6rem 1.5rem;background:var(--gold-pale);border-bottom:1px solid #FDE68A;font-size:.83rem;color:#92400E">
        🗄️ Viewing archived activities — these are hidden from users.
      </div>
      <?php endif; ?>

      <?php if (empty($activities)): ?>
      <div class="adm-card-body">
        <div class="adm-empty">
          <div class="empty-icon">🎯</div>
          <h4>No activities found</h4>
          <p><?= ($search||$dest_filter) ? 'Try adjusting your filters.' : 'Click "Add Activity" to get started.' ?></p>
        </div>
      </div>

      <?php elseif ($isFiltered): ?>
      <!-- FLAT TABLE when searching/filtering -->
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th style="width:50px">#</th>
              <th>Activity Name</th>
              <th>Destination</th>
              <th>Price / Person</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($activities as $a): ?>
          <tr>
            <td style="color:var(--muted);font-size:.82rem"><?= $a['id'] ?></td>
            <td><span class="cell-main"><?= htmlspecialchars($a['name']) ?></span></td>
            <td>
              <span style="background:var(--primary-pale);color:var(--primary);padding:.25rem .65rem;border-radius:20px;font-size:.75rem;font-weight:600">
                📍 <?= htmlspecialchars($a['destination_name'] ?? '—') ?>
              </span>
            </td>
            <td><strong style="color:var(--deep)">₱<?= number_format($a['price'],2) ?></strong></td>
            <td>
              <div style="display:flex;gap:.4rem">
                <button class="btn btn-outline btn-sm" onclick="editActivity(<?= $a['id'] ?>)">✏️ Edit</button>
                <?php if ($show_archived): ?>
                <a href="manage-activities.php?action=unarchive&id=<?= $a['id'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('Restore this activity?')">↩️ Restore</a>
                <?php else: ?>
                <a href="manage-activities.php?action=delete&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Archive this activity?')">🗄️ Archive</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php else: ?>
      <!-- GROUPED by destination (default view) -->
      <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1.75rem">
        <?php foreach ($grouped as $destId => $group): ?>
        <div>
          <!-- Destination group header -->
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;padding-bottom:.6rem;border-bottom:2px solid var(--primary-pale)">
            <span style="font-size:1.1rem">🗺️</span>
            <h4 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:var(--deep)"><?= htmlspecialchars($group['label']) ?></h4>
            <span style="background:var(--primary-pale);color:var(--primary);border-radius:20px;padding:.15rem .65rem;font-size:.72rem;font-weight:600;margin-left:.25rem">
              <?= count($group['items']) ?> activit<?= count($group['items'])!==1?'ies':'y' ?>
            </span>
          </div>

          <div class="adm-table-wrap">
            <table class="adm-table">
              <thead>
                <tr>
                  <th style="width:50px">#</th>
                  <th>Activity Name</th>
                  <th>Price per Person</th>
                  <th style="width:160px">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($group['items'] as $a): ?>
              <tr>
                <td style="color:var(--muted);font-size:.82rem"><?= $a['id'] ?></td>
                <td><span class="cell-main"><?= htmlspecialchars($a['name']) ?></span></td>
                <td><strong style="color:var(--deep)">₱<?= number_format($a['price'],2) ?></strong></td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <button class="btn btn-outline btn-sm" onclick="editActivity(<?= $a['id'] ?>)">✏️ Edit</button>
                    <?php if ($show_archived): ?>
                    <a href="manage-activities.php?action=unarchive&id=<?= $a['id'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('Restore this activity?')">↩️ Restore</a>
                    <?php else: ?>
                    <a href="manage-activities.php?action=delete&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Archive this activity?')">🗄️ Archive</a>
                    <?php endif; ?>
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
      <div style="padding:.75rem 1.5rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border)">
        Showing <strong><?= count($activities) ?></strong> of <strong><?= $total ?></strong> activities
      </div>
      <?php endif; ?>
    </div><!-- /adm-card -->

  </div>
</div>

<!-- ══════════════════════════════════ MODAL ══════════════════════════════════ -->
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
            <label for="f-dest">Destination <span style="color:var(--primary)">*</span></label>
            <select id="f-dest" name="destination_id" required>
              <option value="">— Select Destination —</option>
              <?php foreach ($destinations_all as $dd): ?>
              <option value="<?= $dd['id'] ?>"><?= htmlspecialchars($dd['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="f-name">Activity Name <span style="color:var(--primary)">*</span></label>
            <input type="text" id="f-name" name="name" required placeholder="e.g. Island Hopping Tour, ATV Ride, Snorkeling">
          </div>

          <div class="form-group">
            <label for="f-price">Price per Person (₱) <span style="color:var(--primary)">*</span></label>
            <input type="number" id="f-price" name="price" required min="1" step="0.01" placeholder="800.00">
            <span class="form-hint">This is an add-on cost displayed during booking checkout.</span>
          </div>
        </div>
      </form>
    </div>

    <div class="adm-modal-footer">
      <button class="btn btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="submitActivityForm()" id="modal-submit-btn">Save Activity</button>
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

function submitActivityForm() {
  const dest  = document.getElementById('f-dest').value.trim();
  const name  = document.getElementById('f-name').value.trim();
  const price = document.getElementById('f-price').value.trim();
  if (!dest || !name || !price) { alert('Please fill in all required fields.'); return; }
  if (isNaN(parseFloat(price)) || parseFloat(price) <= 0) { alert('Price must be a positive number.'); return; }
  const btn = document.getElementById('modal-submit-btn');
  btn.disabled = true; btn.textContent = '⏳ Saving…';
  document.getElementById('act-form').submit();
}

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
    })
    .catch(e => { console.error(e); alert('Error loading activity.'); });
}
</script>
</body>
</html>