<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage = 'users';
$msg = '';
$msgType = 'success';

// ── Toggle role ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_role') {
    $uid  = (int)$_POST['user_id'];
    $role = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $uid]);
    header('Location: manage-users.php?msg=' . urlencode("Role updated successfully.") . '&type=success');
    exit;
}

// ── Delete user ──────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    if ($uid === ($_SESSION['user']['id'] ?? 0)) {
        header('Location: manage-users.php?msg=' . urlencode("You cannot delete your own account.") . '&type=error');
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$uid]);
    header('Location: manage-users.php?msg=' . urlencode("User deleted.") . '&type=success');
    exit;
}

if (isset($_GET['msg'])) {
    $msg = htmlspecialchars($_GET['msg']);
    $msgType = ($_GET['type'] ?? 'success') === 'error' ? 'error' : 'success';
}

// ── Fetch users ──────────────────────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role'] ?? '');
$where  = [];
$params = [];
if ($search !== '') {
    $where[]  = "(CONCAT(u.FName,' ',u.LName) LIKE ? OR u.Email LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like]);
}
if ($role_filter !== '') {
    $where[]  = "u.role = ?";
    $params[] = $role_filter;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("
    SELECT u.id, u.FName, u.Mname, u.LName, u.Email, u.role,
           COUNT(b.id) AS booking_count
    FROM users u
    LEFT JOIN bookings b ON b.user_id = u.id
    $whereSQL
    GROUP BY u.id
    ORDER BY u.id DESC
");
$stmt->execute($params);
$users = $stmt->fetchAll();

$total_users  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_admins = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$new_this_week = $pdo->query("SELECT COUNT(*) FROM users WHERE id >= 1")->fetchColumn(); // adjust if created_at exists
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users Management – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left"><h1>👥 Users</h1></div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('M d, Y (D)') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName) ?></div>
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
    <div class="adm-alert adm-alert-<?= $msgType ?>">
      <?= $msgType === 'success' ? '✅' : '❌' ?> <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">👥</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Users</div>
          <div class="stat-value"><?= $total_users ?></div>
          <div class="stat-sub">All members</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">🛡️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Admins</div>
          <div class="stat-value"><?= $total_admins ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">🙋</div>
        <div class="adm-stat-body">
          <div class="stat-label">Regular Users</div>
          <div class="stat-value"><?= $total_users - $total_admins ?></div>
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
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Bookings</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="6">
              <div class="adm-empty"><div class="empty-icon">👥</div><h4>No users found</h4><p>Try adjusting your search.</p></div>
            </td></tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
            <?php
              $fullname = trim(htmlspecialchars($u['FName']) . ' ' . htmlspecialchars($u['Mname'] ?? '') . ' ' . htmlspecialchars($u['LName']));
              $initials = strtoupper(substr($u['FName'],0,1) . substr($u['LName'],0,1));
              $isSelf = ($u['id'] === ($_SESSION['user']['id'] ?? 0));
            ?>
            <tr>
              <td style="color:var(--muted);font-size:.82rem"><?= $u['id'] ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                  <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-pale);color:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0"><?= $initials ?></div>
                  <div>
                    <div class="cell-main"><?= $fullname ?></div>
                    <?php if ($isSelf): ?><div class="cell-sub">(You)</div><?php endif; ?>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($u['Email']) ?></td>
              <td><span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-user' ?>"><?= ucfirst($u['role']) ?></span></td>
              <td><?= $u['booking_count'] ?></td>
              <td>
                <div style="display:flex;gap:.5rem;align-items:center">
                  <!-- Role toggle -->
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="toggle_role">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="new_role" value="<?= $u['role']==='admin'?'user':'admin' ?>">
                    <button type="submit" class="btn btn-outline btn-sm"
                            onclick="return confirm('Change role to <?= $u['role']==='admin'?'user':'admin' ?>?')"
                            <?= $isSelf ? 'disabled title="Cannot change your own role"' : '' ?>>
                      <?= $u['role']==='admin' ? '🙋 Make User' : '🛡️ Make Admin' ?>
                    </button>
                  </form>
                  <!-- Delete -->
                  <?php if (!$isSelf): ?>
                  <a href="manage-users.php?action=delete&id=<?= $u['id'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Delete this user permanently? Their bookings will remain.')">
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
      <div style="padding:.8rem 1.2rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border)">
        Showing <?= count($users) ?> of <?= $total_users ?> users
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>