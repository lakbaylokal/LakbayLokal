// script.js — LakbayLokal Unified Frontend JS
// Handles: auth modal, login/signup/logout, nav, My Trips rendering

var currentUser = JSON.parse(sessionStorage.getItem('lbl_user') || 'null');
const namePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:[ '\-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/;

function setFieldError(inputId, message) {
  const error = document.getElementById(inputId + 'Error');
  if (!error) return;
  error.textContent = message || '';
  error.classList.toggle('show', Boolean(message));
}

function clearAuthErrors(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.querySelectorAll('.validation-error').forEach(el => {
    el.textContent = '';
    el.classList.remove('show');
  });
}

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
  clearAuthErrors(tab === 'login' ? 'authLogin' : 'authSignup');
}

/* ─────────────────────── LOGIN ───────────────────────────────── */

function handleLogin(event) {
  event.preventDefault();
  const email    = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value.trim();

  if (!email || !password) {
    if (!email) setFieldError('loginEmail', 'Please enter your email and password.');
    if (!password) setFieldError('loginPassword', 'Please enter your email and password.');
    showToast('Please enter your email and password.');
    return;
  }
  setFieldError('loginEmail', '');
  setFieldError('loginPassword', '');
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    setFieldError('loginEmail', 'Please enter a valid email address.');
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
        setFieldError('loginPassword', 'Incorrect email or password.');
        showToast(data.message || 'Incorrect email or password.');
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
    if (!FName) setFieldError('signupFName', 'Please complete all required fields.');
    if (!LName) setFieldError('signupLName', 'Please complete all required fields.');
    if (!email) setFieldError('signupEmail', 'Please complete all required fields.');
    if (!password) setFieldError('signupPassword', 'Please complete all required fields.');
    showToast('Please complete all required fields.');
    return;
  }
  clearAuthErrors('authSignup');
  if (!namePattern.test(FName)) {
    setFieldError('signupFName', 'Name must contain letters only.');
    showToast('Name must contain letters only.');
    return;
  }
  if (!namePattern.test(LName)) {
    setFieldError('signupLName', 'Name must contain letters only.');
    showToast('Name must contain letters only.');
    return;
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    setFieldError('signupEmail', 'Please enter a valid email address.');
    showToast('Please enter a valid email address.');
    return;
  }
  if (password.length < 6) {
    setFieldError('signupPassword', 'Password must be at least 6 characters long.');
    showToast('Password must be at least 6 characters long.');
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
        showToast(data.message || 'Signup failed.');
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
  const mobileLogin  = document.getElementById('mobileLoginItem');
  const mobileSignup = document.getElementById('mobileSignupItem');
  const mobileLogout = document.getElementById('mobileLogoutItem');

  if (loginBtn)  loginBtn.style.display  = isLoggedIn ? 'none' : 'inline-flex';
  if (signupBtn) signupBtn.style.display = isLoggedIn ? 'none' : 'inline-flex';
  if (logoutBtn) logoutBtn.style.display = isLoggedIn ? 'inline-flex' : 'none';
  if (userLabel) {
    userLabel.style.display = isLoggedIn ? 'inline-block' : 'none';
    userLabel.textContent   = isLoggedIn ? ('👤 ' + currentUser.FName) : '';
  }
  if (mobileLogin)  mobileLogin.style.display  = isLoggedIn ? 'none' : 'list-item';
  if (mobileSignup) mobileSignup.style.display = isLoggedIn ? 'none' : 'list-item';
  if (mobileLogout) mobileLogout.style.display = isLoggedIn ? 'list-item' : 'none';
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

  const authValidators = {
    signupFName: value => namePattern.test(value.trim()) ? '' : 'Name must contain letters only.',
    signupLName: value => namePattern.test(value.trim()) ? '' : 'Name must contain letters only.',
    signupPassword: value => value.length >= 6 ? '' : 'Password must be at least 6 characters long.'
  };

  Object.keys(authValidators).forEach(id => {
    const input = document.getElementById(id);
    if (!input) return;
    input.addEventListener('input', function() {
      setFieldError(id, authValidators[id](input.value));
    });
  });

  ['loginEmail', 'loginPassword', 'signupEmail'].forEach(id => {
    const input = document.getElementById(id);
    if (!input) return;
    input.addEventListener('input', function() {
      setFieldError(id, '');
    });
  });

  // Render My Trips if section exists
  if (document.getElementById('myTripsContent') && typeof renderMyTrips === 'function') {
    renderMyTrips();
  }

  // Bootstrap collapse handles mobile menu close on outside click

});