<?php
$guestName = $guestName ?? '';
$destName = $destName ?? '';
$hotelName = $hotelName ?? '';
$guestEmail = $guestEmail ?? '';
$checkinFmt = $checkinFmt ?? '';
$checkoutFmt = $checkoutFmt ?? '';
$nights = $nights ?? 0;
$rooms = $rooms ?? 0;
$guests = $guests ?? '';
$paymentMethodDisplay = $paymentMethodDisplay ?? '';
$paymentMethodDisplay = preg_replace('/[^\x20-\x7E]/', '', $paymentMethodDisplay);
$selectedActs = $selectedActs ?? [];
$requests = $requests ?? '';
$hotelSubtotal = $hotelSubtotal ?? 0;
$activityTotal = $activityTotal ?? 0;
$finalDiscountAmount = $finalDiscountAmount ?? 0;
$appliedDiscount = $appliedDiscount ?? 0;
$tax = $tax ?? 0;
$finalTotal = $finalTotal ?? 0;
$ref = $ref ?? '';
$destId = $destId ?? '';
$hotelId = $hotelId ?? '';
$destGradient = $destGradient ?? '';
$destEmoji = $destEmoji ?? '';
?>

<style>
  .ll-confirm-wrapper {
    padding: 3rem 1.5rem;
    background-color: var(--cream);
    min-height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'DM Sans', sans-serif;
  }
  .ll-confirm-card {
    background: var(--white);
    max-width: 620px;
    width: 100%;
    border-radius: 24px;
    box-shadow: var(--shadow);
    padding: 2.5rem;
    text-align: center;
    border: 1px solid var(--border);
  }
  .ll-success-icon {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    display: inline-block;
    animation: popScale 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  @keyframes popScale {
    0% { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
  }
  .ll-confirm-card h2 {
    font-family: 'Playfair Display', serif;
    color: var(--deep);
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
  }
  .ll-confirm-lead {
    color: var(--muted);
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
  }
  .ll-badge-ref {
    display: inline-block;
    background: var(--primary-pale);
    color: var(--primary);
    font-family: monospace;
    font-size: 1.05rem;
    font-weight: 700;
    padding: 0.6rem 1.25rem;
    border-radius: 50px;
    border: 1px dashed var(--primary-light);
    margin-bottom: 2rem;
  }
  .ll-details-box {
    text-align: left;
    background: var(--cream);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }
  .ll-section-title {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted);
    font-weight: 700;
    margin-bottom: 0.75rem;
    border-bottom: 1.5px solid var(--border);
    padding-bottom: 4px;
  }
  .ll-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.6rem 0;
    border-bottom: 1px solid rgba(232, 224, 213, 0.5); /* customized soft border */
    font-size: 0.92rem;
  }
  .ll-row:last-child {
    border-bottom: none;
  }
  .ll-label {
    color: var(--muted);
  }
  .ll-val {
    font-weight: 600;
    color: var(--deep);
    text-align: right;
  }
  .ll-action-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin: 1.5rem 0;
    padding: 1rem 1.25rem;
    border-radius: 16px;
    background: var(--white);
    border: 1px solid var(--border);
    flex-wrap: wrap;
    text-align: left;
  }
  .ll-action-banner span {
    font-size: 0.88rem;
    color: var(--deep);
    font-weight: 500;
  }
  .ll-btn {
    border: none;
    border-radius: 50px;
    padding: 0.65rem 1.25rem;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .ll-btn-primary { background: var(--primary); color: #fff; }
  .ll-btn-primary:hover { background: var(--primary-light); }
  .ll-btn-accent { background: var(--accent); color: #fff; }
  .ll-btn-accent:hover { background: var(--accent-light); }
  .ll-btn-outline { background: var(--white); border: 1.5px solid var(--border); color: var(--deep); }
  .ll-btn-outline:hover { border-color: var(--primary); color: var(--primary); }
  
  .ll-footer-text {
    margin-top: 1.5rem;
    font-size: 0.8rem;
    color: var(--muted);
    letter-spacing: 0.02em;
  }
</style>

<div class="ll-confirm-wrapper">
  <div class="ll-confirm-card">
    
    <div class="ll-success-icon">🎉</div>
    <h2>Booking Confirmed!</h2>
    <p class="ll-confirm-lead">
      Your reservation at <strong><?= htmlspecialchars($hotelName) ?></strong> has been successfully submitted.<br>
      A confirmation has been dispatched to <strong><?= htmlspecialchars($guestEmail) ?></strong>.
    </p>

    <div class="ll-badge-ref">
      Reference: <?= htmlspecialchars($ref) ?>
    </div>

    <div class="ll-details-box">
      <div class="ll-section-title">Stay Information</div>
      <div class="ll-row"><span class="ll-label">Guest Name</span><span class="ll-val"><?= htmlspecialchars($guestName) ?></span></div>
      <div class="ll-row"><span class="ll-label">Destination</span><span class="ll-val"><?= htmlspecialchars($destEmoji) ?> <?= htmlspecialchars($destName) ?></span></div>
      <div class="ll-row"><span class="ll-label">Hotel Resort</span><span class="ll-val"><?= htmlspecialchars($hotelName) ?></span></div>
      <div class="ll-row"><span class="ll-label">Check-in Date</span><span class="ll-val">📅 <?= htmlspecialchars($checkinFmt) ?></span></div>
      <div class="ll-row"><span class="ll-label">Check-out Date</span><span class="ll-val">📅 <?= htmlspecialchars($checkoutFmt) ?></span></div>
      <div class="ll-row"><span class="ll-label">Duration</span><span class="ll-val"><?= $nights ?> night<?= $nights != 1 ? 's' : '' ?></span></div>
      <div class="ll-row"><span class="ll-label">Rooms / Guests</span><span class="ll-val"><?= $rooms ?> Room<?= $rooms != 1 ? 's' : '' ?> · <?= htmlspecialchars($guests) ?></span></div>
      <div class="ll-row"><span class="ll-label">Payment Method</span><span class="ll-val">💳 <?= htmlspecialchars($paymentMethodDisplay) ?></span></div>
    </div>

    <div class="ll-action-banner">
      <span>📄 Your official digital receipt is ready.</span>
      <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <button type="button" onclick="downloadReceiptPdf()" class="ll-btn ll-btn-primary">Download PDF</button>
        <a href="receipt.php?ref=<?= urlencode($ref) ?>" class="ll-btn ll-btn-accent">Full Receipt</a>
      </div>
    </div>

    <?php if (!empty($selectedActs) || $requests): ?>
    <div class="ll-details-box">
      <?php if (!empty($selectedActs)): ?>
        <div class="ll-section-title">Add-on Activities</div>
        <?php foreach ($selectedActs as $act): ?>
          <div class="ll-row">
            <span class="ll-label" style="color:var(--deep); font-weight:500;"><?= htmlspecialchars($act['name'] ?? '') ?></span>
            <span class="ll-val">₱<?= number_format($act['price'] ?? 0) ?></span>
          </div>
        <?php endforeach; ?>
        <?php if ($requests) echo '<div style="margin-top:1rem;"></div>'; ?>
      <?php endif; ?>

      <?php if ($requests): ?>
        <div class="ll-section-title">Special Requests</div>
        <div style="font-size:0.88rem; color:var(--deep); background:var(--white); padding:0.75rem; border-radius:8px; border:1px solid var(--border); line-height:1.4;">
          <?= htmlspecialchars($requests) ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="ll-details-box" style="background:var(--white); border-color:var(--primary-light);">
      <div class="ll-section-title" style="color:var(--primary); border-bottom-color:var(--primary-light);">Payment Breakdown</div>
      
      <div class="ll-row">
        <span class="ll-label">Hotel Subtotal</span>
        <span class="ll-val">₱<?= number_format($hotelSubtotal) ?></span>
      </div>
      
      <?php if ($activityTotal > 0): ?>
      <div class="ll-row">
        <span class="ll-label">Activities Total</span>
        <span class="ll-val">₱<?= number_format($activityTotal) ?></span>
      </div>
      <?php endif; ?>
      
      <?php if ($finalDiscountAmount > 0): ?>
      <div class="ll-row" style="color: #2E6B4F;">
        <span class="ll-label" style="color: #2E6B4F;">Promo Discount Applied</span>
        <span class="ll-val" style="color: #2E6B4F;">-₱<?= number_format($finalDiscountAmount) ?> (<?= round($appliedDiscount * 100) ?>%)</span>
      </div>
      <?php endif; ?>
      
      <div class="ll-row">
        <span class="ll-label">Taxes &amp; Fees (12%)</span>
        <span class="ll-val">₱<?= number_format($tax) ?></span>
      </div>
      
      <div class="ll-row" style="padding-top:1rem; margin-top:0.5rem; border-top:2px solid var(--border); font-size:1.05rem;">
        <span class="ll-label" style="color:var(--deep); font-weight:700;">Total Amount Paid</span>
        <span class="ll-val" style="color:var(--primary); font-size:1.25rem; font-weight:700;">₱<?= number_format($finalTotal) ?></span>
      </div>
    </div>

    <div style="display:flex; gap:0.5rem; justify-content:center; flex-wrap:wrap; margin-top:2rem;">
      <a href="hotel.php?dest=<?= urlencode($destId) ?>&id=<?= urlencode($hotelId) ?>" class="ll-btn ll-btn-outline">
        ← Back to Hotel
      </a>
      <a href="destinations.php?dest=<?= urlencode($destId) ?>" class="ll-btn ll-btn-primary">
        Explore More Hotels
      </a>
      <a href="destinations.php" class="ll-btn ll-btn-accent">
        All Destinations
      </a>
    </div>

    <p class="ll-footer-text">
      📧 Digital copy sent to inbox · 🔒 Insured Free Cancellation within 24 Hours
    </p>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
  const receiptPayload = <?= json_encode([
    'ref' => $ref,
    'guestName' => $guestName,
    'guestEmail' => $guestEmail,
    'destName' => $destName,
    'hotelName' => $hotelName,
    'checkinFmt' => $checkinFmt,
    'checkoutFmt' => $checkoutFmt,
    'nights' => $nights,
    'rooms' => $rooms,
    'guests' => $guests,
    'paymentMethodDisplay' => $paymentMethodDisplay,
    'hotelSubtotal' => $hotelSubtotal,
    'activityTotal' => $activityTotal,
    'discountAmount' => $finalDiscountAmount,
    'discountPercent' => $appliedDiscount,
    'tax' => $tax,
    'total' => $finalTotal,
    'specialRequests' => $requests,
    'selectedActivities' => $selectedActs,
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

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
      doc.text(String(value), valueX, y, { align: 'right' });
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
  }
</script>