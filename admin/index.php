<?php
// admin/index.php — LakbayLokal Admin Dashboard
$activePage = 'dashboard';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// --- Live Stats from DB ---
$totalBookings    = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$activeUsers      = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$pendingPayments  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$totalDests       = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
$totalRevenue     = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status IN ('confirmed','approved')")->fetchColumn();

// --- Recent Bookings (last 7) ---
$recentBookings = $pdo->query("
    SELECT b.reference_code, b.guest_name, b.status, b.total_price, b.created_at,
           d.name AS dest_name
    FROM bookings b
    LEFT JOIN destinations d ON d.id = b.destination_id
    ORDER BY b.created_at DESC LIMIT 7
")->fetchAll();

// --- Recent Users (last 5) ---
$recentUsers = $pdo->query("
    SELECT id, FName, LName, Email, role, created_at
    FROM users ORDER BY created_at DESC LIMIT 5
")->fetchAll();

$today = date('l, F j, Y');

function statusBadge(string $s): string {
    $map = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','approved'=>'badge-approved','rejected'=>'badge-rejected','cancelled'=>'badge-cancelled'];
    $cls = $map[strtolower($s)] ?? 'badge-pending';
    return "<span class=\"badge $cls\">$s</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard — LakbayLokal Admin</title>
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <!-- TOP BAR -->
  <header class="adm-topbar">
    <div class="adm-topbar-left">
      <h1>Dashboard</h1>
    </div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= $today ?></div>
      <div class="adm-admin-badge">👤 <?= $adminName ?></div>
    </div>
  </header>

  <div class="adm-body">
    <!-- PAGE HEADER -->
    <div class="adm-page-header">
      <h2>Dashboard Overview</h2>
      <p>Welcome back, <?= $adminName ?>! Here's what's happening with LakbayLokal.</p>
    </div>

    <!-- STAT CARDS -->
    <div class="adm-stats-grid">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🧾</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value"><?= number_format($totalBookings) ?></div>
          <div class="stat-sub">All time reservations</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">👥</div>
        <div class="adm-stat-body">
          <div class="stat-label">Active Users</div>
          <div class="stat-value"><?= number_format($activeUsers) ?></div>
          <div class="stat-sub">Registered travelers</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">⏳</div>
        <div class="adm-stat-body">
          <div class="stat-label">Pending Payments</div>
          <div class="stat-value"><?= number_format($pendingPayments) ?></div>
          <div class="stat-sub">Need review</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">🗺️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Destinations</div>
          <div class="stat-value"><?= number_format($totalDests) ?></div>
          <div class="stat-sub">Listed locations</div>
        </div>
      </div>
    </div>

    <!-- REVENUE BANNER -->
    <div style="background: linear-gradient(135deg, var(--deep) 0%, var(--deep-2) 100%); color:#fff; border-radius: var(--radius); padding: 1.5rem 2rem; margin-bottom: 1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
      <div>
        <div style="font-size:0.78rem; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:1px; margin-bottom:0.3rem;">Confirmed Revenue</div>
        <div style="font-family:'Playfair Display',serif; font-size:2.2rem; font-weight:700;">₱<?= number_format($totalRevenue) ?></div>
        <div style="font-size:0.82rem; color:rgba(255,255,255,0.5); margin-top:0.2rem;">From confirmed &amp; approved bookings</div>
      </div>
      <a href="manage-bookings.php" class="btn btn-primary">View All Bookings →</a>
    </div>

    <!-- BOTTOM GRID -->
    <div class="adm-dashboard-grid">
      <!-- Recent Bookings -->
      <div class="adm-card">
        <div class="adm-card-header">
          <h3>Recent Bookings</h3>
          <a href="manage-bookings.php" class="btn btn-outline btn-sm">View all</a>
        </div>
        <div class="adm-table-wrap">
          <table class="adm-table">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Guest</th>
                <th>Destination</th>
                <th>Status</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentBookings)): ?>
              <tr><td colspan="5"><div class="adm-empty"><div class="empty-icon">🧾</div><p>No bookings yet.</p></div></td></tr>
              <?php else: ?>
              <?php foreach ($recentBookings as $b): ?>
              <tr onclick="window.location='manage-bookings.php?ref=<?= urlencode($b['reference_code']) ?>'" style="cursor:pointer;">
                <td><span class="cell-main" style="font-family:monospace;font-size:0.8rem;"><?= htmlspecialchars($b['reference_code']) ?></span></td>
                <td><?= htmlspecialchars($b['guest_name']) ?></td>
                <td><?= htmlspecialchars($b['dest_name'] ?? '—') ?></td>
                <td><?= statusBadge($b['status']) ?></td>
                <td style="font-weight:700; color:var(--primary);">₱<?= number_format($b['total_price']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Recent Users -->
      <div class="adm-card">
        <div class="adm-card-header">
          <h3>Recent Users</h3>
          <a href="manage-users.php" class="btn btn-outline btn-sm">View all</a>
        </div>
        <div class="adm-table-wrap">
          <table class="adm-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentUsers)): ?>
              <tr><td colspan="3"><div class="adm-empty"><div class="empty-icon">👥</div><p>No users yet.</p></div></td></tr>
              <?php else: ?>
              <?php foreach ($recentUsers as $u): ?>
              <tr>
                <td>
                  <div class="cell-main"><?= htmlspecialchars($u['FName'] . ' ' . $u['LName']) ?></div>
                  <div class="cell-sub"><?= htmlspecialchars($u['Mname'] ?? '') ?></div>
                </td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars($u['Email']) ?></td>
                <td><span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-user' ?>"><?= $u['role'] ?></span></td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div><!-- /adm-body -->
</div><!-- /adm-main -->
</body>
</html>