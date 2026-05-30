<div class="page" id="page-payment">
  <div class="payment-page">
    <div class="payment-card">
      <div class="confirm-icon">💳</div>
      <h2>Secure Payment</h2>
      <p>Confirm your payment details to complete the booking and receive your receipt.</p>
      <div class="confirm-details" id="paymentDetails">
        <div class="confirm-detail-row"><span>Destination</span><strong id="payDest">—</strong></div>
        <div class="confirm-detail-row"><span>Hotel</span><strong id="payHotel">—</strong></div>
        <div class="confirm-detail-row"><span>Check-in</span><strong id="payDate">—</strong></div>
        <div class="confirm-detail-row"><span>Guest</span><strong id="payName">—</strong></div>
        <div class="confirm-detail-row"><span>Total Due</span><strong id="payTotal" style="color:var(--primary)">—</strong></div>
      </div>
      <div class="payment-methods" id="paymentMethods">
        <div class="payment-method" id="method-gcash" onclick="selectPaymentMethod('gcash')">
          <strong>GCash</strong>
          <span>Enter your GCash mobile and reference</span>
        </div>
        <div class="payment-method" id="method-card" onclick="selectPaymentMethod('card')">
          <strong>Card</strong>
          <span>Enter your card details</span>
        </div>
      </div>
      <div class="payment-form" id="gcashPaymentFields" style="display:none;">
        <div class="form-group">
          <label>GCash Mobile</label>
          <div class="input-with-prefix">
            <span class="input-prefix">+63</span>
            <input type="text" id="gcashMobile" placeholder="9XX XXX XXXX" maxlength="12" oninput="formatGcashMobile(this)">
          </div>
        </div>
        <div class="form-group">
          <label>Reference Code</label>
          <input type="text" id="gcashRef" placeholder="GCASH1234" maxlength="20">
        </div>
      </div>
      <div class="payment-form" id="cardPaymentFields" style="display:none;">
        <div class="form-group">
          <label>Card Number</label>
          <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCardNumber(this)">
        </div>
        <div class="form-group">
          <label>Expiry</label>
          <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5">
        </div>
        <div class="form-group">
          <label>CVV</label>
          <input type="text" id="cardCvv" placeholder="123" maxlength="4">
        </div>
      </div>
      <button class="btn-primary" id="payNowBtn" onclick="processPayment()">Pay Now</button>
      <button class="btn-outline" onclick="showPage('detail')" style="width:100%;justify-content:center;margin-top:0.75rem;">Edit Booking</button>
    </div>
  </div>
</div>
