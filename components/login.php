<div class="page" id="page-login">
  <div class="auth-page">
    <div class="auth-panel">
      <div class="auth-kicker">Welcome back</div>
      <h1>Login to LakbayLokal</h1>
      <p>Use your email and password to continue planning your trips.</p>

      <form class="auth-form" onsubmit="handleLogin(event)" novalidate>
        <div class="form-group">
          <label for="loginEmail">Email</label>
          <input type="email" id="loginEmail" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
          <label for="loginPassword">Password</label>
          <input type="password" id="loginPassword" name="password" autocomplete="current-password" required>
        </div>
        <button class="btn-primary auth-submit" type="submit">Login</button>
      </form>

      <div class="auth-switch">
        No account yet?
        <button type="button" onclick="showPage('signup')">Create one</button>
      </div>
    </div>
  </div>
</div>
