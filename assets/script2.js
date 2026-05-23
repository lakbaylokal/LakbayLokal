function openModal(type) {
  document.getElementById("authModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("authModal").style.display = "none";
}

function switchTab(tab) {
  // login/register switching logic
}

function scrollToDestinations() {
  document.getElementById("destinations").scrollIntoView({ behavior: "smooth" });
}

function selectDest(dest) {
  console.log(dest);
}