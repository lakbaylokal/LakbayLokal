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
<div class="page-wrapper">
  <div class="confirm-page">
    <div class="confirm-card" style="max-width:580px;">

      <div class="confirm-icon">🎉</div>
      <p>Your reservation at <strong><?= $hotelName ?></strong> has been submitted. A confirmation will be sent to <strong><?= $guestEmail ?></strong>.</p>

      <div class="confirm-ref">
        Booking Reference: <strong><?= $ref ?></strong>
      </div>

      <div class="confirm-details">
        <div class="confirm-detail-row"><span>Guest Name</span><strong><?= $guestName ?></strong></div>
        <div class="confirm-detail-row"><span>Destination</span><strong><?= $destName ?></strong></div>
        <div class="confirm-detail-row"><span>Hotel</span><strong><?= $hotelName ?></strong></div>
        <div class="confirm-detail-row"><span>Check-in</span><strong><?= $checkinFmt ?></strong></div>
        <div class="confirm-detail-row"><span>Check-out</span><strong><?= $checkoutFmt ?></strong></div>
        <div class="confirm-detail-row"><span>Duration</span><strong><?= $nights ?> night<?= $nights != 1 ? 's' : '' ?></strong></div>
        <div class="confirm-detail-row"><span>Rooms</span><strong><?= $rooms ?> room<?= $rooms != 1 ? 's' : '' ?></strong></div>
        <div class="confirm-detail-row"><span>Guests</span><strong><?= $guests ?></strong></div>
        <div class="confirm-detail-row"><span>Payment Method</span><strong><?= $paymentMethodDisplay ?></strong></div>

        <div class="receipt-actions" style="display:flex;justify-content:space-between;align-items:center;gap:0.75rem;margin:1rem 0;padding:0.75rem 1rem;border-radius:16px;background:rgba(255,255,255,0.95);box-shadow:0 0 0 1px rgba(0,0,0,0.05);flex-wrap:wrap;">
          <span style="font-size:0.95rem;color:var(--deep);">Receipt is ready. Download it as PDF.</span>
          <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <button type="button" onclick="downloadReceiptPdf()" style="border:none;background:var(--primary);color:#fff;border-radius:50px;padding:0.75rem 1.25rem;font-weight:700;cursor:pointer;">Download PDF</button>
            <a href="receipt.php?ref=<?= urlencode($ref) ?>" style="display:inline-flex;align-items:center;justify-content:center;border:none;background:var(--accent);color:#fff;border-radius:50px;padding:0.75rem 1.25rem;font-weight:700;text-decoration:none;">View Full Receipt</a>
          </div>
        </div>

        <?php if (!empty($selectedActs)): ?>
        <div class="confirm-detail-row" style="flex-direction:column;align-items:flex-start;gap:0.3rem;">
          <span>Selected Activities</span>
          <?php foreach ($selectedActs as $act): ?>
            <div style="display:flex;justify-content:space-between;width:100%;font-size:0.85rem;">
              <span style="color:var(--deep);"><?= htmlspecialchars($act['name'] ?? '') ?></span>
              <strong>₱<?= number_format($act['price'] ?? 0) ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($requests): ?>
        <div class="confirm-detail-row"><span>Special Requests</span><strong><?= $requests ?></strong></div>
        <?php endif; ?>

        <!-- Price Breakdown -->
        <div class="confirm-detail-row" style="margin-top:0.5rem;padding-top:0.5rem;border-top:2px solid var(--border);">
          <span>Hotel (<?= $nights ?> nights × <?= $rooms ?> room<?= $rooms > 1 ? 's' : '' ?>)</span>
          <strong>₱<?= number_format($hotelSubtotal) ?></strong>
        </div>
        <?php if ($activityTotal > 0): ?>
        <div class="confirm-detail-row">
          <span>Activities Total</span>
          <strong>₱<?= number_format($activityTotal) ?></strong>
        </div>
        <?php endif; ?>
        <?php if ($finalDiscountAmount > 0): ?>
        <div class="confirm-detail-row" style="color: #27ae60;">
          <span>Discount Applied</span>
          <strong style="color: #27ae60;">-₱<?= number_format($finalDiscountAmount) ?> (<?= round($appliedDiscount * 100) ?>%)</strong>
        </div>
        <?php endif; ?>
        <div class="confirm-detail-row">
          <span>Taxes &amp; Fees (12%)</span>
          <strong>₱<?= number_format($tax) ?></strong>
        </div>
        <div class="confirm-detail-row" style="font-size:1rem;font-weight:700;border-bottom:none;">
          <span><strong>Total Paid</strong></span>
          <strong style="color:var(--primary);font-size:1.15rem;">₱<?= number_format($finalTotal) ?></strong>
        </div>
      </div>

      <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;margin-top:0.5rem;">
        <a href="hotel.php?dest=<?= $destId ?>&id=<?= $hotelId ?>"
           style="background:var(--cream);border:1.5px solid var(--border);color:var(--deep);border-radius:50px;padding:0.7rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;transition:all 0.2s;"
           onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
          ← Back to Hotel
        </a>
        <a href="destinations.php?dest=<?= $destId ?>"
           style="background:var(--primary);color:white;border-radius:50px;padding:0.7rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">
          Explore More Hotels
        </a>
        <a href="destinations.php"
           style="background:var(--accent);color:white;border-radius:50px;padding:0.7rem 1.5rem;text-decoration:none;font-size:0.88rem;font-weight:600;">
          All Destinations
        </a>
      </div>

      <p style="margin-top:1.5rem;font-size:0.78rem;color:var(--muted);">
        📧 Confirmation sent · 🔒 Free cancellation within 24 hours
      </p>
    </div>
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
  }
</script>