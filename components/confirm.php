<div class="page" id="page-confirm">
  <div class="confirm-page">
    <div class="confirm-card">
      <div class="confirm-icon">✅</div>
      <h2>Booking Confirmed!</h2>
      <p>Your trip has been booked. A confirmation has been sent to your email.</p>
      <div class="confirm-details" id="confirmDetails">
        <div class="confirm-detail-row"><span>Destination</span><strong id="cfDest">—</strong></div>
        <div class="confirm-detail-row"><span>Hotel</span><strong id="cfHotel">—</strong></div>
        <div class="confirm-detail-row"><span>Check-in</span><strong id="cfDate">—</strong></div>
        <div class="confirm-detail-row"><span>Guest</span><strong id="cfName">—</strong></div>
        <div class="confirm-detail-row"><span>Payment</span><strong id="cfMethod">—</strong></div>
        <div class="confirm-detail-row" id="cfPaymentRefRow" style="display:none;"><span>Payment Info</span><strong id="cfPaymentRef">—</strong></div>
        <div class="confirm-detail-row"><span>Total Paid</span><strong id="cfTotal" style="color:var(--primary)">—</strong></div>
      </div>
      <button class="btn-primary" onclick="showPage('dashboard')" style="width:100%;justify-content:center;margin-bottom:0.75rem;">View My Trips</button>
      <button class="btn-outline" onclick="showPage('destinations')" style="width:100%;justify-content:center;">Book Another Trip</button>
    </div>
  </div>
</div>