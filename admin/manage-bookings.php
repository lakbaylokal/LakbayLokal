<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$activePage = 'bookings';
$view       = isset($_GET['view']) && $_GET['view'] === 'calendar' ? 'calendar' : 'table';

// ── AJAX: Booking detail panel ──────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'get_booking' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id   = (int)$_GET['id'];
    $stmt = $pdo->prepare("
        SELECT b.*, d.name AS destination_name, h.name AS hotel_name,
               pd.gcash_number, pd.gcash_account_name,
               pd.card_holder_name, pd.card_last_four, pd.card_brand,
               pd.payment_status AS pd_payment_status, pd.payment_reference
        FROM bookings b
        LEFT JOIN destinations d ON b.destination_id = d.id
        LEFT JOIN hotels h ON b.hotel_id = h.id
        LEFT JOIN payment_details pd ON pd.booking_id = b.id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch() ?: ['error' => 'Not found']); exit;
}

// ── AJAX: Calendar data ─────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'calendar_data') {
    header('Content-Type: application/json');
    $month = (int)($_GET['month'] ?? date('n'));
    $year  = (int)($_GET['year']  ?? date('Y'));
    $start = sprintf('%04d-%02d-01', $year, $month);
    $end   = date('Y-m-t', strtotime($start));
    $stmt  = $pdo->prepare("SELECT id,reference_code,guest_name,checkin_date,checkout_date,status FROM bookings WHERE checkin_date<=? AND checkout_date>=? ORDER BY checkin_date");
    $stmt->execute([$end, $start]);
    echo json_encode($stmt->fetchAll()); exit;
}

// ── AJAX: Approve / Reject ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    if (!$bookingId) { echo json_encode(['success'=>false,'message'=>'Invalid booking ID']); exit; }
    if ($_POST['action'] === 'approve_payment') {
        $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE id=?")->execute([$bookingId]);
        $pdo->prepare("UPDATE payment_details SET payment_status='paid' WHERE booking_id=?")->execute([$bookingId]);
        echo json_encode(['success'=>true,'message'=>'Payment approved successfully.']);
    } elseif ($_POST['action'] === 'reject_payment') {
        $pdo->prepare("UPDATE bookings SET status='rejected' WHERE id=?")->execute([$bookingId]);
        $pdo->prepare("UPDATE payment_details SET payment_status='rejected' WHERE booking_id=?")->execute([$bookingId]);
        echo json_encode(['success'=>true,'message'=>'Payment rejected.']);
    } else { echo json_encode(['success'=>false,'message'=>'Unknown action.']); }
    exit;
}

// ── Table data ──────────────────────────────────────────────────────────────
$search        = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$where = []; $params = [];
if ($search !== '') {
    $where[]  = '(b.reference_code LIKE ? OR b.guest_name LIKE ? OR b.guest_email LIKE ?)';
    $like = "%$search%"; $params = array_merge($params, [$like,$like,$like]);
}
if ($status_filter !== '') { $where[] = 'b.status=?'; $params[] = $status_filter; }
$whereSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';
$stmt = $pdo->prepare("SELECT b.id,b.reference_code,b.guest_name,b.guest_email,b.checkin_date,b.checkout_date,b.total_price,b.status,b.payment_method,d.name AS destination_name FROM bookings b LEFT JOIN destinations d ON b.destination_id=d.id $whereSQL ORDER BY b.id DESC");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$total     = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending   = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed'")->fetchColumn();
$today_ct  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(checkin_date)=CURDATE()")->fetchColumn();
$revenue   = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status='confirmed'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookings – LakbayLokal Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left">
      <h1><?= $view==='calendar' ? '📅 Calendar View' : '🧾 All Bookings' ?></h1>
    </div>
    <div class="adm-topbar-right">
      <div class="adm-date-badge">📅 <?= date('M d, Y (D)') ?></div>
      <div class="adm-admin-badge">👤 <?= htmlspecialchars($adminName) ?></div>
    </div>
  </header>

  <div class="adm-body">

    <!-- PAGE HEADER + VIEW TOGGLE -->
    <div class="adm-page-header-row">
      <div class="adm-page-header" style="margin-bottom:0">
        <h2><?= $view==='calendar' ? 'Calendar View' : 'All Bookings' ?></h2>
        <p><?= $view==='calendar' ? 'Visualize bookings and check-in/out dates across all destinations.' : 'Manage travel reservations and approve payments.' ?></p>
      </div>
      <div class="view-toggle">
        <a href="manage-bookings.php?view=table<?= $search?'&search='.urlencode($search):'' ?>"
           class="view-toggle-btn <?= $view==='table'?'active':'' ?>">📋 Table View</a>
        <a href="manage-bookings.php?view=calendar"
           class="view-toggle-btn <?= $view==='calendar'?'active':'' ?>">📅 Calendar</a>
      </div>
    </div>

    <!-- STATS -->
    <div class="adm-stats-grid" style="margin-top:1.5rem">
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-primary">🧾</div>
        <div class="adm-stat-body">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value"><?= $total ?></div>
          <div class="stat-sub">All-time</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-gold">⏳</div>
        <div class="adm-stat-body">
          <div class="stat-label">Pending Payments</div>
          <div class="stat-value"><?= $pending ?></div>
          <div class="stat-sub">Needs review</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-accent">✅</div>
        <div class="adm-stat-body">
          <div class="stat-label">Confirmed</div>
          <div class="stat-value"><?= $confirmed ?></div>
          <div class="stat-sub">Paid &amp; approved</div>
        </div>
      </div>
      <div class="adm-stat-card">
        <div class="adm-stat-icon stat-icon-muted">🏨</div>
        <div class="adm-stat-body">
          <div class="stat-label">Today's Check-ins</div>
          <div class="stat-value"><?= $today_ct ?></div>
          <div class="stat-sub"><?= date('M d') ?></div>
        </div>
      </div>
    </div>

    <?php if ($view === 'table'): ?>
    <!-- ══ TABLE VIEW ══════════════════════════════════════════════════════ -->
    <div class="adm-card" style="margin-top:1.5rem">
      <div class="adm-card-header">
        <form method="GET" style="display:contents">
          <input type="hidden" name="view" value="table">
          <div class="adm-toolbar" style="flex:1">
            <div class="adm-search-wrap" style="max-width:360px">
              <span class="search-icon">🔍</span>
              <input type="text" name="search" class="adm-search"
                     placeholder="Reference code, guest name or email…"
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="status" class="adm-select" onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <?php foreach (['pending','confirmed','rejected','cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $status_filter===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Apply</button>
            <?php if ($search || $status_filter): ?>
            <a href="manage-bookings.php" class="btn btn-ghost btn-sm">✕ Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table" id="bookings-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Guest</th>
              <th>Destination</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($bookings)): ?>
            <tr><td colspan="8">
              <div class="adm-empty">
                <div class="empty-icon">🧾</div>
                <h4>No bookings found</h4>
                <p><?= ($search||$status_filter) ? 'Try adjusting your filters.' : 'No bookings have been made yet.' ?></p>
              </div>
            </td></tr>
          <?php else: ?>
            <?php foreach ($bookings as $b):
              $sc = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','rejected'=>'badge-rejected','cancelled'=>'badge-cancelled'];
              $cls = $sc[$b['status']] ?? 'badge-cancelled';
            ?>
            <tr class="booking-row" data-id="<?= $b['id'] ?>" style="cursor:pointer" title="Click to view details">
              <td><span class="cell-main" style="font-family:monospace;font-size:.82rem"><?= htmlspecialchars($b['reference_code']) ?></span></td>
              <td>
                <div class="cell-main"><?= htmlspecialchars($b['guest_name']) ?></div>
                <div class="cell-sub"><?= htmlspecialchars($b['guest_email']) ?></div>
              </td>
              <td style="font-size:.85rem"><?= htmlspecialchars($b['destination_name'] ?? '—') ?></td>
              <td style="font-size:.85rem;white-space:nowrap"><?= htmlspecialchars($b['checkin_date']) ?></td>
              <td style="font-size:.85rem;white-space:nowrap"><?= htmlspecialchars($b['checkout_date']) ?></td>
              <td><strong>₱<?= number_format($b['total_price'],2) ?></strong></td>
              <td><span style="font-size:.78rem;text-transform:capitalize;color:var(--muted)"><?= htmlspecialchars($b['payment_method']) ?></span></td>
              <td><span class="badge <?= $cls ?>"><?= ucfirst($b['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($bookings)): ?>
      <div style="padding:.75rem 1.5rem;font-size:.82rem;color:var(--muted);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
        <span>Showing <strong><?= count($bookings) ?></strong> booking<?= count($bookings)!=1?'s':'' ?></span>
        <span style="font-size:.78rem">Click any row to view full details and manage payment</span>
      </div>
      <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ══ CALENDAR VIEW ════════════════════════════════════════════════════ -->
    <div class="adm-cal-wrap" style="margin-top:1.5rem" id="calendar-container">
      <div class="adm-cal-header">
        <div class="adm-cal-title" id="cal-title">Loading…</div>
        <div class="adm-cal-nav">
          <button class="cal-nav-btn" id="cal-prev">◀</button>
          <button class="cal-nav-btn" id="cal-today" style="padding:0 .9rem;width:auto;font-size:.78rem;font-weight:600">Today</button>
          <button class="cal-nav-btn" id="cal-next">▶</button>
        </div>
      </div>
      <div class="adm-cal-grid" id="cal-labels">
        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $lbl): ?>
        <div class="cal-day-label"><?= $lbl ?></div>
        <?php endforeach; ?>
      </div>
      <div class="adm-cal-grid" id="cal-days" style="min-height:420px">
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted)">Loading calendar…</div>
      </div>
      <!-- Legend -->
      <div style="padding:.75rem 1.5rem;border-top:1px solid var(--border);display:flex;gap:1.25rem;font-size:.78rem;color:var(--muted)">
        <span style="display:flex;align-items:center;gap:.4rem"><span style="width:10px;height:10px;border-radius:2px;background:var(--accent-pale);border-left:2px solid var(--accent);display:inline-block"></span>Check-in</span>
        <span style="display:flex;align-items:center;gap:.4rem"><span style="width:10px;height:10px;border-radius:2px;background:var(--primary-pale);border-left:2px solid var(--primary);display:inline-block"></span>Check-out</span>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /adm-body -->
</div><!-- /adm-main -->

<!-- ══ OVERLAY ══════════════════════════════════════════════════════════════ -->
<div class="adm-overlay" id="panel-overlay"></div>

<!-- ══ BOOKING DETAIL SIDE PANEL ════════════════════════════════════════════ -->
<aside class="adm-panel" id="booking-panel">
  <div class="adm-panel-header">
    <div>
      <h3>Booking Details</h3>
      <h2 id="panel-ref">—</h2>
    </div>
    <button class="panel-close" id="panel-close">✕</button>
  </div>
  <div class="adm-panel-body" id="panel-body">
    <div class="adm-empty"><div class="empty-icon">⏳</div><p>Loading…</p></div>
  </div>
  <div class="adm-panel-footer" id="panel-footer"></div>
</aside>

<script>
// ── PANEL ──────────────────────────────────────────────────────────────────
const overlay    = document.getElementById('panel-overlay');
const panel      = document.getElementById('booking-panel');
const panelClose = document.getElementById('panel-close');

function openPanel(id) {
  document.getElementById('panel-ref').textContent   = '…';
  document.getElementById('panel-body').innerHTML    = '<div class="adm-empty"><div class="empty-icon">⏳</div><p>Loading booking details…</p></div>';
  document.getElementById('panel-footer').innerHTML  = '';
  overlay.classList.add('open');
  panel.classList.add('open');

  fetch(`manage-bookings.php?action=get_booking&id=${id}`)
    .then(r => r.json())
    .then(b => {
      if (b.error) {
        document.getElementById('panel-body').innerHTML = `<div class="adm-alert adm-alert-error">❌ ${b.error}</div>`;
        return;
      }
      document.getElementById('panel-ref').textContent = b.reference_code;

      let payHtml = '';
      if (b.payment_method === 'gcash') {
        payHtml  = `<div class="info-row"><span class="info-label">GCash Number</span><span class="info-value mono">${b.gcash_number||'—'}</span></div>`;
        payHtml += `<div class="info-row"><span class="info-label">Account Name</span><span class="info-value">${b.gcash_account_name||'—'}</span></div>`;
      } else if (b.payment_method === 'card') {
        payHtml  = `<div class="info-row"><span class="info-label">Card Holder</span><span class="info-value">${b.card_holder_name||'—'}</span></div>`;
        payHtml += `<div class="info-row"><span class="info-label">Card</span><span class="info-value mono">${b.card_brand||''} •••• ${b.card_last_four||'—'}</span></div>`;
      }
      if (b.payment_reference) {
        payHtml += `<div class="info-row"><span class="info-label">Reference</span><span class="info-value mono">${b.payment_reference}</span></div>`;
      }

      const statusMap = {pending:'badge-pending',confirmed:'badge-confirmed',rejected:'badge-rejected',cancelled:'badge-cancelled'};
      const bclass = statusMap[b.status] || 'badge-cancelled';
      const fmt = v => parseFloat(v||0).toLocaleString('en-PH',{minimumFractionDigits:2});

      document.getElementById('panel-body').innerHTML = `
        <div class="info-section">
          <div class="info-section-title">Booking Information</div>
          <div class="info-row"><span class="info-label">Destination</span><span class="info-value">${b.destination_name||'—'}</span></div>
          <div class="info-row"><span class="info-label">Hotel</span><span class="info-value">${b.hotel_name||'—'}</span></div>
          <div class="info-row"><span class="info-label">Check-in</span><span class="info-value">${b.checkin_date}</span></div>
          <div class="info-row"><span class="info-label">Check-out</span><span class="info-value">${b.checkout_date}</span></div>
          <div class="info-row"><span class="info-label">Guests / Rooms</span><span class="info-value">${b.number_of_guests||'—'} guests · ${b.number_of_rooms||'—'} room(s)</span></div>
          <div class="info-row"><span class="info-label">Status</span><span class="info-value"><span class="badge ${bclass}">${b.status}</span></span></div>
        </div>
        <div class="info-section">
          <div class="info-section-title">Customer Information</div>
          <div class="info-row"><span class="info-label">Name</span><span class="info-value">${b.guest_name}</span></div>
          <div class="info-row"><span class="info-label">Email</span><span class="info-value">${b.guest_email}</span></div>
          ${b.special_requests?`<div class="info-row"><span class="info-label">Requests</span><span class="info-value" style="font-style:italic;font-weight:400">${b.special_requests}</span></div>`:''}
        </div>
        <div class="info-section">
          <div class="info-section-title">Payment Information</div>
          <div class="info-row"><span class="info-label">Method</span><span class="info-value" style="text-transform:capitalize">${b.payment_method}</span></div>
          ${payHtml}
          <div class="info-row"><span class="info-label">Pay Status</span><span class="info-value" style="text-transform:capitalize">${b.pd_payment_status||'—'}</span></div>
          <div class="price-breakdown">
            <div class="info-row"><span class="info-label">Subtotal</span><span class="info-value">₱${fmt(b.subtotal)}</span></div>
            <div class="info-row"><span class="info-label">Activities</span><span class="info-value">₱${fmt(b.activities_total)}</span></div>
            <div class="info-row"><span class="info-label">Tax</span><span class="info-value">₱${fmt(b.tax_amount)}</span></div>
            <div class="info-row total-row"><span class="info-label">Total</span><span class="info-value">₱${fmt(b.total_price)}</span></div>
          </div>
        </div>`;

      document.getElementById('panel-footer').innerHTML = `
        <button class="btn btn-success" onclick="paymentAction('approve_payment',${b.id})">✅ Approve Payment</button>
        <button class="btn btn-danger"  onclick="paymentAction('reject_payment',${b.id})">✕ Reject Payment</button>`;
    })
    .catch(() => {
      document.getElementById('panel-body').innerHTML = '<div class="adm-alert adm-alert-error">❌ Failed to load booking.</div>';
    });
}

function closePanel() {
  overlay.classList.remove('open');
  panel.classList.remove('open');
  document.querySelectorAll('.booking-row.selected').forEach(r => r.classList.remove('selected'));
}

overlay.addEventListener('click', closePanel);
panelClose.addEventListener('click', closePanel);

document.querySelectorAll('.booking-row').forEach(row => {
  row.addEventListener('click', function() {
    document.querySelectorAll('.booking-row.selected').forEach(r => r.classList.remove('selected'));
    this.classList.add('selected');
    openPanel(this.dataset.id);
  });
});

function paymentAction(action, bookingId) {
  const label = action === 'approve_payment' ? 'Approve' : 'Reject';
  if (!confirm(`${label} this payment?`)) return;
  const fd = new FormData();
  fd.append('action', action);
  fd.append('booking_id', bookingId);
  fetch('manage-bookings.php', { method:'POST', body:fd })
    .then(r => r.json())
    .then(res => { alert(res.message); if (res.success) { closePanel(); location.reload(); } });
}

// ── CALENDAR ───────────────────────────────────────────────────────────────
<?php if ($view === 'calendar'): ?>
let calYear  = <?= date('Y') ?>;
let calMonth = <?= date('n') ?>;
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function loadCalendar() {
  document.getElementById('cal-title').textContent = `${MONTHS[calMonth-1]} ${calYear}`;
  const grid = document.getElementById('cal-days');
  grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2.5rem;color:var(--muted)">Loading…</div>';

  fetch(`manage-bookings.php?action=calendar_data&month=${calMonth}&year=${calYear}`)
    .then(r => r.json())
    .then(bookings => {
      const firstDay    = new Date(calYear, calMonth-1, 1).getDay();
      const daysInMonth = new Date(calYear, calMonth, 0).getDate();
      const daysInPrev  = new Date(calYear, calMonth-1, 0).getDate();
      const today = new Date(); today.setHours(0,0,0,0);
      let html = '';

      for (let i = firstDay - 1; i >= 0; i--)
        html += `<div class="cal-day other-month"><div class="cal-date" style="opacity:.3">${daysInPrev-i}</div></div>`;

      for (let d = 1; d <= daysInMonth; d++) {
        const dateStr  = `${calYear}-${String(calMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const thisDate = new Date(calYear, calMonth-1, d);
        const isToday  = thisDate.getTime() === today.getTime();
        let evHtml = '';
        bookings.forEach(b => {
          if (b.checkin_date  === dateStr) evHtml += `<div class="cal-booking cal-booking-checkin"  title="${b.guest_name}">↓ ${b.reference_code}</div>`;
          if (b.checkout_date === dateStr) evHtml += `<div class="cal-booking cal-booking-checkout" title="${b.guest_name}">↑ ${b.reference_code}</div>`;
        });
        html += `<div class="cal-day${isToday?' today':''}"><div class="cal-date">${d}</div>${evHtml}</div>`;
      }

      const total = firstDay + daysInMonth;
      const rem   = total % 7 === 0 ? 0 : 7 - (total % 7);
      for (let i = 1; i <= rem; i++)
        html += `<div class="cal-day other-month"><div class="cal-date" style="opacity:.3">${i}</div></div>`;

      grid.innerHTML = html;
    })
    .catch(() => { grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2rem;color:red">Failed to load calendar.</div>'; });
}

document.getElementById('cal-prev').addEventListener('click', () => { calMonth--; if(calMonth<1){calMonth=12;calYear--;} loadCalendar(); });
document.getElementById('cal-next').addEventListener('click', () => { calMonth++; if(calMonth>12){calMonth=1;calYear++;} loadCalendar(); });
document.getElementById('cal-today').addEventListener('click', () => { calYear=<?= date('Y') ?>; calMonth=<?= date('n') ?>; loadCalendar(); });
loadCalendar();
<?php endif; ?>
</script>
</body>
</html>