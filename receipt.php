<?php
require_once 'data.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$ref = trim($_GET['ref'] ?? $_SESSION['latest_receipt'] ?? '');
$receipt = null;

if ($ref && isset($_SESSION['receipt_history'][$ref])) {
  $receipt = $_SESSION['receipt_history'][$ref];
}

$pageTitle = 'Receipt — LakbayLokal';
$activePage = '';
$rootPath = '';
include 'includes/header.php';
?>

<div class="page-wrapper">
  <div class="confirm-page">
    <div class="confirm-card" style="max-width:680px;">
      <?php if (!$receipt): ?>
        <div class="confirm-icon">!</div>
        <p>Receipt not found. Please return to the booking page or complete a new reservation.</p>
        <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;margin-top:1rem;">
          <a href="index.php" style="background:var(--primary);color:white;border-radius:50px;padding:0.75rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">Go to Home</a>
          <a href="hotel.php?dest=<?= urlencode($_SESSION['receipt_history'][$ref]['dest_id'] ?? '') ?>&id=<?= urlencode($_SESSION['receipt_history'][$ref]['hotel_id'] ?? '') ?>" style="background:var(--cream);border:1.5px solid var(--border);color:var(--deep);border-radius:50px;padding:0.75rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">Back to Hotel</a>
        </div>
      <?php else: ?>
        <div class="confirm-icon"> 🧾 Receipt</div>
        <p>Your receipt for booking <strong><?= htmlspecialchars($receipt['ref']) ?></strong> is ready. Save it or download it for your records.</p>

        <div class="confirm-ref" style="margin-bottom:1rem;">
          Receipt ID: <strong><?= htmlspecialchars($receipt['ref']) ?></strong>
        </div>

        <div class="confirm-details">
          <div class="confirm-detail-row"><span>Guest Name</span><strong><?= htmlspecialchars($receipt['guest_name']) ?></strong></div>
          <div class="confirm-detail-row"><span>Guest Email</span><strong><?= htmlspecialchars($receipt['guest_email']) ?></strong></div>
          <div class="confirm-detail-row"><span>Destination</span><strong><?= htmlspecialchars($receipt['dest_name']) ?></strong></div>
          <div class="confirm-detail-row"><span>Hotel</span><strong><?= htmlspecialchars($receipt['hotel_name']) ?></strong></div>
          <div class="confirm-detail-row"><span>Check-in</span><strong><?= htmlspecialchars($receipt['checkin_fmt']) ?></strong></div>
          <div class="confirm-detail-row"><span>Check-out</span><strong><?= htmlspecialchars($receipt['checkout_fmt']) ?></strong></div>
          <div class="confirm-detail-row"><span>Duration</span><strong><?= htmlspecialchars($receipt['nights']) ?> night<?= $receipt['nights'] != 1 ? 's' : '' ?></strong></div>
          <div class="confirm-detail-row"><span>Rooms</span><strong><?= htmlspecialchars($receipt['rooms']) ?> room<?= $receipt['rooms'] != 1 ? 's' : '' ?></strong></div>
          <div class="confirm-detail-row"><span>Guests</span><strong><?= htmlspecialchars($receipt['guests']) ?></strong></div>
          <div class="confirm-detail-row"><span>Payment Method</span><strong><?= htmlspecialchars(preg_replace('/[^\x20-\x7E]/', '', [
              'gcash' => 'GCash',
              'credit_card' => 'Credit Card',
              'debit_card' => 'Debit Card'
            ][$receipt['payment_method']] ?? $receipt['payment_method'])) ?></strong></div>

          <?php if (!empty($receipt['selected_activities'])): ?>
            <div class="confirm-detail-row" style="flex-direction:column;align-items:flex-start;gap:0.3rem;">
              <span>Selected Activities</span>
              <?php foreach ($receipt['selected_activities'] as $act): ?>
                <div style="display:flex;justify-content:space-between;width:100%;font-size:0.85rem;">
                  <span style="color:var(--deep);"><?= htmlspecialchars($act['name'] ?? '') ?></span>
                  <strong>₱<?= number_format($act['price'] ?? 0) ?></strong>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($receipt['special_requests'])): ?>
            <div class="confirm-detail-row"><span>Special Requests</span><strong><?= htmlspecialchars($receipt['special_requests']) ?></strong></div>
          <?php endif; ?>

          <div class="confirm-detail-row" style="margin-top:0.5rem;padding-top:0.5rem;border-top:2px solid var(--border);">
            <span>Hotel subtotal</span>
            <strong>₱<?= number_format($receipt['hotel_subtotal']) ?></strong>
          </div>
          <?php if ($receipt['activity_total'] > 0): ?>
          <div class="confirm-detail-row">
            <span>Activities total</span>
            <strong>₱<?= number_format($receipt['activity_total']) ?></strong>
          </div>
          <?php endif; ?>
          <?php if ($receipt['discount_amount'] > 0): ?>
          <div class="confirm-detail-row" style="color:#27ae60;">
            <span>Discount Applied</span>
            <strong style="color:#27ae60;">-₱<?= number_format($receipt['discount_amount']) ?> (<?= round($receipt['discount_percent'] * 100) ?>%)</strong>
          </div>
          <?php endif; ?>
          <div class="confirm-detail-row">
            <span>Taxes & Fees (12%)</span>
            <strong>₱<?= number_format($receipt['tax']) ?></strong>
          </div>
          <div class="confirm-detail-row" style="font-size:1rem;font-weight:700;border-bottom:none;">
            <span><strong>Total Paid</strong></span>
            <strong style="color:var(--primary);font-size:1.15rem;">₱<?= number_format($receipt['total']) ?></strong>
          </div>
        </div>

        <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;margin-top:0.5rem;">
          <button id="downloadPdfBtn" type="button" onclick="downloadReceiptPdf()" style="background:var(--primary);color:white;border-radius:50px;padding:0.75rem 1.5rem;border:none;font-size:0.88rem;font-weight:600;cursor:pointer;">Download PDF</button>
          <a href="index.php" style="background:var(--accent);color:white;border-radius:50px;padding:0.75rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">Go to Home</a>
          <a href="hotel.php?dest=<?= urlencode($receipt['dest_id']) ?>&id=<?= urlencode($receipt['hotel_id']) ?>" style="background:var(--cream);border:1.5px solid var(--border);color:var(--deep);border-radius:50px;padding:0.75rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">Back to Hotel</a>
        </div>
        <div id="receiptStatus" style="margin-top:1rem;text-align:center;color:var(--primary);font-weight:600;display:none;">Your receipt is ready to download.</div>

        <p style="margin-top:1.5rem;font-size:0.78rem;color:var(--muted);">
          Generated on <?= htmlspecialchars($receipt['created_at']) ?> · Save this receipt for your records.
        </p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
  const receiptPayload = <?= json_encode($receipt ? [
    'ref' => $receipt['ref'],
    'guestName' => $receipt['guest_name'],
    'guestEmail' => $receipt['guest_email'],
    'destName' => $receipt['dest_name'],
    'hotelName' => $receipt['hotel_name'],
    'checkinFmt' => $receipt['checkin_fmt'],
    'checkoutFmt' => $receipt['checkout_fmt'],
    'nights' => $receipt['nights'],
    'rooms' => $receipt['rooms'],
    'guests' => $receipt['guests'],
    'paymentMethodDisplay' => preg_replace('/[^\x20-\x7E]/', '', [
      'gcash' => 'GCash',
      'credit_card' => 'Credit Card',
      'debit_card' => 'Debit Card'
    ][$receipt['payment_method']] ?? $receipt['payment_method']),
    'hotelSubtotal' => $receipt['hotel_subtotal'],

    'activityTotal' => $receipt['activity_total'],
    'discountAmount' => $receipt['discount_amount'],
    'discountPercent' => $receipt['discount_percent'],
    'tax' => $receipt['tax'],
    'total' => $receipt['total'],
    'specialRequests' => $receipt['special_requests'],
    'selectedActivities' => $receipt['selected_activities'],
  ] : null, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

  function downloadReceiptPdf() {
    if (!receiptPayload) {
      alert('Receipt data is unavailable. Please reload the page.');
      return;
    }
    const jsPDF = window.jspdf?.jsPDF || window.jsPDF;
    if (!jsPDF) {
      alert('Unable to load PDF library. Please check your internet connection and try again.');
      return;
    }

    const doc = new jsPDF({ unit: 'pt', format: 'letter' });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 40;
    const contentWidth = pageWidth - margin * 2;
    let y = 50;

    doc.setFillColor(237, 246, 236);
    doc.roundedRect(margin, y, 180, 24, 12, 12, 'F');
    doc.setTextColor(30, 76, 43);
    doc.setFontSize(10);
    doc.text(`Receipt ID: ${receiptPayload.ref}`, margin + 12, y + 16);

    y += 42;
    doc.setTextColor(26, 33, 44);
    doc.setFontSize(18);
    doc.text('Reservation Receipt', margin, y);
    y += 20;

    doc.setFontSize(11);
    doc.setTextColor(103, 113, 126);
    doc.text(`Generated on ${new Date().toLocaleDateString()}`, margin, y);
    y += 26;

    const cardX = margin;
    const cardY = y;
    const cardWidth = contentWidth;
    const cardHeight = 260 + (receiptPayload.selectedActivities?.length || 0) * 14 + (receiptPayload.specialRequests ? 24 : 0);
    doc.setFillColor(251, 247, 239);
    doc.roundedRect(cardX, cardY, cardWidth, cardHeight, 12, 12, 'F');
    doc.setDrawColor(219, 219, 219);
    doc.roundedRect(cardX, cardY, cardWidth, cardHeight, 12, 12, 'S');

    y = cardY + 30;
    const labelX = cardX + 24;
    const valueX = cardX + cardWidth - 24;
    const lineHeight = 18;

    function addRow(label, value, bold = false) {
      doc.setFontSize(11);
      doc.setTextColor(99, 99, 99);
      doc.text(label, labelX, y);
      doc.setTextColor(23, 29, 39);
      if (bold) doc.setFont('helvetica', 'bold');
      doc.text(value, valueX, y, { align: 'right' });
      if (bold) doc.setFont('helvetica', 'normal');
      y += lineHeight;
    }

    addRow('Guest Name', receiptPayload.guestName);
    addRow('Guest Email', receiptPayload.guestEmail);
    addRow('Destination', receiptPayload.destName);
    addRow('Hotel', receiptPayload.hotelName);
    addRow('Check-in', receiptPayload.checkinFmt);
    addRow('Check-out', receiptPayload.checkoutFmt);
    addRow('Duration', `${receiptPayload.nights} night${receiptPayload.nights != 1 ? 's' : ''}`);
    addRow('Rooms', `${receiptPayload.rooms} room${receiptPayload.rooms != 1 ? 's' : ''}`);
    addRow('Guests', receiptPayload.guests);
    addRow('Payment Method', receiptPayload.paymentMethodDisplay);

    if (receiptPayload.selectedActivities && receiptPayload.selectedActivities.length) {
      y += 4;
      doc.setTextColor(73, 78, 86);
      doc.setFontSize(11);
      doc.text('Selected Activities', labelX, y);
      y += lineHeight;
      receiptPayload.selectedActivities.forEach(act => {
        doc.setFontSize(10);
        doc.setTextColor(90, 96, 104);
        doc.text(act.name || '', labelX, y);
        doc.text(`PHP ${Number(act.price).toLocaleString()}`, valueX, y, { align: 'right' });
        y += 14;
      });
    }

    if (receiptPayload.specialRequests) {
      y += 8;
      doc.setFontSize(11);
      doc.setTextColor(73, 78, 86);
      doc.text('Special Requests', labelX, y);
      y += lineHeight;
      doc.setFontSize(10);
      doc.setTextColor(90, 96, 104);
      doc.text(receiptPayload.specialRequests, labelX, y, { maxWidth: cardWidth - 48 });
      y += 22;
    }

    y += 4;
    doc.setDrawColor(221, 221, 221);
    doc.setLineWidth(0.5);
    doc.line(labelX, y, valueX, y);
    y += 16;

    addRow('Hotel Subtotal', `PHP ${Number(receiptPayload.hotelSubtotal).toLocaleString()}`);
    if (receiptPayload.activityTotal > 0) {
      addRow('Activities Total', `PHP ${Number(receiptPayload.activityTotal).toLocaleString()}`);
    }
    if (receiptPayload.discountAmount > 0) {
      addRow('Discount Applied', `-PHP ${Number(receiptPayload.discountAmount).toLocaleString()} (${Math.round(receiptPayload.discountPercent * 100)}%)`);
    }
    addRow('Taxes & Fees (12%)', `PHP ${Number(receiptPayload.tax).toLocaleString()}`);

    y += 8;
    doc.setDrawColor(221, 221, 221);
    doc.setLineWidth(0.5);
    doc.line(labelX, y, valueX, y);
    y += 18;
    addRow('Total Paid', `PHP ${Number(receiptPayload.total).toLocaleString()}`, true);

    y += 28;
    doc.setFontSize(11);
    doc.setTextColor(108, 115, 125);
    doc.text('Thank you for booking with LakbayLokal!', labelX, y);

    doc.save(`LakbayLokal_Receipt_${receiptPayload.ref}.pdf`);
    const statusEl = document.getElementById('receiptStatus');
    if (statusEl) {
      statusEl.textContent = `Downloaded receipt: LakbayLokal_Receipt_${receiptPayload.ref}.pdf`;
      statusEl.style.display = 'block';
    }
  }
</script>

<?php include 'includes/footer.php';
