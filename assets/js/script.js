// script.js — LakbayLokal Unified Frontend JS
// Handles: auth modal, login/signup/logout, nav, My Trips rendering

var currentUser = JSON.parse(sessionStorage.getItem('lbl_user') || 'null');

/* ─────────────────────── AUTH MODAL ────────────────────────── */

function openAuthModal(tab) {
  document.getElementById('authModal').classList.add('open');
  switchAuthTab(tab || 'login');
}

function closeAuthModal() {
  document.getElementById('authModal').classList.remove('open');
}

function switchAuthTab(tab) {
  document.getElementById('authLogin').style.display  = tab === 'login'  ? 'block' : 'none';
  document.getElementById('authSignup').style.display = tab === 'signup' ? 'block' : 'none';
}

/* ─────────────────────── LOGIN ───────────────────────────────── */

function handleLogin(event) {
  event.preventDefault();
  const email    = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value.trim();

  if (!email || !password) {
    showToast('Please enter your email and password.');
    return;
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    showToast('Please enter a valid email address.');
    return;
  }

  // Send to backend to set PHP session
  const formData = new FormData();
  formData.append('action', 'login');
  formData.append('email', email);
  formData.append('password', password);

  fetch('api_auth.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const user = data.user;
        setLoggedInUser(user);
        document.getElementById('loginPassword').value = '';
        closeAuthModal();
        showToast('Welcome back, ' + user.FName + '! 👋');
      } else {
        showToast('Login failed: ' + data.message);
      }
    })
    .catch(err => showToast('Error: ' + err.message));
}

/* ─────────────────────── SIGNUP ──────────────────────────────── */

function handleSignup(event) {
  event.preventDefault();
  const FName    = document.getElementById('signupFName').value.trim();
  const LName    = document.getElementById('signupLName').value.trim();
  const email    = document.getElementById('signupEmail').value.trim();
  const password = document.getElementById('signupPassword').value.trim();

  if (!FName || !LName || !email || !password) {
    showToast('Please complete all required fields.');
    return;
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    showToast('Please enter a valid email address.');
    return;
  }

  // Send to backend to set PHP session
  const formData = new FormData();
  formData.append('action', 'signup');
  formData.append('FName', FName);
  formData.append('LName', LName);
  formData.append('email', email);
  formData.append('password', password);

  fetch('api_auth.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const user = data.user;
        setLoggedInUser(user);
        document.getElementById('signupPassword').value = '';
        closeAuthModal();
        showToast('Account created! Welcome, ' + user.FName + ' 🎉');
      } else {
        showToast('Signup failed: ' + data.message);
      }
    })
    .catch(err => showToast('Error: ' + err.message));
}

/* ─────────────────────── LOGOUT ──────────────────────────────── */

function logoutUser() {
  const formData = new FormData();
  formData.append('action', 'logout');

  fetch('api_auth.php', { method: 'POST', body: formData })
    .then(() => {
      sessionStorage.removeItem('lbl_user');
      currentUser = null;
      updateAuthNav();
      showToast('You have been logged out.');
      // Reload page to refresh header on all pages
      setTimeout(() => location.reload(), 500);
    })
    .catch(err => {
      showToast('Error: ' + err.message);
    });
}

/* ─────────────────────── NAV STATE ──────────────────────────── */

function fetchCurrentUser() {
  // Check if user is stored in sessionStorage
  if (currentUser) {
    updateAuthNav();
    return;
  }

  // Fetch from server if session exists (on page reload)
  fetch('api_auth.php?action=getCurrentUser')
    .then(res => res.json())
    .then(data => {
      if (data.success && data.user) {
        setLoggedInUser(data.user);
      } else {
        updateAuthNav();
      }
    })
    .catch(() => updateAuthNav());
}

function setLoggedInUser(user) {
  currentUser = user;
  sessionStorage.setItem('lbl_user', JSON.stringify(user));
  updateAuthNav();
}

function updateAuthNav() {
  const isLoggedIn  = Boolean(currentUser);
  const loginBtn    = document.getElementById('navLoginBtn');
  const signupBtn   = document.getElementById('navSignupBtn');
  const logoutBtn   = document.getElementById('navLogoutBtn');
  const userLabel   = document.getElementById('navUserName');
  const mobileLogin  = document.getElementById('mobileLoginLink');
  const mobileSignup = document.getElementById('mobileSignupLink');
  const mobileLogout = document.getElementById('mobileLogoutLink');

  if (loginBtn)  loginBtn.style.display  = isLoggedIn ? 'none' : '';
  if (signupBtn) signupBtn.style.display = isLoggedIn ? 'none' : '';
  if (logoutBtn) logoutBtn.style.display = isLoggedIn ? '' : 'none';
  if (userLabel) {
    userLabel.style.display = isLoggedIn ? '' : 'none';
    userLabel.textContent   = isLoggedIn ? ('👤 ' + currentUser.FName) : '';
  }
  if (mobileLogin)  mobileLogin.style.display  = isLoggedIn ? 'none' : '';
  if (mobileSignup) mobileSignup.style.display = isLoggedIn ? 'none' : '';
  if (mobileLogout) mobileLogout.style.display = isLoggedIn ? '' : 'none';
}

/* ─────────────────────── TOAST ───────────────────────────────── */

function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.style.opacity = '1';
  t.style.transform = 'translateY(0)';
  clearTimeout(t._timeout);
  t._timeout = setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translateY(8px)';
  }, 3000);
}

/* ─────────────────────── INIT ────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function() {
  // Restore auth state and fetch current user if logged in
  fetchCurrentUser();

  // Render My Trips if section exists
  if (document.getElementById('myTripsContent') && typeof renderMyTrips === 'function') {
    renderMyTrips();
  }

  // Close mobile menu on outside click
  document.addEventListener('click', function(e) {
    const menu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger');
    if (menu && menu.classList.contains('open') && !menu.contains(e.target) && e.target !== hamburger && !hamburger.contains(e.target)) {
      menu.classList.remove('open');
    }
  });
});
