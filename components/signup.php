<div class="page" id="page-signup">
  <div class="auth-page">
    <div class="auth-panel auth-panel-wide">
      <div class="auth-kicker">Create account</div>
      <h1>Sign up</h1>
      <p>Please fill out all required fields to create your account.</p>

      <form class="auth-form" onsubmit="handleSignup(event)" novalidate>
        <div class="auth-grid">
          <div class="form-group">
            <label for="signupFName">First Name</label>
            <input type="text" id="signupFName" name="FName" autocomplete="given-name" required>
          </div>
          <div class="form-group">
            <label for="signupLName">Last Name</label>
            <input type="text" id="signupLName" name="LName" autocomplete="family-name" required>
          </div>
          <div class="form-group">
            <label for="signupMname">Middle name</label>
            <input type="text" id="signupMname" name="Mname" autocomplete="additional-name" required>
          </div>
          <div class="form-group">
            <label for="signupEmail">Email</label>
            <input type="email" id="signupEmail" name="Email" autocomplete="email" required>
          </div>
          <div class="form-group auth-grid-full">
            <label for="signupPassword">Password</label>
            <input type="password" id="signupPassword" name="Password" autocomplete="new-password" required>
          </div>
        </div>
        <button class="btn-primary auth-submit" type="submit">Create Account</button>
      </form>

      <div class="auth-switch">
        Already have an account?
        <button type="button" onclick="showPage('login')">Login</button>
      </div>
    </div>
  </div>
</div>
