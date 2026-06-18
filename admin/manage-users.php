<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage = 'users';
$msg        = '';
$msgType    = 'success';

// ── Toggle role ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_role') {
    $uid  = (int)$_POST['user_id'];
    $role = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
    $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role, $uid]);
    header('Location: manage-users.php?msg='.urlencode('Role updated successfully.').'&type=success'); exit;
}

// ── Delete user ───────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    if ($uid === ($_SESSION['user']['id'] ?? 0)) {
        header('Location: manage-users.php?msg='.urlencode('You cannot delete your own account.').'&type=error'); exit;
    }
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$uid]);
    header('Location: manage-users.php?msg='.urlencode('User deleted.').'&type=success'); exit;
}

if (isset($_GET['msg'])) { $msg = htmlspecialchars($_GET['msg']); $msgType = ($_GET['type']??'success')==='error'?'error':'success'; }

// ── Fetch users ───────────────────────────────────────────────────────────
$search      = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role'] ?? '');
$where = []; $params = [];
if ($search !== '') { $where[] = "(CONCAT(u.FName,' ',u.LName) LIKE ? OR u.Email LIKE ?)"; $like="%$search%"; $params=array_merge($params,[$like,$like]); }
if ($role_filter !== '') { $where[] = "u.role=?"; $params[] = $role_filter; }
$whereSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';
$stmt = $pdo->prepare("SELECT u.id,u.FName,u.Mname,u.LName,u.Email,u.role,COUNT(b.id) AS booking_count FROM users u LEFT JOIN bookings b ON b.user_id=u.id $whereSQL GROUP BY u.id ORDER BY u.id DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();

$total_users  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_admins = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

/* ── RECENT TRAVEL HISTORY LOADER (FOR SIDE PANEL) ─────────────────────── */
$userIds = array_column($users, 'id');
$historyByUser = [];

if (!empty($userIds)) {
    $placeholders = implode(',', array_fill(0, count($userIds), '?'));
    
    $histStmt = $pdo->prepare("
        SELECT b.id AS booking_id, b.reference_code, b.user_id, b.checkin_date, b.status, b.total_price,
               d.name AS destination_name, h.name AS hotel_name
        FROM bookings b
        JOIN destinations d ON b.destination_id = d.id
        JOIN hotels h ON b.hotel_id = h.id
        WHERE b.user_id IN ($placeholders)
        ORDER BY b.id DESC
    ");
    $histStmt->execute($userIds);
    $rows = $histStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $uid = $row['user_id'];
        if (!isset($historyByUser[$uid])) {
            $historyByUser[$uid] = [];
        }
        if (count($historyByUser[$uid]) < 5) {
            $historyByUser[$uid][] = $row;
        }
    }
}

function buildHistoryHtml($userId, $historyByUser) {
    $history = $historyByUser[$userId] ?? [];
    if (empty($history)) {
        return '<div class="cell-sub" style="text-align:center; padding:1.5rem 0; color:var(--muted)">No travel records found for this user.</div>';
    }
    
    $html = '';
    foreach ($history as $h) {
        $statusStr = strtolower($h['status']);
        $displayStatus = match($statusStr) {
            'confirmed', 'completed', 'approved' => '🟢 ' . ucfirst($h['status']),
            'cancelled', 'rejected'              => '🔴 ' . ucfirst($h['status']),
            default                              => '🟡 ' . ucfirst($h['status'])
        };

        $html .= '<div style="display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-bottom:1px solid var(--border);">'
            . '<div style="text-align:left;">'
            . '  <div class="cell-main" style="font-size:0.88rem; font-weight:600;">' . htmlspecialchars($h['reference_code']) . '</div>'
            . '  <div class="cell-sub" style="font-size:0.78rem;">' . htmlspecialchars($h['hotel_name']) . ' (' . htmlspecialchars($h['destination_name']) . ')</div>'
            . '  <div class="cell-sub" style="font-size:0.75rem; color:var(--muted)">Check-in: ' . date('M d, Y', strtotime($h['checkin_date'])) . '</div>'
            . '</div>'
            . '<div style="text-align:right;">'
            . '  <span style="font-size:0.75rem; font-weight:600;">' . $displayStatus . '</span>'
            . '  <div class="cell-main" style="color:var(--primary); font-weight:600; font-size:0.85rem; margin-top:2px;">₱' . number_format($h['total_price'], 2) . '</div>'
            . '</div>'
            . '</div>';
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .adm-table tbody tr {
      cursor: pointer;
      transition: background 0.15s ease;
    }
    .adm-table tbody tr:hover {
      background-color: var(--primary-pale) !important;
    }
    .info-section { margin-bottom: 1.5rem; }
    .info-section-title { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.75rem; font-weight: 700; border-bottom: 1.5px solid var(--border); padding-bottom: 4px; }
    .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid var(--cream); font-size: 0.9rem; }
    .info-label { color: var(--muted); }
    .info-value { font-weight: 500; color: var(--deep); text-align: right; }
    .info-value.mono { font-family: monospace; font-weight: 600; font-size: 0.95rem; }
  </style>
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left"><h1>👥 Users</h1></div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('M d, Y (D)') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName ?? 'Admin') ?></div>
    </div>
  </header>

  <div class="adm-body">

    <div class="adm-page-header-row">
      <div class="adm-page-header" style="margin-bottom:0">
        <h2>Users</h2>
        <p>Manage all registered LakbayLokal members and their roles.</p>
      </div>
    </div>

    <?php if ($msg): ?>
    <div class="adm-alert adm-alert-<?= $msgType ?>" style="margin-top:1.25rem">
      <?= $msgType==='success'?'✅':'❌' ?> <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">👥</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Users</div>
          <div class="stat-value"><?= number_format($total_users) ?></div>
          <div class="stat-sub">Registered members</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">🛡️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Admins</div>
          <div class="stat-value"><?= $total_admins ?></div>
          <div class="stat-sub">With admin access</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">🙋</div>
        <div class="adm-stat-body">
          <div class="stat-label">Regular Users</div>
          <div class="stat-value"><?= $total_users - $total_admins ?></div>
          <div class="stat-sub">Standard members</div>
        </div>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:380px">
              <span class="search-icon">🔍</span>
              <input type="text" name="search" class="adm-search"
                     placeholder="Search by name or email…"
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="role" class="adm-select" onchange="this.form.submit()">
              <option value="">All Roles</option>
              <option value="user"  <?= $role_filter==='user' ?'selected':'' ?>>Users</option>
              <option value="admin" <?= $role_filter==='admin'?'selected':'' ?>>Admins</option>
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Apply</button>
            <?php if ($search || $role_filter): ?>
            <a href="manage-users.php" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th style="width:50px">#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th style="width:90px;text-align:center">Bookings</th>
              <th style="width:200px">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="6">
              <div class="adm-empty">
                <div class="empty-icon">👥</div>
                <h4>No users found</h4>
                <p>Try adjusting your search or filters.</p>
              </div>
            </td></tr>
          <?php else: ?>
            <?php foreach ($users as $u):
              $fullname = trim(htmlspecialchars($u['FName']).' '.htmlspecialchars($u['Mname']??'').' '.htmlspecialchars($u['LName']));
              $initials = strtoupper(substr($u['FName'],0,1).substr($u['LName'],0,1));
              $isSelf   = ($u['id'] === ($_SESSION['user']['id'] ?? 0));
              
              $userCode = 'USR-' . str_pad($u['id'], 4, '0', STR_PAD_LEFT);
              $roleText = ($u['role'] === 'admin') ? 'Admin Security' : 'Standard Member';
              $historyData = buildHistoryHtml($u['id'], $historyByUser);
            ?>
            <!-- Idinagdag ang data-fname at data-lname rito -->
            <tr data-id="<?= $userCode ?>"
                data-name="<?= $fullname ?>"
                data-fname="<?= htmlspecialchars($u['FName']) ?>"
                data-lname="<?= htmlspecialchars($u['LName']) ?>"
                data-email="<?= htmlspecialchars($u['Email']) ?>"
                data-role="<?= $roleText ?>"
                data-bookings="<?= $u['booking_count'] ?>"
                data-history="<?= htmlspecialchars($historyData, ENT_QUOTES, 'UTF-8') ?>">
              <td style="color:var(--muted);font-size:.82rem"><?= $u['id'] ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                  <div style="width:36px;height:36px;border-radius:50%;background:<?= $u['role']==='admin'?'var(--primary-pale)':'var(--accent-pale)' ?>;color:<?= $u['role']==='admin'?'var(--primary)':'var(--accent)' ?>;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.78rem;flex-shrink:0;border:1.5px solid <?= $u['role']==='admin'?'var(--primary-light)':'var(--accent-light)' ?>"><?= $initials ?></div>
                  <div>
                    <div class="cell-main"><?= $fullname ?></div>
                    <?php if ($isSelf): ?><div class="cell-sub" style="color:var(--primary)">← You</div><?php endif; ?>
                  </div>
                </div>
              </td>
              <td style="font-size:.85rem"><?= htmlspecialchars($u['Email']) ?></td>
              <td>
                <span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-user' ?>">
                  <?= $u['role']==='admin'?'🛡️ Admin':'🙋 User' ?>
                </span>
              </td>
              <td style="text-align:center">
                <span style="background:var(--cream);border:1px solid var(--border);padding:.25rem .6rem;border-radius:20px;font-size:.8rem;font-weight:600"><?= $u['booking_count'] ?></span>
              </td>
              <td onclick="event.stopPropagation();">
                <div style="display:flex;gap:.4rem;align-items:center">
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="action"   value="toggle_role">
                    <input type="hidden" name="user_id"  value="<?= $u['id'] ?>">
                    <input type="hidden" name="new_role" value="<?= $u['role']==='admin'?'user':'admin' ?>">
                    <button type="submit" class="btn btn-outline btn-sm"
                            onclick="return confirm('Change role to <?= $u['role']==='admin'?'user':'admin' ?>?')"
                            <?= $isSelf?'disabled title="Cannot change your own role"':'' ?>>
                      <?= $u['role']==='admin'?'🙋 Demote':'🛡️ Make Admin' ?>
                    </button>
                  </form>
                  <?php if (!$isSelf): ?>
                  <a href="manage-users.php?action=delete&id=<?= $u['id'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Delete this user permanently?\n\nNote: Their booking records will be kept.')">
                    🗑 Delete
                  </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($users)): ?>
      <div style="padding:.75rem 1.5rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border)">
        Showing <strong><?= count($users) ?></strong> of <strong><?= $total_users ?></strong> users
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- ── LAKBAYLOKAL ORIGINAL SIDE PANEL EXTENSION ── -->
<div class="adm-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

<div class="adm-panel" id="userDrawer">
  <div class="adm-panel-header">
    <div>
      <h3 style="color:var(--muted); font-size:0.85rem; text-transform:uppercase; margin:0;">Customer Profile</h3>
      <h2 id="drawerName" style="margin: 4px 0 0 0; color:var(--deep); font-family:'Playfair Display', serif;">-</h2>
    </div>
    <button class="panel-close" onclick="closeDrawer()" style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:var(--muted);">✕</button>
  </div>
  
  <div class="adm-panel-body" style="padding: 1.5rem;">
    <!-- Account Information Section -->
    <div class="info-section">
      <div class="info-section-title">Registration Details</div>
      <div class="info-row">
        <div class="info-label">User Reference</div>
        <div class="info-value mono" id="drawerId" style="color: var(--primary);"></div >
      </div>
      <!-- Dito inilagay ang bagong fields para sa First Name at Last Name -->
      <div class="info-row">
        <div class="info-label">First Name</div>
        <div class="info-value" id="drawerFirstName"></div>
      </div>
      <div class="info-row">
        <div class="info-label">Last Name</div>
        <div class="info-value" id="drawerLastName"></div>
      </div>
      <div class="info-row">
        <div class="info-label">Email Address</div>
        <div class="info-value" id="drawerEmail"></div>
      </div>
      <div class="info-row">
        <div class="info-label">System Role</div>
        <div class="info-value" id="drawerRole"></div>
      </div> 
      <div class="info-row">
        <div class="info-label">All Bookings</div>
        <div class="info-value" id="drawerBookings" style="font-weight:700;"></div>
      </div>
    </div>
    
    <!-- Travel History Section -->
    <div class="info-section">
      <div class="info-section-title">Recent Travel History</div>
      <div id="drawerHistory" style="display: flex; flex-direction: column; gap: 0.25rem;"></div>
    </div>
  </div>
</div>

<script>
  function openDrawer(row) {
    // In-inject ang data para magpakita ang hiwalay na First at Last name
    document.getElementById('drawerName').textContent      = row.dataset.name;
    document.getElementById('drawerId').textContent        = row.dataset.id;
    document.getElementById('drawerFirstName').textContent = row.dataset.fname;
    document.getElementById('drawerLastName').textContent  = row.dataset.lname;
    document.getElementById('drawerEmail').textContent     = row.dataset.email;
    document.getElementById('drawerRole').textContent      = row.dataset.role;
    document.getElementById('drawerBookings').textContent  = row.dataset.bookings;
    document.getElementById('drawerHistory').innerHTML     = row.dataset.history;

    document.getElementById('userDrawer').classList.add('open');
    document.getElementById('drawerOverlay').classList.add('open');
  }

  function closeDrawer() {
    document.getElementById('userDrawer').classList.remove('open');
    document.getElementById('drawerOverlay').classList.remove('open');
  }

  document.querySelectorAll('.adm-table tbody tr').forEach(row => {
    if(!row.querySelector('.adm-empty')) {
      row.addEventListener('click', () => openDrawer(row));
    }
  });
</script>
</body>
</html>