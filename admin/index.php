<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage = 'dashboard';

// ── Stats ───────────────────────────────────────────────────────────────────
$total_bookings   = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending_payments = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$confirmed_count  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed'")->fetchColumn();
$total_revenue    = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status='confirmed'")->fetchColumn();
$total_users      = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_dest       = $pdo->query("SELECT COUNT(*) FROM destinations WHERE archived=0")->fetchColumn();
$total_hotels     = $pdo->query("SELECT COUNT(*) FROM hotels WHERE archived=0")->fetchColumn();
$today_checkins   = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(checkin_date)=CURDATE()")->fetchColumn();

// Recent bookings
$recent_bookings = $pdo->query("
    SELECT b.id, b.reference_code, b.guest_name, b.checkin_date, b.total_price, b.status, d.name AS destination_name
    FROM bookings b LEFT JOIN destinations d ON b.destination_id=d.id
    ORDER BY b.id DESC LIMIT 8
")->fetchAll();

// Recent destinations
$recent_dest = $pdo->query("SELECT id,name,region,price FROM destinations WHERE archived=0 ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left"><h1>📊 Dashboard</h1></div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('l, F j, Y') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName) ?></div>
    </div>
  </header>

  <div class="adm-body">

    <!-- Welcome -->
    <div class="adm-page-header">
      <h2>Welcome back, <?= htmlspecialchars($adminName) ?> 👋</h2>
      <p>Here's what's happening with LakbayLokal today.</p>
    </div>

    <!-- OVERVIEW STATS -->
    <div class="adm-stats-grid">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🧾</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value"><?= number_format($total_bookings) ?></div>
          <div class="stat-sub">All-time reservations</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">💰</div>
        <div class="adm-stat-body">
          <div class="stat-label">Confirmed Revenue</div>
          <div class="stat-value" style="font-size:1.5rem">₱<?= number_format($total_revenue,0) ?></div>
          <div class="stat-sub">From confirmed bookings</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">⏳</div>
        <div class="adm-stat-body">
          <div class="stat-label">Pending Payments</div>
          <div class="stat-value"><?= $pending_payments ?></div>
          <div class="stat-sub">Awaiting review</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">🏨</div>
        <div class="adm-stat-body">
          <div class="stat-label">Today's Check-ins</div>
          <div class="stat-value"><?= $today_checkins ?></div>
          <div class="stat-sub"><?= date('M d') ?></div>
        </div>
      </div>
    </div>

    <!-- SECONDARY STATS -->
    <div class="adm-stats-grid" style="margin-top:1rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🗺️</div>
        <div class="adm-stat-body">
          <div class="stat-label">Active Destinations</div>
          <div class="stat-value"><?= $total_dest ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">🏨</div>
        <div class="adm-stat-body">
          <div class="stat-label">Active Hotels</div>
          <div class="stat-value"><?= $total_hotels ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">👥</div>
        <div class="adm-stat-body">
          <div class="stat-label">Registered Users</div>
          <div class="stat-value"><?= number_format($total_users) ?></div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">✅</div>
        <div class="adm-stat-body">
          <div class="stat-label">Confirmed Bookings</div>
          <div class="stat-value"><?= $confirmed_count ?></div>
        </div>
      </div>
    </div>

    <!-- RECENT ACTIVITY GRID -->
    <div class="adm-dashboard-grid" style="margin-top:1.75rem">

      <!-- Recent Bookings -->
      <div class="adm-card">
        <div class="adm-card-header">
          <h3>🧾 Recent Bookings</h3>
          <a href="manage-bookings.php" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="adm-table-wrap">
          <table class="adm-table">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Guest</th>
                <th>Destination</th>
                <th>Check-in</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            <?php if (empty($recent_bookings)): ?>
              <tr><td colspan="6">
                <div class="adm-empty" style="padding:1.5rem">
                  <div class="empty-icon" style="font-size:1.5rem">🧾</div>
                  <p>No bookings yet.</p>
                </div>
              </td></tr>
            <?php else: ?>
              <?php foreach ($recent_bookings as $b):
                $sc = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','rejected'=>'badge-rejected','cancelled'=>'badge-cancelled'];
                $cls = $sc[$b['status']] ?? 'badge-cancelled';
              ?>
              <tr style="cursor:pointer" onclick="window.location='manage-bookings.php'">
                <td><span style="font-family:monospace;font-size:.8rem;color:var(--muted)"><?= htmlspecialchars($b['reference_code']) ?></span></td>
                <td><span class="cell-main" style="font-size:.85rem"><?= htmlspecialchars($b['guest_name']) ?></span></td>
                <td style="font-size:.83rem"><?= htmlspecialchars($b['destination_name'] ?? '—') ?></td>
                <td style="font-size:.82rem;color:var(--muted);white-space:nowrap"><?= $b['checkin_date'] ?></td>
                <td style="font-size:.85rem"><strong>₱<?= number_format($b['total_price'],0) ?></strong></td>
                <td><span class="badge <?= $cls ?>" style="font-size:.7rem"><?= ucfirst($b['status']) ?></span></td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Links + Recent Destinations -->
      <div style="display:flex;flex-direction:column;gap:1.25rem">

        <!-- Quick Links -->
        <div class="adm-card">
          <div class="adm-card-header">
            <h3>⚡ Quick Actions</h3>
          </div>
          <div class="adm-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <a href="manage-destinations.php" class="btn btn-outline" style="justify-content:center;border-radius:10px;padding:.75rem">🗺️ Destinations</a>
            <a href="manage-hotels.php" class="btn btn-outline" style="justify-content:center;border-radius:10px;padding:.75rem">🏨 Hotels</a>
            <a href="manage-activities.php" class="btn btn-outline" style="justify-content:center;border-radius:10px;padding:.75rem">🎯 Activities</a>
            <a href="manage-users.php" class="btn btn-outline" style="justify-content:center;border-radius:10px;padding:.75rem">👥 Users</a>
            <a href="manage-bookings.php?status=pending" class="btn btn-primary" style="justify-content:center;border-radius:10px;padding:.75rem;grid-column:1/-1">
              ⏳ Review Pending (<?= $pending_payments ?>)
            </a>
          </div>
        </div>

        <!-- Recent Destinations -->
        <div class="adm-card">
          <div class="adm-card-header">
            <h3>🗺️ Recent Destinations</h3>
            <a href="manage-destinations.php" class="btn btn-ghost btn-sm">Manage</a>
          </div>
          <div class="adm-table-wrap">
            <table class="adm-table">
              <thead>
                <tr><th>Name</th><th>Region</th><th>Price</th></tr>
              </thead>
              <tbody>
              <?php if (empty($recent_dest)): ?>
                <tr><td colspan="3"><div class="adm-empty" style="padding:1.5rem"><div class="empty-icon" style="font-size:1.5rem">🗺️</div><p>No destinations yet.</p></div></td></tr>
              <?php else: ?>
                <?php foreach ($recent_dest as $d): ?>
                <tr>
                  <td><span class="cell-main" style="font-size:.85rem"><?= htmlspecialchars($d['name']) ?></span></td>
                  <td style="font-size:.8rem;color:var(--muted)"><?= htmlspecialchars($d['region']) ?></td>
                  <td style="font-size:.85rem"><strong>₱<?= number_format($d['price'],0) ?></strong></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div><!-- /adm-dashboard-grid -->

  </div><!-- /adm-body -->
</div><!-- /adm-main -->
</body>
</html>