function filterStars(n) {
  document.querySelectorAll('.star-btn').forEach(b => b.classList.remove('active'));
  if (event && event.target) event.target.classList.add('active');
  document.querySelectorAll('.hotel-card').forEach(card => {
    card.style.display = (n === 0 || parseInt(card.dataset.stars) >= n) ? 'flex' : 'none';
  });
}

function filterPrice(val) {
  document.getElementById('priceDisplay').textContent = 'Up to ₱' + parseInt(val).toLocaleString();
  document.querySelectorAll('.hotel-card').forEach(card => {
    card.style.display = parseInt(card.dataset.price) <= parseInt(val) ? 'flex' : 'none';
  });
}
