<?php
require_once __DIR__ . '/../includes/config.php';
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$message = '';
if (!empty($_GET['error'])) {
    $errors = [
        'missing' => 'Please fill in all fields.',
        'email' => 'Please enter a valid email address.',
        'match' => 'Passwords do not match.',
        'exists' => 'That email is already registered.',
        'server' => 'Unable to create account. Please try again.',
    ];
    $message = $errors[$_GET['error']] ?? 'Unable to register.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - LakbayPH</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<section class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <p class="auth-tag">Create account</p>
      <h1>Join LakbayPH</h1>
      <p class="auth-sub">Sign up to save trips, hotels, and travel ideas.</p>
    </div>

    <?php if ($message): ?>
      <div class="auth-alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form class="auth-form" action="register-process.php" method="POST">
      <div class="auth-field">
        <label for="fullname">Full name</label>
        <input id="fullname" type="text" name="fullname" placeholder="Juan Dela Cruz" required>
      </div>
      <div class="auth-field">
        <label for="email">Email address</label>
        <input id="email" type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="auth-field">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" placeholder="Enter password" required>
      </div>
      <div class="auth-field">
        <label for="confirm_password">Confirm password</label>
        <input id="confirm_password" type="password" name="confirm_password" placeholder="Repeat password" required>
      </div>
      <button class="auth-submit" type="submit">Create Account</button>
    </form>

    <p class="auth-footer">Already registered? <a href="login.php">Log in here</a></p>
  </div>
</section>
</body>
</html>