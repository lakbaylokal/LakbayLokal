var currentUser = null;

function getInputValue(id) {
  return document.getElementById(id).value.trim();
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function setLoggedInUser(user) {
  currentUser = user;
  updateAuthNav();
}

function handleLogin(event) {
  event.preventDefault();

  const email = getInputValue('loginEmail');
  const password = getInputValue('loginPassword');

  if (!email || !password) {
    showToast('Please enter your email and password.');
    return;
  }

  if (!isValidEmail(email)) {
    showToast('Please enter a valid email address.');
    return;
  }

  const fallbackName = email.split('@')[0] || 'Traveler';
  setLoggedInUser({
    FName: fallbackName,
    LName: '',
    Mname: '',
    Email: email,
  });

  document.getElementById('loginPassword').value = '';
  showToast('Login successful.');
  showPage('dashboard');
}

function handleSignup(event) {
  event.preventDefault();

  const user = {
    FName: getInputValue('signupFName'),
    LName: getInputValue('signupLName'),
    Mname: getInputValue('signupMname'),
    Email: getInputValue('signupEmail'),
  };
  const password = getInputValue('signupPassword');

  if (!user.FName || !user.LName || !user.Mname || !user.Email || !password) {
    showToast('Please complete all required signup fields.');
    return;
  }

  if (!isValidEmail(user.Email)) {
    showToast('Please enter a valid email address.');
    return;
  }

  setLoggedInUser(user);
  document.getElementById('signupPassword').value = '';
  showToast('Account created for this session.');
  showPage('dashboard');
}

function logoutUser() {
  currentUser = null;
  updateAuthNav();
  showToast('Logged out.');
  showPage('home');
}

function updateAuthNav() {
  const isLoggedIn = Boolean(currentUser);
  const displayName = currentUser ? `${currentUser.FName} ${currentUser.LName}`.trim() : '';

  document.getElementById('navUserName').textContent = displayName;
  document.getElementById('navUserName').style.display = isLoggedIn ? 'inline-flex' : 'none';
  document.getElementById('navLoginBtn').style.display = isLoggedIn ? 'none' : 'inline-flex';
  document.getElementById('navSignupBtn').style.display = isLoggedIn ? 'none' : 'inline-flex';
  document.getElementById('navLogoutBtn').style.display = isLoggedIn ? 'inline-flex' : 'none';
  document.getElementById('mobileLoginLink').style.display = isLoggedIn ? 'none' : 'block';
  document.getElementById('mobileSignupLink').style.display = isLoggedIn ? 'none' : 'block';
  document.getElementById('mobileLogoutLink').style.display = isLoggedIn ? 'block' : 'none';
}

updateAuthNav();
